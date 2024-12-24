<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

require_once '../config.php';

// ดึงรายการหมวดหมู่
$categories = $pdo->query("SELECT * FROM edms_work_categories")->fetchAll(PDO::FETCH_ASSOC);

// ฟังก์ชันสำหรับหาเลขเอกสารถัดไป
function getNextDocumentNumber($pdo) {
    $current_year = date('Y');
    
    $stmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM edms_external_in_documents 
        WHERE document_year = ?
    ");
    $stmt->execute([$current_year]);
    $documentExists = $stmt->fetchColumn();

    if ($documentExists == 0) {
        return 1;
    }

    $stmt = $pdo->prepare("
        SELECT MAX(document_number) + 1
        FROM edms_external_in_documents 
        WHERE document_year = ?
    ");
    $stmt->execute([$current_year]);
    return $stmt->fetchColumn();
}

// ฟังก์ชันตรวจสอบเลขทะเบียนรับซ้ำ
function isDocumentNumberExists($pdo, $document_number, $document_year) {
    $stmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM edms_external_in_documents 
        WHERE document_number = ? 
        AND document_year = ?
    ");
    $stmt->execute([$document_number, $document_year]);
    return $stmt->fetchColumn() > 0;
}

// ฟังก์ชันจัดการไฟล์แนบ
function handleFileUpload($file) {
    if(empty($file['name'])) return null;
    
    $allowed_types = ['pdf'];
    $max_size = 5 * 1024 * 1024; // 5MB
    $upload_dir = "uploads/external_in/" . date('Y') . "/";
    
    if(!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
    
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
        $current_year = date('Y');
        $next_number = getNextDocumentNumber($pdo);
        
        while (isDocumentNumberExists($pdo, $next_number, $current_year)) {
            $next_number++;
        }

        $file_name = isset($_FILES['attachment']) ? handleFileUpload($_FILES['attachment']) : null;
        
        $stmt = $pdo->prepare("
            INSERT INTO edms_external_in_documents (
                document_number, document_year, document_reference_number,
                title, sender, receiver, date_signed, date_received,
                attachment_path, category_id, created_by, note
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $next_number,
            $current_year,
            $_POST['document_reference_number'],
            $_POST['title'],
            $_POST['sender'],
            $_POST['receiver'],
            $_POST['date_signed'],
            $_POST['date_received'],
            $file_name,
            $_POST['category_id'],
            $_SESSION['user_id'],
            $_POST['note']
        ]);
        
        $_SESSION['message'] = "เพิ่มหนังสือรับเข้าภายนอกสำเร็จ";
        $_SESSION['message_type'] = "success";
        
        header("Location: index.php");
        exit();
        
    } catch (Exception $e) {
        $_SESSION['message'] = "เกิดข้อผิดพลาด: " . $e->getMessage();
        $_SESSION['message_type'] = "danger";
    }
}

// ตัวแปรสำหรับแสดงในฟอร์ม
$next_number = getNextDocumentNumber($pdo);
$current_year = date('Y') + 543; // แปลงเป็น พ.ศ.

// เพิ่มการตรวจสอบเลขซ้ำก่อนแสดงในฟอร์ม
while (isDocumentNumberExists($pdo, $next_number, date('Y'))) {
    $next_number++;
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <?php require_once '../components/header.php'; ?>
    <title>สร้างหนังสือรับเข้าภายนอก</title>
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
                        <li class="breadcrumb-item"><a href="/edms/external_in/">หนังสือรับเข้าภายนอก</a></li>
                        <li class="breadcrumb-item active">สร้างหนังสือรับเข้าภายนอก</li>
                    </ol>
                </nav>

                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">สร้างหนังสือรับเข้าภายนอก</h4>
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
                                        <input type="text" class="form-control" value="<?php echo "$next_number / $current_year"; ?>" readonly>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">เลขที่หนังสือ</label>
                                        <input type="text" name="document_reference_number" class="form-control" required>
                                        <div class="invalid-feedback">
                                            กรุณากรอกเลขที่หนังสือ
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">ชื่อเรื่อง</label>
                                        <input type="text" name="title" class="form-control" required>
                                        <div class="invalid-feedback">
                                            กรุณากรอกชื่อเรื่อง
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">จาก</label>
                                        <input type="text" name="sender" class="form-control" required>
                                        <div class="invalid-feedback">
                                            กรุณากรอกชื่อผู้ส่ง
                                        </div>
                                    </div>
                                                              
                                    <div class="mb-3">
                                        <label class="form-label">หมายเหตุ</label>
                                        <textarea name="note" class="form-control" rows="3" 
                                                  placeholder="กรุณากรอกหมายเหตุ (ถ้ามี)"><?php echo isset($_POST['note']) ? htmlspecialchars($_POST['note']) : ''; ?></textarea>
                                    </div>
                                </div>

                                <!-- ฟอร์มด้านขวา -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">ถึง</label>
                                        <input type="text" name="receiver" class="form-control">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">ลงวันที่</label>
                                        <input type="text" name="date_signed" class="form-control" id="date_signed" required>
                                        <div class="invalid-feedback">กรุณาเลือกวันที่ลงเอกสาร</div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">วันที่รับ</label>
                                        <input type="text" name="date_received" class="form-control" id="date_received" 
                                               value="<?php echo date('Y-m-d'); ?>" required>
                                        <div class="invalid-feedback">กรุณาเลือกวันที่รับ</div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">หมวดหมู่งาน</label>
                                        <select name="category_id" class="form-select" required>
                                            <option value="">เลือกหมวดหมู่</option>
                                            <?php foreach ($categories as $category): ?>
                                                <option value="<?php echo $category['category_id']; ?>">
                                                    <?php echo htmlspecialchars($category['category_name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="invalid-feedback">
                                            กรุณาเลือกหมวดหมู่
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
            // ตั้งค่าค่าเริ่มต้นสำหรับวันที่รับ
            var today = new Date();
            var day = ("0" + today.getDate()).slice(-2);
            var month = ("0" + (today.getMonth() + 1)).slice(-2);
            var year = today.getFullYear();
            var currentDate = year + '-' + month + '-' + day;

            // ตั้งค่า Datepicker สำหรับวันที่ลงเอกสาร
            $('#date_signed').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                todayHighlight: true,
                todayBtn: 'linked',
                clearBtn: true,
                orientation: 'bottom auto'
            });

            // ตั้งค่า Datepicker สำหรับวันที่รับ
            $('#date_received').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                todayHighlight: true,
                todayBtn: 'linked',
                clearBtn: true,
                orientation: 'bottom auto'
            }).datepicker('setDate', new Date());
        });
    </script>
</body>
</html>