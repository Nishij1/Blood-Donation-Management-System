<?php
require_once '../includes/ContactHandler.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $contact = new ContactHandler();
        
        // Validate required fields
        $required_fields = ['name', 'email', 'message'];
        foreach ($required_fields as $field) {
            if (empty($_POST[$field])) {
                throw new Exception("Please fill in all required fields");
            }
        }

        // Validate email
        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format");
        }

        if ($contact->saveMessage($_POST)) {
            echo json_encode(['status' => 'success', 'message' => 'Message sent successfully!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to send message']);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?> 