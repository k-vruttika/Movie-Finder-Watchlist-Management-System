<?php
/**
 * Admin Dashboard
 * Overview of site statistics and management
 */
$page_title = 'Admin Dashboard';
require_once '../includes/db.php';
require_once '../includes/auth.php';

// Require admin
require_admin();

// Get total users
$stmt = execute_query($conn, "SELECT COUNT(*) as count FROM users WHERE role = 'user'");
$total_users = fetch_single($stmt)['count'];
$stmt->close();

// Get total movies
$stmt = execute_query($conn, "SELECT COUNT(*) as count FROM movies");
$total_movies = fetch_single($stmt)['count'];
$stmt->close();

// Get total ratings
$stmt = execute_query($conn, "SELECT COUNT(*) as count FROM ratings");
$total_ratings = fetch_single($stmt)['count'];
$stmt->close();

// Get total watchlist entries
$stmt = execute_query($conn, "SELECT COUNT(*) as count FROM watchlist");
$total_watchlist = fetch_single($stmt)['count'];
$stmt->close();

// Get most popular movies (most watchlisted)
$stmt = execute_query($conn,
    "SELECT m.*, COUNT(w.id) as watchlist_count
     FROM movies m
     LEFT JOIN watchlist w ON m.id = w.movie_id
     GROUP BY m.id
     ORDER BY watchlist_count DESC
     LIMIT 5"
);
$popular_movies = fetch_all($stmt);
$stmt->close();

// Get highest rated movies
$stmt = execute_query($conn,
    "SELECT * FROM movies 
     WHERE rating_count > 0
     ORDER BY average_rating DESC, rating_count DESC
     LIMIT 5"
);
$top_rated = fetch_all($stmt);
$stmt->close();

// Get recent users
$stmt = execute_query($conn,
    "SELECT id, username, email, created_at FROM users 
     WHERE role = 'user'
     ORDER BY created_at DESC
     LIMIT 5"
);
$recent_users = fetch_all($stmt);
$stmt->close();

include '../includes/header.php';
?>

<link rel="stylesheet" href="/movie-viewer/assets/css/admin.css">

<div class="container">
    <div class="admin-header">
        <h1>Admin Dashboard</h1>
        <p>Manage your movie database and users</p>
    </div>
    
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">👥</div>
            <div class="stat-content">
                <h3><?php echo $total_users; ?></h3>
                <p>Total Users</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">🎬</div>
            <div class="stat-content">
                <h3><?php echo $total_movies; ?></h3>
                <p>Total Movies</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">⭐</div>
            <div class="stat-content">
                <h3><?php echo $total_ratings; ?></h3>
                <p>Total Ratings</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">📝</div>
            <div class="stat-content">
                <h3><?php echo $total_watchlist; ?></h3>
                <p>Watchlist Entries</p>
            </div>
        </div>
    </div>
    
    <div class="admin-actions">
        <a href="manage_movies.php" class="btn btn-primary">Manage Movies</a>
        <a href="manage_users.php" class="btn btn-secondary">Manage Users</a>
    </div>
    
    <div class="admin-grid">
        <section class="admin-section">
            <h2>Most Popular Movies</h2>
            <p class="section-subtitle">By watchlist count</p>
            <div class="admin-table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Genre</th>
                            <th>Rating</th>
                            <th>Watchlists</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($popular_movies as $movie): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($movie['title']); ?></td>
                                <td><?php echo htmlspecialchars($movie['genre']); ?></td>
                                <td>★ <?php echo number_format($movie['average_rating'], 1); ?></td>
                                <td><?php echo $movie['watchlist_count']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
        
        <section class="admin-section">
            <h2>Top Rated Movies</h2>
            <p class="section-subtitle">Highest average ratings</p>
            <div class="admin-table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Genre</th>
                            <th>Rating</th>
                            <th>Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($top_rated as $movie): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($movie['title']); ?></td>
                                <td><?php echo htmlspecialchars($movie['genre']); ?></td>
                                <td>★ <?php echo number_format($movie['average_rating'], 1); ?></td>
                                <td><?php echo $movie['rating_count']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
    
    <section class="admin-section">
        <h2>Recent Users</h2>
        <div class="admin-table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Joined</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_users as $user): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>

<?php include '../includes/footer.php'; ?>
