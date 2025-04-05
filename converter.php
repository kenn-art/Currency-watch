<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// API Key and URL
$apiKey = '681354cdbdc6c112e3309d61';
$apiUrl = 'https://api.exchangerate-api.com/v4/latest/USD';

// Fetch exchange rates
$response = file_get_contents($apiUrl);
if ($response === FALSE) {
    die('Error occurred while fetching data.');
}

$data = json_decode($response, true);
$exchangeRates = $data['rates'];

// Currency Conversion Function
function convertCurrency($amount, $fromCurrency, $toCurrency, $exchangeRates) {
    if (isset($exchangeRates[$fromCurrency]) && isset($exchangeRates[$toCurrency])) {
        $baseToFrom = $amount / $exchangeRates[$fromCurrency];
        $convertedAmount = $baseToFrom * $exchangeRates[$toCurrency];
        return $convertedAmount;
    }
    return false;
}

$convertedAmount = null;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $amount = $_POST['amount'];
    $fromCurrency = $_POST['from_currency'];
    $toCurrency = $_POST['to_currency'];

    if ($amount > 0 && $fromCurrency !== $toCurrency) {
        $convertedAmount = convertCurrency($amount, $fromCurrency, $toCurrency, $exchangeRates);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Currency Converter</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color:rgb(53, 53, 53);        }

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
            transition: margin-left 0.3s ease; /* Add ease transition */
        }

        .sidebar.active ~ .main-content {
            margin-left: 250px; /* Ensure main content shifts correctly */
        }

        .converter-container {
            background: #fff;
            padding: 40px;
            border-radius: 15px;
            width: 90%;
            max-width: 600px;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.1);
            margin: 80px auto 20px auto;
        }

        label, select, input, button {
            font-size: 18px;
            padding: 12px;
            margin: 10px 0;
            width: 100%;
            border-radius: 8px;
            border: 1px solid #ddd;
        }

        input, select {
            background-color: #f4f4f4;
            color: #333;
        }

        button {
            background-color: #3498db;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #2980b9;
        }

        .conversion-result {
            background-color: #4CAF50;
            padding: 20px;
            margin-top: 20px;
            border-radius: 8px;
            font-size: 18px;
            color: white;
        }

        .error-message {
            color: #e74c3c;
            font-size: 18px;
            text-align: center;
        }
        .logo {
            align-items: baseline;
            width: 80px;
            height: auto;
            margin-bottom: 20px;
        }
    </style>
    <script>
        function toggleSidebar() {
            document.querySelector(".sidebar").classList.toggle("active");
        }
    </script>
</head>
<body>
    <div class="sidebar">
        <h2>Currency Watch</h2>
        <a href="dashboard.php">Dashboard</a>
        <a href="converter.php">Converter</a>
        <a href="predictions.php">Predictions</a>
        <a href="search.php">Search</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="menu-btn" onclick="toggleSidebar()">&#9776;</div>

    <div class="main-content">
    <img src="CurrencyW-removebg-preview.png" alt="Logo" class="logo">
    <h1>Currency Converter</h1>
        <p>Real-time exchange rates powered by API.</p>
</div>
        <form action="" method="POST">
            <div class="converter-container">
                <label for="amount">Amount:</label>
                <input type="number" name="amount" id="amount" required step="any" placeholder="Enter amount to convert" value="<?= isset($amount) ? htmlspecialchars($amount) : '' ?>">

                <label for="from_currency">From Currency:</label>
                <select name="from_currency" id="from_currency" required>
                    <?php foreach ($exchangeRates as $currency => $rate): ?>
                        <option value="<?= $currency ?>" <?= isset($fromCurrency) && $fromCurrency == $currency ? 'selected' : '' ?>><?= $currency ?></option>
                    <?php endforeach; ?>
                </select>

                <label for="to_currency">To Currency:</label>
                <select name="to_currency" id="to_currency" required>
                    <?php foreach ($exchangeRates as $currency => $rate): ?>
                        <option value="<?= $currency ?>" <?= isset($toCurrency) && $toCurrency == $currency ? 'selected' : '' ?>><?= $currency ?></option>
                    <?php endforeach; ?>
                </select>

                <button type="submit">Convert</button>
            </div>
        </form>

        <?php if ($convertedAmount !== null): ?>
            <div class="conversion-result">
                <h3>Conversion Result</h3>
                <p><?= number_format($amount, 2) ?> <?= htmlspecialchars($fromCurrency) ?> = <?= number_format($convertedAmount, 2) ?> <?= htmlspecialchars($toCurrency) ?></p>
            </div>
        <?php elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && $amount <= 0): ?>
            <p class="error-message">Please enter a valid amount greater than zero.</p>
        <?php endif; ?>
    </div>
</body>
</html>
