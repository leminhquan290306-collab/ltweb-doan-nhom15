<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Quà Tặng Thông Minh - Tư vấn và mua sắm quà tặng</title>

    <meta name="description"
          content="Website tư vấn và mua sắm quà tặng thông minh, giúp người dùng tìm món quà phù hợp với người nhận, dịp tặng và ngân sách.">

    <link rel="icon"
          type="image/jpeg"
          href="images/hop-qua.jpg">

    <link rel="stylesheet" href="css/01-bien.css">
    <link rel="stylesheet" href="css/02-chuan-hoa.css">
    <link rel="stylesheet" href="css/03-bo-cuc.css">
    <link rel="stylesheet" href="css/04-thanh-phan.css">
    <link rel="stylesheet" href="css/05-tien-ich.css">
</head>

<body class="trang">

    <header>
        <h1>Quà Tặng Thông Minh</h1>

        <p>
            Gợi ý món quà phù hợp cho những người bạn yêu thương
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

        <section class="hero">

            <h2>Tìm món quà phù hợp</h2>

            <p>
                Bạn đang phân vân không biết nên tặng gì cho người thân,
                bạn bè hoặc đồng nghiệp? Website giúp bạn tìm kiếm và
                lựa chọn món quà dựa trên người nhận, dịp tặng và ngân sách.
            </p>

            <p>
                Thay vì mất nhiều thời gian tìm kiếm, bạn có thể sử dụng
                công cụ tư vấn để nhận được những gợi ý phù hợp.
            </p>

        </section>

        <section class="card">

            <h2 class="card__title">
                Những khó khăn khi chọn quà
            </h2>

            <ul>
                <li>
                    Không biết món quà nào phù hợp với người nhận.
                </li>

                <li>
                    Khó lựa chọn quà theo sở thích cá nhân.
                </li>

                <li>
                    Mất nhiều thời gian tìm kiếm sản phẩm.
                </li>

                <li>
                    Khó kiểm soát món quà trong phạm vi ngân sách.
                </li>
            </ul>

        </section>

        <section class="card">

            <h2 class="card__title">
                Quy trình lựa chọn quà
            </h2>

            <ol>
                <li>
                    Nhập thông tin về người nhận và dịp tặng.
                </li>

                <li>
                    Nhận các gợi ý quà phù hợp.
                </li>

                <li>
                    Xem sản phẩm và lựa chọn món quà.
                </li>
            </ol>

        </section>

        <section class="card">

            <h2 class="card__title">
                Các nhóm quà phổ biến
            </h2>

            <ul>
                <li>Quà sinh nhật</li>
                <li>Quà kỷ niệm</li>
                <li>Quà dành cho gia đình</li>
                <li>Quà dành cho bạn bè</li>
                <li>Quà dành cho đồng nghiệp</li>
            </ul>

        </section>

        <section class="card">

            <h2 class="card__title">
                Khám phá website
            </h2>

            <figure>

                <img
                    src="images/hop-qua.jpg"
                    alt="Hộp quà được chuẩn bị để tặng"
                    width="400"
                    height="300"
                    class="anh-responsive"
                    loading="lazy">

                <figcaption>
                    Gợi ý quà tặng được lựa chọn theo nhu cầu của người dùng.
                </figcaption>

            </figure>

            <p>
                <a
                    class="btn btn--chinh"
                    href="lien-he.php">
                    Bắt đầu liên hệ
                </a>
            </p>

        </section>

        <section
            class="card"
            id="thoi-tiet">

            <h2 class="card__title">
                Thời tiết Đà Nẵng
            </h2>

            <p
                id="thong-bao-thoi-tiet"
                aria-live="polite">
                Đang tải dữ liệu thời tiết...
            </p>

            <div id="du-lieu-thoi-tiet">

                <p>
                    Nhiệt độ:
                    <strong id="nhiet-do">--</strong>
                </p>

                <p>
                    Độ ẩm:
                    <strong id="do-am">--</strong>
                </p>

                <p>
                    Trạng thái:
                    <strong id="trang-thai-thoi-tiet">--</strong>
                </p>

            </div>

            <h3>Public REST API sử dụng</h3>

            <p>
                API:
                <code id="url-api-thoi-tiet">
                    https://api.open-meteo.com/v1/forecast
                </code>
            </p>

            <p>
                Phản hồi JSON rút gọn:
            </p>

            <pre
                id="json-thoi-tiet"
                aria-live="polite">Đang tải...</pre>

        </section>

        <noscript>
            <p>
                JavaScript đang bị tắt. Nội dung giới thiệu website
                vẫn có thể được xem, nhưng dữ liệu thời tiết trực tuyến
                không được cập nhật.
            </p>
        </noscript>

    </main>

    <footer>

        <p>
            &copy; 2026 Quà Tặng Thông Minh. Nhóm 15.
        </p>

    </footer>

    <script
        type="module"
        src="js/main.js">
    </script>

    <script
        type="module"
        src="js/trang-chu.js">
    </script>

    <script
        type="module"
        src="js/yeu-thich.js">
    </script>

</body>
</html>