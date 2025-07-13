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

<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      background-size: cover;
      background-position: center;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .form-wrapper {
      background-color: rgba(36, 36, 36, 0.9);
      border-radius: 10px;
      padding: 30px;
      box-shadow: 0 0 20px rgba(0,0,0,0.3);
      max-width: 1000px;
      width: 90%;
    }

    .form-box {
      height: 100%;
    }

    h2 {
      font-weight: bold;
      margin-bottom: 20px;
    }

    input {
      margin-bottom: 15px;
    }

    .btn {
      width: 100%;
    }
  </style>
</head>
<body>

</br>

<div class="position-fixed top-0 start-0 w-100 h-100" style="
            background-image: url('pictures/login-bg.jpg');
            background-size: cover;
            background-position: center;
            opacity: 0.85;
            z-index: -1;">
</div>

<div class="form-wrapper">
  <div class="row g-4">
    <!-- Login -->
    <div class="col-md-6 d-flex align-items-stretch">
      <div class="form-box p-4 bg-body-secondary w-100 rounded shadow-sm">
        <form method="post">
          <h2 class="mb-3">Einloggen</h2>

          <div class="mb-3">
            <label for="username" class="form-label">Benutzername:</label>
            <input type="text" id="username" name="username" class="form-control" required>
          </div>

          <div class="mb-3">
            <label for="password" class="form-label">Passwort:</label>
            <input type="password" id="password" name="password" class="form-control" required>
          </div>

          <button type="submit" name="login" class="btn btn-success">Einloggen</button>
        </form>
      </div>
    </div>

    <!-- Registrierung -->
    <div class="col-md-6 d-flex align-items-stretch">
      <div class="form-box p-4 bg-body-secondary w-100 rounded shadow-sm">
        <form method="post">
          <h2 class="mb-3">Registrieren</h2>

          <div class="mb-3">
            <label for="new_username" class="form-label">Benutzername:</label>
            <input type="text" id="new_username" name="new_username" class="form-control" required>
          </div>

          <div class="mb-3">
            <label for="email" class="form-label">E-Mail:</label>
            <input type="email" id="email" name="email" class="form-control" required>
          </div>

          <div class="mb-3">
            <label for="new_password" class="form-label">Passwort:</label>
            <input type="password" id="new_password" name="new_password" class="form-control" required>
          </div>

          <div class="mb-3">
            <label for="confirm_password" class="form-label">Passwort bestätigen:</label>
            <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
          </div>

          <button type="submit" name="register" class="btn btn-primary">Registrieren</button>
        </form>
      </div>
    </div>
  </div>
</div>
</body>
</html>

<?php 
require_once 'footer.php'; 
?>