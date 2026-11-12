
/*
 * canhan.js
 * Chức năng:
 * 1. Chuyển đổi giao diện sáng/tối và lưu lựa chọn.
 * 2. Lọc các kỹ năng theo nhóm.
 * Cách thử: nhấn nút giao diện và các nút lọc kỹ năng.
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Tạo nút chuyển đổi giao diện.
    const nutGiaoDien = document.createElement('button');
    nutGiaoDien.type = 'button';
    nutGiaoDien.textContent = '🌙 Giao diện tối';
    nutGiaoDien.id = 'nut-giao-dien';

    const tieuDe = document.querySelector('h1');

    if (tieuDe) {
        tieuDe.insertAdjacentElement('afterend', nutGiaoDien);
    }

    // 2. Khôi phục giao diện đã lưu.
    const giaoDienDaLuu = localStorage.getItem('giaoDien');

    if (giaoDienDaLuu === 'toi') {
        document.body.classList.add('giao-dien-toi');
        nutGiaoDien.textContent = '☀️ Giao diện sáng';
    }

    // 3. Xử lý chuyển đổi giao diện.
    nutGiaoDien.addEventListener('click', () => {
        document.body.classList.toggle('giao-dien-toi');

        if (document.body.classList.contains('giao-dien-toi')) {
            localStorage.setItem('giaoDien', 'toi');
            nutGiaoDien.textContent = '☀️ Giao diện sáng';
        } else {
            localStorage.setItem('giaoDien', 'sang');
            nutGiaoDien.textContent = '🌙 Giao diện tối';
        }
    });

    // 4. Tạo bộ lọc kỹ năng.
    const khuKyNang = document.querySelector('.muc-noi-dung');

    if (khuKyNang) {
        const cacNutLoc = document.createElement('div');
        cacNutLoc.className = 'bo-loc-ky-nang';

        const danhSach = [
            ['Tất cả', 'tat-ca'],
            ['Web', 'web'],
            ['Lập trình', 'lap-trinh'],
            ['Git', 'git'],
            ['Mạng & CSDL', 'csdl']
        ];

        danhSach.forEach(([ten, loai]) => {
            const nut = document.createElement('button');
            nut.type = 'button';
            nut.textContent = ten;
            nut.dataset.loai = loai;
            cacNutLoc.appendChild(nut);
        });

        khuKyNang.insertBefore(cacNutLoc, khuKyNang.firstChild);

        const cacKyNang = document.querySelectorAll('.the-ky-nang');

        // 5. Xử lý lọc kỹ năng.
        cacNutLoc.addEventListener('click', (event) => {
            if (event.target.tagName !== 'BUTTON') {
                return;
            }

            const loai = event.target.dataset.loai;

            cacKyNang.forEach((kyNang) => {
                const noiDung = kyNang.textContent.toLowerCase();

                if (
                    loai === 'tat-ca' ||
                    (loai === 'web' && noiDung.includes('web')) ||
                    (loai === 'lap-trinh' &&
                        (noiDung.includes('python') ||
                         noiDung.includes('c++'))) ||
                    (loai === 'git' && noiDung.includes('git')) ||
                    (loai === 'csdl' &&
                        (noiDung.includes('mạng') ||
                         noiDung.includes('csdl') ||
                         noiDung.includes('cơ sở dữ liệu')))
                ) {
                    kyNang.hidden = false;
                } else {
                    kyNang.hidden = true;
                }
            });
        });
    }
});
