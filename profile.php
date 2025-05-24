<?php

/**
 * Auf dieser Seite sollst du es dem Nutzer ermöglichen, sein Profil zu bearbeiten.
 * Das bedeutet: Der Nutzer soll seinen Benutzernamen, sein Passwort und seine E-Mail-Adresse ändern und seinen Account löschen können.
 * ACHTUNG: Wenn der Benutzer seine E-Mail-Adresse ändert, muss er sie auch verifizieren, bevor(!) sie in der Datenbank geändert wird.
 */
require_once 'functions.php';

if  (!isset($_SESSION['user_id'])) {
    header("Location: index.php?page=login$error=not_logged_in");
    exit;
}
//$user_id festlegen
$user_id = $_SESSION['user_id'];

//username abfragen
$username = get_username($user_id);

//email abfragen
$email = get_email($user_id);

//dob abfragen
$dob = get_dob($user_id);
?>

<h2>Mein Profil</h2>  
  
<div class="profile-container">  
    <div class="profile-details">  
        <!-- username  anzeigen -->  
        <p><strong>Benutzername:</strong> <?php echo ($username); ?></p>  
          
        <!-- email anzeigen -->  
        <p><strong>E-Mail-Adresse:</strong> <?php echo ($email); ?></p>  
          
        <!-- dob anzeigen -->  
        <p><strong>Geburtsdatum:</strong> <?php   
            echo ($dob); ?></p>    
        </p>  
    </div>  
</div>