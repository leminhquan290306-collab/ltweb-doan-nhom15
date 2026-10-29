-- Dữ liệu mẫu cho các trang cá nhân của nhóm 15.
-- Gồm dữ liệu mục tiêu, dự án, kỹ năng, lượt xem và sổ lưu bút.
-- Chạy sau database/schema.sql trong database ltweb_doan_nhom15.

-- 1. Thành viên
INSERT INTO thanh_vien (mssv, ho_ten)
VALUES
    ('3120224120', 'Nguyễn Bùi Anh Quốc'),
    ('3120224116', 'Lê Minh Quân'),
    ('3120224032', 'Nguyễn Phạm Tiến Dũng')
ON DUPLICATE KEY UPDATE
    ho_ten = VALUES(ho_ten);

-- 2. Dữ liệu mục tiêu của Quốc (giữ nguyên chức năng hiện có)
INSERT INTO tv_muc_tieu (mssv, ten_muc_tieu, mo_ta)
SELECT '3120224120', 'Hoàn thành đồ án PHP',
       'Hoàn thiện chức năng và kiểm thử website'
WHERE NOT EXISTS (
    SELECT 1 FROM tv_muc_tieu
    WHERE mssv = '3120224120'
      AND ten_muc_tieu = 'Hoàn thành đồ án PHP'
);

-- 3. Dự án của Quốc
INSERT INTO tv_du_an (mssv, ten_du_an, mo_ta, cong_nghe)
SELECT '3120224120', 'Website Đồ Án Nhóm 15',
       'Website quà tặng của nhóm',
       'PHP, HTML, CSS, JavaScript, MySQL'
WHERE NOT EXISTS (
    SELECT 1 FROM tv_du_an
    WHERE mssv = '3120224120'
      AND ten_du_an = 'Website Đồ Án Nhóm 15'
);

-- 4. Kỹ năng của Quân
INSERT INTO tv_ky_nang (mssv, ten_ky_nang, nhom, mo_ta)
SELECT '3120224116', 'Lập trình Web', 'web',
       'HTML5, CSS3, PHP 8 và JavaScript.'
WHERE NOT EXISTS (
    SELECT 1 FROM tv_ky_nang
    WHERE mssv = '3120224116' AND ten_ky_nang = 'Lập trình Web'
);

INSERT INTO tv_ky_nang (mssv, ten_ky_nang, nhom, mo_ta)
SELECT '3120224116', 'Cơ sở dữ liệu', 'laptrinh',
       'Thực hành làm việc với MySQL và PostgreSQL.'
WHERE NOT EXISTS (
    SELECT 1 FROM tv_ky_nang
    WHERE mssv = '3120224116' AND ten_ky_nang = 'Cơ sở dữ liệu'
);

INSERT INTO tv_ky_nang (mssv, ten_ky_nang, nhom, mo_ta)
SELECT '3120224116', 'Lập trình cơ bản', 'laptrinh',
       'Sử dụng Python và C++ để giải quyết các bài toán.'
WHERE NOT EXISTS (
    SELECT 1 FROM tv_ky_nang
    WHERE mssv = '3120224116' AND ten_ky_nang = 'Lập trình cơ bản'
);

INSERT INTO tv_ky_nang (mssv, ten_ky_nang, nhom, mo_ta)
SELECT '3120224116', 'Công cụ phát triển', 'congcu',
       'Git, GitHub và Visual Studio Code.'
WHERE NOT EXISTS (
    SELECT 1 FROM tv_ky_nang
    WHERE mssv = '3120224116' AND ten_ky_nang = 'Công cụ phát triển'
);

-- 5. Dự án của Quân
INSERT INTO tv_du_an (mssv, ten_du_an, mo_ta, cong_nghe, nhom)
SELECT '3120224116', 'Website Quà Tặng Thông Minh',
       'Xây dựng website giới thiệu và hiển thị danh sách sản phẩm quà tặng.',
       'PHP, HTML, CSS, JavaScript', 'web'
WHERE NOT EXISTS (
    SELECT 1 FROM tv_du_an
    WHERE mssv = '3120224116'
      AND ten_du_an = 'Website Quà Tặng Thông Minh'
);

INSERT INTO tv_du_an (mssv, ten_du_an, mo_ta, cong_nghe, nhom)
SELECT '3120224116', 'Trang giới thiệu cá nhân',
       'Thiết kế trang cá nhân bằng HTML, CSS và PHP.',
       'HTML, CSS, PHP', 'web'
WHERE NOT EXISTS (
    SELECT 1 FROM tv_du_an
    WHERE mssv = '3120224116'
      AND ten_du_an = 'Trang giới thiệu cá nhân'
);

INSERT INTO tv_du_an (mssv, ten_du_an, mo_ta, cong_nghe, nhom)
SELECT '3120224116', 'Bài tập lập trình mạng',
       'Thực hành giao tiếp giữa client và server bằng socket.',
       'Python, Socket', 'laptrinh'
WHERE NOT EXISTS (
    SELECT 1 FROM tv_du_an
    WHERE mssv = '3120224116'
      AND ten_du_an = 'Bài tập lập trình mạng'
);

-- 6. Khởi tạo bộ đếm lượt xem của Dũng.
-- Không ghi đè số lượt xem đã có.
INSERT INTO tv_luot_xem (mssv, luot_xem)
VALUES ('3120224032', 0)
ON DUPLICATE KEY UPDATE
    mssv = VALUES(mssv);

-- 6.1. Dữ liệu mẫu thống kê lượt xem theo ngày của Dũng.
-- Chỉ thêm ngày chưa có để không ghi đè số lượt xem hiện tại.
INSERT INTO tv_luot_xem_ngay (mssv, ngay, luot_xem)
SELECT '3120224032', DATE_SUB(CURDATE(), INTERVAL 6 DAY), 3
WHERE NOT EXISTS (
    SELECT 1
    FROM tv_luot_xem_ngay
    WHERE mssv = '3120224032'
      AND ngay = DATE_SUB(CURDATE(), INTERVAL 6 DAY)
);

INSERT INTO tv_luot_xem_ngay (mssv, ngay, luot_xem)
SELECT '3120224032', DATE_SUB(CURDATE(), INTERVAL 3 DAY), 5
WHERE NOT EXISTS (
    SELECT 1
    FROM tv_luot_xem_ngay
    WHERE mssv = '3120224032'
      AND ngay = DATE_SUB(CURDATE(), INTERVAL 3 DAY)
);

INSERT INTO tv_luot_xem_ngay (mssv, ngay, luot_xem)
SELECT '3120224032', CURDATE(), 8
WHERE NOT EXISTS (
    SELECT 1
    FROM tv_luot_xem_ngay
    WHERE mssv = '3120224032'
      AND ngay = CURDATE()
);

-- 7. Lời nhắn mẫu cho sổ lưu bút của Dũng.
-- Chỉ thêm một lần để tránh trùng dữ liệu khi chạy seed nhiều lần.
INSERT INTO tv_luu_but (mssv, ten, noi_dung)
SELECT
    '3120224032',
    'Người dùng thử',
    'Kiểm tra chức năng sổ lưu bút'
WHERE NOT EXISTS (
    SELECT 1
    FROM tv_luu_but
    WHERE mssv = '3120224032'
      AND ten = 'Người dùng thử'
      AND noi_dung = 'Kiểm tra chức năng sổ lưu bút'
);
