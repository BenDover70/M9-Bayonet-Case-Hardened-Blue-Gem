<?php require_once 'functions.php'?>
<script>
let deck = [];
let playerHand = [];
let dealerHand = [];
let gameActive = false;
let wins = <?php echo $stats['wins']; ?>;  
let losses = <?php echo $stats['losses']; ?>;  
let ties = <?php echo $stats['ties']; ?>;
let currentBet = 0;  
let lastBet = 0;
let userChips = <?php echo get_user_chips($user_id); ?>;


function createDeck() {
    let suits = ['S', 'H', 'D', 'C'];
    let values = ['2', '3', '4', '5', '6', '7', '8', '9', '10', 'J', 'Q', 'K', 'A'];
    deck = [];
    for (let suit of suits) {
        for (let value of values) {
            deck.push({ suit, value });
        }
    }
    deck.sort(() => Math.random() - 0.5);
}

function getCardImage(card) {
    return `<img src="cards/${card.value}${card.suit}.png" alt="${card.value}${card.suit}">`;
}

function getCardValue(card) {
    if (['J', 'Q', 'K'].includes(card.value)) return 10;
    if (card.value === 'A') return 11;
    return parseInt(card.value);
}

function calculateScore(hand) {
    let score = hand.reduce((sum, card) => sum + getCardValue(card), 0);
    let aces = hand.filter(card => card.value === 'A').length;
    while (score > 21 && aces > 0) {
        score -= 10;
        aces -= 1;
    }
    return score;
}

function updateUI(revealDealer = false) {
    document.getElementById('player-hand').innerHTML = playerHand.map(card => `<div class="card">${getCardImage(card)}</div>`).join('');
    if (dealerHand.length > 0) {
        if (revealDealer) {
            document.getElementById('dealer-hand').innerHTML = dealerHand.map(card => `<div class="card">${getCardImage(card)}</div>`).join('');
            document.getElementById('dealer-score').innerText = calculateScore(dealerHand);
        } else {
            document.getElementById('dealer-hand').innerHTML = `<div class="card">${getCardImage(dealerHand[0])}</div><div class="card hidden-card"><img src="cards/back.png" alt="hidden"></div>`;
            document.getElementById('dealer-score').innerText = "?";
        }
    } else {
        document.getElementById('dealer-hand').innerHTML = '';
        document.getElementById('dealer-score').innerText = '?';
    }
    document.getElementById('player-score').innerText = playerHand.length > 0 ? calculateScore(playerHand) : '0';
}

function startGame() {
    if (currentBet === 0) {  
        alert("Bitte setze zuerst deinen Einsatz!");  
        return;  
    }  
    if (currentBet > userChips) {  
        alert("Du hast nicht genug Chips!");  
        return;  
    }  

    lastBet = currentBet;

    // Chips im Backend abziehen  
    updateChipsOnServer(userChips - currentBet);  
    userChips -= currentBet;  
    document.getElementById('user-chips').innerText = userChips;

    createDeck();
    playerHand = [deck.pop(), deck.pop()];
    dealerHand = [deck.pop(), deck.pop()];
    gameActive = true;
    updateUI();
    document.getElementById('message').innerText = "Hit or Stand?";
}

function hit() {
    if (!gameActive) return;
    playerHand.push(deck.pop());
    updateUI();
    if (calculateScore(playerHand) > 21) {
        document.getElementById('message').innerText = "Bust! Verloren.";
        endGame('loss');
    }
}

function stand() {
    if (!gameActive) return;
    while (calculateScore(dealerHand) < 17) {
        dealerHand.push(deck.pop());
    }
    updateUI(true);
    let playerScore = calculateScore(playerHand);
    let dealerScore = calculateScore(dealerHand);
    if (dealerScore > 21 || playerScore > dealerScore) {
        document.getElementById('message').innerText = "Gewonnen!";
        endGame('win');
    } else if (playerScore < dealerScore) {
        document.getElementById('message').innerText = "Dealer gewinnt.";
        endGame('loss');
    } else {
        document.getElementById('message').innerText = "Tie!";
        endGame('tie');
    }
}

function endGame(result) {
    gameActive = false;
    updateStats(result);
    // Einsatz zurücksetzen
    currentBet = 0;
    document.getElementById('current-bet').innerText = currentBet;
    // Nachricht für neues Spiel anzeigen
    setTimeout(() => {
        document.getElementById('message').innerText = "Setze deinen Einsatz und klicke 'Start Game'!";
        // Hände leeren für neues Spiel
        playerHand = [];
        dealerHand = [];
        updateUI();
    }, 1500);
}

function updateStats(result) {  
    if (result === 'win') {  
        wins++;  
        userChips += currentBet * 2;  
        updateChipsOnServer(userChips);  
    } else if (result === 'loss') {  
        losses++;  
    } else if (result === 'tie') {  
        ties++;  
        userChips += currentBet;  
        updateChipsOnServer(userChips);  
    }  
    // Statistiken aktualisieren  
    document.getElementById('wins') && (document.getElementById('wins').innerText = wins);  
    document.getElementById('losses') && (document.getElementById('losses').innerText = losses);  
    document.getElementById('ties') && (document.getElementById('ties').innerText = ties);  
    document.getElementById('user-chips').innerText = userChips;  
    // Statistiken an Server senden  
    var xhr = new XMLHttpRequest();    
    xhr.open('POST', window.location.href, true);    
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');    
    xhr.send('update_stats=1&result=' + encodeURIComponent(result));  
}

function setBet(amount) {    
    if (gameActive) {  
        alert("Du kannst den Einsatz nicht während eines laufenden Spiels ändern!");  
        return;  
    }  
    if (currentBet + amount > userChips) {    
        alert("Du hast nicht genug Chips!");    
        return;    
    }    
    currentBet += amount;   
    document.getElementById('current-bet').innerText = currentBet;    
}

function updateChipsOnServer(newAmount) {  
    var xhr = new XMLHttpRequest();  
    xhr.open('POST', window.location.href, true);  
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');  
    xhr.send('update_chips=1&new_amount=' + encodeURIComponent(newAmount));  
}

function repeatBet() {  
    if (gameActive) {  
        alert("Du kannst den Einsatz nicht während eines laufenden Spiels ändern!");  
        return;  
    }  
    if (lastBet === 0) {  
        alert("Kein vorheriger Einsatz vorhanden!");  
        return;  
    }  
    if (lastBet > userChips) {  
        alert("Du hast nicht genug Chips für den vorherigen Einsatz!");  
        return;  
    }  
    currentBet = lastBet;  
    document.getElementById('current-bet').innerText = currentBet;  
}

function clearBet() {  
    if (gameActive) {  
        alert("Du kannst den Einsatz nicht während eines laufenden Spiels ändern!");  
        return;  
    }  
    currentBet = 0;  
    document.getElementById('current-bet').innerText = currentBet;  
}


</script>