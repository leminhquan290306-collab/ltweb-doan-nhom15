<?php

namespace App\Data;

class KhoLienHe
{
    public function __construct()
    {
        // Sử dụng kết nối PDO chung từ hàm hamCSDL()
    }

    public function luu(array $duLieu): void
    {
        $db = hamCSDL();
        $st = $db->prepare("INSERT INTO lien_he (ten, email, noi_dung) VALUES (?, ?, ?)");
        $st->execute([
            $duLieu['ten'] ?? '',
            $duLieu['email'] ?? '',
            $duLieu['noiDung'] ?? $duLieu['noi_dung'] ?? ''
        ]);
    }

    public function layTatCa(): array
    {
        $db = hamCSDL();
        $st = $db->query("SELECT id, ten, email, noi_dung as noiDung, ngay_tao as ngayTao FROM lien_he ORDER BY id DESC");
        return $st->fetchAll();
    }
}