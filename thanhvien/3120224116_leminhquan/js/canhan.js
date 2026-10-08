/*

* canhan.js
* 1. Chuyển đổi giao diện sáng / tối.
* 2. Thu gọn / mở rộng nội dung.
* 3. Hiển thị nút lên đầu trang khi cuộn.
     */

document.addEventListener('DOMContentLoaded', () => {
// 1. Chuyển đổi giao diện sáng / tối
const nutDoiGiaoDien =
document.getElementById('nut-doi-giao-dien');


if (nutDoiGiaoDien) {
    const cheDoDaLuu =
        localStorage.getItem('cheDoGiaoDien');

    const dangToi = cheDoDaLuu === 'toi';

    document.body.classList.toggle(
        'giao-dien-toi',
        dangToi
    );

    function capNhatNut(dangToi) {
        nutDoiGiaoDien.textContent = dangToi
            ? '☀️ Chuyển sang giao diện sáng'
            : '🌙 Chuyển sang giao diện tối';

        nutDoiGiaoDien.setAttribute(
            'aria-pressed',
            String(dangToi)
        );
    }

    capNhatNut(dangToi);

    nutDoiGiaoDien.addEventListener('click', () => {
        const dangToiHienTai =
            document.body.classList.toggle(
                'giao-dien-toi'
            );

        localStorage.setItem(
            'cheDoGiaoDien',
            dangToiHienTai ? 'toi' : 'sang'
        );

        capNhatNut(dangToiHienTai);
    });
}

// 2. Thu gọn / mở rộng nội dung từng mục
const cacMucNoiDung =
    document.querySelectorAll('.muc-noi-dung');

cacMucNoiDung.forEach((muc, chiSo) => {
    const tieuDe =
        muc.querySelector(':scope > h2, :scope > h3');

    if (!tieuDe) return;

    const nutThuGon = document.createElement('button');
    nutThuGon.type = 'button';
    nutThuGon.className = 'nut-accordion';
    nutThuGon.textContent = 'Thu gọn';
    nutThuGon.setAttribute('aria-expanded', 'true');

    const khungNoiDung = document.createElement('div');
    khungNoiDung.className = 'khung-noi-dung-accordion';
    khungNoiDung.id = `noi-dung-${chiSo + 1}`;

    nutThuGon.setAttribute(
        'aria-controls',
        khungNoiDung.id
    );

    Array.from(muc.children).forEach((phanTu) => {
        if (phanTu !== tieuDe) {
            khungNoiDung.appendChild(phanTu);
        }
    });

    muc.appendChild(nutThuGon);
    muc.appendChild(khungNoiDung);

    nutThuGon.addEventListener('click', () => {
        const dangMo =
            nutThuGon.getAttribute('aria-expanded') === 'true';

        nutThuGon.setAttribute(
            'aria-expanded',
            String(!dangMo)
        );

        khungNoiDung.hidden = dangMo;
        nutThuGon.textContent = dangMo
            ? 'Mở rộng'
            : 'Thu gọn';
    });
});

// 3. Nút lên đầu trang
const nutLenDau = document.createElement('button');
nutLenDau.type = 'button';
nutLenDau.id = 'nut-len-dau';
nutLenDau.textContent = '↑ Lên đầu trang';
nutLenDau.setAttribute('aria-label', 'Lên đầu trang');
nutLenDau.hidden = true;

document.body.appendChild(nutLenDau);

function capNhatNutLenDau() {
    nutLenDau.hidden = window.scrollY < 300;
}

window.addEventListener('scroll', capNhatNutLenDau);

nutLenDau.addEventListener('click', () => {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
});

capNhatNutLenDau();


});
