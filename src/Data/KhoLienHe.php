<?php

namespace App\Data;

class KhoLienHe
{
    private string $duongDan;

    public function __construct(?string $duongDan = null)
    {
        $this->duongDan = $duongDan
            ?? dirname(__DIR__, 2) . '/storage/lien-he.jsonl';
    }

    public function luu(array $duLieu): void
    {
        $thuMuc = dirname($this->duongDan);

        if (!is_dir($thuMuc)) {
            mkdir($thuMuc, 0755, true);
        }

        $dong = json_encode(
            $duLieu,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );

        if ($dong === false) {
            throw new \RuntimeException('Không thể chuyển dữ liệu sang JSON.');
        }

        $ketQua = file_put_contents(
            $this->duongDan,
            $dong . PHP_EOL,
            FILE_APPEND | LOCK_EX
        );

        if ($ketQua === false) {
            throw new \RuntimeException('Không thể lưu thông tin liên hệ.');
        }
    }

    public function layTatCa(): array
    {
        if (!is_file($this->duongDan)) {
            return [];
        }

        $cacDong = file(
            $this->duongDan,
            FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES
        );

        if ($cacDong === false) {
            throw new \RuntimeException(
                'Không thể đọc dữ liệu liên hệ.'
            );
        }

        $ketQua = [];

        foreach ($cacDong as $dong) {
            $duLieu = json_decode($dong, true);

            if (is_array($duLieu)) {
                $ketQua[] = $duLieu;
            }
        }

        return $ketQua;
    }
}