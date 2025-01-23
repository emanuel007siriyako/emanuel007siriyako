<?php
// Start the session
session_start();

// Database connection
$host = 'localhost';
$username = 'root';
$password = '';  // Your MySQL password
$dbname = 'voting';
$conn = new mysqli($host, $username, $password, $dbname);

// Check if the connection is successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if an admin is logged in (you can also do a session check if needed)
if (isset($_SESSION['admin_id'])) {
    // Get the current admin's ID
    $admin_id = $_SESSION['admin_id'];

    // SQL query to delete the admin account from the admin table
    $stmt = $conn->prepare("DELETE FROM admin WHERE id = ?");
    $stmt->bind_param("i", $admin_id);

    // Execute the delete query
    if ($stmt->execute()) {
        // Unset the session variables to log out the admin
        session_unset();
        session_destroy();

        // Redirect to default.php after deletion
        header("Location: default.php");
        exit();
    } else {
        // If the delete query fails
        echo "<script>alert('Error deleting admin account.');</script>";
    }

    // Close the statement
    $stmt->close();
} else {
    // If no admin is logged in, redirect to default.php
    header("Location: default.php");
    exit();
}

// Close the connection
$conn->close();
?>
