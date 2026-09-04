<?php
/**
 * Landing Page
 */
$page_title = 'Home';
require_once 'includes/db.php';
require_once 'includes/auth.php';

// Redirect if logged in
if (is_logged_in()) {
    if (is_admin()) {
        header('Location: admin/admin_dashboard.php');
    } else {
        header('Location: user/movies.php');
    }
    exit();
}

// Get featured movies (highest rated)
$stmt = execute_query($conn, 
    "SELECT * FROM movies ORDER BY average_rating DESC, rating_count DESC LIMIT 6"
);
$featured_movies = fetch_all($stmt);
$stmt->close();

include 'includes/header.php';
?>

<div class="hero-section">
    <div class="container">
        <div class="hero-content">
            <h1 class="hero-title">Track films you've watched.<br>Save those you want to see.<br>Tell your friends what's good.</h1>
            <p class="hero-subtitle">The social network for film lovers. Discover, rate, and share your favorite movies.</p>
            <div class="hero-actions">
                <a href="register.php" class="btn btn-primary btn-lg">Get Started - It's Free!</a>
                <a href="login.php" class="btn btn-secondary btn-lg">Sign In</a>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <section class="featured-section">
        <h2 class="section-title">Featured Movies</h2>
        <p class="section-subtitle">Highest rated films on MovieViewer</p>
        
        <div class="movie-grid">
            <?php foreach ($featured_movies as $movie): ?>
                <div class="movie-card">
                    <div class="movie-poster">
                        <img src="/movie-viewer/assets/images/<?php echo htmlspecialchars($movie['poster']); ?>" 
                             alt="<?php echo htmlspecialchars($movie['title']); ?>"
                             onerror="this.src='/movie-viewer/assets/images/placeholder.jpg'">
                        <div class="movie-overlay">
                            <div class="movie-rating">
                                <span class="star">★</span>
                                <span><?php echo number_format($movie['average_rating'], 1); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="movie-info">
                        <h3 class="movie-title"><?php echo htmlspecialchars($movie['title']); ?></h3>
                        <p class="movie-meta"><?php echo htmlspecialchars($movie['year']); ?> • <?php echo htmlspecialchars($movie['genre']); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    
    <section class="features-section">
        <h2 class="section-title">Why MovieViewer?</h2>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">📝</div>
                <h3>Keep Track</h3>
                <p>Build your personal watchlist and never forget a movie recommendation again.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">⭐</div>
                <h3>Rate & Review</h3>
                <p>Share your opinions by rating movies from 1 to 5 stars.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🎯</div>
                <h3>Get Recommendations</h3>
                <p>Discover new movies based on your watchlist and ratings.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🔍</div>
                <h3>Browse & Search</h3>
                <p>Explore our extensive movie database with powerful search and filters.</p>
            </div>
        </div>
    </section>
</div>

<?php include 'includes/footer.php'; ?>
