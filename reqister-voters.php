<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('includes/config.php');

if(strlen($_SESSION['admin_id'])==0) { 
    header('location:admin.php');
} else { 
    ?>

<?php
// Database connection (replace with your actual connection details)
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'voting';
$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to get the year prefix
function getYearPrefix($year) {
    $currentYear = date("Y"); // Get the current year
    
    switch ($year) {
        case 'First Year':
            // For First Year, use the current year
            return $currentYear-1;
        case 'Second Year':
            // For Second Year, use the previous year
            return $currentYear - 2;
        case 'Third Year':
            // For Third Year, use the year before that
            return $currentYear - 3;
        default:
            return 'Unknown';
    }
}

// Function to check if admission number already exists
function admissionExists($conn, $admission_number) {
    $stmt = $conn->prepare("SELECT id FROM voter_registration WHERE admission_number = ?");
    $stmt->bind_param('s', $admission_number);
    $stmt->execute();
    $stmt->store_result();
    return $stmt->num_rows > 0; // If rows exist, admission number is already taken
}

// First Year Form Processing
if (isset($_POST['code1'], $_POST['code2'], $_POST['code3'], $_POST['code4'])) {
    $voter_code = $_POST['code1'] . $_POST['code2'] . $_POST['code3'] . $_POST['code4']; // Combine the code from individual input fields
    $year = 'First Year'; // Example, based on the form you're handling

    // Get the appropriate admission number prefix for First Year
    $admission_number = getYearPrefix($year) . '/SD/' . $voter_code;

    // Check if the admission number already exists
    if (admissionExists($conn, $admission_number)) {
        echo '<div class="error-message">Error: This Voter is already Requested to Vote!</div>';
    } else {
        // Insert into the database without encryption
        $stmt = $conn->prepare("INSERT INTO voter_registration (admission_number, year, voter_code) VALUES (?, ?, ?)");
        $stmt->bind_param('sss', $admission_number, $year, $voter_code);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $last_id = $conn->insert_id; // Get the ID of the inserted voter
            echo '<div class="success-message">Success: First Year Voter Requested successfully! ID: ' . $last_id . '</div>';
            echo '<script>alert("Requested successfully! ID: ' . $last_id . '");</script>';
        } else {
            echo '<div class="error-message">Error: Requesting First Year Code!.</div>';
        }
        $stmt->close();
    }
}

// Second Year Form Processing
if (isset($_POST['code5'], $_POST['code6'], $_POST['code7'], $_POST['code8'])) {
    $voter_code = $_POST['code5'] . $_POST['code6'] . $_POST['code7'] . $_POST['code8'];
    $year = 'Second Year';

    // Get the appropriate admission number prefix for Second Year
    $admission_number = getYearPrefix($year) . '/SD/' . $voter_code;

    // Check if the admission number already exists
    if (admissionExists($conn, $admission_number)) {
        echo '<div class="error-message">Error: This Voter is already Requested to Vote!</div>';
    } else {
        $stmt = $conn->prepare("INSERT INTO voter_registration (admission_number, year, voter_code) VALUES (?, ?, ?)");
        $stmt->bind_param('sss', $admission_number, $year, $voter_code);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $last_id = $conn->insert_id; // Get the ID of the inserted voter
            echo '<div class="success-message">Success: Second Year Voter Requested successfully! ID: ' . $last_id . '</div>';
            echo '<script>alert("Requested successfully! ID: ' . $last_id . '");</script>';
        } else {
            echo '<div class="error-message">Error: Requesting Second Year Code!.</div>';
        }
        $stmt->close();
    }
}

// Third Year Form Processing
if (isset($_POST['code9'], $_POST['code10'], $_POST['code11'], $_POST['code12'])) {
    $voter_code = $_POST['code9'] . $_POST['code10'] . $_POST['code11'] . $_POST['code12'];
    $year = 'Third Year';

    // Get the appropriate admission number prefix for Third Year
    $admission_number = getYearPrefix($year) . '/SD/' . $voter_code;

    // Check if the admission number already exists
    if (admissionExists($conn, $admission_number)) {
        echo '<div class="error-message">Error: This Voter is already Requested to Vote!</div>';
    } else {
        $stmt = $conn->prepare("INSERT INTO voter_registration (admission_number, year, voter_code) VALUES (?, ?, ?)");
        $stmt->bind_param('sss', $admission_number, $year, $voter_code);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $last_id = $conn->insert_id; // Get the ID of the inserted voter
            echo '<div class="success-message">Success: Third Year Voter Requested successfully! ID: ' . $last_id . '</div>';
            echo '<script>alert("Requested successfully! ID: ' . $last_id . '");</script>';
        } else {
            echo '<div class="error-message">Error: Requesting Third Year Code!.</div>';
        }
        $stmt->close();
    }
}

// On Page Load: Fetch the last inserted data from voter_registration table and insert into voters table
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $stmt = $conn->prepare("SELECT id, admission_number FROM voter_registration ORDER BY id DESC LIMIT 1");
    $stmt->execute();
    $stmt->bind_result($last_id, $admission_number);
    $stmt->fetch();
}

$conn->close();
?> 

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-VOTING | MOROGORO TEACHERS COLLEGE VOTING SYSTEM</title>
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <link href="assets/css/style.css" rel="stylesheet" />
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />

    <!-- jQuery Library -->
    <script src="assets/js/jquery-1.10.2.js"></script>

    <style>
        /* Basic Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

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

        .navbar {
            width: 100%;
            background-color: rgba(76, 175, 80, 0.4);
            color: white;
            padding: 10px 0;
            text-align: center;
            margin-bottom: 30px;
        }

        .evoting-title {
            width: 100%;
            background: rgba(76, 175, 80, 0.6);
            padding: 40px 0;
            text-align: center;
            color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: -10px;
            border: 2px solid rgba(76, 175, 80, 0.4);
            margin-bottom: 30px;
        }

        .evoting-title h1 {
            font-size: 48px;
            font-weight: bold;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin: 0;
        }

        /* Center the boxes within the content */
        .year-container {
            display: flex;
            justify-content: space-evenly; /* Distribute forms evenly in the row */
            align-items: center;
            flex-wrap: nowrap; /* Prevent wrapping */
            gap: 30px;
            width: 100%;
            margin-top: 30px; /* Add margin to space from title */
        }

        .year-section {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            border: 2px solid #4CAF50;
            width: 250px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease-in-out;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .year-section:hover {
            transform: translateY(-10px);
        }

        .year-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 15px;
            color: #4CAF50;
            text-transform: uppercase;
        }

        .box {
            width: 60px;
            height: 60px;
            border: 2px solid #4CAF50;
            border-radius: 8px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #fff;
            margin-bottom: 15px;
            transition: all 0.3s ease-in-out;
        }

        .box:focus {
            outline: none;
            border-color: #FF5722;
            background-color: #FFEB3B;
            transform: scale(1.2);
        }

        input[type="text"] {
            width: 70px;
            padding:20px;
            margin:20px;
            text-align: center;
            font-size: 30px;
            border: 2px solid green;
            outline: green;
            background-color: transparent;
            border-radius:12px;
        }

        .submit-btn {
            margin-top: 15px;
            padding: 12px 25px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .submit-btn:hover {
            background-color: #45a049;
        }

        /* Sidebar Styles */
        aside {
            position: fixed;
            top: 10px;
            left: 10px;
            height: 100vh;
            width: 250px;
            background-color: rgba(34, 139, 34, 0.9); /* Green with 80% opacity */
            color: white;
            padding: 20px;
            box-shadow: 4px 0 6px rgba(0, 0, 0, 0.2);
        }

        aside h2 {
            font-size: 24px;
            margin-bottom: 30px;
            text-align: center;
        }

        aside a {
            color: #ccc;
            text-decoration: none;
            display: block;
            padding: 12px;
            margin: 10px 0;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }

        aside a:hover {
            background-color: rgba(34, 139, 34, 1);
                color:white;
                text-decoration:none;
        }
        .navbar-toggle {
            position: absolute;
            top: 20px;
            left: 20px;
            display: none;
            z-index: 1000;
        }
        /* Push content to the right */
        .content {
            margin-left: 250px;
            padding: 20px;
            width: calc(100% - 270px); /* Adjust content width to fit sidebar */
        }

        /* Mobile and smaller screen adjustments */
        @media (max-width: 768px) {
            .year-container {
                flex-direction: column;
                gap: 20px;
            }

            .year-section {
                width: 90%;
                max-width: 350px;
            }

            .content {
                margin-left: 0;
                width: 100%;
            }
        }

        @media (max-width: 480px) {
            .year-section {
                width: 100%;
                max-width: 280px;
            }

            .box {
                width: 50px;
                height: 50px;
            }

            .submit-btn {
                width: 100%;
            }

            .navbar .header-wrapper img {
                width: 100%;
            }
        }

        /* Footer styles */
        .footer-section {
            border: 2px solid rgba(76, 175, 80, 0.4); /* Green border with opacity */
            padding-top: 30px;
            margin-top: 30px;
        }
                /* Success and Error Message Styles */
        .success-message {
            color: white;
            background-color: rgba(34, 139, 34, 0.6);
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            margin-top: 20px;
        }

        .error-message {
            color: white;
            background-color: #f44336;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            margin-top: 20px;
        }
        
        @media (max-width: 992px) {
            #aside {
                transform: translateX(-110%);
                position: absolute;
                margin-top:100px;
            
            }

            #aside.show {
                transform: translateX(0);
            }

            .content {
                margin-left: 0;
                position:relative;
            }

            .navbar-toggle {
                display: block;
            }
            #h2{
                display:none;
            }
            .navbar{
                background:transparent;
                color:green;
                padding:20px;
                right:-20px;
            }
            .evoting-title{
                right:-10px;
                background:transparent;
                color:green;
                width:100%;
                border:none;
            }
        }
        
#toggle-btn {
    cursor: pointer;
   
    padding: 10px;
}

#toggle-btn .icon-bar {
   
    width: 30px;      
    height: 4px;      
    margin: 6px auto; 
    background-color: #000;
    border-radius: 2px;
    
}


#toggle-btn:hover .icon-bar {
    background-color: #4CAF50;
   
}


    </style>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body onload="transfer()">
<div class="navbar-toggle" id="toggle-btn" style="color:green; background:transparent; border:none;">
         
         <span class="icon-bar"></span>
         <span class="icon-bar"></span>
         <span class="icon-bar"></span>
     
 
</div>

    <!-- Sidebar (Aside Section) -->
    <aside id="aside" style="width: 250px; background-color: #2C3E50; color: white; height: 100vh; display: flex; flex-direction: column; align-items: flex-start; padding-top: 20px; transition: all 0.3s ease;">
    <div style="background-color: #27AE60; width: 100%; padding: 20px; text-align: center; border-bottom: 2px solid #1B6B3A;">
        <h2 style="font-size: 24px; font-weight: 700; margin: 0; color: white;">Admin</h2>
    </div>
    
    <a href="dashboard.php" style="display: flex; align-items: center; color: white; text-decoration: none; padding: 15px 25px; width: 100%; font-size: 18px; transition: background-color 0.3s ease; border-bottom: 1px solid #34495E;">
        <i class="fas fa-tachometer-alt" style="margin-right: 15px; font-size: 20px;"></i>
        Dashboard
    </a>
</aside>

    <!-- Main Content -->
    <div class="content">
        <div class="evoting-title">
            <h1>E-VOTING SYSTEM -- Codes Request</h1>
        </div>

        <div class="row">
            <!-- First Year Form -->
            <div class="col-md-4">
                <form method="POST" id="firstYearForm">
                    <div class="year-section">
                        <div class="year-title">First Year</div>
                        <input type="text" maxlength="1" id="firstYearBox1" name="code1" oninput="moveFocus(event, 'firstYearBox2')" required/>
                        <input type="text" maxlength="1" id="firstYearBox2" name="code2" oninput="moveFocus(event, 'firstYearBox3')" required/>
                        <input type="text" maxlength="1" id="firstYearBox3" name="code3" oninput="moveFocus(event, 'firstYearBox4')" required/>
                        <input type="text" maxlength="1" id="firstYearBox4" name="code4" oninput="moveFocus(event, 'firstYearBox4')" required/>
                    </div>
                    <button type="submit" class="submit-btn">Request Voting Code</button>
                </form>
            </div>

            <!-- Second Year Form -->
            <div class="col-md-4">
                <form method="POST" id="secondYearForm">
                    <div class="year-section">
                        <div class="year-title">Second Year</div>
                        <input type="text" maxlength="1" id="secondYearBox1" name="code5" oninput="moveFocus(event, 'secondYearBox2')" required/>
                        <input type="text" maxlength="1" id="secondYearBox2" name="code6" oninput="moveFocus(event, 'secondYearBox3')" required/>
                        <input type="text" maxlength="1" id="secondYearBox3" name="code7" oninput="moveFocus(event, 'secondYearBox4')" required/>
                        <input type="text" maxlength="1" id="secondYearBox4" name="code8" required/>
                    </div>
                    <button type="submit" class="submit-btn">Request Voting Code</button>
                </form>
            </div>

            <!-- Third Year Form -->
            <div class="col-md-4">
                <form method="POST" id="thirdYearForm">
                    <div class="year-section">
                        <div class="year-title">Third Year</div>
                        <input type="text" maxlength="1" id="thirdYearBox1" name="code9" oninput="moveFocus(event, 'thirdYearBox2')" required/>
                        <input type="text" maxlength="1" id="thirdYearBox2" name="code10" oninput="moveFocus(event, 'thirdYearBox3')" required/>
                        <input type="text" maxlength="1" id="thirdYearBox3" name="code11" oninput="moveFocus(event, 'thirdYearBox4')" required/>
                        <input type="text" maxlength="1" id="thirdYearBox4" name="code12" required/>
                    </div>
                    <button type="submit" class="submit-btn">Request Voting Code</button>
                </form>
            </div>

        </div>
    </div>
</body>
<script>
        // Move focus to the next input field after each input is entered
        function moveFocus(event, nextFieldId) {
            if (event.target.value.length == event.target.maxLength) {
                document.getElementById(nextFieldId).focus();
            }
        }
           // Function to call the PHP script for transferring voter data
        function transfer() {
            // Create an XMLHttpRequest object to communicate with the server
            var xhr = new XMLHttpRequest();
            
            // Configure the request: GET request to the PHP script
            xhr.open("GET", "transfer_voter.php", true);

            // When the request is done, check the response
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    // Display the response (for now, just alert it)
                    alert(xhr.responseText);
                }
            };
            // Send the request to the server
            xhr.send();
        }
             // Sidebar Toggle Function
             const toggleButton = document.getElementById('toggle-btn');
        const aside = document.getElementById('aside');

        toggleButton.addEventListener('click', function() {
            aside.classList.toggle('show');
        });

        window.addEventListener('resize', function() {
            if (window.innerWidth <= 768) {
                toggleButton.style.display = 'block';
            } else {
                toggleButton.style.display = 'none';
                aside.classList.remove('show');
            }
        });
    </script>
</html>
<?php } ?>