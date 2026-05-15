<?php
session_start();
require_once '../config.php';
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// إحصائيات
$totalComplaints = $pdo->query("SELECT COUNT(*) FROM complaints")->fetchColumn();
$pendingComplaints = $pdo->query("SELECT COUNT(*) FROM complaints WHERE status = 'pending'")->fetchColumn();
$resolvedComplaints = $pdo->query("SELECT COUNT(*) FROM complaints WHERE status = 'resolved'")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>لوحة التحكم - صوتي.ma</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: #f4f7fb;
            font-family: 'Tajawal', sans-serif;
        }

        .sidebar {
            background: #006233;
            min-height: 100vh;
            color: white;
        }

        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 15px 20px;
            margin: 5px 0;
            border-radius: 12px;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: #C1272D;
        }

        .stat-card {
            background: white;
            border-radius: 20px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .stat-number {
            font-size: 2rem;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2 sidebar p-3">
                <h3 class="text-center mb-4">صوتي.ma</h3>
                <a href="dashboard.php" class="active"><i class="fas fa-chart-line"></i> الرئيسية</a>
                <a href="complaints.php"><i class="fas fa-list"></i> الشكايات</a>
                <a href="users.php"><i class="fas fa-users"></i> المستخدمون</a>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> تسجيل الخروج</a>
            </div>
            <div class="col-md-10 p-4">
                <h2><i class="fas fa-tachometer-alt"></i> لوحة التحكم</h2>
                <div class="row mt-4">
                    <div class="col-md-4">
                        <div class="stat-card">
                            <div class="stat-number">
                                <?php echo $totalComplaints; ?>
                            </div>
                            <div>إجمالي الشكايات</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card">
                            <div class="stat-number">
                                <?php echo $pendingComplaints; ?>
                            </div>
                            <div>قيد الانتظار</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card">
                            <div class="stat-number">
                                <?php echo $resolvedComplaints; ?>
                            </div>
                            <div>تمت المعالجة</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>