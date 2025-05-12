<?php

/**
 * Die router.php verwaltet die Seitenanfragen und stellt sicher, dass nur autorisierte
 * Benutzer auf bestimmte Seiten zugreifen können.
 * 
 * Alle Seiten, auf die dein Benutzer zugreifen können soll, müssen hier im Array $allowedPages eingetragen werden.
 * Seiten, die nur für angemeldete Benutzer verfügbar sein sollen, werden im Array $protectedPages eingetragen.
 * 
 * Damit wird sichergestellt, dass ein Benutzer nur auf Seiten zugreifen kann, die für ihn zugelassen sind.
 * 
 * Alles Seiten, die nur für Administratoren verfügbar sein sollen, werden im Array $adminPages eingetragen.
 */

// Alle Seiten, die in deinem Projekt für alle zugänglich sein sollen, werden hier in das Array eingetragen.


$allowedPages = [
    'login',
    'register',
    'verify',
    'logout'
]; 

// Alle Seiten, die nur für angemeldete Benutzer verfügbar sein sollen, werden hier in das Array eingetragen.
$protectedPages = [
    'home',
    'profile'
]; 

// Alle Seiten, die für Administratoren deiner Seite verfügbar sein sollen, aber nicht für andere angemeldete Benutzer, werden hier in das Array eingeragen.
$adminPages = [
    'admin' // Diese Seite gibt es noch nicht. Du musst sie erst erstellen, wenn du sie verwenden möchtest.
];

$page = $_GET['page'] ?? 'login'; // Standardseite

// Ist die Seite geschützt und der Nutzer nicht eingeloggt?
if (in_array($page, $protectedPages) && !isset($_SESSION['user_id'])) {
    header("Location: index.php?page=login&error=not_logged_in");
    exit;
}

// Falls die Seite nicht existiert, 404 anzeigen
if (!in_array($page, array_merge($allowedPages, $protectedPages, $adminPages))) {
    header("HTTP/1.0 404 Not Found");
    $page = '404';   
    
}

// Hier wird die angeforderte Seite inklusive head und footer geladen
require_once "head.php"; // Kopfbereich der HTML-Seite, der sicherstellt, dass wir valides HTML auseben.
require_once "$page.php"; // Seite laden
require_once "footer.php"; // Fußbereich der HTML-Seite, der sicherstellt, dass wir valides HTML ausgeben.
?>
