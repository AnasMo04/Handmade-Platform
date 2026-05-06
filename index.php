<?php
/**
 * Main Landing Page - Productive Families & Handmade Crafts Platform
 * This page displays all available crafts in a dynamic card-based gallery.
 * It includes a search feature and navigation based on user roles.
 */
session_start();
require_once 'includes/db.php';

// Handle search query if provided
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

if ($search) {
    // Search for crafts matching the title or description
    $stmt = $pdo->prepare("SELECT crafts.*, users.username, 
                           (SELECT AVG(rating) FROM ratings WHERE craft_id = crafts.id) as avg_rating,
                           (SELECT COUNT(*) FROM ratings WHERE craft_id = crafts.id) as rating_count
                           FROM crafts 
                           JOIN users ON crafts.user_id = users.id 
                           WHERE title LIKE ? OR description LIKE ?");
    $stmt->execute(["%$search%", "%$search%"]);
} else {
    $stmt = $pdo->query("SELECT crafts.*, users.username,
                         (SELECT AVG(rating) FROM ratings WHERE craft_id = crafts.id) as avg_rating,
                         (SELECT COUNT(*) FROM ratings WHERE craft_id = crafts.id) as rating_count
                         FROM crafts 
                         JOIN users ON crafts.user_id = users.id 
                         ORDER BY created_at DESC");
}
$crafts = $stmt->fetchAll();

$success = isset($_GET['success']) ? $_GET['success'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Crafts Platform</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <nav>
            <div class="logo">Crafts Platform</div>
            <ul>
                <li><a href="index.php">Home</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if ($_SESSION['role'] == 'craftsman'): ?>
                        <li><a href="craftsman_dashboard.php">My Dashboard</a></li>
                    <?php elseif ($_SESSION['role'] == 'admin'): ?>
                        <li><a href="admin_dashboard.php">Admin Panel</a></li>
                    <?php endif; ?>
                    <li><a href="logout.php" class="btn btn-outline" style="margin-left: 1rem;">Logout (<?php echo htmlspecialchars($_SESSION['username']); ?>)</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php" class="btn">Get Started</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main class="container">
        <section class="search-section">
            <form action="index.php" method="GET" class="search-bar">
                <input type="text" name="search" placeholder="Search crafts by name or description..." value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit" class="btn">Search</button>
            </form>
        </section>

        <?php if ($success == 'rated'): ?>
            <div class="alert success-msg">Thank you for your rating!</div>
        <?php endif; ?>

        <h2 class="section-title">Available Crafts</h2>
        <div class="gallery">
            <?php if (empty($crafts)): ?>
                <p>No crafts found matching your search.</p>
            <?php else: ?>
                <?php foreach ($crafts as $craft): ?>
                    <div class="craft-card">
                        <div class="craft-image-container">
                            <?php if ($craft['image_url']): ?>
                                <img src="<?php echo htmlspecialchars($craft['image_url']); ?>" alt="<?php echo htmlspecialchars($craft['title']); ?>" class="craft-image">
                            <?php else: ?>
                                <img src="https://via.placeholder.com/600x400?text=No+Image" alt="No Image" class="craft-image">
                            <?php endif; ?>
                        </div>
                        <div class="craft-info">
                            <h3><?php echo htmlspecialchars($craft['title']); ?></h3>
                            <p class="craft-price">$<?php echo htmlspecialchars($craft['price']); ?></p>

                            <p class="craft-description"><?php echo htmlspecialchars($craft['description']); ?></p>

                            <div class="craft-meta">
                                <div class="rating-display">
                                    <?php if ($craft['avg_rating']): ?>
                                        <span>★</span> <?php echo number_format($craft['avg_rating'], 1); ?> <span style="color: var(--text-muted); font-weight: normal;">(<?php echo $craft['rating_count']; ?>)</span>
                                    <?php else: ?>
                                        <span style="color: #ccc;">★</span> <span style="color: var(--text-muted); font-weight: normal;">No ratings</span>
                                    <?php endif; ?>
                                </div>
                                <span>By <?php echo htmlspecialchars($craft['username']); ?></span>
                            </div>
                            
                            <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] == 'user'): ?>
                                <div style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px dashed var(--border-color);">
                                    <form action="rate_craft.php" method="POST" style="display: flex; gap: 0.5rem; align-items: center;">
                                        <input type="hidden" name="craft_id" value="<?php echo $craft['id']; ?>">
                                        <select name="rating" required style="flex: 1; padding: 0.4rem; border-radius: 4px; border: 1px solid var(--border-color);">
                                            <option value="5">5 - Excellent</option>
                                            <option value="4">4 - Very Good</option>
                                            <option value="3">3 - Good</option>
                                            <option value="2">2 - Fair</option>
                                            <option value="1">1 - Poor</option>
                                        </select>
                                        <button type="submit" class="btn btn-outline" style="padding: 0.4rem 0.8rem;">Rate</button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Productive Families & Handmade Crafts Platform</p>
    </footer>
</body>
</html>
