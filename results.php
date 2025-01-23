<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <link href="assets/css/style.css" rel="stylesheet" />
    <title>Voting Results - E-Voting System</title>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            color: #333;
            width: 100%;
            height: 100%;
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

        table {
            width: 70%; /* Set table width to 80% */
            border-collapse: collapse;
            margin: 20px auto; /* Center the table */
        }

        table th, table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        table th {
            background-color: rgba(34, 139, 34, 0.8);
            color: white;
        }

        table tr:hover {
            background-color: rgba(34, 139, 34, 0.2);
        }

        .bar-chart-container {
            margin-top: 10px;
            width: 60%; /* Set chart container width to 80% */
            margin: auto;
        }

        h1 {
            text-align: center;
            margin-top: 20px;
            font-size: 3.5rem;
            color: #28a745;
        }

        .chart-labels {
            text-align: center;
            font-size: 1.6rem;
            margin-top: 20px;
        }

        .content {
            text-align: center; /* Center all content */
            padding: 20px;
        }

        /* Print Button Style */
        .print-button {
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="content">
    <h1>Voting Results</h1>

    <!-- Table showing Voting Results -->
    <div class="table-responsive">
        <table border="1px" class="table" style="border:2px double green;">
            <thead style="font-size:20px;">
                <tr>
                    <th>President</th>
                    <th>Vice-President</th>
                    <th>Total Votes</th>
                    <th>Percentage</th>
                    <th>Difference in Percentage</th>
                </tr>
            </thead>
            <tbody id="resultsTableBody">
                <!-- Table rows will be populated via AJAX -->
            </tbody>
        </table>
    </div>

    <!-- Display Non-Voters -->
    <div style="text-align:center; margin-top: 20px;">
        <h3>Total Non-Voters</h3>
        <p><strong id="nonVoters"></strong> non-voters</p>
        <p>Percentage of Non-Voters: <strong id="nonVotersPercentage"></strong>%</p>
    </div>

    <!-- Bar Chart showing Voting Results -->
    <div class="bar-chart-container">
        <canvas id="barChart"></canvas>
    </div>

    <!-- Print Button -->
    <div class="print-button">
        <button onclick="printPage()" class="btn btn-primary" style="width:50%;">Print Results</button>
    </div>

    <center>
        <a href="dashboard.php" class="btn btn-success" style="width:50%; margin:20px;">Back</a>
    </center>
</div>

<script>
    let chartInstance; // This will store the chart instance to update dynamically

    // Function to load the voting results via AJAX and update the bar chart dynamically
    function loadVotingResults() {
        $.ajax({
            url: 'voting_results.php', // The PHP file fetching results from the database
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                if (data) {
                    const pairs = data.pairs;
                    const totalVotes = data.totalVotes;
                    const totalVoters = data.totalVoters;
                    const nonVoters = data.nonVoters;

                    // Update the table dynamically
                    let tableBody = '';
                    let previousPercentage = null;

                    pairs.forEach(pair => {
                        const combinedVotes = pair.total_votes;
                        const percentage = (combinedVotes / totalVotes) * 100;
                        const diffPercentage = previousPercentage !== null
                            ? (percentage - previousPercentage).toFixed(2)
                            : "0.00";
                        previousPercentage = percentage;

                        tableBody += ` 
                            <tr>
                                <td style='font-size:20px;'><b>${pair.president_name}</b></td>
                                <td style='font-size:20px;'>${pair.vice_name}</td>
                                <td>${combinedVotes}</td>
                                <td>${percentage.toFixed(2)}%</td>
                                <td>${diffPercentage}%</td>
                            </tr>
                        `;
                    });

                    $('#resultsTableBody').html(tableBody);

                    // Update Non-Voters section
                    $('#nonVoters').text(nonVoters);
                    $('#nonVotersPercentage').text(((nonVoters / totalVoters) * 100).toFixed(2));

                    // Prepare data for the bar chart
                    const results = pairs.map(pair => ({
                        name: `${pair.president_name} & ${pair.vice_name}`,
                        votes: pair.total_votes,
                        percentage: ((pair.total_votes / totalVotes) * 100).toFixed(2)
                    }));

                    // Add non-voters to the results for charting
                    results.push({
                        name: 'Non-Voters',
                        votes: nonVoters,
                        percentage: ((nonVoters / totalVoters) * 100).toFixed(2)
                    });

                    const barColors = results.map((result, index) => {
                        if (result.name === 'Non-Voters') {
                            return 'rgba(255, 0, 0, 0.8)'; // Red for non-voters
                        }
                        return index % 2 === 0 ? 'green' : 'orange'; // Alternating colors
                    });

                    // Update the Bar Chart if already initialized, otherwise create a new one
                    if (chartInstance) {
                        chartInstance.data.labels = results.map(result => result.name);
                        chartInstance.data.datasets[0].data = results.map(result => result.percentage);
                        chartInstance.data.datasets[0].backgroundColor = barColors;
                        chartInstance.data.datasets[0].borderColor = barColors.map(color => color.replace('0.7', '1'));
                        chartInstance.update();
                    } else {
                        // Render Bar Chart for the first time
                        const ctxBar = document.getElementById('barChart').getContext('2d');
                        chartInstance = new Chart(ctxBar, {
                            type: 'bar',
                            data: {
                                labels: results.map(result => result.name),
                                datasets: [{
                                    label: 'Total percentage (%)',
                                    data: results.map(result => result.percentage),
                                    backgroundColor: barColors,
                                    borderColor: barColors.map(color => color.replace('0.7', '1')),
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                responsive: true,
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        max: 100
                                    }
                                }
                            }
                        });
                    }
                }
            }
        });
    }

    // Load the voting results when the page is ready
    $(document).ready(function() {
        loadVotingResults();
        
        // Refresh data every 500ms (this will call loadVotingResults and update both table and chart)
        setInterval(loadVotingResults, 500);
    });

    // Print Page function
    function printPage() {
        window.print();
    }
</script>

</body>
</html>
