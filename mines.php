<?php
session_start();
require_once 'head.php';
require_once 'config.php';
require_once 'functions.php';

if (!isset($_SESSION['user_id'])) {
    die('Bitte zuerst einloggen!');
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT chips FROM user_chips WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($chips);
$stmt->fetch();
$stmt->close();
?>

<body data-bs-theme="dark" class="mines-page">

<?php require_once 'navbar.php'; ?>

<br>

<div class="d-flex justify-content-center align-items-center">
    <h1>Wilkommen im Minenfeld!</h1>
</div>

<br>

<main class="mines-wrapper">

  <!-- 💰 Chips-Anzeige (oben rechts fixiert) -->
  <div id="chips-general">💰 Chips: <?php echo $chips; ?></div>

  <!-- Status-Meldung -->
  <div id="game-message" class="mt-3 text-center text-warning"></div>

  <!-- Mines Grid -->
  <div id="mines-container">
    <?php for ($i = 0; $i < 25; $i++): ?>
      <div class="box" data-index="<?= $i ?>"></div>
    <?php endfor; ?>
  </div>

  <!-- Einsatz & Buttons -->
  <div class="controls-container mt-4 d-flex justify-content-center align-items-center gap-3 flex-wrap">
    <!-- Einsatzfeld -->
    <div class="form-group">
      <input 
        type="number" 
        id="bet-amount" 
        class="form-control" 
        placeholder="Einsatz (min 10)" 
        min="10" step="10" 
        style="width: 150px;" 
        value="10"
      >
    </div>

    <!-- Buttons -->
    <div class="buttons d-flex gap-2">
      <button id="start-game" type="button" class="btn btn-success">Start Game</button>
      <button id="cash-out" type="button" class="btn btn-success">Auszahlen</button>
    </div>
  </div>

</main>

<!-- JS-Logik kommt aus PHP-Datei -->
<script src="mineslogic.php"></script>

<?php require_once 'footer.php'; ?>

</body>