/*
 * api.js
 * Hàm dùng chung để tải dữ liệu JSON bằng Fetch API.
 */

export async function taiJSON(url) {
    const phanHoi = await fetch(url);

    if (!phanHoi.ok) {
        throw new Error(`Không thể tải dữ liệu: ${phanHoi.status}`);
    }

    return await phanHoi.json();
}