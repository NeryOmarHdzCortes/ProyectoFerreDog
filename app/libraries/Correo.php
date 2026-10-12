<?php
/**
 * Clase Correo
 * Envío del código de recuperación en modo prueba o SMTP
 */

class Correo {
    private $modo;
    
    public function __construct() {
        // Leer modo desde .env
        $this->modo = $_ENV['MAIL_MODO'] ?? 'prueba';
    }
    
    /**
     * Enviar código de recuperación
     * @param string $correo Correo destino
     * @param string $nombre Nombre del usuario
     * @param string $codigo Código de 6 dígitos
     * @return array ['exito' => bool, 'mensaje' => string]
     */
    public function enviarCodigo($correo, $nombre, $codigo) {
        if ($this->modo === 'smtp') {
            return $this->enviarPorSMTP($correo, $nombre, $codigo);
        } else {
            return $this->escribirEnLog($correo, $nombre, $codigo);
        }
    }
    
    /**
     * Modo prueba: escribir en storage/logs/correos.log
     */
    private function escribirEnLog($correo, $nombre, $codigo) {
        $rutaLog = BASE_PATH . '/storage/logs/correos.log';
        $fecha = date('Y-m-d H:i:s');
        $linea = "[{$fecha}] Código de recuperación para {$correo} ({$nombre}): {$codigo}\n";
        
        // Crear el archivo si no existe
        if (!file_exists($rutaLog)) {
            $directorio = dirname($rutaLog);
            if (!is_dir($directorio)) {
                mkdir($directorio, 0755, true);
            }
        }
        
        // Escribir en el log
        $resultado = file_put_contents($rutaLog, $linea, FILE_APPEND);
        
        if ($resultado !== false) {
            return [
                'exito' => true,
                'mensaje' => 'Código enviado. Revisa tu bandeja de entrada (vence en 10 min).'
            ];
        } else {
            return [
                'exito' => false,
                'mensaje' => 'No se pudo escribir el código. Intenta más tarde.'
            ];
        }
    }
    
    /**
     * Modo SMTP: enviar por Gmail con PHPMailer
     * Solo se usará cuando PHPMailer esté instalado al final del proyecto
     */
    private function enviarPorSMTP($correo, $nombre, $codigo) {
        // Verificar que PHPMailer esté disponible
        $rutaPHPMailer = BASE_PATH . '/app/libraries/PHPMailer/PHPMailer.php';
        
        if (!file_exists($rutaPHPMailer)) {
            // PHPMailer no está instalado, registrar error y cambiar a modo log
            $this->registrarError('PHPMailer no está instalado. Cambia MAIL_MODO a "prueba" en .env');
            return $this->escribirEnLog($correo, $nombre, $codigo);
        }
        
        try {
            // Cargar PHPMailer
            require_once BASE_PATH . '/app/libraries/PHPMailer/PHPMailer.php';
            require_once BASE_PATH . '/app/libraries/PHPMailer/SMTP.php';
            require_once BASE_PATH . '/app/libraries/PHPMailer/Exception.php';
            
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            
            // Configuración SMTP
            $mail->isSMTP();
            $mail->Host = $_ENV['MAIL_HOST'] ?? 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['MAIL_USER'] ?? '';
            $mail->Password = $_ENV['MAIL_PASS'] ?? '';
            $mail->SMTPSecure = 'tls';
            $mail->Port = $_ENV['MAIL_PORT'] ?? 587;
            $mail->CharSet = 'UTF-8';
            
            // Remitente y destinatario
            $fromName = $_ENV['MAIL_FROM_NAME'] ?? 'Ferredog';
            $mail->setFrom($_ENV['MAIL_USER'], $fromName);
            $mail->addAddress($correo, $nombre);
            
            // Contenido
            $mail->isHTML(true);
            $mail->Subject = 'Código de recuperación - Ferredog';
            $mail->Body = "
                <h2>Recuperación de contraseña</h2>
                <p>Hola {$nombre},</p>
                <p>Tu código de recuperación es: <strong style='font-size: 24px;'>{$codigo}</strong></p>
                <p>Este código vence en 10 minutos.</p>
                <p>Si no solicitaste este código, ignora este correo.</p>
                <hr>
                <p style='font-size: 12px; color: #888;'>Ferredog - Control de inventario</p>
            ";
            
            $mail->send();
            
            return [
                'exito' => true,
                'mensaje' => 'Código enviado. Revisa tu bandeja de entrada (vence en 10 min).'
            ];
            
        } catch (Exception $e) {
            // Error al enviar
            $this->registrarError("Error al enviar correo: {$mail->ErrorInfo}");
            
            return [
                'exito' => false,
                'mensaje' => 'No se pudo enviar el correo. Intenta más tarde.'
            ];
        }
    }
    
    /**
     * Registrar error en el log
     */
    private function registrarError($mensaje) {
        $rutaLog = BASE_PATH . '/storage/logs/correos.log';
        $fecha = date('Y-m-d H:i:s');
        $linea = "[{$fecha}] ERROR: {$mensaje}\n";
        
        file_put_contents($rutaLog, $linea, FILE_APPEND);
    }
}
