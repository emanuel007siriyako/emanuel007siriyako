<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = ""; // Replace with your database password
$dbname = "voting";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Delete all records from both tables
$voterRegistrationDeleteQuery = "DELETE FROM voter_registration";
$votersDeleteQuery = "DELETE FROM voters";

if ($conn->query($voterRegistrationDeleteQuery) === TRUE && $conn->query($votersDeleteQuery) === TRUE) {
    echo "<script>alert('All registered voters have been successfully deleted.');</script>";
    echo "<script>window.location.href = 'dashboard.php';</script>"; // Redirect back to dashboard or another page
} else {
    echo "<script>alert('Error deleting voters: " . $conn->error . "');</script>";
}

$conn->close();
?>
