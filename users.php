<?php
session_start();

// Check for login and admin privileges
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require_once 'config.php';

// Fetch all users
$users = $pdo->query("SELECT * FROM edms_users")->fetchAll(PDO::FETCH_ASSOC);

// Handle user deletion
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $stmt = $pdo->prepare("DELETE FROM edms_users WHERE user_id = ?");
    $stmt->execute([$_GET['id']]);
    $_SESSION['message'] = "User deleted successfully.";
    $_SESSION['message_type'] = "success";
    header("Location: users.php");
    exit();
}

// Handle adding a user
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_user'])) {
    try {
        $stmt = $pdo->prepare("INSERT INTO edms_users (username, password, first_name, last_name, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['username'],
            password_hash('123456', PASSWORD_ARGON2ID),
            $_POST['first_name'],
            $_POST['last_name'],
            $_POST['role']
        ]);
        $_SESSION['message'] = "User added successfully. Default password is '123456'.";
        $_SESSION['message_type'] = "success";
        header("Location: users.php");
        exit();
    } catch (Exception $e) {
        $_SESSION['message'] = "Error: " . $e->getMessage();
        $_SESSION['message_type'] = "danger";
    }
}

// Handle editing a user
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_user'])) {
    try {
        $stmt = $pdo->prepare("UPDATE edms_users SET username = ?, first_name = ?, last_name = ?, role = ? WHERE user_id = ?");
        $stmt->execute([
            $_POST['username'],
            $_POST['first_name'],
            $_POST['last_name'],
            $_POST['role'],
            $_POST['user_id']
        ]);
        $_SESSION['message'] = "User updated successfully.";
        $_SESSION['message_type'] = "success";
        header("Location: users.php");
        exit();
    } catch (Exception $e) {
        $_SESSION['message'] = "Error: " . $e->getMessage();
        $_SESSION['message_type'] = "danger";
    }
}

// Handle password change for a user
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change_password'])) {
    try {
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        $user_id = $_POST['user_id'];

        if ($new_password === $confirm_password) {
            $hashed_password = password_hash($new_password, PASSWORD_ARGON2ID); // ใช้ Argon2
            $stmt = $pdo->prepare("UPDATE edms_users SET password = ? WHERE user_id = ?");
            $stmt->execute([$hashed_password, $user_id]);
            $_SESSION['message'] = "Password changed successfully.";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Passwords do not match.";
            $_SESSION['message_type'] = "danger";
        }
    } catch (Exception $e) {
        $_SESSION['message'] = "Error: " . $e->getMessage();
        $_SESSION['message_type'] = "danger";
    }
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <?php require_once './components/header.php'; ?>
    <title>จัดการผู้ใช้</title>
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
</head>

<body>
    <?php require_once './components/navbar.php'; ?>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-3">
                <?php require_once './components/sidebar.php'; ?>
            </div>
            <div class="col-md-9">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/edms/index.php">หน้าหลัก</a></li>
                        <li class="breadcrumb-item active" aria-current="page">จัดการผู้ใช้</li>
                    </ol>
                </nav>

                <div class="card shadow">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">จัดการผู้ใช้</h4>
                        <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#addUserModal">
                            <i class="bi bi-plus-lg"></i> เพิ่มผู้ใช้
                        </button>
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
                            <table id="usersTable" class="table table-bordered table-striped table-hover">
                                <thead class="table-primary">
                                    <tr>
                                        <th>รหัสผู้ใช้</th>
                                        <th>ชื่อผู้ใช้</th>
                                        <th>ชื่อ</th>
                                        <th>นามสกุล</th>
                                        <th>สิทธิ์การเข้าถึง</th>
                                        <th class="text-center">จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $user): ?>
                                        <tr>
                                            <td><?php echo $user['user_id']; ?></td>
                                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                                            <td><?php echo htmlspecialchars($user['first_name']); ?></td>
                                            <td><?php echo htmlspecialchars($user['last_name']); ?></td>
                                            <td><?php echo htmlspecialchars($user['role']); ?></td>
                                            <td class="text-center">
                                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editUserModal" data-id="<?php echo $user['user_id']; ?>" data-username="<?php echo htmlspecialchars($user['username']); ?>" data-firstname="<?php echo htmlspecialchars($user['first_name']); ?>" data-lastname="<?php echo htmlspecialchars($user['last_name']); ?>" data-role="<?php echo htmlspecialchars($user['role']); ?>">แก้ไข</button>
                                                <a href="users.php?action=delete&id=<?php echo $user['user_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบผู้ใช้?');">ลบ</a>
                                                <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#changePasswordModal" data-id="<?php echo $user['user_id']; ?>" data-username="<?php echo htmlspecialchars($user['username']); ?>">เปลี่ยนรหัสผ่าน</button>
                                            </td>
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

    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addUserModalLabel">เพิ่มผู้ใช้ใหม่</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="" method="POST">
                        <div class="mb-3">
                            <label class="form-label">ชื่อผู้ใช้</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">ชื่อ</label>
                            <input type="text" name="first_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">นามสกุล</label>
                            <input type="text" name="last_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">สิทธิ์การเข้าถึง</label>
                            <select name="role" class="form-select" required>
                                <option value="admin">Admin</option>
                                <option value="user">User</option>
                            </select>
                        </div>
                        <button type="submit" name="add_user" class="btn btn-primary">เพิ่มผู้ใช้</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel">แก้ไขผู้ใช้</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="" method="POST">
                        <input type="hidden" name="user_id" id="editUserId">
                        <div class="mb-3">
                            <label class="form-label">ชื่อผู้ใช้</label>
                            <input type="text" name="username" id="editUsername" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">ชื่อ</label>
                            <input type="text" name="first_name" id="editFirstName" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">นามสกุล</label>
                            <input type="text" name="last_name" id="editLastName" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">สิทธิ์การเข้าถึง</label>
                            <select name="role" id="editRole" class="form-select" required>
                                <option value="admin">Admin</option>
                                <option value="user">User</option>
                            </select>
                        </div>
                        <button type="submit" name="edit_user" class="btn btn-warning">แก้ไขผู้ใช้</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Change Password Modal -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="changePasswordModalLabel">เปลี่ยนรหัสผ่าน</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="" method="POST">
                        <input type="hidden" name="user_id" id="changeUserId">
                        <div class="mb-3">
                            <label class="form-label">รหัสผ่านใหม่:</label>
                            <input type="password" name="new_password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">ยืนยันรหัสผ่านใหม่:</label>
                            <input type="password" name="confirm_password" class="form-control" required>
                        </div>
                        <button type="submit" name="change_password" class="btn btn-info">เปลี่ยนรหัสผ่าน</button>
                    </form>
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
            $('#usersTable').DataTable({
                "order": [[0, "asc"]],
                "pageLength": 25,
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/th.json"
                },
                "responsive": true,
                "dom": "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                        "<'row'<'col-sm-12'tr>>" +
                        "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                "lengthMenu": [
                    [10, 25, 50, -1],
                    [10, 25, 50, "ทั้งหมด"]
                ]
            });
        });

        // Function to populate edit modal
        const editUserModal = document.getElementById('editUserModal');
        editUserModal.addEventListener('show.bs.modal', event => {
            const button = event.relatedTarget; // Button that triggered the modal
            const userId = button.getAttribute('data-id');
            const username = button.getAttribute('data-username');
            const firstName = button.getAttribute('data-firstname');
            const lastName = button.getAttribute('data-lastname');
            const role = button.getAttribute('data-role');

            // Set the values in the form
            document.getElementById('editUserId').value = userId;
            document.getElementById('editUsername').value = username;
            document.getElementById('editFirstName').value = firstName;
            document.getElementById('editLastName').value = lastName;
            document.getElementById('editRole').value = role;
        });

        // Function to populate change password modal
        const changePasswordModal = document.getElementById('changePasswordModal');
        changePasswordModal.addEventListener('show.bs.modal', event => {
            const button = event.relatedTarget; // Button that triggered the modal
            const userId = button.getAttribute('data-id');

            // Set the user ID in the form
            document.getElementById('changeUserId').value = userId;
        });
    </script>
</body>

</html>