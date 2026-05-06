<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$id = $_GET['id'] ?? null;

if ($id) {
    // Prevent admin from deleting themselves
    if ($id == $_SESSION['user_id']) {
        die("You cannot delete your own account.");
    }

    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    if ($stmt->execute([$id])) {
        header("Location: admin_dashboard.php?success=user_deleted");
        exit;
    } else {
        die("Error deleting user.");
    }
} else {
    header("Location: admin_dashboard.php");
    exit;
}
?>
