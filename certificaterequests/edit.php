<?php
session_start();

// ตรวจสอบการเข้าสู่ระบบและสิทธิ์แอดมิน
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

require_once '../config.php';

// ตรวจสอบว่ามี ID ที่ส่งมาหรือไม่
if (!isset($_GET['id'])) {
    $_SESSION['message'] = "ไม่พบรหัสคำขอที่ต้องการแก้ไข";
    $_SESSION['message_type'] = "danger";
    header("Location: index.php");
    exit();
}

// ดึงข้อมูลคำขอ
try {
    $stmt = $pdo->prepare("SELECT * FROM edms_certificate_requests WHERE request_id = ?");
    $stmt->execute([$_GET['id']]);
    $request = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$request) {
        $_SESSION['message'] = "ไม่พบคำขอที่ต้องการแก้ไข";
        $_SESSION['message_type'] = "danger";
        header("Location: index.php");
        exit();
    }
} catch (PDOException $e) {
    $_SESSION['message'] = "เกิดข้อผิดพลาด: " . $e->getMessage();
    $_SESSION['message_type'] = "danger";
    header("Location: index.php");
    exit();
}

// ดัดการการส่งฟอร์ม
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // จัดการไฟล์แนบใหม่ถ้ามี
        $file_name = $request['attachment_path']; // ใช้ไฟล์เดิมถ้าไม่มีการอัพโหลดไฟล์ใหม่
        if (!empty($_FILES['attachment']['name'])) {
            $file_name = handleFileUpload($_FILES['attachment']);
        }

        // อัพเดทข้อมูลคำขอ (ไม่อัพเดท document_number และ document_year)
        $stmt = $pdo->prepare("
            UPDATE edms_certificate_requests
            SET receiver = ?,
                date_created = ?,
                attachment_path = ?,
                note = ? 
            WHERE request_id = ?
        ");

        $stmt->execute([
            $_POST['receiver'],
            $_POST['date_created'],
            $file_name,
            $_POST['note'],
            $_GET['id']
        ]);

        $_SESSION['message'] = "แก้ไขข้อมูลคำขอสำเร็จ";
        $_SESSION['message_type'] = "success";
        header("Location: index.php");
        exit();

    } catch (Exception $e) {
        $_SESSION['message'] = "เกิดข้อผิดพลาด: " . $e->getMessage();
        $_SESSION['message_type'] = "danger";
    }
}

// ฟังก์ชันสำหรับการอัพโหลดไฟล์
function handleFileUpload($file) {
    if (empty($file['name'])) return null;
    
    $allowed_types = ['pdf'];
    $max_size = 5 * 1024 * 1024; // 5MB
    $upload_dir = "uploads/" . date('Y') . "/";
    
    // ตรวจสอบและสร้างโฟลเดอร์
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    // ตรวจสอบไฟล์
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed_types)) {
        throw new Exception("กรุณาอัพโหลดไฟล์ PDF เท่านั้น");
    }
    
    if ($file['size'] > $max_size) {
        throw new Exception("ไฟล์มีขนาดใหญ่เกิน 5MB");
    }
    
    $filename = 'document_' . uniqid() . '.pdf';
    if (!move_uploaded_file($file['tmp_name'], $upload_dir . $filename)) {
        throw new Exception("เกิดข้อผิดพลาดในการอัพโหลดไฟล์");
    }
    
    return $filename;
}
?>


<!DOCTYPE html>
<html lang="th">

<head>
    <?php require_once '../components/header.php'; ?>
    <title>แก้ไขคำขอหนังสือรับรอง</title>
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
                        <li class="breadcrumb-item"><a href="/edms/certificate_requests/">คำขอหนังสือรับรอง</a></li>
                        <li class="breadcrumb-item active">แก้ไขคำขอหนังสือรับรอง</li>
                    </ol>
                </nav>

                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">แก้ไขคำขอหนังสือรับรอง</h4>
                    </div>
                    <div class="card-body">
                        <?php if (isset($_SESSION['message'])): ?>
                            <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible fade show">
                                <?php echo htmlspecialchars($_SESSION['message']); unset($_SESSION['message']); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form action="" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                            <div class="row g-3">
                                <!-- ฟอร์มด้านซ้าย -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">เลขที่คำขอ</label>
                                        <input type="text" class="form-control" value="<?php echo $request['document_number'] . " / " . $request['document_year'] + 543; ?>" readonly>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">วันที่สร้างคำขอ</label>
                                        <input type="text" name="date_created" class="form-control" id="datepicker" value="<?php echo $request['date_created']; ?>" required>
                                        <div class="invalid-feedback">
                                            กรุณากรอกวันที่สร้างคำขอ
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">หมายเหตุ</label>
                                        <textarea name="note" class="form-control" rows="3" placeholder="กรุณากรอกหมายเหตุ (ถ้ามี)"><?php echo $request['note']; ?></textarea>
                                    </div>
                                </div>

                                <!-- ฟอร์มด้านขวา -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">ผู้รับ</label>
                                        <input type="text" name="receiver" class="form-control" value="<?php echo $request['receiver']; ?>" required>
                                        <div class="invalid-feedback">
                                            กรุณากรอกผู้รับ
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">ไฟล์แนบ (PDF เท่านั้น, ไม่เกิน 5MB)</label>
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
            var today = new Date();
            var day = ("0" + today.getDate()).slice(-2); // เติม 0 ข้างหน้า
            var month = ("0" + (today.getMonth() + 1)).slice(-2); // เดือนเริ่มจาก 0
            var year = today.getFullYear();
            var currentDate = year + '-' + month + '-' + day; // รูปแบบ yyyy-mm-dd

            // ตั้งค่าให้แสดงวันที่ปัจจุบันใน input
            $('#datepicker').val(currentDate);

            // เปิดใช้งาน Datepicker
            $('#datepicker').datepicker({
                format: 'yyyy-mm-dd', // รูปแบบวันที่ที่ต้องการ
                autoclose: true, // ปิด datepicker เมื่อเลือกวันที่
                todayHighlight: true, // เน้นวันที่ปัจจุบัน
                todayBtn: 'linked', // แสดงปุ่ม "วันนี้"
                clearBtn: true, // แสดงปุ่ม "ลบ"
                orientation: 'bottom auto' // กำหนดตำแหน่งแสดง datepicker
            });
        });
    </script>
</body>

</html>
