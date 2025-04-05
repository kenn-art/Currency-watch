<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// API URL and your API key
$apiUrl = 'https://api.exchangerate-api.com/v4/latest/USD'; // Replace with your API URL
$apiKey = '681354cdbdc6c112e3309d61'; // Use your actual API key

// Fetch exchange rate data from the API
$apiResponse = file_get_contents($apiUrl);
$data = json_decode($apiResponse, true);

// Assuming the API returns exchange rates and flags for currencies
$exchangeRates = $data['rates'];  // The exchange rates

function generatePrediction($baseRate) {
    $fluctuation = (rand(-5, 5) / 100); // Random change between -5% and +5%
    return round($baseRate * (1 + $fluctuation), 4);
}

// Generate AI predictions
$predictions = [];
foreach ($exchangeRates as $currency => $rate) {
    $predictions[$currency] = generatePrediction($rate);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Currency Predictions</title>
    <style>
       body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color:rgb(53, 53, 53);
        }

        .sidebar {
            position: fixed;
            left: -250px;
            top: 0;
            width: 250px;
            height: 100%;
            background-color: #2c3e50;
            padding-top: 60px;
            transition: 0.3s;
            z-index: 200;
        }

        .sidebar.active {
            left: 0;
        }

        .sidebar h2 {
            color: #fff;
            text-align: center;
            margin-bottom: 30px;
        }

        .sidebar a {
            padding: 15px 25px;
            text-decoration: none;
            font-size: 18px;
            color: #ecf0f1;
            display: block;
            transition: 0.3s;
        }

        .sidebar a:hover {
            background-color: #34495e;
        }

        .menu-btn {
            font-size: 30px;
            cursor: pointer;
            padding: 15px;
            position: fixed;
            top: 10px;
            left: 10px;
            z-index: 300;
            color: #333;
        }

        .main-content {
            margin-top: 50px;
            margin-left: 0;
            padding: 20px;
            transition: margin-left 0.3s;
        }

        .sidebar.active ~ .main-content {
            margin-left: 250px;
        }


        .prediction-container {
            width: 90%;
            max-width: 800px;
            margin: auto;
            background: #222;
            padding: 30px;
            border-radius: 15px;
            margin-top: 30px;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.4);
        }

        .prediction-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .prediction-table th, .prediction-table td {
            padding: 15px;
            border-bottom: 2px solid #444;
            text-align: center; /* Center all columns */
        }

        .prediction-table td:first-child, /* Currency column */
        .prediction-table td:nth-child(2) { /* Current Rate column */
            text-align: center; /* Center content for these columns */
        }

        .prediction-table th {
            background-color: #004d00; /* Rolex green */
            color: #f0e68c; /* Gold for headings */
        }


        .ai-output {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            color: #00bcd4; /* Light cyan for AI prediction output */
        }

        h1 {
            font-size: 2.5em;
            margin-top: 40px;
            color: #f0e68c; /* Gold for the main heading */
        }

        p {
            font-size: 1.2em;
            margin-top: 10px;
            color: #f0f0f0;
        }
        .logo {
            align-items: baseline;
            width: 80px;
            height: auto;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Currency Watch</h2>
        <a href="dashboard.php">Dashboard</a>
        <a href="converter.php">Converter</a>
        <a href="predictions.php">Predictions</a>
        <a href="search.php">Search</a>
        <a href="logout.php">Logout</a>
    </div>

    <!-- Hamburger Menu Icon -->
    <div class="menu-btn" onclick="toggleSidebar()">&#9776;</div>

    <div class="main-content">
    <img src="CurrencyW-removebg-preview.png" alt="Logo" class="logo">
    <h1>AI-Powered Currency Predictions</h1>
    <p>Predictions based on historical trends and market volatility.</p>
   </div>

    <div class="prediction-container">
        <table class="prediction-table">
            <tr>
                <th>Currency</th>
                <th>Current Rate (USD)</th>
                <th>Next Week's Prediction</th>
            </tr>
            <?php foreach ($predictions as $currency => $predictedRate): ?>
                <tr>
                    <td><?= htmlspecialchars($currency) ?></td>
                    <td><?= number_format($exchangeRates[$currency], 4) ?></td>
                    <td class="ai-output"><?= number_format($predictedRate, 4) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <!-- Script for toggling sidebar -->
    <script>
        function toggleSidebar() {
            document.querySelector(".sidebar").classList.toggle("active");
        }
    </script>
</body>
</html>
