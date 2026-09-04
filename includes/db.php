<?php
/**
 * Database Connection Handler
 * Provides MySQLi connection and helper functions
 */

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'movie_viewer_db');

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8mb4
$conn->set_charset("utf8mb4");

/**
 * Execute a prepared statement with parameters
 * @param mysqli $conn Database connection
 * @param string $sql SQL query with placeholders
 * @param string $types Parameter types (e.g., 'ssi' for string, string, int)
 * @param array $params Array of parameters
 * @return mysqli_stmt|false Prepared statement or false on failure
 */
function execute_query($conn, $sql, $types = '', $params = []) {
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        return false;
    }
    
    if (!empty($types) && !empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    return $stmt;
}

/**
 * Fetch a single row from a prepared statement
 * @param mysqli_stmt $stmt Prepared statement
 * @return array|null Associative array or null
 */
function fetch_single($stmt) {
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

/**
 * Fetch all rows from a prepared statement
 * @param mysqli_stmt $stmt Prepared statement
 * @return array Array of associative arrays
 */
function fetch_all($stmt) {
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

/**
 * Sanitize input to prevent XSS
 * @param string $data Input data
 * @return string Sanitized data
 */
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Close database connection
 */
function close_connection($conn) {
    if ($conn) {
        $conn->close();
    }
}
?>
