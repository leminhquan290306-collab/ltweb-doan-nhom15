<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Chi tiết sản phẩm - Quà Tặng Thông Minh</title>

    <meta name="description"
          content="Thông tin chi tiết về sản phẩm quà tặng, gồm hình ảnh, đặc điểm, thông số và gợi ý sử dụng.">

    <link rel="stylesheet" href="css/01-bien.css">
    <link rel="stylesheet" href="css/02-chuan-hoa.css">
    <link rel="stylesheet" href="css/03-bo-cuc.css">
    <link rel="stylesheet" href="css/04-thanh-phan.css">
    <link rel="stylesheet" href="css/05-tien-ich.css">
</head>

<body class="trang">

    <header>
        <h1>Chi tiết sản phẩm quà tặng</h1>

        <p>
            Thông tin chi tiết về sản phẩm được chọn.
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
                <a href="index.php">
                    Trang chủ
                </a>
            </li>

            <li>
                <a href="danh-sach.php">
                    Danh sách
                </a>
            </li>

            <li>
                <a href="chi-tiet.php">
                    Chi tiết
                </a>
            </li>

            <li>
                <a href="gioi-thieu.php">
                    Về chúng tôi
                </a>
            </li>

            <li>
                <a href="lien-he.php">
                    Liên hệ
                </a>
            </li>

        </ul>

    </nav>

    <main class="noi-dung-chi-tiet">

        <article class="chi-tiet-chinh">

            <p id="thong-bao-chi-tiet" aria-live="polite">
                Đang tải thông tin sản phẩm...
            </p>

            <div id="noi-dung-san-pham">

                <h2 id="ten-san-pham">
                    Sản phẩm
                </h2>

                <button
                    type="button"
                    id="nut-yeu-thich-chi-tiet"
                    data-hanh-dong-yeu-thich="them"
                    data-id="">
                    Thêm yêu thích
                </button>

                <figure>

                    <img
                        id="hinh-san-pham"
                        src="images/hop-qua.jpg"
                        alt="Hình ảnh sản phẩm"
                        width="400"
                        height="300"
                        class="anh-responsive">

                    <figcaption id="mo-ta-hinh">
                        Hình ảnh sản phẩm quà tặng.
                    </figcaption>

                </figure>

                <section>

                    <h3>
                        Giới thiệu sản phẩm
                    </h3>

                    <p id="mo-ta-san-pham">
                        Đang tải mô tả sản phẩm...
                    </p>

                </section>

                <section>

                    <h3>
                        Thông số sản phẩm
                    </h3>

                    <table>

                        <caption>
                            Thông tin chi tiết sản phẩm
                        </caption>

                        <thead>
                            <tr>
                                <th scope="col">
                                    Thông tin
                                </th>

                                <th scope="col">
                                    Chi tiết
                                </th>
                            </tr>
                        </thead>

                        <tbody>

                            <tr>
                                <th scope="row">
                                    Tên sản phẩm
                                </th>

                                <td id="thong-so-ten"></td>
                            </tr>

                            <tr>
                                <th scope="row">
                                    Danh mục
                                </th>

                                <td id="thong-so-danh-muc"></td>
                            </tr>

                            <tr>
                                <th scope="row">
                                    Giá tham khảo
                                </th>

                                <td id="thong-so-gia"></td>
                            </tr>

                            <tr>
                                <th scope="row">
                                    Số lượng
                                </th>

                                <td id="thong-so-so-luong"></td>
                            </tr>

                        </tbody>

                    </table>

                </section>

                <section>

                    <h3>
                        Gợi ý sử dụng
                    </h3>

                    <ul>
                        <li>
                            Phù hợp làm quà tặng trong nhiều dịp.
                        </li>

                        <li>
                            Có thể tặng bạn bè hoặc người thân.
                        </li>

                        <li>
                            Có thể kết hợp với thiệp chúc mừng.
                        </li>
                    </ul>

                </section>

            </div>

        </article>

        <section class="card chi-tiet-phu">

            <h2 class="card__title">
                Khám phá thêm
            </h2>

            <p>
                <a
                    class="btn btn--chinh"
                    href="danh-sach.php">
                    Quay lại danh sách sản phẩm
                </a>
            </p>

            <p>
                <a
                    class="btn btn--phu"
                    href="lien-he.php">
                    Liên hệ để được tư vấn quà tặng
                </a>
            </p>

        </section>

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
        src="js/trang-chi-tiet.js">
    </script>

    <script
        type="module"
        src="js/yeu-thich.js">
    </script>

</body>
</html>