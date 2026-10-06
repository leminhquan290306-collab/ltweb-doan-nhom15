/*
 * trang-danh-sach.js
 * Tải, tìm kiếm, lọc, sắp xếp và quản lý yêu thích sản phẩm.
 */

import { taiJSON } from './api.js';

import {
    laYeuThich,
    themYeuThich,
    xoaYeuThich,
    capNhatSoLuongYeuThich
} from './yeu-thich.js';

const oTimKiem = document.querySelector('#tim-kiem');
const oDanhMuc = document.querySelector('#loc-danh-muc');
const oSapXep = document.querySelector('#sap-xep');

const khuVucSanPham =
    document.querySelector('#danh-sach-dong');

const thongBao =
    document.querySelector('#thong-bao-danh-sach');

let danhSachSanPham = [];

function boDauChuoi(chuoi) {
    return chuoi
        .normalize('NFD')
        .replace(/\p{Diacritic}/gu, '');
}

function hienThiThongBao(noiDung) {
    if (thongBao) {
        thongBao.textContent = noiDung;
    }
}

function taoTheSanPham(sanPham) {
    const the = document.createElement('article');

    the.className = 'card';

    const hinhAnh = document.createElement('img');

    hinhAnh.src = sanPham.hinhAnh;
    hinhAnh.alt = sanPham.ten;

    const tieuDe = document.createElement('h3');

    tieuDe.textContent = sanPham.ten;

    const danhMuc = document.createElement('p');

    danhMuc.textContent =
        `Danh mục: ${sanPham.danhMuc}`;

    const gia = document.createElement('p');

    gia.textContent =
        `Giá: ${sanPham.gia.toLocaleString('vi-VN')} VNĐ`;

    const soLuong = document.createElement('p');

    soLuong.textContent =
        `Số lượng: ${sanPham.soLuong}`;

    const moTa = document.createElement('p');

    moTa.textContent = sanPham.moTa;

    const lienKet = document.createElement('a');

    lienKet.href =
        `chi-tiet.php?id=${sanPham.id}`;

    lienKet.textContent = 'Xem chi tiết';

    const nutYeuThich =
        document.createElement('button');

    nutYeuThich.type = 'button';

    nutYeuThich.dataset.id =
        String(sanPham.id);

    capNhatNutYeuThich(
        nutYeuThich,
        sanPham.id
    );

    the.append(
        hinhAnh,
        tieuDe,
        danhMuc,
        gia,
        soLuong,
        moTa,
        lienKet,
        nutYeuThich
    );

    return the;
}

function capNhatNutYeuThich(nut, id) {
    nut.dataset.id = String(id);

    if (laYeuThich(id)) {
        nut.dataset.hanhDongYeuThich = 'xoa';
        nut.textContent = 'Xóa yêu thích';
    } else {
        nut.dataset.hanhDongYeuThich = 'them';
        nut.textContent = 'Thêm yêu thích';
    }
}

function hienThiSanPham(danhSach) {
    if (!khuVucSanPham) {
        return;
    }

    khuVucSanPham.replaceChildren();

    if (danhSach.length === 0) {
        hienThiThongBao(
            'Không tìm thấy sản phẩm phù hợp.'
        );
        return;
    }

    hienThiThongBao('');

    const danhSachThe =
        document.createDocumentFragment();

    danhSach.forEach((sanPham) => {
        danhSachThe.appendChild(
            taoTheSanPham(sanPham)
        );
    });

    khuVucSanPham.appendChild(danhSachThe);

    capNhatSoLuongYeuThich();
}

function locVaSapXepSanPham() {
    const tuKhoa = boDauChuoi(
        oTimKiem?.value.trim().toLowerCase() ?? ''
    );

    const danhMuc =
        oDanhMuc?.value ?? '';

    const kieuSapXep =
        oSapXep?.value ?? '';

    let ketQua =
        danhSachSanPham.filter((sanPham) => {

            const tenSanPham =
                boDauChuoi(
                    sanPham.ten.toLowerCase()
                );

            const moTaSanPham =
                boDauChuoi(
                    sanPham.moTa.toLowerCase()
                );

            const phuHopTuKhoa =
                tenSanPham.includes(tuKhoa) ||
                moTaSanPham.includes(tuKhoa);

            const phuHopDanhMuc =
                danhMuc === '' ||
                sanPham.danhMuc === danhMuc;

            return (
                phuHopTuKhoa &&
                phuHopDanhMuc
            );
        });

    if (kieuSapXep === 'gia-tang') {
        ketQua.sort(
            (a, b) => a.gia - b.gia
        );
    }

    if (kieuSapXep === 'gia-giam') {
        ketQua.sort(
            (a, b) => b.gia - a.gia
        );
    }

    if (kieuSapXep === 'ten-tang') {
        ketQua.sort(
            (a, b) =>
                a.ten.localeCompare(b.ten, 'vi')
        );
    }

    if (kieuSapXep === 'ten-giam') {
        ketQua.sort(
            (a, b) =>
                b.ten.localeCompare(a.ten, 'vi')
        );
    }

    hienThiSanPham(ketQua);
}

function ganSuKien() {
    oTimKiem?.addEventListener(
        'input',
        locVaSapXepSanPham
    );

    oDanhMuc?.addEventListener(
        'change',
        locVaSapXepSanPham
    );

    oSapXep?.addEventListener(
        'change',
        locVaSapXepSanPham
    );

    khuVucSanPham?.addEventListener(
        'click',
        (suKien) => {

            const nut =
                suKien.target.closest(
                    '[data-hanh-dong-yeu-thich]'
                );

            if (!nut) {
                return;
            }

            const id =
                Number(nut.dataset.id);

            if (!Number.isInteger(id) || id <= 0) {
                return;
            }

            const hanhDong =
                nut.dataset.hanhDongYeuThich;

            if (hanhDong === 'them') {
                themYeuThich(id);
            }

            if (hanhDong === 'xoa') {
                xoaYeuThich(id);
            }

            capNhatNutYeuThich(nut, id);
        }
    );
}

async function khoiTaoDanhSach() {
    hienThiThongBao(
        'Đang tải danh sách sản phẩm...'
    );

    try {
        danhSachSanPham =
            await taiJSON('./data/san-pham.json');

        if (!Array.isArray(danhSachSanPham)) {
            throw new Error(
                'Dữ liệu sản phẩm không hợp lệ.'
            );
        }

        locVaSapXepSanPham();

    } catch (loi) {
        console.error(loi);

        hienThiThongBao(
            'Không thể tải danh sách sản phẩm. Vui lòng thử lại sau.'
        );
    }
}

ganSuKien();
khoiTaoDanhSach();