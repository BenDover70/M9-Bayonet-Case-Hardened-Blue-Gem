<?php
require_once 'functions.php';
require_once 'head.php';
require_once 'navbar.php';
?>

<link rel="stylesheet" href="./blackjackstyle.css">
    <script src="blackjacklogic.js" defer></script>



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
                <p id="wins">0</p>
            </div>
            <div class="stat">
                <h3>Losses:</h3>
                <p id="losses">0</p>
            </div>
            <div class="stat">
                <h3>Ties:</h3>
                <p id="ties">0</p>
            </div>
        </div>
        
    </div>
</body>




<?php require_once 'footer.php'?>