<?php
/**
 * User Dashboard
 * Overview of user activity and recommendations
 */
$page_title = 'Dashboard';
require_once '../includes/db.php';
require_once '../includes/auth.php';

// Require login
require_login();

$user_id = get_user_id();
$username = get_username();

// Get watchlist count
$stmt = execute_query($conn, "SELECT COUNT(*) as count FROM watchlist WHERE user_id = ?", 'i', [$user_id]);
$watchlist_count = fetch_single($stmt)['count'];
$stmt->close();

// Get ratings count
$stmt = execute_query($conn, "SELECT COUNT(*) as count FROM ratings WHERE user_id = ?", 'i', [$user_id]);
$ratings_count = fetch_single($stmt)['count'];
$stmt->close();

// Get recently added to watchlist
$stmt = execute_query($conn,
    "SELECT m.* FROM movies m
     INNER JOIN watchlist w ON m.id = w.movie_id
     WHERE w.user_id = ?
     ORDER BY w.added_at DESC
     LIMIT 4",
    'i',
    [$user_id]
);
$recent_watchlist = fetch_all($stmt);
$stmt->close();

// Get recently rated movies
$stmt = execute_query($conn,
    "SELECT m.*, r.rating as user_rating, r.rated_at
     FROM movies m
     INNER JOIN ratings r ON m.id = r.movie_id
     WHERE r.user_id = ?
     ORDER BY r.rated_at DESC
     LIMIT 4",
    'i',
    [$user_id]
);
$recent_ratings = fetch_all($stmt);
$stmt->close();

include '../includes/header.php';
?>

<div class="container">
    <div class="dashboard-header">
        <h1>Welcome back, <?php echo htmlspecialchars($username); ?>! 👋</h1>
        <p>Here's what's happening with your movie collection</p>
    </div>
    
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">📝</div>
            <div class="stat-content">
                <h3><?php echo $watchlist_count; ?></h3>
                <p>Movies in Watchlist</p>
            </div>
            <a href="watchlist.php" class="stat-link">View Watchlist →</a>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">⭐</div>
            <div class="stat-content">
                <h3><?php echo $ratings_count; ?></h3>
                <p>Movies Rated</p>
            </div>
            <a href="movies.php" class="stat-link">Browse Movies →</a>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">🎯</div>
            <div class="stat-content">
                <h3>Personalized</h3>
                <p>Recommendations</p>
            </div>
            <a href="recommendations.php" class="stat-link">Get Recommendations →</a>
        </div>
    </div>
    
    <?php if (!empty($recent_watchlist)): ?>
        <section class="dashboard-section">
            <div class="section-header">
                <h2>Recently Added to Watchlist</h2>
                <a href="watchlist.php" class="section-link">View All →</a>
            </div>
            <div class="movie-grid">
                <?php foreach ($recent_watchlist as $movie): ?>
                    <div class="movie-card">
                        <div class="movie-poster">
                            <a href="movie_details.php?id=<?php echo $movie['id']; ?>">
                                <img src="/movie-viewer/assets/images/<?php echo htmlspecialchars($movie['poster']); ?>" 
                                     alt="<?php echo htmlspecialchars($movie['title']); ?>"
                                     onerror="this.src='/movie-viewer/assets/images/placeholder.jpg'">
                            </a>
                            <div class="movie-overlay">
                                <div class="movie-rating">
                                    <span class="star">★</span>
                                    <span><?php echo number_format($movie['average_rating'], 1); ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="movie-info">
                            <h3 class="movie-title">
                                <a href="movie_details.php?id=<?php echo $movie['id']; ?>">
                                    <?php echo htmlspecialchars($movie['title']); ?>
                                </a>
                            </h3>
                            <p class="movie-meta"><?php echo htmlspecialchars($movie['year']); ?> • <?php echo htmlspecialchars($movie['genre']); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>
    
    <?php if (!empty($recent_ratings)): ?>
        <section class="dashboard-section">
            <div class="section-header">
                <h2>Recently Rated</h2>
                <a href="movies.php" class="section-link">Rate More →</a>
            </div>
            <div class="movie-grid">
                <?php foreach ($recent_ratings as $movie): ?>
                    <div class="movie-card">
                        <div class="movie-poster">
                            <a href="movie_details.php?id=<?php echo $movie['id']; ?>">
                                <img src="/movie-viewer/assets/images/<?php echo htmlspecialchars($movie['poster']); ?>" 
                                     alt="<?php echo htmlspecialchars($movie['title']); ?>"
                                     onerror="this.src='/movie-viewer/assets/images/placeholder.jpg'">
                            </a>
                            <div class="movie-overlay">
                                <div class="movie-rating">
                                    <span class="star">★</span>
                                    <span><?php echo number_format($movie['average_rating'], 1); ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="movie-info">
                            <h3 class="movie-title">
                                <a href="movie_details.php?id=<?php echo $movie['id']; ?>">
                                    <?php echo htmlspecialchars($movie['title']); ?>
                                </a>
                            </h3>
                            <p class="movie-meta"><?php echo htmlspecialchars($movie['year']); ?> • <?php echo htmlspecialchars($movie['genre']); ?></p>
                            <div class="user-rating-badge">
                                Your rating: <?php echo str_repeat('★', $movie['user_rating']); ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>
    
    <?php if (empty($recent_watchlist) && empty($recent_ratings)): ?>
        <div class="empty-state">
            <div class="empty-icon">🎬</div>
            <h2>Start Your Movie Journey</h2>
            <p>Browse movies, add them to your watchlist, and rate the ones you've watched!</p>
            <a href="movies.php" class="btn btn-primary">Browse Movies</a>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
