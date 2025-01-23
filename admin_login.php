<?php
session_start();
error_reporting(0);
include('includes/config.php');

// Check if admin is already logged in
if ($_SESSION['admin'] != '') {
    header("Location: dashboard.php");
    exit();
}

// Handle login
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];  

    // SQL Query to fetch user data from the database
    $sql = "SELECT * FROM admin WHERE username=:username";
    $query = $dbh->prepare($sql);
    $query->bindParam(':username', $username, PDO::PARAM_STR);
    $query->execute();

    $results = $query->fetchAll(PDO::FETCH_OBJ);

    if ($query->rowCount() > 0) {
        foreach ($results as $result) {
            // Check if password matches
            if ($result->Password == $password) {  // Ideally, use password_verify() if hashed
                $_SESSION['admid'] = $result->Id;
                $_SESSION['admin'] = $_POST['username'];

                // Check if account is active
                if ($result->Status == 1) {
                    header("Location: dashboard.php");
                    exit();
                } else {
                    echo "<script>alert('Your account is inactive.');</script>";
                }
            } else {
                echo "<script>alert('Invalid Password');</script>";
            }
        }
    } else {
        echo "<script>alert('Invalid Username');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Admin Login</title>
    <style>
        /* General styling */
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

        .login-container {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
            text-align: center;
            display: block;
        }

        .login-container h2 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #333;
        }

        .login-container input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            color: #333;
        }

        .login-container button {
            width: 100%;
            padding: 12px;
            margin-top: 10px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }

        .login-container button:hover {
            background-color: #0056b3;
        }

        .login-container .forgot-password {
            font-size: 14px;
            margin-top: 10px;
        }

        .login-container .forgot-password a {
            color: #007BFF;
            text-decoration: none;
        }

        .login-container .forgot-password a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="navbar">
    <div class="header-wrapper">
        <h1>E-VOTING SYSTEM</h1>
    </div>
</div>

<!-- Login Form -->
<div class="login-container" id="loginContainer">
    <h2>Admin Login</h2>
    <form action="" method="post">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="login">Login</button>
    </form>
    <div class="forgot-password">
        <a href="#">Forgot password?</a>
    </div>
</div>

</body>
</html>
