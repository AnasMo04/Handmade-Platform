<?php
session_start();
require_once 'includes/db.php';

// Admin access only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Fetch all users
$users_stmt = $pdo->query("SELECT id, username, email, role, created_at FROM users ORDER BY created_at DESC");
$users = $users_stmt->fetchAll();

// Fetch all crafts
$crafts_stmt = $pdo->query("SELECT crafts.*, users.username FROM crafts JOIN users ON crafts.user_id = users.id ORDER BY crafts.created_at DESC");
$crafts = $crafts_stmt->fetchAll();

$success = isset($_GET['success']) ? $_GET['success'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <nav>
            <div class="logo">Crafts Platform (Admin)</div>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="admin_dashboard.php">Dashboard</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main class="container">
        <h2>System Management</h2>

        <?php if ($success == 'deleted'): ?>
            <p class="success-msg">Craft removed successfully!</p>
        <?php elseif ($success == 'user_deleted'): ?>
            <p class="success-msg">User deleted successfully!</p>
        <?php endif; ?>

        <section style="margin-top: 40px;">
            <h3>Users</h3>
            <table>
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Registered</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo htmlspecialchars($user['role']); ?></td>
                            <td><?php echo $user['created_at']; ?></td>
                            <td>
                                <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                    <a href="delete_user.php?id=<?php echo $user['id']; ?>" class="btn btn-danger delete-btn" style="width: auto; padding: 5px 10px;">Delete</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <section style="margin-top: 40px;">
            <h3>All Crafts</h3>
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Craftsman</th>
                        <th>Price</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($crafts as $craft): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($craft['title']); ?></td>
                            <td><?php echo htmlspecialchars($craft['username']); ?></td>
                            <td>$<?php echo htmlspecialchars($craft['price']); ?></td>
                            <td>
                                <!-- Admin could potentially delete inappropriate content -->
                                <a href="delete_craft_admin.php?id=<?php echo $craft['id']; ?>" class="btn btn-danger delete-btn" style="width: auto; padding: 5px 10px;">Remove</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </main>

    <script src="js/validation.js"></script>
</body>
</html>
