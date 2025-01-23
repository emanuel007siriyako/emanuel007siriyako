<?php
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['voter_id'])) {
    echo "Access denied. Please log in.";
    exit();
}

include('includes/config.php'); // Database connection

// Get voter ID from session
$voterId = $_SESSION['voter_id'];

// Validate and sanitize input parameters
$presidentId = filter_input(INPUT_GET, 'president_id', FILTER_VALIDATE_INT);
$vicePresidentId = filter_input(INPUT_GET, 'vice_president_id', FILTER_VALIDATE_INT);

if (!$presidentId) {
    echo "Invalid President ID.";
    exit();
}

try {
    // Check if voter has already voted
    $sql = "SELECT * FROM votes WHERE voter_id = :voter_id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':voter_id', $voterId, PDO::PARAM_INT);
    $query->execute();
    if ($query->rowCount() > 0) {
        // Show a styled message if the user has already voted
        echo "
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Already Voted</title>
            <link href='assets/css/bootstrap.css' rel='stylesheet' />
            <style>
                body {
                    font-family: Arial, sans-serif;
                    background-color: #f8f9fa;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    height: 100vh;
                    margin: 0;
                }
                      body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('National_Electoral_Commission_(Tanzania)_Logo (1).png') no-repeat center center fixed;
            background-size: cover;
            background-attachment: fixed;
            opacity: 0.16;
            z-index: -1;
        }
                .message-box {
                    background-color: #ffebee;
                    padding: 30px;
                    border-radius: 10px;
                    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
                    text-align: center;
                    max-width: 500px;
                    width: 90%;
                }
                .message-box h2 {
                    color: #d32f2f;
                    font-size: 24px;
                    margin-bottom: 20px;
                }
                .btn-home {
                    background-color: #4CAF50;
                    color: white;
                    padding: 12px 20px;
                    border-radius: 5px;
                    font-size: 16px;
                    text-decoration: none;
                    display: inline-block;
                    margin-top: 20px;
                    transition: background-color 0.3s ease;
                }
                .btn-home:hover {
                    background-color: #45a049;
                }
            </style>
        </head>
        <body>
            <div class='message-box'>
                <h2>You have already voted!</h2>
                <p>Your vote has already been recorded. Thank you!</p>
                <!-- Modify the href to log out the user -->
                <a href='logoutuser.php' class='btn-home'>Back to Home</a>
            </div>
        </body>
        </html>
        ";
        exit();
    }

    // Fetch president name
    $sql = "SELECT name FROM candidate WHERE id = :president_id AND role = 'President'";
    $query = $dbh->prepare($sql);
    $query->bindParam(':president_id', $presidentId, PDO::PARAM_INT);
    $query->execute();
    $president = $query->fetch(PDO::FETCH_ASSOC);

    if (!$president) {
        echo "President ID does not exist.";
        exit();
    }
    $presidentName = $president['name'];

    // Fetch vice president name if provided
    $vicePresidentName = null;
    if ($vicePresidentId) {
        $sql = "SELECT name FROM candidate WHERE id = :vice_president_id AND role = 'Vice President'";
        $query = $dbh->prepare($sql);
        $query->bindParam(':vice_president_id', $vicePresidentId, PDO::PARAM_INT);
        $query->execute();
        $vicePresident = $query->fetch(PDO::FETCH_ASSOC);

        if ($vicePresident) {
            $vicePresidentName = $vicePresident['name'];
        } else {
            echo "Invalid Vice President ID.";
            exit();
        }
    }

    // Insert vote into the database
    $sql = "INSERT INTO votes (voter_id, president_name, vice_president_name) 
            VALUES (:voter_id, :president_name, :vice_president_name)";
    $query = $dbh->prepare($sql);
    $query->bindParam(':voter_id', $voterId, PDO::PARAM_INT);
    $query->bindParam(':president_name', $presidentName, PDO::PARAM_STR);
    $query->bindValue(':vice_president_name', $vicePresidentName, $vicePresidentName ? PDO::PARAM_STR : PDO::PARAM_NULL);
    $query->execute();

    // Redirect to a thank-you page
    header("Location: thank-you.php?president=" . urlencode($presidentName) . "&vice_president=" . urlencode($vicePresidentName));
    exit();

} catch (PDOException $e) {
    // Log error details to a file for debugging
    error_log("Database Error: " . $e->getMessage());
    echo "An error occurred while processing your request. Please try again later.";
    exit();
}
?>
