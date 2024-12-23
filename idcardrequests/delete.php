<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

require_once '../config.php';

try {
    $request_id = $_GET['id'] ?? null;
    if (!$request_id) {
        throw new Exception("ไม่พบรหัสคำขอ");
    }

    // ดึงข้อมูลไฟล์แนบก่อนลบ
    $stmt = $pdo->prepare("SELECT attachment_path, created_at FROM edms_id_card_requests WHERE request_id = ?");
    $stmt->execute([$request_id]);
    $request = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($request && $request['attachment_path']) {
        $file_path = "uploads/" . date('Y', strtotime($request['created_at'])) . "/" . $request['attachment_path'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }

    // ลบข้อมูลจากฐานข้อมูล
    $stmt = $pdo->prepare("DELETE FROM edms_id_card_requests WHERE request_id = ?");
    $stmt->execute([$request_id]);

    $_SESSION['message'] = "ลบข้อมูลสำเร็จ";
    $_SESSION['message_type'] = "success";
} catch (Exception $e) {
    $_SESSION['message'] = "เกิดข้อผิดพลาด: " . $e->getMessage();
    $_SESSION['message_type'] = "danger";
}

header("Location: index.php");
exit();
?>