<?php
/**
 * Die Loginseite stellt das Login und das Registrierungsformular bereit und kümmert sich um die Logik dahinter.
 */

require_once 'functions.php';

// Wenn die Session noch nicht gestartet wurde, starte sie
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$message = "";


// Falls ein Fehler aufgetreten ist, wird hier der Fehler ausgewertet und eine entsprechende Meldung ausgegeben
if (isset($_GET['error'])) {
    if ($_GET['error'] == 'not_logged_in') {
        echo "<p style='color: red;'>Du musst eingeloggt sein, um diese Seite zu sehen.</p>";
    } else if ($_GET["error"] == "not_admin") {
        echo "<p style='color: red;'>Du musst ein Administrator sein, um diese Seite zu sehen.</p>";
    } 
}


// Loginlogik
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $message = login_user($username, $password);

    if ($message === true) {
        header("Location: index.php?page=home");
        exit;
    }
}

// Registrierungslogik
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $new_username = trim($_POST['new_username']);
    $email = trim($_POST['email']);
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        $message = "Die Passwörter stimmen nicht überein!";
    } else {
        if (register_user($new_username, $email, $new_password)) {
            $_SESSION['message'] = "Registrierung erfolgreich! Bitte überprüfe deine E-Mails und bestätige deine Registrierung.";
            header("Location: index.php?page=login"); // Weiterleitung, um doppeltes Absenden zu verhindern
            exit;
        } else {
            $message = "Registrierung fehlgeschlagen. Bitte versuche es erneut.";
        }
    }
}
?>

<?php require_once 'head.php'; ?>

<h2>Login / Registrierung</h2>

<?php 
// Fehler- oder Erfolgsmeldungen anzeigen
if (!empty($message)) {
    echo "<p style='color: red;'>$message</p>";
}

if (isset($_SESSION['message'])) {
    echo "<p class='success-message' style='color: green;'>" . htmlspecialchars($_SESSION['message']) . "</p>";
    unset($_SESSION['message']); // Nachricht nach der Anzeige löschen
}
?>

<!-- Loginformular -->
<form method="post">
    <h3>Einloggen</h3>
    <label>Benutzername:</label>
    <input type="text" name="username" required><br />

    <label>Passwort:</label>
    <input type="password" name="password" required><br />

    <button type="submit" name="login">Einloggen</button>
</form>

<hr />

<!-- Registrierungsformular -->
<form method="post">
    <h3>Registrieren</h3>
    <label>Benutzername:</label>
    <input type="text" name="new_username" required><br />

    <label>E-Mail:</label>
    <input type="email" name="email" required><br />

    <label>Passwort:</label>
    <input type="password" name="new_password" required><br />

    <label>Passwort bestätigen:</label>
    <input type="password" name="confirm_password" required><br /> 

    <button type="submit" name="register">Registrieren</button>
</form>

<?php require_once 'footer.php'; ?>