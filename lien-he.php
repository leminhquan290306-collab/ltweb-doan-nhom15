<?php

require_once __DIR__ . '/inc/config.php';

use App\Data\KhoLienHe;

$tieuDeTrang = 'Liên hệ và tư vấn - Quà Tặng Thông Minh';
$baseUrl = '';

$thongBao = $_SESSION['thong_bao_lien_he'] ?? '';
unset($_SESSION['thong_bao_lien_he']);

$loiForm = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /* =========================
       LẤY DỮ LIỆU FORM
       ========================= */

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

    /* =========================
       KIỂM TRA CSRF
       ========================= */

    if (!kiemTraCsrfToken($_POST['csrf_token'] ?? null)) {
        $loiForm[] =
            'Phiên gửi biểu mẫu không hợp lệ. Vui lòng tải lại trang.';
    }

    /* =========================
       KIỂM TRA HỌ TÊN
       ========================= */

    if (
        $hoTen === ''
        || mb_strlen($hoTen) < 2
        || mb_strlen($hoTen) > 50
        || !preg_match('/^[\p{L}\s]+$/u', $hoTen)
    ) {
        $loiForm[] =
            'Họ tên phải từ 2 đến 50 ký tự và chỉ gồm chữ cái, khoảng trắng.';
    }

    /* =========================
       KIỂM TRA EMAIL
       ========================= */

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $loiForm[] = 'Email không hợp lệ.';
    }

    /* =========================
       KIỂM TRA SỐ ĐIỆN THOẠI
       ========================= */

    if (!preg_match('/^[0-9]{10}$/', $soDienThoai)) {
        $loiForm[] =
            'Số điện thoại phải gồm đúng 10 chữ số.';
    }

    /* =========================
       KIỂM TRA NGƯỜI NHẬN
       ========================= */

    $nguoiNhanHopLe = [
        'ban-be',
        'nguoi-yeu',
        'gia-dinh',
        'dong-nghiep'
    ];

    if (!in_array($nguoiNhan, $nguoiNhanHopLe, true)) {
        $loiForm[] =
            'Vui lòng chọn người nhận quà hợp lệ.';
    }

    /* =========================
       KIỂM TRA DỊP TẶNG
       ========================= */

    $dipTangHopLe = [
        'sinh-nhat',
        'ky-niem',
        'le',
        'khac'
    ];

    if (!in_array($dipTang, $dipTangHopLe, true)) {
        $loiForm[] =
            'Vui lòng chọn dịp tặng hợp lệ.';
    }

    /* =========================
       KIỂM TRA NGÂN SÁCH
       5.000 - 100.000.000
       Bước 1.000
       ========================= */

    if (
        $nganSach === false
        || $nganSach < 5000
        || $nganSach > 100000000
        || $nganSach % 1000 !== 0
    ) {
        $loiForm[] =
            'Ngân sách phải từ 5.000 đến 100.000.000 VNĐ và theo bước 1.000 VNĐ.';
    }

    /* =========================
       KIỂM TRA NGÀY CẦN QUÀ
       ========================= */

    if ($ngayCanQua === '') {

        $loiForm[] =
            'Vui lòng chọn ngày cần quà.';

    } else {

        $ngay = DateTime::createFromFormat(
            'Y-m-d',
            $ngayCanQua
        );

        if (
            !$ngay
            || $ngay->format('Y-m-d') !== $ngayCanQua
        ) {
            $loiForm[] =
                'Ngày cần quà không hợp lệ.';
        }
    }

    /* =========================
       KIỂM TRA SỞ THÍCH
       ========================= */

    if ($soThich === '') {

        $loiForm[] =
            'Vui lòng nhập sở thích hoặc yêu cầu.';

    } elseif (mb_strlen($soThich) > 1000) {

        $loiForm[] =
            'Sở thích hoặc yêu cầu không được vượt quá 1000 ký tự.';
    }

    /* =========================
       KIỂM TRA ĐỒNG Ý
       ========================= */

    if (!$dongY) {

        $loiForm[] =
            'Bạn cần đồng ý cung cấp thông tin để được tư vấn.';
    }

    /* =========================
       KIỂM TRA UPLOAD ẢNH
       ========================= */

    $tenAnhDaLuu = null;

    if (
        isset($_FILES['anh'])
        && is_array($_FILES['anh'])
        && $_FILES['anh']['error'] !== UPLOAD_ERR_NO_FILE
    ) {

        $anh = $_FILES['anh'];

        /* Kiểm tra lỗi upload */

        if ($anh['error'] !== UPLOAD_ERR_OK) {

            $loiForm[] =
                'Không thể tải ảnh lên. Vui lòng thử lại.';

        } else {

            /* Kiểm tra dung lượng tối đa 2MB */

            if ((int) $anh['size'] > 2 * 1024 * 1024) {

                $loiForm[] =
                    'Ảnh tải lên không được vượt quá 2MB.';
            }

            /* Kiểm tra MIME bằng finfo */

            if (is_uploaded_file($anh['tmp_name'])) {

                $finfo = new finfo(FILEINFO_MIME_TYPE);

                $mime = $finfo->file($anh['tmp_name']);

                $mimeHopLe = [
                    'image/jpeg' => 'jpg',
                    'image/png' => 'png',
                    'image/webp' => 'webp'
                ];

                if (!isset($mimeHopLe[$mime])) {

                    $loiForm[] =
                        'Ảnh chỉ được phép có định dạng JPG, PNG hoặc WEBP.';

                } else {

                    /*
                     * Lưu tạm MIME và phần mở rộng.
                     * Chỉ di chuyển file sau khi toàn bộ
                     * validation của form thành công.
                     */

                    $phanMoRongAnh = $mimeHopLe[$mime];
                }

            } else {

                $loiForm[] =
                    'File tải lên không hợp lệ.';
            }
        }
    }

    /* =========================
       NẾU TẤT CẢ HỢP LỆ
       ========================= */

    if (!$loiForm) {

        /*
         * Nếu có ảnh thì tạo tên ngẫu nhiên
         * và lưu vào uploads/
         */

        if (
            isset($_FILES['anh'])
            && is_array($_FILES['anh'])
            && $_FILES['anh']['error'] === UPLOAD_ERR_OK
        ) {

            $thuMucUploads = __DIR__ . '/uploads';

            if (!is_dir($thuMucUploads)) {

                if (!mkdir($thuMucUploads, 0755, true)) {

                    $loiForm[] =
                        'Không thể tạo thư mục lưu ảnh.';
                }
            }

            if (!$loiForm) {

                $tenNgauNhien =
                    bin2hex(random_bytes(16))
                    . '.'
                    . $phanMoRongAnh;

                $duongDanAnh =
                    $thuMucUploads . '/' . $tenNgauNhien;

                if (
                    !move_uploaded_file(
                        $_FILES['anh']['tmp_name'],
                        $duongDanAnh
                    )
                ) {

                    $loiForm[] =
                        'Không thể lưu ảnh tải lên.';
                } else {

                    $tenAnhDaLuu = $tenNgauNhien;
                }
            }
        }
    }

    /* =========================
       LƯU DỮ LIỆU
       ========================= */

    if (!$loiForm) {

        $duLieu = [
            'thoiGian' => date('c'),
            'hoTen' => $hoTen,
            'email' => $email,
            'soDienThoai' => $soDienThoai,
            'nguoiNhan' => $nguoiNhan,
            'dipTang' => $dipTang,
            'nganSach' => $nganSach,
            'ngayCanQua' => $ngayCanQua,
            'soThich' => $soThich,
            'dongY' => true,
            'anh' => $tenAnhDaLuu
        ];

        try {

            $khoLienHe = new KhoLienHe();

            $khoLienHe->luu($duLieu);

            /*
             * PRG:
             * POST -> lưu dữ liệu -> chuyển hướng sang GET
             */

            $_SESSION['thong_bao_lien_he'] =
                'Gửi yêu cầu tư vấn thành công!';

            redirect('lien-he.php');

        } catch (Throwable $e) {

            /*
             * Nếu lưu JSON thất bại thì xóa ảnh vừa upload
             * để tránh tạo file rác.
             */

            if ($tenAnhDaLuu !== null) {

                $duongDanAnh =
                    __DIR__ . '/uploads/' . $tenAnhDaLuu;

                if (is_file($duongDanAnh)) {
                    unlink($duongDanAnh);
                }
            }

            $loiForm[] =
                'Không thể lưu thông tin liên hệ. Vui lòng thử lại.';
        }
    }

    if ($loiForm) {

        $thongBao =
            'Vui lòng kiểm tra lại thông tin trong biểu mẫu.';
    }
}

$csrfToken = taoCsrfToken();

$jsFiles = [
    'js/trang-lien-he.js',
    'js/yeu-thich.js'
];

require_once __DIR__ . '/inc/header.php';

?>

<main>

    <section class="form-lien-he">

        <h2>Đăng ký tư vấn</h2>

        <?php if ($thongBao !== ''): ?>

            <p
                id="thong-bao-lien-he"
                aria-live="polite">
                <?= e($thongBao) ?>
            </p>

        <?php endif; ?>

        <?php if ($loiForm): ?>

            <div
                class="thong-bao-loi"
                role="alert">

                <ul>

                    <?php foreach ($loiForm as $loi): ?>

                        <li>
                            <?= e($loi) ?>
                        </li>

                    <?php endforeach; ?>

                </ul>

            </div>

        <?php endif; ?>

        <form
            id="form-lien-he"
            action="lien-he.php"
            method="post"
            enctype="multipart/form-data"
            novalidate>

            <input
                type="hidden"
                name="csrf_token"
                value="<?= e($csrfToken) ?>">

            <fieldset>

                <legend>Thông tin liên hệ</legend>

                <p class="form-nhom">

                    <label for="ho-ten">
                        Họ và tên:
                    </label>

                    <input
                        type="text"
                        id="ho-ten"
                        name="ho-ten"
                        pattern="[A-Za-zÀ-ỹĐđ\s]{2,50}"
                        value="<?= e($_POST['ho-ten'] ?? '') ?>"
                        required>

                    <small
                        class="loi-truong"
                        id="loi-ho-ten"
                        aria-live="polite">
                    </small>

                </p>

                <p class="form-nhom">

                    <label for="email">
                        Email:
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="<?= e($_POST['email'] ?? '') ?>"
                        required>

                    <small
                        class="loi-truong"
                        id="loi-email"
                        aria-live="polite">
                    </small>

                </p>

                <p class="form-nhom">

                    <label for="so-dien-thoai">
                        Số điện thoại:
                    </label>

                    <input
                        type="tel"
                        id="so-dien-thoai"
                        name="so-dien-thoai"
                        pattern="[0-9]{10}"
                        value="<?= e($_POST['so-dien-thoai'] ?? '') ?>"
                        required>

                    <small
                        class="loi-truong"
                        id="loi-so-dien-thoai"
                        aria-live="polite">
                    </small>

                </p>

                <p class="form-nhom">

                    <label for="nguoi-nhan">
                        Người nhận quà:
                    </label>

                    <select
                        id="nguoi-nhan"
                        name="nguoi-nhan"
                        required>

                        <option value="">
                            -- Chọn người nhận --
                        </option>

                        <option
                            value="ban-be"
                            <?= (($_POST['nguoi-nhan'] ?? '') === 'ban-be') ? 'selected' : '' ?>>
                            Bạn bè
                        </option>

                        <option
                            value="nguoi-yeu"
                            <?= (($_POST['nguoi-nhan'] ?? '') === 'nguoi-yeu') ? 'selected' : '' ?>>
                            Người yêu
                        </option>

                        <option
                            value="gia-dinh"
                            <?= (($_POST['nguoi-nhan'] ?? '') === 'gia-dinh') ? 'selected' : '' ?>>
                            Gia đình
                        </option>

                        <option
                            value="dong-nghiep"
                            <?= (($_POST['nguoi-nhan'] ?? '') === 'dong-nghiep') ? 'selected' : '' ?>>
                            Đồng nghiệp
                        </option>

                    </select>

                    <small
                        class="loi-truong"
                        id="loi-nguoi-nhan"
                        aria-live="polite">
                    </small>

                </p>

                <p class="form-nhom">

                    <label for="dip-tang">
                        Dịp tặng:
                    </label>

                    <select
                        id="dip-tang"
                        name="dip-tang"
                        required>

                        <option value="">
                            -- Chọn dịp tặng --
                        </option>

                        <option
                            value="sinh-nhat"
                            <?= (($_POST['dip-tang'] ?? '') === 'sinh-nhat') ? 'selected' : '' ?>>
                            Sinh nhật
                        </option>

                        <option
                            value="ky-niem"
                            <?= (($_POST['dip-tang'] ?? '') === 'ky-niem') ? 'selected' : '' ?>>
                            Kỷ niệm
                        </option>

                        <option
                            value="le"
                            <?= (($_POST['dip-tang'] ?? '') === 'le') ? 'selected' : '' ?>>
                            Ngày lễ
                        </option>

                        <option
                            value="khac"
                            <?= (($_POST['dip-tang'] ?? '') === 'khac') ? 'selected' : '' ?>>
                            Dịp khác
                        </option>

                    </select>

                    <small
                        class="loi-truong"
                        id="loi-dip-tang"
                        aria-live="polite">
                    </small>

                </p>

                <p class="form-nhom">

                    <label for="ngan-sach">
                        Ngân sách dự kiến (VNĐ):
                    </label>

                    <input
                        type="number"
                        id="ngan-sach"
                        name="ngan-sach"
                        min="5000"
                        max="100000000"
                        step="1000"
                        value="<?= e($_POST['ngan-sach'] ?? '') ?>"
                        required>

                    <small
                        class="loi-truong"
                        id="loi-ngan-sach"
                        aria-live="polite">
                    </small>

                </p>

                <p class="form-nhom">

                    <label for="ngay-can-qua">
                        Ngày cần quà:
                    </label>

                    <input
                        type="date"
                        id="ngay-can-qua"
                        name="ngay-can-qua"
                        value="<?= e($_POST['ngay-can-qua'] ?? '') ?>"
                        required>

                    <small
                        class="loi-truong"
                        id="loi-ngay-can-qua"
                        aria-live="polite">
                    </small>

                </p>

                <p class="form-nhom">

                    <label for="so-thich">
                        Sở thích hoặc yêu cầu:
                    </label>

                    <textarea
                        id="so-thich"
                        name="so-thich"
                        rows="5"
                        required><?= e($_POST['so-thich'] ?? '') ?></textarea>

                    <small
                        class="loi-truong"
                        id="loi-so-thich"
                        aria-live="polite">
                    </small>

                </p>

                <!-- UPLOAD ẢNH -->

                <p class="form-nhom">

                    <label for="anh">
                        Ảnh tham khảo (không bắt buộc):
                    </label>

                    <input
                        type="file"
                        id="anh"
                        name="anh"
                        accept="image/jpeg,image/png,image/webp">

                    <small>
                        Chỉ nhận JPG, PNG, WEBP và dung lượng tối đa 2MB.
                    </small>

                </p>

                <p class="form-checkbox">

                    <label>

                        <input
                            type="checkbox"
                            id="dong-y"
                            name="dong-y"
                            required>

                        Tôi đồng ý cung cấp thông tin để được tư vấn.

                    </label>

                    <small
                        class="loi-truong"
                        id="loi-dong-y"
                        aria-live="polite">
                    </small>

                </p>

            </fieldset>

            <div class="nhom-nut">

                <button
                    id="nut-gui-lien-he"
                    type="submit"
                    class="btn btn--chinh">
                    Gửi yêu cầu tư vấn
                </button>

                <button
                    type="reset"
                    class="btn btn--phu">
                    Nhập lại
                </button>

            </div>

        </form>

    </section>

    <section class="card thong-tin-lien-he">

        <div>

            <h2 class="card__title">
                Lưu ý
            </h2>

            <p>
                Thông tin bạn cung cấp giúp website hiểu rõ hơn
                về người nhận, dịp tặng và ngân sách để đưa ra
                gợi ý phù hợp.
            </p>

        </div>

    </section>

</main>

<?php require_once __DIR__ . '/inc/footer.php'; ?>