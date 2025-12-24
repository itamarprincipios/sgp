<?php
require_once __DIR__ . '/../Core/Database.php';

class Notice {
    private $conn;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }

    public function create($senderId, $recipientId, $title, $message, $severity = 'warning') {
        $stmt = $this->conn->prepare("INSERT INTO official_notices (sender_id, recipient_id, title, message, severity, created_at) VALUES (:sender_id, :recipient_id, :title, :message, :severity, NOW())");
        return $stmt->execute([
            ':sender_id' => $senderId,
            ':recipient_id' => $recipientId,
            ':title' => $title,
            ':message' => $message,
            ':severity' => $severity
        ]);
    }

    public function getUnreadByRecipient($recipientId) {
        $stmt = $this->conn->prepare("
            SELECT n.*, u.name as sender_name 
            FROM official_notices n
            JOIN users u ON n.sender_id = u.id
            WHERE n.recipient_id = :recipient_id 
            AND n.viewed_at IS NULL 
            ORDER BY n.created_at DESC
        ");
        $stmt->execute([':recipient_id' => $recipientId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function markAsViewed($id, $recipientId) {
        $stmt = $this->conn->prepare("UPDATE official_notices SET viewed_at = NOW() WHERE id = :id AND recipient_id = :recipient_id");
        return $stmt->execute([':id' => $id, ':recipient_id' => $recipientId]);
    }
}
