# Quà Tặng Thông Minh

## 1. Yêu cầu môi trường
- PHP 8.1 trở lên
- MySQL
- Composer
- Apache

## 2. Cài đặt
1. Clone repository.
2. Chạy `composer install`.
3. Sao chép `.env.example` thành `.env`.
4. Cập nhật thông tin kết nối cơ sở dữ liệu trong `.env`.

## 3. Phân quyền cơ sở dữ liệu

Ứng dụng sử dụng tài khoản riêng `ltweb_app`, không sử dụng tài khoản quản trị MySQL `root`.

Chỉ cấp các quyền cần thiết cho cơ sở dữ liệu:

    GRANT SELECT, INSERT, UPDATE, DELETE
    ON ltweb_doan_nhom15.*
    TO 'ltweb_app'@'127.0.0.1';

Tài khoản ứng dụng không được cấp các quyền quản trị hoặc thay đổi cấu trúc cơ sở dữ liệu như CREATE, DROP, ALTER, GRANT.

Không đưa file `.env` chứa thông tin đăng nhập thật lên GitHub.

## 4. Khởi chạy
Cấu hình Apache trỏ đến thư mục dự án và truy cập website qua localhost.
