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
     * Notifica al proveedor que su pedido fue cancelado.
     */
    public function enviarCancelacion($correo, $proveedor, $idPedido, $total) {
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            error_log('No se envió la cancelación: el correo del proveedor no es válido.');
            return false;
        }

        $asunto = 'Ferredog - Pedido #' . (int) $idPedido . ' cancelado';
        $nombreSeguro = htmlspecialchars($proveedor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $totalFormateado = number_format((float) $total, 2, '.', ',');
        $cuerpoHtml = "
            <h2>Pedido cancelado</h2>
            <p>Hola {$nombreSeguro},</p>
            <p>El pedido #" . (int) $idPedido . " por un total de $" . $totalFormateado . " fue cancelado.</p>
            <p>Ferredog - Control de inventario</p>
        ";
        $cuerpoTexto = "Hola {$proveedor}, el pedido #" . (int) $idPedido
            . " por un total de $" . $totalFormateado . " fue cancelado.";

        if ($this->modo === 'prueba') {
            $rutaLog = BASE_PATH . '/storage/logs/correos.log';
            $directorio = dirname($rutaLog);
            if (!is_dir($directorio) && !mkdir($directorio, 0755, true) && !is_dir($directorio)) {
                error_log('No se pudo crear el directorio del log de correos.');
                return false;
            }

            $linea = '[' . date('Y-m-d H:i:s') . '] ' . $asunto . ' para ' . $correo
                . ' (' . $proveedor . '), total $' . $totalFormateado . PHP_EOL;
            if (file_put_contents($rutaLog, $linea, FILE_APPEND | LOCK_EX) === false) {
                error_log('No se pudo escribir la cancelación en el log de correos.');
                return false;
            }

            return true;
        }

        if ($this->modo !== 'gmail') {
            error_log('Modo de correo no válido. Configura MAIL_MODO como "prueba" o "gmail".');
            return false;
        }

        return $this->enviarNotificacionSMTP($correo, $proveedor, $asunto, $cuerpoHtml, $cuerpoTexto);
    }

    /**
     * Envía al proveedor el detalle del pedido ya guardado.
     */
    public function enviarPedido($correo, $proveedor, $idPedido, $fecha, $renglones, $total) {
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            error_log('No se envió el pedido: el correo del proveedor no es válido.');
            return false;
        }

        $idPedido = (int) $idPedido;
        $asunto = 'Ferredog - Pedido #' . $idPedido;
        $proveedorSeguro = htmlspecialchars($proveedor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $filasHtml = '';
        $filasTexto = [];
        foreach ($renglones as $renglon) {
            $nombreProducto = (string) ($renglon['producto'] ?? '');
            $nombreSeguro = htmlspecialchars($nombreProducto, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            $cantidad = (int) ($renglon['cantidad'] ?? 0);
            $costo = number_format((float) ($renglon['costo'] ?? 0), 2, '.', ',');
            $subtotal = number_format((float) ($renglon['subtotal'] ?? 0), 2, '.', ',');
            $filasHtml .= '<tr><td>' . $nombreSeguro . '</td><td>' . $cantidad
                . '</td><td>$' . $costo . '</td><td>$' . $subtotal . '</td></tr>';
            $filasTexto[] = $nombreProducto . ' | ' . $cantidad . ' | $' . $costo . ' | $' . $subtotal;
        }

        $totalFormateado = number_format((float) $total, 2, '.', ',');
        $cuerpoHtml = '<h2>Pedido #' . $idPedido . '</h2>'
            . '<p>Hola ' . $proveedorSeguro . ',</p><p>Fecha: '
            . htmlspecialchars($fecha, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</p>'
            . '<table border="1" cellpadding="6" cellspacing="0">'
            . '<thead><tr><th>Producto</th><th>Cantidad</th><th>Costo</th><th>Subtotal</th></tr></thead>'
            . '<tbody>' . $filasHtml . '</tbody></table>'
            . '<p><strong>Total: $' . $totalFormateado . '</strong></p>';
        $cuerpoTexto = "Pedido #{$idPedido} para {$proveedor}. Fecha: {$fecha}.\n"
            . "Producto | Cantidad | Costo | Subtotal\n" . implode("\n", $filasTexto)
            . "\nTotal: $" . $totalFormateado;

        if ($this->modo === 'prueba') {
            $rutaLog = BASE_PATH . '/storage/logs/correos.log';
            $directorio = dirname($rutaLog);
            if (!is_dir($directorio) && !mkdir($directorio, 0755, true) && !is_dir($directorio)) {
                error_log('No se pudo crear el directorio del log de correos.');
                return false;
            }

            $linea = '[' . date('Y-m-d H:i:s') . '] ' . $asunto . ' para ' . $correo
                . ' (' . $proveedor . "), fecha {$fecha}\n"
                . implode("\n", $filasTexto) . "\nTotal: $" . $totalFormateado . PHP_EOL;
            if (file_put_contents($rutaLog, $linea, FILE_APPEND | LOCK_EX) === false) {
                error_log('No se pudo escribir el pedido en el log de correos.');
                return false;
            }

            return true;
        }

        if ($this->modo !== 'gmail') {
            error_log('Modo de correo no válido. Configura MAIL_MODO como "prueba" o "gmail".');
            return false;
        }

        return $this->enviarNotificacionSMTP($correo, $proveedor, $asunto, $cuerpoHtml, $cuerpoTexto);
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
     * Envía una notificación HTML mediante la configuración SMTP existente.
     */
    private function enviarNotificacionSMTP($correo, $nombre, $asunto, $cuerpoHtml, $cuerpoTexto) {
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
            $mail->setFrom($usuarioSMTP, $_ENV['MAIL_FROM_NAME'] ?? 'Ferredog');
            $mail->addAddress($correo, $nombre);
            $mail->isHTML(true);
            $mail->Subject = $asunto;
            $mail->Body = $cuerpoHtml;
            $mail->AltBody = $cuerpoTexto;
            $mail->send();

            return true;
        } catch (\Throwable $e) {
            $detalle = $e->getMessage();
            if ($mail instanceof \PHPMailer\PHPMailer\PHPMailer && $mail->ErrorInfo !== '') {
                $detalle .= ' ' . $mail->ErrorInfo;
            }
            $this->registrarError($detalle, [$contraseñaSMTP, $usuarioSMTP]);

            return false;
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
