<?php

/**
 * Servicio de envío de emails con PHPMailer
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../../vendor/autoload.php';

class EmailService
{
    private PHPMailer $mail;

    public function __construct()
    {
        // Crear instancia con excepciones habilitadas
        $this->mail = new PHPMailer(true);
        $this->configureSMTP();
    }

    /**
     * Configura el servidor SMTP
     */
    private function configureSMTP(): void
    {
        // Server settings
        $this->mail->SMTPDebug = 0;
        $this->mail->isSMTP();
        $this->mail->Host = SMTP_HOST;
        $this->mail->SMTPAuth = true;
        $this->mail->Username = SMTP_USERNAME;
        $this->mail->Password = SMTP_PASSWORD;
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->Port = SMTP_PORT;
        $this->mail->CharSet = 'UTF-8';
    }

    /**
     * Envía email de cotización con PDF adjunto
     * 
     * @param array $userData ['fullName', 'email', 'phone']
     * @param string $pdfPath Ruta completa del PDF
     * @param string $pdfFilename Nombre del archivo
     * @param array $quoteData ['totalProducts', 'totalQuantity']
     * @return bool True si se envió correctamente
     */
    public function sendQuoteEmail(
        array $userData,
        string $pdfPath,
        array $quoteData
    ): bool {
        try {
            // Recipients
            $this->mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
            $this->mail->addAddress($userData['email'], $userData['fullName']);
            $this->mail->addReplyTo('info@medworkcr.com', 'Medical Works');

            // Attachments
            $this->mail->addAttachment($pdfPath, 'Cotizacion_MedicalWorks.pdf');

            // Content
            $this->mail->isHTML(true);
            $this->mail->Subject = 'Tu cotización de Medical Works';
            $this->mail->Body = $this->buildHtmlBody($userData, $quoteData);
            $this->mail->AltBody = $this->buildTextBody($userData, $quoteData);

            // Send
            $this->mail->send();

            // Limpiar para próximo envío
            $this->mail->clearAddresses();
            $this->mail->clearAttachments();

            return true;
        } catch (Exception $e) {
            error_log("Error enviando email: {$this->mail->ErrorInfo}");
            return false;
        }
    }

    /**
     * Construye el cuerpo HTML del email
     */
    private function buildHtmlBody(array $userData, array $quoteData): string
    {
        $name = htmlspecialchars($userData['fullName']);
        $totalProducts = $quoteData['totalProducts'];
        $totalQuantity = $quoteData['totalQuantity'];

        return <<<HTML
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
        </head>
        <body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
            <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f4f4; padding: 20px;">
                <tr>
                    <td align="center">
                        <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                            
                            <!-- Header -->
                            <tr>
                                <td style="background-color: #0EA5E9; padding: 30px; text-align: center;">
                                    <h1 style="margin: 0; color: #ffffff; font-size: 28px;">Medical Works</h1>
                                    <p style="margin: 10px 0 0 0; color: #ffffff; font-size: 14px;">Suministros Médicos de Calidad</p>
                                </td>
                            </tr>
                            
                            <!-- Body -->
                            <tr>
                                <td style="padding: 40px 30px;">
                                    <h2 style="margin: 0 0 20px 0; color: #1E293B; font-size: 24px;">¡Hola, {$name}!</h2>
                                    
                                    <p style="margin: 0 0 15px 0; color: #64748B; font-size: 16px; line-height: 1.6;">
                                        Gracias por tu interés en nuestros productos. Hemos generado tu cotización con los siguientes detalles:
                                    </p>
                                    
                                    <table width="100%" cellpadding="10" style="margin: 20px 0; background-color: #F8FAFC; border-radius: 6px;">
                                        <tr>
                                            <td style="color: #64748B; font-size: 14px;">Productos únicos:</td>
                                            <td style="color: #1E293B; font-size: 14px; font-weight: bold; text-align: right;">{$totalProducts}</td>
                                        </tr>
                                        <tr>
                                            <td style="color: #64748B; font-size: 14px;">Cantidad total:</td>
                                            <td style="color: #1E293B; font-size: 14px; font-weight: bold; text-align: right;">{$totalQuantity} unidades</td>
                                        </tr>
                                    </table>
                                    
                                    <p style="margin: 20px 0 15px 0; color: #64748B; font-size: 16px; line-height: 1.6;">
                                        Encontrarás el detalle completo en el archivo PDF adjunto.
                                    </p>
                                    
                                    <p style="margin: 20px 0 15px 0; color: #64748B; font-size: 16px; line-height: 1.6;">
                                        Uno de nuestros asesores se pondrá en contacto contigo pronto para confirmar precios y disponibilidad.
                                    </p>
                                </td>
                            </tr>
                            
                            <tr>
                                <td style="padding: 0 30px 40px 30px; text-align: center;">
                                    <a href="https://cambiar_despues_por_url" style="display: inline-block; padding: 14px 30px; background-color: #0EA5E9; color: #ffffff; text-decoration: none; border-radius: 6px; font-size: 16px; font-weight: bold;">
                                        Visitar nuestro catálogo
                                    </a>
                                </td>
                            </tr>
                            
                            <!-- Footer -->
                            <tr>
                                <td style="background-color: #F8FAFC; padding: 30px; text-align: center; border-top: 1px solid #E2E8F0;">
                                    <p style="margin: 0 0 10px 0; color: #64748B; font-size: 14px;">
                                        <strong>Medical Works</strong><br>
                                        Costa Rica, San José, Aserrí<br>
                                        Tel: +506 2230-8023 | Email: info@medworkcr.com
                                    </p>
                                    <p style="margin: 15px 0 0 0; color: #94A3B8; font-size: 12px;">
                                        Este correo fue generado automáticamente. Por favor, no respondas a este mensaje.
                                    </p>
                                </td>
                            </tr>
                            
                        </table>
                    </td>
                </tr>
            </table>
        </body>
        </html>
        HTML;
    }

    /**
     * Construye el cuerpo de texto plano
     */
    private function buildTextBody(array $userData, array $quoteData): string
    {
        $name = $userData['fullName'];
        $totalProducts = $quoteData['totalProducts'];
        $totalQuantity = $quoteData['totalQuantity'];

        return <<<TEXT
        Hola, {$name}!
        
        Gracias por tu interés en Medical Works. Hemos generado tu cotización.
        
        RESUMEN:
        - Productos únicos: {$totalProducts}
        - Cantidad total: {$totalQuantity} unidades
        
        Encontrarás el detalle completo en el archivo PDF adjunto.
        
        Uno de nuestros asesores se pondrá en contacto contigo pronto.
        
        ---
        Medical Works
        Costa Rica, San José, Aserrí
        Tel: +506 2230-8023
        Email: info@medworkcr.com
        
        Este correo fue generado automáticamente.
        TEXT;
    }
}
