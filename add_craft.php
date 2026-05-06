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
    // Handle optional image upload with improved checks
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
    <title>Add New Craft - CraftsPlatform</title>
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
                <li><a href="craftsman_dashboard.php"><i class="fas fa-box-open"></i> <span>My Inventory</span></a></li>
                <li><a href="add_craft.php" class="active"><i class="fas fa-plus-circle"></i> <span>Add New Craft</span></a></li>
                <li><a href="#"><i class="fas fa-user-circle"></i> <span>Profile</span></a></li>
            </ul>
            <div class="sidebar-footer">
                <a href="logout.php" class="btn btn-outline" style="width: 100%; border-color: rgba(255,255,255,0.3); color: #fff;">Logout</a>
            </div>
        </aside>

        <main class="content-area">
            <div class="container" style="max-width: 800px;">
                <h2 class="section-title">List a New Creation</h2>

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <div class="table-container" style="padding: 2.5rem;">
                    <form action="add_craft.php" method="POST" enctype="multipart/form-data" id="craftForm">
                        <div class="form-group">
                            <label for="title">Craft Title</label>
                            <input type="text" name="title" id="title" placeholder="e.g. Hand-Woven Silk Scarf" required>
                        </div>
                        <div class="form-group">
                            <label for="description">Detailed Description</label>
                            <textarea name="description" id="description" rows="6" placeholder="Describe the materials, process, and uniqueness..." required></textarea>
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                            <div class="form-group">
                                <label for="price">Price (USD)</label>
                                <input type="number" step="0.01" name="price" id="price" placeholder="0.00" required>
                            </div>
                            <div class="form-group">
                                <label for="image">Product Image</label>
                                <input type="file" name="image" id="image">
                            </div>
                        </div>
                        <div style="margin-top: 2rem; display: flex; gap: 1rem;">
                            <button type="submit" class="btn" style="flex: 2;">Publish Craft</button>
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
