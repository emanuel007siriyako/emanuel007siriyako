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

// Fetch total votes
$totalVotesQuery = "SELECT COUNT(*) AS total_votes FROM votes";
$totalVotesResult = $conn->query($totalVotesQuery);
$totalVotes = $totalVotesResult->fetch_assoc()['total_votes'] ?? 1; // Avoid division by zero

// Fetch total voters
$totalVotersQuery = "SELECT COUNT(*) AS total_voters FROM votersregistered";
$totalVotersResult = $conn->query($totalVotersQuery);
$totalVoters = $totalVotersResult->fetch_assoc()['total_voters'];

// Calculate non-voters
$nonVoters = $totalVoters - $totalVotes;

// Fetch president and vice president pairs with vote counts
$sql = "SELECT 
            c1.name AS president_name, 
            c2.name AS vice_name, 
            COUNT(v.id) AS total_votes
        FROM candidate c1
        LEFT JOIN candidate c2 ON c1.id = c2.president_id
        LEFT JOIN votes v ON v.president_name = c1.name AND v.vice_president_name = c2.name
        WHERE c1.role = 'President'
        GROUP BY c1.name, c2.name";
$result = $conn->query($sql);

$pairs = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $pairs[] = $row;
    }
}

// Return data in JSON format
echo json_encode([
    'pairs' => $pairs,
    'totalVotes' => $totalVotes,
    'totalVoters' => $totalVoters,
    'nonVoters' => $nonVoters
]);

$conn->close();
?>
