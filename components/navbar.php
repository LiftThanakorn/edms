<?php
// ตรวจสอบว่ามีการ start session หรือยัง
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">ระบบจัดการเอกสาร</a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text text-white me-3">
                    ยินดีต้อนรับ, <u><strong><?php echo $_SESSION['username']; ?></strong></u>
                </span>
<!--                 <a href="/edms/logout.php" class="btn btn-outline-light">
                    <i class="bi bi-box-arrow-right me-1"></i>ออกจากระบบ
                </a> -->
            </div>
        </div>
    </nav>