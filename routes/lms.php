<?php

/**
 * LMS Routes — Loaded when the 'elearning' subdomain is detected.
 */
return function($router) {
    // LMS Home
    $router->get('/', 'Plugins\Elearning\Controllers\HomeController@index', 'lms.home');
    
    // Auth (redirect to main CMS login or handle here)
    $router->get('/login', 'Controllers\AuthController@showLogin', 'lms.login');
    $router->post('/login', 'Controllers\AuthController@login', 'lms.login.post');
    
    // Dashboard
    $router->get('/dashboard', 'Plugins\Elearning\Controllers\DashboardController@index', 'lms.dashboard');
    
    // Courses
    $router->get('/courses', 'Plugins\Elearning\Controllers\CourseController@index', 'lms.courses');
    $router->get('/courses/:slug', 'Plugins\Elearning\Controllers\CourseController@show', 'lms.courses.show');
    
    // Lessons
    $router->get('/courses/:courseId/lessons/:id', 'Plugins\Elearning\Controllers\LessonController@show', 'lms.lessons.show');
    $router->post('/courses/:courseId/lessons/:id/complete', 'Plugins\Elearning\Controllers\LessonController@complete', 'lms.lessons.complete');
    
    // Quizzes
    $router->get('/courses/:courseId/quizzes/:id', 'Plugins\Elearning\Controllers\QuizController@show', 'lms.quizzes.show');
    $router->post('/courses/:courseId/quizzes/:id/submit', 'Plugins\Elearning\Controllers\QuizController@submit', 'lms.quizzes.submit');
    $router->get('/courses/:courseId/quizzes/:id/results', 'Plugins\Elearning\Controllers\QuizController@results', 'lms.quizzes.results');
};
