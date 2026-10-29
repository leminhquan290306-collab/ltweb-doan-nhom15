document.addEventListener('DOMContentLoaded', function () {
    document.body.addEventListener('submit', function (e) {
        const form = e.target;
        const inputHanhDong = form.querySelector('input[name="hanh-dong"]');
        
        // Kiểm tra nếu là form thêm vào giỏ hàng
        if (inputHanhDong && inputHanhDong.value === 'them') {
            e.preventDefault(); // Chặn việc load lại trang

            const formData = new FormData(form);

            fetch('api/gio-hang.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.thanh_cong) {
                    // Cập nhật ngay số lượng giỏ hàng trên header (tìm thẻ có id hoặc class chứa số lượng giỏ hàng)
                    const badgeGioHang = document.querySelector('#so-luong-gio-hang, .gio-hang-badge, [data-so-luong-gio-hang]');
                    if (badgeGioHang) {
                        badgeGioHang.textContent = data.tong_so_luong;
                    }

                    alert(data.thanh_bao);
                } else {
                    alert('Lỗi: ' + (data.loi || 'Không thể thêm vào giỏ hàng'));
                }
            })
            .catch(err => {
                console.error('Lỗi AJAX giỏ hàng:', err);
                form.submit(); // Fallback về submit truyền thống nếu lỗi mạng
            });
        }
    });
});