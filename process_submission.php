<?php
session_start();
require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: form.php');
    exit;
}

$departmentId = filter_input(INPUT_POST, 'department_id', FILTER_VALIDATE_INT);
$title = trim($_POST['title'] ?? '');
$requestType = trim($_POST['request_type'] ?? '');
$mainTopic = trim($_POST['main_topic'] ?? '');
$regionId = filter_input(INPUT_POST, 'region_id', FILTER_VALIDATE_INT);
$provinceId = filter_input(INPUT_POST, 'province_id', FILTER_VALIDATE_INT);
$communeId = filter_input(INPUT_POST, 'commune_id', FILTER_VALIDATE_INT);
$address = trim($_POST['address'] ?? '');
$description = trim($_POST['description'] ?? '');
$captchaInput = trim($_POST['captcha_input'] ?? '');
$captchaSaved = $_SESSION['captcha_code'] ?? null;
$termsAccepted = isset($_POST['terms']);
$action = trim($_POST['action'] ?? 'submit');

$errors = [];

if (!$departmentId) {
    $errors[] = 'تعذر تحديد الإدارة المستهدفة.';
}
if ($title === '') {
    $errors[] = 'يرجى إدخال عنوان موجز للطلب.';
}
if ($requestType === '') {
    $errors[] = 'يرجى اختيار نوع الطلب.';
}
if ($mainTopic === '') {
    $errors[] = 'يرجى اختيار الموضوع الرئيسي.';
}
if (!$regionId) {
    $errors[] = 'يرجى اختيار الجهة.';
}
if (!$provinceId) {
    $errors[] = 'يرجى اختيار الإقليم / العمالة.';
}
if (!$communeId) {
    $errors[] = 'يرجى اختيار الجماعة.';
}
if ($description === '') {
    $errors[] = 'يرجى كتابة وصف تفصيلي للطلب.';
}
if (!$termsAccepted) {
    $errors[] = 'يرجى الموافقة على شروط الاستخدام.';
}
if ($captchaSaved === null || $captchaInput === '' || $captchaInput !== (string)$captchaSaved) {
    $errors[] = 'رمز التحقق غير صحيح. حاول مرة أخرى.';
}

$allowedMime = [
    'application/pdf', 'image/jpeg', 'image/png', 'image/gif',
    'audio/mpeg', 'audio/mp3', 'audio/wav', 'audio/aac', 'audio/amr',
    'audio/x-m4a', 'audio/3gpp', 'video/mp4', 'video/quicktime',
    'video/x-msvideo', 'video/mpeg'
];

$attachments = [];
if (!empty($_FILES['attachments']) && is_array($_FILES['attachments']['name'])) {
    for ($i = 0; $i < count($_FILES['attachments']['name']); $i++) {
        if ($_FILES['attachments']['error'][$i] === UPLOAD_ERR_NO_FILE) {
            continue;
        }
        if ($_FILES['attachments']['error'][$i] !== UPLOAD_ERR_OK) {
            $errors[] = 'حدث خطأ أثناء رفع الملف: ' . $_FILES['attachments']['name'][$i];
            continue;
        }

        $size = $_FILES['attachments']['size'][$i];
        if ($size > 5 * 1024 * 1024) {
            $errors[] = 'حجم الملف أكبر من 5 ميغابايت: ' . $_FILES['attachments']['name'][$i];
            continue;
        }

        $tmpName = $_FILES['attachments']['tmp_name'][$i];
        $mime = mime_content_type($tmpName);
        if (!in_array($mime, $allowedMime, true)) {
            $errors[] = 'نوع الملف غير مدعوم: ' . $_FILES['attachments']['name'][$i];
            continue;
        }

        $uploadDir = __DIR__ . '/uploads';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $baseName = basename($_FILES['attachments']['name'][$i]);
        $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/u', '_', $baseName);
        $targetPath = $uploadDir . '/' . $fileName;

        if (move_uploaded_file($tmpName, $targetPath)) {
            $attachments[] = 'uploads/' . $fileName;
        } else {
            $errors[] = 'فشل رفع الملف: ' . $baseName;
        }
    }
}

if (empty($errors)) {
    $trackingCode = 'SAW' . time() . rand(100, 999);
    $status = 'pending';
    $filePath = !empty($attachments) ? json_encode($attachments, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : null;
    $userId = 1;

    $stmt = $pdo->prepare('INSERT INTO complaints (user_id, department_id, title, request_type, main_topic, description, city, region_id, province_id, commune_id, file_path, status, tracking_code) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([
        $userId,
        $departmentId,
        $title,
        $requestType,
        $mainTopic,
        $description,
        $address,
        $regionId,
        $provinceId,
        $communeId,
        $filePath,
        $status,
        $trackingCode
    ]);

    unset($_SESSION['captcha_code']);

    if ($action === 'draft') {
        $titleText = 'تم حفظ المسودة';
        $message = 'تم حفظ النموذج كمسودة بنجاح. يمكنك العودة لاحقاً لاستكماله.';
    } else {
        $titleText = 'تم إرسال الطلب';
        $message = 'تم إرسال طلبك بنجاح. تم تسجيل الشكوى برقم تتبع: ' . htmlspecialchars($trackingCode);
    }
} else {
    $titleText = 'حدثت أخطاء';
    $message = '<ul>' . implode('', array_map(function ($error) {
        return '<li>' . htmlspecialchars($error) . '</li>';
    }, $errors)) . '</ul>';
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($titleText); ?> - منصة صوتي</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Tajawal', sans-serif; margin:0; padding:0; min-height:100vh; display:flex; align-items:center; justify-content:center; background: linear-gradient(135deg, #f7f8fb 0%, #e9eef4 100%); }
        .message-card { width:min(760px,95%); background: rgba(255,255,255,0.95); border-radius: 28px; box-shadow: 0 25px 50px rgba(15,23,42,0.12); border:1px solid rgba(255,255,255,0.85); padding: 38px; text-align:center; }
        .message-card h1 { font-size:2.4rem; color:#006233; margin-bottom:20px; }
        .message-card p { color:#334155; line-height:1.8; margin-bottom:28px; }
        .message-card .error { color:#b91c1c; font-weight:700; }
        .message-card .success { color:#006233; font-weight:700; }
        .message-card ul { text-align: right; display: inline-block; padding-left: 18px; color: #334155; margin-bottom: 0; }
        .message-card li { margin-bottom: 8px; }
        .message-content { margin-bottom: 28px; }
        .message-card a { display:inline-flex; gap:10px; align-items:center; padding:14px 28px; background:#006233; color:#fff; border-radius:999px; text-decoration:none; font-weight:700; margin:0 6px; }
        .message-card a.secondary { background:#C1272D; }
    </style>
</head>
<body>
    <article class="message-card">
        <h1><?php echo htmlspecialchars($titleText); ?></h1>
        <div class="message-content <?php echo empty($errors) ? 'success' : 'error'; ?>">
            <?php echo $message; ?>
        </div>
        <div>
            <a href="form.php?id=<?php echo htmlspecialchars($departmentId); ?>"><i class="fas fa-arrow-right"></i> العودة إلى النموذج</a>
            <a class="secondary" href="index.html"><i class="fas fa-home"></i> العودة للرئيسية</a>
        </div>
    </article>
</body>
</html>
