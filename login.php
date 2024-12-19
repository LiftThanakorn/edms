<?php
session_start();
require_once 'config.php';

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Validate input
    if (empty($username) || empty($password)) {
        $login_error = 'กรุณากรอกชื่อผู้ใช้และรหัสผ่าน';
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $login_error = 'ชื่อผู้ใช้สามารถประกอบด้วยตัวอักษรภาษาอังกฤษ ตัวเลข และขีดล่างเท่านั้น';
    } elseif (strlen($password) < 6 || strlen($password) > 20) {
        $login_error = 'รหัสผ่านต้องมีความยาวระหว่าง 6 ถึง 20 ตัวอักษร';
    } else {
        try {
            // Prepare statement to fetch user
            $stmt = $pdo->prepare("SELECT * FROM edms_users WHERE username = :username");
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verify password
            if ($user && password_verify($password, $user['password'])) {
                // Login successful
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['first_name'] = $user['first_name'];
                $_SESSION['last_name'] = $user['last_name'];
                $_SESSION['role'] = $user['role'];

                // Regenerate session ID for security
                session_regenerate_id(true);

                // Redirect to index.php after successful login
                header("Location: index.php");
                exit();
            } else {
                $login_error = 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง';
            }
        } catch (PDOException $e) {
            $login_error = 'เกิดข้อผิดพลาดในการเข้าสู่ระบบ: ' . htmlspecialchars($e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ EDMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8eb 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 1rem;
            font-family: 'Sarabun', sans-serif;
        }

        .login-container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            max-width: 900px;
            width: 100%;
            margin: auto;
        }

        .logo-section {
            background: #4a90e2;
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            height: 100%;
            min-height: 300px;
            text-align: center;
        }

        .logo-section h1 {
            font-size: 3rem;
            margin-bottom: 1.5rem;
        }

        .logo-section p {
            font-size: 1.2rem;
            line-height: 1.6;
            text-align: center;
            opacity: 0.95;
        }

        .logo-image {
            width: 150px;
            height: 150px;
            margin-bottom: 2rem;
            object-fit: contain;
        }

        .form-section {
            padding: 2rem;
        }

        .form-control {
            border: 1px solid #e0e0e0;
            padding: 0.75rem;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .form-control:focus {
            border-color: #4a90e2;
            box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
        }

        .btn-primary {
            background-color: #4a90e2;
            border: none;
            padding: 0.75rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s;
        }

        .btn-primary:hover {
            background-color: #357abd;
            transform: translateY(-1px);
        }

        .form-label {
            color: #546e7a;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .link-primary {
            color: #4a90e2;
            text-decoration: none;
        }

        .link-primary:hover {
            color: #357abd;
            text-decoration: underline;
        }

        .alert {
            border-radius: 8px;
            font-size: 0.9rem;
        }

        @media (max-width: 767.98px) {
            .logo-section {
                padding: 2rem;
                min-height: auto;
            }

            .logo-section h1 {
                font-size: 2rem;
            }

            .logo-image {
                width: 120px;
                height: 120px;
            }

            .form-section {
                padding: 1rem;
            }
        }
    </style>
    <script>
        function validateForm() {
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;

            if (username.trim() === '' || password.trim() === '') {
                alert('กรุณากรอกชื่อผู้ใช้และรหัสผ่าน');
                return false;
            }

            // Check for special characters in username
            const usernameRegex = /^[a-zA-Z0-9_]+$/;
            if (!usernameRegex.test(username)) {
                alert('ชื่อผู้ใช้สามารถประกอบด้วยตัวอักษรภาษาอังกฤษ ตัวเลข และขีดล่างเท่านั้น');
                return false;
            }

            return true; // Allow form submission
        }
    </script>
</head>

<body>
    <div class="container">
        <div class="login-container">
            <div class="row g-0">
                <div class="col-md-7">
                    <div class="logo-section">
                        <img src="images/logo.png" alt="EDMS Logo" class="logo-image">
                        <h1>HRLAD-EDMS</h1>
                        <p>ระบบจัดการเอกสารอิเล็กทรอนิกส์<br>Electronic Document Management System<br>งานบริหารทรัพยากรบุคคลและนิติการ<br>Human Resources and Legal Affairs Division</p>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="form-section">
                        <h2 class="text-center mb-4">เข้าสู่ระบบ</h2>
                        <?php
                        if (isset($_GET['registration']) && $_GET['registration'] === 'success') {
                            echo '<div class="alert alert-success">การลงทะเบียนสำเร็จ กรุณากรอกข้อมูลเพื่อเข้าสู่ระบบ</div>';
                        }

                        if (!empty($login_error)) {
                            echo '<div class="alert alert-danger">' . htmlspecialchars($login_error) . '</div>';
                        }
                        ?>

                        <form method="post" action="" onsubmit="return validateForm();">
                            <div class="mb-3">
                                <label for="username" class="form-label">ชื่อผู้ใช้</label>
                                <input type="text" class="form-control" id="username" name="username" required maxlength="50">
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">รหัสผ่าน</label>
                                <input type="password" class="form-control" id="password" name="password" required minlength="6" maxlength="20">
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">เข้าสู่ระบบ</button>
                            </div>
                            <div class="text-center mt-3">
                                <!--  <p class="mb-0">ยังไม่มีบัญชี? <a href="#" class="link-primary">สมัครสมาชิก</a></p> -->
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>