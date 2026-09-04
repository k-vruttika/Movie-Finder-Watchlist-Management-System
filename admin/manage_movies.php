<?php
/**
 * Manage Movies
 * Admin interface for CRUD operations on movies
 */
$page_title = 'Manage Movies';
require_once '../includes/db.php';
require_once '../includes/auth.php';

// Require admin
require_admin();

$success = '';
$errors = [];

// Handle delete
if (isset($_GET['delete'])) {
    $movie_id = intval($_GET['delete']);
    
    // Get movie poster to delete file
    $stmt = execute_query($conn, "SELECT poster FROM movies WHERE id = ?", 'i', [$movie_id]);
    $movie = fetch_single($stmt);
    $stmt->close();
    
    // Delete movie (cascade will handle ratings and watchlist)
    $stmt = execute_query($conn, "DELETE FROM movies WHERE id = ?", 'i', [$movie_id]);
    $stmt->close();
    
    $success = 'Movie deleted successfully';
}

// Get all movies
$search = sanitize_input($_GET['search'] ?? '');
$sql = "SELECT * FROM movies WHERE 1=1";
$params = [];
$types = '';

if (!empty($search)) {
    $sql .= " AND title LIKE ?";
    $params[] = "%$search%";
    $types .= 's';
}

$sql .= " ORDER BY title ASC";

$stmt = execute_query($conn, $sql, $types, $params);
$movies = fetch_all($stmt);
$stmt->close();

include '../includes/header.php';
?>

<link rel="stylesheet" href="/movie-viewer/assets/css/admin.css">

<div class="container">
    <div class="admin-header">
        <h1>Manage Movies</h1>
        <a href="add_movie.php" class="btn btn-primary">+ Add New Movie</a>
    </div>
    
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>
    
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
                <?php if (!empty($search)): ?>
                    <a href="manage_movies.php" class="btn btn-secondary">Clear</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
    
    <div class="admin-table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Poster</th>
                    <th>Title</th>
                    <th>Genre</th>
                    <th>Year</th>
                    <th>Rating</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($movies as $movie): ?>
                    <tr>
                        <td><?php echo $movie['id']; ?></td>
                        <td>
                            <img src="/movie-viewer/assets/images/<?php echo htmlspecialchars($movie['poster']); ?>" 
                                 alt="<?php echo htmlspecialchars($movie['title']); ?>"
                                 class="table-poster"
                                 onerror="this.src='/movie-viewer/assets/images/placeholder.jpg'">
                        </td>
                        <td><?php echo htmlspecialchars($movie['title']); ?></td>
                        <td><?php echo htmlspecialchars($movie['genre']); ?></td>
                        <td><?php echo $movie['year']; ?></td>
                        <td>★ <?php echo number_format($movie['average_rating'], 1); ?> (<?php echo $movie['rating_count']; ?>)</td>
                        <td class="action-buttons">
                            <a href="edit_movie.php?id=<?php echo $movie['id']; ?>" class="btn btn-sm btn-secondary">Edit</a>
                            <a href="?delete=<?php echo $movie['id']; ?>" 
                               class="btn btn-sm btn-danger"
                               onclick="return confirm('Are you sure you want to delete this movie? This will also remove all ratings and watchlist entries.')">
                                Delete
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <div class="admin-stats">
        <p>Total Movies: <strong><?php echo count($movies); ?></strong></p>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
