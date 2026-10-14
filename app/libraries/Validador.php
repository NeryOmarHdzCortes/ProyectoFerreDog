<?php

class Validador
{
    public static function telefonoValido(string $tel): bool
    {
        return preg_match('/^[0-9]{10}$/', trim($tel)) === 1;
    }

    public static function correoValido(string $correo): bool
    {
        $correo = strtolower(trim($correo));

        if (filter_var($correo, FILTER_VALIDATE_EMAIL) === false
            || preg_match('/^[a-z0-9._%+-]+@[a-z0-9-]+(\.[a-z0-9-]+)*\.[a-z]{2,}$/', $correo) !== 1
        ) {
            return false;
        }

        $partes = explode('@', $correo);
        $dominio = end($partes);
        $extensionesPermitidas = [
            '.com',
            '.net',
            '.org',
            '.edu',
            '.info',
            '.mx',
            '.com.mx',
            '.edu.mx',
            '.gob.mx',
            '.org.mx',
            '.net.mx'
        ];
        $terminaConExtensionPermitida = false;

        foreach ($extensionesPermitidas as $extension) {
            if (substr($dominio, -strlen($extension)) === $extension) {
                $terminaConExtensionPermitida = true;
                break;
            }
        }

        if (!$terminaConExtensionPermitida) {
            return false;
        }

        if (preg_match('/^(gmail|hotmail|outlook|yahoo|live|icloud)/', $dominio) === 1) {
            return in_array($dominio, [
                'gmail.com',
                'hotmail.com',
                'outlook.com',
                'outlook.es',
                'yahoo.com',
                'yahoo.com.mx',
                'live.com',
                'live.com.mx',
                'icloud.com'
            ], true);
        }

        return true;
    }
}
