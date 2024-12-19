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
    $_SESSION['message'] = "ไม่พบรหัสเอกสารที่ต้องการแก้ไข";
    $_SESSION['message_type'] = "danger";
    header("Location: index.php");
    exit();
}

// ดึงข้อมูลเอกสาร
try {
    $stmt = $pdo->prepare("
        SELECT * FROM edms_internal_in_documents 
        WHERE document_id = ?
    ");
    $stmt->execute([$_GET['id']]);
    $document = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$document) {
        $_SESSION['message'] = "ไม่พบเอกสารที่ต้องการแก้ไข";
        $_SESSION['message_type'] = "danger";
        header("Location: index.php");
        exit();
    }
} catch (PDOException $e) {
    $_SESSION['message'] = "เกิดข้อผิด���ลาด: " . $e->getMessage();
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
            UPDATE edms_internal_in_documents 
            SET document_reference_number = ?,
                title = ?,
                sender = ?,
                receiver = ?,
                date_signed = ?,
                category_id = ?,
                attachment_path = ?,
                note = ?
            WHERE document_id = ?
        ");

        $stmt->execute([
            $_POST['document_reference_number'],
            $_POST['title'],
            $_POST['sender'],
            $_POST['receiver'],
            $_POST['date_signed'],
            $_POST['category_id'],
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

// เพิ่มฟังก์ชันนี้ก่อนส่วนของการจัดการฟอร์ม
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
    <title>แก้ไขหนังสือรับเข้าภายใน</title>
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
                        <li class="breadcrumb-item"><a href="index.php">หนังสือรับเข้าภายใน</a></li>
                        <li class="breadcrumb-item active">แก้ไขหนังสือรับเข้าภายใน</li>
                    </ol>
                </nav>

                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">แก้ไขหนังสือรับเข้าภายใน</h4>
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
                                        <label class="form-label">เลขทะเบียนรับ</label>
                                        <input type="text" class="form-control" 
                                               value="<?php echo $document['document_number'] . ' / ' . ($document['document_year'] + 543); ?>" 
                                               readonly>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">เลขที่หนังสือ</label>
                                        <input type="text" name="document_reference_number" class="form-control" value="<?php echo htmlspecialchars($document['document_reference_number']); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">ลงวันที่</label>
                                        <input type="date" name="date_signed" class="form-control" value="<?php echo $document['date_signed']; ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">ชื่อเรื่อง</label>
                                        <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($document['title']); ?>" required>
                                    </div>
                                                                        <div class="mb-3">
                                        <label class="form-label">หายเหตุ</label>
                                        <textarea name="note" class="form-control" rows="3" 
                                                  placeholder="กรอกหมายเหตุ (ถ้ามี)"><?php echo htmlspecialchars($document['note'] ?? ''); ?></textarea>
                                    </div>
                                </div>


                                <!-- ฟอร์มด้านขวา -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">ผู้ส่ง</label>
                                        <input type="text" name="sender" class="form-control" value="<?php echo htmlspecialchars($document['sender']); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">ผู้รับ</label>
                                        <input type="text" name="receiver" class="form-control" value="<?php echo htmlspecialchars($document['receiver']); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">หมวดหมู่งาน</label>
                                        <select name="category_id" class="form-select" required>
                                            <?php foreach ($categories as $category): ?>
                                                <option value="<?php echo $category['category_id']; ?>" 
                                                    <?php echo ($category['category_id'] == $document['category_id']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($category['category_name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
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
