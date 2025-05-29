<?php
session_start();
session_unset();      // Unset all session variables
session_destroy();    // Destroy the session
setcookie('token', '', time() - 3600, '/'); // Optional: delete token cookie
header("Location: login.php"); // Redirect to login page
exit;
