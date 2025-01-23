<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['admin_id'])==0) { 
    header('location:admin.php');
} else { 
if (isset($_POST['delete_all'])) {
    try {
        // SQL query to delete all records from the candidate table
        $sql = "DELETE FROM candidate";
        $query = $dbh->prepare($sql);
        $query->execute();

        // Success message
        $successMessage = "All candidates have been successfully deleted.";
    } catch (PDOException $e) {
        // Handle errors
        $errorMessage = "Error: " . $e->getMessage();
    }
}

// Initialize error and success messages
$errorMessage = '';
$successMessage = '';

// Function to check if a candidate already exists
function candidateExists($name, $role, $dbh) {
    $sql = "SELECT COUNT(*) FROM candidate WHERE name = :name AND role = :role";
    $query = $dbh->prepare($sql);
    $query->bindParam(':name', $name, PDO::PARAM_STR);
    $query->bindParam(':role', $role, PDO::PARAM_STR);
    $query->execute();
    $count = $query->fetchColumn();
    return $count > 0;  // Return true if candidate exists, otherwise false
}

// Function to check if there are fewer than 4 pairs in the database
function countCandidatePairs($dbh) {
    $sql = "SELECT COUNT(*) FROM candidate WHERE role = 'President'";
    $query = $dbh->prepare($sql);
    $query->execute();
    return $query->fetchColumn();
}

// Handle form submission for the first pair (President and Vice President)
if (isset($_POST['submit_first_pair'])) {
    $presidentName = $_POST['president1'];
    $vicePresidentName = $_POST['vice1'];

    // Handle file uploads
    $presidentImage = null;
    $vicePresidentImage = null;
    
    if (isset($_FILES['president_image']) && $_FILES['president_image']['error'] == 0) {
        $presidentImage = 'img/' . basename($_FILES['president_image']['name']);
        move_uploaded_file($_FILES['president_image']['tmp_name'], $presidentImage);
    }

    if (isset($_FILES['vice_image']) && $_FILES['vice_image']['error'] == 0) {
        $vicePresidentImage = 'img/' . basename($_FILES['vice_image']['name']);
        move_uploaded_file($_FILES['vice_image']['tmp_name'], $vicePresidentImage);
    }

    try {
        // Check if there are already 4 pairs in the database
        $candidateCount = countCandidatePairs($dbh);
        if ($candidateCount >= 2) {
            $errorMessage = "Only Two pairs of candidates are required.";
        } else {
            // Check if President and Vice President already exist
            if (candidateExists($presidentName, 'President', $dbh)) {
                $errorMessage = "President 1 already exists in the database.";
            } elseif (candidateExists($vicePresidentName, 'Vice President', $dbh)) {
                $errorMessage = "Vice President 1 already exists in the database.";
            } else {
                // Insert President into the database
                $sql = "INSERT INTO candidate (name, role, picture) VALUES (:name, 'President', :picture)";
                $query = $dbh->prepare($sql);
                $query->bindParam(':name', $presidentName, PDO::PARAM_STR);
                $query->bindParam(':picture', $presidentImage, PDO::PARAM_STR);
                $query->execute();

                // Get the President ID for linking Vice President
                $presidentId = $dbh->lastInsertId();

                // Insert Vice President and link to the President using president_id
                $sql = "INSERT INTO candidate (name, role, president_id, picture) VALUES (:name, 'Vice President', :president_id, :picture)";
                $query = $dbh->prepare($sql);
                $query->bindParam(':name', $vicePresidentName, PDO::PARAM_STR);
                $query->bindParam(':president_id', $presidentId, PDO::PARAM_INT);
                $query->bindParam(':picture', $vicePresidentImage, PDO::PARAM_STR);
                $query->execute();

                $successMessage = "First President and Vice President pair added successfully!";
            }
        }
    } catch (PDOException $e) {
        $errorMessage = "Error: " . $e->getMessage();
    }
}

// Handle form submission for the second pair (President and Vice President)
if (isset($_POST['submit_second_pair'])) {
    $presidentName = $_POST['president2'];
    $vicePresidentName = $_POST['vice2'];

    // Handle file uploads
    $presidentImage = null;
    $vicePresidentImage = null;
    
    if (isset($_FILES['president_image2']) && $_FILES['president_image2']['error'] == 0) {
        $presidentImage = 'img/' . basename($_FILES['president_image2']['name']);
        move_uploaded_file($_FILES['president_image2']['tmp_name'], $presidentImage);
    }

    if (isset($_FILES['vice_image2']) && $_FILES['vice_image2']['error'] == 0) {
        $vicePresidentImage = 'img/' . basename($_FILES['vice_image2']['name']);
        move_uploaded_file($_FILES['vice_image2']['tmp_name'], $vicePresidentImage);
    }

    try {
        // Check if there are already 4 pairs in the database
        $candidateCount = countCandidatePairs($dbh);
        if ($candidateCount >= 4) {
            $errorMessage = "You can only add up to 4 pairs of candidates.";
        } else {
            // Check if President and Vice President already exist
            if (candidateExists($presidentName, 'President', $dbh)) {
                $errorMessage = "President 2 already exists in the database.";
            } elseif (candidateExists($vicePresidentName, 'Vice President', $dbh)) {
                $errorMessage = "Vice President 2 already exists in the database.";
            } else {
                // Insert President into the database
                $sql = "INSERT INTO candidate (name, role, picture) VALUES (:name, 'President', :picture)";
                $query = $dbh->prepare($sql);
                $query->bindParam(':name', $presidentName, PDO::PARAM_STR);
                $query->bindParam(':picture', $presidentImage, PDO::PARAM_STR);
                $query->execute();

                // Get the President ID for linking Vice President
                $presidentId = $dbh->lastInsertId();

                // Insert Vice President and link to the President using president_id
                $sql = "INSERT INTO candidate (name, role, president_id, picture) VALUES (:name, 'Vice President', :president_id, :picture)";
                $query = $dbh->prepare($sql);
                $query->bindParam(':name', $vicePresidentName, PDO::PARAM_STR);
                $query->bindParam(':president_id', $presidentId, PDO::PARAM_INT);
                $query->bindParam(':picture', $vicePresidentImage, PDO::PARAM_STR);
                $query->execute();

                $successMessage = "Second President and Vice President pair added successfully!";
            }
        }
    } catch (PDOException $e) {
        $errorMessage = "Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Election Candidate Information</title>
    <style>
             body {
    font-family: 'Arial', sans-serif;
    position: relative;
    overflow-x: hidden;
    min-height: 90vh;
    display: flex;
    background: url('National_Electoral_Commission_(Tanzania)_Logo (1).png') no-repeat center center fixed;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    padding: 20px;
}

body::before {
    content: "";
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

.form-container {
    background: rgba(255, 255, 255, 0.8);
    padding: 2px;
    border-radius: 12px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    max-width: 70%;
    width: 100%;
    text-align: center;
    position: relative;
    margin: 20px;
}

.form-container h2 {
    font-size: 24px;
    color: #333;
    margin-bottom: 30px;
}

input[type="text"], input[type="file"], select {
    width: 90%;
    padding: 12px;
    margin-bottom: 15px;
    border: 2px solid rgba(76, 175, 80, 0.4);
    border-radius: 6px;
    font-size: 16px;
}

input[type="text"]:focus, select:focus, input[type="file"]:focus {
    border-color: rgba(76, 175, 80, 0.8);
    background-color: #f1f1f1;
    transform: scale(1.05);
    background-color:   background-color: rgba(34, 139, 34, 0.9); 
}

.submit-btn {
    padding: 12px 25px;
    background-color: rgba(76, 175, 80, 0.8);
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 18px;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.3s ease;
}

.submit-btn:hover {
    background-color: rgba(76, 175, 80, 1);
    transform: scale(1.05);
}

.error-container, .success-container {
    color: red;
    font-weight: bold;
    margin-top: 20px;
    font-size: 16px;
}

.success-container {
    color: green;
}

.form-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 15px;
}

.form-row .col {
    width: 50%;
}

@media (max-width: 768px) {
    .form-row {
        flex-direction: column;
        align-items: flex-start;
    }

    .form-row .col {
        width: 100%;
    }
}

.candidate-list {
    margin-top: 20px;
}

.candidate-list table {
    width: 100%;
    border-collapse: collapse;
}

.candidate-list th, .candidate-list td {
    padding: 10px;
    border: 1px solid #ddd;
    text-align: left;
}
.full-width-btn {
    display: inline-block;
    width: 100%;
}
.btn-danger {
    background-color: #dc3545;
    border-color: #dc3545;
    color: white;
    text-align: center;
    padding: 10px 20px;
    border-radius: 5px;
    font-size: 16px;
    display: inline-flex;
    align-items: center;
}

.btn-danger:hover {
    background-color: #c82333;
    border-color: #c82333;
    cursor: pointer;
}

.fas.fa-trash-alt {
    margin-right: 8px;
}

    </style>
</head>
<body>

<!-- Form for First Pair -->
<div class="form-container">
    <h2>Insert First Pair (President and Vice President)</h2>
    <form method="POST" enctype="multipart/form-data">
        <div class="form-row">
            <div class="col">
                <label for="president1">President Name:</label>
                <input type="text" name="president1" placeholder="Enter President 1 name" required>
            </div>
            <div class="col">
                <label for="vice1">Vice President Name:</label>
                <input type="text" name="vice1" placeholder="Enter Vice President 1 name" required>
            </div>
        </div>

        <div class="form-row">
            <div class="col">
                <label for="president_image">President Image:</label>
                <input type="file" name="president_image" accept="image/*">
            </div>
            <div class="col">
                <label for="vice_image">Vice President Image:</label>
                <input type="file" name="vice_image" accept="image/*">
            </div>
        </div>

        <button type="submit" class="submit-btn" name="submit_first_pair">Add candidate</button>

        <!-- Success and Error Messages -->
        <?php if (!empty($errorMessage)): ?>
            <div class="error-container"><?php echo $errorMessage; ?></div>
        <?php endif; ?>

        <?php if (!empty($successMessage)): ?>
            <div class="success-container"><?php echo $successMessage; ?></div>
        <?php endif; ?>
    </form>
</div>

<!-- Form for Second Pair -->
<div class="form-container">
    <h2>Insert Second Pair (President and Vice President)</h2>
    <form method="POST" enctype="multipart/form-data">
        <div class="form-row">
            <div class="col">
                <label for="president2">President Name:</label>
                <input type="text" name="president2" placeholder="Enter President 2 name" required>
            </div>
            <div class="col">
                <label for="vice2">Vice President Name:</label>
                <input type="text" name="vice2" placeholder="Enter Vice President 2 name" required>
            </div>
        </div>

        <div class="form-row">
            <div class="col">
                <label for="president_image2">President Image:</label>
                <input type="file" name="president_image2" accept="image/*">
            </div>
            <div class="col">
                <label for="vice_image2">Vice President Image:</label>
                <input type="file" name="vice_image2" accept="image/*">
            </div>
        </div>

        <button type="submit" class="submit-btn" name="submit_second_pair">Add candidate</button>
        
    </form>
    <a href="dashboard.php" style="text-decoration: none; float:left;" class="submit-btn ">Back to dashboard</a>
<form method="POST" style="float:right;" onsubmit="return confirmDelete();">
    <button type="submit" name="delete_all" class="btn btn-danger" style="display: inline-flex; align-items: center; padding: 10px 20px;">
        <i class="fa fa-trash" style="margin-right: 8px;"></i> Delete All Candidates
    </button>
</form>
</div>

<script>
function confirmDelete() {
    // Show a confirmation dialog
    return confirm('Are you sure you want to delete all candidates? This action cannot be undone.');
}

</script>
</body>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</html>
<?php } ?>