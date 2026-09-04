<?php
/**
 * AJAX Endpoint: Rate Movie
 * Add or update user's rating for a movie
 */
require_once '../includes/db.php';
require_once '../includes/auth.php';

header('Content-Type: application/json');

// Require login
if (!is_logged_in()) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$user_id = get_user_id();
$movie_id = intval($_POST['movie_id'] ?? 0);
$rating = intval($_POST['rating'] ?? 0);

// Validation
if ($movie_id === 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid movie ID']);
    exit();
}

if ($rating < 1 || $rating > 5) {
    echo json_encode(['success' => false, 'message' => 'Rating must be between 1 and 5']);
    exit();
}

// Check if movie exists
$stmt = execute_query($conn, "SELECT id FROM movies WHERE id = ?", 'i', [$movie_id]);
$movie = fetch_single($stmt);
$stmt->close();

if (!$movie) {
    echo json_encode(['success' => false, 'message' => 'Movie not found']);
    exit();
}

// Check if user has already rated this movie
$stmt = execute_query($conn,
    "SELECT id FROM ratings WHERE user_id = ? AND movie_id = ?",
    'ii',
    [$user_id, $movie_id]
);
$existing = fetch_single($stmt);
$stmt->close();

if ($existing) {
    // Update existing rating
    $stmt = execute_query($conn,
        "UPDATE ratings SET rating = ? WHERE user_id = ? AND movie_id = ?",
        'iii',
        [$rating, $user_id, $movie_id]
    );
} else {
    // Insert new rating
    $stmt = execute_query($conn,
        "INSERT INTO ratings (user_id, movie_id, rating) VALUES (?, ?, ?)",
        'iii',
        [$user_id, $movie_id, $rating]
    );
}

$stmt->close();

// Get updated average rating (triggers will handle this automatically)
$stmt = execute_query($conn,
    "SELECT average_rating, rating_count FROM movies WHERE id = ?",
    'i',
    [$movie_id]
);
$updated_movie = fetch_single($stmt);
$stmt->close();

echo json_encode([
    'success' => true,
    'rating' => $rating,
    'average_rating' => floatval($updated_movie['average_rating']),
    'rating_count' => intval($updated_movie['rating_count']),
    'message' => 'Rating saved successfully'
]);
?>
