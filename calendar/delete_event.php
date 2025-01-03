<?php
session_start();
require_once '../config.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Unauthorized access');
    }

    $data = json_decode(file_get_contents('php://input'), true);
    if (!isset($data['id'])) {
        throw new Exception('Missing event ID');
    }

    $eventId = intval($data['id']);
    $userId = $_SESSION['user_id'];

    // ลบข้อมูลเฉพาะของผู้ใช้คนนั้น
    $stmt = $pdo->prepare("DELETE FROM events WHERE id = ? AND created_by = ?");
    $success = $stmt->execute([$eventId, $userId]);

    if (!$success) {
        throw new Exception('Failed to delete event');
    }

    if ($stmt->rowCount() === 0) {
        throw new Exception('Event not found or unauthorized');
    }

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
