<?php
/**
 * Ecommerce Plugin
 * Provides ecommerce functionality including products, cart, and orders
 */

use Core\PluginManager;
use Core\Config;

// Get plugin manager instance
$pm = PluginManager::getInstance();
$router = $GLOBALS['app']->getRouter();

// Register ecommerce routes
$router->get('/shop', 'Plugins\Ecommerce\Controllers\ProductController@index', 'shop.index');
$router->get('/shop/:slug', 'Plugins\Ecommerce\Controllers\ProductController@show', 'shop.product');
$router->get('/cart', 'Plugins\Ecommerce\Controllers\CartController@index', 'cart.index');
$router->post('/cart/add', 'Plugins\Ecommerce\Controllers\CartController@add', 'cart.add');
$router->post('/cart/remove', 'Plugins\Ecommerce\Controllers\CartController@remove', 'cart.remove');
$router->post('/cart/update', 'Plugins\Ecommerce\Controllers\CartController@update', 'cart.update');
$router->get('/checkout', 'Plugins\Ecommerce\Controllers\OrderController@checkout', 'checkout');
$router->post('/checkout', 'Plugins\Ecommerce\Controllers\OrderController@process', 'checkout.process');

// Add hooks
$pm->addAction('header_meta', function() {
    echo '<meta name="ecommerce" content="enabled">';
});

// Filter product prices
$pm->addFilter('product_price', function($price) {
    return number_format($price, 2);
});
