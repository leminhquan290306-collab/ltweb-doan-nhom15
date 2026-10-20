
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
    nhom VARCHAR(30) NULL,
    ngay_tao TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_tv_du_an_thanh_vien
        FOREIGN KEY (mssv) REFERENCES thanh_vien(mssv)
        ON DELETE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;
-- Chức năng bổ sung cho trang cá nhân của Lê Minh Quân và Nguyễn Phạm Tiến Dũng.

-- Kỹ năng của Lê Minh Quân.
CREATE TABLE IF NOT EXISTS tv_ky_nang (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    mssv VARCHAR(20) NOT NULL,
    ten_ky_nang VARCHAR(200) NOT NULL,
    nhom VARCHAR(30) NOT NULL,
    mo_ta TEXT NULL,
    ngay_tao TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_tv_ky_nang_thanh_vien
        FOREIGN KEY (mssv) REFERENCES thanh_vien(mssv)
        ON DELETE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

-- Bộ đếm lượt xem của Nguyễn Phạm Tiến Dũng.
CREATE TABLE IF NOT EXISTS tv_luot_xem (
    mssv VARCHAR(20) PRIMARY KEY,
    luot_xem BIGINT UNSIGNED NOT NULL DEFAULT 0,
    CONSTRAINT fk_tv_luot_xem_thanh_vien
        FOREIGN KEY (mssv) REFERENCES thanh_vien(mssv)
        ON DELETE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

-- Sổ lưu bút của Nguyễn Phạm Tiến Dũng.
CREATE TABLE IF NOT EXISTS tv_luu_but (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    mssv VARCHAR(20) NOT NULL,
    ten VARCHAR(50) NOT NULL,
    noi_dung VARCHAR(300) NOT NULL,
    ngay_tao TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_tv_luu_but_thanh_vien
        FOREIGN KEY (mssv) REFERENCES thanh_vien(mssv)
        ON DELETE CASCADE,
    INDEX idx_tv_luu_but_mssv_id (mssv, id)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;