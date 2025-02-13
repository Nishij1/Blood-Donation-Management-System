<?php
require_once '../config/config.php';
init_session();

// Clear all session data
session_destroy();

// Redirect to admin login page
header("Location: login.php");
exit();
?> 