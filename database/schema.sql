
-- Cấu trúc dữ liệu cá nhân Nguyễn Bùi Anh Quốc.
-- Chức năng: lưu mục tiêu học tập và danh sách dự án.
-- Chạy trong database ltweb_doan_nhom15.
-- MSSV: 3120224120.

CREATE TABLE IF NOT EXISTS thanh_vien (
    mssv VARCHAR(20) PRIMARY KEY,
    ho_ten VARCHAR(100) NOT NULL
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS tv_muc_tieu (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    mssv VARCHAR(20) NOT NULL,
    ten_muc_tieu VARCHAR(200) NOT NULL,
    mo_ta TEXT NULL,
    ngay_tao TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_tv_muc_tieu_thanh_vien
        FOREIGN KEY (mssv) REFERENCES thanh_vien(mssv)
        ON DELETE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS tv_du_an (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    mssv VARCHAR(20) NOT NULL,
    ten_du_an VARCHAR(200) NOT NULL,
    mo_ta TEXT NULL,
    cong_nghe VARCHAR(200) NULL,
    ngay_tao TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_tv_du_an_thanh_vien
        FOREIGN KEY (mssv) REFERENCES thanh_vien(mssv)
        ON DELETE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;
