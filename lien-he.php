<?php
declare(strict_types=1);

require_once __DIR__ . '/inc/config.php';

$tieuDeTrang = 'Liên hệ và tư vấn - Quà Tặng Thông Minh';
$baseUrl = '';

$thongBao = $_SESSION['thong_bao_lien_he'] ?? '';
unset($_SESSION['thong_bao_lien_he']);

$loiForm = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $csrfTokenGuiLen = $_POST['csrf_token'] ?? null;

    if (!kiemTraCsrfToken(
        is_string($csrfTokenGuiLen) ? $csrfTokenGuiLen : null
    )) {
        http_response_code(419);
        exit('Phiên làm việc đã hết hạn hoặc mã xác thực không hợp lệ. Vui lòng tải lại trang.');
    }

    // Từ chối dữ liệu POST không đúng kiểu chuỗi.
    $cacTruongChuoi = [
        'ho-ten',
        'email',
        'so-dien-thoai',
        'nguoi-nhan',
        'dip-tang',
        'ngan-sach',
        'ngay-can-qua',
        'so-thich',
        'dong-y'
    ];

    foreach ($cacTruongChuoi as $truong) {
        if (
            array_key_exists($truong, $_POST)
            && !is_string($_POST[$truong])
        ) {
            http_response_code(400);
            exit('Dữ liệu biểu mẫu không hợp lệ.');
        }
    }

    $hoTen = trim((string) ($_POST['ho-ten'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $soDienThoai = trim((string) ($_POST['so-dien-thoai'] ?? ''));
    $nguoiNhan = trim((string) ($_POST['nguoi-nhan'] ?? ''));
    $dipTang = trim((string) ($_POST['dip-tang'] ?? ''));

    $nganSach = filter_var(
        $_POST['ngan-sach'] ?? null,
        FILTER_VALIDATE_INT
    );

    $ngayCanQua = trim((string) ($_POST['ngay-can-qua'] ?? ''));
    $soThich = trim((string) ($_POST['so-thich'] ?? ''));
    $dongY = isset($_POST['dong-y']);

    if ($hoTen === '' || mb_strlen($hoTen) < 2 || mb_strlen($hoTen) > 50) {
        $loiForm[] = 'Họ tên phải từ 2 đến 50 ký tự.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $loiForm[] = 'Email không hợp lệ.';
    }

    if (!preg_match('/^[0-9]{10}$/', $soDienThoai)) {
        $loiForm[] = 'Số điện thoại phải gồm đúng 10 chữ số.';
    }

    if ($nguoiNhan === '') {
        $loiForm[] = 'Vui lòng chọn người nhận quà.';
    }

    if ($dipTang === '') {
        $loiForm[] = 'Vui lòng chọn dịp tặng.';
    }

    if ($nganSach === false || $nganSach < 5000 || $nganSach > 100000000) {
        $loiForm[] = 'Ngân sách phải từ 5.000 đến 100.000.000 VNĐ.';
    }

    if ($ngayCanQua === '') {
        $loiForm[] = 'Vui lòng chọn ngày cần quà.';
    }

    if ($soThich === '') {
        $loiForm[] = 'Vui lòng nhập sở thích hoặc yêu cầu.';
    }

    if (!$dongY) {
        $loiForm[] = 'Bạn cần đồng ý cung cấp thông tin để được tư vấn.';
    }


$tenAnhDaLuu = null;

if (isset($_FILES['anh']) && $_FILES['anh']['error'] !== UPLOAD_ERR_NO_FILE) {

    $fileAnh = $_FILES['anh'];

    if ($fileAnh['error'] !== UPLOAD_ERR_OK) {
        $loiForm[] = 'Không thể tải ảnh lên. Vui lòng thử lại.';
    } elseif ($fileAnh['size'] > 5 * 1024 * 1024) {
        $loiForm[] = 'Ảnh không được vượt quá 5MB.';
    } else {
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($fileAnh['tmp_name']);

        $dinhDangAnh = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp'
        ];

        if (!isset($dinhDangAnh[$mimeType])) {
            $loiForm[] = 'Chỉ chấp nhận ảnh JPG, PNG hoặc WEBP.';
        } else {
            $thuMucUploads = __DIR__ . '/uploads';

            if (!is_dir($thuMucUploads)) {
                if (!mkdir($thuMucUploads, 0755, true)) {
                    $loiForm[] = 'Không thể tạo thư mục lưu ảnh.';
                }
            }

            if (empty($loiForm)) {
                $tenNgauNhien = bin2hex(random_bytes(16))
                    . '.' . $dinhDangAnh[$mimeType];

                if (move_uploaded_file(
                    $fileAnh['tmp_name'],
                    $thuMucUploads . '/' . $tenNgauNhien
                )) {
                    $tenAnhDaLuu = $tenNgauNhien;
                } else {
                    $loiForm[] = 'Không thể lưu ảnh. Vui lòng thử lại.';
                }
            }
        }
    }
}

if (empty($loiForm)) {
    // Gộp tất cả thông tin vào cột noi_dung
    $noiDungChiTiet = "SĐT: {$soDienThoai} | Người nhận: {$nguoiNhan} | Dịp: {$dipTang} | Ngân sách: " . number_format((float)$nganSach, 0, ',', '.') . "đ | Ngày cần: {$ngayCanQua} | Yêu cầu: {$soThich}" . ($tenAnhDaLuu ? " | Ảnh: {$tenAnhDaLuu}" : "");

    try {
        $pdo = hamCSDL();

        $stmt = $pdo->prepare("INSERT INTO lien_he (ten, email, noi_dung) VALUES (?, ?, ?)");
        $stmt->execute([$hoTen, $email, $noiDungChiTiet]);

        $_SESSION['thong_bao_lien_he'] = 'Gửi yêu cầu tư vấn thành công!';
        redirect('lien-he.php');

    } catch (Throwable $e) {
        error_log('Lỗi gửi liên hệ: ' . $e->getMessage());
        $loiForm[] = 'Không thể gửi yêu cầu tư vấn lúc này. Vui lòng thử lại sau.';
    }
}

if (!empty($loiForm)) {
    $thongBao = 'Vui lòng kiểm tra lại thông tin trong biểu mẫu.';
  }
}
$csrfToken = function_exists('taoCsrfToken') ? taoCsrfToken() : '';
require_once __DIR__ . '/inc/header.php';
?>


<main class="container trang-lien-he">
    <section class="form-lien-he trang-lien-he__card">
        <h2>Đăng ký tư vấn quà tặng</h2>

        <?php if ($thongBao !== ''): ?>
            <p class="trang-lien-he__thong-bao <?= empty($loiForm) ? 'trang-lien-he__thong-bao--thanh-cong' : 'trang-lien-he__thong-bao--loi' ?>">
                <?= e($thongBao) ?>
            </p>
        <?php endif; ?>

        <?php if (!empty($loiForm)): ?>
            <div class="thong-bao-loi trang-lien-he__loi" role="alert">
                <ul class="trang-lien-he__danh-sach-loi">
                    <?php foreach ($loiForm as $loi): ?>
                        <li><?= e($loi) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form id="form-lien-he" action="lien-he.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">

            <fieldset class="trang-lien-he__fieldset">
                <legend class="trang-lien-he__legend">Thông tin người gửi</legend>

                <p class="trang-lien-he__truong">
                    <label for="ho-ten">Họ và tên:</label><br>
                    <input type="text" id="ho-ten" name="ho-ten" value="<?= e($_POST['ho-ten'] ?? '') ?>" required>
                </p>

                <p class="trang-lien-he__truong">
                    <label for="email">Email:</label><br>
                    <input type="email" id="email" name="email" value="<?= e($_POST['email'] ?? '') ?>" required>
                </p>

                <p class="trang-lien-he__truong">
                    <label for="so-dien-thoai">Số điện thoại (10 chữ số):</label><br>
                    <input type="tel" id="so-dien-thoai" name="so-dien-thoai" value="<?= e($_POST['so-dien-thoai'] ?? '') ?>" required>
                </p>

                <p class="trang-lien-he__truong">
                    <label for="nguoi-nhan">Người nhận quà:</label><br>
                    <select id="nguoi-nhan" name="nguoi-nhan" required>
                        <option value="">-- Chọn người nhận --</option>
                        <option value="Bạn bè">Bạn bè</option>
                        <option value="Người yêu">Người yêu</option>
                        <option value="Gia đình">Gia đình</option>
                        <option value="Đồng nghiệp">Đồng nghiệp</option>
                    </select>
                </p>

                <p class="trang-lien-he__truong">
                    <label for="dip-tang">Dịp tặng:</label><br>
                    <select id="dip-tang" name="dip-tang" required>
                        <option value="">-- Chọn dịp tặng --</option>
                        <option value="Sinh nhật">Sinh nhật</option>
                        <option value="Kỷ niệm">Kỷ niệm</option>
                        <option value="Ngày lễ">Ngày lễ</option>
                        <option value="Khác">Khác</option>
                    </select>
                </p>

                <p class="trang-lien-he__truong">
                    <label for="ngan-sach">Ngân sách dự kiến (VNĐ):</label><br>
                    <input type="number" id="ngan-sach" name="ngan-sach" min="5000" max="100000000" step="1000" value="<?= e($_POST['ngan-sach'] ?? '') ?>" required>
                </p>

                <p class="trang-lien-he__truong">
                    <label for="ngay-can-qua">Ngày cần quà:</label><br>
                    <input type="date" id="ngay-can-qua" name="ngay-can-qua" value="<?= e($_POST['ngay-can-qua'] ?? '') ?>" required>
                </p>

                <p class="trang-lien-he__truong">
                    <label for="so-thich">Sở thích hoặc yêu cầu cụ thể:</label><br>
                    <textarea id="so-thich" name="so-thich" rows="4" required><?= e($_POST['so-thich'] ?? '') ?></textarea>
                </p>

                <p class="trang-lien-he__truong">
                    <label for="anh">Ảnh tham khảo (không bắt buộc):</label><br>
                    <input type="file" id="anh" name="anh" accept="image/jpeg,image/png,image/webp" class="trang-lien-he__tep">
                </p>

                <p class="trang-lien-he__dong-y">
                    <label class="trang-lien-he__nhan-dong-y">
                        <input type="checkbox" id="dong-y" name="dong-y" required>
                        <span>Tôi đồng ý cung cấp thông tin để được tư vấn.</span>
                    </label>
                </p>
            </fieldset>

            <div class="trang-lien-he__nut-nhom">
                <button type="submit" class="trang-lien-he__nut-gui">
                    Gửi yêu cầu tư vấn
                </button>
                <button type="reset" class="trang-lien-he__nut-nhap-lai">
                    Nhập lại
                </button>
            </div>
        </form>
    </section>
</main>

<?php require_once __DIR__ . '/inc/footer.php'; ?>