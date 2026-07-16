<?php
/**
 * Plugin Name: PayPal Payment Gateway
 * Description: Integración de PayPal para procesar pagos en el sistema
 * Version: 1.0.0
 * Author: CMS Team
 */

use Core\PluginManager;

// Register plugin hooks
$pluginManager = PluginManager::getInstance();

// Add settings link in admin
$pluginManager->addAction('admin_menu', function() {
    // This will be handled by the admin routes
});

// Register payment routes
$pluginManager->addAction('init_routes', function($router) {
    // Payment processing routes
    $router->post('/payment/create', 'Plugins\\Paypal\\Controllers\\PaymentController@createPayment');
    $router->get('/payment/success', 'Plugins\\Paypal\\Controllers\\PaymentController@executePayment');
    $router->get('/payment/cancel', 'Plugins\\Paypal\\Controllers\\PaymentController@cancelPayment');
    
    // Admin settings route
    $router->get('/manager/paypal-settings', 'Plugins\\Paypal\\Controllers\\AdminSettingsController@index');
    $router->post('/manager/paypal-settings', 'Plugins\\Paypal\\Controllers\\AdminSettingsController@save');
});

// Add PayPal button to order actions
$pluginManager->addFilter('order_actions', function($actions, $order) {
    if (in_array(strtolower($order['status']), ['pending', 'failed', 'on-hold'])) {
        $actions[] = [
            'type' => 'paypal',
            'label' => 'Pagar con PayPal',
            'icon' => 'fab fa-paypal',
            'class' => 'text-blue-600 hover:text-blue-700',
            'order_id' => $order['id']
        ];
    }
    return $actions;
}, 10, 2);
