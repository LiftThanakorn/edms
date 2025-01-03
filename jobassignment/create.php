<?php
session_start();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// ตรวจสอบการเข้าสู่ระบบ
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

require_once '../config.php';

// ดึงรายการหมวดหมู่
$categories = $pdo->query("SELECT * FROM edms_work_categories")->fetchAll(PDO::FETCH_ASSOC);

// ฟังก์ชันสำหรับดึงเลขที่เอกสารถัดไป โดยแยกตามประเภทเอกสารและประเภทตำแหน่ง
function getNextDocumentNumber($pdo, $document_type = null, $position_type = null)
{
    $current_year = date('Y');

    // ถ้าไม่มีการระบุประเภท ให้แสดงเป็น - 
    if (empty($document_type) || empty($position_type)) {
        return "-";
    }

    // ค้นหาเลขที่เอกสารล่าสุดตามประเภทที่ระบุ
    $stmt = $pdo->prepare("
        SELECT MAX(document_number) as max_number
        FROM edms_job_assignment_documents 
        WHERE document_year = ? 
        AND document_type = ?
        AND position_type = ?
    ");

    $stmt->execute([$current_year, $document_type, $position_type]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // ถ้ายังไม่มีเอกสารในปีนี้ ให้เริ่มที่เลข 1
    if (!$result['max_number']) {
        return sprintf("%03d", 1); // แสดงเป็น 001
    }

    // ส่งคืนเลขถัดไปในรูปแบบ 3 หลัก
    return sprintf("%03d", $result['max_number'] + 1);
}

// ฟังก์ชันจัดการการอัพโหลดไฟล์
function handleFileUpload($file)
{
    if (empty($file['name'])) return null;

    $allowed_types = ['pdf'];
    $max_size = 5 * 1024 * 1024; // 5MB
    $upload_dir = "uploads/" . date('Y') . "/";

    if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed_types)) throw new Exception("กรุณาอัพโหลดไฟล์ PDF เท่านั้น");
    if ($file['size'] > $max_size) throw new Exception("ไฟล์มีขนาดใหญ่เกิน 5MB");

    $filename = 'assignment_' . uniqid() . '.pdf';
    if (!move_uploaded_file($file['tmp_name'], $upload_dir . $filename)) {
        throw new Exception("เกิดข้อผิดพลาดในการอัพโหลดไฟล์");
    }

    return $filename;
}

// ฟังก์ชัน AJAX สำหรับดึงเลขที่เอกสารถัดไป
if (isset($_POST['action']) && $_POST['action'] === 'get_next_number') {
    $document_type = $_POST['document_type'] ?? '';
    $position_type = $_POST['position_type'] ?? '';

    $next_number = getNextDocumentNumber($pdo, $document_type, $position_type);
    $current_year = date('Y') + 543; // แปลงเป็น พ.ศ.

    header('Content-Type: application/json');
    echo json_encode([
        'next_number' => $next_number,
        'year' => $current_year
    ]);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['action'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['message'] = 'Invalid CSRF token';
        $_SESSION['message_type'] = 'danger';
        header('Location: index.php');
        exit();
    }
    try {
        $document_type = $_POST['document_type'];
        $position_type = $_POST['position_type'];

        // ดึงเลขที่เอกสารถัดไป
        $next_number = getNextDocumentNumber($pdo, $document_type, $position_type);
        $current_year = date('Y');

        $file_name = null;
        if (!empty($_FILES['attachment']['name'])) {
            $file_name = handleFileUpload($_FILES['attachment']);
        }

        // กำหนดค่า document_reference_number
        $document_reference_number = null;
        if ($document_type === 'รับ' && !empty($_POST['document_reference_number'])) {
            $document_reference_number = $_POST['document_reference_number'];
        }

        $stmt = $pdo->prepare("
        INSERT INTO edms_job_assignment_documents (
            document_number, document_year, document_reference_number, reference_date, title, 
            sender, receiver, document_type, position_type, date_created, 
            attachment_path, note, category_id, created_by
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

        $stmt->execute([
            $next_number,
            $current_year,
            $document_reference_number,
            $_POST['reference_date'] ?? null,
            $_POST['title'],
            $_POST['sender'],
            $_POST['receiver'],
            $document_type,
            $position_type,
            $_POST['date_created'],
            $file_name,
            $_POST['note'],
            $_POST['category_id'],
            $_SESSION['user_id']
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

// ค่าเริ่มต้นสำหรับเลขที่เอกสาร
$next_number = "-";
$current_year = date('Y') + 543; // แปลงเป็น พ.ศ.
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <?php require_once '../components/header.php'; ?>
    <title>เพิมทะเบียนรับ - ส่ง</title>
    <!-- Add jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
                        <li class="breadcrumb-item"><a href="index.php">ทะเบียนรับ - ส่งงาน กำหนดตำแหน่ง</a></li>
                        <li class="breadcrumb-item active">เพิ่มทะเบียนรับ - ส่ง งานกำหนดตำแหน่ง</li>
                    </ol>
                </nav>

                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">เพิ่มทะเบียนรับ - ส่ง งานกำหนดตำแหน่ง</h4>
                    </div>
                    <div class="card-body">
                        <?php if (isset($_SESSION['message'])): ?>
                            <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible fade show">
                                <?php 
                                    echo htmlspecialchars($_SESSION['message']); 
                                    unset($_SESSION['message']);
                                    unset($_SESSION['message_type']);
                                ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                            <!-- เพิ่ม CSRF token -->
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            <div class="row g-3">
                                <!-- ฟอร์มด้านซ้าย -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">เลขที่หนังสือ</label>
                                        <input type="text" id="document_display" class="form-control" value="<?php echo "$next_number/$current_year"; ?>" readonly>
                                    </div>
                                    <!-- เพิ่มหลังจาก input เลขที่หนังสือ -->
                                    <div class="mb-3">
                                        <label class="form-label">เลขที่อ้างอิงหนังสือ</label>
                                        <input type="text" name="document_reference_number" id="document_reference_number" class="form-control" disabled>
                                        <div class="form-text">สามารถกรอกได้เฉพาะเมื่อประเภทเอกสารเป็น "รับ" เท่านั้น</div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">วันที่อ้างอิงหนังสือ</label>
                                        <input type="date" name="reference_date" id="reference_date" class="form-control"
                                            value="<?php echo date('Y-m-d'); ?>" disabled>
                                        <div class="form-text">สามารถกรอกได้เฉพาะเมื่อประเภทเอกสารเป็น "รับ" เท่านั้น</div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">ชื่อเรื่อง</label>
                                        <input type="text" name="title" class="form-control" 
       value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>" required>
                                        <div class="invalid-feedback">กรุณากรอกชื่อเรื่อง</div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">ผู้รับหรือผู้ส่ง</label>
                                        <input type="text" name="receiver" class="form-control" required>
                                        <div class="invalid-feedback">กรุณากรอกผู้รับมอบหมายงาน</div>
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
                                        <label class="form-label">ประเภทเอกสาร</label>
                                        <select name="document_type" id="document_type" class="form-select" required>
                                            <option value="">เลือกประเภทเอกสาร</option>
                                            <option value="รับ">รับ</option>
                                            <option value="ส่ง">ส่ง</option>
                                            <option value="เวียน">เวียน</option>
                                            <option value="สั่งการ">สั่งการ</option>
                                        </select>
                                        <div class="invalid-feedback">กรุณาเลือกประเภทเอกสาร</div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">ประเภทตำแหน่ง</label>
                                        <select name="position_type" id="position_type" class="form-select" required>
                                            <option value="">เลือกประเภทตำแหน่ง</option>
                                            <option value="สายวิชาการ">สายวิชาการ</option>
                                            <option value="สายสนับสนุน">สายสนับสนุน</option>
                                        </select>
                                        <div class="invalid-feedback">กรุณาเลือกประเภทตำแหน่ง</div>
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
                                        <div class="invalid-feedback">กรุณาเลือกหมวดหมู่</div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">วันที่สร้าง</label>
                                        <input type="date" name="date_created" class="form-control"
                                            value="<?php echo date('Y-m-d'); ?>" required>
                                        <div class="invalid-feedback">กรุณาเลือกวันที่</div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">ไฟล์แนบ (PDF เท่านั้น, ไม่เกิน 5MB)</label>
                                        <input type="file" name="attachment" class="form-control" 
                                               accept=".pdf" 
                                               data-max-size="5242880">
                                        <div class="invalid-feedback">กรุณาเลือกไฟล์ PDF ขนาดไม่เกิน 5MB</div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">ผู้สร้าง</label>
                                        <input type="text" name="sender" class="form-control"
                                            value="<?php echo htmlspecialchars($_SESSION['username']); ?>" readonly>
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
        $(document).ready(function() {
            // Element selections
            const documentTypeSelect = document.getElementById('document_type');
            const positionTypeSelect = document.getElementById('position_type');
            const documentDisplay = document.getElementById('document_display');
            const documentReferenceInput = document.getElementById('document_reference_number');
            const referenceDateInput = document.getElementById('reference_date');
            const form = document.querySelector('form.needs-validation');

            // Function to update document number
            async function updateDocumentNumber() {
                const documentType = documentTypeSelect.value;
                const positionType = positionTypeSelect.value;

                if (documentType === 'รับ') {
                    documentReferenceInput.disabled = false;
                    referenceDateInput.disabled = false;
                } else {
                    documentReferenceInput.disabled = true;
                    referenceDateInput.disabled = true;
                    documentReferenceInput.value = '';
                    referenceDateInput.value = '';
                }

                if (documentType && positionType) {
                    try {
                        const formData = new FormData();
                        formData.append('action', 'get_next_number');
                        formData.append('document_type', documentType);
                        formData.append('position_type', positionType);

                        const response = await fetch('', {
                            method: 'POST',
                            body: formData
                        });

                        if (!response.ok) throw new Error('เกิดข้อผิดพลาดในการดึงข้อมูล');

                        const data = await response.json();
                        // แก้ไขการแสดงผลให้ใช้เลขที่เอกสารที่มี leading zeros
                        documentDisplay.value = `${data.next_number}/${data.year}`;
                    } catch (error) {
                        console.error('Error:', error);
                        documentDisplay.value = '-/<?php echo $current_year; ?>';
                    }
                } else {
                    documentDisplay.value = '-/<?php echo $current_year; ?>';
                }
            }

            // Event listeners
            documentTypeSelect.addEventListener('change', updateDocumentNumber);
            positionTypeSelect.addEventListener('change', updateDocumentNumber);

            // Form validation
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            });

            // File size validation
            const fileInput = document.querySelector('input[type="file"]');
            fileInput.addEventListener('change', function() {
                if (this.files[0]) {
                    if (this.files[0].size > parseInt(this.dataset.maxSize)) {
                        this.setCustomValidity('ไฟล์มีขนาดใหญ่เกิน 5MB');
                    } else {
                        this.setCustomValidity('');
                    }
                }
            });

            // Initial setup
            updateDocumentNumber();
        });
    </script>
</body>

</html>