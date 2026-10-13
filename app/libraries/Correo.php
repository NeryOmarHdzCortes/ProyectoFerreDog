<?php
/**
 * Clase Correo
 * Envío del código de recuperación en modo prueba o Gmail
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
        if ($this->modo === 'prueba') {
            return $this->escribirEnLog($correo, $nombre, $codigo);
        }

        if ($this->modo === 'gmail') {
            return $this->enviarPorSMTP($correo, $nombre, $codigo);
        }

        error_log('Modo de correo no válido. Configura MAIL_MODO como "prueba" o "gmail".');
        return [
            'exito' => false,
            'mensaje' => 'No se pudo enviar el correo. Intenta más tarde.'
        ];
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
     * Modo Gmail: enviar por SMTP con PHPMailer
     */
    private function enviarPorSMTP($correo, $nombre, $codigo) {
        $contraseñaSMTP = $_ENV['MAIL_PASS'] ?? '';
        $usuarioSMTP = $_ENV['MAIL_USER'] ?? '';
        $mail = null;

        try {
            require_once BASE_PATH . '/app/libraries/PHPMailer/src/Exception.php';
            require_once BASE_PATH . '/app/libraries/PHPMailer/src/PHPMailer.php';
            require_once BASE_PATH . '/app/libraries/PHPMailer/src/SMTP.php';

            $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = $_ENV['MAIL_HOST'] ?? 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = $usuarioSMTP;
            $mail->Password = $contraseñaSMTP;
            $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = (int) ($_ENV['MAIL_PORT'] ?? 587);
            $mail->CharSet = 'UTF-8';

            $fromName = $_ENV['MAIL_FROM_NAME'] ?? 'Ferredog';
            $mail->setFrom($usuarioSMTP, $fromName);
            $mail->addAddress($correo, $nombre);

            $nombreSeguro = htmlspecialchars($nombre, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            $codigoSeguro = htmlspecialchars($codigo, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            $mail->isHTML(true);
            $mail->Subject = 'Código de recuperación - Ferredog';
            $mail->Body = "
                <h2>Recuperación de contraseña</h2>
                <p>Hola {$nombreSeguro},</p>
                <p>Tu código de recuperación es: <strong style='font-size: 24px;'>{$codigoSeguro}</strong></p>
                <p>Este código vence en 10 minutos.</p>
                <p>Si no solicitaste este código, ignora este correo.</p>
                <hr>
                <p style='font-size: 12px; color: #888;'>Ferredog - Control de inventario</p>
            ";
            $mail->AltBody = "Hola {$nombre}, tu código de recuperación es {$codigo}. "
                . "Este código vence en 10 minutos. Si no solicitaste este código, ignora este correo.";

            $mail->send();

            return [
                'exito' => true,
                'mensaje' => 'Código enviado. Revisa tu bandeja de entrada (vence en 10 min).'
            ];
        } catch (\Throwable $e) {
            $detalle = $e->getMessage();
            if ($mail instanceof \PHPMailer\PHPMailer\PHPMailer && $mail->ErrorInfo !== '') {
                $detalle .= ' ' . $mail->ErrorInfo;
            }
            $this->registrarError($detalle, [$contraseñaSMTP, $usuarioSMTP]);

            return [
                'exito' => false,
                'mensaje' => 'No se pudo enviar el correo. Intenta más tarde.'
            ];
        }
    }
    
    /**
     * Registrar error en el log
     */
    private function registrarError($mensaje, $valoresSensibles = []) {
        foreach ($valoresSensibles as $valorSensible) {
            if ($valorSensible !== '') {
                $mensaje = str_replace($valorSensible, '[redactado]', $mensaje);
            }
        }

        error_log('Error al enviar correo: ' . $mensaje);
    }
}
