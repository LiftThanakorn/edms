<?php
session_start();
require_once '../config.php';

// นำเข้าฟังก์ชัน getNextDocumentNumber
function getNextDocumentNumber($pdo)
{
    $current_year = date('Y');
    
    // ตรวจสอบว่ามีเอกสารในปีปัจจุบันหรือไม่
    $stmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM edms_internal_out_documents 
        WHERE document_year = ?
    ");
    $stmt->execute([$current_year]);
    $documentExists = $stmt->fetchColumn();

    // ถ้าไม่มีเอกสารในปีปัจจุบัน ให้เริ่มที่เลข 1
    if ($documentExists == 0) {
        return 1;
    }

    // ถ้ามีเอกสารแล้ว ให้หาเลขถัดไป
    $stmt = $pdo->prepare("
        SELECT MAX(document_number) + 1
        FROM edms_internal_out_documents 
        WHERE document_year = ?
    ");
    $stmt->execute([$current_year]);
    return $stmt->fetchColumn();
}

// จำลอง session สำหรับการทดสอบ
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1; // สมมติว่าเป็น user_id = 1
}

// ฟังก์ชันสำหรับจำลองการเพิ่มเอกสาร
function insertTestDocument($pdo, $year, $number) {
    $stmt = $pdo->prepare("
        INSERT INTO edms_internal_out_documents (
            document_number, 
            document_year, 
            title, 
            sender, 
            receiver, 
            date_created,
            created_by
        ) VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    
    return $stmt->execute([
        $number,
        $year,
        "เอกสารทดสอบ #$number ปี $year",
        "ผู้ทดสอบระบบ",
        "ผู้รับทดสอบ",
        "$year-01-01",
        $_SESSION['user_id']
    ]);
}

// ฟังก์ชันลบข้อมูลทดสอบ
function cleanupTestData($pdo) {
    $stmt = $pdo->prepare("
        DELETE FROM edms_internal_out_documents 
        WHERE title LIKE 'เอกสารทดสอบ%'
    ");
    return $stmt->execute();
}

// เริ่มการทดสอบ
try {
    $pdo->beginTransaction();
    
    // 1. ล้างข้อมูลทดสอบเก่า
    echo "ลบข้อมูลทดสอบเก่า...<br>";
    cleanupTestData($pdo);
    
    // 2. ทดสอบปี 2023
    echo "<h3>ทดสอบปี 2023:</h3>";
    insertTestDocument($pdo, 2023, 1);
    insertTestDocument($pdo, 2023, 2);
    insertTestDocument($pdo, 2023, 3);
    
    // เช็คเลขล่าสุดของปี 2023
    $nextNum2023 = getNextDocumentNumber($pdo);
    echo "เลขถัดไปของปี 2023: $nextNum2023<br>";
    
    // 3. ทดสอบปี 2024
    echo "<h3>ทดสอบปี 2024:</h3>";
    // จำลองการเปลี่ยนปี (ในสถานการณ์จริงไม่ต้องทำ)
    $currentYear = 2024;
    
    $nextNum2024First = getNextDocumentNumber($pdo);
    echo "เลขแรกของปี 2024: $nextNum2024First (ควรเป็น 1)<br>";
    
    insertTestDocument($pdo, 2024, $nextNum2024First);
    insertTestDocument($pdo, 2024, $nextNum2024First + 1);
    
    $nextNum2024 = getNextDocumentNumber($pdo);
    echo "เลขถัดไปของปี 2024: $nextNum2024<br>";
    
    // แสดงผลการทดสอบ
    $stmt = $pdo->query("
        SELECT document_year, document_number, title 
        FROM edms_internal_out_documents 
        WHERE title LIKE 'เอกสารทดสอบ%'
        ORDER BY document_year, document_number
    ");
    
    echo "<h3>รายการเอกสารทดสอบทั้งหมด:</h3>";
    echo "<table border='1' cellpadding='5'>
            <tr>
                <th>ปี</th>
                <th>เลขที่</th>
                <th>ชื่อเรื่อง</th>
            </tr>";
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>
                <td>{$row['document_year']}</td>
                <td>{$row['document_number']}</td>
                <td>{$row['title']}</td>
            </tr>";
    }
    echo "</table>";
    
    // ยืนยันการทดสอบ
    $pdo->commit();
    echo "<br>การทดสอบเสร็จสมบูรณ์";
    
} catch (Exception $e) {
    $pdo->rollBack();
    echo "เกิดข้อผิดพลาด: " . $e->getMessage();
}
?>