
-- Dữ liệu mẫu cho trang cá nhân Nguyễn Bùi Anh Quốc.
-- Dùng để kiểm tra hai chức năng MySQL.
-- MSSV: 3120224120.

INSERT INTO thanh_vien (mssv, ho_ten)
VALUES ('3120224120', 'Nguyễn Bùi Anh Quốc')
ON DUPLICATE KEY UPDATE
    ho_ten = VALUES(ho_ten);

INSERT INTO tv_muc_tieu (mssv, ten_muc_tieu, mo_ta)
SELECT '3120224120', 'Hoàn thành đồ án PHP',
       'Hoàn thiện chức năng và kiểm thử website'
WHERE NOT EXISTS (
    SELECT 1
    FROM tv_muc_tieu
    WHERE mssv = '3120224120'
      AND ten_muc_tieu = 'Hoàn thành đồ án PHP'
);

INSERT INTO tv_du_an (mssv, ten_du_an, mo_ta, cong_nghe)
SELECT '3120224120', 'Website Đồ Án Nhóm 15',
       'Website quà tặng của nhóm',
       'PHP, HTML, CSS, JavaScript, MySQL'
WHERE NOT EXISTS (
    SELECT 1
    FROM tv_du_an
    WHERE mssv = '3120224120'
      AND ten_du_an = 'Website Đồ Án Nhóm 15'
);
