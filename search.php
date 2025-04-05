<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// API Key for fetching exchange rates
$apiKey = '681354cdbdc6c112e3309d61'; // Replace with your actual API key
$apiUrl = 'https://api.exchangerate-api.com/v4/latest/USD'; // Adjust API endpoint if needed

// Fetch exchange rates from the API
$response = file_get_contents($apiUrl);
if ($response === FALSE) {
    die('Error occurred while fetching data.');
}

// Decode the JSON response to an associative array
$data = json_decode($response, true);

// Store the exchange rates in a variable
$exchangeRates = $data['rates'];

// Function to get the exchange rate for a specific currency
function getCurrencyRate($currency, $exchangeRates) {
    // Check if the currency exists in the exchange rates array
    if (isset($exchangeRates[$currency])) {
        return $exchangeRates[$currency];
    }
    return false;
}

// Handle the form submission (search for a currency)
$currencyCode = null;
$rate = null;
$error = null;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize the input
    $currencyCode = strtoupper(trim($_POST['currency_code']));

    // Validate currency code
    if (empty($currencyCode)) {
        $error = "Currency code cannot be empty.";
    } else {
        // Fetch the exchange rate
        $rate = getCurrencyRate($currencyCode, $exchangeRates);
        if (!$rate) {
            $error = "No results found for '{$currencyCode}'. Please check the currency code and try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Currency Search</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

        /* Form Styles */
        .form-container {
            margin-top: 30px;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 3px 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: auto;
        }

        .form-container input, .form-container button {
            padding: 12px;
            margin: 10px 0;
            font-size: 16px;
            width: 100%;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-sizing: border-box;
            transition: all 0.3s ease;
        }

        .form-container input:focus, .form-container button:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0px 0px 10px rgba(52, 152, 219, 0.5);
        }

        .form-container input {
            background-color: #f9f9f9;
        }

        .form-container button {
            background-color: #3498db;
            color: white;
            cursor: pointer;
            border: none;
            font-weight: bold;
        }

        .form-container button:hover {
            background-color: #2980b9;
            transform: scale(1.05);
        }

        .error-message {
            color: red;
            font-size: 1.2em;
            margin-top: 10px;
        }

        /* Chart Container */
        .chart-container {
            width: 80%;
            margin: 30px auto;
            background-color: #1e1e1e;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 3px 15px rgba(255, 255, 255, 0.1);
        }

        .loading-spinner {
            margin-top: 20px;
            text-align: center;
            display: none;
        }

        .loading-spinner.active {
            display: block;
        }

        .tooltip {
            position: relative;
        }

        .tooltip .tooltiptext {
            visibility: hidden;
            width: 200px;
            background-color: #555;
            color: #fff;
            text-align: center;
            border-radius: 5px;
            padding: 5px 0;
            position: absolute;
            z-index: 1;
            bottom: 125%; /* Position above the text */
            left: 50%;
            margin-left: -100px;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .tooltip:hover .tooltiptext {
            visibility: visible;
            opacity: 1;
        }
        .logo-container {
            text-align: right;
        }

        .logo-container img {
            width: 150px;
            height: auto;
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
    <h1>Search Currency</h1>
        <p>Enter the currency code to get the exchange rate from USD.</p>
    </div>

    <!-- Currency Search Form -->
    <div class="form-container">
        <form action="" method="POST" onsubmit="showLoading(true)">
            <label for="currency_code" class="tooltip">Currency Code (e.g., USD, EUR, GBP):
                <span class="tooltiptext">Enter a valid 3-letter currency code.</span>
            </label>
            <input type="text" name="currency_code" id="currency_code" placeholder="Enter currency code (e.g., EUR)" required>
            <button type="submit">Search</button>

            <div class="loading-spinner" id="loadingSpinner">Loading...</div>
        </form>

        <?php if ($error): ?>
            <div class="error-message"><?= htmlspecialchars($error) ?></div>
        <?php elseif ($currencyCode && $rate !== null): ?>
            <h3>Exchange Rate for <?= htmlspecialchars($currencyCode) ?>:</h3>
            <p>1 USD = <?= number_format($rate, 4) ?> <?= htmlspecialchars($currencyCode) ?></p>

            <!-- Chart Container -->
            <div class="chart-container">
                <canvas id="currencyChart"></canvas>
            </div>

            <script>
            var ctx = document.getElementById('currencyChart').getContext('2d');

            // Generate realistic past exchange rate data for the chart (simulating the past 10 days)
            var pastData = [];
            var labels = [];
            for (let i = 9; i >= 0; i--) {
                labels.push(`T-${9 - i}`);
                pastData.push(<?= $rate ?> + (Math.random() * 0.1 - 0.05));  // Small random fluctuations around the actual rate
            }

            // Predict future data (generate predictions for the next 7 days, based on past data)
            var predictedData = [];
            var lastRate = pastData[pastData.length - 1];
            for (let i = 0; i < 7; i++) {
                var trend = (pastData[pastData.length - 1] - pastData[pastData.length - 2]) * 0.8; // Slight trend based on previous two points
                predictedData.push(lastRate + trend + (Math.random() * 0.05 - 0.02)); // Slight fluctuations on trend
                lastRate = predictedData[predictedData.length - 1];
            }

            var chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: [...labels, 'T+1', 'T+2', 'T+3', 'T+4', 'T+5', 'T+6', 'T+7'],  // Add future time points for 7 days
                    datasets: [
                        {
                            label: 'Exchange Rate (Past 10 Data Points)',
                            data: pastData,  // Past data only
                            borderColor: 'cyan',
                            backgroundColor: 'transparent',
                            borderWidth: 2,
                            pointStyle: 'circle',
                            pointRadius: 5
                        },
                        {
                            label: 'Prediction (Next 7 Days)',
                            data: predictedData,  // Prediction data only
                            borderColor: 'orange',  // New color for the prediction line
                            backgroundColor: 'transparent',
                            borderWidth: 2,
                            pointStyle: 'circle',
                            pointRadius: 5
                        }
                    ]
                }
            });
            </script>
        <?php endif; ?>
    </div>

    <!-- Script for toggling sidebar -->
    <script>
        function toggleSidebar() {
            document.querySelector(".sidebar").classList.toggle("active");
        }

        function showLoading(isLoading) {
            document.getElementById('loadingSpinner').classList.toggle('active', isLoading);
        }
    </script>
</body>
</html>
