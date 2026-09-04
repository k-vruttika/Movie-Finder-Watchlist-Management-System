<?php
/**
 * Movies Listing Page
 * Browse and search all movies
 */
$page_title = 'Browse Movies';
require_once '../includes/db.php';
require_once '../includes/auth.php';

// Require login
require_login();

$user_id = get_user_id();

// Get search and filter parameters
$search = sanitize_input($_GET['search'] ?? '');
$genre_filter = sanitize_input($_GET['genre'] ?? '');

// Build query
$sql = "SELECT m.*, 
        (SELECT COUNT(*) FROM watchlist WHERE movie_id = m.id AND user_id = ?) as in_watchlist,
        (SELECT rating FROM ratings WHERE movie_id = m.id AND user_id = ?) as user_rating
        FROM movies m WHERE 1=1";

$params = [$user_id, $user_id];
$types = 'ii';

if (!empty($search)) {
    $sql .= " AND m.title LIKE ?";
    $params[] = "%$search%";
    $types .= 's';
}

if (!empty($genre_filter)) {
    $sql .= " AND m.genre = ?";
    $params[] = $genre_filter;
    $types .= 's';
}

$sql .= " ORDER BY m.average_rating DESC, m.title ASC";

$stmt = execute_query($conn, $sql, $types, $params);
$movies = fetch_all($stmt);
$stmt->close();

// Get all genres for filter
$stmt = execute_query($conn, "SELECT DISTINCT genre FROM movies ORDER BY genre");
$genres = fetch_all($stmt);
$stmt->close();

include '../includes/header.php';
?>

<div class="container">
    <div class="page-header">
        <h1>Browse Movies</h1>
        <p>Discover and add movies to your watchlist</p>
    </div>
    
    <div class="search-filter-section">
        <form method="GET" action="" class="search-form">
            <div class="search-group">
                <input 
                    type="text" 
                    name="search" 
                    class="search-input" 
                    placeholder="Search movies..." 
                    value="<?php echo htmlspecialchars($search); ?>"
                >
                <button type="submit" class="btn btn-primary">Search</button>
            </div>
            
            <div class="filter-group">
                <label for="genre">Filter by Genre:</label>
                <select name="genre" id="genre" class="form-control" onchange="this.form.submit()">
                    <option value="">All Genres</option>
                    <?php foreach ($genres as $genre): ?>
                        <option value="<?php echo htmlspecialchars($genre['genre']); ?>" 
                                <?php echo $genre_filter === $genre['genre'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($genre['genre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <?php if (!empty($search) || !empty($genre_filter)): ?>
                <a href="movies.php" class="btn btn-secondary">Clear Filters</a>
            <?php endif; ?>
        </form>
    </div>
    
    <?php if (empty($movies)): ?>
        <div class="empty-state">
            <p>No movies found. Try adjusting your search or filters.</p>
        </div>
    <?php else: ?>
        <div class="movie-grid">
            <?php foreach ($movies as $movie): ?>
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
                                <?php if ($movie['rating_count'] > 0): ?>
                                    <span class="rating-count">(<?php echo $movie['rating_count']; ?>)</span>
                                <?php endif; ?>
                            </div>
                            <button 
                                class="watchlist-btn <?php echo $movie['in_watchlist'] > 0 ? 'in-watchlist' : ''; ?>"
                                data-movie-id="<?php echo $movie['id']; ?>"
                                onclick="toggleWatchlist(<?php echo $movie['id']; ?>, this)"
                                title="<?php echo $movie['in_watchlist'] > 0 ? 'Remove from watchlist' : 'Add to watchlist'; ?>"
                            >
                                <?php echo $movie['in_watchlist'] > 0 ? '✓' : '+'; ?>
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
