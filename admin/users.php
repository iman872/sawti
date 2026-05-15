<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$success = '';
$error = '';

// إضافة مستخدم جديد
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $full_name = $_POST['full_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $cin = $_POST['cin'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'user';

    if (empty($full_name) || empty($email) || empty($password)) {
        $error = 'الرجاء ملء جميع الحقول الإلزامية';
    } else {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        try {
            $stmt = $pdo->prepare("INSERT INTO users (full_name, email, phone, cin, password, role) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$full_name, $email, $phone, $cin, $hashed_password, $role]);
            $success = 'تم إضافة المستخدم بنجاح';
        } catch (PDOException $e) {
            $error = 'البريد الإلكتروني مسجل بالفعل';
        }
    }
}

// حذف مستخدم
if (isset($_GET['delete'])) {
    $user_id = $_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role != 'admin'");
        $stmt->execute([$user_id]);
        $success = 'تم حذف المستخدم بنجاح';
    } catch (PDOException $e) {
        $error = 'لا يمكن حذف هذا المستخدم';
    }
}

// تحديث حالة المستخدم
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_status'])) {
    $user_id = $_POST['user_id'];
    $is_active = $_POST['is_active'] == 1 ? 0 : 1;
    try {
        $stmt = $pdo->prepare("UPDATE users SET is_active = ? WHERE id = ?");
        $stmt->execute([$is_active, $user_id]);
        $success = 'تم تحديث حالة المستخدم';
    } catch (PDOException $e) {
        $error = 'حدث خطأ';
    }
}

// جلب قائمة المستخدمين
$users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>إدارة المستخدمين - صوتي.ma</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Tajawal', sans-serif;
        }

        body {
            background: #f4f7fb;
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
            transition: all 0.3s;
        }

        .sidebar a:hover {
            background: #C1272D;
        }

        .content {
            margin-right: 270px;
            padding: 20px;
        }

        .user-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .user-info h5 {
            margin: 0 0 5px 0;
            color: #006233;
        }

        .user-info p {
            margin: 0;
            color: #666;
            font-size: 0.9rem;
        }

        .badge-role {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
        }

        .badge-admin {
            background: #C1272D;
            color: white;
        }

        .badge-user {
            background: #006233;
            color: white;
        }

        .btn-action {
            padding: 8px 15px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-view {
            background: #3b82f6;
            color: white;
        }

        .btn-delete {
            background: #ef4444;
            color: white;
        }

        .status-active {
            background: #10b981;
            color: white;
            padding: 3px 10px;
            border-radius: 15px;
            font-size: 0.75rem;
        }

        .status-inactive {
            background: #6b7280;
            color: white;
            padding: 3px 10px;
            border-radius: 15px;
            font-size: 0.75rem;
        }
    </style>
</head>

<body>
    <div class="sidebar p-3">
        <h3 class="text-center mb-4"><i class="fas fa-microphone-alt"></i> صوتي.ma</h3>
        <a href="dashboard.php"><i class="fas fa-chart-line"></i> الرئيسية</a>
        <a href="complaints.php"><i class="fas fa-list"></i> الشكايات</a>
        <a href="users.php" class="active" style="background:#C1272D;"><i class="fas fa-users"></i> المستخدمون</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> تسجيل الخروج</a>
    </div>

    <div class="content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-users"></i> إدارة المستخدمين</h2>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addUserModal">
                <i class="fas fa-plus"></i> إضافة مستخدم
            </button>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="row">
            <?php foreach ($users as $user): ?>
                <div class="col-md-6">
                    <div class="user-card">
                        <div class="user-info">
                            <h5><?php echo htmlspecialchars($user['full_name']); ?></h5>
                            <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($user['email']); ?></p>
                            <p><i class="fas fa-phone"></i> <?php echo htmlspecialchars($user['phone'] ?? 'غير متوفر'); ?></p>
                            <p><i class="fas fa-id-card"></i> <?php echo htmlspecialchars($user['cin'] ?? 'غير متوفر'); ?></p>
                        </div>
                        <div class="text-end">
                            <span class="badge-role badge-<?php echo $user['role']; ?>">
                                <?php echo $user['role'] === 'admin' ? 'مسؤول' : 'مستخدم'; ?>
                            </span>
                            <span class="<?php echo $user['is_active'] ? 'status-active' : 'status-inactive'; ?>">
                                <?php echo $user['is_active'] ? 'نشط' : 'معطل'; ?>
                            </span>
                            <div class="mt-2">
                                <?php if ($user['role'] !== 'admin'): ?>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                        <input type="hidden" name="is_active" value="<?php echo $user['is_active']; ?>">
                                        <button type="submit" name="toggle_status" class="btn btn-sm btn-view">
                                            <?php echo $user['is_active'] ? '<i class="fas fa-ban"></i> تعطيل' : '<i class="fas fa-check"></i> تفعيل'; ?>
                                        </button>
                                    </form>
                                    <a href="users.php?delete=<?php echo $user['id']; ?>" 
                                       class="btn btn-sm btn-delete"
                                       onclick="return confirm('هل أنت متأكد من حذف هذا المستخدم؟');">
                                        <i class="fas fa-trash"></i> حذف
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if (empty($users)): ?>
            <div class="text-center py-5">
                <i class="fas fa-users" style="font-size: 4rem; color: #ccc;"></i>
                <p class="mt-3 text-muted">لا يوجد مستخدمون</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Modal إضافة مستخدم -->
    <div class="modal fade" id="addUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">إضافة مستخدم جديد</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">الاسم الكامل <span class="text-danger">*</span></label>
                            <input type="text" name="full_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">البريد الإلكتروني <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">رقم الهاتف</label>
                            <input type="text" name="phone" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">رقم بطاقة التعريف الوطنية</label>
                            <input type="text" name="cin" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">كلمة المرور <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">الدور</label>
                            <select name="role" class="form-control">
                                <option value="user">مستخدم</option>
                                <option value="admin">مسؤول</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="add_user" class="btn btn-success">إضافة</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
