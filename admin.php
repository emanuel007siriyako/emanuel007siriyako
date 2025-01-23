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

// Check if the admin table is empty
$query = "SELECT COUNT(*) FROM admin";
$result = $conn->query($query);
$row = $result->fetch_row();
$is_empty = $row[0] == 0;  // Check if no admin record exists

// Handle form submission for creating an admin account or login
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($is_empty) {
        // If no records exist, create the first admin account
        $admin_username = $_POST['username'];
        $admin_password = $_POST['password'];  // Plain text password

        // Insert the new admin into the database (password stored as plain text)
        $stmt = $conn->prepare("INSERT INTO admin (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $admin_username, $admin_password);

        if ($stmt->execute()) {
            echo "<script>alert('Admin created successfully! You can now login.');</script>";
        } else {
            echo "<script>alert('Error creating admin.');</script>";
        }

        $stmt->close();
    } else {
        // Handle login form submission
        $login_username = $_POST['username'];
        $login_password = $_POST['password'];  // Plain text password

        // Query to check if the user exists
        $stmt = $conn->prepare("SELECT id, username, password FROM admin WHERE username = ?");
        $stmt->bind_param("s", $login_username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $username, $stored_password);
            $stmt->fetch();

            // Compare the plain text passwords
            if ($login_password === $stored_password) {
                // Login successful, set session and redirect to dashboard
                $_SESSION['admin_id'] = $id;
                $_SESSION['admin_username'] = $username;
                header("Location: dashboard.php");
                exit();
            } else {
                echo "<script>alert('Invalid password.');</script>";
            }
        } else {
            echo "<script>alert('Admin username not found.');</script>";
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Admin Login</title>
    <style>
        /* Your CSS styles here */
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

        /* Loader styling */
        .loading {
            display: flex;
            justify-content: center;
            align-items: center;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 18px;
            font-weight: bold;
        }

        .loading img {
            width: 40px;
            height: 40px;
            margin-right: 10px;
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
    <?php if ($is_empty): ?>
        <h2>Create Admin Account</h2>
        <form action="" method="post">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Create Admin</button>
        </form>
    <?php else: ?>
        <h2>Admin Login</h2>
        <form action="" method="post">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
    <?php endif; ?>
</div>

</body>
</html>
