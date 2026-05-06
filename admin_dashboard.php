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
                <li><a href="logout.php" class="btn btn-outline" style="margin-left: 1rem;">Logout (<?php echo htmlspecialchars($_SESSION['username']); ?>)</a></li>
            </ul>
        </nav>
    </header>

    <main class="container">
        <h2 class="section-title">System Management</h2>

        <?php if ($success == 'deleted'): ?>
            <div class="alert success-msg">Craft removed successfully!</div>
        <?php elseif ($success == 'user_deleted'): ?>
            <div class="alert success-msg">User deleted successfully!</div>
        <?php endif; ?>

        <section style="margin-top: 2rem;">
            <h3 style="margin-bottom: 1rem; color: var(--secondary-color);">User Accounts</h3>
            <div class="table-container">
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
                                <td style="font-weight: 600;"><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><span style="background: #e1f5fe; color: #01579b; padding: 2px 8px; border-radius: 12px; font-size: 0.8rem; font-weight: 600;"><?php echo htmlspecialchars($user['role']); ?></span></td>
                                <td style="color: var(--text-muted);"><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                <td>
                                    <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                        <a href="delete_user.php?id=<?php echo $user['id']; ?>" class="btn btn-danger delete-btn" style="padding: 0.4rem 0.8rem; font-size: 0.8rem;">Delete</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section style="margin-top: 3rem;">
            <h3 style="margin-bottom: 1rem; color: var(--secondary-color);">All Crafts</h3>
            <div class="table-container">
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
                                <td style="font-weight: 600;"><?php echo htmlspecialchars($craft['title']); ?></td>
                                <td><?php echo htmlspecialchars($craft['username']); ?></td>
                                <td style="color: var(--primary-color); font-weight: 700;">$<?php echo htmlspecialchars($craft['price']); ?></td>
                                <td>
                                    <a href="delete_craft_admin.php?id=<?php echo $craft['id']; ?>" class="btn btn-danger delete-btn" style="padding: 0.4rem 0.8rem; font-size: 0.8rem;">Remove</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <script src="js/validation.js"></script>
</body>
</html>
