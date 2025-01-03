<?php
require_once '../config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

try {
    $stmt = $pdo->prepare("
        SELECT d.*, 
               u.username as created_by_name,
               c.category_name,
               DATE_FORMAT(d.date_created, '%d/%m/%Y') as formatted_date_created,
               DATE_FORMAT(d.created_at, '%d/%m/%Y') as formatted_created_at
        FROM edms_external_out_documents d
        LEFT JOIN edms_users u ON d.created_by = u.user_id
        LEFT JOIN edms_work_categories c ON d.category_id = c.category_id
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
    <title>ทะเบียนหนังสือส่งภายนอก</title>
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
                        <li class="breadcrumb-item"><a href="/edms/">หน้าหลัก</a></li>
                        <li class="breadcrumb-item active">ทะเบียนหนังสือส่งภายนอก</li>
                    </ol>
                </nav>

                <div class="card shadow">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">ทะเบียนหนังสือส่งภายนอก</h4>
                        <a href="create.php" class="btn btn-light">
                            <i class="bi bi-plus-lg"></i> เพิ่มทะเบียนหนังสือส่งภายนอก
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
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <div class="table-responsive">
                            <table id="documentsTable" class="table table-bordered table-striped table-hover">
                                <thead class="table-primary">
                                    <tr class="text-center">
                                        <th style="width: 10%">เลขที่หนังสือ</th>
                                        <th style="width: 10%">วันที่ส่ง</th>
                                        <th style="width: 15%">จาก</th>
                                        <th style="width: 15%">ถึง</th>
                                        <th>เรื่อง</th>
                                        <th style="width: 10%">ผู้สร้าง</th>
                                        <th style="width: 5%">ไฟล์</th>
                                        <?php if ($is_admin): ?>
                                            <th style="width: 5%"><i class="bi bi-gear"></i></th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($documents as $document): ?>
                                        <tr>
                                            <td class="text-center">
                                                <?php echo str_pad($document['document_number'], 3, '0', STR_PAD_LEFT); ?>/<?php echo $document['document_year'] + 543; ?>
                                            </td>
                                            <td class="text-center"><?php echo $document['formatted_date_created']; ?></td>
                                            <td><?php echo htmlspecialchars($document['sender']); ?></td>
                                            <td><?php echo htmlspecialchars($document['receiver'] ?? '-'); ?></td>
                                            <td><?php echo htmlspecialchars($document['title']); ?></td>
                                            <td><?php echo htmlspecialchars($document['created_by_name']); ?></td>
                                            <td class="text-center">
                                                <?php if ($document['attachment_path']): ?>
                                                    <a href="../uploads/<?php echo $document['document_year']; ?>/<?php echo $document['attachment_path']; ?>"
                                                        class="btn btn-primary btn-sm"
                                                        target="_blank">
                                                        <i class="bi bi-file-pdf"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                            <?php if ($is_admin): ?>
                                                <td class="text-center">
                                                    <div class="btn-group">
                                                        <a href="edit.php?id=<?php echo $document['document_id']; ?>"
                                                            class="btn btn-warning btn-sm">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <a href="delete.php?id=<?php echo $document['document_id']; ?>"
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
            $('#documentsTable').DataTable({
                order: [[0, "desc"]],
                pageLength: 10,
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.13.7/i18n/th.json"
                },
                responsive: true,
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, "ทั้งหมด"]
                ]
            });
        });
    </script>
</body>
</html>
