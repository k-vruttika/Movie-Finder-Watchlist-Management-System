<?php
/**
 * Movie Details Page
 * View detailed information about a movie
 */
require_once '../includes/db.php';
require_once '../includes/auth.php';

// Require login
require_login();

$user_id = get_user_id();
$movie_id = intval($_GET['id'] ?? 0);

if ($movie_id === 0) {
    header('Location: movies.php');
    exit();
}

// Get movie details
$stmt = execute_query($conn, 
    "SELECT m.*,
     (SELECT COUNT(*) FROM watchlist WHERE movie_id = m.id AND user_id = ?) as in_watchlist,
     (SELECT rating FROM ratings WHERE movie_id = m.id AND user_id = ?) as user_rating
     FROM movies m WHERE m.id = ?",
    'iii',
    [$user_id, $user_id, $movie_id]
);

$movie = fetch_single($stmt);
$stmt->close();

if (!$movie) {
    header('Location: movies.php');
    exit();
}

$page_title = $movie['title'];

// Get related movies (same genre, excluding current movie)
$stmt = execute_query($conn,
    "SELECT * FROM movies WHERE genre = ? AND id != ? ORDER BY average_rating DESC LIMIT 4",
    'si',
    [$movie['genre'], $movie_id]
);
$related_movies = fetch_all($stmt);
$stmt->close();

include '../includes/header.php';
?>

<div class="container">
    <div class="movie-details">
        <div class="movie-details-poster">
            <img src="/movie-viewer/assets/images/<?php echo htmlspecialchars($movie['poster']); ?>" 
                 alt="<?php echo htmlspecialchars($movie['title']); ?>"
                 onerror="this.src='/movie-viewer/assets/images/placeholder.jpg'">
        </div>
        
        <div class="movie-details-content">
            <h1><?php echo htmlspecialchars($movie['title']); ?></h1>
            
            <div class="movie-details-meta">
                <span class="badge"><?php echo htmlspecialchars($movie['year']); ?></span>
                <span class="badge"><?php echo htmlspecialchars($movie['genre']); ?></span>
                <span class="movie-rating-large">
                    <span class="star">★</span>
                    <span><?php echo number_format($movie['average_rating'], 1); ?>/5.0</span>
                    <?php if ($movie['rating_count'] > 0): ?>
                        <span class="rating-count">(<?php echo $movie['rating_count']; ?> ratings)</span>
                    <?php endif; ?>
                </span>
            </div>
            
            <div class="movie-description">
                <h3>Synopsis</h3>
                <p><?php echo nl2br(htmlspecialchars($movie['description'])); ?></p>
            </div>
            
            <div class="movie-actions">
                <button 
                    class="btn <?php echo $movie['in_watchlist'] > 0 ? 'btn-secondary' : 'btn-primary'; ?> watchlist-toggle"
                    data-movie-id="<?php echo $movie['id']; ?>"
                    onclick="toggleWatchlist(<?php echo $movie['id']; ?>)"
                >
                    <?php echo $movie['in_watchlist'] > 0 ? '✓ In Watchlist' : '+ Add to Watchlist'; ?>
                </button>
            </div>
            
            <div class="rating-section">
                <h3>Rate this movie</h3>
                <div class="star-rating" data-movie-id="<?php echo $movie['id']; ?>">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <span class="star-btn <?php echo $movie['user_rating'] >= $i ? 'active' : ''; ?>" 
                              data-rating="<?php echo $i; ?>"
                              onclick="rateMovie(<?php echo $movie['id']; ?>, <?php echo $i; ?>)">
                            ★
                        </span>
                    <?php endfor; ?>
                </div>
                <?php if ($movie['user_rating']): ?>
                    <p class="rating-text">Your rating: <?php echo $movie['user_rating']; ?>/5</p>
                <?php else: ?>
                    <p class="rating-text">Click to rate</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <?php if (!empty($related_movies)): ?>
        <section class="related-section">
            <h2>More <?php echo htmlspecialchars($movie['genre']); ?> Movies</h2>
            <div class="movie-grid">
                <?php foreach ($related_movies as $related): ?>
                    <div class="movie-card">
                        <div class="movie-poster">
                            <a href="movie_details.php?id=<?php echo $related['id']; ?>">
                                <img src="/movie-viewer/assets/images/<?php echo htmlspecialchars($related['poster']); ?>" 
                                     alt="<?php echo htmlspecialchars($related['title']); ?>"
                                     onerror="this.src='/movie-viewer/assets/images/placeholder.jpg'">
                            </a>
                            <div class="movie-overlay">
                                <div class="movie-rating">
                                    <span class="star">★</span>
                                    <span><?php echo number_format($related['average_rating'], 1); ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="movie-info">
                            <h3 class="movie-title">
                                <a href="movie_details.php?id=<?php echo $related['id']; ?>">
                                    <?php echo htmlspecialchars($related['title']); ?>
                                </a>
                            </h3>
                            <p class="movie-meta"><?php echo htmlspecialchars($related['year']); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>
</div>

<script>
function toggleWatchlist(movieId) {
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
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
}

function rateMovie(movieId, rating) {
    fetch('/movie-viewer/user/rate_movie.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'movie_id=' + movieId + '&rating=' + rating
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update star display
            const stars = document.querySelectorAll('.star-btn');
            stars.forEach((star, index) => {
                if (index < rating) {
                    star.classList.add('active');
                } else {
                    star.classList.remove('active');
                }
            });
            
            // Update rating text
            document.querySelector('.rating-text').textContent = 'Your rating: ' + rating + '/5';
            
            // Reload to show updated average rating
            setTimeout(() => location.reload(), 500);
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
