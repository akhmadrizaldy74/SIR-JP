<?php

namespace App\Services;

class SecurityMaskingService
{
    /**
     * Mask NIK (16 digit) menjadi format terlindungi: 317101******0001
     */
    public static function maskNik(?string $nik): string
    {
        if (empty($nik)) {
            return '-';
        }
        $nik = trim($nik);
        $len = strlen($nik);
        if ($len <= 6) {
            return str_repeat('*', $len);
        }
        return substr($nik, 0, 6) . str_repeat('*', max(0, $len - 10)) . substr($nik, -4);
    }

    /**
     * Mask NIP (18 digit) menjadi format terlindungi: 198501******0001
     */
    public static function maskNip(?string $nip): string
    {
        if (empty($nip)) {
            return '-';
        }
        $nip = trim($nip);
        $len = strlen($nip);
        if ($len <= 6) {
            return str_repeat('*', $len);
        }
        return substr($nip, 0, 6) . str_repeat('*', max(0, $len - 10)) . substr($nip, -4);
    }

    /**
     * Mask Nomor Telepon/HP: 0812****7890
     */
    public static function maskPhone(?string $phone): string
    {
        if (empty($phone)) {
            return '-';
        }
        $phone = trim($phone);
        $len = strlen($phone);
        if ($len <= 6) {
            return str_repeat('*', $len);
        }
        return substr($phone, 0, 4) . str_repeat('*', max(0, $len - 7)) . substr($phone, -3);
    }

    /**
     * Mask Alamat Email: ad***@domain.com
     */
    public static function maskEmail(?string $email): string
    {
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $email ?: '-';
        }
        [$name, $domain] = explode('@', $email, 2);
        $len = strlen($name);
        if ($len <= 2) {
            $maskedName = substr($name, 0, 1) . '*';
        } else {
            $maskedName = substr($name, 0, 2) . str_repeat('*', max(1, $len - 2));
        }
        return $maskedName . '@' . $domain;
    }
}
