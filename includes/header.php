<?php
/**
 * Common Header
 * Included on all pages for consistent navigation
 */
require_once __DIR__ . '/auth.php';

// Get current page for active menu highlighting
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>Movie Viewer</title>
    <link rel="stylesheet" href="/movie-viewer/assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="nav-brand">
                <a href="/movie-viewer/index.php">
                    <span class="logo">🎬 MovieViewer</span>
                </a>
            </div>
            
            <button class="nav-toggle" id="navToggle">
                <span></span>
                <span></span>
                <span></span>
            </button>
            
            <div class="nav-menu" id="navMenu">
                <?php if (is_logged_in()): ?>
                    <?php if (is_admin()): ?>
                        <!-- Admin Navigation -->
                        <a href="/movie-viewer/admin/admin_dashboard.php" class="nav-link <?php echo $current_page === 'admin_dashboard.php' ? 'active' : ''; ?>">Dashboard</a>
                        <a href="/movie-viewer/admin/manage_movies.php" class="nav-link <?php echo $current_page === 'manage_movies.php' ? 'active' : ''; ?>">Manage Movies</a>
                        <a href="/movie-viewer/admin/manage_users.php" class="nav-link <?php echo $current_page === 'manage_users.php' ? 'active' : ''; ?>">Manage Users</a>
                        <a href="/movie-viewer/user/movies.php" class="nav-link">View Site</a>
                    <?php else: ?>
                        <!-- User Navigation -->
                        <a href="/movie-viewer/user/movies.php" class="nav-link <?php echo $current_page === 'movies.php' ? 'active' : ''; ?>">Browse Movies</a>
                        <a href="/movie-viewer/user/watchlist.php" class="nav-link <?php echo $current_page === 'watchlist.php' ? 'active' : ''; ?>">My Watchlist</a>
                        <a href="/movie-viewer/user/dashboard.php" class="nav-link <?php echo $current_page === 'dashboard.php' ? 'active' : ''; ?>">Dashboard</a>
                        <a href="/movie-viewer/user/recommendations.php" class="nav-link <?php echo $current_page === 'recommendations.php' ? 'active' : ''; ?>">Recommendations</a>
                    <?php endif; ?>
                    
                    <div class="nav-user">
                        <span class="user-greeting">Hello, <?php echo htmlspecialchars(get_username()); ?>!</span>
                        <a href="/movie-viewer/logout.php" class="btn btn-secondary btn-sm">Logout</a>
                    </div>
                <?php else: ?>
                    <!-- Guest Navigation -->
                    <a href="/movie-viewer/index.php" class="nav-link <?php echo $current_page === 'index.php' ? 'active' : ''; ?>">Home</a>
                    <a href="/movie-viewer/login.php" class="btn btn-secondary">Login</a>
                    <a href="/movie-viewer/register.php" class="btn btn-primary">Sign Up</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    
    <main class="main-content">
