<?php
// เริ่มต้น session และเชื่อมต่อฐานข้อมูล
require_once '../config.php'; // เชื่อมต่อฐานข้อมูล (เปลี่ยนตามเส้นทางที่ใช้)

session_start();

// ตรวจสอบการล็อกอิน
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// ดึง role ของผู้ใช้
$is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

// ดึงข้อมูลหนังสือเวียนทั้งหมด
try {
    $stmt = $pdo->prepare("
        SELECT d.*, 
               u.username as created_by_name,
               DATE_FORMAT(d.date_sent, '%d/%m/%Y') as formatted_date_sent,
               DATE_FORMAT(d.created_at, '%d/%m/%Y %H:%i') as formatted_created_at
        FROM edms_circular_documents d
        LEFT JOIN edms_users u ON d.created_by = u.user_id
        ORDER BY d.created_at DESC
    ");
    $stmt->execute();
    $documents = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "เกิดข้อผิดพลาด: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <?php require_once '../components/header.php'; ?>
    <title>ทะเบียนหนังสือเวียน</title>
    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
</head>

<body>
    <?php require_once '../components/navbar.php'; ?>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-3">
                <?php require_once '../components/sidebar.php'; ?>
            </div>
            <div class="col-md-9">
                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/edms/index.php">หน้าหลัก</a></li>
                        <li class="breadcrumb-item active" aria-current="page">ทะเบียนหนังสือเวียน</li>
                    </ol>
                </nav>

                <div class="card shadow">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">ทะเบียนหนังสือเวียน</h4>
                        <a href="create.php" class="btn btn-light">
                            <i class="bi bi-plus-lg"></i> เพิ่มหนังสือเวียน
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
                            <table id="documentsTable" class="table table-bordered table-striped table-hover">
                                <thead class="table-primary">
                                    <tr class="text-center">
                                        <th class="text-center" style="width: 10%">เลขที่หนังสือ</th>
                                        <th class="text-center" style="width: 10%">วันที่ส่ง</th>
                                        <th class="text-center">ผู้รับ</th>
                                        <th class="text-center">ผู้ส่ง</th>
                                        <th class="text-center">ชื่อเรื่อง</th>
                                        <th class="text-center">หมายเหตุ</th>
                                        <th class="text-center" style="width: 5%">ไฟล์</th>
                                        <th class="text-center" style="width: 10%">วันที่สร้าง</th>
                                        <?php if ($is_admin): ?>
                                            <th class="text-center" style="width: 10%">จัดการ</th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($documents as $document): ?>
                                        <tr>
                                            <td class="text-center">ว <?php echo $document['document_number']; ?>/<?php echo $document['document_year'] + 543; ?></td>
                                            <td class="text-center"><?php echo $document['formatted_date_sent']; ?></td>
                                            <td><?php echo htmlspecialchars($document['receiver']); ?></td>
                                            <td><?php echo htmlspecialchars($document['sender']); ?></td>
                                            <td><?php echo htmlspecialchars($document['title']); ?></td>
                                            <td><?php echo htmlspecialchars($document['note']); ?></td>
                                            <td class="text-center">
                                                <?php if ($document['attachment_path']): ?>
                                                    <a href="uploads/<?php echo date('Y', strtotime($document['created_at'])); ?>/<?php echo $document['attachment_path']; ?>"
                                                        class="btn btn-primary btn-sm"
                                                        target="_blank"
                                                        title="ดูไฟล์แนบ">
                                                        <i class="bi bi-file-pdf"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center"><?php echo $document['formatted_created_at']; ?></td>
                                            <?php if ($is_admin): ?>
                                                <td class="text-center">
                                                    <div class="btn-group" role="group">
                                                        <a href="edit.php?id=<?php echo $document['document_id']; ?>"
                                                            class="btn btn-warning btn-sm"
                                                            title="แก้ไข">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <a href="delete.php?id=<?php echo $document['document_id']; ?>"
                                                            class="btn btn-danger btn-sm"
                                                            onclick="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบ?');"
                                                            title="ลบ">
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

    <!-- รวม JS ของ DataTables -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

    <!-- การเปิดใช้งาน DataTables -->
    <script>
        $(document).ready(function() {
            $('#documentsTable').DataTable({
                "order": [[7, "desc"]], // เรียงตามคอลัมน์วันที่สร้าง (index 7)
                "pageLength": 10,
                "dom": '<"top"lf>rt<"bottom"ip><"clear">',
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/th.json"
                },
                "responsive": true,
                "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "ทั้งหมด"]],
                "initComplete": function(settings, json) {
                    console.log('DataTable has been initialized');
                }
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
