<?php
/**
 * Logout Handler
 */
require_once 'includes/auth.php';

// Destroy session and redirect to login
destroy_user_session();
header('Location: login.php');
exit();
?>
