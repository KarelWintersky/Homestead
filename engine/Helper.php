<?php

namespace Homestead;
class Helper
{
    /**
     * Проверяет, разрешен ли текущий IP-адрес для доступа к секции
     *
     * @param array $allowedIPs Массив разрешенных IP-адресов или CIDR-блоков
     * @return bool
     */
    public static function isIPAllowed(array $allowedIPs): bool
    {
        $clientIP = self::getClientIP();

        foreach ($allowedIPs as $allowed) {
            if (self::checkIP($clientIP, $allowed)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Получает реальный IP-адрес клиента
     *
     * @return string
     */
    public static function getClientIP(): string
    {
        // Проверяем заголовки прокси
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            return trim($ips[0]);
        }

        if (!empty($_SERVER['HTTP_X_REAL_IP'])) {
            return $_SERVER['HTTP_X_REAL_IP'];
        }

        return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }

    /**
     * Проверяет, входит ли IP в указанный диапазон (CIDR или точное совпадение)
     *
     * @param string $ip Проверяемый IP-адрес
     * @param string $range CIDR-блок или конкретный IP
     * @return bool
     */
    public static function checkIP(string $ip, string $range): bool
    {
        // Если указан конкретный IP
        if (!str_contains($range, '/')) {
            return $ip === $range;
        }

        // Обработка CIDR-нотации
        list($subnet, $bits) = explode('/', $range);
        $bits = (int)$bits;

        // Конвертируем IP в числа
        $ip = ip2long($ip);
        $subnet = ip2long($subnet);
        $mask = -1 << (32 - $bits);

        return ($ip & $mask) === ($subnet & $mask);
    }



}