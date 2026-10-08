<?php
/**
 * Tệp: thanhvien/3120224032_dung/gioithieu.php
 * Tác giả: Nguyễn Phạm Tiến Dũng (MSSV: 3120224032 - Lớp 24CNTT2)
 * Mô tả: Trang cá nhân sử dụng chung header/footer của nhóm.
 * Chức năng PHP máy chủ:
 *  1. Bộ đếm lượt xem trang cá nhân (Session + File storage/3120224032_luotxem.txt).
 *  2. Sổ lưu bút cá nhân (Validation máy chủ, PRG, lưu storage/3120224032_luubut.jsonl).
 */

declare(strict_types=1);

require_once __DIR__ . '/../../inc/config.php';

$baseUrl = '../../';
$goc = '../../';
$tieuDeTrang = 'Giới thiệu cá nhân - Nguyễn Phạm Tiến Dũng';
$tieuDe = $tieuDeTrang;

$fileLuotXem = __DIR__ . '/../../storage/3120224032_luotxem.txt';
$fileLuuBut = __DIR__ . '/../../storage/3120224032_luubut.jsonl';

/* 1. Bộ đếm lượt xem */
if (!isset($_SESSION['da_xem_trang_dung'])) {
    $_SESSION['da_xem_trang_dung'] = true;
    $count = is_file($fileLuotXem) ? (int)file_get_contents($fileLuotXem) : 0;
    $count++;
    file_put_contents($fileLuotXem, (string)$count, LOCK_EX);
} else {
    $count = is_file($fileLuotXem) ? (int)file_get_contents($fileLuotXem) : 1;
}

/* 2. Sổ lưu bút */
$loiLuuBut = [];
$duLieuLuuBut = ['ten' => '', 'noiDung' => ''];
$thongBaoLuuBut = $_SESSION['thong_bao_luu_but'] ?? '';
unset($_SESSION['thong_bao_luu_but']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['gui_luu_but'])) {
    $duLieuLuuBut['ten'] = trim((string)($_POST['ten'] ?? ''));
    $duLieuLuuBut['noiDung'] = trim((string)($_POST['noiDung'] ?? ''));

    if ($duLieuLuuBut['ten'] === '') {
        $loiLuuBut['ten'] = 'Vui lòng nhập họ tên của bạn.';
    } elseif (mb_strlen($duLieuLuuBut['ten']) > 50) {
        $loiLuuBut['ten'] = 'Họ tên không được vượt quá 50 ký tự.';
    }

    if ($duLieuLuuBut['noiDung'] === '') {
        $loiLuuBut['noiDung'] = 'Vui lòng nhập nội dung lời nhắn.';
    } elseif (mb_strlen($duLieuLuuBut['noiDung']) > 300) {
        $loiLuuBut['noiDung'] = 'Lời nhắn không được vượt quá 300 ký tự.';
    }

    if ($loiLuuBut === []) {
        $record = [
            'thoi_gian' => date('d/m/Y H:i'),
            'ten'       => $duLieuLuuBut['ten'],
            'noiDung'   => $duLieuLuuBut['noiDung']
        ];
        
        $line = json_encode($record, JSON_UNESCAPED_UNICODE) . PHP_EOL;
        file_put_contents($fileLuuBut, $line, FILE_APPEND | LOCK_EX);

        $_SESSION['thong_bao_luu_but'] = 'Đã gửi lời nhắn thành công!';

        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit;
    }
}

$danhSachLuuBut = [];
if (is_file($fileLuuBut)) {
    $lines = file($fileLuuBut, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines !== false) {
        $all = array_map(fn($l) => json_decode($l, true), $lines);
        $all = array_reverse(array_filter($all, 'is_array'));
        $danhSachLuuBut = array_slice($all, 0, 5);
    }
}
?>

<?php require_once __DIR__ . '/../../inc/header.php'; ?>

<!-- LINK CSS CÁ NHÂN -->
<link rel="stylesheet" href="css/style.css">

<div class="trang-ca-nhan">

  <main>

    <h1>Trang Giới Thiệu Cá Nhân</h1>

    <!-- 1. THÔNG TIN CÁ NHÂN -->
    <div class="khoi-dau">

      <img
        class="anh-chan-dung"
        src="dung_anh.jpg"
        alt="Ảnh chân dung của Nguyễn Phạm Tiến Dũng"
      >

      <div class="thong-tin-chinh">

        <p>
          <strong>Họ và tên:</strong> Nguyễn Phạm Tiến Dũng
        </p>

        <p class="chuc-danh">Thành viên nhóm 15 - Lớp 24CNTT2</p>

        <p>
          <strong>MSSV:</strong> 3120224032
        </p>

        <p>
          <strong>Email:</strong> 
          <span id="my-email">dungshity@gmail.com</span>
          <button type="button" id="btn-copy-email" class="btn-copy">📋 Sao chép Email</button>
          <span id="copy-status" class="toast-message"></span>
        </p>

        <p>
          <strong>Lượt xem trang cá nhân:</strong> <?= e((string)$count) ?> lượt
        </p>

        <p>
          <strong>Vai trò trong nhóm:</strong>
          Phụ trách xây dựng backend PHP &amp; xử lý dữ liệu động cho đồ án.
        </p>

      </div>

    </div>

    <!-- 2. ĐỒNG HỒ ĐẾM NGƯỜC -->
    <section class="muc-noi-dung">
      <h2>Đồng hồ đếm ngược ngày thi cuối kỳ</h2>
      
      <p id="countdown-status" class="countdown-status"></p>

      <div class="countdown-timer">
        <div class="time-box">
          <span id="cd-days">00</span>
          <label>Ngày</label>
        </div>
        <div class="time-box">
          <span id="cd-hours">00</span>
          <label>Giờ</label>
        </div>
        <div class="time-box">
          <span id="cd-minutes">00</span>
          <label>Phút</label>
        </div>
        <div class="time-box">
          <span id="cd-seconds">00</span>
          <label>Giây</label>
        </div>
      </div>
    </section>

    <!-- 3. DỰ ÁN VÀ SỞ THÍCH -->
    <article class="muc-noi-dung">

      <h2>Dự án &amp; Sở thích</h2>

      <h3>1. Đóng góp trong đồ án nhóm</h3>

      <p>
        Xây dựng kiến trúc xử lý phía máy chủ, tổ chức mã nguồn PHP theo mô-đun, 
        kiểm tra an toàn biểu mẫu và quản lý mã nguồn trên GitHub.
      </p>

      <h3>2. Danh sách kỹ năng</h3>

      <div class="danh-sach-ky-nang">

        <div class="the-ky-nang">
          <h3>Lập trình Web</h3>
          <p>HTML5, CSS3, PHP 8, JavaScript</p>
        </div>

        <div class="the-ky-nang">
          <h3>Cơ sở dữ liệu</h3>
          <p>MySQL, PostgreSQL</p>
        </div>

        <div class="the-ky-nang">
          <h3>Công cụ phát triển</h3>
          <p>Git, GitHub, VS Code, Composer</p>
        </div>

      </div>

      <h3>3. Sở thích cá nhân</h3>

      <ul>
        <li>Tập Gym / Fitness</li>
        <li>Chơi game giải trí</li>
        <li>Xem phim AI</li>
      </ul>

    </article>

    <!-- 4. SỔ LƯU BÚT -->
    <section class="muc-noi-dung">

      <h2>Sổ lưu bút cá nhân</h2>

      <form method="post">

        <p>
          <label for="ten">Họ tên của bạn:</label>
          <input
            type="text"
            id="ten"
            name="ten"
            maxlength="50"
            value="<?= e($duLieuLuuBut['ten']) ?>"
            required
          >
        </p>

        <?php if (isset($loiLuuBut['ten'])): ?>
          <p role="alert" style="color: #dc2626;"><?= e($loiLuuBut['ten']) ?></p>
        <?php endif; ?>

        <p>
          <label for="noiDung">Lời nhắn:</label>
          <textarea
            id="noiDung"
            name="noiDung"
            rows="3"
            maxlength="300"
            required
          ><?= e($duLieuLuuBut['noiDung']) ?></textarea>
        </p>

        <?php if (isset($loiLuuBut['noiDung'])): ?>
          <p role="alert" style="color: #dc2626;"><?= e($loiLuuBut['noiDung']) ?></p>
        <?php endif; ?>

        <button type="submit" name="gui_luu_but" class="nut-ve-trang-chu">
          Gửi lời nhắn
        </button>

      </form>

      <?php if ($thongBaoLuuBut !== ''): ?>
        <p style="color: #16a34a; font-weight: bold;">
          <?= e($thongBaoLuuBut) ?>
        </p>
      <?php endif; ?>

      <?php if ($danhSachLuuBut !== []): ?>

        <h3>Lời nhắn mới nhất</h3>

        <ul>
          <?php foreach ($danhSachLuuBut as $lb): ?>
            <li>
              <strong><?= e($lb['ten'] ?? '') ?></strong>
              <small>(<?= e($lb['thoi_gian'] ?? '') ?>)</small>:
              <div><?= e($lb['noiDung'] ?? '') ?></div>
            </li>
          <?php endforeach; ?>
        </ul>

      <?php endif; ?>

    </section>

  </main>

</div>

<script src="js/canhan.js"></script>

<?php require_once __DIR__ . '/../../inc/footer.php'; ?>