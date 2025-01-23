<?php
session_start();
error_reporting(0);
include('includes/config.php');

if (strlen($_SESSION['admin_id']) == 0) {
    header('location:default.php');
} else {
    // Database connection
    $conn = new mysqli('localhost', 'root', '', 'voting');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Query to select all rows from the voters table (Total Voters)
    $queryVoters = "SELECT * FROM votersregistered";
    $resultVoters = $conn->query($queryVoters);
    if ($resultVoters) {
        $regVoters = mysqli_num_rows($resultVoters);
    } else {
        $regVoters = 0; // In case of error or no data
    }

    // Query to select all rows from the votes table (Total Votes)
    $queryVotes = "SELECT * FROM votes";
    $resultVotes = $conn->query($queryVotes);
    if ($resultVotes) {
        $totalVotes = mysqli_num_rows($resultVotes);
    } else {
        $totalVotes = 0; // In case of error or no data
    }

    $queryVoters = "SELECT * FROM voters";
    $resultVoters = $conn->query($queryVoters);
    if ($resultVoters) {
        $totalVoters = mysqli_num_rows($resultVoters);
    } else {
        $totalVoters = 0; // In case of error or no data
    }
    
    // Calculate the number of non-voters
    $nonVoters = $totalVoters - $totalVotes;

    // Calculate the percentages
    $votePercentage = ($totalVoters > 0) ? ($totalVotes / $totalVoters) * 100 : 0;
    $nonVotePercentage = ($totalVoters > 0) ? ($nonVoters / $totalVoters) * 100 : 0;

    // Close connection
    $conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <link href="assets/css/style.css" rel="stylesheet" />
    <title>Admin Dashboard - E-Voting System</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* General Styles */
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
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
        /* Sidebar Styles */
        #sidebar {
            width: 250px;
            height: 100vh;
            background-color: #2C3E50;
            color: white;
            position: fixed;
            top: 0;
            left: 0;
            padding-top: 20px;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
        }

        #sidebar a {
            color: white;
            text-decoration: none;
            padding: 15px 25px;
            font-size: 18px;
            border-bottom: 1px solid #34495E;
            display: flex;
            align-items: center;
            transition: background-color 0.3s ease;
        }

        #sidebar a:hover {
            background-color: rgba(34, 139, 34, 1);
        }

        #sidebar .sidebar-header {
            background-color: rgba(34, 139, 34, 0.8);
            padding: 20px;
            text-align: center;
        }

        /* Toggle Button */
        .navbar-toggle {
            position: absolute;
            top: 20px;
            left: 20px;
            display: none;
            z-index: 1000;
        }

        /* Main Content */
        .content {
            margin-left: 300px;
            padding: 20px;
            transition: margin-left 0.3s ease;
        }

        .navbar {
            background-color: rgba(34, 139, 34, 0.9);
            color: white;
            padding: 10px;
            text-align: center;
            font-size: 30px;
        }

        /* Widgets Section */
        .widgets {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-top: 20px;
        }

        .widget {
            background-color: rgba(34, 139, 34, 0.5);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .widget h3 {
            margin-bottom: 20px;
            font-size: 20px;
            color: #2c6b2f;
        }

        .widget .count {
            font-size: 32px;
            color: #2c6b2f;
            font-weight: bold;
        }

        .widget .description {
            color: #7f8c8d;
        }

        .main-section {
            margin-top: 20px;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #7f8c8d;
        }

       
        @media (max-width: 992px) {
            #sidebar {
                transform: translateX(-100%);
                position: absolute;
                margin-top:100px;
            }

            #sidebar.show {
                transform: translateX(0);
            }

            .content {
                margin-left: 0;
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
</head>
<body>

   
<div class="navbar-toggle" id="toggle-btn" style="color:green; background:transparent; border:none;">
         
         <span class="icon-bar"></span>
         <span class="icon-bar"></span>
         <span class="icon-bar"></span>
     
 
</div>

    <!-- Sidebar -->
    <div id="sidebar" class="sidebar" style="width: auto; height: 100vh; background-color: #2C3E50; color: white; position: fixed; top: 0; left: 0; padding-top: 20px; transition: all 0.3s ease;">
   
   <div style="background-color: rgba(34, 139, 34, 0.8); width: 100%; padding: 20px; text-align: center;" id="h2">
           <h2 style="color: white; margin: 0;">Admin</h2>
       </div>
       <a href="dashboard.php" style="display: flex; align-items: center; color: white; text-decoration: none; padding: 15px 25px; width: 100%; font-size: 18px; transition: background-color 0.3s ease; border-bottom: 1px solid #34495E;">
           <i class="fas fa-tachometer-alt" style="margin-right: 15px; font-size: 20px;"></i>
           Dashboard
       </a>
       <a href="add-candidate.php" style="display: flex; align-items: center; color: white; text-decoration: none; padding: 15px 25px; width: 100%; font-size: 18px; border-bottom: 1px solid #34495E;">
           <i class="fas fa-user-plus" style="margin-right: 15px;"></i>Candidates
       </a>
       <?php if ($totalVotes > 0) { ?>
           <a href="results.php" style="display: flex; align-items: center; color: white; text-decoration: none; padding: 15px 25px; width: 100%; font-size: 18px; border-bottom: 1px solid #34495E;">
               <i class="fas fa-chart-pie" style="margin-right: 15px;"></i> Election Results
           </a>
       <?php } ?>
       <!-- <a href="reqister-voters.php" style="display: flex; align-items: center; color: white; text-decoration: none; padding: 15px 25px; width: 100%; font-size: 18px; border-bottom: 1px solid #34495E;">
           <i class="fas fa-users" style="margin-right: 15px;"></i> Request voting code
       </a> -->
        
       <a href="delete-all.php" onclick="return confirm('Are you sure you want to delete all registered voters?');" style="display: flex; align-items: center; color: white; text-decoration: none; padding: 15px 25px; width: 100%; font-size: 18px; border-bottom: 1px solid #34495E;">
           <i class="fas fa-trash-alt" style="margin-right: 15px;"></i> Clear All Registered Voters
       </a>
       <a href="delete_admin.php" 
   style="display: flex; align-items: center; color: white; text-decoration: none; padding: 15px 25px; width: 100%; font-size: 18px; border-bottom: 1px solid #34495E;" 
   onclick="return confirm('Are you sure you want to delete this account?');">
    <i class="fas fa-user-slash" style="margin-right: 15px;"></i> Delete Account
</a>


       <a href="fetch-voters.php" style="display: flex; align-items: center; color: white; text-decoration: none; padding: 15px 25px; width: 100%; font-size: 18px; border-bottom: 1px solid #34495E;">
           <i class="fas fa-users" style="margin-right: 15px;"></i> Pull Voters
       </a>
       <a href="logout.php" style="display: flex; align-items: center; color: white; text-decoration: none; padding: 15px 25px; width: 100%; font-size: 18px; border-bottom: 1px solid #34495E;">
           <i class="fas fa-sign-out-alt" style="margin-right: 15px;"></i> Logout
       </a>
   </div>

    <!-- Main Content -->
    <div onclick="remgt()">
    <div class="content" >
        <div class="navbar">Admin Dashboard - E-Voting System</div>

        <!-- Widgets Section -->
        <div class="widgets">
            <div class="widget">
                <h3>Total Votes</h3>
                <div class="count"><?php echo $totalVotes; ?></div>
                <div class="description">Total Votes</div>
            </div>
            <div class="widget">
                <h3>Total Registered Voters</h3>
                <div class="count"><?php echo $regVoters; ?></div>
                <div class="description">Total number of registered voters</div>
            </div>
            <div class="widget">
                <h3>Voters Requested to Participate</h3>
                <div class="count"><?php echo $totalVoters; ?></div>
                <div class="description">Total voters</div>
            </div>
        </div>

        <!-- Chart Section -->
        <div class="main-section">
            <h2>Voter Participation Statistics</h2>
            <div style="width: 30%; float: left;">
                <canvas id="pieChart"></canvas>
            </div>
            <div style="width: 55%; float: right;">
                <canvas id="barChart"></canvas>
            </div>
        </div>

       </div>

    <!-- Chart.js Scripts -->
    <script>
        // Pie Chart - Votes vs Non-Votes with percentages
        var ctxPie = document.getElementById('pieChart').getContext('2d');
        var pieChart = new Chart(ctxPie, {
            type: 'pie',
            data: {
                labels: [
                    'Voted (' + <?php echo round($votePercentage, 2); ?> + '%)',
                    'Not Yet Voted (' + <?php echo round($nonVotePercentage, 2); ?> + '%)'
                ],
                datasets: [{
                    data: [<?php echo $totalVotes; ?>, <?php echo $nonVoters; ?>],
                    backgroundColor: [
                        'rgba(76, 175, 80, 0.9)',
                        'rgba(255, 0, 0, 0.9)'
                    ],
                    borderColor: [
                        'rgba(76, 175, 80, 1)',
                        'rgba(255, 0, 0, .9)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                }
            }
        });

        // Bar Chart - Total Voters vs Total Votes with percentages
        var ctxBar = document.getElementById('barChart').getContext('2d');
        var barChart = new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: ['Registered Voters', 'Voters Participated'],
                datasets: [{
                    label: 'Total',
                    data: [<?php echo $regVoters; ?>, <?php echo $totalVotes; ?>],
                    backgroundColor: ['#4CAF50', 'orange'],
                    borderColor: ['#4CAF50', 'orange'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Sidebar Toggle Function
        const toggleButton = document.getElementById('toggle-btn');
        const sidebar = document.getElementById('sidebar');

        toggleButton.addEventListener('click', function() {
            sidebar.classList.toggle('show');
        });

        window.addEventListener('resize', function() {
            if (window.innerWidth <= 768) {
                toggleButton.style.display = 'block';
            } else {
                toggleButton.style.display = 'none';
                sidebar.classList.remove('show');
            }
        });
        function remgt(){
            toggleButton.style.display = 'block';
                sidebar.classList.remove('show');
        }
    </script>

</body>
</html>
<?php } ?>
