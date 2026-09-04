<?php
/**
 * Edit Movie
 * Admin form to edit existing movies
 */
$page_title = 'Edit Movie';
require_once '../includes/db.php';
require_once '../includes/auth.php';

// Require admin
require_admin();

$movie_id = intval($_GET['id'] ?? 0);

if ($movie_id === 0) {
    header('Location: manage_movies.php');
    exit();
}

$errors = [];
$success = '';

// Get movie data
$stmt = execute_query($conn, "SELECT * FROM movies WHERE id = ?", 'i', [$movie_id]);
$movie = fetch_single($stmt);
$stmt->close();

if (!$movie) {
    header('Location: manage_movies.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize_input($_POST['title'] ?? '');
    $genre = sanitize_input($_POST['genre'] ?? '');
    $description = sanitize_input($_POST['description'] ?? '');
    $year = intval($_POST['year'] ?? 0);
    $poster = sanitize_input($_POST['poster'] ?? 'placeholder.jpg');
    
    // Validation
    if (empty($title)) {
        $errors[] = 'Title is required';
    }
    
    if (empty($genre)) {
        $errors[] = 'Genre is required';
    }
    
    if (empty($description)) {
        $errors[] = 'Description is required';
    }
    
    if ($year < 1900 || $year > date('Y') + 5) {
        $errors[] = 'Invalid year';
    }
    
    // Update movie if no errors
    if (empty($errors)) {
        $stmt = execute_query($conn,
            "UPDATE movies SET title = ?, genre = ?, description = ?, year = ?, poster = ? WHERE id = ?",
            'sssssi',
            [$title, $genre, $description, $year, $poster, $movie_id]
        );
        
        if ($stmt) {
            $success = 'Movie updated successfully!';
            $stmt->close();
            
            // Refresh movie data
            $stmt = execute_query($conn, "SELECT * FROM movies WHERE id = ?", 'i', [$movie_id]);
            $movie = fetch_single($stmt);
            $stmt->close();
        } else {
            $errors[] = 'Failed to update movie';
        }
    }
} else {
    // Pre-fill form with existing data
    $title = $movie['title'];
    $genre = $movie['genre'];
    $description = $movie['description'];
    $year = $movie['year'];
    $poster = $movie['poster'];
}

include '../includes/header.php';
?>

<link rel="stylesheet" href="/movie-viewer/assets/css/admin.css">

<div class="container">
    <div class="admin-header">
        <h1>Edit Movie</h1>
        <a href="manage_movies.php" class="btn btn-secondary">← Back to Movies</a>
    </div>
    
    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>
    
    <div class="admin-form-container">
        <form method="POST" action="" class="admin-form">
            <div class="form-group">
                <label for="title">Movie Title *</label>
                <input 
                    type="text" 
                    id="title" 
                    name="title" 
                    class="form-control" 
                    value="<?php echo htmlspecialchars($title); ?>"
                    required
                >
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="genre">Genre *</label>
                    <select id="genre" name="genre" class="form-control" required>
                        <option value="">Select Genre</option>
                        <option value="Action" <?php echo $genre === 'Action' ? 'selected' : ''; ?>>Action</option>
                        <option value="Comedy" <?php echo $genre === 'Comedy' ? 'selected' : ''; ?>>Comedy</option>
                        <option value="Drama" <?php echo $genre === 'Drama' ? 'selected' : ''; ?>>Drama</option>
                        <option value="Horror" <?php echo $genre === 'Horror' ? 'selected' : ''; ?>>Horror</option>
                        <option value="Romance" <?php echo $genre === 'Romance' ? 'selected' : ''; ?>>Romance</option>
                        <option value="Sci-Fi" <?php echo $genre === 'Sci-Fi' ? 'selected' : ''; ?>>Sci-Fi</option>
                        <option value="Thriller" <?php echo $genre === 'Thriller' ? 'selected' : ''; ?>>Thriller</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="year">Year *</label>
                    <input 
                        type="number" 
                        id="year" 
                        name="year" 
                        class="form-control" 
                        min="1900" 
                        max="<?php echo date('Y') + 5; ?>"
                        value="<?php echo $year; ?>"
                        required
                    >
                </div>
            </div>
            
            <div class="form-group">
                <label for="description">Description *</label>
                <textarea 
                    id="description" 
                    name="description" 
                    class="form-control" 
                    rows="5"
                    required
                ><?php echo htmlspecialchars($description); ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="poster">Poster Filename</label>
                <input 
                    type="text" 
                    id="poster" 
                    name="poster" 
                    class="form-control" 
                    value="<?php echo htmlspecialchars($poster); ?>"
                >
                <small class="form-text">Current: <?php echo htmlspecialchars($poster); ?></small>
            </div>
            
            <div class="movie-stats">
                <p><strong>Average Rating:</strong> <?php echo number_format($movie['average_rating'], 1); ?>/5.0</p>
                <p><strong>Total Ratings:</strong> <?php echo $movie['rating_count']; ?></p>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Update Movie</button>
                <a href="manage_movies.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
