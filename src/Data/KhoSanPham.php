<?php

namespace App\Data;

use App\Models\SanPham;

class KhoSanPham
{
    private string $duongDan;

    public function __construct(?string $duongDan = null)
    {
        $this->duongDan = $duongDan ?? dirname(__DIR__, 2) . '/data/san-pham.json';
    }

    /**
     * Đọc toàn bộ sản phẩm từ file JSON.
     */
    public function layTatCa(): array
    {
        $noiDung = file_get_contents($this->duongDan);

        if ($noiDung === false) {
            throw new \RuntimeException('Không thể đọc dữ liệu sản phẩm.');
        }

        $duLieu = json_decode($noiDung, true);

        if (!is_array($duLieu)) {
            throw new \RuntimeException('Dữ liệu sản phẩm không hợp lệ.');
        }

        return array_map(
            fn(array $sp) => $this->taoSanPham($sp),
            $duLieu
        );
    }

    /**
     * Tìm sản phẩm theo ID.
     */
    public function timTheoId(int $id): ?SanPham
    {
        foreach ($this->layTatCa() as $sanPham) {
            if ($sanPham->getId() === $id) {
                return $sanPham;
            }
        }

        return null;
    }

    /**
     * Chuyển dữ liệu JSON thành đối tượng SanPham.
     */
    private function taoSanPham(array $duLieu): SanPham
    {
        return new SanPham(
            (int) $duLieu['id'],
            (string) $duLieu['ten'],
            (string) $duLieu['danhMuc'],
            (float) $duLieu['gia'],
            (int) $duLieu['soLuong'],
            (string) $duLieu['moTa'],
            (string) $duLieu['hinhAnh']
        );
    }
}
