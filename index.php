<?php
// เริ่มต้นเซสชัน
session_start();

// ตรวจสอบการเข้าสู่ระบบ
if (!isset($_SESSION['user_id'])) {
    // หากยังไม่ได้เข้าสู่ระบบ ให้เปลี่ยนเส้นทางไปยังหน้า login.php
    header('Location: login.php');
    exit();
}

// เชื่อมต่อกับฐานข้อมูล
require_once 'config.php';

// Query เพื่อดึงข้อมูล
$user_count_query = "SELECT COUNT(*) FROM edms_users";
$category_count_query = "SELECT COUNT(*) FROM edms_work_categories";
$internal_in_count_query = "SELECT COUNT(*) FROM edms_internal_in_documents";
$internal_out_count_query = "SELECT COUNT(*) FROM edms_internal_out_documents";
$external_in_count_query = "SELECT COUNT(*) FROM edms_external_in_documents";
$external_out_count_query = "SELECT COUNT(*) FROM edms_external_out_documents";
$circular_count_query = "SELECT COUNT(*) FROM edms_circular_documents";
$command_count_query = "SELECT COUNT(*) FROM edms_command_documents";

// ดึงข��อมูลจากฐานข้อมูล
$user_count = $pdo->query($user_count_query)->fetchColumn();
$category_count = $pdo->query($category_count_query)->fetchColumn();
$internal_in_count = $pdo->query($internal_in_count_query)->fetchColumn();
$internal_out_count = $pdo->query($internal_out_count_query)->fetchColumn();
$external_in_count = $pdo->query($external_in_count_query)->fetchColumn();
$external_out_count = $pdo->query($external_out_count_query)->fetchColumn();
$circular_count = $pdo->query($circular_count_query)->fetchColumn();
$command_count = $pdo->query($command_count_query)->fetchColumn();
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <?php require_once 'components/header.php'; ?>
    <title>Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .stat-card {
            background: #fff;
            border-radius: 12px;
            padding: 24px;
            height: 100%;
            transition: all 0.3s ease;
            border: 1px solid rgba(0,0,0,0.1);
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .stat-icon {
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            margin-bottom: 16px;
        }
        .stat-icon i {
            font-size: 24px;
        }
        .stat-number {
            font-size: 32px;
            font-weight: 700;
            color: #2c3e50;
            margin: 8px 0;
            line-height: 1;
        }
        .stat-label {
            font-size: 14px;
            color: #64748b;
            font-weight: 500;
        }
        
        /* สีสำหรับไอคอนแต่ละประเภท */
        .bg-soft-primary {
            background: rgba(13, 110, 253, 0.1);
            color: #0d6efd;
        }
        .bg-soft-success {
            background: rgba(25, 135, 84, 0.1);
            color: #198754;
        }
        .bg-soft-warning {
            background: rgba(255, 193, 7, 0.1);
            color: #ffc107;
        }
        .chart-container {
            position: relative;
            height: 300px;
            margin: 20px 0;
        }
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        }
        .card-header {
            background: none;
            border-bottom: none;
            padding: 20px;
        }
        .card-header h5 {
            color: #2c3e50;
            font-size: 16px;
            font-weight: 600;
        }
    </style>
</head>

<body>
    <?php require_once 'components/navbar.php'; ?>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-3">
                <?php require_once 'components/sidebar.php'; ?>
            </div>

            <div class="col-md-9">
                <div class="row g-4">
                    <!-- หนังสือรับเข้าภายใน -->
                    <div class="col-md-4">
                        <a href="internal_in/index.php" class="text-decoration-none">
                            <div class="stat-card">
                                <div class="stat-icon bg-soft-success">
                                    <i class="bi bi-inbox-fill"></i>
                                </div>
                                <div class="stat-label">หนังสือรับเข้าภายใน</div>
                                <div class="stat-number"><?php echo number_format($internal_in_count); ?></div>
                            </div>
                        </a>
                    </div>

                    <!-- หนังสือส่งออกภายใน -->
                    <div class="col-md-4">
                        <a href="internal_out/index.php" class="text-decoration-none">
                            <div class="stat-card">
                                <div class="stat-icon bg-soft-success">
                                    <i class="bi bi-send-fill"></i>
                                </div>
                                <div class="stat-label">หนังสือส่งออกภายใน</div>
                                <div class="stat-number"><?php echo number_format($internal_out_count); ?></div>
                            </div>
                        </a>
                    </div>

                    <!-- หนังสือเวียน -->
                    <div class="col-md-4">
                        <a href="circular/index.php" class="text-decoration-none">
                            <div class="stat-card">
                                <div class="stat-icon bg-soft-primary">
                                    <i class="bi bi-repeat"></i>
                                </div>
                                <div class="stat-label">หนังสือเวียน</div>
                                <div class="stat-number"><?php echo number_format($circular_count); ?></div>
                            </div>
                        </a>
                    </div>

                    <!-- หนังสือรับเข้าภายนอก 
                    <div class="col-md-4">
                        <a href="external_in/index.php" class="text-decoration-none">
                            <div class="stat-card">
                                <div class="stat-icon bg-soft-warning">
                                    <i class="bi bi-arrow-down-circle"></i>
                                </div>
                                <div class="stat-label">หนังสือรับเข้าภายนอก</div>
                                <div class="stat-number"><?php echo number_format($external_in_count); ?></div>
                            </div>
                        </a>
                    </div>

                    หนังสือส่งออกภายนอก 
                    <div class="col-md-4">
                        <a href="external_out/index.php" class="text-decoration-none">
                            <div class="stat-card">
                                <div class="stat-icon bg-soft-warning">
                                    <i class="bi bi-arrow-up-circle"></i>
                                </div>
                                <div class="stat-label">หนังสือส่งออกภายนอก</div>
                                <div class="stat-number"><?php echo number_format($external_out_count); ?></div>
                            </div>
                        </a>
                    </div>

                    หนังสือสั่งการ 
                    <div class="col-md-4">
                        <a href="command/index.php" class="text-decoration-none">
                            <div class="stat-card">
                                <div class="stat-icon bg-soft-primary">
                                    <i class="bi bi-file-earmark-text"></i>
                                </div>
                                <div class="stat-label">หนังสือสั่งการ</div>
                                <div class="stat-number"><?php echo number_format($command_count); ?></div>
                            </div>
                        </a>
                    </div>-->
                </div>
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card shadow-sm">
                            <div class="card-header">
                                <h5 class="mb-0">สถิติเอกสารทั้งหมด</h5>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="documentsChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card shadow-sm">
                            <div class="card-header">
                                <h5 class="mb-0">เอกสารภายใน/ภายนอก</h5>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="documentTypesChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // กราฟวงกลมแสดงสัดส่วนเอกสารทั้งหมด
    const ctx1 = document.getElementById('documentsChart').getContext('2d');
    new Chart(ctx1, {
        type: 'doughnut',
        data: {
            labels: [
                'หนังสือรับเข้าภายใน',
                'หนังสือส่งออกภายใน',
                'หนังสือรับเข้าภายนอก',
                'หนังสือส่งออกภายนอก',
                'หนังสือเวียน',
                'หนังสือสั่งการ'
            ],
            datasets: [{
                data: [
                    <?php echo $internal_in_count; ?>,
                    <?php echo $internal_out_count; ?>,
                    <?php echo $external_in_count; ?>,
                    <?php echo $external_out_count; ?>,
                    <?php echo $circular_count; ?>,
                    <?php echo $command_count; ?>
                ],
                backgroundColor: [
                    'rgba(25, 135, 84, 0.9)',   // success
                    'rgba(25, 135, 84, 0.7)',   // success lighter
                    'rgba(255, 193, 7, 0.9)',   // warning
                    'rgba(255, 193, 7, 0.7)',   // warning lighter
                    'rgba(13, 110, 253, 0.9)',  // primary
                    'rgba(13, 110, 253, 0.7)'   // primary lighter
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        padding: 20,
                        font: {
                            size: 13
                        },
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                }
            }
        }
    });

    // กราฟแท่งเปรียบเทียบเอกสารภายใน/ภายนอก
    const ctx2 = document.getElementById('documentTypesChart').getContext('2d');
    new Chart(ctx2, {
        type: 'bar',
        data: {
            labels: ['เอกสารรับเข้า', 'เอกสารส่งออก', 'เอกสารอื่นๆ'],
            datasets: [
                {
                    label: 'ภายใน',
                    data: [
                        <?php echo $internal_in_count; ?>,
                        <?php echo $internal_out_count; ?>,
                        <?php echo $circular_count; ?>
                    ],
                    backgroundColor: 'rgba(25, 135, 84, 0.8)'  // success
                },
                {
                    label: 'ภายนอก',
                    data: [
                        <?php echo $external_in_count; ?>,
                        <?php echo $external_out_count; ?>,
                        <?php echo $command_count; ?>
                    ],
                    backgroundColor: 'rgba(255, 193, 7, 0.8)'  // warning
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    grid: {
                        display: false
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)',
                        drawBorder: false
                    },
                    ticks: {
                        font: {
                            size: 12
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                    align: 'start',
                    labels: {
                        usePointStyle: true,
                        pointStyle: 'circle',
                        padding: 20,
                        font: {
                            size: 13
                        }
                    }
                }
            },
            barThickness: 25,
            borderRadius: 6
        }
    });
    </script>
</body>

</html>