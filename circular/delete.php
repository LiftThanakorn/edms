<?php
session_start();

// ตรวจสอบการเข้าสู่ระบบ
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

require_once '../config.php';

// ตรวจสอบว่ามี ID ที่ส่งมาหรือไม่
if (!isset($_GET['id'])) {
    $_SESSION['message'] = "ไม่พบรหัสหนังสือที่ต้องการลบ";
    $_SESSION['message_type'] = "danger";
    header("Location: index.php");
    exit();
}

try {
    // ดึงข้อมูลหนังสือเวียนเพื่อตรวจสอบไฟล์แนบ
    $stmt = $pdo->prepare("SELECT * FROM edms_circular_documents WHERE document_id = ?");
    $stmt->execute([$_GET['id']]);
    $document = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$document) {
        $_SESSION['message'] = "ไม่พบหนังสือที่ต้องการลบ";
        $_SESSION['message_type'] = "danger";
        header("Location: index.php");
        exit();
    }

    // ลบไฟล์แนบถ้ามี
    if ($document['attachment_path']) {
        $file_path = "uploads/" . date('Y', strtotime($document['created_at'])) . "/" . $document['attachment_path'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }

    // ลบข้อมูลจากฐานข้อมูล
    $stmt = $pdo->prepare("DELETE FROM edms_circular_documents WHERE document_id = ?");
    $stmt->execute([$_GET['id']]);

    $_SESSION['message'] = "ลบหนังสือสำเร็จ";
    $_SESSION['message_type'] = "success";

} catch (PDOException $e) {
    $_SESSION['message'] = "เกิดข้อผิดพลาด: " . $e->getMessage();
    $_SESSION['message_type'] = "danger";
}

// กลับไปยังหน้า index
header("Location: index.php");
exit();
?>
