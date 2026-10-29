<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/inc/config.php';

$tieuDeTrang = 'Liên hệ và tư vấn - Quà Tặng Thông Minh';
$baseUrl = '';

$thongBao = $_SESSION['thong_bao_lien_he'] ?? '';
unset($_SESSION['thong_bao_lien_he']);

$loiForm = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

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
    if (isset($_FILES['anh']) && $_FILES['anh']['error'] === UPLOAD_ERR_OK) {
        $thuMucUploads = __DIR__ . '/uploads';
        if (!is_dir($thuMucUploads)) {
            mkdir($thuMucUploads, 0755, true);
        }
        $phanMoRong = pathinfo($_FILES['anh']['name'], PATHINFO_EXTENSION);
        $tenNgauNhien = bin2hex(random_bytes(8)) . '.' . $phanMoRong;
        if (move_uploaded_file($_FILES['anh']['tmp_name'], $thuMucUploads . '/' . $tenNgauNhien)) {
            $tenAnhDaLuu = $tenNgauNhien;
        }
    }

    if (empty($loiForm)) {
        // Gộp tất cả thông tin vào cột noi_dung
        $noiDungChiTiet = "SĐT: {$soDienThoai} | Người nhận: {$nguoiNhan} | Dịp: {$dipTang} | Ngân sách: " . number_format((float)$nganSach, 0, ',', '.') . "đ | Ngày cần: {$ngayCanQua} | Yêu cầu: {$soThich}" . ($tenAnhDaLuu ? " | Ảnh: {$tenAnhDaLuu}" : "");

        try {
            $pdo = hamCSDL();
            
            // Dùng đúng tên cột 'noi_dung' theo cấu trúc bảng CSDL thực tế của ông
            $stmt = $pdo->prepare("INSERT INTO lien_he (ten, email, noi_dung) VALUES (?, ?, ?)");
            $stmt->execute([$hoTen, $email, $noiDungChiTiet]);

            $_SESSION['thong_bao_lien_he'] = 'Gửi yêu cầu tư vấn thành công!';
            redirect('lien-he.php');

        } catch (Throwable $e) {
            $loiForm[] = 'Lỗi CSDL: ' . $e->getMessage();
        }
    }

    if (!empty($loiForm)) {
        $thongBao = 'Vui lòng kiểm tra lại thông tin trong biểu mẫu.';
    }
}

$csrfToken = function_exists('taoCsrfToken') ? taoCsrfToken() : '';
require_once __DIR__ . '/inc/header.php';
?>

<main class="container" style="max-width: 700px; margin: 30px auto; padding: 0 15px;">
    <section class="form-lien-he" style="background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <h2>Đăng ký tư vấn quà tặng</h2>

        <?php if ($thongBao !== ''): ?>
            <p style="padding: 10px; background: <?= empty($loiForm) ? '#d4edda' : '#f8d7da' ?>; color: <?= empty($loiForm) ? '#155724' : '#721c24' ?>; border-radius: 4px; margin-bottom: 20px;">
                <?= e($thongBao) ?>
            </p>
        <?php endif; ?>

        <?php if (!empty($loiForm)): ?>
            <div class="thong-bao-loi" role="alert" style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 4px; margin-bottom: 20px;">
                <ul style="margin: 0; padding-left: 20px;">
                    <?php foreach ($loiForm as $loi): ?>
                        <li><?= e($loi) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form id="form-lien-he" action="lien-he.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">

            <fieldset style="border: none; padding: 0; margin: 0;">
                <legend style="font-weight: bold; font-size: 18px; margin-bottom: 15px;">Thông tin người gửi</legend>

                <p style="margin-bottom: 15px;">
                    <label for="ho-ten">Họ và tên:</label><br>
                    <input type="text" id="ho-ten" name="ho-ten" value="<?= e($_POST['ho-ten'] ?? '') ?>" required style="width: 100%; padding: 8px; margin-top: 5px;">
                </p>

                <p style="margin-bottom: 15px;">
                    <label for="email">Email:</label><br>
                    <input type="email" id="email" name="email" value="<?= e($_POST['email'] ?? '') ?>" required style="width: 100%; padding: 8px; margin-top: 5px;">
                </p>

                <p style="margin-bottom: 15px;">
                    <label for="so-dien-thoai">Số điện thoại (10 chữ số):</label><br>
                    <input type="tel" id="so-dien-thoai" name="so-dien-thoai" value="<?= e($_POST['so-dien-thoai'] ?? '') ?>" required style="width: 100%; padding: 8px; margin-top: 5px;">
                </p>

                <p style="margin-bottom: 15px;">
                    <label for="nguoi-nhan">Người nhận quà:</label><br>
                    <select id="nguoi-nhan" name="nguoi-nhan" required style="width: 100%; padding: 8px; margin-top: 5px;">
                        <option value="">-- Chọn người nhận --</option>
                        <option value="Bạn bè">Bạn bè</option>
                        <option value="Người yêu">Người yêu</option>
                        <option value="Gia đình">Gia đình</option>
                        <option value="Đồng nghiệp">Đồng nghiệp</option>
                    </select>
                </p>

                <p style="margin-bottom: 15px;">
                    <label for="dip-tang">Dịp tặng:</label><br>
                    <select id="dip-tang" name="dip-tang" required style="width: 100%; padding: 8px; margin-top: 5px;">
                        <option value="">-- Chọn dịp tặng --</option>
                        <option value="Sinh nhật">Sinh nhật</option>
                        <option value="Kỷ niệm">Kỷ niệm</option>
                        <option value="Ngày lễ">Ngày lễ</option>
                        <option value="Khác">Khác</option>
                    </select>
                </p>

                <p style="margin-bottom: 15px;">
                    <label for="ngan-sach">Ngân sách dự kiến (VNĐ):</label><br>
                    <input type="number" id="ngan-sach" name="ngan-sach" min="5000" max="100000000" step="1000" value="<?= e($_POST['ngan-sach'] ?? '') ?>" required style="width: 100%; padding: 8px; margin-top: 5px;">
                </p>

                <p style="margin-bottom: 15px;">
                    <label for="ngay-can-qua">Ngày cần quà:</label><br>
                    <input type="date" id="ngay-can-qua" name="ngay-can-qua" value="<?= e($_POST['ngay-can-qua'] ?? '') ?>" required style="width: 100%; padding: 8px; margin-top: 5px;">
                </p>

                <p style="margin-bottom: 15px;">
                    <label for="so-thich">Sở thích hoặc yêu cầu cụ thể:</label><br>
                    <textarea id="so-thich" name="so-thich" rows="4" required style="width: 100%; padding: 8px; margin-top: 5px;"><?= e($_POST['so-thich'] ?? '') ?></textarea>
                </p>

                <p style="margin-bottom: 15px;">
                    <label for="anh">Ảnh tham khảo (không bắt buộc):</label><br>
                    <input type="file" id="anh" name="anh" accept="image/jpeg,image/png,image/webp" style="margin-top: 5px;">
                </p>

                <p style="margin-bottom: 20px;">
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="checkbox" id="dong-y" name="dong-y" required>
                        <span>Tôi đồng ý cung cấp thông tin để được tư vấn.</span>
                    </label>
                </p>
            </fieldset>

            <div style="display: flex; gap: 10px;">
                <button type="submit" style="background: #5a20cb; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;">
                    Gửi yêu cầu tư vấn
                </button>
                <button type="reset" style="background: #6c757d; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;">
                    Nhập lại
                </button>
            </div>
        </form>
    </section>
</main>

<?php require_once __DIR__ . '/inc/footer.php'; ?>