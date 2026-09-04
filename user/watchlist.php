<?php
/**
 * User Watchlist Page
 * View and manage personal watchlist
 */
$page_title = 'My Watchlist';
require_once '../includes/db.php';
require_once '../includes/auth.php';

// Require login
require_login();

$user_id = get_user_id();

// Get watchlist movies
$stmt = execute_query($conn,
    "SELECT m.*, w.added_at,
     (SELECT rating FROM ratings WHERE movie_id = m.id AND user_id = ?) as user_rating
     FROM movies m
     INNER JOIN watchlist w ON m.id = w.movie_id
     WHERE w.user_id = ?
     ORDER BY w.added_at DESC",
    'ii',
    [$user_id, $user_id]
);

$watchlist_movies = fetch_all($stmt);
$stmt->close();

include '../includes/header.php';
?>

<div class="container">
    <div class="page-header">
        <h1>My Watchlist</h1>
        <p>Movies you want to watch</p>
    </div>
    
    <?php if (empty($watchlist_movies)): ?>
        <div class="empty-state">
            <div class="empty-icon">📝</div>
            <h2>Your watchlist is empty</h2>
            <p>Start adding movies you want to watch!</p>
            <a href="movies.php" class="btn btn-primary">Browse Movies</a>
        </div>
    <?php else: ?>
        <div class="watchlist-stats">
            <p>You have <strong><?php echo count($watchlist_movies); ?></strong> movie<?php echo count($watchlist_movies) !== 1 ? 's' : ''; ?> in your watchlist</p>
        </div>
        
        <div class="movie-grid">
            <?php foreach ($watchlist_movies as $movie): ?>
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
                                class="watchlist-btn in-watchlist"
                                onclick="removeFromWatchlist(<?php echo $movie['id']; ?>, this)"
                                title="Remove from watchlist"
                            >
                                ✓
                            </button>
                        </div>
                    </div>
                    <div class="movie-info">
                        <h3 class="movie-title">
                            <a href="movie_details.php?id=<?php echo $movie['id']; ?>">
                                <?php echo htmlspecialchars($movie['title']); ?>
                            </a>
                        </h3>
                        <p class="movie-meta">
                            <?php echo htmlspecialchars($movie['year']); ?> • <?php echo htmlspecialchars($movie['genre']); ?>
                        </p>
                        <p class="watchlist-date">
                            Added <?php echo date('M j, Y', strtotime($movie['added_at'])); ?>
                        </p>
                        <?php if ($movie['user_rating']): ?>
                            <div class="user-rating-badge">
                                Your rating: <?php echo str_repeat('★', $movie['user_rating']); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
function removeFromWatchlist(movieId, button) {
    if (!confirm('Remove this movie from your watchlist?')) {
        return;
    }
    
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
            // Remove the movie card from the page
            button.closest('.movie-card').remove();
            
            // Check if watchlist is now empty
            const movieGrid = document.querySelector('.movie-grid');
            if (movieGrid && movieGrid.children.length === 0) {
                location.reload();
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
