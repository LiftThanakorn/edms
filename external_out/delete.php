<?php
session_start();

// ตรวจสอบการล็อกอิน
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

require_once '../config.php';

// ตรวจสอบว่ามี ID ที่ส่งมาหรือไม่
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['message'] = "ไม่พบรหัสเอกสารที่ต้องการลบ";
    $_SESSION['message_type'] = "danger";
    header("Location: index.php");
    exit();
}

try {
    // ดึงข้อมูลเอกสารเพื่อตรวจสอบไฟล์แนบ
    $stmt = $pdo->prepare("SELECT * FROM edms_external_out_documents WHERE document_id = ?");
    $stmt->execute([$_GET['id']]);
    $document = $stmt->fetch();

    if (!$document) {
        throw new Exception("ไม่พบเอกสารที่ต้องการลบ");
    }

    // ลบไฟล์แนบ (ถ้ามี)
    if ($document['attachment_path']) {
        $file_path = "uploads/external_out/" . date('Y') . "/" . $document['attachment_path'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }

    // ลบข้อมูลจากฐานข้อมูล
    $stmt = $pdo->prepare("DELETE FROM edms_external_out_documents WHERE document_id = ?");
    $stmt->execute([$_GET['id']]);

    $_SESSION['message'] = "ลบเอกสารเรียบร้อยแล้ว";
    $_SESSION['message_type'] = "success";

} catch (Exception $e) {
    $_SESSION['message'] = "เกิดข้อผิดพลาด: " . $e->getMessage();
    $_SESSION['message_type'] = "danger";
}

header("Location: index.php");
exit();
