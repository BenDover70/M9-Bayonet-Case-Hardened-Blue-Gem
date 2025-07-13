<?php
require_once 'functions.php';
require_once 'head.php';
require_once 'navbar.php';

// Stats-Update verarbeiten  
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_stats']) && isset($_POST['result'])) {  
    $user_id = $_SESSION['user_id'];  
    $result = $_POST['result']; // 'win', 'loss', 'tie'  
    update_blackjack_stats($user_id, $result);  
    // Optional: Seite neu laden, damit die neuen Werte angezeigt werden  
    header("Location: index.php?page=blackjack");  
    exit;  
}

$user_id = $_SESSION['user_id'];  
$stats = get_blackjack_stats($user_id);
?>

<link rel="stylesheet" href="./blackjackstyle.css">
    



<body>  
    <div class="game-container">  
        <h1>Blackjack</h1>  
        <p id="message">Click 'Start Game' to play!</p>  
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
        <div class="buttons">  
            <button onclick="startGame()">Start Game</button>  
            <button onclick="hit()">Hit</button>  
            <button onclick="stand()">Stand</button>  
        </div>  
        <div class="stats">    
            <div class="stat">    
                <h3>Wins:</h3>    
                <p id="wins"><?php echo $stats['wins']; ?></p>    
            </div>    
            <div class="stat">    
                <h3>Losses:</h3>    
                <p id="losses"><?php echo $stats['losses']; ?></p>    
            </div>    
            <div class="stat">    
                <h3>Ties:</h3>    
                <p id="ties"><?php echo $stats['ties']; ?></p>    
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