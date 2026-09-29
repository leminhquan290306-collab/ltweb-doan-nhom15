/*
 * canhan.js
 * Chức năng:
 * 1. Thu gọn / mở rộng nội dung từng mục trên trang cá nhân.
 * 2. Hiển thị nút lên đầu trang khi cuộn và đưa trang về đầu.
 * Cách thử: bấm các nút "Thu gọn", "Mở rộng" và nút "↑ Lên đầu".
 */

document.addEventListener('DOMContentLoaded', () => {
    const cacMucNoiDung =
        document.querySelectorAll('.muc-noi-dung');

    cacMucNoiDung.forEach((muc, chiSo) => {
        const tieuDe =
            muc.querySelector(':scope > h2, :scope > h3');

        if (!tieuDe) {
            return;
        }

        const nutThuGon =
            document.createElement('button');

        nutThuGon.type = 'button';
        nutThuGon.className = 'nut-accordion';
        nutThuGon.textContent = 'Thu gọn';
        nutThuGon.setAttribute(
            'aria-expanded',
            'true'
        );
        nutThuGon.setAttribute(
            'aria-controls',
            `noi-dung-${chiSo + 1}`
        );

        const khungNoiDung =
            document.createElement('div');

        khungNoiDung.className =
            'khung-noi-dung-accordion';

        khungNoiDung.id =
            `noi-dung-${chiSo + 1}`;

        const cacPhanTu =
            Array.from(muc.children);

        cacPhanTu.forEach((phanTu) => {
            if (phanTu !== tieuDe) {
                khungNoiDung.appendChild(phanTu);
            }
        });

        muc.appendChild(nutThuGon);
        muc.appendChild(khungNoiDung);

        nutThuGon.addEventListener(
            'click',
            () => {
                const dangMo =
                    nutThuGon.getAttribute(
                        'aria-expanded'
                    ) === 'true';

                nutThuGon.setAttribute(
                    'aria-expanded',
                    String(!dangMo)
                );

                khungNoiDung.hidden = dangMo;

                nutThuGon.textContent =
                    dangMo
                        ? 'Mở rộng'
                        : 'Thu gọn';
            }
        );
    });

    const nutLenDau =
        document.createElement('button');

    nutLenDau.type = 'button';
    nutLenDau.id = 'nut-len-dau';
    nutLenDau.textContent = '↑ Lên đầu trang';
    nutLenDau.setAttribute(
        'aria-label',
        'Lên đầu trang'
    );
    nutLenDau.hidden = true;

    document.body.appendChild(nutLenDau);

    function capNhatNutLenDau() {
        nutLenDau.hidden =
            window.scrollY < 300;
    }

    window.addEventListener(
        'scroll',
        capNhatNutLenDau
    );

    nutLenDau.addEventListener(
        'click',
        () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }
    );

    capNhatNutLenDau();
});