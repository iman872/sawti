<?php
// admin/index.php - redirect to login or dashboard based on session
session_start();

if (isset($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit();
} else {
    header('Location: login.php');
    exit();
}
?>
