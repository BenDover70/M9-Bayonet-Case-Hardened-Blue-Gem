<?php

/**
 * Diese Datei wird aufgerufen, wenn ein Benutzer seine E-Mail-Adresse bestätigen möchte.
 * Der Benutzer erhält einen Link per E-Mail, der auf diese Seite verweist und einen Verifizierungscode enthält.
 * Dieser Code wird hier überprüft und der Benutzer wird als verifiziert markiert. 
 */
require_once 'functions.php';
$conn = connect();

$message = ""; // Falls $message noch nicht existiert, initialisieren

if (isset($_GET['code'])) {
    $code = $_GET['code'];

    // Prüfen, ob der Code existiert
    $query = "SELECT id FROM users WHERE verification_code = ? AND is_verified = 0";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $code);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        // Benutzer als verifiziert markieren
        $update_query = "UPDATE users SET is_verified = 1 WHERE id = ?";
        $update_stmt = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($update_stmt, "i", $row['id']);
        mysqli_stmt_execute($update_stmt);

        $message = "Dein Account wurde erfolgreich verifiziert! Du kannst dich jetzt <a href='index.php'>einloggen.</a>";
    } else {
        $message = "Ungültiger oder bereits verwendeter Verifizierungscode!";
    }
} else {
    $message = "Kein Verifizierungscode angegeben!";
}

mysqli_close($conn);
require_once 'footer.php';
?>

<h2>Email-Bestätigung</h2>
<p><?php echo $message; ?></p>
