SET NAMES utf8mb4;
USE ltweb_doan_nhom15;

-- =======================================================
-- PHẦN A: CÁC BẢNG DÙNG CHUNG CỦA ĐỒ ÁN (BẠN PHỤ TRÁCH)
-- =======================================================

-- 1. Bảng danh mục sản phẩm
CREATE TABLE danh_muc (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ten VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Bảng người dùng (Đăng ký, đăng nhập, phân quyền)
CREATE TABLE nguoi_dung (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(150) NOT NULL UNIQUE,
    ho_ten VARCHAR(100) NOT NULL,
    mat_khau VARCHAR(255) NOT NULL,
    vai_tro ENUM('khach_hang', 'quan_tri') NOT NULL DEFAULT 'khach_hang',
    ngay_tao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Bảng sản phẩm (Thực thể chính)
CREATE TABLE san_pham (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    danh_muc_id INT UNSIGNED NOT NULL,
    ten VARCHAR(200) NOT NULL,
    gia INT UNSIGNED NOT NULL CHECK (gia >= 1000),
    ton_kho INT UNSIGNED NOT NULL DEFAULT 0,
    anh VARCHAR(255) NULL,
    mo_ta TEXT NULL,
    ngay_tao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (danh_muc_id) REFERENCES danh_muc(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Bảng đơn hàng
CREATE TABLE don_hang (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nguoi_dung_id INT UNSIGNED NOT NULL,
    tong_tien INT UNSIGNED NOT NULL,
    trang_thai VARCHAR(50) NOT NULL DEFAULT 'moi',
    ngay_tao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (nguoi_dung_id) REFERENCES nguoi_dung(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Bảng chi tiết đơn hàng (Quan hệ n - n)
CREATE TABLE chi_tiet_don (
    don_hang_id INT UNSIGNED NOT NULL,
    san_pham_id INT UNSIGNED NOT NULL,
    so_luong SMALLINT UNSIGNED NOT NULL,
    don_gia INT UNSIGNED NOT NULL,
    PRIMARY KEY (don_hang_id, san_pham_id),
    FOREIGN KEY (don_hang_id) REFERENCES don_hang(id) ON DELETE CASCADE,
    FOREIGN KEY (san_pham_id) REFERENCES san_pham(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Bảng liên hệ
CREATE TABLE lien_he (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ten VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    noi_dung TEXT NOT NULL,
    ngay_tao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =======================================================
-- PHẦN B: CÁC BẢNG TRANG CÁ NHÂN CỦA CÁC THÀNH VIÊN
-- =======================================================

-- Bảng chung thành viên (kết nối với tài khoản nguoi_dung nếu cần)
CREATE TABLE IF NOT EXISTS thanh_vien (
    mssv VARCHAR(20) PRIMARY KEY,
    ho_ten VARCHAR(100) NOT NULL,
    nguoi_dung_id INT UNSIGNED NULL UNIQUE,
    FOREIGN KEY (nguoi_dung_id) REFERENCES nguoi_dung(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 1. Chức năng cá nhân Nguyễn Bùi Anh Quốc
CREATE TABLE IF NOT EXISTS tv_muc_tieu (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    mssv VARCHAR(20) NOT NULL,
    ten_muc_tieu VARCHAR(200) NOT NULL,
    mo_ta TEXT NULL,
    ngay_tao TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_tv_muc_tieu_thanh_vien
        FOREIGN KEY (mssv) REFERENCES thanh_vien(mssv)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Kỹ năng của Lê Minh Quân
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Bộ đếm lượt xem của Nguyễn Phạm Tiến Dũng
CREATE TABLE IF NOT EXISTS tv_luot_xem (
    mssv VARCHAR(20) PRIMARY KEY,
    luot_xem BIGINT UNSIGNED NOT NULL DEFAULT 0,
    CONSTRAINT fk_tv_luot_xem_thanh_vien
        FOREIGN KEY (mssv) REFERENCES thanh_vien(mssv)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3.1. Thống kê lượt xem theo ngày của Nguyễn Phạm Tiến Dũng
CREATE TABLE IF NOT EXISTS tv_luot_xem_ngay (
    mssv VARCHAR(20) NOT NULL,
    ngay DATE NOT NULL,
    luot_xem INT UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (mssv, ngay),
    CONSTRAINT fk_tv_luot_xem_ngay_thanh_vien
        FOREIGN KEY (mssv) REFERENCES thanh_vien(mssv)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- 4. Sổ lưu bút của Nguyễn Phạm Tiến Dũng
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
-- 5. Nhật ký học tập của Lê Minh Quân
CREATE TABLE IF NOT EXISTS tv_nhat_ky (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    mssv VARCHAR(20) NOT NULL,
    tieu_de VARCHAR(200) NOT NULL,
    noi_dung TEXT NOT NULL,
    ngay_tao TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_tv_nhat_ky_thanh_vien
        FOREIGN KEY (mssv) REFERENCES thanh_vien(mssv)
        ON DELETE CASCADE,
    INDEX idx_tv_nhat_ky_mssv_id (mssv, id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Danh sách công việc của Lê Minh Quân
CREATE TABLE IF NOT EXISTS tv_cong_viec (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    mssv VARCHAR(20) NOT NULL,
    ten_cong_viec VARCHAR(200) NOT NULL,
    hoan_thanh TINYINT(1) NOT NULL DEFAULT 0,
    ngay_tao TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_tv_cong_viec_thanh_vien
        FOREIGN KEY (mssv) REFERENCES thanh_vien(mssv)
        ON DELETE CASCADE,
    INDEX idx_tv_cong_viec_mssv_id (mssv, id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
