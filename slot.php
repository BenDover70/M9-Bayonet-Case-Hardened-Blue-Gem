<?php
require_once 'head.php';
require_once 'functions.php';
require_once 'navbar.php';
require_once 'config.php';

session_start();

// Dummy-Login für Testzwecke
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1; // Test-Benutzer-ID
}
$user_id = $_SESSION['user_id'];

// Chips abrufen
$stmt = $conn->prepare("SELECT chips FROM user_chips WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($chips);
$stmt->fetch();
$stmt->close();

$message = "";

// Spin ausführen (nur Chips speichern)
if (isset($_POST['spin']) && isset($_POST['newchips'])) {
    $newchips = intval($_POST['newchips']);

    $stmt = $conn->prepare("UPDATE user_chips SET chips = ? WHERE user_id = ?");
    $stmt->bind_param("ii", $newchips, $user_id);
    $stmt->execute();
    $stmt->close();

    echo '<div id="chip-display">💰 Chips: ' . $newchips . '</div>';
    echo '<div id="winText" style="text-align:center; margin-top: 10px; color: #0f0;">Chips gespeichert!</div>';
    exit;
}
?>

<body data-bs-theme="dark" class="slots-page">
  <main class="slot-wrapper">
    <div class="slots">
      <div class="reel"></div>
      <div class="reel"></div>
      <div class="reel"></div>
    </div>
    <div id="chip-display">💰 Chips: <?php echo $chips; ?></div>
    <div class="slot-controls">
      <button id="spinButton">Spin</button>
      <label class="autospin-toggle">
        <input type="checkbox" id="autoSpin" />Auto-Spin</label>
    </div>
    <div id="winText" style="text-align:center; margin-top: 10px; color: #0f0;"><?php echo $message; ?></div>
  </main>

  <script src="./slotslogic.php"></script>

<?php require_once 'footer.php'; ?>