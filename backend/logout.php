<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start session only if it hasn't started yet
}

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1000)) {
    session_unset();     // Unset session variables
    session_destroy();   // Destroy the session
}
$_SESSION['LAST_ACTIVITY'] = time(); // Update last activity time
?>
