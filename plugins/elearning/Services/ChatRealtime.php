<?php

namespace Plugins\Elearning\Services;

use Core\Env;

/**
 * Chat en vivo, lado pamel: el mismo ticket y el mismo aviso que usa el portal
 * (elearning/src/Core/ChatRealtime.php). El servicio WebSocket vive en el portal
 * (bin/chat-ws.php); aquí solo se firma el ticket del administrador y se le
 * avisa de cada mensaje nuevo, para que el manager también funcione en vivo.
 *
 * Sin CHAT_WS_SECRET (mínimo 32 caracteres) en el .env, el chat en vivo queda
 * apagado y el manager sondea.
 */
class ChatRealtime
{
    private static function secret(): string
    {
        return Env::get('CHAT_WS_SECRET', '');
    }

    public static function enabled(): bool
    {
        return strlen(self::secret()) >= 32 && self::wsUrl() !== '';
    }

    public static function wsUrl(): string
    {
        return Env::get('CHAT_WS_URL', '');
    }

    private static function b64(string $s): string
    {
        return rtrim(strtr(base64_encode($s), '+/', '-_'), '=');
    }

    /** Ticket de conexión de corta vida (mismo formato que verifica el servicio). */
    public static function ticket(int $userId, int $ttl = 300): string
    {
        $datos = self::b64(json_encode(['u' => $userId, 'e' => time() + $ttl]));
        $firma = self::b64(hash_hmac('sha256', 'chat-ws|' . $datos, self::secret(), true));
        return $datos . '.' . $firma;
    }

    /**
     * Avisa al servicio de un mensaje nuevo para estos usuarios.
     *
     * @param int[] $destinatarios
     * @return int[]|null ids que estaban conectados; null si el servicio no responde
     */
    public static function avisar(array $destinatarios, int $conversacionId, int $mensajeId, int $remitenteId): ?array
    {
        if (!self::enabled() || !$destinatarios) {
            return null;
        }
        $interno = rtrim(Env::get('CHAT_WS_INTERNAL', 'http://127.0.0.1:8082'), '/');
        $ch = curl_init($interno . '/notify');
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode([
                'users' => array_values(array_unique(array_map('intval', $destinatarios))),
                'cid'   => $conversacionId,
                'id'    => $mensajeId,
                'from'  => $remitenteId,
            ]),
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json', 'X-Chat-Key: ' . hash('sha256', 'interno|' . self::secret())],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT_MS => 400,
            CURLOPT_TIMEOUT_MS     => 1500,
        ]);
        $r = curl_exec($ch);
        $codigo = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($r === false || $codigo !== 200) {
            return null;
        }
        $d = json_decode((string) $r, true);
        return is_array($d['online'] ?? null) ? array_map('intval', $d['online']) : null;
    }
}
