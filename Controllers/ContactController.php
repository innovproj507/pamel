<?php

namespace Controllers;

use Core\Controller;
use Core\Email;
use Core\Models\ContactMessage;
use Core\View;

class ContactController extends Controller
{
    private $contactModel;

    public function __construct()
    {
        parent::__construct();
        $this->contactModel = new ContactMessage();
    }

    public function submit()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        try {
            // Validate required fields
            $required = ['name', 'email', 'subject', 'message'];
            foreach ($required as $field) {
                if (empty($_POST[$field])) {
                    throw new \Exception("Field {$field} is required");
                }
            }

            // Validar email
            if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                throw new \Exception('Invalid email address');
            }

            // Sanitize
            $data = [
                'name' => htmlspecialchars(trim($_POST['name'])),
                'email' => filter_var($_POST['email'], FILTER_SANITIZE_EMAIL),
                'phone' => htmlspecialchars(trim($_POST['phone'] ?? '')),
                'subject' => htmlspecialchars(trim($_POST['subject'])),
                'message' => htmlspecialchars(trim($_POST['message']))
            ];

            // Save to DB
            $this->contactModel->create($data);

            Email::sendContactNotification($data);
            Email::sendContactConfirmation($data);

            echo json_encode(['success' => true, 'message' => 'Message sent successfully']);

        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function index()
    {
        // Admin view
        $auth = \Core\Auth::getInstance();
        $auth->requireAdmin();

        $messages = $this->contactModel->getAll();

        $view = new View();
        $view->render('admin/views/contact-messages', [
            'title' => 'Mensajes de Contacto',
            'messages' => $messages
        ], 'admin/views/layout');
    }
}
