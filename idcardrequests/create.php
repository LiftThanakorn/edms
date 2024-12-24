<?php
session_start();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

require_once '../config.php';

function getNextDocumentNumber($pdo) {
    $current_year = date('Y');
    $stmt = $pdo->prepare("
        SELECT MAX(document_number) as max_number
        FROM edms_id_card_requests 
        WHERE document_year = ?
    ");
    $stmt->execute([$current_year]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return ($result['max_number'] ?? 0) + 1;
}

function handleFileUpload($file) {
    if (empty($file['name'])) return null;

    $allowed_types = ['pdf'];
    $max_size = 5 * 1024 * 1024; // 5MB
    $upload_dir = "uploads/" . date('Y') . "/";

    if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed_types)) throw new Exception("กรุณาอัพโหลดไฟล์ PDF เท่านั้น");
    if ($file['size'] > $max_size) throw new Exception("ไฟล์มีขนาดใหญ่เกิน 5MB");

    $filename = 'idcard_request_' . uniqid() . '.pdf';
    if (!move_uploaded_file($file['tmp_name'], $upload_dir . $filename)) {
        throw new Exception("เกิดข้อผิดพลาดในการอัพโหลดไฟล์");
    }

    return $filename;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['message'] = 'Invalid CSRF token';
        $_SESSION['message_type'] = 'danger';
        header('Location: index.php');
        exit();
    }

    try {
        $document_number = getNextDocumentNumber($pdo);
        $current_year = date('Y');

        $file_name = null;
        if (!empty($_FILES['attachment']['name'])) {
            $file_name = handleFileUpload($_FILES['attachment']);
        }

        $stmt = $pdo->prepare("
            INSERT INTO edms_id_card_requests (
                document_number, document_year, applicant_name, 
                date_submitted, attachment_path, note, created_by
            ) VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $document_number,
            $current_year,
            $_POST['applicant_name'],
            $_POST['date_submitted'],
            $file_name,
            $_POST['note'],
            $_SESSION['user_id']
        ]);

        $_SESSION['message'] = "เพิ่มคำขอบัตรประจำตัวสำเร็จ";
        $_SESSION['message_type'] = "success";
        header("Location: index.php");
        exit();
    } catch (Exception $e) {
        $_SESSION['message'] = "เกิดข้อผิดพลาด: " . $e->getMessage();
        $_SESSION['message_type'] = "danger";
    }
}

$next_number = getNextDocumentNumber($pdo);
$current_year = date('Y') + 543;
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <?php require_once '../components/header.php'; ?>
    <title>เพิ่มคำขอบัตรประจำตัว</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" rel="stylesheet">
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
                        <li class="breadcrumb-item"><a href="index.php">ทะเบียนคำขอบัตรประจำตัว</a></li>
                        <li class="breadcrumb-item active">เพิ่มคำขอบัตรประจำตัว</li>
                    </ol>
                </nav>

                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">เพิ่มคำขอบัตรประจำตัว</h4>
                    </div>
                    <div class="card-body">
                        <form action="" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">เลขที่คำขอ</label>
                                        <input type="text" class="form-control" value="<?php echo "$next_number/$current_year"; ?>" readonly>
                                    </div>


                                    <div class="mb-3">
                                        <label class="form-label">วันที่ยื่นคำขอ</label>
                                        <input type="text" name="date_submitted" class="form-control datepicker" 
                                               value="<?php echo date('d/m/Y'); ?>" required>
                                        <div class="invalid-feedback">กรุณาเลือกวันที่</div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">หมายเหตุ</label>
                                        <textarea name="note" class="form-control" rows="3" placeholder="กรุณากรอกหมายเหตุ (ถ้ามี)"></textarea>
                                    </div>
                                </div>
                                
                                <!-- ฟอร์มด้านขวา -->
                                <div class="col-md-6">
                                <div class="mb-3">
                                        <label class="form-label">ผู้ยื่นคำขอ</label>
                                        <input type="text" name="applicant_name" class="form-control" required>
                                        <div class="invalid-feedback">กรุณากรอกชื่อผู้ยื่นคำขอ</div>
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
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.th.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.datepicker').datepicker({
                format: 'dd/mm/yyyy',
                autoclose: true,
                todayBtn: true,
                language: 'th',
                thaiyear: true
            });

            $('form').on('submit', function() {
                $('.datepicker').each(function() {
                    let dateThai = $(this).val();
                    if (dateThai) {
                        let dateParts = dateThai.split('/');
                        let dateEng = `${dateParts[2]}-${dateParts[1]}-${dateParts[0]}`;
                        $(this).val(dateEng);
                    }
                });
            });

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
        });
    </script>
</body>
</html>