<?php

namespace App\Models;

class NguoiDung
{
    private ?int $id;
    private string $tenDangNhap;
    private string $matKhau;
    private string $hoTen;
    private string $email;
    private ?string $soDienThoai;
    private ?string $diaChi;
    private string $vaiTro;
    private string $ngayTao;

    public function __construct(
        ?int $id,
        string $tenDangNhap,
        string $matKhau,
        string $hoTen,
        string $email,
        ?string $soDienThoai,
        ?string $diaChi,
        string $vaiTro,
        string $ngayTao
    ) {
        $this->id = $id;
        $this->tenDangNhap = $tenDangNhap;
        $this->matKhau = $matKhau;
        $this->hoTen = $hoTen;
        $this->email = $email;
        $this->soDienThoai = $soDienThoai;
        $this->diaChi = $diaChi;
        $this->vaiTro = $vaiTro;
        $this->ngayTao = $ngayTao;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTenDangNhap(): string
    {
        return $this->tenDangNhap;
    }

    public function getMatKhau(): string
    {
        return $this->matKhau;
    }

    public function getHoTen(): string
    {
        return $this->hoTen;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getSoDienThoai(): ?string
    {
        return $this->soDienThoai;
    }

    public function getDiaChi(): ?string
    {
        return $this->diaChi;
    }

    public function getVaiTro(): string
    {
        return $this->vaiTro;
    }

    public function getNgayTao(): string
    {
        return $this->ngayTao;
    }
}