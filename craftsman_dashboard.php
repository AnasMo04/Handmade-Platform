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
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main class="container">
        <h2>My Dashboard</h2>
        <?php if ($success == 'added'): ?>
            <p class="success-msg">Craft added successfully!</p>
        <?php elseif ($success == 'updated'): ?>
            <p class="success-msg">Craft updated successfully!</p>
        <?php elseif ($success == 'deleted'): ?>
            <p class="success-msg">Craft deleted successfully!</p>
        <?php endif; ?>

        <div style="margin-bottom: 20px;">
            <a href="add_craft.php" class="btn" style="width: auto;">Add New Craft</a>
        </div>

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
                        <td colspan="4">You haven't added any crafts yet.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($crafts as $craft): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($craft['title']); ?></td>
                            <td>$<?php echo htmlspecialchars($craft['price']); ?></td>
                            <td><?php echo $craft['created_at']; ?></td>
                            <td>
                                <a href="edit_craft.php?id=<?php echo $craft['id']; ?>" class="btn" style="width: auto; padding: 5px 10px; background: #3498db; color: white;">Edit</a>
                                <a href="delete_craft.php?id=<?php echo $craft['id']; ?>" class="btn btn-danger delete-btn" style="width: auto; padding: 5px 10px;">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </main>

    <script src="js/validation.js"></script>
</body>
</html>
