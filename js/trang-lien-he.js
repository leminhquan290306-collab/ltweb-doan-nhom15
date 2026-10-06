/*
 * trang-lien-he.js
 * Kiểm tra biểu mẫu liên hệ phía trình duyệt.
 * Dữ liệu hợp lệ sẽ được gửi POST về lien-he.php.
 */

const form = document.querySelector('#form-lien-he');
const thongBao = document.querySelector('#thong-bao-lien-he');
const nutGui = document.querySelector('#nut-gui-lien-he');

const cacTruong = [
    document.querySelector('#ho-ten'),
    document.querySelector('#email'),
    document.querySelector('#so-dien-thoai'),
    document.querySelector('#nguoi-nhan'),
    document.querySelector('#dip-tang'),
    document.querySelector('#ngan-sach'),
    document.querySelector('#ngay-can-qua'),
    document.querySelector('#so-thich'),
    document.querySelector('#dong-y')
];

function layOThongBao(truong) {
    return document.querySelector(`#loi-${truong.id}`);
}

function datLoiChoTruong(truong, noiDung) {
    const oLoi = layOThongBao(truong);

    truong.setCustomValidity(noiDung);

    if (oLoi) {
        oLoi.textContent = noiDung;
    }
}

function xoaLoiChoTruong(truong) {
    const oLoi = layOThongBao(truong);

    truong.setCustomValidity('');

    if (oLoi) {
        oLoi.textContent = '';
    }
}

function kiemTraTruong(truong) {
    xoaLoiChoTruong(truong);

    if (truong.validity.valueMissing) {
        datLoiChoTruong(
            truong,
            'Vui lòng nhập hoặc chọn thông tin này.'
        );

        return false;
    }

    if (truong.validity.typeMismatch) {
        datLoiChoTruong(
            truong,
            'Vui lòng nhập email đúng định dạng.'
        );

        return false;
    }

    if (truong.validity.patternMismatch) {

        if (truong.id === 'ho-ten') {
            datLoiChoTruong(
                truong,
                'Họ tên chỉ gồm chữ cái và khoảng trắng, từ 2 đến 50 ký tự.'
            );
        }

        if (truong.id === 'so-dien-thoai') {
            datLoiChoTruong(
                truong,
                'Số điện thoại phải gồm đúng 10 chữ số.'
            );
        }

        return false;
    }

    if (truong.validity.rangeUnderflow) {
        datLoiChoTruong(
            truong,
            'Ngân sách tối thiểu là 5.000 VNĐ.'
        );

        return false;
    }

    if (truong.validity.rangeOverflow) {
        datLoiChoTruong(
            truong,
            'Ngân sách tối đa là 100.000.000 VNĐ.'
        );

        return false;
    }

    if (truong.validity.stepMismatch) {
        datLoiChoTruong(
            truong,
            'Ngân sách phải theo bước 1.000 VNĐ.'
        );

        return false;
    }

    return true;
}

function kiemTraToanBoForm() {
    let hopLe = true;

    cacTruong.forEach((truong) => {

        if (!kiemTraTruong(truong)) {
            hopLe = false;
        }

    });

    return hopLe;
}

function ganSuKien() {

    cacTruong.forEach((truong) => {

        truong.addEventListener(
            'blur',
            () => {
                kiemTraTruong(truong);
            }
        );

        truong.addEventListener(
            'input',
            () => {

                if (truong.validity.valid) {
                    xoaLoiChoTruong(truong);
                }

            }
        );

        truong.addEventListener(
            'change',
            () => {
                kiemTraTruong(truong);
            }
        );

    });

    form?.addEventListener(
        'submit',
        (suKien) => {

            const hopLe =
                kiemTraToanBoForm();

            if (!hopLe) {

                suKien.preventDefault();

                if (thongBao) {
                    thongBao.textContent =
                        'Vui lòng kiểm tra lại thông tin trong biểu mẫu.';
                }

                const truongLoi =
                    cacTruong.find(
                        (truong) =>
                            !truong.validity.valid
                    );

                truongLoi?.focus();

                return;
            }

            /*
             * Không preventDefault().
             * Form sẽ POST trực tiếp tới lien-he.php
             * để PHP xử lý, lưu JSONL và redirect.
             */

            if (nutGui) {
                nutGui.disabled = true;
                nutGui.textContent = 'Đang gửi...';
            }

        }
    );

    form?.addEventListener(
        'reset',
        () => {

            window.setTimeout(() => {

                cacTruong.forEach((truong) => {
                    xoaLoiChoTruong(truong);
                });

                if (thongBao) {
                    thongBao.textContent = '';
                }

            }, 0);

        }
    );
}

ganSuKien();
