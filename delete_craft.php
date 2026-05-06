<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'craftsman') {
    header("Location: login.php");
    exit;
}

$id = $_GET['id'] ?? null;

if ($id) {
    // Ensure the craft belongs to the logged-in craftsman
    $stmt = $pdo->prepare("DELETE FROM crafts WHERE id = ? AND user_id = ?");
    if ($stmt->execute([$id, $_SESSION['user_id']])) {
        header("Location: craftsman_dashboard.php?success=deleted");
        exit;
    } else {
        die("Error deleting record.");
    }
} else {
    header("Location: craftsman_dashboard.php");
    exit;
}
?>
