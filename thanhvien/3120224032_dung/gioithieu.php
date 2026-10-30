<?php
/**
 * Tệp: thanhvien/3120224032_dung/gioithieu.php
 * Tác giả: Nguyễn Phạm Tiến Dũng (MSSV: 3120224032 - Lớp 24CNTT2)
 * Mô tả: Trang cá nhân sử dụng chung header/footer của nhóm.
 * Chức năng:
 *  1. Bộ đếm lượt xem lưu trong MySQL.
 *  2. Sổ lưu bút lưu trong MySQL.
 */

declare(strict_types=1);

require_once __DIR__ . '/../../inc/config.php';
require_once __DIR__ . '/../../inc/db.php';

$baseUrl = '../../';
$goc = '../../';
$tieuDeTrang = 'Giới thiệu cá nhân - Nguyễn Phạm Tiến Dũng';
$tieuDe = $tieuDeTrang;

$mssvDung = '3120224032';
$pdo = hamCSDL();


/* 1. Bộ đếm tổng và lượt xem theo ngày: mỗi phiên chỉ tăng một lần */
if (!isset($_SESSION['da_xem_trang_dung'])) {
    $pdo->beginTransaction();

    try {
        // Tăng bộ đếm tổng lượt xem hiện có.
        $stmt = $pdo->prepare(
            'INSERT INTO tv_luot_xem (mssv, luot_xem)
             VALUES (:mssv, 1)
             ON DUPLICATE KEY UPDATE luot_xem = luot_xem + 1'
        );
        $stmt->execute(['mssv' => $mssvDung]);

        // Ghi nhận lượt xem của ngày hiện tại.
        $stmt = $pdo->prepare(
            'INSERT INTO tv_luot_xem_ngay (mssv, ngay, luot_xem)
             VALUES (:mssv, CURDATE(), 1)
             ON DUPLICATE KEY UPDATE luot_xem = luot_xem + 1'
        );
        $stmt->execute(['mssv' => $mssvDung]);

        $pdo->commit();
        $_SESSION['da_xem_trang_dung'] = true;
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        throw $e;
    }
}
     $stmt = $pdo->prepare(
    'SELECT luot_xem
     FROM tv_luot_xem
     WHERE mssv = :mssv'

);
$stmt->execute(['mssv' => $mssvDung]);
$count = (int) $stmt->fetchColumn();

/* 1.1. Thống kê lượt xem trong 7 ngày gần nhất */
$stmt = $pdo->prepare(
    'SELECT ngay, luot_xem
     FROM tv_luot_xem_ngay
     WHERE mssv = :mssv
       AND ngay >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
       AND ngay <= CURDATE()
     ORDER BY ngay ASC'
);
$stmt->execute(['mssv' => $mssvDung]);
$luotXemTheoNgay = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* Bổ sung các ngày chưa có lượt xem để đủ 7 ngày */
$thongKeLuotXem = [];
$duLieuTheoNgay = [];

foreach ($luotXemTheoNgay as $dong) {
    $duLieuTheoNgay[$dong['ngay']] = (int) $dong['luot_xem'];
}

for ($i = 6; $i >= 0; $i--) {
    $ngay = date('Y-m-d', strtotime("-{$i} days"));

    $thongKeLuotXem[] = [
        'ngay' => date('d/m/Y', strtotime($ngay)),
        'luot_xem' => $duLieuTheoNgay[$ngay] ?? 0,
    ];
}

/* 2. Khởi tạo dữ liệu và thông báo sổ lưu bút */
$loiLuuBut = [];
$duLieuLuuBut = [
    'ten' => '',
    'noiDung' => '',
];

$thongBaoLuuBut = $_SESSION['thong_bao_luu_but'] ?? '';
unset($_SESSION['thong_bao_luu_but']);

/* 3. Xử lý gửi lời nhắn */
if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['gui_luu_but'])
) {
    $duLieuLuuBut['ten'] = trim(
        (string) ($_POST['ten'] ?? '')
    );
    $duLieuLuuBut['noiDung'] = trim(
        (string) ($_POST['noiDung'] ?? '')
    );

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
        $stmt = $pdo->prepare(
            'INSERT INTO tv_luu_but (mssv, ten, noi_dung)
             VALUES (:mssv, :ten, :noi_dung)'
        );

        $stmt->execute([
            'mssv' => $mssvDung,
            'ten' => $duLieuLuuBut['ten'],
            'noi_dung' => $duLieuLuuBut['noiDung'],
        ]);

        $_SESSION['thong_bao_luu_but'] =
            'Đã gửi lời nhắn thành công!';

        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit;
    }
}

/* 4. Lấy 5 lời nhắn mới nhất từ MySQL */
$stmt = $pdo->prepare(
    'SELECT
        ten,
        noi_dung AS noiDung,
        DATE_FORMAT(ngay_tao, "%d/%m/%Y %H:%i") AS thoi_gian
     FROM tv_luu_but
     WHERE mssv = :mssv
     ORDER BY id DESC
     LIMIT 5'
);
$stmt->execute(['mssv' => $mssvDung]);
$danhSachLuuBut = $stmt->fetchAll();
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
          <button type="button" id="btn-copy-email" class="btn-copy">
            📋 Sao chép Email
          </button>
          <span id="copy-status" class="toast-message"></span>
        </p>

        <p>
          <strong>Lượt xem trang cá nhân:</strong>
          <?= e((string) $count) ?> lượt
        </p>

        <section class="thong-ke-luot-xem">
          <h2>Thống kê lượt xem 7 ngày gần nhất</h2>

          <table>
            <thead>
              <tr>
                <th>Ngày</th>
                <th>Lượt xem</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($thongKeLuotXem as $dong): ?>
                <tr>
                  <td><?= e($dong['ngay']) ?></td>
                  <td><?= e((string) $dong['luot_xem']) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </section>

        <p>
          <strong>Vai trò trong nhóm:</strong>
          Phụ trách xây dựng backend PHP &amp; xử lý dữ liệu động cho đồ án.
        </p>

      </div>

    </div>

    <!-- 2. ĐỒNG HỒ ĐẾM NGƯỢC -->
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
          <p role="alert" style="color: #dc2626;">
            <?= e($loiLuuBut['ten']) ?>
          </p>
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
          <p role="alert" style="color: #dc2626;">
            <?= e($loiLuuBut['noiDung']) ?>
          </p>
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

      <?php else: ?>
        <p>Chưa có lời nhắn nào.</p>
      <?php endif; ?>

    </section>

  </main>

</div>

<script src="js/canhan.js"></script>

<?php require_once __DIR__ . '/../../inc/footer.php'; ?>