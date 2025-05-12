<?php

/**
 * Die index.php dient dem Session-Maanagement. Alle Seitenanfragen werden hierhin geleitet, um sicherzustellen, dass ein eingeloggter
 * Benutzer eingeloggt bleibt und auf alle Seiten zugreifen kann, die nur angemeldeten Benutzern zur Verfügung stehen.
 * 
 * In dieser Datei wird nur die Session gestartet und der Router geladen, der die Seitenanfragen verwaltet.
 */
session_start(); // Session starten

// Sicherstellen, dass keine Session-Fixation möglich ist
if (isset($_SESSION['user_id'])) {
    session_regenerate_id(true);
}


require_once 'router.php'; // Der Router verwaltet die Seiten
?>
