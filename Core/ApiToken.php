<?php

namespace Core;

class ApiToken
{
    private static function secret(): string
    {
        return Config::get('app.secret', 'pamel-api-secret-change-me');
    }

    /**
     * Generate a signed, stateless API token for a user.
     *
     * @param int $userId
     * @param int $ttlDays
     * @return string
     */
    public static function generate(int $userId, int $ttlDays = 30): string
    {
        $payload = base64_encode(json_encode([
            'uid' => $userId,
            'exp' => time() + ($ttlDays * 86400),
            'iat' => time(),
        ]));
        $sig = hash_hmac('sha256', $payload, self::secret());
        return $payload . '.' . $sig;
    }

    /**
     * Validate a token and return the user ID, or false if invalid/expired.
     *
     * @param string $token
     * @return int|false
     */
    public static function validate(string $token)
    {
        $parts = explode('.', $token, 2);
        if (count($parts) !== 2) return false;

        [$payload, $sig] = $parts;
        $expected = hash_hmac('sha256', $payload, self::secret());

        if (!hash_equals($expected, $sig)) return false;

        $data = json_decode(base64_decode($payload), true);
        if (!$data || !isset($data['uid'], $data['exp'])) return false;
        if ($data['exp'] < time()) return false;

        return (int) $data['uid'];
    }

    /**
     * Extract and validate the Bearer token from the current request.
     *
     * @return int|false  User ID on success, false otherwise
     */
    public static function fromRequest()
    {
        // 1. Header estándar (puede ser stripeado por nginx en algunas configs)
        $header = $_SERVER['HTTP_AUTHORIZATION']
               ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION']
               ?? '';

        // 2. Fallback: header personalizado que nginx no stripea
        if (empty($header)) {
            $custom = $_SERVER['HTTP_X_CMS_TOKEN'] ?? '';
            if ($custom !== '') {
                return self::validate($custom);
            }
        }

        if (strpos($header, 'Bearer ') !== 0) return false;
        return self::validate(substr($header, 7));
    }
}
