<?php

/**
 * Die home.php ist deine Startseite, die ein Benutzer zu sehen bekommt, wenn er eingeloggt ist.
 * Hier kannst du beliebige Inhalte einfügen, die nur für eingeloggte Benutzer sichtbar sein sollen.
 */

require_once 'functions.php';
?>

<!-- Begrüße den Benutzer mit seinem Benutzernamen -->
<h1>Willkommen, <?php echo get_username($_SESSION['user_id']); ?>!</h1>
<p>Du bist erfolgreich eingeloggt.</p>

<nav>
    <ul>
        <li><a href="index.php?page=home">Startseite</a></li>
        <li><a href="index.php?page=profile">Profil</a></li>
        <li><a href="index.php?page=logout">Logout</a></li>
    </ul>
</nav>