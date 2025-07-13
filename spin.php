<?php
require_once 'config.php';

header('Content-Type: application/json');

// Session starten, falls noch nicht
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Benutzer-ID prüfen
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "Nicht eingeloggt"]);
    exit;
}

$user_id = $_SESSION['user_id'];

// Aktuelle Chips holen
$stmt = $conn->prepare("SELECT chips FROM user_chips WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($chips);
$stmt->fetch();
$stmt->close();

$spinCost = 100;
if ($chips < $spinCost) {
    echo json_encode(["error" => "Nicht genug Chips"]);
    exit;
}

// Spinpreis sofort abziehen
$chips -= $spinCost;

// Gewinn auslosen
$possible_wins = [0, 0, 100, 200, 500];
$win = $possible_wins[array_rand($possible_wins)];

// Gewinn addieren (auch wenn es 0 ist)
$chips += $win;

// Neue Chipsanzahl speichern
$stmt = $conn->prepare("UPDATE user_chips SET chips = ? WHERE user_id = ?");
$stmt->bind_param("ii", $chips, $user_id);
$stmt->execute();
$stmt->close();

// Rückmeldung an Frontend
echo json_encode([
    "chips" => $chips,
    "win" => $win,
    "message" => $win > 0 ? "Du hast $win Chips gewonnen!" : "Kein Gewinn."
]);
?>