<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'msg' => 'Nicht eingeloggt']);
        exit;
    }

    $user_id = $_SESSION['user_id'];

    // Einsatz starten: Abziehen des Einsatzes beim Spielstart
    if (isset($_POST['start_bet'])) {
        $bet = (int)$_POST['start_bet'];
        if ($bet < 10) {
            echo json_encode(['success' => false, 'msg' => 'Mindesteinsatz 10 Chips']);
            exit;
        }

        // Chips des Users abfragen
        $stmt = $conn->prepare("SELECT chips FROM user_chips WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($currentChips);
        $stmt->fetch();
        $stmt->close();

        if ($currentChips < $bet) {
            echo json_encode(['success' => false, 'msg' => 'Nicht genug Chips']);
            exit;
        }

        // Chips abziehen
        $stmt = $conn->prepare("UPDATE user_chips SET chips = chips - ? WHERE user_id = ?");
        $stmt->bind_param("ii", $bet, $user_id);
        $stmt->execute();
        $stmt->close();

        // Neue Chips abrufen
        $stmt = $conn->prepare("SELECT chips FROM user_chips WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($newChips);
        $stmt->fetch();
        $stmt->close();

        echo json_encode(['success' => true, 'chips' => $newChips]);
        exit;
    }

    // Auszahlung mit Multiplikator (Gewinn berechnen und draufaddieren)
    if (isset($_POST['cashout']) && isset($_POST['bet'])) {
        $multiplier = (float)$_POST['cashout'];
        $bet = (int)$_POST['bet'];

        if ($multiplier <= 0 || $bet < 10) {
            echo json_encode(['success' => false, 'msg' => 'Ungültiger Wert']);
            exit;
        }

        // Gewinn berechnen: Gewinn = Einsatz * Multiplikator
        // Gewinnchips sind: (Einsatz * Multiplikator) - Einsatz (weil Einsatz schon abgezogen wurde)
        $profit = floor($bet * $multiplier);
        $netGain = $profit; // da Einsatz schon abgezogen wurde, einfach den gesamten Profit addieren

        $stmt = $conn->prepare("UPDATE user_chips SET chips = chips + ? WHERE user_id = ?");
        $stmt->bind_param("ii", $netGain, $user_id);
        $stmt->execute();
        $stmt->close();

        // Neue Chips abrufen
        $stmt = $conn->prepare("SELECT chips FROM user_chips WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($newChips);
        $stmt->fetch();
        $stmt->close();

        echo json_encode(['success' => true, 'new_chips' => $newChips]);
        exit;
    }

    // Spielfeld-Klick - Prüfung ob Mine getroffen oder safe
    if (isset($_POST['index']) && isset($_POST['mine'])) {
        $index = (int)$_POST['index'];
        $mineIndex = (int)$_POST['mine'];

        if ($index === $mineIndex) {
            echo json_encode(['status' => 'mine']);
        } else {
            echo json_encode(['status' => 'safe']);
        }
        exit;
    }
}

// ---------- JS OUTPUT ----------
header('Content-Type: application/javascript');
?>

document.addEventListener("DOMContentLoaded", () => {
  const boxes = document.querySelectorAll('.box');
  const chipCountEl = document.getElementById('chips-mines');
  const messageEl = document.getElementById('game-message');
  const startBtn = document.getElementById('start-game');
  const cashoutBtn = document.getElementById('cash-out');
  const betInput = document.getElementById('bet-amount');

  let gameOver = false;
  let diamondsFound = 0;
  let multiplier = 0.1; // minimaler start-multiplier
  const maxMultiplier = 2.5;
  let mineIndex = Math.floor(Math.random() * 25);
  let currentBet = 0;

  function getBetAmount() {
    const value = parseInt(betInput.value);
    return isNaN(value) || value < 10 ? 10 : value;
  }

  function resetGame() {
    boxes.forEach(box => {
      box.classList.remove('safe', 'mine');
      box.textContent = '';
    });
    messageEl.textContent = '';
    diamondsFound = 0;
    multiplier = 0.1;
    gameOver = false;
    mineIndex = Math.floor(Math.random() * 25);
    currentBet = 0;
  }

  // Spiel starten: Einsatz abziehen vom Server
  startBtn.addEventListener('click', () => {
    if (gameOver === false && diamondsFound > 0) {
      messageEl.textContent = 'Bitte zuerst auszahlen oder Spiel beenden!';
      return;
    }

    const bet = getBetAmount();

    fetch('mineslogic.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/x-www-form-urlencoded'},
      body: 'start_bet=' + bet
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        chipCountEl.textContent = '💰 Chips: ' + data.chips;
        resetGame();
        currentBet = bet;
        messageEl.textContent = `Spiel gestartet mit Einsatz ${bet} Chips. Viel Glück!`;
      } else {
        messageEl.textContent = 'Fehler: ' + data.msg;
      }
    });
  });

  boxes.forEach((box, i) => {
    box.addEventListener('click', () => {
      if (gameOver) return;
      if (box.classList.contains('safe') || box.classList.contains('mine')) return;
      if (currentBet === 0) {
        messageEl.textContent = 'Bitte starte das Spiel mit Einsatz!';
        return;
      }

      fetch('mineslogic.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'index=' + i + '&mine=' + mineIndex
      })
      .then(res => res.json())
      .then(data => {
        if (data.status === 'mine') {
          box.classList.add('mine');
          box.textContent = '💣';
          messageEl.textContent = '💥 Boom! Du hast eine Mine getroffen!';
          gameOver = true;
          // kein Gewinn, Einsatz ist schon abgezogen
        } else if (data.status === 'safe') {
          box.classList.add('safe');
          box.textContent = '💎';
          diamondsFound++;
          multiplier = Math.min(maxMultiplier, 0.1 + diamondsFound * 0.1);
          messageEl.textContent = `Diamanten: ${diamondsFound} | Aktueller Multiplikator: x${multiplier.toFixed(1)}`;

          if (diamondsFound === 24) {
            revealAllAndJackpot();
          }
        }
      });
    });
  });

  cashoutBtn.addEventListener('click', () => {
    if (diamondsFound > 0 && !gameOver && currentBet > 0) {
      messageEl.textContent = `💰 Ausgezahlt mit x${multiplier.toFixed(1)}!`;
      gameOver = true;

      fetch('mineslogic.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `cashout=${multiplier}&bet=${currentBet}`
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          chipCountEl.textContent = '💰 Chips: ' + data.new_chips;
          currentBet = 0;
        } else {
          messageEl.textContent = 'Fehler bei Auszahlung!';
        }
      });
    } else {
      messageEl.textContent = 'Kein Gewinn zum Auszahlen!';
    }
  });

  function revealAllAndJackpot() {
    boxes.forEach((box, i) => {
      if (!box.classList.contains('safe') && !box.classList.contains('mine')) {
        if (i === mineIndex) {
          box.classList.add('mine');
          box.textContent = '💣';
        }
      }
    });
    messageEl.textContent = `🎉 Jackpot! Du bekommst x${maxMultiplier.toFixed(1)}!`;
    gameOver = true;

    fetch('mineslogic.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/x-www-form-urlencoded'},
      body: `cashout=${maxMultiplier}&bet=${currentBet}`
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        chipCountEl.textContent = '💰 Chips: ' + data.new_chips;
        currentBet = 0;
      } else {
        messageEl.textContent = 'Fehler bei Jackpot Auszahlung!';
      }
    });
  }
});