<?php
// เริ่ม session และเชื่อมต่อฐานข้อมูล
session_start();
require_once 'config.php';  // ใส่การเชื่อมต่อฐานข้อมูล

// ตรวจสอบว่า user มี role เป็น admin หรือไม่
if ($_SESSION['role'] !== 'admin') {
    // ถ้าไม่ใช่ admin ให้ redirect ไปหน้าอื่น (เช่น หน้าเข้าสู่ระบบ)
    header("Location: login.php");
    exit();
}

// ตัวแปรสำหรับข้อความแจ้งเตือน
$message = '';
// เพิ่มหมวดหมู่งาน
if (isset($_POST['add_category'])) {
    $category_name = $_POST['category_name'];
    $description = $_POST['description'];

    if (!empty($category_name)) {
        $query = "INSERT INTO edms_work_categories (category_name, description) VALUES (:category_name, :description)";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':category_name', $category_name);
        $stmt->bindParam(':description', $description);

        if ($stmt->execute()) {
            $message = "เพิ่มหมวดหมู่งานเรียบร้อยแล้ว";
        } else {
            $message = "เกิดข้อผิดพลาดในการเพิ่มหมวดหมู่งาน";
        }
    } else {
        $message = "กรุณากรอกชื่อหมวดหมู่งาน";
    }
}

// แก้ไขหมวดหมู่งาน
if (isset($_POST['edit_category'])) {
    $category_id = $_POST['category_id'];
    $category_name = $_POST['category_name'];
    $description = $_POST['description'];

    if (!empty($category_name)) {
        $query = "UPDATE edms_work_categories SET category_name = :category_name, description = :description WHERE category_id = :category_id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':category_name', $category_name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':category_id', $category_id);

        if ($stmt->execute()) {
            $message = "แก้ไขหมวดหมู่งานเรียบร้อยแล้ว";
        } else {
            $message = "เกิดข้อผิดพลาดในการแก้ไขหมวดหมู่งาน";
        }
    } else {
        $message = "กรุณากรอกชื่อหมวดหมู่งาน";
    }
}

// ลบหมวดหมู่งาน
if (isset($_GET['delete_id'])) {
    $category_id = $_GET['delete_id'];

    $query = "DELETE FROM edms_work_categories WHERE category_id = :category_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':category_id', $category_id);

    if ($stmt->execute()) {
        $message = "ลบหมวดหมู่งานเรียบร้อยแล้ว";
    } else {
        $message = "เกิดข้อผิดพลาดในการลบหมวดหมู่งาน";
    }
}

// แสดงข้อมูลหมวดหมู่งานทั้งหมด
$query = "SELECT * FROM edms_work_categories ORDER BY created_at DESC";
$stmt = $pdo->query($query);
$categories = $stmt->fetchAll();
?>


<!DOCTYPE html>
<html lang="th">

<head>
    <?php require_once './components/header.php'; ?>
    <title>จัดการหมวดหมู่งาน</title>
</head>

<body>
    <?php require_once './components/navbar.php'; ?>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-3">
                <?php require_once './components/sidebar.php'; ?>
            </div>
            <div class="col-md-9">
                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/edms/index.php">หน้าหลัก</a></li>
                        <li class="breadcrumb-item active" aria-current="page">จัดการหมวดหมู่งาน</li>
                    </ol>
                </nav>
                <!-- แสดงข้อความแจ้งเตือน -->
                <?php if ($message): ?>
                    <div class="alert alert-warning">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">เพิ่มหมวดหมู่งาน</h4>
                    </div>
                    <div class="card-body">
                        <!-- ฟอร์มการเพิ่มหมวดหมู่งาน -->
                        <form action="work_categories.php" method="POST">
                            <div class="mb-3">
                                <label for="category_name" class="form-label">ชื่อหมวดหมู่งาน</label>
                                <input type="text" id="category_name" name="category_name" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">รายละเอียด</label>
                                <textarea id="description" name="description" class="form-control" rows="3"></textarea>
                            </div>
                            <button type="submit" name="add_category" class="btn btn-primary">เพิ่มหมวดหมู่งาน</button>
                        </form>
                    </div>
                </div>

                <!-- แสดงรายการหมวดหมู่งานทั้งหมด -->
                <div class="card mt-4">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">รายการหมวดหมู่งาน</h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>ชื่อหมวดหมู่งาน</th>
                                    <th>รายละเอียด</th>
                                    <th>วันที่สร้าง</th>
                                    <th>ดำเนินการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($categories as $category): ?>
                                    <tr>
                                        <td><?php echo $category['category_id']; ?></td>
                                        <td><?php echo $category['category_name']; ?></td>
                                        <td><?php echo $category['description']; ?></td>
                                        <td><?php echo $category['created_at']; ?></td>
                                        <td>
                                            <!-- ฟอร์มสำหรับแก้ไขหมวดหมู่งาน -->
                                            <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editCategoryModal<?php echo $category['category_id']; ?>">แก้ไข</button>

                                            <!-- ปุ่มลบหมวดหมู่งาน -->
                                            <a href="work_categories.php?delete_id=<?php echo $category['category_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('คุณต้องการลบหมวดหมู่งานนี้หรือไม่?')">ลบ</a>
                                        </td>
                                    </tr>

                                    <!-- Modal แก้ไขหมวดหมู่งาน -->
                                    <div class="modal fade" id="editCategoryModal<?php echo $category['category_id']; ?>" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="work_categories.php" method="POST">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="editCategoryModalLabel">แก้ไขหมวดหมู่งาน</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <input type="hidden" name="category_id" value="<?php echo $category['category_id']; ?>">
                                                        <div class="mb-3">
                                                            <label for="category_name" class="form-label">ชื่อหมวดหมู่งาน</label>
                                                            <input type="text" id="category_name" name="category_name" class="form-control" value="<?php echo $category['category_name']; ?>" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="description" class="form-label">รายละเอียด</label>
                                                            <textarea id="description" name="description" class="form-control" rows="3"><?php echo $category['description']; ?></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                                                        <button type="submit" name="edit_category" class="btn btn-primary">บันทึกการแก้ไข</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>