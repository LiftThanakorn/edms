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
        return 1;
    }

    // ส่งคืนเลขถัดไป
    return $result['max_number'] + 1;
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

// การจัดการการส่งฟอร์ม
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['action'])) {
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

        $stmt = $pdo->prepare("
            INSERT INTO edms_job_assignment_documents (
                document_number, document_year, title, sender, receiver,
                document_type, position_type, date_created, attachment_path,
                note, category_id, created_by
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $next_number,
            $current_year,
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
    <title>บันทึกการมอบหมายงาน</title>
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
                        <li class="breadcrumb-item"><a href="index.php">การมอบหมายงาน</a></li>
                        <li class="breadcrumb-item active">บันทึกการมอบหมายงาน</li>
                    </ol>
                </nav>

                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">บันทึกการมอบหมายงาน</h4>
                    </div>
                    <div class="card-body">
                        <form action="" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                            <div class="row g-3">
                                <!-- ฟอร์มด้านซ้าย -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">เลขที่หนังสือ</label>
                                        <input type="text" id="document_display" class="form-control" value="<?php echo "$next_number/$current_year"; ?>" readonly>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">ชื่อเรื่อง</label>
                                        <input type="text" name="title" class="form-control" required>
                                        <div class="invalid-feedback">กรุณากรอกชื่อเรื่อง</div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">ผู้มอบหมายงาน</label>
                                        <input type="text" name="sender" class="form-control"
                                            value="<?php echo htmlspecialchars($_SESSION['username']); ?>" readonly>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">ผู้รับมอบหมายงาน</label>
                                        <input type="text" name="receiver" class="form-control" required>
                                        <div class="invalid-feedback">กรุณากรอกผู้รับมอบหมายงาน</div>
                                    </div>
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
                                </div>

                                <!-- ฟอร์มด้านขวา -->
                                <div class="col-md-6">
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
                                        <label class="form-label">ตันที่มอบหมาย</label>
                                        <input type="date" name="date_created" class="form-control"
                                            value="<?php echo date('Y-m-d'); ?>" required>
                                        <div class="invalid-feedback">กรุณาเลือกวันที่</div>
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
                                        <label class="form-label">ไฟล์แนบ (PDF เท่านั้น, ไม่เกิน 5MB)</label>
                                        <input type="file" name="attachment" class="form-control" accept=".pdf">
                                        <div class="form-text">รองรับเฉพาะไฟล์ PDF ขนาดไม่เกิน 5MB</div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label">หมายเหตุ</label>
                                        <textarea name="note" class="form-control" rows="3"
                                            placeholder="กรอกหมายเหตุ (ถ้ามี)"><?php echo isset($_POST['note']) ? htmlspecialchars($_POST['note']) : ''; ?></textarea>
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
        document.addEventListener('DOMContentLoaded', function() {
            // เลือก elements ที่ต้องการใช้งาน
            const documentTypeSelect = document.getElementById('document_type');
            const positionTypeSelect = document.getElementById('position_type');
            const documentDisplay = document.getElementById('document_display');

            // ฟังก์ชันอัพเดทเลขที่เอกสาร
            async function updateDocumentNumber() {
                const documentType = documentTypeSelect.value;
                const positionType = positionTypeSelect.value;

                // ตรวจสอบว่ามีการเลือกทั้งสองค่าแล้ว
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

                        if (!response.ok) {
                            throw new Error('เกิดข้อผิดพลาดในการดึงข้อมูล');
                        }

                        const data = await response.json();
                        documentDisplay.value = `${data.next_number}/${data.year}`;
                    } catch (error) {
                        console.error('Error:', error);
                        documentDisplay.value = '-/<?php echo $current_year; ?>';
                    }
                } else {
                    // ถ้ายังเลือกไม่ครบ ให้แสดงเครื่องหมาย -
                    documentDisplay.value = '-/<?php echo $current_year; ?>';
                }
            }

            // เพิ่ม event listeners สำหรับ select boxes
            documentTypeSelect.addEventListener('change', updateDocumentNumber);
            positionTypeSelect.addEventListener('change', updateDocumentNumber);

            // ตรวจสอบ form validation
            const form = document.querySelector('form.needs-validation');
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);

            // เรียกฟังก์ชันครั้งแรกเพื่อตั้งค่าเริ่มต้น
            updateDocumentNumber();
        });
    </script>
</body>

</html>