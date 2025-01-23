<?php
    session_start();
    error_reporting(0);
   

    if(strlen($_SESSION['admin_id'])==0) { 
        header('location:admin.php');
    } else { 
$servername = "localhost"; // Database server
$username = "root"; // Your database username
$password = ""; // Your database password
$dbname = "voting"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if all fields and files are set
    if (
        isset($_POST['president1'], $_POST['vice1'], $_POST['president2'], $_POST['vice2']) &&
        isset($_FILES['picture1'], $_FILES['picture2'], $_FILES['picture3'], $_FILES['picture4'])
    ) {
        $president1 = $_POST['president1'];
        $vice1 = $_POST['vice1'];
        $president2 = $_POST['president2'];
        $vice2 = $_POST['vice2'];

        // Handle image uploads
        $target_dir = "uploads/";
        $target_file1 = $target_dir . basename($_FILES["picture1"]["name"]);
        $target_file2 = $target_dir . basename($_FILES["picture2"]["name"]);
        $target_file3 = $target_dir . basename($_FILES["picture3"]["name"]);
        $target_file4 = $target_dir . basename($_FILES["picture4"]["name"]);

        // Move uploaded files to the target directory
        if (
            move_uploaded_file($_FILES["picture1"]["tmp_name"], $target_file1) &&
            move_uploaded_file($_FILES["picture2"]["tmp_name"], $target_file2) &&
            move_uploaded_file($_FILES["picture3"]["tmp_name"], $target_file3) &&
            move_uploaded_file($_FILES["picture4"]["tmp_name"], $target_file4)
        ) {
            // Insert candidates into the database
            $conn->begin_transaction();

            try {
                // Insert President 1
                $stmt1 = $conn->prepare("INSERT INTO CANDIDATE (name, role, picture) VALUES (?, 'President', ?)");
                $stmt1->bind_param("ss", $president1, $target_file1);
                $stmt1->execute();

                // Insert Vice President 1
                $stmt2 = $conn->prepare("INSERT INTO CANDIDATE (name, role, picture) VALUES (?, 'Vice President', ?)");
                $stmt2->bind_param("ss", $vice1, $target_file2);
                $stmt2->execute();

                // Get last inserted id for pair_id
                $president1_id = $stmt1->insert_id;
                $vice1_id = $stmt2->insert_id;

                // Update pair_id for each candidate
                $stmt3 = $conn->prepare("UPDATE CANDIDATE SET pair_id = ? WHERE id = ?");
                $stmt3->bind_param("ii", $president1_id, $vice1_id);
                $stmt3->execute();

                // Insert President 2
                $stmt4 = $conn->prepare("INSERT INTO CANDIDATE (name, role, picture) VALUES (?, 'President', ?)");
                $stmt4->bind_param("ss", $president2, $target_file3);
                $stmt4->execute();

                // Insert Vice President 2
                $stmt5 = $conn->prepare("INSERT INTO CANDIDATE (name, role, picture) VALUES (?, 'Vice President', ?)");
                $stmt5->bind_param("ss", $vice2, $target_file4);
                $stmt5->execute();

                // Get last inserted id for pair_id
                $president2_id = $stmt4->insert_id;
                $vice2_id = $stmt5->insert_id;

                // Update pair_id for each candidate
                $stmt6 = $conn->prepare("UPDATE CANDIDATE SET pair_id = ? WHERE id = ?");
                $stmt6->bind_param("ii", $president2_id, $vice2_id);
                $stmt6->execute();

                // Commit transaction
                $conn->commit();
                echo "Candidates added successfully!";
            } catch (Exception $e) {
                // Rollback transaction in case of error
                $conn->rollback();
                echo "Error: " . $e->getMessage();
            }
        } else {
            echo "Sorry, there was an error uploading your files.";
        }
    } else {
        echo "Please fill in all the fields and upload all the required files.";
    }
}

// Close the connection
$conn->close();
    }
?>
