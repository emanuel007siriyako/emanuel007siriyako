<?php
session_start();
error_reporting(0);
include('includes/config.php');

// Initialize error message
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $admissionNumber = $_POST['admission_number'];
    $votingCode = $_POST['voting_code'];

    // SQL Query to fetch the user's data from the database
    $sql = "SELECT * FROM voter_registration WHERE admission_number = :admission_number AND id = :voting_code AND status = 'Active'";
    $query = $dbh->prepare($sql);
    $query->bindParam(':admission_number', $admissionNumber, PDO::PARAM_STR);
    $query->bindParam(':voting_code', $votingCode, PDO::PARAM_STR);
    $query->execute();

    // Check if a record exists
    if ($query->rowCount() > 0) {
        $result = $query->fetch(PDO::FETCH_OBJ);
        $_SESSION['voter_id'] = $result->id;
        $_SESSION['admission_number'] = $result->admission_number;
        $_SESSION['voter_code'] = $result->voter_code;
        
        // Redirect to the dashboard after successful login
        header("Location: make-vote.php");
        exit();
    } else {
        $errorMessage = "<span id='anim'>Invalid Admission Number or Voting Code.</span>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | E-VOTING</title>
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <link href="assets/css/style.css" rel="stylesheet" />
    <style>
        body {
            font-family: 'Arial', sans-serif;
            position: relative;
            overflow-x: hidden;
            min-height: 100vh;
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

        h1 {
            font-size: 36px;
            color: rgba(76, 175, 80, 1);
            text-align: center;
            margin-bottom: 30px;
            font-weight: bold;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }

        .form-container {
            background: rgba(255, 255, 255, 0.8);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            max-width: 500px;
            width: 100%;
            text-align: center;
            position: relative;
            border: 2px solid rgba(76, 175, 80, 0.7);
        }

        .form-container h2 {
            font-size: 24px;
            color: #333;
            margin-bottom: 30px;
        }

        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 2px solid rgba(76, 175, 80, 0.4);
            border-radius: 6px;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        input[type="text"]:focus, input[type="password"]:focus {
            border-color: rgba(76, 175, 80, 0.8);
            background-color: #f1f1f1;
            transform: scale(1.05);
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

        .error-container {
            color: red;
            font-weight: bold;
            margin-top: 20px;
            font-size: 16px;
        }
        #anim {
          animation:erro 2s ease-in-out forwards infinite;



}
@keyframes erro {
  0% {
    transform: translateY(0);
    opacity: .2;
  }
  100% {
    transform: translateY(-200px);
    opacity: 1;
  }
}

    </style>
</head>
<body>

<h1>Morogoro Teacher College E-Voting <?php echo date('Y'); ?></h1>

    
    <div class="form-container">
        <h2>Login to Vote</h2>
        
        <!-- Login Form -->
        <form method="POST" id="login-form">
            <input type="text" name="admission_number" placeholder="Admission Number" required oninput="this.value = this.value.toUpperCase();">
            <input type="text" name="voting_code" placeholder="Voting Code" required>
            
            <button type="submit" class="submit-btn" name="login">Login</button>
        </form>

        <!-- Error Message -->
        <?php if (!empty($errorMessage)): ?>
            <div class="error-container">
                <?php echo $errorMessage; ?>
            </div>
            <?php endif; ?>

            <a href="../homepoll.php" style="color:green; float:left; font-size:20px;"><i class="fa fa-home"></i>Home</a>
        </div>

</body>
<?php include('includes/footer.php') ?>
</html>
