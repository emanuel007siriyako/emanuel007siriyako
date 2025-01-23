<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "voting";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to fetch the required data (Admission Number, Voter ID, Voter Name) and order by Voter Name
$query = "
    SELECT 
        vr.Admissionnumber,vr.option, 
        v.voter_id, 
        CONCAT(vr.FName, ' ', vr.MName, ' ', vr.LName) AS VoterName
    FROM 
        votersregistered vr
    JOIN 
        voters v ON vr.Admissionnumber = v.admissionnumber
    WHERE 
        v.status = 'active'
    ORDER BY 
        VoterName ASC
";

$result = $conn->query($query);

// Check if any results were returned
if ($result->num_rows > 0):
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Active Voters</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: flex-start; /* Align to the top */
            height: auto;
            margin: 0;
            background-color: #f4f4f4;
        }
        .container {
            width: 80%;
            max-width: 1000px;
            background-color: white;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            font-size: 24px;
        }
        .search-panel {
            margin-bottom: 20px;
        }
        .search-panel input {
            padding: 10px;
            font-size: 16px;
            width: 50%;
            max-width: 400px;
            border: 1px solid #ddd;
            border-radius: 4px;
            display: inline-block;
        }
        .button {
            padding: 12px 18px;
            font-size: 18px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            margin-top: 10px;
        }
        .button:hover {
            background-color: #45a049;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 18px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-size: 20px;
        }
        td:first-child {
            text-align: center;
        }
        /* Align the checkbox column */
        td:last-child {
            text-align: center;
        }
       /* Apply scaling to checkboxes */
input[type='checkbox'] {
    transform: scale(2.5); /* Scale by 1.5 times */
    margin: 0; /* Optional to remove any margin */
    cursor: pointer; /* Optional: to change the cursor to a pointer when hovering */
}
i{
    padding-right: 12px;
}

    </style>
    <script>
        // JavaScript function to print the table
        function printTable() {
            var printContent = document.getElementById('voterTable').outerHTML;
            var newWindow = window.open('', '', 'width=800, height=600');
            newWindow.document.write('<html><head><title>Print Voter List</title></head><body>');
            newWindow.document.write(printContent);
            newWindow.document.write('</body></html>');
            newWindow.document.close();
            newWindow.print();
        }

        // JavaScript function to search the table
        function searchTable() {
            var input, filter, table, tr, td, i, j, txtValue;
            input = document.getElementById("searchInput");
            filter = input.value.toUpperCase();
            table = document.getElementById("voterTable");
            tr = table.getElementsByTagName("tr");

            // Loop through all rows, and hide those that don't match the search query
            for (i = 1; i < tr.length; i++) { // Start from 1 to skip header row
                td = tr[i].getElementsByTagName("td");
                let found = false;
                for (j = 0; j < td.length; j++) {
                    if (td[j]) {
                        txtValue = td[j].textContent || td[j].innerText;
                        if (txtValue.toUpperCase().indexOf(filter) > -1) {
                            found = true;
                            break; // Stop once a match is found
                        }
                    }
                }
                tr[i].style.display = found ? "" : "none";
            }
        }
    </script>
</head>
<body>

<div class="container">
    <h2>Active Voters List</h2>

    <!-- Search Panel -->
    <div class="search-panel">
        <input type="text" id="searchInput" onkeyup="searchTable()" placeholder="Search by Voter ID or Name">
    </div>

    <!-- Print Button -->
    <button class="button" onclick="printTable()"><i class="fa fa-print"></i>Print Voter List</button>
    <a class="button" href = "dashboard.php" style="text-decoration:none; float:right;"><i class="fa fa-dashboard"></i>Dashboard</a>

    <table id="voterTable">
        <thead>
            <tr>
                <th>SN</th>
                <th>Voter ID</th>
                <th>Voter Name</th>
                <!-- <th>Option</th> -->
                <th>Checked</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $serial = 1; // Initialize serial number
            while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $serial++; ?></td> <!-- Increment serial number -->
                    <td><?php echo $row['voter_id']; ?></td>
                    <td><?php echo $row['VoterName']; ?></td>
                   
                    <td><input type="checkbox" name="voter_select[]" value="<?php echo $row['voter_id']; ?>" ></td> 
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>

<?php
else:
    echo "No active voters found.";
endif;

$conn->close();
?>
