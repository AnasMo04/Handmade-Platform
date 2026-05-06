<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$id = $_GET['id'] ?? null;

if ($id) {
    $stmt = $pdo->prepare("DELETE FROM crafts WHERE id = ?");
    if ($stmt->execute([$id])) {
        header("Location: admin_dashboard.php?success=deleted");
        exit;
    } else {
        die("Error deleting record.");
    }
} else {
    header("Location: admin_dashboard.php");
    exit;
}
?>
