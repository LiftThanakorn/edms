<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

require_once '../config.php';

// ดึงข้อมูลเอกสารที่ต้องการแก้ไข
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("
        SELECT d.*, c.category_name, u.username as created_by_name
        FROM edms_external_out_documents d
        LEFT JOIN edms_work_categories c ON d.category_id = c.category_id
        LEFT JOIN edms_users u ON d.created_by = u.user_id
        WHERE d.document_id = ?
    ");
    $stmt->execute([$_GET['id']]);
    $document = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$document) {
        $_SESSION['message'] = "ไม่พบข้อมูลเอกสารที่ต้องการแก้ไข";
        $_SESSION['message_type'] = "danger";
        header("Location: index.php");
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}

// ดึงรายการหมวดหมู่
$categories = $pdo->query("SELECT * FROM edms_work_categories")->fetchAll(PDO::FETCH_ASSOC);

// ฟังก์ชันจัดการไฟล์แนบ
function handleFileUpload($file, $old_file = null) {
    if(empty($file['name'])) return $old_file;
    
    $allowed_types = ['pdf'];
    $max_size = 5 * 1024 * 1024; // 5MB
    $upload_dir = "uploads/external_out/" . date('Y') . "/";
    
    if(!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
    
    // ลบไฟล์เก่า
    if($old_file && file_exists($upload_dir . $old_file)) {
        unlink($upload_dir . $old_file);
    }
    
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if(!in_array($ext, $allowed_types)) throw new Exception("กรุณาอัพโหลดไฟล์ PDF เท่านั้น");
    if($file['size'] > $max_size) throw new Exception("ไฟล์มีขนาดใหญ่เกิน 5MB");
    
    $filename = 'document_' . uniqid() . '.pdf';
    if(!move_uploaded_file($file['tmp_name'], $upload_dir . $filename)) {
        throw new Exception("เกิดข้อผิดพลาดในการอัพโหลดไฟล์");
    }
    
    return $filename;
}

// จัดการการส่งฟอร์ม
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $file_name = isset($_FILES['attachment']) ? 
            handleFileUpload($_FILES['attachment'], $document['attachment_path']) : 
            $document['attachment_path'];
        
        $stmt = $pdo->prepare("
            UPDATE edms_external_out_documents SET
                title = ?,
                sender = ?,
                receiver = ?,
                date_created = ?,
                attachment_path = ?,
                category_id = ?,
                note = ?
            WHERE document_id = ?
        ");
        
        $stmt->execute([
            $_POST['title'],
            $_POST['sender'],
            $_POST['receiver'],
            $_POST['date_created'],
            $file_name,
            $_POST['category_id'],
            $_POST['note'],
            $_GET['id']
        ]);
        
        $_SESSION['message'] = "แก้ไขหนังสือส่งออกภายนอกสำเร็จ";
        $_SESSION['message_type'] = "success";
        
        header("Location: index.php");
        exit();
        
    } catch (Exception $e) {
        $_SESSION['message'] = "เกิดข้อผิดพลาด: " . $e->getMessage();
        $_SESSION['message_type'] = "danger";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <?php require_once '../components/header.php'; ?>
    <title>แก้ไขหนังสือส่งออกภายนอก</title>
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
                        <li class="breadcrumb-item"><a href="/edms/external_out/">หนังสือส่งออกภายนอก</a></li>
                        <li class="breadcrumb-item active">แก้ไขหนังสือส่งออกภายนอก</li>
                    </ol>
                </nav>

                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">แก้ไขหนังสือส่งออกภายนอก</h4>
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
                                        <label class="form-label">เลขทะเบียนส่ง</label>
                                        <input type="text" class="form-control" 
                                               value="<?php echo $document['document_number'] . ' / ' . ($document['document_year'] + 543); ?>" 
                                               readonly>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">ชื่อเรื่อง</label>
                                        <input type="text" name="title" class="form-control" 
                                               value="<?php echo htmlspecialchars($document['title']); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">จาก</label>
                                        <input type="text" name="sender" class="form-control" 
                                               value="<?php echo htmlspecialchars($document['sender']); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">หมายเหตุ</label>
                                        <textarea name="note" class="form-control" rows="3"><?php echo htmlspecialchars($document['note']); ?></textarea>
                                    </div>
                                </div>

                                <!-- ฟอร์มด้านขวา -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">ถึง</label>
                                        <input type="text" name="receiver" class="form-control" 
                                               value="<?php echo htmlspecialchars($document['receiver']); ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">วันที่สร้างเอกสาร</label>
                                        <input type="text" name="date_created" class="form-control" id="date_created"
                                               value="<?php echo $document['date_created']; ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">หมวดหมู่งาน</label>
                                        <select name="category_id" class="form-select" required>
                                            <option value="">เลือกหมวดหมู่</option>
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
                                                <a href="uploads/external_out/<?php echo date('Y'); ?>/<?php echo $document['attachment_path']; ?>" 
                                                   target="_blank" class="btn btn-sm btn-info">
                                                    <i class="bi bi-file-pdf"></i> ดูไฟล์เดิม
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                        <input type="file" name="attachment" class="form-control" accept=".pdf">
                                        <div class="form-text">รองรับเฉพาะไฟล์ PDF ขนาดไม่เกิน 5MB</div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save"></i> บันทึก
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
        // Form Validation
        (function() {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms)
                .forEach(function(form) {
                    form.addEventListener('submit', function(event) {
                        if (!form.checkValidity()) {
                            event.preventDefault()
                            event.stopPropagation()
                        }
                        form.classList.add('was-validated')
                    }, false)
                })
        })()

        // เรียกใช้ Datepicker
        $(document).ready(function() {
            // ตั้งค่าค่าเริ่มต้นสำหรับวันที่ออกหนังสือ
            var today = new Date();
            var day = ("0" + today.getDate()).slice(-2);
            var month = ("0" + (today.getMonth() + 1)).slice(-2);
            var year = today.getFullYear();
            var currentDate = year + '-' + month + '-' + day;

            // ตั้งค่า Datepicker สำหรับวันที่ออกหนังสือ
            $('#date_created').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                todayHighlight: true,
                todayBtn: 'linked',
                clearBtn: true,
                orientation: 'bottom auto'
            });
        });
    </script>
</body>
</html>
