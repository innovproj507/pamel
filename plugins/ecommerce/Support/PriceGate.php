<?php

namespace Plugins\Ecommerce\Support;

use Core\Models\QuoteRequest;

class PriceGate
{
    public static function unlock(int $quoteRequestId): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['unlocked_quote_id'] = $quoteRequestId;
    }

    public static function isUnlocked(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $quoteRequestId = $_SESSION['unlocked_quote_id'] ?? null;
        if (!$quoteRequestId) {
            return false;
        }

        $quoteRequest = (new QuoteRequest())->getById($quoteRequestId);
        if (!$quoteRequest || !in_array($quoteRequest['status'], ['quoted', 'completed'], true)) {
            unset($_SESSION['unlocked_quote_id']);
            return false;
        }

        return true;
    }

    public static function currentQuoteRequestId(): ?int
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return isset($_SESSION['unlocked_quote_id']) ? (int)$_SESSION['unlocked_quote_id'] : null;
    }
}
