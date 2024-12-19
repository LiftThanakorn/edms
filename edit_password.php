<?php
session_start();

// ตรวจสอบการเข้าสู่ระบบ
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'config.php'; // เชื่อมต่อฐานข้อมูล

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password === $confirm_password) {
        // ใช้ Argon2 ในการแฮชรหัสผ่าน
        $hashed_password = password_hash($new_password, PASSWORD_ARGON2ID);
        $stmt = $pdo->prepare("UPDATE edms_users SET password = ? WHERE user_id = ?");
        $stmt->execute([$hashed_password, $user_id]);
        $_SESSION['message'] = "รหัสผ่านถูกเปลี่ยนเรียบร้อยแล้ว";
        $_SESSION['message_type'] = "success";
        header("Location: edit_password.php");
        exit();
    } else {
        $_SESSION['message'] = "รหัสผ่านไม่ตรงกัน";
        $_SESSION['message_type'] = "danger";
    }
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <?php require_once './components/header.php'; ?>
    <title>แก้ไขรหัสผ่าน</title>
</head>

<body>
    <?php require_once './components/navbar.php'; ?>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-3">
                <?php require_once './components/sidebar.php'; ?> <!-- เพิ่ม sidebar ที่นี่ -->
            </div>
            <div class="col-md-9">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/edms/index.php">หน้าหลัก</a></li>
                        <li class="breadcrumb-item active" aria-current="page">แก้ไขรหัสผ่าน</li>
                    </ol>
                </nav>
                <div class="card">
                    <div class="card-header">
                        <h5>แก้ไขรหัสผ่าน</h5>
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
                        <form method="POST">
                            <div class="mb-3">
                                <label for="new_password" class="form-label">รหัสผ่านใหม่:</label>
                                <input type="password" name="new_password" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">ยืนยันรหัสผ่านใหม่:</label>
                                <input type="password" name="confirm_password" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary">เปลี่ยนรหัสผ่าน</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>