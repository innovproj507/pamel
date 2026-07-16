<?php

namespace Core;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

class Email
{
    /**
     * Returns email config merging DB settings over config.php values.
     * DB keys: email_from_name, email_from_email, email_admin_email,
     *          email_smtp_enabled, email_smtp_host, email_smtp_port,
     *          email_smtp_username, email_smtp_password, email_smtp_encryption
     */
    private static function getConfig(): array
    {
        $config = Config::get('email', []);

        try {
            $db = Database::getInstance();
            $rows = $db->fetchAll(
                "SELECT setting_key, setting_value FROM settings WHERE setting_key LIKE 'email_%'"
            );
            foreach ($rows as $row) {
                $key = substr($row['setting_key'], \strlen('email_'));
                $value = $row['setting_value'];
                if ($key === 'smtp_enabled') {
                    $value = $value === '1' || $value === 'true';
                } elseif ($key === 'smtp_port') {
                    $value = (int) $value;
                }
                $config[$key] = $value;
            }
        } catch (\Exception $e) {
            error_log("Email::getConfig DB error: " . $e->getMessage());
        }

        return $config;
    }

    /**
     * Send email. Uses PHPMailer+SMTP when smtp_enabled, falls back to mail().
     * $attachment is an absolute file path (optional).
     */
    public static function send(string $to, string $subject, string $body, bool $isHtml = true, ?string $attachment = null): bool
    {
        $config = self::getConfig();

        if (!empty($config['smtp_enabled'])) {
            return self::sendViaSMTP($to, $subject, $body, $isHtml, $attachment, $config);
        }

        return self::sendViaMail($to, $subject, $body, $isHtml, $attachment, $config);
    }

    private static function sendViaSMTP(string $to, string $subject, string $body, bool $isHtml, ?string $attachment, array $config): bool
    {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = $config['smtp_host'] ?? 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = $config['smtp_username'] ?? '';
            $mail->Password   = $config['smtp_password'] ?? '';
            $mail->Port       = (int) ($config['smtp_port'] ?? 587);

            $encryption = strtolower($config['smtp_encryption'] ?? 'tls');
            if ($encryption === 'ssl') {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            } else {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            }

            // Log SMTP conversation to error_log for debugging
            $mail->SMTPDebug  = 2;
            $mail->Debugoutput = function(string $str, int $level) {
                error_log("PHPMailer [$level]: " . trim($str));
            };

            $mail->setFrom($config['from_email'] ?? '', $config['from_name'] ?? '');
            $mail->addAddress($to);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = $subject;
            $mail->isHTML($isHtml);
            $mail->Body = $body;
            if (!$isHtml) {
                $mail->AltBody = $body;
            }

            if ($attachment && file_exists($attachment)) {
                $mail->addAttachment($attachment);
            }

            $mail->send();
            error_log("SMTP OK: email sent to $to");
            return true;
        } catch (PHPMailerException $e) {
            error_log("SMTP FAIL to $to: " . $mail->ErrorInfo);
            return false;
        }
    }

    private static function sendViaMail(string $to, string $subject, string $body, bool $isHtml, ?string $attachment, array $config): bool
    {
        $boundary = md5(time());

        $headers   = [];
        $headers[] = 'From: ' . ($config['from_name'] ?? '') . ' <' . ($config['from_email'] ?? '') . '>';
        $headers[] = 'Reply-To: ' . ($config['from_email'] ?? '');
        $headers[] = 'X-Mailer: PHP/' . phpversion();
        $headers[] = 'MIME-Version: 1.0';

        if ($attachment && file_exists($attachment)) {
            $headers[] = 'Content-Type: multipart/mixed; boundary="' . $boundary . '"';

            $message  = "--{$boundary}\r\n";
            $message .= ($isHtml ? "Content-Type: text/html; charset=utf-8\r\n" : "Content-Type: text/plain; charset=utf-8\r\n");
            $message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
            $message .= $body . "\r\n\r\n";

            $filename       = basename($attachment);
            $encodedContent = chunk_split(base64_encode(file_get_contents($attachment)));
            $message .= "--{$boundary}\r\n";
            $message .= "Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document; name=\"{$filename}\"\r\n";
            $message .= "Content-Transfer-Encoding: base64\r\n";
            $message .= "Content-Disposition: attachment; filename=\"{$filename}\"\r\n\r\n";
            $message .= $encodedContent . "\r\n";
            $message .= "--{$boundary}--";

            $body = $message;
        } else {
            if ($isHtml) {
                $headers[] = 'Content-type: text/html; charset=utf-8';
            }
        }

        try {
            $result = mail($to, $subject, $body, implode("\r\n", $headers));
            if ($result) {
                error_log("Email sent via mail() to: $to");
            } else {
                error_log("Failed to send email via mail() to: $to");
            }
            return $result;
        } catch (\Exception $e) {
            error_log("Email mail() error: " . $e->getMessage());
            return false;
        }
    }

    // -------------------------------------------------------------------------
    // Notification helpers (to admin)
    // -------------------------------------------------------------------------

    public static function sendAdmissionNotification(array $data): bool
    {
        $config  = self::getConfig();
        $name    = $data['full_name'] ?? ($data['given_name'] . ' ' . $data['surname']);
        $subject = 'Nueva Solicitud de Admisión - ' . $name;
        $body    = self::getAdmissionEmailTemplate($data);

        $wordPath = null;
        try {
            $wordPath = \Core\WordGenerator::generateAdmissionDocument($data);
        } catch (\Exception $e) {
            error_log("Failed to generate admission Word document: " . $e->getMessage());
        }

        $result = self::send($config['admin_email'] ?? '', $subject, $body, true, $wordPath);

        if ($wordPath && file_exists($wordPath)) {
            unlink($wordPath);
        }

        return $result;
    }

    public static function sendSurveyNotification(array $surveyData): bool
    {
        $config  = self::getConfig();
        $subject = 'Nueva Encuesta de Satisfacción - ' . $surveyData['course_name'];
        $body    = self::getSurveyEmailTemplate($surveyData);

        $wordPath = null;
        try {
            $wordPath = \Core\WordGenerator::generateSurveyDocument($surveyData);
        } catch (\Exception $e) {
            error_log("Failed to generate Word document for email: " . $e->getMessage());
        }

        $result = self::send($config['admin_email'] ?? '', $subject, $body, true, $wordPath);

        if ($wordPath && file_exists($wordPath)) {
            unlink($wordPath);
        }

        return $result;
    }

    public static function sendContactNotification(array $data): bool
    {
        $config  = self::getConfig();
        $subject = 'Nuevo Mensaje de Contacto - ' . $data['subject'];
        return self::send($config['admin_email'] ?? '', $subject, self::getContactEmailTemplate($data));
    }

    // -------------------------------------------------------------------------
    // Confirmation helpers (to the person who submitted)
    // -------------------------------------------------------------------------

    public static function sendAdmissionConfirmation(array $data): bool
    {
        $name    = $data['full_name'] ?? ($data['given_name'] . ' ' . $data['surname']);
        $subject = 'Solicitud de Admisión Recibida - PAMEL';
        $body    = self::wrapTemplate(
            'Solicitud Recibida',
            '<p style="font-size:15px">Estimado/a <strong>' . htmlspecialchars($name) . '</strong>,</p>
             <p>Hemos recibido su solicitud de admisión para el curso <strong>' . htmlspecialchars($data['course']) . '</strong>.</p>
             <p>Nuestro equipo la revisará y se pondrá en contacto con usted a la brevedad.</p>
             <p style="margin-top:20px">Gracias por su interés en <strong>PAMEL</strong>.</p>',
            'Este es un correo automático, por favor no responda a este mensaje.'
        );
        return self::send($data['email'], $subject, $body);
    }

    public static function sendSurveyConfirmation(array $data): bool
    {
        $name    = $data['first_name'] . ' ' . $data['last_name'];
        $subject = 'Encuesta de Satisfacción Recibida - PAMEL';
        $body    = self::wrapTemplate(
            'Gracias por su Feedback',
            '<p style="font-size:15px">Estimado/a <strong>' . htmlspecialchars($name) . '</strong>,</p>
             <p>Hemos recibido su encuesta de satisfacción del curso <strong>' . htmlspecialchars($data['course_name']) . '</strong>.</p>
             <p>Su opinión es muy valiosa para seguir mejorando nuestros servicios.</p>
             <p style="margin-top:20px">Gracias por confiar en <strong>PAMEL</strong>.</p>',
            'Este es un correo automático, por favor no responda a este mensaje.'
        );
        return self::send($data['email'], $subject, $body);
    }

    public static function sendContactConfirmation(array $data): bool
    {
        $subject = 'Mensaje Recibido - PAMEL';
        $body    = self::wrapTemplate(
            'Mensaje Recibido',
            '<p style="font-size:15px">Estimado/a <strong>' . htmlspecialchars($data['name']) . '</strong>,</p>
             <p>Hemos recibido su mensaje sobre <strong>"' . htmlspecialchars($data['subject']) . '"</strong>.</p>
             <p>Nuestro equipo se pondrá en contacto con usted a la brevedad.</p>
             <p style="margin-top:20px">Gracias por contactar a <strong>PAMEL</strong>.</p>',
            'Este es un correo automático, por favor no responda a este mensaje.'
        );
        return self::send($data['email'], $subject, $body);
    }

    // -------------------------------------------------------------------------
    // Email templates
    // -------------------------------------------------------------------------

    private static function getAdmissionEmailTemplate(array $data): string
    {
        $fields = [
            'Nombre completo'     => $data['given_name'] . ' ' . $data['surname'],
            'Pasaporte / Cédula'  => $data['passport_id'],
            'Nacionalidad'        => $data['nationality'],
            'Fecha de nacimiento' => $data['date_of_birth'],
            'País de nacimiento'  => $data['country_of_birth'],
            'Email'               => $data['email'],
            'Teléfono'            => $data['phone'],
            'Dirección'           => $data['address'],
            'Curso solicitado'    => $data['course'],
        ];
        if (!empty($data['capacity'])) {
            $fields['Capacidad / Cargo'] = $data['capacity'];
        }

        $rows = '';
        foreach ($fields as $label => $value) {
            $rows .= '<div class="field"><div class="label">' . $label . ':</div>'
                   . '<div class="value">' . htmlspecialchars((string) $value) . '</div></div>';
        }

        return self::wrapTemplate('Nueva Solicitud de Admisión', $rows,
            'Para ver los archivos adjuntos (cédula / certificado de salud), ingrese al panel de administración.');
    }

    private static function getContactEmailTemplate(array $data): string
    {
        $fields = [
            'Nombre'   => $data['name'],
            'Email'    => $data['email'],
            'Teléfono' => $data['phone'] ?? '',
            'Asunto'   => $data['subject'],
            'Mensaje'  => $data['message'],
        ];

        $rows = '';
        foreach ($fields as $label => $value) {
            if ($value === '') {
                continue;
            }
            $rows .= '<div class="field"><div class="label">' . $label . ':</div>'
                   . '<div class="value">' . nl2br(htmlspecialchars((string) $value)) . '</div></div>';
        }

        return self::wrapTemplate('Nuevo Mensaje de Contacto', $rows,
            'Para responder, ingrese al panel de administración en Mensajes.');
    }

    private static function getSurveyEmailTemplate(array $data): string
    {
        $ratingClass = function (string $r): string {
            return 'rating-' . strtolower(substr($r, 0, 1));
        };

        $sections = [
            ['label' => 'Atención del Personal',          'rating_key' => 'staff_attention_rating',         'comment_key' => 'staff_attention_comments'],
            ['label' => 'Calidad del Entrenamiento',      'rating_key' => 'training_quality_rating',        'comment_key' => 'training_quality_comments'],
            ['label' => 'Desempeño del Instructor',       'rating_key' => 'instructor_performance_rating',  'comment_key' => 'instructor_performance_comments'],
            ['label' => 'Infraestructura y Equipos',      'rating_key' => 'infrastructure_rating',          'comment_key' => 'infrastructure_comments'],
        ];

        $rows  = '<div class="field"><div class="label">Nombre:</div><div class="value">'
               . htmlspecialchars($data['first_name'] . ' ' . $data['last_name']) . '</div></div>';
        $rows .= '<div class="field"><div class="label">Email:</div><div class="value">'
               . htmlspecialchars($data['email']) . '</div></div>';
        $rows .= '<div class="field"><div class="label">Curso:</div><div class="value">'
               . htmlspecialchars($data['course_name']) . '</div></div>';
        $rows .= '<div class="field"><div class="label">Fecha:</div><div class="value">'
               . htmlspecialchars($data['survey_date']) . '</div></div>';
        $rows .= '<h2 style="color:#0066cc;margin-top:20px">Calificaciones</h2>';

        foreach ($sections as $s) {
            $rating  = htmlspecialchars($data[$s['rating_key']] ?? '');
            $comment = htmlspecialchars($data[$s['comment_key']] ?? '');
            $cls     = $ratingClass($data[$s['rating_key']] ?? 'c');
            $rows   .= '<div class="field"><div class="label">' . $s['label'] . ':</div>'
                     . '<div class="value"><span class="rating ' . $cls . '">' . $rating . '</span></div>';
            if ($comment) {
                $rows .= '<div class="value" style="margin-top:8px;font-style:italic">"' . $comment . '"</div>';
            }
            $rows .= '</div>';
        }

        return self::wrapTemplate('Nueva Encuesta de Satisfacción', $rows,
            'Para ver más detalles ingrese al panel de administración.');
    }

    private static function wrapTemplate(string $heading, string $rows, string $footer): string
    {
        return '<!DOCTYPE html><html><head><meta charset="UTF-8"><style>
            body{font-family:Arial,sans-serif;line-height:1.6;color:#333}
            .container{max-width:600px;margin:0 auto;padding:20px}
            .header{background-color:#0066cc;color:white;padding:20px;text-align:center}
            .content{background-color:#f9f9f9;padding:20px;margin-top:20px}
            .field{margin-bottom:15px}
            .label{font-weight:bold;color:#0066cc}
            .value{margin-top:4px}
            .rating{display:inline-block;padding:4px 10px;border-radius:4px;font-weight:bold}
            .rating-a{background:#4CAF50;color:white}
            .rating-b{background:#FF9800;color:white}
            .rating-c{background:#f44336;color:white}
            .footer{text-align:center;margin-top:20px;color:#666;font-size:12px}
        </style></head><body>
        <div class="container">
            <div class="header"><h1>' . $heading . '</h1></div>
            <div class="content">' . $rows . '</div>
            <div class="footer"><p>' . $footer . '</p></div>
        </div></body></html>';
    }
}
