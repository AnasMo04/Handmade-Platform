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
        // Handle optional image upload
        $image_url = $craft['image_url']; // Keep old image by default
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
            $file_ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

            if (in_array($file_ext, $allowed_ext)) {
                $target_dir = "uploads/";
                if (!is_dir($target_dir)) mkdir($target_dir, 0755, true);

                if (is_writable($target_dir)) {
                    $file_name = time() . '_' . bin2hex(random_bytes(8)) . '.' . $file_ext;
                    $target_file = $target_dir . $file_name;
                    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                        $image_url = $target_file;
                    }
                } else {
                    $error = 'Upload directory is not writable.';
                }
            } else {
                $error = 'Invalid file type.';
            }
        }

        if (empty($error)) {
            $stmt = $pdo->prepare("UPDATE crafts SET title = ?, description = ?, price = ?, image_url = ? WHERE id = ? AND user_id = ?");
            if ($stmt->execute([$title, $description, $price, $image_url, $id, $_SESSION['user_id']])) {
                header("Location: craftsman_dashboard.php?success=updated");
                exit;
            } else {
                $error = 'Database error. Failed to update craft.';
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
    <title>Edit Craft - CraftsPlatform</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body style="background: var(--bg-color);">
    <div class="main-wrapper">
        <aside class="sidebar">
            <a href="index.php" class="logo">
                <i class="fas fa-hammer"></i>
                <span>CraftsPlatform</span>
            </a>
            <ul class="sidebar-nav">
                <li><a href="craftsman_dashboard.php"><i class="fas fa-chart-line"></i> <span>My Dashboard</span></a></li>
                <li><a href="index.php"><i class="fas fa-search"></i> <span>Browse Others</span></a></li>
                <li><a href="craftsman_dashboard.php" class="active"><i class="fas fa-box-open"></i> <span>My Inventory</span></a></li>
            </ul>
            <div class="sidebar-footer">
                <a href="logout.php" class="btn btn-outline" style="width: 100%; border-color: rgba(255,255,255,0.3); color: #fff;">Logout</a>
            </div>
        </aside>

        <main class="content-area">
            <div class="container" style="max-width: 800px;">
                <h2 class="section-title">Edit Your Creation</h2>

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <div class="table-container" style="padding: 2.5rem;">
                    <form action="edit_craft.php?id=<?php echo $id; ?>" method="POST" enctype="multipart/form-data" id="craftForm">
                        <div class="form-group">
                            <label for="title">Craft Title</label>
                            <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($craft['title']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="description">Detailed Description</label>
                            <textarea name="description" id="description" rows="6" required><?php echo htmlspecialchars($craft['description']); ?></textarea>
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                            <div class="form-group">
                                <label for="price">Price (USD)</label>
                                <input type="number" step="0.01" name="price" id="price" value="<?php echo htmlspecialchars($craft['price']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="image">Update Image</label>
                                <input type="file" name="image" id="image">
                                <?php if ($craft['image_url']): ?>
                                    <p style="margin-top: 5px; font-size: 0.8rem; color: var(--text-muted);">Current: <?php echo basename($craft['image_url']); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div style="margin-top: 2rem; display: flex; gap: 1rem;">
                            <button type="submit" class="btn" style="flex: 2;">Save Changes</button>
                            <a href="craftsman_dashboard.php" class="btn btn-outline" style="flex: 1;">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <footer style="margin-left: var(--sidebar-width);">
        <div class="container">
            <p style="font-weight: 700; color: var(--primary-color); margin-bottom: 1rem;">CraftsPlatform</p>
            <p>&copy; <?php echo date("Y"); ?> Craftsman Management Portal. All rights reserved.</p>
        </div>
    </footer>

    <script src="js/validation.js"></script>
</body>
</html>
