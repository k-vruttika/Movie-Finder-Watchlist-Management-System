<?php
/**
 * User Login Page
 */
$page_title = 'Login';
require_once 'includes/db.php';
require_once 'includes/auth.php';

// Redirect if already logged in
if (is_logged_in()) {
    if (is_admin()) {
        header('Location: admin/admin_dashboard.php');
    } else {
        header('Location: user/movies.php');
    }
    exit();
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize_input($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username)) {
        $errors[] = 'Username is required';
    }
    
    if (empty($password)) {
        $errors[] = 'Password is required';
    }
    
    if (empty($errors)) {
        // Fetch user from database
        $stmt = execute_query($conn, 
            "SELECT id, username, email, password, role FROM users WHERE username = ?",
            's',
            [$username]
        );
        
        $user = fetch_single($stmt);
        $stmt->close();
        
        if ($user && password_verify($password, $user['password'])) {
            // Set session
            set_user_session($user['id'], $user['username'], $user['email'], $user['role']);
            
            // Redirect based on role
            if ($user['role'] === 'admin') {
                header('Location: admin/admin_dashboard.php');
            } else {
                header('Location: user/movies.php');
            }
            exit();
        } else {
            $errors[] = 'Invalid username or password';
        }
    }
}

include 'includes/header.php';
?>

<div class="auth-container">
    <div class="auth-card">
        <h1>Welcome Back</h1>
        <p class="auth-subtitle">Login to continue to MovieViewer</p>
        
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
                <label for="username">Username</label>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    class="form-control" 
                    value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>"
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
            
            <button type="submit" class="btn btn-primary btn-block">Login</button>
        </form>
        
        <p class="auth-footer">
            Don't have an account? <a href="register.php">Sign up here</a>
        </p>
        
        <div class="demo-credentials">
            <p><strong>Demo Credentials:</strong></p>
            <p>User: <code>john_doe</code> / Password: <code>password123</code></p>
            <p>Admin: <code>admin</code> / Password: <code>admin123</code></p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
