<?php
session_start();

// ตรวจสอบการเข้าสู่ระบบ
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

require_once '../config.php';

// ดึงรายการหมวดหมู่
$categories = $pdo->query("SELECT * FROM edms_work_categories")->fetchAll(PDO::FETCH_ASSOC);

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

// ฟังก์ชันจัดการไฟล์แนบ
function handleFileUpload($file)
{
    if (empty($file['name'])) return null;

    $allowed_types = ['pdf'];
    $max_size = 5 * 1024 * 1024; // 5MB
    $upload_dir = "uploads/" . date('Y') . "/";

    // ตรวจสอบและสร้างโฟลเดอร์
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

    // ตรวจสอบไฟล์
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed_types)) throw new Exception("กรุณาอัพโหลดไฟล์ PDF เท่านั้น");
    if ($file['size'] > $max_size) throw new Exception("ไฟล์มีขนาดใหญ่เกิน 5MB");

    $filename = 'document_' . uniqid() . '.pdf';
    if (!move_uploaded_file($file['tmp_name'], $upload_dir . $filename)) {
        throw new Exception("เกิดข้อผิดพลาดในการอัพโหลดไฟล์");
    }

    return $filename;
}

// จัดการการส่งฟอร์ม
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $next_number = getNextDocumentNumber($pdo);
        $current_year = date('Y');

        // จัดการไฟล์แนบ
        $file_name = null;
        if (!empty($_FILES['attachment']['name'])) {
            $file_name = handleFileUpload($_FILES['attachment']);
        }

        // เพิ่มข้อมูลลงฐานข้อมูล
        $stmt = $pdo->prepare("
            INSERT INTO edms_internal_out_documents (
                document_number, document_year, title, sender, receiver,
                date_created, attachment_path, category_id, created_by, note
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $next_number,
            $current_year,
            $_POST['title'],
            $_POST['sender'],
            $_POST['receiver'],
            $_POST['date_created'],
            $file_name,
            $_POST['category_id'],
            $_SESSION['user_id'],
            $_POST['note']
        ]);

        $_SESSION['message'] = "เพิ่มข้อมูลสำเร็จ";
        $_SESSION['message_type'] = "success";
        header("Location: index.php");
        exit();
    } catch (Exception $e) {
        $_SESSION['message'] = "เกิดข้อผิดพลาด: " . $e->getMessage();
        $_SESSION['message_type'] = "danger";
    }
}

$next_number = getNextDocumentNumber($pdo);
$current_year = date('Y') + 543; // แปลงเป็น พ.ศ.
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <?php require_once '../components/header.php'; ?>
    <title>สร้างหนังสือส่งออกภายใน</title>
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
                        <li class="breadcrumb-item"><a href="index.php">หนังสือส่งออกภายใน</a></li>
                        <li class="breadcrumb-item active">สร้างหนังสือส่งออกภายใน</li>
                    </ol>
                </nav>

                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">สร้างหนังสือส่งออกภายใน</h4>
                    </div>
                    <div class="card-body">
                        <form action="" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                            <div class="row g-3">
                                <!-- ฟอร์มด้านซ้าย -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">เลขที่หนังสือ</label>
                                        <input type="text" class="form-control" value="<?php echo "$next_number/$current_year"; ?>" readonly>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">ชื่อเรื่อง</label>
                                        <input type="text" name="title" class="form-control" required>
                                        <div class="invalid-feedback">
                                            กรุณากรอกชื่อเรื่อง
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">ผู้ส่ง</label>
                                        <input type="text" name="sender" class="form-control"
                                            value="<?php echo htmlspecialchars($_SESSION['username']); ?>" readonly>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">ผู้รับ</label>
                                        <input type="text" name="receiver" class="form-control" required>
                                        <div class="invalid-feedback">
                                            กรุณากรอกผู้รับ
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">หมายเหตุ</label>
                                        <textarea name="note" class="form-control" rows="3"
                                            placeholder="กรอกหมายเหตุ (ถ้ามี)"><?php echo isset($_POST['note']) ? htmlspecialchars($_POST['note']) : ''; ?></textarea>
                                    </div>
                                </div>

                                <!-- ฟอร์มด้านขวา -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">วันที่ส่งออก</label>
                                        <input type="date" name="date_created" class="form-control"
                                            value="<?php echo date('Y-m-d'); ?>" required>
                                        <div class="invalid-feedback">
                                            กรุณาเลือกวันที่
                                        </div>
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
    </script>
</body>

</html>