<?php
/**
 * Manage Users
 * Admin interface to view and manage users
 */
$page_title = 'Manage Users';
require_once '../includes/db.php';
require_once '../includes/auth.php';

// Require admin
require_admin();

$success = '';

// Handle delete
if (isset($_GET['delete'])) {
    $user_id = intval($_GET['delete']);
    
    // Prevent deleting yourself
    if ($user_id === get_user_id()) {
        $success = 'Cannot delete your own account';
    } else {
        $stmt = execute_query($conn, "DELETE FROM users WHERE id = ? AND role = 'user'", 'i', [$user_id]);
        $stmt->close();
        $success = 'User deleted successfully';
    }
}

// Get all users with stats
$stmt = execute_query($conn,
    "SELECT u.*, 
     (SELECT COUNT(*) FROM watchlist WHERE user_id = u.id) as watchlist_count,
     (SELECT COUNT(*) FROM ratings WHERE user_id = u.id) as ratings_count
     FROM users u
     WHERE u.role = 'user'
     ORDER BY u.created_at DESC"
);
$users = fetch_all($stmt);
$stmt->close();

include '../includes/header.php';
?>

<link rel="stylesheet" href="/movie-viewer/assets/css/admin.css">

<div class="container">
    <div class="admin-header">
        <h1>Manage Users</h1>
        <a href="admin_dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
    </div>
    
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>
    
    <div class="admin-table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Watchlist</th>
                    <th>Ratings</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo $user['watchlist_count']; ?> movies</td>
                        <td><?php echo $user['ratings_count']; ?> ratings</td>
                        <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                        <td class="action-buttons">
                            <a href="?delete=<?php echo $user['id']; ?>" 
                               class="btn btn-sm btn-danger"
                               onclick="return confirm('Are you sure you want to delete this user? This will also remove their watchlist and ratings.')">
                                Delete
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <div class="admin-stats">
        <p>Total Users: <strong><?php echo count($users); ?></strong></p>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
