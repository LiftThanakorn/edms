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
    $_SESSION['message'] = "ไม่พบรหัสหนังสือที่ต้องการแก้ไข";
    $_SESSION['message_type'] = "danger";
    header("Location: index.php");
    exit();
}

// ดึงข้อมูลหนังสือเวียน
try {
    $stmt = $pdo->prepare("
        SELECT * FROM edms_circular_documents 
        WHERE document_id = ?
    ");
    $stmt->execute([$_GET['id']]);
    $document = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$document) {
        $_SESSION['message'] = "ไม่พบหนังสือที่ต้องการแก้ไข";
        $_SESSION['message_type'] = "danger";
        header("Location: index.php");
        exit();
    }
} catch (PDOException $e) {
    $_SESSION['message'] = "เกิ���ข้อผิดพลาด: " . $e->getMessage();
    $_SESSION['message_type'] = "danger";
    header("Location: index.php");
    exit();
}

// ดัดการการส่งฟอร์ม
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // จัดการไฟล์แนบใหม่ถ้ามี
        $file_name = $document['attachment_path'];
        if (!empty($_FILES['attachment']['name'])) {
            $file_name = handleFileUpload($_FILES['attachment']);
        }

        // อัพเดทข้อมูล
        $stmt = $pdo->prepare("
            UPDATE edms_circular_documents 
            SET document_number = ?,
                document_year = ?,
                title = ?,
                sender = ?,
                receiver = ?,
                date_sent = ?,
                attachment_path = ?,
                note = ?
            WHERE document_id = ?
        ");

        $stmt->execute([
            $_POST['document_number'],
            date('Y', strtotime($_POST['date_sent'])), // แปลงปีเป็นปีปัจจุบัน
            $_POST['title'],
            $_POST['sender'],
            $_POST['receiver'],
            $_POST['date_sent'],
            $file_name,
            $_POST['note'],
            $_GET['id']
        ]);

        $_SESSION['message'] = "แก้ไขข้อมูลสำเร็จ";
        $_SESSION['message_type'] = "success";
        header("Location: index.php");
        exit();

    } catch (Exception $e) {
        $_SESSION['message'] = "เกิดข้อผิดพลาด: " . $e->getMessage();
        $_SESSION['message_type'] = "danger";
    }
}

// ดึงรายการหมวดหมู่
$categories = $pdo->query("SELECT * FROM edms_work_categories")->fetchAll(PDO::FETCH_ASSOC);

// ฟังก์ชันสำหรับจัดการไฟล์แนบ
function handleFileUpload($file) {
    if(empty($file['name'])) return null;
    
    $allowed_types = ['pdf'];
    $max_size = 5 * 1024 * 1024; // 5MB
    $upload_dir = "uploads/" . date('Y') . "/";
    
    // ตรวจสอบและสร้างโฟลเดอร์
    if(!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    // ตรวจสอบไฟล์
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if(!in_array($ext, $allowed_types)) {
        throw new Exception("กรุณาอัพโหลดไฟล์ PDF เท่านั้น");
    }
    
    if($file['size'] > $max_size) {
        throw new Exception("ไฟล์มีขนาดใหญ่เกิน 5MB");
    }
    
    $filename = 'document_' . uniqid() . '.pdf';
    if(!move_uploaded_file($file['tmp_name'], $upload_dir . $filename)) {
        throw new Exception("เกิดข้อผิดพลาดในการอัพโหลดไฟล์");
    }
    
    return $filename;
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <?php require_once '../components/header.php'; ?>
    <title>แก้ไขหนังสือเวียน</title>
</head>
<body>
    <?php require_once '../components/navbar.php'; ?>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-3">
                <?php require_once '../components/sidebar.php'; ?>
            </div>
            <div class="col-md-9">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/edms/">หน้าหลัก</a></li>
                        <li class="breadcrumb-item"><a href="index.php">ทะเบียนหนังสือเวียน</a></li>
                        <li class="breadcrumb-item active">แก้ไขหนังสือเวียน</li>
                    </ol>
                </nav>

                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">แก้ไขหนังสือเวียน</h4>
                    </div>
                    <div class="card-body">
                        <?php if (isset($_SESSION['message'])): ?>
                            <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible fade show">
                                <?php 
                                    echo htmlspecialchars($_SESSION['message']); 
                                    unset($_SESSION['message']);
                                ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form action="" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                            <div class="row g-3">
                                <!-- ฟอร์มด้านซ้าย -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">เลขที่หนังสือ</label>
                                        <input type="text" name="document_number" class="form-control" value="<?php echo htmlspecialchars($document['document_number']); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">ชื่อเรื่อง</label>
                                        <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($document['title']); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">ผู้ส่ง</label>
                                        <input type="text" name="sender" class="form-control" value="<?php echo htmlspecialchars($document['sender']); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">ผู้รับ</label>
                                        <input type="text" name="receiver" class="form-control" value="<?php echo htmlspecialchars($document['receiver']); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">หมายเหตุ</label>
                                        <textarea name="note" class="form-control" rows="3" placeholder="กรอกหมายเหตุ (ถ้ามี)"><?php echo htmlspecialchars($document['note'] ?? ''); ?></textarea>
                                    </div>
                                </div>

                                <!-- ฟอร์มด้านขวา -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">วันที่ส่ง</label>
                                        <input type="date" name="date_sent" class="form-control" value="<?php echo $document['date_sent']; ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">ไฟล์แนบ (PDF เท่านั้น, ไม่เกิน 5MB)</label>
                                        <?php if ($document['attachment_path']): ?>
                                            <div class="mb-2">
                                                <a href="uploads/<?php echo date('Y', strtotime($document['created_at'])); ?>/<?php echo $document['attachment_path']; ?>" 
                                                   class="btn btn-sm btn-info" target="_blank">
                                                    <i class="bi bi-file-pdf"></i> ดูไฟล์ปัจจุบัน
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                        <input type="file" name="attachment" class="form-control" accept=".pdf">
                                        <small class="text-muted">อัพโหลดเฉพาะเมื่อต้องการเปลี่ยนไฟล์</small>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save"></i> บันทึกการแก้ไข
                                </button>
                                <a href="index.php" class="btn btn-secondary">
                                    <i class="bi bi-x-circle"></i> ยกเลิก
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // เพิ่ม Form Validation
        (function () {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms)
                .forEach(function (form) {
                    form.addEventListener('submit', function (event) {
                        if (!form.checkValidity()) {
                            event.preventDefault()
                            event.stopPropagation()
                        }
                        form.classList.add('was-validated')
                    }, false)
                })
        })()
    </script>
</body>
</html>
