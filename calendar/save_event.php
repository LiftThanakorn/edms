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

    $id = isset($_POST['id']) && !empty($_POST['id']) ? intval($_POST['id']) : null;
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $start_date = $_POST['start_date'] ?? '';
    $end_date = $_POST['end_date'] ?? '';
    $color = $_POST['color'] ?? '#3788d8';
    $created_by = $_SESSION['user_id'];

    // Validation
    if (empty($title) || empty($start_date) || empty($end_date)) {
        throw new Exception('กรุณากรอกข้อมูลให้ครบถ้วน');
    }

    if ($id) {
        $stmt = $pdo->prepare("UPDATE events SET title = ?, description = ?, start_date = ?, end_date = ?, color = ?, created_by = ? WHERE id = ? AND created_by = ?");
        $success = $stmt->execute([$title, $description, $start_date, $end_date, $color, $created_by, $id, $created_by]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO events (title, description, start_date, end_date, color, created_by) VALUES (?, ?, ?, ?, ?, ?)");
        $success = $stmt->execute([$title, $description, $start_date, $end_date, $color, $created_by]);
    }

    if (!$success) {
        throw new Exception('Failed to save event');
    }

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
