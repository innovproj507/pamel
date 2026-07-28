<?php
/**
 * Admin Routes
 * All admin panel routes
 */

return function($router) {
    // Dashboard
    $router->get('/manager', 'Admin\Controllers\DashboardController@index', 'admin.dashboard');
    $router->get('/manager/', 'Admin\Controllers\DashboardController@index', 'admin.dashboard.slash');

    // Authentication
    $router->get('/manager/login', 'Admin\Controllers\AuthController@showLogin', 'admin.login');
    $router->post('/manager/login', 'Admin\Controllers\AuthController@login', 'admin.login.post');
    $router->get('/manager/logout', 'Admin\Controllers\AuthController@logout', 'admin.logout');

    // Plugins
    $router->get('/manager/plugins', 'Admin\Controllers\PluginController@index', 'admin.plugins');

    // Products
    $router->get('/manager/products', 'Plugins\Ecommerce\Controllers\AdminProductController@index', 'admin.products');
    $router->get('/manager/products/create', 'Plugins\Ecommerce\Controllers\AdminProductController@create', 'admin.products.create');
    $router->post('/manager/products/store', 'Plugins\Ecommerce\Controllers\AdminProductController@store', 'admin.products.store');
    $router->get('/manager/products/:id/edit', 'Plugins\Ecommerce\Controllers\AdminProductController@edit', 'admin.products.edit');
    $router->post('/manager/products/:id/update', 'Plugins\Ecommerce\Controllers\AdminProductController@update', 'admin.products.update');
    $router->post('/manager/products/:id/delete', 'Plugins\Ecommerce\Controllers\AdminProductController@delete', 'admin.products.delete');
    $router->post('/manager/products/sync-courses', 'Plugins\Ecommerce\Controllers\AdminProductController@syncCourses', 'admin.products.sync');

    // Categories
    $router->get('/manager/categories', 'Plugins\Ecommerce\Controllers\AdminCategoryController@index', 'admin.categories');
    $router->get('/manager/categories/create', 'Plugins\Ecommerce\Controllers\AdminCategoryController@create', 'admin.categories.create');
    $router->post('/manager/categories/store', 'Plugins\Ecommerce\Controllers\AdminCategoryController@store', 'admin.categories.store');
    $router->get('/manager/categories/:id/edit', 'Plugins\Ecommerce\Controllers\AdminCategoryController@edit', 'admin.categories.edit');
    $router->post('/manager/categories/:id/update', 'Plugins\Ecommerce\Controllers\AdminCategoryController@update', 'admin.categories.update');
    $router->post('/manager/categories/:id/delete', 'Plugins\Ecommerce\Controllers\AdminCategoryController@delete', 'admin.categories.delete');

    // Orders
    $router->get('/manager/orders', 'Plugins\Ecommerce\Controllers\AdminOrderController@index', 'admin.orders');
    $router->get('/manager/orders/:id', 'Plugins\Ecommerce\Controllers\AdminOrderController@show', 'admin.orders.show');

    // Admission Requests
    $router->get('/manager/admission-requests', 'Admin\Controllers\AdminAdmissionController@index', 'admin.admission.requests');
    $router->get('/manager/admission-requests/:id', 'Admin\Controllers\AdminAdmissionController@show', 'admin.admission.show');
    $router->get('/manager/admission-requests/:id/download-word', 'Admin\Controllers\AdminAdmissionController@downloadWord', 'admin.admission.download.word');
    $router->post('/manager/admission-requests/update-status', 'Admin\Controllers\AdminAdmissionController@updateStatus', 'admin.admission.update.status');

    // Quote Requests
    $router->get('/manager/quote-requests', 'Admin\Controllers\AdminQuoteController@index', 'admin.quote.requests');
    $router->post('/manager/quote-requests/update-status', 'Admin\Controllers\AdminQuoteController@updateStatus', 'admin.quote.update.status');
    $router->get('/manager/quote-requests/:id', 'Admin\Controllers\AdminQuoteController@show', 'admin.quote.show');
    $router->post('/manager/quote-requests/:id/send-quote', 'Admin\Controllers\AdminQuoteController@sendQuote', 'admin.quote.send');

    // Satisfaction Surveys
    $router->get('/manager/satisfaction-surveys', 'Admin\Controllers\AdminSatisfactionController@index', 'admin.satisfaction.index');
    $router->get('/manager/satisfaction-surveys/:id', 'Admin\Controllers\AdminSatisfactionController@show', 'admin.satisfaction.show');
    $router->post('/manager/satisfaction-surveys/:id/update-status', 'Admin\Controllers\AdminSatisfactionController@updateStatus', 'admin.satisfaction.update.status');
    $router->post('/manager/satisfaction-surveys/:id/delete', 'Admin\Controllers\AdminSatisfactionController@delete', 'admin.satisfaction.delete');
    $router->get('/manager/satisfaction-surveys/:id/download-word', 'Admin\Controllers\AdminSatisfactionController@downloadWord', 'admin.satisfaction.download.word');


    // Contact Messages
    $router->get('/manager/contact-messages', 'Controllers\\ContactController@index', 'admin.contact.index');

    // Users
    $router->get('/manager/users', 'Admin\Controllers\AdminUserController@index', 'admin.users');
    $router->get('/manager/users/create', 'Admin\Controllers\AdminUserController@create', 'admin.users.create');
    $router->post('/manager/users/store', 'Admin\Controllers\AdminUserController@store', 'admin.users.store');
    $router->get('/manager/users/:id/edit', 'Admin\Controllers\AdminUserController@edit', 'admin.users.edit');
    $router->post('/manager/users/:id/update', 'Admin\Controllers\AdminUserController@update', 'admin.users.update');
    $router->post('/manager/users/:id/delete', 'Admin\Controllers\AdminUserController@delete', 'admin.users.delete');
    $router->get('/manager/users/:id/toggle-status', 'Admin\Controllers\AdminUserController@toggleStatus', 'admin.users.toggle_status');

    // PayPal Settings
    $router->get('/manager/paypal-settings', 'Plugins\Paypal\Controllers\AdminSettingsController@index', 'admin.paypal.settings');
    $router->post('/manager/paypal-settings', 'Plugins\Paypal\Controllers\AdminSettingsController@save', 'admin.paypal.settings.save');

    // Email Settings
    $router->get('/manager/email-settings', 'Admin\Controllers\AdminEmailSettingsController@index', 'admin.email.settings');
    $router->post('/manager/email-settings/save', 'Admin\Controllers\AdminEmailSettingsController@save', 'admin.email.settings.save');
    $router->post('/manager/email-settings/test', 'Admin\Controllers\AdminEmailSettingsController@test', 'admin.email.settings.test');

    // E-Learning Admin
    $router->get('/manager/lms/courses', 'Plugins\Elearning\Controllers\AdminCourseController@index', 'admin.lms.courses');
    $router->get('/manager/lms/courses/create', 'Plugins\Elearning\Controllers\AdminCourseController@create', 'admin.lms.courses.create');
    $router->post('/manager/lms/courses/store', 'Plugins\Elearning\Controllers\AdminCourseController@store', 'admin.lms.courses.store');
    $router->get('/manager/lms/courses/:id/edit', 'Plugins\Elearning\Controllers\AdminCourseController@edit', 'admin.lms.courses.edit');
    $router->get('/manager/lms/courses/:id/show', 'Plugins\Elearning\Controllers\AdminCourseController@show', 'admin.lms.courses.show');
    $router->post('/manager/lms/courses/:id/update', 'Plugins\Elearning\Controllers\AdminCourseController@update', 'admin.lms.courses.update');
    $router->post('/manager/lms/courses/:id/delete', 'Plugins\Elearning\Controllers\AdminCourseController@delete', 'admin.lms.courses.delete');
    $router->get('/manager/lms/courses/:id/students', 'Plugins\Elearning\Controllers\AdminCourseController@courseStudents', 'admin.lms.courses.students');
    $router->post('/manager/lms/courses/:id/students/unenroll', 'Plugins\Elearning\Controllers\AdminCourseController@unenrollStudent', 'admin.lms.courses.unenroll');

    // Lessons Admin (nested under course)
    $router->get('/manager/lms/courses/:courseId/lessons', 'Plugins\Elearning\Controllers\AdminCourseController@lessons', 'admin.lms.lessons');
    $router->get('/manager/lms/courses/:courseId/lessons/create', 'Plugins\Elearning\Controllers\AdminCourseController@createLesson', 'admin.lms.lessons.create');
    $router->post('/manager/lms/courses/:courseId/lessons/store', 'Plugins\Elearning\Controllers\AdminCourseController@storeLesson', 'admin.lms.lessons.store');
    $router->get('/manager/lms/courses/:courseId/lessons/:id/edit', 'Plugins\Elearning\Controllers\AdminCourseController@editLesson', 'admin.lms.lessons.edit');
    $router->post('/manager/lms/courses/:courseId/lessons/:id/update', 'Plugins\Elearning\Controllers\AdminCourseController@updateLesson', 'admin.lms.lessons.update');
    $router->post('/manager/lms/courses/:courseId/lessons/:id/delete', 'Plugins\Elearning\Controllers\AdminCourseController@deleteLesson', 'admin.lms.lessons.delete');

    $router->get('/manager/lms/quizzes', 'Plugins\Elearning\Controllers\AdminQuizController@index', 'admin.lms.quizzes');
    $router->get('/manager/lms/quizzes/create', 'Plugins\Elearning\Controllers\AdminQuizController@create', 'admin.lms.quizzes.create');
    $router->post('/manager/lms/quizzes/store', 'Plugins\Elearning\Controllers\AdminQuizController@store', 'admin.lms.quizzes.store');
    $router->get('/manager/lms/quizzes/:id/edit', 'Plugins\Elearning\Controllers\AdminQuizController@edit', 'admin.lms.quizzes.edit');
    $router->post('/manager/lms/quizzes/:id/update', 'Plugins\Elearning\Controllers\AdminQuizController@update', 'admin.lms.quizzes.update');
    $router->post('/manager/lms/quizzes/:id/delete', 'Plugins\Elearning\Controllers\AdminQuizController@delete', 'admin.lms.quizzes.delete');

    // Questions Admin (nested under quiz)
    $router->get('/manager/lms/quizzes/:quizId/questions', 'Plugins\Elearning\Controllers\AdminQuizController@questions', 'admin.lms.questions');
    $router->get('/manager/lms/quizzes/:quizId/questions/add', 'Plugins\Elearning\Controllers\AdminQuizController@addQuestion', 'admin.lms.questions.add');
    $router->post('/manager/lms/quizzes/:quizId/questions/store', 'Plugins\Elearning\Controllers\AdminQuizController@storeQuestion', 'admin.lms.questions.store');
    $router->post('/manager/lms/quizzes/:quizId/questions/:id/delete', 'Plugins\Elearning\Controllers\AdminQuizController@deleteQuestion', 'admin.lms.questions.delete');

    // LMS Categories
    $router->get('/manager/lms/categories', 'Plugins\Elearning\Controllers\AdminCategoryController@index', 'admin.lms.categories');
    $router->post('/manager/lms/categories/store', 'Plugins\Elearning\Controllers\AdminCategoryController@store', 'admin.lms.categories.store');
    $router->post('/manager/lms/categories/:id/delete', 'Plugins\Elearning\Controllers\AdminCategoryController@delete', 'admin.lms.categories.delete');

    // LMS Forums
    $router->get('/manager/lms/forums', 'Plugins\Elearning\Controllers\AdminForumController@index', 'admin.lms.forums');
    $router->post('/manager/lms/forums/store', 'Plugins\Elearning\Controllers\AdminForumController@store', 'admin.lms.forums.store');
    $router->post('/manager/lms/forums/:id/toggle', 'Plugins\Elearning\Controllers\AdminForumController@toggle', 'admin.lms.forums.toggle');
    $router->post('/manager/lms/forums/:id/delete', 'Plugins\Elearning\Controllers\AdminForumController@delete', 'admin.lms.forums.delete');
    $router->post('/manager/lms/forums/topics/:id/delete', 'Plugins\Elearning\Controllers\AdminForumController@deleteTopic', 'admin.lms.forums.topics.delete');
    $router->post('/manager/lms/forums/posts/:id/delete', 'Plugins\Elearning\Controllers\AdminForumController@deletePost', 'admin.lms.forums.posts.delete');

    // LMS Students / Enrollments
    $router->get('/manager/lms/students', 'Plugins\Elearning\Controllers\AdminEnrollmentController@index', 'admin.lms.students');
    $router->get('/manager/lms/students/create', 'Plugins\Elearning\Controllers\AdminEnrollmentController@create', 'admin.lms.students.create');
    $router->post('/manager/lms/students/store', 'Plugins\Elearning\Controllers\AdminEnrollmentController@store', 'admin.lms.students.store');
    $router->post('/manager/lms/students/:id/delete', 'Plugins\Elearning\Controllers\AdminEnrollmentController@delete', 'admin.lms.students.delete');
};
