<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch currency exchange rates from an API
$api_url = "https://v6.exchangerate-api.com/v6/681354cdbdc6c112e3309d61/latest/USD"; // Replace with a real API
$response = file_get_contents($api_url);
$exchange_data = json_decode($response, true);

// Example: Extracting a few exchange rates
$rates = $exchange_data['conversion_rates'] ?? [];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
       body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: rgb(53, 53, 53);
            color: #ecf0f1;
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
            color: #fff;
        }

        .main-content {
            margin-top: 10px;
            margin-left: 0;
            padding: 20px;
            transition: margin-left 0.3s;
        }

        .sidebar.active ~ .main-content {
            margin-left: 250px;
        }

        .currency-section {
            background-color: #34495e;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
        }

        .currency-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .currency-table th, .currency-table td {
            padding: 10px;
            border: 1px solid #ecf0f1;
            text-align: left;
        }

        .logo-container {
      
            margin-left: 1500px;
           
        }

        .logo-container img {
            margin-top: 50px;
            width: 90px;
            height: flex;
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
        <a href="login.php">Logout</a>
    </div>

    <!-- Hamburger Menu Icon -->
    <div class="menu-btn" onclick="toggleSidebar()">&#9776;</div>

    <!-- Main content section -->
        <div class="logo-container">
        <img src="CurrencyW-removebg-preview.png" alt="Logo" class="logo">
        </div>
        <div class="main-content">
        <div class="hero">
            <h1>Welcome, <?= htmlspecialchars($_SESSION['username']) ?>!</h1>
            <p>Stay updated with real-time currency trends and predictions.</p>
        </div>

        <!-- Currencies Overview Section -->
        <div class="currency-section">
            <h2>About Currencies</h2>
            <p>Currencies are the backbone of global trade. The exchange rate of a currency affects international business, travel, and investments. Here are some major currencies:</p>
            <ul>
                <li><strong>USD ($) - United States Dollar</strong>: The most widely traded currency.</li>
                <li><strong>EUR (€) - Euro</strong>: Used by the European Union countries.</li>
                <li><strong>JPY (¥) - Japanese Yen</strong>: The third most traded currency in the world.</li>
                <li><strong>GBP (£) - British Pound</strong>: The oldest currency still in use.</li>
            </ul>
        </div>

        <!-- Currency Exchange Rates Section -->
        <div class="currency-section">
            <h2>Live Exchange Rates (Base: USD)</h2>
            <table class="currency-table">
                <tr>
                    <th>Currency</th>
                    <th>Exchange Rate</th>
                </tr>
                <?php if (!empty($rates)): ?>
                    <tr>
                        <td>EUR (€)</td>
                        <td><?= number_format($rates['EUR'], 4) ?></td>
                    </tr>
                    <tr>
                        <td>GBP (£)</td>
                        <td><?= number_format($rates['GBP'], 4) ?></td>
                    </tr>
                    <tr>
                        <td>JPY (¥)</td>
                        <td><?= number_format($rates['JPY'], 2) ?></td>
                    </tr>
                    <tr>
                        <td>AUD (A$)</td>
                        <td><?= number_format($rates['AUD'], 4) ?></td>
                    </tr>
                <?php else: ?>
                    <tr>
                        <td colspan="2">Failed to fetch exchange rates.</td>
                    </tr>
                <?php endif; ?>
            </table>
        </div>
    </div>

    <!-- Script for toggling sidebar -->
    <script>
        function toggleSidebar() {
            document.querySelector(".sidebar").classList.toggle("active");
        }
    </script>
</body>
</html>
