
-- Tao tai khoan rieng cho website.
-- Thay MAT_KHAU_MAU khi khoi tao tren may chu.

CREATE USER IF NOT EXISTS 'ltweb_app'@'127.0.0.1'
IDENTIFIED BY 'THAY_MAT_KHAU_MAU_KHI_CAI_DAT';

GRANT SELECT, INSERT, UPDATE, DELETE
ON ltweb_doan_nhom15.*
TO 'ltweb_app'@'127.0.0.1';

FLUSH PRIVILEGES;
