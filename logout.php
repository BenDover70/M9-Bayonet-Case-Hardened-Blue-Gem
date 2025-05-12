<?php

/**
 * Diese Datei wird aufgerufen, wenn ein Benutzer sich ausloggt.
 * Hier wird die Session beendet und der Benutzer wird auf die Login-Seite weitergeleitet.
 */

session_start();
session_destroy();
header("Location: index.php?page=login"); // Weiterleitung nach Logout
exit;
?>