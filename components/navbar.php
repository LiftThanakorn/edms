<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check session timeout (15 minutes)
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 900)) {
    header("Location: /edms/logout.php");
    exit();
}
$_SESSION['last_activity'] = time();
?>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">ระบบจัดการเอกสาร</a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text text-white me-3">
                    ยินดีต้อนรับ, <u><strong><?php echo $_SESSION['username']; ?></strong></u>
                </span>
                <a href="/edms/logout.php" class="btn btn-outline-light">
                    <i class="bi bi-box-arrow-right me-1"></i>ออกจากระบบ
                </a>
            </div>
        </div>
    </nav>

<!-- <script>
var inactivityTime = function () {
    var time;
    window.onload = resetTimer;
    document.onmousemove = resetTimer;
    document.onkeydown = resetTimer;

    function logout() {
        window.location.href = "/edms/logout.php";
    }

    function resetTimer() {
        clearTimeout(time);
        time = setTimeout(logout, 900000) // 15 minutes
    }
};
inactivityTime();
</script> -->