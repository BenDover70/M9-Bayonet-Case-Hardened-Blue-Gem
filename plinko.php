<?php
session_start();
require_once 'head.php';
require_once 'config.php';  // DB-Verbindung
require_once 'functions.php';
require_once 'navbar.php';

// Sicherstellen, dass User eingeloggt ist
if (!isset($_SESSION['user_id'])) {
    die('Bitte zuerst einloggen!');
}

$user_id = $_SESSION['user_id'];

// Chips aus DB holen
$stmt = $conn->prepare("SELECT chips FROM user_chips WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($chips);
$stmt->fetch();
$stmt->close();
?>

<div class="container mt-5">
  <h1 class="text-center mb-4">Plinko</h1>

  <div class="row justify-content-center mb-3">
    <form id="betForm" class="col-auto d-flex gap-2 align-items-center" autocomplete="off" novalidate>
      <div id="chips-general">
        💰 Chips: <span id="chips-count"><?= htmlspecialchars($chips) ?></span>
      </div>
      <input type="number" name="bet" id="betInput" class="form-control" placeholder="Einsatz" min="1" style="width: 120px;" required>
      <button type="submit" class="btn btn-success">Setzen &amp; Ball fallen lassen</button>
    </form>
  </div>

  <!-- Statusmeldung ganz bewusst VOR dem Canvas, damit sichtbar -->
  <div id="message" class="text-center mb-3" style="font-weight: bold; min-height: 1.4em;"></div>

  <div class="d-flex justify-content-center">
    <canvas id="plinkoCanvas" width="420" height="600" style="border:1px solid #ccc;"></canvas>
  </div>
</div>

<script>
  // Startchips für JS verfügbar machen
  window.START_CHIPS = <?= (int)$chips ?>;
</script>

<script src="plinkologic.php"></script>

<?php
require_once 'footer.php';
?>