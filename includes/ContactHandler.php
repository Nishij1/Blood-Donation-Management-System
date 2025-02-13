<?php
require_once 'BaseController.php';

class ContactHandler extends BaseController {
    private $table_name = "contact_messages";

    public function saveMessage($data) {
        try {
            $query = "INSERT INTO " . $this->table_name . 
                    " (name, email, message, created_at) VALUES 
                    (:name, :email, :message, NOW())";
            
            $stmt = $this->db->prepare($query);
            
            // Sanitize inputs
            $name = $this->sanitizeInput($data['name']);
            $email = filter_var($data['email'], FILTER_SANITIZE_EMAIL);
            $message = $this->sanitizeInput($data['message']);
            
            $stmt->bindParam(":name", $name);
            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":message", $message);
            
            if($stmt->execute()) {
                // Send email notification to admin
                $this->sendEmailNotification($data);
                return true;
            }
            return false;
        } catch(PDOException $e) {
            error_log("Error saving contact message: " . $e->getMessage());
            throw new Exception("Failed to send message. Please try again later.");
        }
    }

    private function sendEmailNotification($data) {
        $to = "admin@hms.com";
        $subject = "New Contact Form Submission";
        $message = "Name: " . $data['name'] . "\n";
        $message .= "Email: " . $data['email'] . "\n";
        $message .= "Message: " . $data['message'];
        $headers = "From: " . $data['email'];

        mail($to, $subject, $message, $headers);
    }
}
?> 