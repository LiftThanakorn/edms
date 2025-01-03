<?php
session_start();
require_once '../config.php';

// ตรวจสอบการล็อกอิน
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// ดึง role ของผู้ใช้
$is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

// ดึงข้อมูลการมอบหมายงานทั้งหมด
try {
    $stmt = $pdo->prepare("
    SELECT 
        d.*,
        u.username as created_by_name,
        COALESCE(c.category_name, 'ไม่ระบุหมวดหมู่') as category_name,
        DATE_FORMAT(d.date_created, '%d/%m/%Y') as formatted_date_created,
        DATE_FORMAT(d.created_at, '%d/%m/%Y %H:%i') as formatted_created_at
    FROM edms_job_assignment_documents d
    LEFT JOIN edms_users u ON d.created_by = u.user_id
    LEFT JOIN edms_work_categories c ON d.category_id = c.category_id
    ORDER BY d.created_at DESC
");

    $stmt->execute();
    $assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "เกิดข้อผิดพลาด: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <?php require_once '../components/header.php'; ?>
    <title>ทะเบียนการรับ-ส่งงานกำหนดตำแหน่ง</title>
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
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
                        <li class="breadcrumb-item"><a href="/edms/index.php">หน้าหลัก</a></li>
                        <li class="breadcrumb-item active" aria-current="page">ทะเบียนการรับ-ส่งงานกำหนดตำแหน่ง</li>
                    </ol>
                </nav>

                <div class="card shadow">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">ทะเบียนการรับ-ส่งงานกำหนดตำแหน่ง</h4>
                        <a href="create.php" class="btn btn-light">
                            <i class="bi bi-plus-lg"></i> เพิ่มทะเบียนการรับ-ส่งงานกำหนดตำแหน่ง
                        </a>
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

                        <div class="table-responsive">
                            <table id="assignmentsTable" class="table table-bordered table-striped table-hover">
                                <thead class="table-primary">
                                    <tr class="text-center">
                                        <th style="width: 8%">ประเภท</th>
                                        <th style="width: 10%">เลขที่หนังสือ</th>
                                        <th style="width: 10%">เลขที่อ้างอิง</th> <!-- เพิ่มหัวข้อ -->
                                        <th style="width: 12%">เรื่อง</th>
                                        <th style="width: 8%">วันที่</th>
                                        <th style="width: 10%">ผู้รับมอบหมาย</th>
                                        <th style="width: 15%">สายงาน</th>
                                        <th style="width: 5%">ไฟล์</th>
                                        <th style="width: 10%">ผู้สร้าง</th>
                                        <?php if ($is_admin): ?>
                                            <th style="width: 8%">จัดการ</th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($assignments as $assignment): ?>
                                        <tr>
                                            <td class="text-center">
                                                <?php if ($assignment['document_type'] === 'รับ'): ?>
                                                    <span class="badge bg-success rounded-pill p-2 fs-6">
                                                        <i class="bi bi-arrow-right-circle"></i> รับ
                                                    </span>
                                                <?php elseif ($assignment['document_type'] === 'ส่ง'): ?>
                                                    <span class="badge bg-primary rounded-pill p-2 fs-6">
                                                        <i class="bi bi-arrow-left-circle"></i> ส่ง
                                                    </span>
                                                <?php elseif ($assignment['document_type'] === 'เวียน'): ?>
                                                    <span class="badge bg-warning text-dark rounded-pill p-2 fs-6">
                                                        <i class="bi bi-arrow-repeat"></i> เวียน
                                                    </span>
                                                <?php elseif ($assignment['document_type'] === 'สั่งการ'): ?>
                                                    <span class="badge bg-danger rounded-pill p-2 fs-6">
                                                        <i class="bi bi-broadcast"></i> สั่งการ
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <?php
                                                if ($assignment['document_type'] === 'เวียน'):
                                                    echo "ว " . sprintf("%03d", $assignment['document_number']) . "/" . ($assignment['document_year'] + 543);
                                                else:
                                                    echo sprintf("%03d", $assignment['document_number']) . "/" . ($assignment['document_year'] + 543);
                                                endif;
                                                ?>
                                            </td>
                                            <td class="text-center">
                                                <?php echo htmlspecialchars($assignment['document_reference_number'] ?? '-'); ?> <!-- เพิ่มการแสดงเลขที่อ้างอิง -->
                                            </td>
                                            <td><?php echo htmlspecialchars($assignment['title']); ?></td>
                                            <td class="text-center"><?php echo $assignment['formatted_date_created']; ?></td>
                                            <td><?php echo htmlspecialchars($assignment['receiver']); ?></td>
                                            <td class="text-center"><?php echo $assignment['position_type']; ?></td>
                                            <td class="text-center">
                                                <?php if ($assignment['attachment_path']): ?>
                                                    <a href="uploads/<?php echo date('Y', strtotime($assignment['created_at'])); ?>/<?php echo $assignment['attachment_path']; ?>"
                                                        class="btn btn-primary btn-sm"
                                                        target="_blank"
                                                        title="ดูไฟล์แนบ">
                                                        <i class="bi bi-file-pdf"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($assignment['sender']); ?></td>
                                            <?php if ($is_admin): ?>
                                                <td class="text-center">
                                                    <div class="btn-group" role="group">
                                                        <a href="edit.php?id=<?php echo $assignment['assignment_id']; ?>" class="btn btn-warning btn-sm">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <a href="delete.php?id=<?php echo $assignment['assignment_id']; ?>"
                                                            class="btn btn-danger btn-sm"
                                                            onclick="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบ?');">
                                                            <i class="bi bi-trash"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            <?php endif; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>




    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#assignmentsTable').DataTable({
                "order": [[8, "desc"]], // Sort by created_by column (index 8) in descending order
                "pageLength": 10,
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/th.json"
                },
                "responsive": true,
                "lengthMenu": [
                    [10, 25, 50, -1],
                    [10, 25, 50, "ทั้งหมด"]
                ]
            });

        });
    </script>
</body>

</html>