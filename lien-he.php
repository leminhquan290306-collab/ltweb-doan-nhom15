<!DOCTYPE html>

<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Liên hệ và tư vấn - Quà Tặng Thông Minh</title>

<meta name="description"
      content="Biểu mẫu liên hệ và đăng ký tư vấn quà tặng theo người nhận, dịp tặng, ngân sách và sở thích.">

<link rel="stylesheet" href="css/01-bien.css">
<link rel="stylesheet" href="css/02-chuan-hoa.css">
<link rel="stylesheet" href="css/03-bo-cuc.css">
<link rel="stylesheet" href="css/04-thanh-phan.css">
<link rel="stylesheet" href="css/05-tien-ich.css">


</head>

<body class="trang">

<header>
    <h1>Liên hệ và tư vấn quà tặng</h1>

    <p>
        Cung cấp một số thông tin để nhận được gợi ý quà tặng phù hợp.
    </p>

    <p>
        Yêu thích:
        <strong data-so-luong-yeu-thich>0</strong>
    </p>
</header>

<nav aria-label="Điều hướng chính">

    <button
        class="nut-menu"
        type="button"
        aria-expanded="false"
        aria-controls="menu-chinh">
        Menu
    </button>

    <ul class="menu-list" id="menu-chinh">

        <li>
            <a href="index.php">Trang chủ</a>
        </li>

        <li>
            <a href="danh-sach.php">Danh sách</a>
        </li>

        <li>
            <a href="chi-tiet.php">Chi tiết</a>
        </li>

        <li>
            <a href="gioi-thieu.php">Về chúng tôi</a>
        </li>

        <li>
            <a href="lien-he.php">Liên hệ</a>
        </li>

    </ul>

</nav>

<main>

    <section class="form-lien-he">

        <h2>Đăng ký tư vấn</h2>

        <p
            id="thong-bao-lien-he"
            aria-live="polite">
        </p>

        <form
            id="form-lien-he"
            action="https://jsonplaceholder.typicode.com/posts"
            method="post"
            novalidate>

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

                        <option value="ban-be">
                            Bạn bè
                        </option>

                        <option value="nguoi-yeu">
                            Người yêu
                        </option>

                        <option value="gia-dinh">
                            Gia đình
                        </option>

                        <option value="dong-nghiep">
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

                        <option value="sinh-nhat">
                            Sinh nhật
                        </option>

                        <option value="ky-niem">
                            Kỷ niệm
                        </option>

                        <option value="le">
                            Ngày lễ
                        </option>

                        <option value="khac">
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
                        min="50000"
                        max="10000000"
                        step="50000"
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
                        required></textarea>

                    <small
                        class="loi-truong"
                        id="loi-so-thich"
                        aria-live="polite">
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

<footer>

    <p>
        &copy; 2026 Quà Tặng Thông Minh.
        Website đồ án môn Lập trình Web.
    </p>

</footer>

<script
    type="module"
    src="js/main.js">
</script>

<script
    type="module"
    src="js/trang-lien-he.js">
</script>

<script
    type="module"
    src="js/yeu-thich.js">
</script>
</body>
</html>
