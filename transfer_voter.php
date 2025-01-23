<?php
// transfer_voter.php - This file handles transferring the last inserted voter to the voters table

// Database connection
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'voting';
$conn = new mysqli($host, $username, $password, $dbname);

// Check for database connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to transfer the last inserted voter to the voters table
function transferVoterData($conn) {
    // Prepare the statement to fetch the most recent voter from the voter_registration table
    $stmt = $conn->prepare("SELECT id, admission_number, voter_code, year FROM voter_registration ORDER BY id DESC LIMIT 1");

    // Check if statement preparation failed
    if (!$stmt) {
        return "Error preparing SQL statement: " . $conn->error;
    }

    // Execute the statement
    if (!$stmt->execute()) {
        $stmt->close();  // Close statement on error
        return "Error executing SQL statement: " . $stmt->error;
    }

    // Bind result variables
    $stmt->bind_result($last_id, $admission_number, $voter_code, $year);
    
    // Fetch the result
    if ($stmt->fetch()) {
        // Close the SELECT statement after fetching results
        $stmt->close();

        // Check if the admission number already exists in the voters table
        $check_stmt = $conn->prepare("SELECT COUNT(*) FROM voters WHERE admissionnumber = ?");
        $check_stmt->bind_param('s', $admission_number);
        $check_stmt->execute();
        $check_stmt->bind_result($count);
        $check_stmt->fetch();
        $check_stmt->close();

        // If the admission number already exists, return an error message
        if ($count > 0) {
            return "Sorry! Voter Already Requested To vote!";
        }

        // Now proceed with inserting the data into the voters table
        $insert_stmt = $conn->prepare("INSERT INTO voters (admissionnumber, voter_code, year, voter_id, status) VALUES (?, ?, ?, ?, ?)");

        // Check if insert statement preparation failed
        if (!$insert_stmt) {
            return "Error preparing insert statement: " . $conn->error;
        }

        // Bind parameters for the insert statement
        $status = "active";  // Default status is active
        $insert_stmt->bind_param('sssis', $admission_number, $voter_code, $year, $last_id, $status);

        // Execute the insert statement
        if ($insert_stmt->execute()) {
            // Close the insert statement after execution
            $insert_stmt->close();
            return "Voter confirmed To Vote!";
        } else {
            // Return error if insert failed
            $insert_stmt->close();
            return "Error: transferring voter to voters table!";
        }
    } else {
        // Return error if no voter data found
        $stmt->close();
        return "Error: No voter data found for transfer!";
    }

    // Close the statement
    $stmt->close();
}

// Get the result from the function
$response = transferVoterData($conn);

// Send response to JavaScript for alert
echo "'$response'";

// Close the database connection
$conn->close();
?>
