<?php

namespace App\Services;

use App\Data\KhoSanPham;

class GioHang
{
    private const SESSION_KEY = 'gio_hang';

    private KhoSanPham $khoSanPham;

    public function __construct(?KhoSanPham $khoSanPham = null)
    {
        $this->khoSanPham = $khoSanPham ?? new KhoSanPham();

        if (!isset($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = [];
        }
    }

    /**
     * Thêm sản phẩm vào giỏ.
     */
    public function them(int $id, int $soLuong = 1): void
    {
        $sanPham = $this->khoSanPham->timTheoId($id);

        if ($sanPham === null) {
            throw new \RuntimeException('Sản phẩm không tồn tại.');
        }

        if ($soLuong < 1) {
            throw new \InvalidArgumentException(
                'Số lượng phải lớn hơn 0.'
            );
        }

        $soLuongHienTai = $this->soLuong($id);
        $soLuongMoi = $soLuongHienTai + $soLuong;

        if ($soLuongMoi > $sanPham->getSoLuong()) {
            throw new \InvalidArgumentException(
                'Số lượng sản phẩm trong giỏ vượt quá tồn kho.'
            );
        }

        $_SESSION[self::SESSION_KEY][$id] = $soLuongMoi;
    }

    /**
     * Cập nhật số lượng một sản phẩm.
     */
    public function capNhat(int $id, int $soLuong): void
    {
        $sanPham = $this->khoSanPham->timTheoId($id);

        if ($sanPham === null) {
            throw new \RuntimeException('Sản phẩm không tồn tại.');
        }

        if ($soLuong < 1) {
            $this->xoa($id);
            return;
        }

        if ($soLuong > $sanPham->getSoLuong()) {
            throw new \InvalidArgumentException(
                'Số lượng vượt quá tồn kho.'
            );
        }

        $_SESSION[self::SESSION_KEY][$id] = $soLuong;
    }

    /**
     * Xóa một sản phẩm khỏi giỏ.
     */
    public function xoa(int $id): void
    {
        unset($_SESSION[self::SESSION_KEY][$id]);
    }

    /**
     * Xóa toàn bộ giỏ hàng.
     */
    public function xoaTatCa(): void
    {
        $_SESSION[self::SESSION_KEY] = [];
    }

    /**
     * Lấy số lượng của một sản phẩm.
     */
    public function soLuong(int $id): int
    {
        return (int) (
            $_SESSION[self::SESSION_KEY][$id] ?? 0
        );
    }

    /**
     * Lấy danh sách sản phẩm trong giỏ.
     */
    public function laySanPham(): array
    {
        $ketQua = [];

        foreach ($_SESSION[self::SESSION_KEY] as $id => $soLuong) {
            $sanPham = $this->khoSanPham->timTheoId((int) $id);

            if ($sanPham !== null) {
                $ketQua[] = [
                    'sanPham' => $sanPham,
                    'soLuong' => (int) $soLuong,
                    'thanhTien' => $sanPham->getGia() * (int) $soLuong,
                ];
            }
        }

        return $ketQua;
    }

    /**
     * Tính tổng tiền.
     */
    public function tongTien(): float
    {
        $tong = 0;

        foreach ($this->laySanPham() as $item) {
            $tong += $item['thanhTien'];
        }

        return $tong;
    }

    /**
     * Tổng số sản phẩm trong giỏ.
     */
    public function tongSoLuong(): int
    {
        $tong = 0;

        foreach ($_SESSION[self::SESSION_KEY] as $soLuong) {
            $tong += (int) $soLuong;
        }

        return $tong;
    }
}
