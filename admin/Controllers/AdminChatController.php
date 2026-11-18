<?php

namespace Admin\Controllers;

use Core\Auth;
use Core\Controller;
use Core\Security;
use Plugins\Elearning\Models\Chat;
use Plugins\Elearning\Services\ChatRealtime;

/**
 * Chat del manager: la burbuja con la que administración e instructores ven y
 * responden las conversaciones del alumnado sin salir del panel.
 *
 * Todas las respuestas son JSON. Quién puede ver o escribir qué lo decide
 * Plugins\Elearning\Models\Chat (el mismo modelo que usa el API del portal).
 */
class AdminChatController extends Controller
{
    private Chat $chat;

    public function __construct()
    {
        parent::__construct();
        $this->chat = new Chat();
    }

    /** Usuario con sesión; si no, 401 en JSON. */
    private function usuario(): array
    {
        $u = Auth::getInstance()->user();
        if (!$u) {
            $this->json(['ok' => false, 'error' => 'Sesión caducada.'], 401);
        }
        return $u;
    }

    /** Conversaciones y estado del chat para la burbuja. */
    public function widget(): void
    {
        $u = $this->usuario();
        $this->json([
            'ok'             => true,
            'habilitado'     => $this->chat->puedeAtender((int) $u['id']),
            'conversaciones' => $this->chat->conversaciones((int) $u['id']),
        ]);
    }

    /** Una conversación; con ?after=N solo los mensajes posteriores. */
    public function hilo($id): void
    {
        $u = $this->usuario();
        $c = $this->chat->conversacion((int) $u['id'], (int) $id);
        if (!$c) {
            $this->json(['ok' => false, 'error' => 'Conversación no encontrada.'], 404);
        }
        $mensajes = $this->chat->mensajes((int) $u['id'], (int) $id, (int) ($_GET['after'] ?? 0));
        $this->json(['ok' => true, 'conversacion' => $c, 'mensajes' => $mensajes ?? []]);
    }

    /** Responde en una conversación y avisa en vivo a los demás participantes. */
    public function enviar($id): void
    {
        $u = $this->usuario();
        $token = $_POST['csrf_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
        if (!is_string($token) || $token === '' || !Security::validateCsrfToken($token)) {
            $this->json(['ok' => false, 'error' => 'Tu sesión caducó. Recarga la página.'], 419);
        }

        $r = $this->chat->responder((int) $u['id'], (int) $id, trim((string) ($_POST['body'] ?? '')));
        if (isset($r['error'])) {
            $this->json(['ok' => false, 'error' => $r['error']], (int) ($r['codigo'] ?? 422));
        }

        $ids = array_map(static fn($d) => (int) $d['id'], $this->chat->destinatarios((int) $id, (int) $u['id']));
        ChatRealtime::avisar($ids, (int) $id, (int) $r['id'], (int) $u['id']);

        $this->json(['ok' => true, 'id' => $r['id']]);
    }

    /** Ticket para abrir el WebSocket; {ok:false} si el chat en vivo está apagado. */
    public function ticket(): void
    {
        $u = $this->usuario();
        if (!ChatRealtime::enabled()) {
            $this->json(['ok' => false]);
        }
        $this->json(['ok' => true, 'url' => ChatRealtime::wsUrl(), 't' => ChatRealtime::ticket((int) $u['id'])]);
    }

    /** Mensajes sin leer, para el contador. */
    public function sinLeer(): void
    {
        $u = $this->usuario();
        $this->json(['count' => $this->chat->sinLeer((int) $u['id'])]);
    }
}
