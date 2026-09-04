<?php
/**
 * Admin Login Page
 * Separate login for admin users
 */
$page_title = 'Admin Login';
require_once '../includes/db.php';
require_once '../includes/auth.php';

// Redirect if already logged in as admin
if (is_logged_in() && is_admin()) {
    header('Location: admin_dashboard.php');
    exit();
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize_input($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $errors[] = 'All fields are required';
    } else {
        // Fetch admin user
        $stmt = execute_query($conn,
            "SELECT id, username, email, password, role FROM users WHERE username = ? AND role = 'admin'",
            's',
            [$username]
        );
        
        $user = fetch_single($stmt);
        $stmt->close();
        
        if ($user && password_verify($password, $user['password'])) {
            set_user_session($user['id'], $user['username'], $user['email'], $user['role']);
            header('Location: admin_dashboard.php');
            exit();
        } else {
            $errors[] = 'Invalid admin credentials';
        }
    }
}

include '../includes/header.php';
?>

<div class="auth-container">
    <div class="auth-card">
        <h1>Admin Login</h1>
        <p class="auth-subtitle">Access the admin dashboard</p>
        
        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="" class="auth-form">
            <div class="form-group">
                <label for="username">Admin Username</label>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    class="form-control" 
                    required
                    autofocus
                >
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    class="form-control" 
                    required
                >
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">Login as Admin</button>
        </form>
        
        <p class="auth-footer">
            <a href="/movie-viewer/login.php">← Back to User Login</a>
        </p>
        
        <div class="demo-credentials">
            <p><strong>Demo Admin:</strong></p>
            <p>Username: <code>admin</code> / Password: <code>admin123</code></p>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
