<?php
// Start the session
session_start();

// Destroy all session variables
session_unset(); 

// Destroy the session
session_destroy(); 

// Redirect to the login page
header("Location: default.php");
exit();
?>
