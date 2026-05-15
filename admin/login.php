<?php
session_start();
require_once '../config.php';

$error = '';

// معالجة نموذج تسجيل الدخول
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'الرجاء إدخال البريد الإلكتروني وكلمة المرور';
    } else {
        // البحث عن المستخدم في قاعدة البيانات
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND role = 'admin'");
        $stmt->execute([$email]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password'])) {
            // تسجيل الدخول بنجاح
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['full_name'];
            $_SESSION['admin_role'] = $admin['role'];
            header('Location: dashboard.php');
            exit();
        } else {
            $error = 'البريد الإلكتروني أو كلمة المرور غير صحيحة';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - صوتي.ma</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Tajawal', sans-serif;
        }
        body {
            background: linear-gradient(135deg, #006233 0%, #004422 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            overflow: hidden;
            max-width: 450px;
            width: 90%;
        }
        .login-header {
            background: #006233;
            color: white;
            padding: 30px;
            text-align: center;
        }
        .login-header h1 {
            margin: 0;
            font-size: 2rem;
        }
        .login-body {
            padding: 40px;
        }
        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
        }
        .form-control:focus {
            border-color: #006233;
            box-shadow: none;
        }
        .btn-login {
            background: #006233;
            color: white;
            border: none;
            border-radius: 10px;
            padding: 12px;
            width: 100%;
            font-size: 1.1rem;
            font-weight: bold;
        }
        .btn-login:hover {
            background: #004422;
        }
        .input-group-text {
            background: #f8f9fa;
            border: 2px solid #e0e0e0;
            border-right: none;
            border-radius: 10px 0 0 10px;
        }
        .input-group .form-control {
            border-radius: 0 10px 10px 0;
        }
        .error-message {
            background: #fff3cd;
            color: #856404;
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="login-card">
        <div class="login-header">
            <h1><i class="fas fa-microphone-alt"></i> صوتي.ma</h1>
            <p class="mb-0">لوحة التحكم - تسجيل الدخول</p>
        </div>
        <div class="login-body">
            <?php if ($error): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-4">
                    <label class="form-label">البريد الإلكتروني</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" name="email" class="form-control" placeholder="admin@sawti.ma" required
                            value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">كلمة المرور</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" name="password" class="form-control" placeholder="********" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-login">
                    <i class="fas fa-sign-in-alt"></i> تسجيل الدخول
                </button>
            </form>

<div class="text-center mt-4">
                <small class="text-muted">
                    <i class="fas fa-info-circle"></i> الحساب الافتراضي: admin@sawti.ma / admin123
                </small>
            </div>
            
            <div class="text-center mt-2">
                <a href="../index.html" class="text-decoration-none">
                    <i class="fas fa-home"></i> العودة للصفحة الرئيسية
                </a>
            </div>
        </div>
    </div>
</body>

</html>
