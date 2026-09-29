/*
 * yeu-thich.js
 * Quản lý danh sách sản phẩm yêu thích bằng localStorage.
 */

const TEN_KHOA_LUU_TRU = 'sanPhamYeuThich';

function layDanhSachYeuThich() {
    try {
        const duLieu =
            localStorage.getItem(TEN_KHOA_LUU_TRU);

        if (!duLieu) {
            return [];
        }

        const danhSach = JSON.parse(duLieu);

        return Array.isArray(danhSach)
            ? danhSach
            : [];
    } catch (loi) {
        console.error(loi);
        return [];
    }
}

function luuDanhSachYeuThich(danhSach) {
    localStorage.setItem(
        TEN_KHOA_LUU_TRU,
        JSON.stringify(danhSach)
    );
}

function themYeuThich(id) {
    const danhSach = layDanhSachYeuThich();

    if (!danhSach.includes(id)) {
        danhSach.push(id);
        luuDanhSachYeuThich(danhSach);
    }

    capNhatSoLuongYeuThich();
}

function xoaYeuThich(id) {
    const danhSach =
        layDanhSachYeuThich().filter(
            (sanPhamId) => sanPhamId !== id
        );

    luuDanhSachYeuThich(danhSach);

    capNhatSoLuongYeuThich();
}

function laYeuThich(id) {
    return layDanhSachYeuThich().includes(id);
}

function capNhatSoLuongYeuThich() {
    const cacBoDem =
        document.querySelectorAll(
            '[data-so-luong-yeu-thich]'
        );

    const soLuong =
        layDanhSachYeuThich().length;

    cacBoDem.forEach((boDem) => {
        boDem.textContent = String(soLuong);
    });
}

capNhatSoLuongYeuThich();

export {
    layDanhSachYeuThich,
    themYeuThich,
    xoaYeuThich,
    laYeuThich,
    capNhatSoLuongYeuThich
};