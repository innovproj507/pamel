<?php
/**
 * PayPal Configuration
 * 
 * This file loads PayPal credentials from the database settings table
 */

// Helper function to get settings
function getSetting($key, $default = null) {
    static $cache = [];
    
    if (isset($cache[$key])) {
        return $cache[$key];
    }
    
    $db = \Core\Database::getInstance();
    $result = $db->fetchOne("SELECT setting_value FROM settings WHERE setting_key = ?", [$key]);
    $value = $result ? $result['setting_value'] : $default;
    $cache[$key] = $value;
    
    return $value;
}

return [
    'mode' => getSetting('paypal_mode', 'sandbox'), // sandbox or live
    'client_id' => getSetting('paypal_client_id'),
    'client_secret' => getSetting('paypal_client_secret'),
    'merchant_id' => getSetting('paypal_merchant_id'),
    'email' => getSetting('paypal_email'),
    'currency' => 'USD',
    'return_url' => ($_ENV['SITE_URL'] ?? 'http://cms.test') . '/payment/success',
    'cancel_url' => ($_ENV['SITE_URL'] ?? 'http://cms.test') . '/payment/cancel',
];
