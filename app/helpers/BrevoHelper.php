<?php

namespace app\helpers;

class BrevoHelper
{
    /**
     * Envía un correo de recuperación de contraseña a través de la API v3 de Brevo usando cURL
     * Retorna un array con: ['exito' => bool, 'mensaje' => string]
     */
    public static function enviarCorreoRecuperacion($destinatarioEmail, $destinatarioNombre, $linkRecuperacion)
    {
        $apiKey    = $_ENV['BREVO_API_KEY'] ?? getenv('BREVO_API_KEY') ?? '';
        $fromEmail = $_ENV['BREVO_FROM_EMAIL'] ?? getenv('BREVO_FROM_EMAIL') ?? '';
        $fromName  = $_ENV['BREVO_FROM_NAME'] ?? getenv('BREVO_FROM_NAME') ?? 'DeskFlow Soporte';

        // Si no está configurada la API Key, avisamos amistosamente
        if (empty($apiKey) || empty($fromEmail)) {
            return [
                'exito'   => false,
                'mensaje' => 'Servicio de correo Brevo no configurado en el archivo .env.'
            ];
        }

        $url = 'https://api.brevo.com/v3/smtp/email';

        // Estructura HTML del correo
        $htmlContent = '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e2e8f0; border-radius: 8px;">
            <h2 style="color: #0d6efd; text-align: center;">DeskFlow</h2>
            <p>Hola <strong>' . htmlspecialchars($destinatarioNombre) . '</strong>,</p>
            <p>Recibimos una solicitud para restablecer la contraseña de tu cuenta en DeskFlow.</p>
            <p style="text-align: center; margin: 30px 0;">
                <a href="' . htmlspecialchars($linkRecuperacion) . '" style="background-color: #0d6efd; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold; display: inline-block;">
                    Restablecer Contraseña
                </a>
            </p>
            <p style="font-size: 0.85rem; color: #64748b;">Este enlace expirará en 1 hora. Si vos no solicitaste este cambio, podés ignorar este mensaje con tranquilidad.</p>
            <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 20px 0;">
            <p style="font-size: 0.75rem; color: #94a3b8; text-align: center;">DeskFlow - Sistema de Help Desk</p>
        </div>';

        $data = [
            'sender' => [
                'name'  => $fromName,
                'email' => $fromEmail
            ],
            'to' => [
                [
                    'email' => $destinatarioEmail,
                    'name'  => $destinatarioNombre
                ]
            ],
            'subject'     => 'Restablecer contraseña - DeskFlow',
            'htmlContent' => $htmlContent
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'api-key: ' . $apiKey,
            'Content-Type: application/json',
            'Accept: application/json'
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            return [
                'exito'   => false,
                'mensaje' => 'Error de conexión con el servicio de correo: ' . $curlError
            ];
        }

        if ($httpCode >= 200 && $httpCode < 300) {
            return [
                'exito'   => true,
                'mensaje' => 'Correo enviado exitosamente.'
            ];
        }

        return [
            'exito'   => false,
            'mensaje' => 'Brevo respondió con código ' . $httpCode . ': ' . $response
        ];
    }
}
