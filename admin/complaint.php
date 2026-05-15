
<?php
session_start();
require_once '../config.php';
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// تحديث حالة الشكاية
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $stmt = $pdo->prepare("UPDATE complaints SET status = ?, admin_response = ? WHERE id = ?");
    $stmt->execute([$_POST['status'], $_POST['response'], $_POST['complaint_id']]);
    $success = "تم تحديث الحالة بنجاح";
}

$complaints = $pdo->query("SELECT c.*, d.name_ar as dept_name FROM complaints c LEFT JOIN departments d ON c.department_id = d.id ORDER BY c.created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>إدارة الشكايات</title>
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
            position: fixed;
            width: 250px;
        }

        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 15px 20px;
            border-radius: 12px;
        }

        .sidebar a:hover {
            background: #C1272D;
        }

        .content {
            margin-right: 270px;
            padding: 20px;
        }

        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
        }

        .status-pending {
            background: #f59e0b;
            color: white;
        }

.status-processing {
            background: #3b82f6;
            color: white;
        }

        .status-done {
            background: #10b981;
            color: white;
        }

        .status-rejected {
            background: #ef4444;
            color: white;
        }
    </style>
</head>

<body>
    <div class="sidebar p-3">
        <h3 class="text-center mb-4">صوتي.ma</h3>
        <a href="dashboard.php"><i class="fas fa-chart-line"></i> الرئيسية</a>
        <a href="complaints.php" class="active" style="background:#C1272D;"><i class="fas fa-list"></i> الشكايات</a>
        <a href="users.php"><i class="fas fa-users"></i> المستخدمون</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> تسجيل الخروج</a>
    </div>
    <div class="content">
        <h2><i class="fas fa-list"></i> قائمة الشكايات</h2>
        <?php if (isset($success))
            echo "<div class='alert alert-success'>$success</div>"; ?>

        <div class="table-responsive mt-4">
            <table class="table table-bordered bg-white">
                <thead class="table-success">
                    <tr>
                        <th>رقم التتبع</th>
                        <th>الإدارة</th>
                        <th>الموضوع</th>
                        <th>الحالة</th>
                        <th>تاريخ الإرسال</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($complaints as $c): ?>
                        <tr>
                            <td>
                                <?php echo $c['tracking_code']; ?>
                            </td>
                            <td>
                                <?php echo $c['dept_name']; ?>
                            </td>
                            <td>
                                <?php echo mb_substr($c['title'], 0, 50); ?>
                            </td>
                            <td><span class="status-badge status-<?php echo $c['status']; ?>">
                                    <?php echo $c['status']; ?>
                                </span></td>
                            <td>
                                <?php echo date('Y-m-d', strtotime($c['created_at'])); ?>
                            </td>
                            <td><button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#modal<?php echo $c['id']; ?>">عرض وتعديل</button></td>
                        </tr>

                        <!-- Modal -->
                        <div class="modal fade" id="modal<?php echo $c['id']; ?>" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <form method="POST">
                                        <div class="modal-header">
                                            <h5>
                                                <?php echo $c['title']; ?>
                                            </h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" name="complaint_id" value="<?php echo $c['id']; ?>">
                                            <div class="mb-3"><label>الوصف الكامل</label><textarea class="form-control"
                                                    readonly rows="4"><?php echo $c['description']; ?></textarea></div>
                                            <div class="mb-3"><label>الرد (اختياري)</label><textarea name="response"
                                                    class="form-control"
                                                    rows="3"><?php echo $c['admin_response']; ?></textarea></div>
                                            <div class="mb-3"><label>تغيير الحالة</label>
<select name="status" class="form-control">
                                                    <option value="pending" <?php echo $c['status'] == 'pending' ? 'selected' : ''; ?>>قيد الانتظار</option>
                                                    <option value="processing" <?php echo $c['status'] == 'processing' ? 'selected' : ''; ?>>قيد المعالجة</option>
                                                    <option value="done" <?php echo $c['status'] == 'done' ? 'selected' : ''; ?>>تمت المعالجة</option>
                                                    <option value="rejected" <?php echo $c['status'] == 'rejected' ? 'selected' : ''; ?>>مرفوض</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer"><button type="submit" name="update_status"
                                                class="btn btn-success">حفظ التغييرات</button></div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>