<?php
session_start();
require_once '../config.php';

header('Content-Type: application/json');

try {
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Unauthorized access');
    }

    // เพิ่มการดีบัก
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    $stmt = $pdo->prepare("SELECT * FROM events WHERE created_by = ?");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $pdo->errorInfo()[2]);
    }

    $exec = $stmt->execute([$_SESSION['user_id']]);
    if (!$exec) {
        throw new Exception("Execute failed: " . $stmt->errorInfo()[2]);
    }

    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // ตรวจสอบข้อมูลที่ได้
    if ($events === false) {
        throw new Exception("Fetch failed: " . $stmt->errorInfo()[2]);
    }

    $formattedEvents = array_map(function($event) {
        return [
            'id' => $event['id'],
            'title' => $event['title'],
            'description' => $event['description'],
            'start' => $event['start_date'],
            'end' => $event['end_date'],
            'backgroundColor' => $event['color'],
            'borderColor' => $event['color'],
            'editable' => true,
            'allDay' => false
        ];
    }, $events);

    echo json_encode($formattedEvents);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
}
