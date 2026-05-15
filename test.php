<?php
echo "<h1>اختبار اتصال قاعدة البيانات</h1>";

// محاولة تحميل ملف database.php
$config_path = __DIR__ . '/config/database.php';

if (file_exists($config_path)) {
    echo "✅ ملف database.php موجود في: " . $config_path . "<br>";
    require_once $config_path;
    echo "✅ تم الاتصال بقاعدة البيانات بنجاح<br>";
    
    // اختبار جلب البيانات من جدول departments
    $stmt = $pdo->query("SELECT * FROM departments");
    $departments = $stmt->fetchAll();
    echo "✅ عدد الوزارات في قاعدة البيانات: " . count($departments) . "<br>";
    
    echo "<h2>قائمة الوزارات:</h2><ul>";
    foreach ($departments as $dept) {
        echo "<li>" . $dept['name_ar'] . " (ID: " . $dept['id'] . ")</li>";
    }
    echo "</ul>";
    
} else {
    echo "❌ ملف database.php غير موجود في: " . $config_path . "<br>";
    echo "<br>الحل: قم بإنشاء مجلد 'config' وبداخله ملف 'database.php'";
}
?>