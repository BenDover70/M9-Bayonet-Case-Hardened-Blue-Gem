<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['error' => 'Nicht eingeloggt']);
        exit;
    }

    $data = json_decode(file_get_contents('php://input'), true);
    if (!isset($data['delta'])) {
        echo json_encode(['error' => 'Ungültige Daten']);
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $delta = (int)$data['delta'];

    $stmt = $conn->prepare("SELECT chips FROM user_chips WHERE user_id = ?");
    if (!$stmt) {
        echo json_encode(['error' => 'Prepare SELECT fehlgeschlagen: ' . $conn->error]);
        exit;
    }
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows === 0) {
        echo json_encode(['error' => 'User nicht gefunden']);
        exit;
    }
    $stmt->close();

    $stmt = $conn->prepare("UPDATE user_chips SET chips = chips + ? WHERE user_id = ?");
    if (!$stmt) {
        echo json_encode(['error' => 'Prepare UPDATE fehlgeschlagen: ' . $conn->error]);
        exit;
    }
    $stmt->bind_param("ii", $delta, $user_id);
    if (!$stmt->execute()) {
        echo json_encode(['error' => 'Execute UPDATE fehlgeschlagen: ' . $stmt->error]);
        exit;
    }
    $stmt->close();

    $stmt = $conn->prepare("SELECT chips FROM user_chips WHERE user_id = ?");
    if (!$stmt) {
        echo json_encode(['error' => 'Prepare SELECT Chips fehlgeschlagen: ' . $conn->error]);
        exit;
    }
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($chips);
    $stmt->fetch();
    $stmt->close();

    echo json_encode(['success' => true, 'chips' => $chips]);
    exit;
}

header("Content-Type: application/javascript");

$user_id = $_SESSION['user_id'] ?? 0;
$stmt = $conn->prepare("SELECT chips FROM user_chips WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($chips);
$stmt->fetch();
$stmt->close();
?>

document.addEventListener("DOMContentLoaded", () => {
  const canvas = document.getElementById("plinkoCanvas");
  const ctx = canvas.getContext("2d");
  const canvasWidth = canvas.width;
  const canvasHeight = canvas.height;

  const ROWS = 10;
  const BALL_RADIUS = 7;
  const PEG_RADIUS = 4;
  const SPACING_X = 36;
  const SPACING_Y = 36;
  const GRAVITY = 0.03;

  const multipliers = [16, 9, 2, 1.4, 1.4, 1.2, 1.1, 1, 0.5, 1, 1.1, 1.2, 1.4, 1.4, 2, 9, 16];
  const binsCount = multipliers.length;

  const binSpacing = 4; // Abstand zwischen Kästen
  const binBorderRadius = 10; // Abrundung der Kästen

  let pegs = [];
  let bins = [];
  let ball = null;
  let animationId = null;

  const chipsDisplay = document.getElementById("chips");
  const betForm = document.getElementById("betForm");
  const betInput = document.getElementById("betInput");
  const messageDiv = document.getElementById("message");

  let currentChips = window.START_CHIPS || 0;

  function getColorByMultiplier(multiplier) {
    const map = {
      "0.5": "bin-yellow",
      "1": "bin-light-orange",
      "1.1": "bin-orange",
      "1.2": "bin-orange",
      "1.4": "bin-dark-orange",
      "2": "bin-red",
      "9": "bin-deep-red",
      "16": "bin-darkest-red"
    };
    return map[multiplier.toString()] || "bin-default";
  }

  function initBoard() {
    pegs = [];
    bins = [];

    for (let row = 0; row < ROWS; row++) {
      let pegsInRow = row + 1;
      let offsetX = (canvasWidth - (pegsInRow - 1) * SPACING_X) / 2;
      for (let col = 0; col < pegsInRow; col++) {
        let x = offsetX + col * SPACING_X;
        let y = 60 + row * SPACING_Y;
        pegs.push({ x, y });
      }
    }

    const binWidth = (canvasWidth - (binsCount - 1) * binSpacing) / binsCount;
    for (let i = 0; i < binsCount; i++) {
      bins.push({
        x: i * (binWidth + binSpacing),
        width: binWidth,
        multiplier: multipliers[i]
      });
    }
  }

  function draw() {
    ctx.clearRect(0, 0, canvasWidth, canvasHeight);

    ctx.fillStyle = "#888";
    for (const peg of pegs) {
      ctx.beginPath();
      ctx.arc(peg.x, peg.y, PEG_RADIUS, 0, Math.PI * 2);
      ctx.fill();
    }

    for (const bin of bins) {
      ctx.save();
      ctx.beginPath();
      // Abgerundetes Rechteck
      roundRect(ctx, bin.x, canvasHeight - 40, bin.width, 40, binBorderRadius);
      ctx.clip();

      const binClass = getColorByMultiplier(bin.multiplier);
      ctx.fillStyle = getComputedStyle(document.documentElement).getPropertyValue(`--${binClass}`);
      ctx.fillRect(bin.x, canvasHeight - 40, bin.width, 40);
      ctx.restore();

      ctx.fillStyle = "#fff"; // Weiße Schrift
      ctx.textAlign = "center";
      ctx.textBaseline = "middle";
      ctx.font = "bold 18px sans-serif";
      ctx.fillText(`x${bin.multiplier}`, bin.x + bin.width / 2, canvasHeight - 20);
    }

    if (ball) {
      ctx.fillStyle = "#ffffff";
      ctx.beginPath();
      ctx.arc(ball.x, ball.y, BALL_RADIUS, 0, Math.PI * 2);
      ctx.fill();
    }
  }

  // Hilfsfunktion: abgerundetes Rechteck zeichnen
  function roundRect(ctx, x, y, width, height, radius) {
    ctx.beginPath();
    ctx.moveTo(x + radius, y);
    ctx.lineTo(x + width - radius, y);
    ctx.quadraticCurveTo(x + width, y, x + width, y + radius);
    ctx.lineTo(x + width, y + height - radius);
    ctx.quadraticCurveTo(x + width, y + height, x + width - radius, y + height);
    ctx.lineTo(x + radius, y + height);
    ctx.quadraticCurveTo(x, y + height, x, y + height - radius);
    ctx.lineTo(x, y + radius);
    ctx.quadraticCurveTo(x, y, x + radius, y);
    ctx.closePath();
  }

  function updateChipsDisplay(value) {
    currentChips = value;
    chipsDisplay.textContent = currentChips;
  }

  async function updateChipsServer(delta) {
    try {
      const res = await fetch("plinkologic.php", {
        method: "POST",
        headers: {"Content-Type": "application/json"},
        body: JSON.stringify({ delta })
      });
      const json = await res.json();
      if (json.success) {
        updateChipsDisplay(json.chips);
      } else {
        messageDiv.className = "error";
        messageDiv.textContent = "Fehler: " + (json.error || "Unbekannter Fehler");
      }
    } catch (e) {
      messageDiv.className = "error";
      messageDiv.textContent = "Verbindungsfehler";
    }
  }

  function startBall(bet) {
    if (ball) return;

    if (isNaN(bet) || bet < 1) {
      messageDiv.className = "error";
      messageDiv.textContent = "Bitte gültigen Einsatz angeben!";
      return;
    }

    if (bet > currentChips) {
      messageDiv.className = "error";
      messageDiv.textContent = "Nicht genug Chips!";
      return;
    }

    messageDiv.className = "info";
    messageDiv.textContent = "Ball rollt...";

    currentChips -= bet;
    updateChipsDisplay(currentChips);
    updateChipsServer(-bet);

    ball = {
      x: canvasWidth / 2,
      y: 30,
      vx: 0,
      vy: 0,
      bet: bet,
      row: 0,
      col: 0,
      falling: true
    };

    animationId = requestAnimationFrame(animate);
  }

  function animate() {
    if (!ball) return;

    ball.vy += GRAVITY;
    ball.y += ball.vy;
    ball.x += ball.vx;

    let nextRow = ball.row + 1;
    if (nextRow < ROWS && ball.y >= (60 + nextRow * SPACING_Y - BALL_RADIUS)) {
      let closest = pegs
        .slice((nextRow * (nextRow + 1)) / 2, ((nextRow + 1) * (nextRow + 2)) / 2)
        .reduce((prev, curr) => Math.abs(curr.x - ball.x) < Math.abs(prev.x - ball.x) ? curr : prev);

      let dir = Math.random() < 0.5 ? -1 : 1;
      ball.x = closest.x + dir * SPACING_X / 2;
      ball.y = closest.y - BALL_RADIUS;
      ball.vx = 0;
      ball.vy = 0;
      ball.row = nextRow;

      if (nextRow === ROWS - 1) {
        ball.falling = false;
      }
    }

    if (!ball.falling) {
      ball.vy = Math.min(ball.vy + GRAVITY, 2);
      ball.y += ball.vy;

      if (ball.y >= canvasHeight - 40 - BALL_RADIUS) {
        let binWidth = (canvasWidth - (binsCount - 1) * binSpacing) / binsCount;
        let index = Math.floor(ball.x / (binWidth + binSpacing));
        index = Math.max(0, Math.min(binsCount - 1, index));

        let multiplier = bins[index].multiplier;
        let win = Math.floor(ball.bet * multiplier);
        updateChipsServer(win);
        updateChipsDisplay(currentChips += win);

        messageDiv.className = "success";
        messageDiv.textContent = `Gewonnen: ${win} Chips!`;

        ball = null;
        cancelAnimationFrame(animationId);
        return;
      }
    }

    draw();
    animationId = requestAnimationFrame(animate);
  }

  betForm.addEventListener("submit", (e) => {
    e.preventDefault();
    const bet = parseInt(betInput.value, 10);
    startBall(bet);
  });

  initBoard();
  draw();
  updateChipsDisplay(currentChips);
});