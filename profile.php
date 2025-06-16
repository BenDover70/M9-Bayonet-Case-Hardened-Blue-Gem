<?php

/**
 * Auf dieser Seite sollst du es dem Nutzer ermöglichen, sein Profil zu bearbeiten.
 * Das bedeutet: Der Nutzer soll seinen Benutzernamen, sein Passwort und seine E-Mail-Adresse ändern und seinen Account löschen können.
 * ACHTUNG: Wenn der Benutzer seine E-Mail-Adresse ändert, muss er sie auch verifizieren, bevor(!) sie in der Datenbank geändert wird.
 */
require_once 'functions.php';
require_once 'head.php';
require_once 'navbar.php';

if  (!isset($_SESSION['user_id'])) {
    header("Location: index.php?page=login&error=not_logged_in");
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

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {  
    // profildaten ändern 
    if (isset($_POST['update_profile'])) {  
        $result = update_user_data(  
            $user_id,   
            $_POST['username'],   
            $_POST['email'],   
            $_POST['dob']  
        );  
          
        if ($result === true) {  
            $message = "<p style='color: green;'>Profil aktualisiert!</p>";   
            $username = get_username($user_id);  
            $email = get_email($user_id);  
            $dob = get_dob($user_id);  
        } else {  
            $message = "<p style='color: red;'>$result</p>";  
        }  
    }  
      
    // passwort ändern  
    if (isset($_POST['update_password'])) {  
        if ($_POST['new_password'] !== $_POST['confirm_password']) {  
            $message = "<p style='color: red;'>Passwörter stimmen nicht überein!</p>";  
        } else {  
            $result = update_password($user_id, $_POST['current_password'], $_POST['new_password']);  
            if ($result === true) {  
                $message = "<p style='color: green;'>Passwort aktualisiert!</p>";  
            } else {  
                $message = "<p style='color: red;'>$result</p>";  
            }  
        }  
    }  
}
?>



<h2>Mein Profil</h2>  
<?php echo $message; ?>  
  
<!--  daten anzeigen -->  
<div>  
    <h3>Meine Daten</h3>  
    <p>Benutzername: <?php echo htmlspecialchars($username); ?></p>  
    <p>E-Mail: <?php echo htmlspecialchars($email); ?></p>  
    <p>Geburtsdatum: <?php echo htmlspecialchars($dob ?: 'Nicht angegeben'); ?></p>  
</div>  
  
<!-- profil bearbeiten -->  
<div>  
    <h3>Profil bearbeiten</h3>  
    <form method="post">  
        <p>  
            <label for="username">Benutzername:</label>  
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required>  
        </p>  
        <p>  
            <label for="email">E-Mail:</label>  
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>  
        </p>  
        <p>  
            <label for="dob">Geburtsdatum:</label>  
            <input type="date" id="dob" name="dob" value="<?php echo htmlspecialchars($dob); ?>">  
        </p>  
        <button type="submit" name="update_profile">Speichern</button>  
    </form>  
      
<!-- neues passwort -->  
    <h3>Passwort ändern</h3>  
    <form method="post">  
        <p>  
            <label for="current_password">Aktuelles Passwort:</label>  
            <input type="password" id="current_password" name="current_password" required>  
        </p>  
        <p>  
            <label for="new_password">Neues Passwort:</label>  
            <input type="password" id="new_password" name="new_password" required>  
        </p>  
        <p>  
            <label for="confirm_password">Passwort bestätigen:</label>  
            <input type="password" id="confirm_password" name="confirm_password" required>  
        </p>  
        <button type="submit" name="update_password">Passwort ändern</button>  
    </form>  
</div>

<?php require_once 'footer.php'; ?>