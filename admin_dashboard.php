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

// Analytics
$total_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$total_craftsmen = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'craftsman'")->fetchColumn();
$total_crafts = $pdo->query("SELECT COUNT(*) FROM crafts")->fetchColumn();

$success = isset($_GET['success']) ? $_GET['success'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - CraftsPlatform</title>
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
                <li><a href="index.php"><i class="fas fa-th-large"></i> <span>Browse Crafts</span></a></li>
                <li><a href="admin_dashboard.php" class="active"><i class="fas fa-users-cog"></i> <span>Manage Users</span></a></li>
            </ul>
            <div class="sidebar-footer">
                <a href="logout.php" class="btn btn-outline" style="width: 100%; border-color: rgba(255,255,255,0.3); color: #fff;">Logout</a>
            </div>
        </aside>

        <main class="content-area">
            <div class="container">
        <h2 class="section-title">System Management</h2>

        <div class="analytics-row">
            <div class="analytics-card">
                <div class="card-icon"><i class="fas fa-users"></i></div>
                <div class="card-info">
                    <h4>Total Users</h4>
                    <p><?php echo $total_users; ?></p>
                </div>
            </div>
            <div class="analytics-card">
                <div class="card-icon"><i class="fas fa-user-tie"></i></div>
                <div class="card-info">
                    <h4>Total Craftsmen</h4>
                    <p><?php echo $total_craftsmen; ?></p>
                </div>
            </div>
            <div class="analytics-card">
                <div class="card-icon"><i class="fas fa-box-open"></i></div>
                <div class="card-info">
                    <h4>Total Crafts</h4>
                    <p><?php echo $total_crafts; ?></p>
                </div>
            </div>
        </div>

        <?php if ($success == 'deleted'): ?>
            <div class="alert alert-success">Craft removed successfully!</div>
        <?php elseif ($success == 'user_deleted'): ?>
            <div class="alert alert-success">User deleted successfully!</div>
        <?php elseif ($success == 'updated'): ?>
            <div class="alert alert-success">Craft updated successfully!</div>
        <?php endif; ?>

        <section style="margin-top: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3 style="margin: 0; color: var(--secondary-color);">User Accounts</h3>
                <button onclick="window.print()" class="btn btn-outline no-print"><i class="fas fa-print"></i> Print User Report</button>
            </div>
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

            </div>
        </main>
    </div>

    <footer style="margin-left: var(--sidebar-width);">
        <div class="container">
            <p style="font-weight: 700; color: var(--primary-color); margin-bottom: 1rem;">CraftsPlatform</p>
            <p>&copy; <?php echo date("Y"); ?> Admin Control Panel. All rights reserved.</p>
        </div>
    </footer>

    <script src="js/validation.js"></script>
</body>
</html>
