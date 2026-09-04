<?php
/**
 * Add Movie
 * Admin form to add new movies
 */
$page_title = 'Add Movie';
require_once '../includes/db.php';
require_once '../includes/auth.php';

// Require admin
require_admin();

$errors = [];
$success = '';

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
    
    // Insert movie if no errors
    if (empty($errors)) {
        $stmt = execute_query($conn,
            "INSERT INTO movies (title, genre, description, year, poster) VALUES (?, ?, ?, ?, ?)",
            'sssss',
            [$title, $genre, $description, $year, $poster]
        );
        
        if ($stmt) {
            $success = 'Movie added successfully!';
            $stmt->close();
            
            // Clear form
            $title = $genre = $description = '';
            $year = 0;
            $poster = 'placeholder.jpg';
        } else {
            $errors[] = 'Failed to add movie';
        }
    }
}

include '../includes/header.php';
?>

<link rel="stylesheet" href="/movie-viewer/assets/css/admin.css">

<div class="container">
    <div class="admin-header">
        <h1>Add New Movie</h1>
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
        <div class="alert alert-success">
            <?php echo htmlspecialchars($success); ?>
            <a href="manage_movies.php" class="alert-link">View all movies</a>
        </div>
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
                    value="<?php echo isset($title) ? htmlspecialchars($title) : ''; ?>"
                    required
                >
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="genre">Genre *</label>
                    <select id="genre" name="genre" class="form-control" required>
                        <option value="">Select Genre</option>
                        <option value="Action" <?php echo (isset($genre) && $genre === 'Action') ? 'selected' : ''; ?>>Action</option>
                        <option value="Comedy" <?php echo (isset($genre) && $genre === 'Comedy') ? 'selected' : ''; ?>>Comedy</option>
                        <option value="Drama" <?php echo (isset($genre) && $genre === 'Drama') ? 'selected' : ''; ?>>Drama</option>
                        <option value="Horror" <?php echo (isset($genre) && $genre === 'Horror') ? 'selected' : ''; ?>>Horror</option>
                        <option value="Romance" <?php echo (isset($genre) && $genre === 'Romance') ? 'selected' : ''; ?>>Romance</option>
                        <option value="Sci-Fi" <?php echo (isset($genre) && $genre === 'Sci-Fi') ? 'selected' : ''; ?>>Sci-Fi</option>
                        <option value="Thriller" <?php echo (isset($genre) && $genre === 'Thriller') ? 'selected' : ''; ?>>Thriller</option>
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
                        value="<?php echo isset($year) && $year > 0 ? $year : ''; ?>"
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
                ><?php echo isset($description) ? htmlspecialchars($description) : ''; ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="poster">Poster Filename</label>
                <input 
                    type="text" 
                    id="poster" 
                    name="poster" 
                    class="form-control" 
                    value="<?php echo isset($poster) ? htmlspecialchars($poster) : 'placeholder.jpg'; ?>"
                    placeholder="e.g., movie_poster.jpg"
                >
                <small class="form-text">Place image in /assets/images/ directory</small>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Add Movie</button>
                <a href="manage_movies.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
