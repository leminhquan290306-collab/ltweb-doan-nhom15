/*
 * trang-chi-tiet.js
 * Hiển thị thông tin một sản phẩm theo id trên URL
 * và quản lý trạng thái yêu thích của sản phẩm.
 */

import { taiJSON } from './api.js';
import {
    laYeuThich,
    themYeuThich,
    xoaYeuThich
} from './yeu-thich.js';

const thongBao = document.querySelector('#thong-bao-chi-tiet');
const noiDungSanPham = document.querySelector('#noi-dung-san-pham');

const tenSanPham = document.querySelector('#ten-san-pham');
const hinhSanPham = document.querySelector('#hinh-san-pham');
const moTaHinh = document.querySelector('#mo-ta-hinh');
const moTaSanPham = document.querySelector('#mo-ta-san-pham');

const thongSoTen = document.querySelector('#thong-so-ten');
const thongSoDanhMuc = document.querySelector('#thong-so-danh-muc');
const thongSoGia = document.querySelector('#thong-so-gia');
const thongSoSoLuong = document.querySelector('#thong-so-so-luong');

const nutYeuThich = document.querySelector(
    '#nut-yeu-thich-chi-tiet'
);

function hienThiThongBao(noiDung) {
    if (thongBao) {
        thongBao.textContent = noiDung;
    }
}

function capNhatNutYeuThich(id) {
    if (!nutYeuThich) {
        return;
    }

    nutYeuThich.dataset.id = String(id);

    if (laYeuThich(id)) {
        nutYeuThich.dataset.hanhDongYeuThich = 'xoa';
        nutYeuThich.textContent = 'Xóa yêu thích';
    } else {
        nutYeuThich.dataset.hanhDongYeuThich = 'them';
        nutYeuThich.textContent = 'Thêm yêu thích';
    }
}

function hienThiSanPham(sanPham) {
    tenSanPham.textContent = sanPham.ten;

    hinhSanPham.src = sanPham.hinhAnh;
    hinhSanPham.alt = sanPham.ten;

    moTaHinh.textContent = sanPham.moTa;
    moTaSanPham.textContent = sanPham.moTa;

    thongSoTen.textContent = sanPham.ten;
    thongSoDanhMuc.textContent = sanPham.danhMuc;
    thongSoGia.textContent =
        `${sanPham.gia.toLocaleString('vi-VN')} VNĐ`;
    thongSoSoLuong.textContent = String(sanPham.soLuong);

    document.title =
        `${sanPham.ten} - Quà Tặng Thông Minh`;

    capNhatNutYeuThich(sanPham.id);

    hienThiThongBao('');
}

function hienThiKhongTimThay() {
    hienThiThongBao(
        'Không tìm thấy sản phẩm. Vui lòng kiểm tra lại mã sản phẩm.'
    );

    if (noiDungSanPham) {
        noiDungSanPham.hidden = true;
    }

    document.title =
        'Không tìm thấy sản phẩm - Quà Tặng Thông Minh';
}

function xuLyYeuThich() {
    if (!nutYeuThich) {
        return;
    }

    nutYeuThich.addEventListener('click', () => {
        const id = Number(nutYeuThich.dataset.id);

        if (!Number.isInteger(id) || id <= 0) {
            return;
        }

        const hanhDong = nutYeuThich.dataset.hanhDongYeuThich;

        if (hanhDong === 'them') {
            themYeuThich(id);
        }

        if (hanhDong === 'xoa') {
            xoaYeuThich(id);
        }

        capNhatNutYeuThich(id);
    });
}

async function khoiTaoChiTiet() {
    const thamSoURL =
        new URLSearchParams(window.location.search);

    const id = Number(thamSoURL.get('id'));

    if (!Number.isInteger(id) || id <= 0) {
        hienThiKhongTimThay();
        return;
    }

    hienThiThongBao(
        'Đang tải thông tin sản phẩm...'
    );

    try {
        const danhSachSanPham =
            await taiJSON('./data/san-pham.json');

        if (!Array.isArray(danhSachSanPham)) {
            throw new Error(
                'Dữ liệu sản phẩm không hợp lệ.'
            );
        }

        const sanPham = danhSachSanPham.find(
            (item) => item.id === id
        );

        if (!sanPham) {
            hienThiKhongTimThay();
            return;
        }

        hienThiSanPham(sanPham);
    } catch (loi) {
        console.error(loi);

        hienThiThongBao(
            'Không thể tải thông tin sản phẩm. Vui lòng thử lại sau.'
        );

        if (noiDungSanPham) {
            noiDungSanPham.hidden = true;
        }
    }
}

xuLyYeuThich();
khoiTaoChiTiet();