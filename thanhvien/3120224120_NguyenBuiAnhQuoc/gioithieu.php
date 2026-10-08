<?php
/**
 * Trang giới thiệu cá nhân của Nguyễn Bùi Anh Quốc.
 * Thành viên nhóm 15 - Phần B.
 * Chức năng: lưu mục tiêu cá nhân và tính tiến độ học tập.
 * Dữ liệu mục tiêu lưu phía máy chủ và xử lý theo PRG.
 * MSSV: 3120224120
 */

declare(strict_types=1);

require __DIR__ . '/../../inc/config.php';

$baseUrl = '../../';
$tieuDeTrang = 'Giới thiệu cá nhân - Nguyễn Bùi Anh Quốc';

$tepMucTieu = __DIR__ . '/../../storage/3120224120_muctieu.json';

$loiMucTieu = '';
$danhSachMucTieu = [];
$thongBaoMucTieu = $_SESSION['thong_bao_muc_tieu'] ?? '';
unset($_SESSION['thong_bao_muc_tieu']);

$loiTienDo = '';
$ketQuaTienDo = '';
$thongBaoTienDo = $_SESSION['thong_bao_tien_do'] ?? '';
unset($_SESSION['thong_bao_tien_do']);

/**
 * Đọc danh sách mục tiêu từ file JSON.
 */
function docMucTieu(string $tep): array
{
    if (!is_file($tep)) {
        return [];
    }

    $noiDung = file_get_contents($tep);

    if ($noiDung === false || trim($noiDung) === '') {
        return [];
    }

    $duLieu = json_decode($noiDung, true);

    return is_array($duLieu) ? $duLieu : [];
}

/**
 * Lưu danh sách mục tiêu vào file JSON.
 */
function luuMucTieu(string $tep, array $danhSach): bool
{
    $noiDung = json_encode(
        $danhSach,
        JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
    );

    if ($noiDung === false) {
        return false;
    }

    return file_put_contents(
        $tep,
        $noiDung,
        LOCK_EX
    ) !== false;
}

$danhSachMucTieu = docMucTieu($tepMucTieu);

/*
 * Chức năng 2: tính tiến độ học tập.
 */
if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['tinh_tien_do'])
) {
    $tongSoBai = filter_var(
        $_POST['tong_so_bai'] ?? null,
        FILTER_VALIDATE_INT
    );

    $soBaiHoanThanh = filter_var(
        $_POST['so_bai_hoan_thanh'] ?? null,
        FILTER_VALIDATE_INT
    );

    if ($tongSoBai === false || $tongSoBai <= 0) {

        $loiTienDo = 'Tổng số bài phải là số nguyên lớn hơn 0.';

    } elseif (
        $soBaiHoanThanh === false
        || $soBaiHoanThanh < 0
        || $soBaiHoanThanh > $tongSoBai
    ) {

        $loiTienDo =
            'Số bài hoàn thành phải từ 0 đến tổng số bài.';

    } else {

        $phanTram = ($soBaiHoanThanh / $tongSoBai) * 100;

        if ($phanTram >= 80) {
            $xepLoaiTienDo = 'Hoàn thành tốt';
        } elseif ($phanTram >= 50) {
            $xepLoaiTienDo = 'Đang tiến bộ';
        } else {
            $xepLoaiTienDo = 'Cần cố gắng thêm';
        }

        $ketQuaTienDo =
            'Tiến độ: '
            . number_format($phanTram, 1)
            . '% - '
            . $xepLoaiTienDo;

        $_SESSION['thong_bao_tien_do'] = $ketQuaTienDo;

        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit;
    }
}

/*
 * Chức năng 1: lưu mục tiêu cá nhân.
 */
if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['muc_tieu'])
) {
    $mucTieu = trim(
        (string) ($_POST['muc_tieu'] ?? '')
    );

    if ($mucTieu === '') {

        $loiMucTieu = 'Vui lòng nhập mục tiêu.';

    } elseif (mb_strlen($mucTieu) > 100) {

        $loiMucTieu =
            'Mục tiêu không được vượt quá 100 ký tự.';

    } else {

        $danhSachMucTieu[] = [
            'muc_tieu' => $mucTieu,
            'thoi_gian' => date('d/m/Y H:i')
        ];

        if (luuMucTieu($tepMucTieu, $danhSachMucTieu)) {

            $_SESSION['thong_bao_muc_tieu'] =
                'Đã lưu mục tiêu thành công.';

            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit;

        } else {

            $loiMucTieu =
                'Không thể lưu mục tiêu. Vui lòng thử lại.';
        }
    }
}

$danhSachMucTieu = docMucTieu($tepMucTieu);
?>

<?php require __DIR__ . '/../../inc/header.php'; ?>

<div class="trang-ca-nhan">

  <main>

    <h1>Trang Giới Thiệu Cá Nhân</h1>

    <section class="khoi-dau thong-tin-ca-nhan">

      <h2>Thông tin cá nhân</h2>

      <img
        class="anh-chan-dung"
        src="anh_the.jpg"
        alt="Ảnh chân dung của Nguyễn Bùi Anh Quốc"
        width="300"
        height="400"
      >

      <div class="thong-tin-chinh">

        <p>
          <strong>Họ và tên:</strong> Nguyễn Bùi Anh Quốc
        </p>

        <p>
          <strong>Lớp:</strong> 24CNTT3
        </p>

        <p>
          <strong>Vai trò trong nhóm:</strong>
          Thành viên - Phụ trách xây dựng trang giới thiệu thành viên
          và hỗ trợ nội dung đồ án.
        </p>

      </div>

    </section>

    <article class="muc-noi-dung">

      <h2>Dự án &amp; Sở thích</h2>

      <h3>1. Đóng góp trong đồ án nhóm</h3>

      <p>
        Tham gia xây dựng giao diện trang giới thiệu thành viên,
        đóng góp mã nguồn HTML5/CSS3 chuẩn W3C Validator và
        quản lý phiên bản trên GitHub.
      </p>

      <h3>2. Danh sách kỹ năng</h3>

      <div class="danh-sach-ky-nang">

        <div class="the-ky-nang">
          <h3>Lập trình Web</h3>
          <p>HTML5, CSS cơ bản</p>
        </div>

        <div class="the-ky-nang">
          <h3>Ngôn ngữ lập trình</h3>
          <p>Python, C++</p>
        </div>

        <div class="the-ky-nang">
          <h3>Quản lý mã nguồn</h3>
          <p>Git &amp; GitHub</p>
        </div>

        <div class="the-ky-nang">
          <h3>Mạng &amp; CSDL</h3>
          <p>Mạng máy tính, Cơ sở dữ liệu cơ bản</p>
        </div>

      </div>

      <h3>3. Sở thích cá nhân</h3>

      <ul>
        <li>Bóng đá</li>
        <li>Cầu lông</li>
        <li>Ăn uống</li>
      </ul>

    </article>

    <section class="muc-noi-dung">

      <h2>Thời khóa biểu cá nhân tuần này</h2>

      <div class="khung-chua-bang" tabindex="0">

        <table>

          <caption>
            Thời khóa biểu học tập trong tuần
          </caption>

          <thead>
            <tr>
              <th scope="col">Thời gian</th>
              <th scope="col">Thứ 2</th>
              <th scope="col">Thứ 3</th>
              <th scope="col">Thứ 4</th>
              <th scope="col">Thứ 5</th>
              <th scope="col">Thứ 6</th>
            </tr>
          </thead>

          <tbody>

            <tr>
              <th scope="row">Sáng</th>
              <td>Tự học</td>
              <td>Công nghệ phần mềm</td>
              <td>AI</td>
              <td>Lập trình Web</td>
              <td>Tự học</td>
            </tr>

            <tr>
              <th scope="row">Chiều</th>
              <td>Tự học</td>
              <td>Lập trình Web</td>
              <td>Tự học</td>
              <td>Làm bài nhóm</td>
              <td>Khai phá dữ liệu</td>
            </tr>

          </tbody>

        </table>

      </div>

    </section>

    <section class="muc-noi-dung">

      <h2>Mục tiêu cá nhân</h2>

      <form method="post">

        <p>
          <label for="muc_tieu">Nhập mục tiêu:</label>

          <input
            type="text"
            id="muc_tieu"
            name="muc_tieu"
            maxlength="100"
            required
          >
        </p>

        <button type="submit">
          Ghi nhận mục tiêu
        </button>

      </form>

      <?php if ($loiMucTieu !== ''): ?>

        <p role="alert">
          <?= e($loiMucTieu) ?>
        </p>

      <?php endif; ?>

      <?php if ($thongBaoMucTieu !== ''): ?>

        <p>
          <?= e($thongBaoMucTieu) ?>
        </p>

      <?php endif; ?>

      <?php if ($danhSachMucTieu !== []): ?>

        <h3>Danh sách mục tiêu đã lưu</h3>

        <ul>

          <?php foreach ($danhSachMucTieu as $mucTieu): ?>

            <li>
              <?= e($mucTieu['muc_tieu'] ?? '') ?>
              -
              <?= e($mucTieu['thoi_gian'] ?? '') ?>
            </li>

          <?php endforeach; ?>

        </ul>

      <?php endif; ?>

    </section>

    <section class="muc-noi-dung">

      <h2>Tính tiến độ học tập</h2>

      <form method="post" >

        <p>
          <label for="tong_so_bai">
            Tổng số bài:
          </label>

          <input
            type="number"
            id="tong_so_bai"
            name="tong_so_bai"
            min="1"
            required
          >
        </p>

        <p>
          <label for="so_bai_hoan_thanh">
            Số bài đã hoàn thành:
          </label>

          <input
            type="number"
            id="so_bai_hoan_thanh"
            name="so_bai_hoan_thanh"
            min="0"
            required
          >
        </p>

        <button type="submit" name="tinh_tien_do">
          Tính tiến độ
        </button>

      </form>

      <?php if ($loiTienDo !== ''): ?>

        <p role="alert">
          <?= e($loiTienDo) ?>
        </p>

      <?php endif; ?>

      <?php if ($thongBaoTienDo !== ''): ?>

        <p>
          <?= e($thongBaoTienDo) ?>
        </p>

      <?php endif; ?>

    </section>

  </main>

</div>

<script src="js/canhan.js"></script>

<?php require __DIR__ . '/../../inc/footer.php'; ?>