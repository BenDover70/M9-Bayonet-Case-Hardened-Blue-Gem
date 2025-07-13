<?php
/**
 * In dieser Datei werden alle Konfigurationen für die Webseite vorgenommen.
 * Dazu gehören die Datenbankverbindung und die Sessionkonfiguration.
 * Hier musst du deine eigenen Datenbankdaten eintragen.
 */

define('DB_HOST', 'localhost');
define('DB_USER', 'cu-freieevangelischeschule01_dob-g03');
define('DB_PASS', 'j&097y4Io');
define('DB_NAME', 'cu-freieevangelischeschule01_dob-g03');

// Sessionkonfiguration
ini_set('session.gc_maxlifetime', 3600); // Session läuft nach 1 Stunde ab
session_set_cookie_params(3600); // Cookie läuft ebenfalls nach 1 Stunde ab
session_start();

// DB Verbindung aufbauen
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Verbindung fehlgeschlagen: " . $conn->connect_error);
}
?>

