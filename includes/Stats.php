<?php
require_once 'BaseController.php';

class Stats extends BaseController {
    public function getStats() {
        try {
            $stats = [];
            
            // Get patient count
            $query = "SELECT COUNT(*) as count FROM patients";
            $stmt = $this->db->query($query);
            $stats['patients'] = $stmt->fetch()['count'];
            
            // Get doctor count
            $query = "SELECT COUNT(*) as count FROM users WHERE role = 'doctor'";
            $stmt = $this->db->query($query);
            $stats['doctors'] = $stmt->fetch()['count'];
            
            // Get appointment count
            $query = "SELECT COUNT(*) as count FROM appointments";
            $stmt = $this->db->query($query);
            $stats['appointments'] = $stmt->fetch()['count'];
            
            // Get pending appointments
            $query = "SELECT COUNT(*) as count FROM appointments WHERE status = 'pending'";
            $stmt = $this->db->query($query);
            $stats['pending_appointments'] = $stmt->fetch()['count'];
            
            // Calculate revenue (example)
            $query = "SELECT SUM(amount) as total FROM payments WHERE MONTH(payment_date) = MONTH(CURRENT_DATE())";
            $stmt = $this->db->query($query);
            $stats['revenue'] = $stmt->fetch()['total'] ?? 0;
            
            return $stats;
        } catch(PDOException $e) {
            error_log("Error fetching stats: " . $e->getMessage());
            return false;
        }
    }

    public function getRecentActivities($limit = 5) {
        try {
            $activities = [];
            
            // Get recent appointments
            $query = "SELECT a.*, p.name as patient_name, d.name as doctor_name 
                     FROM appointments a 
                     JOIN patients p ON a.patient_id = p.id 
                     JOIN doctors d ON a.doctor_id = d.id 
                     ORDER BY a.created_at DESC LIMIT ?";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute([$limit]);
            
            while ($row = $stmt->fetch()) {
                $activities[] = [
                    'type' => 'primary',
                    'icon' => 'calendar-check',
                    'title' => 'New Appointment',
                    'description' => "Appointment scheduled for {$row['patient_name']} with Dr. {$row['doctor_name']}",
                    'time' => $this->timeAgo($row['created_at'])
                ];
            }
            
            return $activities;
        } catch(PDOException $e) {
            error_log("Error fetching activities: " . $e->getMessage());
            return [];
        }
    }

    private function timeAgo($datetime) {
        $time = strtotime($datetime);
        $now = time();
        $diff = $now - $time;
        
        if ($diff < 60) {
            return "Just now";
        } elseif ($diff < 3600) {
            $mins = floor($diff / 60);
            return $mins . " minute" . ($mins > 1 ? "s" : "") . " ago";
        } elseif ($diff < 86400) {
            $hours = floor($diff / 3600);
            return $hours . " hour" . ($hours > 1 ? "s" : "") . " ago";
        } else {
            $days = floor($diff / 86400);
            return $days . " day" . ($days > 1 ? "s" : "") . " ago";
        }
    }
}
?> 