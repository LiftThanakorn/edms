<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

require_once '../config.php';

$assignment_id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("SELECT * FROM edms_job_assignment_documents WHERE assignment_id = ?");
$stmt->execute([$assignment_id]);
$document = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$document) {
    $_SESSION['message'] = "ไม่พบข้อมูลที่ต้องการแก้ไข";
    $_SESSION['message_type'] = "danger";
    header("Location: index.php");
    exit();
}

$categories = $pdo->query("SELECT * FROM edms_work_categories")->fetchAll(PDO::FETCH_ASSOC);

function handleFileUpload($file) {
    if (empty($file['name'])) return null;
    
    $allowed_types = ['pdf'];
    $max_size = 5 * 1024 * 1024;
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $file_name = $document['attachment_path'];
        if (!empty($_FILES['attachment']['name'])) {
            $file_name = handleFileUpload($_FILES['attachment']);
            if ($document['attachment_path']) {
                unlink("uploads/" . date('Y') . "/" . $document['attachment_path']);
            }
        }

        $stmt = $pdo->prepare("
        UPDATE edms_job_assignment_documents SET
            document_reference_number = ?, 
            reference_date = ?, 
            title = ?,
            sender = ?, 
            receiver = ?, 
            document_type = ?, 
            position_type = ?,
            date_created = ?, 
            attachment_path = ?, 
            note = ?, 
            category_id = ?, 
            created_by = ?
        WHERE assignment_id = ?
    ");
    
    $stmt->execute([
        $_POST['document_reference_number'],  // 1
        $_POST['reference_date'],            // 2
        $_POST['title'],                     // 3
        $_POST['sender'],                    // 4
        $_POST['receiver'],                  // 5
        $_POST['document_type'],             // 6
        $_POST['position_type'],             // 7
        $_POST['date_created'],              // 8
        $file_name,                          // 9
        $_POST['note'],                      // 10
        $_POST['category_id'],               // 11
        $_SESSION['user_id'],                // 12
        $assignment_id                       // 13
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
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <?php require_once '../components/header.php'; ?>
    <title>แก้ไขทะเบียนรับ - ส่ง</title>
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
                        <li class="breadcrumb-item active">แก้ไขทะเบียนรับ - ส่ง</li>
                    </ol>
                </nav>

                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">แก้ไขทะเบียนรับ - ส่ง งานกำหนดตำแหน่ง</h4>
                    </div>
                    <div class="card-body">
                        <form action="" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">เลขที่หนังสือ</label>
                                        <input type="text" class="form-control" value="<?php echo $document['document_number'] . '/' . ($document['document_year'] + 543); ?>" readonly>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">เลขที่อ้างอิงหนังสือ</label>
                                        <input type="text" name="document_reference_number" class="form-control" 
                                            value="<?php echo htmlspecialchars($document['document_reference_number']); ?>"
                                            <?php echo $document['document_type'] !== 'รับ' ? 'disabled' : ''; ?>>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">วันที่อ้างอิงหนังสือ</label>
                                        <input type="date" name="reference_date" class="form-control"
                                            value="<?php echo $document['reference_date']; ?>"
                                            <?php echo $document['document_type'] !== 'รับ' ? 'disabled' : ''; ?>>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">ชื่อเรื่อง</label>
                                        <input type="text" name="title" class="form-control" 
                                            value="<?php echo htmlspecialchars($document['title']); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">ผู้รับหรือผู้ส่ง</label>
                                        <input type="text" name="receiver" class="form-control"
                                            value="<?php echo htmlspecialchars($document['receiver']); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">หมายเหตุ</label>
                                        <textarea name="note" class="form-control" rows="3"><?php echo htmlspecialchars($document['note']); ?></textarea>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">ประเภทเอกสาร</label>
                                        <select name="document_type" id="document_type" class="form-select" required>
                                            <?php
                                            $types = ['รับ', 'ส่ง', 'เวียน', 'สั่งการ'];
                                            foreach ($types as $type) {
                                                $selected = ($type === $document['document_type']) ? 'selected' : '';
                                                echo "<option value=\"$type\" $selected>$type</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">ประเภทตำแหน่ง</label>
                                        <select name="position_type" class="form-select" required>
                                            <?php
                                            $positions = ['สายวิชาการ', 'สายสนับสนุน'];
                                            foreach ($positions as $position) {
                                                $selected = ($position === $document['position_type']) ? 'selected' : '';
                                                echo "<option value=\"$position\" $selected>$position</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">หมวดหมู่งาน</label>
                                        <select name="category_id" class="form-select" required>
                                            <?php foreach ($categories as $category): ?>
                                                <option value="<?php echo $category['category_id']; ?>"
                                                    <?php echo $category['category_id'] == $document['category_id'] ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($category['category_name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">วันที่สร้าง</label>
                                        <input type="date" name="date_created" class="form-control"
                                            value="<?php echo $document['date_created']; ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">ไฟล์แนบ</label>
                                        <?php if ($document['attachment_path']): ?>
                                            <div class="mb-2">
                                                <a href="uploads/<?php echo date('Y'); ?>/<?php echo $document['attachment_path']; ?>" 
                                                   target="_blank" class="btn btn-sm btn-info">
                                                    <i class="bi bi-file-pdf"></i> ดูไฟล์
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                        <input type="file" name="attachment" class="form-control" accept=".pdf">
                                        <div class="form-text">อัพโหลดไฟล์ใหม่เพื่อแทนที่ไฟล์เดิม (ถ้ามี)</div>
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
        document.addEventListener('DOMContentLoaded', function() {
            const documentTypeSelect = document.getElementById('document_type');
            const documentReferenceInput = document.querySelector('[name="document_reference_number"]');
            const referenceDateInput = document.querySelector('[name="reference_date"]');

            documentTypeSelect.addEventListener('change', function() {
                const isReceived = this.value === 'รับ';
                documentReferenceInput.disabled = !isReceived;
                referenceDateInput.disabled = !isReceived;
                if (!isReceived) {
                    documentReferenceInput.value = '';
                    referenceDateInput.value = '';
                }
            });

            const form = document.querySelector('form.needs-validation');
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }

                const documentType = documentTypeSelect.value;
                if (documentType === 'รับ') {
                    if (!documentReferenceInput.value.trim()) {
                        event.preventDefault();
                        documentReferenceInput.setCustomValidity('กรุณากรอกเลขที่อ้างอิงหนังสือ');
                    } else {
                        documentReferenceInput.setCustomValidity('');
                    }

                    if (!referenceDateInput.value) {
                        event.preventDefault();
                        referenceDateInput.setCustomValidity('กรุณากรอกวันที่อ้างอิงหนังสือ');
                    } else {
                        referenceDateInput.setCustomValidity('');
                    }
                }

                form.classList.add('was-validated');
            }, false);
        });
    </script>
</body>
</html>