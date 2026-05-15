<?php
echo "<h2>فحص مسار ملف قاعدة البيانات</h2>";

$paths = [
    __DIR__ . '/config/database.php',
    'config/database.php',
    'database.php'
];

foreach ($paths as $path) {
    if (file_exists($path)) {
        echo "✅ الملف موجود في: " . $path . "<br>";
        require_once $path;
        echo "✅ تم تحميل الملف بنجاح!<br>";
        break;
    } else {
        echo "❌ الملف غير موجود في: " . $path . "<br>";
    }
}

echo "<br><h3>هيكل المجلدات الحالي:</h3>";
echo "المسار الحالي: " . __DIR__ . "<br>";

// عرض محتويات المجلد الحالي
$files = scandir(__DIR__);
echo "الملفات في المجلد الحالي:<br><ul>";
foreach ($files as $file) {
    if ($file != '.' && $file != '..') {
        echo "<li>" . $file;
        if (is_dir(__DIR__ . '/' . $file)) {
            echo " (مجلد)";
        }
        echo "</li>";
    }
}
echo "</ul>";
?>