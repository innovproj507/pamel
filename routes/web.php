<?php
/**
 * Frontend Routes
 * All public-facing routes
 */

use Core\View;
use Core\Database;

return function($router) {
    // Home page
    $router->get('/', function() {
        $db = Database::getInstance();

        $pamelProducts = $db->fetchAll(
            "SELECT p.*, c.name as category_name, c.slug as category_slug
             FROM products p
             LEFT JOIN categories c ON p.category_id = c.id
             WHERE p.status = 'active'
               AND (c.name LIKE '%PAMEL%' OR c.slug LIKE '%pamel%')
             ORDER BY p.created_at DESC
             LIMIT 4"
        );

        $indiaProducts = $db->fetchAll(
            "SELECT p.*, c.name as category_name, c.slug as category_slug
             FROM products p
             LEFT JOIN categories c ON p.category_id = c.id
             WHERE p.status = 'active'
               AND (c.name LIKE '%Latin%' OR c.slug LIKE '%latin%')
             ORDER BY p.created_at DESC
             LIMIT 4"
        );

        $view = new View();
        $view->render('public/views/home', [
            'pamelProducts' => $pamelProducts,
            'indiaProducts' => $indiaProducts,
        ], 'public/views/layout');
    }, 'home');

    // Static pages
    $router->get('/our-company', function() {
        $view = new View();
        $view->render('public/views/our-company', ['title' => 'Our Company'], 'public/views/layout');
    }, 'our-company');

    $router->get('/quality-policy', function() {
        $view = new View();
        $view->render('public/views/quality-policy', ['title' => 'Quality Policy & Objectives'], 'public/views/layout');
    }, 'quality-policy');

    $router->get('/our-certificates', function() {
        $view = new View();
        $view->render('public/views/our-certificates', ['title' => 'Our Certificates'], 'public/views/layout');
    }, 'our-certificates');

    $router->get('/branches', function() {
        $view = new View();
        $view->render('public/views/branches', ['title' => 'Our Branches'], 'public/views/layout');
    }, 'branches');

    $router->get('/gcl-iso', function() {
        $view = new View();
        $view->render('public/views/gcl-iso', ['title' => 'GCL International - ISO 9001:2015'], 'public/views/layout');
    }, 'gcl-iso');

    $router->get('/privacy-policy', function() {
        $view = new View();
        $view->render('public/views/privacy-policy', ['title' => 'Privacy Policy'], 'public/views/layout');
    }, 'privacy-policy');

    $router->get('/cookie-policy', function() {
        $view = new View();
        $view->render('public/views/cookie-policy', ['title' => 'Cookie Policy'], 'public/views/layout');
    }, 'cookie-policy');

    $router->get('/terms-and-conditions', function() {
        $view = new View();
        $view->render('public/views/terms-and-conditions', ['title' => 'Terms & Conditions'], 'public/views/layout');
    }, 'terms-and-conditions');

    $router->get('/payment-methods', function() {
        $view = new View();
        $view->render('public/views/payment-methods', ['title' => 'Payment Methods'], 'public/views/layout');
    }, 'payment-methods');

    $router->get('/services', function() {
        $view = new View();
        $view->render('public/views/services', ['title' => 'Our Services'], 'public/views/layout');
    }, 'services');

    $router->get('/limr', function() {
        $view = new View();
        $view->render('public/views/limr', ['title' => 'LATIN Indo Marine Registry'], 'public/views/layout');
    }, 'limr');

    $router->get('/admission', function() {
        $view = new View();
        $view->render('public/views/admission', ['title' => 'Admission Process'], 'public/views/layout');
    }, 'admission');

    $router->get('/formulario-de-admision', function() {
        // Fetch active courses (products)
        $productModel = new \Plugins\Ecommerce\Models\Product();
        $courses = $productModel->getActive();
        
        $view = new View();
        $view->render('public/views/formulario-de-admision', [
            'title' => 'Formulario de Admisión',
            'courses' => $courses
        ], 'public/views/layout');
    }, 'formulario-de-admision');

    $router->post('/admission/submit', 'Controllers\\AdmissionController@submit', 'admission.submit');

    // Satisfaction Survey
    $router->get('/formulario-de-satisfaccion', 'Controllers\\SatisfactionSurveyController@showForm', 'satisfaction.form');
    $router->post('/satisfaction-survey/submit', 'Controllers\\SatisfactionSurveyController@submit', 'satisfaction.submit');


    $router->get('/contact', function() {
        $view = new View();
        $view->render('public/views/contact', ['title' => 'Contact Us'], 'public/views/layout');
    }, 'contact');
    
    $router->post('/contact/submit', 'Controllers\\ContactController@submit', 'contact.submit');

    // User account
    $router->get('/my-account', 'Controllers\\AccountController@index', 'account.index');
    $router->post('/my-account/payment-methods/add', 'Controllers\\AccountController@addPaymentMethod', 'payment.add');
    $router->post('/my-account/payment-methods/set-default', 'Controllers\\AccountController@setDefaultPaymentMethod', 'payment.set-default');
    $router->post('/my-account/payment-methods/delete', 'Controllers\\AccountController@deletePaymentMethod', 'payment.delete');
    $router->post('/my-account/address/update', 'Controllers\\AccountController@updateAddress', 'account.address.update');
    $router->post('/my-account/update-details', 'Controllers\\AccountController@updateDetails', 'account.update_details');

    // Payment routes
    $router->post('/payment/process', 'Controllers\\PaymentController@process', 'payment.process');
    $router->post('/payment/create', 'Controllers\\PaymentController@createPayment', 'payment.create');
    $router->get('/payment/success', 'Controllers\\PaymentController@executePayment', 'payment.success');
    $router->get('/payment/cancel', 'Controllers\\PaymentController@cancelPayment', 'payment.cancel');

    // Authentication
    $router->get('/login', 'Controllers\\AuthController@showLogin', 'login.show');
    $router->post('/login', 'Controllers\\AuthController@login', 'login.post');
    $router->get('/logout', 'Controllers\\AuthController@logout', 'logout');
    $router->get('/register', 'Controllers\\AuthController@showRegister', 'register.show');
    $router->post('/register', 'Controllers\\AuthController@register', 'register.post');
    $router->get('/cursos/:slug', 'Plugins\Elearning\Controllers\CourseController@show', 'lms.course.show');
    $router->post('/cursos/:id/enroll', 'Plugins\Elearning\Controllers\CourseController@enroll', 'lms.course.enroll');
    $router->get('/cursos/:slug/certificado', 'Plugins\Elearning\Controllers\CourseController@certificate', 'lms.course.certificate');
    
    // Lessons & Quizzes
    $router->get('/cursos/:slug/lecciones/:lesson_slug', 'Plugins\Elearning\Controllers\LessonController@show', 'lms.lesson.show');

    // Dynamic pages
    $router->get('/page/:slug', 'Controllers\\PageController@show', 'page.show');

    // API Routes for E-learning Sync
    $router->get('/api/products', 'Controllers\\ApiController@getProducts', 'api.products');

    // Debug route (remove in production)
    $router->get('/debug-db', function() {
        require __DIR__ . '/public/debug_db.php';
    }, 'debug-db');
};
