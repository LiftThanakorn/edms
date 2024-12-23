<?php
// ตรวจสอบว่ามีการ start session หรือยัง
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (isset($_SESSION['first_name']) && isset($_SESSION['last_name'])) {
    $first_name = htmlspecialchars($_SESSION['first_name']);
    $last_name = htmlspecialchars($_SESSION['last_name']);
}
// กำหนดหน้าปัจจุบัน เพื่อเน้นเมนูที่กำลังใช้งาน
$current_page = $_SERVER['REQUEST_URI']; // ตรวจสอบ URI เต็ม

// ตั้งค่าภาษาไทย
setlocale(LC_TIME, 'th_TH.UTF-8'); // ตั้งค่าภาษาไทย
date_default_timezone_set('Asia/Bangkok'); // ตั้งค่าเขตเวลาเป็น Bangkok

?>
<div class="list-group mb-3">
    <div class="list-group-item text-center bg-light">
        <div class="mb-2">
            <i class="bi bi-person-circle fs-2 text-primary"></i>
        </div>
        <h6 class="mb-1 fw-bold"><?php echo $first_name . ' ' . $last_name; ?></h6>
        <small class="text-muted"><?php echo isset($_SESSION['role']) ? $_SESSION['role'] : 'ผู้ใช้งาน'; ?></small>
    </div>
</div>
<div class="list-group mt-3">
    <!-- แสดงวันเดือนปีและเวลาปัจจุบันแบบเรียลไทม์ -->
    <div class="list-group-item" id="realtime">
        <strong id="currentDateTime"></strong>
    </div>
</div>

<div class="list-group mt-3">
    <!-- เมนู หน้าหลัก -->
    <a href="/edms/index.php" class="list-group-item list-group-item-action <?php echo (strpos($current_page, '/edms/index.php') !== false) ? 'active' : ''; ?>">
        <i class="bi-grid-fill me-2"></i>หน้าหลัก
    </a>

    <!-- เมนู หนังสือส่งออกภายใน -->
    <a href="/edms/internal_out/index.php" class="list-group-item list-group-item-action <?php echo (strpos($current_page, '/edms/internal_out/index.php') !== false || strpos($current_page, '/edms/internal_out/create.php') !== false || strpos($current_page, '/edms/internal_out/edit.php') !== false) ? 'active' : ''; ?>">
        <i class="bi bi-send me-2"></i>หนังสือส่งออกภายใน
    </a>
    <!-- เมนู หนังสือรับเข้าภายใน -->
    <a href="/edms/internal_in/index.php" class="list-group-item list-group-item-action <?php echo (strpos($current_page, '/edms/internal_in/index.php') !== false || strpos($current_page, '/edms/internal_in/create.php') !== false || strpos($current_page, '/edms/internal_in/edit.php') !== false) ? 'active' : ''; ?>">
        <i class="bi bi-inbox me-2"></i>หนังสือรับเข้าภายใน
    </a>
    <!-- 
    เมนู หนังสือรับเข้าภายนอก 
    <a href="/edms/external_in/index.php" class="list-group-item list-group-item-action <?php echo (strpos($current_page, '/edms/external_in/index.php') !== false || strpos($current_page, '/edms/external_in/create.php') !== false || strpos($current_page, '/edms/external_in/edit.php') !== false) ? 'active' : ''; ?>">
        <i class="bi bi-arrow-down me-2"></i>หนังสือบเข้าภายนอก
    </a>

     เมนู หนังสือส่งออกภายนอก 
    <a href="/edms/external_out/index.php" class="list-group-item list-group-item-action <?php echo (strpos($current_page, '/edms/external_out/index.php') !== false || strpos($current_page, '/edms/external_out/create.php') !== false || strpos($current_page, '/edms/external_out/edit.php') !== false) ? 'active' : ''; ?>">
        <i class="bi bi-arrow-up me-2"></i>หนังสือส่งออกภายนอก
    </a> 
    -->
    <!-- เมนู หนังสือเวียน -->
    <a href="/edms/circular/index.php" class="list-group-item list-group-item-action <?php echo (strpos($current_page, '/edms/circular/index.php') !== false || strpos($current_page, '/edms/circular/create.php') !== false || strpos($current_page, '/edms/circular/edit.php') !== false) ? 'active' : ''; ?>">
        <i class="bi bi-repeat me-2"></i>หนังสือเวียน
    </a>

    <!-- เมนู หนังสือสั่งการ 
    <a href="/edms/command/index.php" class="list-group-item list-group-item-action <?php echo (strpos($current_page, '/edms/command/index.php') !== false || strpos($current_page, '/edms/command/create.php') !== false || strpos($current_page, '/edms/command/edit.php') !== false) ? 'active' : ''; ?>">
        <i class="bi bi-command me-2"></i>หนังสือสั่งการ
    </a> -->

        <!-- เมนู ทะเบียนการรับ-ส่งงานกำหนดตำแหน่ง -->
        <a href="/edms/jobassignment/index.php" class="list-group-item list-group-item-action <?php echo (strpos($current_page, '/edms/jobassignment/') !== false) ? 'active' : ''; ?>">
        <i class="bi bi-graph-up-arrow me-2"></i>ทะเบียนการรับ-ส่งงานกำหนดตำแหน่ง
    </a>

</div>


<div class="list-group mt-3">
    <!-- กลุ่มใหม่ สำหรับ ทะเบียนการรับ-ส่งงานกำหนดตำแหน่ง และ คำขอใบรับรอง -->
    <div class="list-group-item list-group-item-secondary">
        <strong><i class="bi bi-file-earmark-check me-2"></i>อื่น ๆ</strong>
    </div>

    <!-- เมนู คำขอใบรับรอง -->
    <a href="/edms/certificaterequests/index.php" class="list-group-item list-group-item-action <?php echo (strpos($current_page, '/edms/certificaterequests/index.php') !== false || strpos($current_page, '/edms/certificaterequests/create.php') !== false || strpos($current_page, '/edms/certificaterequests/edit.php') !== false) ? 'active' : ''; ?>">
        <i class="bi bi-file-earmark-text me-2"></i>คำขอใบรับรอง
    </a>
        <!-- เมนู ทะเบียนคำขอบัตรประจำตัว -->
        <a href="/edms/idcardrequests/index.php" class="list-group-item list-group-item-action <?php echo (strpos($current_page, '/edms/idcardrequests/') !== false) ? 'active' : ''; ?>">
        <i class="bi bi-person-vcard me-2"></i>ทะเบียนคำขอบัตรประจำตัว
    </a>
</div>

<?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
    <div class="list-group mt-3">
        <div class="list-group-item list-group-item-secondary">
            <strong><i class="bi bi-gear me-2"></i>การจัดการระบบ</strong>
        </div>
        <a href="/edms/work_categories.php" class="list-group-item list-group-item-action <?php echo (strpos($_SERVER['REQUEST_URI'], 'work_categories.php') !== false) ? 'active' : ''; ?>">
            <i class="bi bi-tags me-2"></i>หมวดหมู่งาน
        </a>
        <!-- เมนู จัดการผู้ใช้ -->
        <a href="/edms/users.php" class="list-group-item list-group-item-action <?php echo (strpos($current_page, '/edms/users.php') !== false) ? 'active' : ''; ?>">
            <i class="bi bi-person-gear me-2"></i>จัดการผู้ใช้
        </a>
    </div>
<?php endif; ?>

<div class="list-group mt-3">
    <div class="list-group-item list-group-item-secondary">
        <strong><i class="bi bi-person me-2"></i>สำหรับผู้ใช้</strong>
    </div>
    <!-- เมนู แก้ไขรหัสผ่าน -->
    <a href="/edms/edit_password.php" class="list-group-item list-group-item-action <?php echo (strpos($current_page, '/edms/edit_password.php') !== false) ? 'active' : ''; ?>">
        <i class="bi bi-lock me-2"></i>แก้ไขรหัสผ่าน
    </a>
</div>


<div class="mt-3">
    <a href="/edms/logout.php" class="btn btn-outline-danger w-100">
        <i class="bi bi-box-arrow-right me-2"></i>ออกจากระบบ
    </a>
</div>
<script>
    function updateDateTime() {
        const now = new Date();
        const options = {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false,
            timeZone: 'Asia/Bangkok'
        };
        document.getElementById('currentDateTime').innerHTML = now.toLocaleString('th-TH', options) + ' น.';
    }
    setInterval(updateDateTime, 1000); // อัปเดตทุกวินาที
    updateDateTime(); // เรียกใช้ครั้งแรก
</script>


<style>
    .list-group-item.active {
        background-color: #007bff;
        border-color: #007bff;
        color: white;
    }

    .list-group-item-action.small {
        padding: 0.5rem 1.5rem;
        font-size: 0.9rem;
    }
</style>