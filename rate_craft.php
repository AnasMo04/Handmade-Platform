<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $craft_id = $_POST['craft_id'];
    $user_id = $_SESSION['user_id'];
    $rating = $_POST['rating'];
    $comment = trim($_POST['comment']);

    // Server-side validation
    if ($rating < 1 || $rating > 5) {
        die("Invalid rating value.");
    }

    // Check if user already rated this craft
    $stmt = $pdo->prepare("SELECT id FROM ratings WHERE craft_id = ? AND user_id = ?");
    $stmt->execute([$craft_id, $user_id]);
    
    if ($stmt->fetch()) {
        // Update existing rating
        $stmt = $pdo->prepare("UPDATE ratings SET rating = ?, comment = ? WHERE craft_id = ? AND user_id = ?");
        $stmt->execute([$rating, $comment, $craft_id, $user_id]);
    } else {
        // Insert new rating
        $stmt = $pdo->prepare("INSERT INTO ratings (craft_id, user_id, rating, comment) VALUES (?, ?, ?, ?)");
        $stmt->execute([$craft_id, $user_id, $rating, $comment]);
    }

    header("Location: index.php?success=rated");
    exit;
} else {
    header("Location: index.php");
    exit;
}
?>
