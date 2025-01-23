<?php
header('Content-Type: application/json');
session_start();

include('includes/config.php'); // Database connection

// Check if the voter is logged in
if (!isset($_SESSION['voter_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

// Get the raw input data
$input = json_decode(file_get_contents('php://input'), true);

// Validate and sanitize input
$voterId = filter_var($input['voter_id'], FILTER_VALIDATE_INT);
$presidentName = filter_var($input['president_name'], FILTER_SANITIZE_STRING);
$vicePresidentName = filter_var($input['vice_president_name'], FILTER_SANITIZE_STRING) ?? null;

if (!$voterId || !$presidentName) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit();
}

try {
    // Insert vote into the database
    $sql = "INSERT INTO votes (voter_id, president_name, vice_president_name) 
            VALUES (:voter_id, :president_name, :vice_president_name)";
    $query = $dbh->prepare($sql);
    $query->bindParam(':voter_id', $voterId, PDO::PARAM_INT);
    $query->bindParam(':president_name', $presidentName, PDO::PARAM_STR);
    $query->bindParam(':vice_president_name', $vicePresidentName, PDO::PARAM_STR);
    $query->execute();
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

?>
