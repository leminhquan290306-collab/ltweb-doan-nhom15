
/*
 * trang-chu.js
 * Tải và hiển thị dữ liệu thời tiết Đà Nẵng
 * từ Public REST API Open-Meteo.
 */

const URL_API =
    'https://api.open-meteo.com/v1/forecast'
    + '?latitude=16.0471'
    + '&longitude=108.2068'
    + '&current=temperature_2m,relative_humidity_2m,weather_code'
    + '&timezone=Asia%2FBangkok';

const thongBao =
    document.querySelector('#thong-bao-thoi-tiet');

const nhietDo =
    document.querySelector('#nhiet-do');

const doAm =
    document.querySelector('#do-am');

const trangThai =
    document.querySelector('#trang-thai-thoi-tiet');

const urlApi =
    document.querySelector('#url-api-thoi-tiet');

const jsonThoiTiet =
    document.querySelector('#json-thoi-tiet');

function hienThiThongBao(noiDung) {
    if (thongBao) {
        thongBao.textContent = noiDung;
    }
}

function layMoTaThoiTiet(maThoiTiet) {
    const moTa = {
        0: 'Trời quang',
        1: 'Ít mây',
        2: 'Có mây',
        3: 'Nhiều mây',
        45: 'Sương mù',
        48: 'Sương mù đóng băng',
        51: 'Mưa phùn nhẹ',
        53: 'Mưa phùn vừa',
        55: 'Mưa phùn mạnh',
        61: 'Mưa nhẹ',
        63: 'Mưa vừa',
        65: 'Mưa to',
        71: 'Tuyết nhẹ',
        73: 'Tuyết vừa',
        75: 'Tuyết to',
        80: 'Mưa rào nhẹ',
        81: 'Mưa rào vừa',
        82: 'Mưa rào mạnh',
        95: 'Dông',
        96: 'Dông có mưa đá nhẹ',
        99: 'Dông có mưa đá mạnh'
    };

    return moTa[maThoiTiet] ?? 'Không xác định';
}

function hienThiThoiTiet(duLieu) {
    const hienTai = duLieu.current;

    if (!hienTai) {
        throw new Error(
            'API không trả về dữ liệu thời tiết hiện tại.'
        );
    }

    // Kiểm tra các phần tử trước khi cập nhật.
    // Nếu trang không có khu vực thời tiết thì dừng an toàn.
    if (!nhietDo || !doAm || !trangThai || !jsonThoiTiet) {
        return;
    }

    const nhietDoHienTai =
        hienTai.temperature_2m;

    const doAmHienTai =
        hienTai.relative_humidity_2m;

    const maThoiTiet =
        hienTai.weather_code;

    nhietDo.textContent =
        `${nhietDoHienTai} °C`;

    doAm.textContent =
        `${doAmHienTai} %`;

    trangThai.textContent =
        layMoTaThoiTiet(maThoiTiet);

    const duLieuRutGon = {
        temperature_2m: nhietDoHienTai,
        relative_humidity_2m: doAmHienTai,
        weather_code: maThoiTiet
    };

    jsonThoiTiet.textContent =
        JSON.stringify(duLieuRutGon, null, 2);

    hienThiThongBao(
        'Đã cập nhật dữ liệu thời tiết.'
    );
}

async function taiThoiTiet() {
    hienThiThongBao(
        'Đang tải dữ liệu thời tiết...'
    );

    if (urlApi) {
        urlApi.textContent = URL_API;
    }

    try {
        const phanHoi =
            await fetch(URL_API);

        if (!phanHoi.ok) {
            throw new Error(
                `API trả về lỗi ${phanHoi.status}.`
            );
        }

        const duLieu =
            await phanHoi.json();

        hienThiThoiTiet(duLieu);

    } catch (loi) {
        console.error(loi);

        hienThiThongBao(
            'Không thể tải dữ liệu thời tiết. Vui lòng thử lại sau.'
        );

        if (jsonThoiTiet) {
            jsonThoiTiet.textContent =
                'Không thể nhận dữ liệu từ API.';
        }
    }
}

// Chỉ tải thời tiết khi trang có đủ các phần tử cần thiết.
if (nhietDo && doAm && trangThai && jsonThoiTiet) {
    taiThoiTiet();
}