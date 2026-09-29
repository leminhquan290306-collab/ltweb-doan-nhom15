/*
 * trang-lien-he.js
 * Kiểm tra biểu mẫu liên hệ và gửi dữ liệu bằng POST JSON.
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
            'Ngân sách tối thiểu là 50.000 VNĐ.'
        );

        return false;
    }

    if (truong.validity.rangeOverflow) {
        datLoiChoTruong(
            truong,
            'Ngân sách tối đa là 10.000.000 VNĐ.'
        );

        return false;
    }

    if (truong.validity.stepMismatch) {
        datLoiChoTruong(
            truong,
            'Ngân sách phải theo bước 50.000 VNĐ.'
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

function layDuLieuForm() {
    return {
        hoTen: document.querySelector('#ho-ten').value.trim(),

        email: document.querySelector('#email').value.trim(),

        soDienThoai:
            document.querySelector('#so-dien-thoai').value.trim(),

        nguoiNhan:
            document.querySelector('#nguoi-nhan').value,

        dipTang:
            document.querySelector('#dip-tang').value,

        nganSach:
            Number(document.querySelector('#ngan-sach').value),

        ngayCanQua:
            document.querySelector('#ngay-can-qua').value,

        soThich:
            document.querySelector('#so-thich').value.trim(),

        dongY:
            document.querySelector('#dong-y').checked
    };
}

function hienThiThongBao(noiDung) {
    if (thongBao) {
        thongBao.textContent = noiDung;
    }
}

async function guiDuLieuForm() {
    const duLieu = layDuLieuForm();

    if (nutGui) {
        nutGui.disabled = true;
        nutGui.textContent = 'Đang gửi...';
    }

    hienThiThongBao(
        'Đang gửi yêu cầu tư vấn...'
    );

    try {
        const phanHoi = await fetch(
            'https://jsonplaceholder.typicode.com/posts',
            {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(duLieu)
            }
        );

        if (!phanHoi.ok) {
            throw new Error(
                `Gửi dữ liệu thất bại: ${phanHoi.status}`
            );
        }

        const ketQua = await phanHoi.json();

        console.log(
            'Dữ liệu phản hồi:',
            ketQua
        );

        hienThiThongBao(
            'Gửi yêu cầu tư vấn thành công!'
        );

        form.reset();

        cacTruong.forEach((truong) => {
            xoaLoiChoTruong(truong);
        });

    } catch (loi) {
        console.error(loi);

        hienThiThongBao(
            'Không thể gửi yêu cầu. Vui lòng thử lại sau.'
        );

    } finally {
        if (nutGui) {
            nutGui.disabled = false;
            nutGui.textContent =
                'Gửi yêu cầu tư vấn';
        }
    }
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
        async (suKien) => {
            suKien.preventDefault();

            const hopLe =
                kiemTraToanBoForm();

            if (!hopLe) {
                hienThiThongBao(
                    'Vui lòng kiểm tra lại thông tin trong biểu mẫu.'
                );

                const truongLoi =
                    cacTruong.find(
                        (truong) =>
                            !truong.validity.valid
                    );

                truongLoi?.focus();

                return;
            }

            await guiDuLieuForm();
        }
    );

    form?.addEventListener(
        'reset',
        () => {
            window.setTimeout(() => {
                cacTruong.forEach((truong) => {
                    xoaLoiChoTruong(truong);
                });
            }, 0);
        }
    );
}

ganSuKien();