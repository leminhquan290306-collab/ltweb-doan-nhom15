document.addEventListener('DOMContentLoaded', function () {
    const inputTimKiem = document.querySelector('#tim-kiem'); // Ô input tìm kiếm ở trang danh sách
    const khuVucSanPham = document.querySelector('#danh-sach-dong'); // Vùng chứa danh sách sản phẩm

    if (!inputTimKiem || !khuVucSanPham) return;

    let timer = null;

    inputTimKiem.addEventListener('input', function () {
        clearTimeout(timer);
        const tuKhoa = inputTimKiem.value.trim();

        // Sử dụng debounce để gọi API mượt mà
        timer = setTimeout(() => {
            fetch(`api/san-pham.php?q=${encodeURIComponent(tuKhoa)}`)
                .then(res => res.json())
                .then(data => {
                    if (!data.thanh_cong) return;

                    if (data.du_lieu.length === 0) {
                        khuVucSanPham.innerHTML = '<p>Không tìm thấy sản phẩm nào phù hợp.</p>';
                        return;
                    }

                    let html = '';
                    data.du_lieu.forEach(sp => {
                        html += `
                            <article class="card">
                                <img src="${sp.anh}" alt="${sp.ten}">
                                <h3>${sp.ten}</h3>
                                <p>Danh mục: ${sp.danh_muc}</p>
                                <p>Giá: ${Number(sp.gia).toLocaleString('vi-VN')} VNĐ</p>
                                <a href="chi-tiet.php?id=${sp.id}">Xem chi tiết</a>
                            </article>
                        `;
                    });
                    khuVucSanPham.innerHTML = html;
                })
                .catch(err => console.error('Lỗi tìm kiếm:', err));
        }, 300);
    });
});