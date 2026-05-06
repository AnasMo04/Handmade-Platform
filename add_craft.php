<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'craftsman') {
    header("Location: login.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $price = $_POST['price'];
    $user_id = $_SESSION['user_id'];

    // Server-side validation
    if (empty($title) || empty($description) || empty($price)) {
        $error = 'All fields are required.';
    } elseif (!is_numeric($price)) {
        $error = 'Price must be a number.';
    } else {
        // Handle optional image upload with basic security checks
        $image_url = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
            $file_ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            
            if (in_array($file_ext, $allowed_ext)) {
                $target_dir = "uploads/";
                $file_name = time() . '_' . bin2hex(random_bytes(8)) . '.' . $file_ext;
                $target_file = $target_dir . $file_name;
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                    $image_url = $target_file;
                }
            } else {
                $error = 'Invalid file type. Only JPG, PNG, and GIF are allowed.';
            }
        }

        if (empty($error)) {

            $stmt = $pdo->prepare("INSERT INTO crafts (user_id, title, description, price, image_url) VALUES (?, ?, ?, ?, ?)");
            if ($stmt->execute([$user_id, $title, $description, $price, $image_url])) {
                header("Location: craftsman_dashboard.php?success=added");
                exit;
            } else {
                $error = 'Database error. Failed to add craft.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Craft</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="auth-container" style="max-width: 600px;">
        <h2>Add New Craft</h2>
        <?php if ($error): ?>
            <div class="alert error-msg"><?php echo $error; ?></div>
        <?php endif; ?>
        <form action="add_craft.php" method="POST" enctype="multipart/form-data" id="craftForm">
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" name="title" id="title" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea name="description" id="description" rows="5" required></textarea>
            </div>
            <div class="form-group">
                <label for="price">Price ($)</label>
                <input type="number" step="0.01" name="price" id="price" required>
            </div>
            <div class="form-group">
                <label for="image">Image (Optional)</label>
                <input type="file" name="image" id="image">
            </div>
            <button type="submit" class="btn" style="width: 100%;">Add Craft</button>
            <a href="craftsman_dashboard.php" style="display:block; text-align:center; margin-top:1.5rem; color: var(--text-muted); text-decoration: none; font-weight: 500;">Cancel</a>
        </form>
    </div>
    <script src="js/validation.js"></script>
</body>
</html>
