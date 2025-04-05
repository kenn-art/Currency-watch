<?
$query = "SELECT rate FROM currency_rates ORDER BY timestamp DESC LIMIT 5";
$stmt = $pdo->query($query);
$rates = $stmt->fetchAll(PDO::FETCH_COLUMN);

$prediction = array_sum($rates) / count($rates);
echo json_encode(["latest_rate" => $rate, "predicted_rate" => $prediction]);

?>