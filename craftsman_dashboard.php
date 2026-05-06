<?php
session_start();
require_once 'includes/db.php';

// Check if user is logged in and is a craftsman
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'craftsman') {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM crafts WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$crafts = $stmt->fetchAll();

$success = isset($_GET['success']) ? $_GET['success'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Craftsman Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <nav>
            <div class="logo">Crafts Platform</div>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="craftsman_dashboard.php">Dashboard</a></li>
                <li><a href="logout.php" class="btn btn-outline" style="margin-left: 1rem;">Logout (<?php echo htmlspecialchars($_SESSION['username']); ?>)</a></li>
            </ul>
        </nav>
    </header>

    <main class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h2 class="section-title" style="margin-bottom: 0;">My Crafts Inventory</h2>
            <a href="add_craft.php" class="btn">Add New Craft</a>
        </div>

        <?php if ($success == 'added'): ?>
            <div class="alert success-msg">Craft added successfully!</div>
        <?php elseif ($success == 'updated'): ?>
            <div class="alert success-msg">Craft updated successfully!</div>
        <?php elseif ($success == 'deleted'): ?>
            <div class="alert success-msg">Craft deleted successfully!</div>
        <?php endif; ?>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Price</th>
                        <th>Date Added</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($crafts)): ?>
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 3rem; color: var(--text-muted);">You haven't added any crafts yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($crafts as $craft): ?>
                            <tr>
                                <td style="font-weight: 600;"><?php echo htmlspecialchars($craft['title']); ?></td>
                                <td style="color: var(--primary-color); font-weight: 700;">$<?php echo htmlspecialchars($craft['price']); ?></td>
                                <td style="color: var(--text-muted);"><?php echo date('M d, Y', strtotime($craft['created_at'])); ?></td>
                                <td>
                                    <div style="display: flex; gap: 0.5rem;">
                                        <a href="edit_craft.php?id=<?php echo $craft['id']; ?>" class="btn btn-outline" style="padding: 0.4rem 0.8rem; font-size: 0.8rem;">Edit</a>
                                        <a href="delete_craft.php?id=<?php echo $craft['id']; ?>" class="btn btn-danger delete-btn" style="padding: 0.4rem 0.8rem; font-size: 0.8rem;">Delete</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <script src="js/validation.js"></script>
</body>
</html>
