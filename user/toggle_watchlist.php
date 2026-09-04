<?php
/**
 * AJAX Endpoint: Toggle Watchlist
 * Add or remove movie from user's watchlist
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

if ($movie_id === 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid movie ID']);
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

// Check if already in watchlist
$stmt = execute_query($conn, 
    "SELECT id FROM watchlist WHERE user_id = ? AND movie_id = ?",
    'ii',
    [$user_id, $movie_id]
);
$existing = fetch_single($stmt);
$stmt->close();

if ($existing) {
    // Remove from watchlist
    $stmt = execute_query($conn,
        "DELETE FROM watchlist WHERE user_id = ? AND movie_id = ?",
        'ii',
        [$user_id, $movie_id]
    );
    $stmt->close();
    
    echo json_encode([
        'success' => true,
        'in_watchlist' => false,
        'message' => 'Removed from watchlist'
    ]);
} else {
    // Add to watchlist
    $stmt = execute_query($conn,
        "INSERT INTO watchlist (user_id, movie_id) VALUES (?, ?)",
        'ii',
        [$user_id, $movie_id]
    );
    $stmt->close();
    
    echo json_encode([
        'success' => true,
        'in_watchlist' => true,
        'message' => 'Added to watchlist'
    ]);
}
?>
