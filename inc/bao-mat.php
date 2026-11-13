<?php
declare(strict_types=1);

/**
 * Ghi nhật ký sự kiện bảo mật.
 */
function ghiNhatKyBaoMat(string $suKien, array $duLieu = []): void
{
    $suKienChoPhep = [
        'dang_nhap_sai',
        'csrf_sai',
        'cam_truy_cap',
    ];

    if (!in_array($suKien, $suKienChoPhep, true)) {
        return;
    }

    $duLieuAnToan = [];

    if (isset($duLieu['ip']) && is_string($duLieu['ip'])) {
        $duLieuAnToan['ip'] = substr($duLieu['ip'], 0, 45);
    }

    if (isset($duLieu['email_hash']) && is_string($duLieu['email_hash'])) {
        $duLieuAnToan['email_hash'] = $duLieu['email_hash'];
    }

    $banGhi = [
        'thoi_gian' => date('c'),
        'su_kien' => $suKien,
        'du_lieu' => $duLieuAnToan,
    ];

    $duongDan = dirname(__DIR__) . '/logs/bao-mat.log';

    $json = json_encode(
        $banGhi,
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );

    if ($json === false) {
        return;
    }

    error_log($json . PHP_EOL, 3, $duongDan);
}
/**
 * Ghi nhận số lần đăng nhập sai trong vòng 15 phút theo IP.
 */
/**
 * Ghi nhận số lần đăng nhập sai trong vòng 15 phút theo IP.
 *
 * Ném RuntimeException nếu không thể lưu dữ liệu giới hạn.
 */
function ghiNhanDangNhapThatBai(string $ip): int
{
    $duongDan = dirname(__DIR__) . '/logs/dang-nhap-rate-limit.json';
    $khoaIp = hash('sha256', $ip);
    $thoiGianHienTai = time();
    $cuaSoThoiGian = 15 * 60;

    $tep = fopen($duongDan, 'c+');

    if ($tep === false) {
        throw new RuntimeException('Không thể mở dữ liệu giới hạn đăng nhập.');
    }

    $daKhoa = false;

    try {
        if (!flock($tep, LOCK_EX)) {
            throw new RuntimeException('Không thể khóa dữ liệu giới hạn đăng nhập.');
        }

        $daKhoa = true;

        rewind($tep);
        $noiDung = stream_get_contents($tep);

        if ($noiDung === false) {
            throw new RuntimeException('Không thể đọc dữ liệu giới hạn đăng nhập.');
        }

        $duLieu = $noiDung === ''
            ? []
            : json_decode($noiDung, true, 512, JSON_THROW_ON_ERROR);

        if (!is_array($duLieu)) {
            throw new RuntimeException('Dữ liệu giới hạn đăng nhập không hợp lệ.');
        }

        foreach ($duLieu as $khoa => $cacLanSai) {
            if (!is_array($cacLanSai)) {
                unset($duLieu[$khoa]);
                continue;
            }

            $duLieu[$khoa] = array_values(array_filter(
                $cacLanSai,
                static fn ($mocThoiGian): bool =>
                    is_int($mocThoiGian)
                    && $mocThoiGian > $thoiGianHienTai - $cuaSoThoiGian
            ));

            if ($duLieu[$khoa] === []) {
                unset($duLieu[$khoa]);
            }
        }

        $duLieu[$khoaIp] ??= [];
        $duLieu[$khoaIp][] = $thoiGianHienTai;

        $json = json_encode($duLieu, JSON_THROW_ON_ERROR);

        rewind($tep);

        if (!ftruncate($tep, 0)) {
            throw new RuntimeException('Không thể cập nhật dữ liệu giới hạn đăng nhập.');
        }

        $daGhi = fwrite($tep, $json);

        if ($daGhi !== strlen($json) || !fflush($tep)) {
            throw new RuntimeException('Không thể lưu dữ liệu giới hạn đăng nhập.');
        }

        return count($duLieu[$khoaIp]);
    } finally {
        if ($daKhoa) {
            flock($tep, LOCK_UN);
        }

        fclose($tep);
    }
}
/**
 * Đếm số lần đăng nhập sai trong 15 phút gần nhất theo IP.
 */
function demLanDangNhapThatBai(string $ip): int
{
    $duongDan = dirname(__DIR__) . '/logs/dang-nhap-rate-limit.json';

    $tep = fopen($duongDan, 'c+');

    if ($tep === false) {
        throw new RuntimeException('Không thể đọc dữ liệu giới hạn đăng nhập.');
    }

    try {
        if (!flock($tep, LOCK_SH)) {
            throw new RuntimeException('Không thể khóa dữ liệu giới hạn đăng nhập.');
        }

        rewind($tep);
        $noiDung = stream_get_contents($tep);

        if ($noiDung === false) {
            throw new RuntimeException('Không thể đọc dữ liệu giới hạn đăng nhập.');
        }

        $duLieu = $noiDung === ''
            ? []
            : json_decode($noiDung, true, 512, JSON_THROW_ON_ERROR);

        if (!is_array($duLieu)) {
            throw new RuntimeException('Dữ liệu giới hạn đăng nhập không hợp lệ.');
        }

        $khoaIp = hash('sha256', $ip);
        $cacLanSai = $duLieu[$khoaIp] ?? [];

        if (!is_array($cacLanSai)) {
            throw new RuntimeException('Dữ liệu đăng nhập không hợp lệ.');
        }

        $mocThoiGian = time() - (15 * 60);

        return count(array_filter(
            $cacLanSai,
            static fn ($lan): bool =>
                is_int($lan) && $lan > $mocThoiGian
        ));
    } finally {
        flock($tep, LOCK_UN);
        fclose($tep);
    }
}