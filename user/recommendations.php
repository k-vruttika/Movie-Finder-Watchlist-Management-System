<?php
/**
 * Recommendations Page
 * Personalized movie recommendations based on watchlist and ratings
 */
$page_title = 'Recommendations';
require_once '../includes/db.php';
require_once '../includes/auth.php';

// Require login
require_login();

$user_id = get_user_id();

// Get user's favorite genres (from watchlist)
$stmt = execute_query($conn,
    "SELECT m.genre, COUNT(*) as count
     FROM movies m
     INNER JOIN watchlist w ON m.id = w.movie_id
     WHERE w.user_id = ?
     GROUP BY m.genre
     ORDER BY count DESC
     LIMIT 3",
    'i',
    [$user_id]
);
$favorite_genres = fetch_all($stmt);
$stmt->close();

$genre_recommendations = [];
if (!empty($favorite_genres)) {
    // Get recommendations based on favorite genres
    $genre_list = array_column($favorite_genres, 'genre');
    $placeholders = str_repeat('?,', count($genre_list) - 1) . '?';
    
    $sql = "SELECT m.* FROM movies m
            WHERE m.genre IN ($placeholders)
            AND m.id NOT IN (SELECT movie_id FROM watchlist WHERE user_id = ?)
            AND m.id NOT IN (SELECT movie_id FROM ratings WHERE user_id = ?)
            ORDER BY m.average_rating DESC
            LIMIT 8";
    
    $types = str_repeat('s', count($genre_list)) . 'ii';
    $params = array_merge($genre_list, [$user_id, $user_id]);
    
    $stmt = execute_query($conn, $sql, $types, $params);
    $genre_recommendations = fetch_all($stmt);
    $stmt->close();
}

// Get highly rated movies not yet watched
$stmt = execute_query($conn,
    "SELECT m.* FROM movies m
     WHERE m.id NOT IN (SELECT movie_id FROM watchlist WHERE user_id = ?)
     AND m.id NOT IN (SELECT movie_id FROM ratings WHERE user_id = ?)
     AND m.average_rating >= 4.0
     AND m.rating_count > 0
     ORDER BY m.average_rating DESC, m.rating_count DESC
     LIMIT 8",
    'ii',
    [$user_id, $user_id]
);
$highly_rated = fetch_all($stmt);
$stmt->close();

include '../includes/header.php';
?>

<div class="container">
    <div class="page-header">
        <h1>Recommendations for You</h1>
        <p>Discover movies based on your taste</p>
    </div>
    
    <?php if (!empty($favorite_genres)): ?>
        <div class="recommendations-info">
            <h3>Your Favorite Genres</h3>
            <div class="genre-tags">
                <?php foreach ($favorite_genres as $genre): ?>
                    <span class="badge badge-large">
                        <?php echo htmlspecialchars($genre['genre']); ?>
                        (<?php echo $genre['count']; ?> movie<?php echo $genre['count'] !== 1 ? 's' : ''; ?>)
                    </span>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($genre_recommendations)): ?>
        <section class="recommendation-section">
            <h2>Based on Your Watchlist</h2>
            <p class="section-subtitle">Movies from genres you love</p>
            <div class="movie-grid">
                <?php foreach ($genre_recommendations as $movie): ?>
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
                                <button 
                                    class="watchlist-btn"
                                    onclick="toggleWatchlist(<?php echo $movie['id']; ?>, this)"
                                    title="Add to watchlist"
                                >
                                    +
                                </button>
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
    
    <?php if (!empty($highly_rated)): ?>
        <section class="recommendation-section">
            <h2>Highly Rated Movies</h2>
            <p class="section-subtitle">Top-rated films you haven't seen yet</p>
            <div class="movie-grid">
                <?php foreach ($highly_rated as $movie): ?>
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
                                    <span class="rating-count">(<?php echo $movie['rating_count']; ?>)</span>
                                </div>
                                <button 
                                    class="watchlist-btn"
                                    onclick="toggleWatchlist(<?php echo $movie['id']; ?>, this)"
                                    title="Add to watchlist"
                                >
                                    +
                                </button>
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
    
    <?php if (empty($genre_recommendations) && empty($highly_rated)): ?>
        <div class="empty-state">
            <div class="empty-icon">🎯</div>
            <h2>No Recommendations Yet</h2>
            <p>Start adding movies to your watchlist to get personalized recommendations!</p>
            <a href="movies.php" class="btn btn-primary">Browse Movies</a>
        </div>
    <?php endif; ?>
</div>

<script>
function toggleWatchlist(movieId, button) {
    fetch('/movie-viewer/user/toggle_watchlist.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'movie_id=' + movieId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (data.in_watchlist) {
                button.classList.add('in-watchlist');
                button.textContent = '✓';
                button.title = 'Remove from watchlist';
            } else {
                button.classList.remove('in-watchlist');
                button.textContent = '+';
                button.title = 'Add to watchlist';
            }
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
}
</script>

<?php include '../includes/footer.php'; ?>
