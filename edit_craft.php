<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'craftsman') {
    header("Location: login.php");
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: craftsman_dashboard.php");
    exit;
}

// Fetch existing craft data
$stmt = $pdo->prepare("SELECT * FROM crafts WHERE id = ? AND user_id = ?");
$stmt->execute([$id, $_SESSION['user_id']]);
$craft = $stmt->fetch();

if (!$craft) {
    die("Craft not found or unauthorized access.");
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $price = $_POST['price'];

    if (empty($title) || empty($description) || empty($price)) {
        $error = 'All fields are required.';
    } else {
        $stmt = $pdo->prepare("UPDATE crafts SET title = ?, description = ?, price = ? WHERE id = ? AND user_id = ?");
        if ($stmt->execute([$title, $description, $price, $id, $_SESSION['user_id']])) {
            header("Location: craftsman_dashboard.php?success=updated");
            exit;
        } else {
            $error = 'Database error. Failed to update craft.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Craft</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="auth-container">
        <h2>Edit Craft</h2>
        <?php if ($error): ?>
            <p class="error-msg"><?php echo $error; ?></p>
        <?php endif; ?>
        <form action="edit_craft.php?id=<?php echo $id; ?>" method="POST" id="craftForm">
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($craft['title']); ?>" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea name="description" id="description" rows="5" required><?php echo htmlspecialchars($craft['description']); ?></textarea>
            </div>
            <div class="form-group">
                <label for="price">Price ($)</label>
                <input type="number" step="0.01" name="price" id="price" value="<?php echo htmlspecialchars($craft['price']); ?>" required>
            </div>
            <button type="submit" class="btn">Update Craft</button>
            <a href="craftsman_dashboard.php" style="display:block; text-align:center; margin-top:10px;">Cancel</a>
        </form>
    </div>
    <script src="js/validation.js"></script>
</body>
</html>
