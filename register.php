<?php
require_once 'config.php';
// Initialize variables to empty values
$username = '';
$password = '';
$confirm_password = '';
$first_name = '';
$last_name = '';
$email = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate inputs
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $role = 'user'; // Default role

    // Validation checks
    if (empty($username)) {
        $errors[] = 'กรุณากรอกชื่อผู้ใช้';
    }

    if (empty($password)) {
        $errors[] = 'กรุณากรอกรหัสผ่าน';
    }

    if ($password !== $confirm_password) {
        $errors[] = 'รหัสผ่านไม่ตรงกัน';
    }

    if (empty($first_name)) {
        $errors[] = 'กรุณากรอกชื่อ';
    }

    if (empty($last_name)) {
        $errors[] = 'กรุณากรอกนามสกุล';
    }

    // Check if username already exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM edms_users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    if ($stmt->fetchColumn() > 0) {
        $errors[] = 'ชื่อผู้ใช้นี้มีอยู่แล้ว';
    }

    // If no errors, proceed with registration
    if (empty($errors)) {
        // Hash password using Argon2
        $hashed_password = password_hash($password, PASSWORD_ARGON2ID);

        try {
            $stmt = $pdo->prepare("INSERT INTO edms_users 
                (username, password, first_name, last_name, email, role) 
                VALUES (:username, :password, :first_name, :last_name, :email, :role)");

            $stmt->execute([
                'username' => $username,
                'password' => $hashed_password,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'email' => $email,
                'role' => $role
            ]);

            // Redirect to login page or show success message
            header("Location: login.php?registration=success");
            exit();
        } catch(PDOException $e) {
            $errors[] = 'การลงทะเบียนล้มเหลว: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ลงทะเบียน</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white text-center">
                        <h2>สมัครสมาชิก</h2>
                    </div>
                    <div class="card-body">
                        <?php 
                        // Display errors if any
                        if (!empty($errors)) {
                            echo '<div class="alert alert-danger">';
                            foreach ($errors as $error) {
                                echo $error . '<br>';
                            }
                            echo '</div>';
                        }
                        ?>

                        <form method="post" action="">
                            <div class="mb-3">
                                <label for="username" class="form-label">ชื่อผู้ใช้</label>
                                <input type="text" class="form-control" id="username" name="username" required 
                                       value="<?php echo htmlspecialchars($username); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">รหัสผ่าน</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">ยืนยันรหัสผ่าน</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>
                            <div class="mb-3">
                                <label for="first_name" class="form-label">ชื่อ</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" required 
                                       value="<?php echo htmlspecialchars($first_name); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="last_name" class="form-label">นามสกุล</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" required 
                                       value="<?php echo htmlspecialchars($last_name); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">อีเมล</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo htmlspecialchars($email); ?>">
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">สมัครสมาชิก</button>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer text-center">
                        <p class="mb-0">มีบัญชีอยู่แล้ว? <a href="login.php" class="link-primary">เข้าสู่ระบบ</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
