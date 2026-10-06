<?php

namespace App\Models;

class SanPham
{
    public function __construct(
        private int $id,
        private string $ten,
        private string $danhMuc,
        private float $gia,
        private int $soLuong,
        private string $moTa,
        private string $hinhAnh
    ) {}

    public function getId(): int
    {
        return $this->id;
    }

    public function getTen(): string
    {
        return $this->ten;
    }

    public function getDanhMuc(): string
    {
        return $this->danhMuc;
    }

    public function getGia(): float
    {
        return $this->gia;
    }

    public function getSoLuong(): int
    {
        return $this->soLuong;
    }

    public function getMoTa(): string
    {
        return $this->moTa;
    }

    public function getHinhAnh(): string
    {
        return $this->hinhAnh;
    }
}
