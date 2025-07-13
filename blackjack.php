<?php
require_once 'functions.php';
require_once 'head.php';
require_once 'navbar.php';

// Chips-Update verarbeiten
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_chips']) && isset($_POST['new_amount'])) {
    $user_id = $_SESSION['user_id'];
    $new_amount = (int)$_POST['new_amount'];
    update_user_chips($user_id, $new_amount);
    exit;
}

// Stats-Update verarbeiten  
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_stats']) && isset($_POST['result'])) {  
    $user_id = $_SESSION['user_id'];  
    $result = $_POST['result']; // 'win', 'loss', 'tie'  
    update_blackjack_stats($user_id, $result);  
    header("Location: index.php?page=blackjack");  
    exit;  
}

$user_id = $_SESSION['user_id'];  
$stats = get_blackjack_stats($user_id);
$user_chips = get_user_chips($user_id);
?>

<body class="blackjack-page" data-bs-theme="dark">
    <!-- Chip Display -->
    <div id="chip-display">
        💰 Chips: <span id="user-chips"><?php echo $user_chips; ?></span>
    </div>

    <div class="game-container">  
        <h1>Blackjack</h1>  
        <p id="message">Setze deinen Einsatz und klicke 'Start Game'!</p>  
        
        <div class="hand-container">  
            <div>  
                <h2>Player</h2>  
                <div id="player-hand" class="hand"></div>  
                <p>Score: <span id="player-score">0</span></p>  
            </div>  
            <div>  
                <h2>Dealer</h2>  
                <div id="dealer-hand" class="hand"></div>  
                <p>Score: <span id="dealer-score">?</span></p>  
            </div>  
        </div>  
        
        <!-- Bet Container mit integrierten Buttons -->
        <div class="bet-container">  
    <h3>Setze deinen Einsatz:</h3>  
    <div class="bet-controls">  
        <!-- Clear/Repeat links -->  
        <div class="bet-options">  
            <button type="button" class="bet-option-btn" onclick="clearBet()">Clear</button>  
            <button type="button" class="bet-option-btn" onclick="repeatBet()">Repeat</button>  
        </div>  
        <!-- Chips mittig -->  
        <div class="chips-section">  
            <div id="chip-buttons">  
                <div class="chip-button-wrapper">  
                    <button type="button" class="chip-btn" onclick="setBet(5)">  
                        <img src="pictures/5-chips.png" alt="5€ Chip">  
                    </button>  
                    <span class="chip-value">5</span>  
                </div>  
                <div class="chip-button-wrapper">  
                    <button type="button" class="chip-btn" onclick="setBet(10)">  
                        <img src="pictures/10-chips.png" alt="10€ Chip">  
                    </button>  
                    <span class="chip-value">10</span>  
                </div>  
                <div class="chip-button-wrapper">  
                    <button type="button" class="chip-btn" onclick="setBet(20)">  
                        <img src="pictures/20-chips.png" alt="20€ Chip">  
                    </button>  
                    <span class="chip-value">20</span>  
                </div>  
                <div class="chip-button-wrapper">  
                    <button type="button" class="chip-btn" onclick="setBet(50)">  
                        <img src="pictures/50-chips.png" alt="50€ Chip">  
                    </button>  
                    <span class="chip-value">50</span>  
                </div>  
                <div class="chip-button-wrapper">  
                    <button type="button" class="chip-btn" onclick="setBet(100)">  
                        <img src="pictures/100-chips.png" alt="100€ Chip">  
                    </button>  
                    <span class="chip-value">100</span>  
                </div>  
                <div class="chip-button-wrapper">  
                    <button type="button" class="chip-btn" onclick="setBet(500)">  
                        <img src="pictures/500-chips.png" alt="500€ Chip">  
                    </button>  
                    <span class="chip-value">500</span>  
                </div>  
            </div>  
            <p class="bet-info">Aktueller Einsatz: <span id="current-bet">0</span></p>  
        </div>  
        <!-- Game Buttons rechts -->  
        <div class="game-buttons">  
            <button onclick="startGame()">Start Game</button>  
            <button onclick="hit()">Hit</button>  
            <button onclick="stand()">Stand</button>  
        </div>  
    </div>  
</div>
        
        <!-- Verstecktes Formular für Stats-Update -->  
        <form id="stats-form" method="POST" style="display: none;">  
            <input type="hidden" name="update_stats" value="1">  
            <input type="hidden" name="result" id="game-result" value="">  
        </form>  
    </div>

    <?php include 'blackjacklogic.php'; ?>
</body>

<?php require_once 'footer.php'?>