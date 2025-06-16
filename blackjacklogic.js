let deck = [];
let playerHand = [];
let dealerHand = [];
let gameActive = false;
let wins = 0;
let losses = 0;
let ties = 0;

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
    
    if (revealDealer) {
        document.getElementById('dealer-hand').innerHTML = dealerHand.map(card => `<div class="card">${getCardImage(card)}</div>`).join('');
        document.getElementById('dealer-score').innerText = calculateScore(dealerHand);
    } else {
        document.getElementById('dealer-hand').innerHTML = `<div class="card">${getCardImage(dealerHand[0])}</div><div class="card hidden-card"><img src="cards/back.png" alt="hidden"></div>`;
        document.getElementById('dealer-score').innerText = "?";
    }
    document.getElementById('player-score').innerText = calculateScore(playerHand);
}

function startGame() {
    createDeck();
    playerHand = [deck.pop(), deck.pop()];
    dealerHand = [deck.pop(), deck.pop()];
    gameActive = true;
    updateUI();
    document.getElementById('message').innerText = "Game Started! Hit or Stand?";
}

function hit() {
    if (!gameActive) return;
    playerHand.push(deck.pop());
    updateUI();
    if (calculateScore(playerHand) > 21) {
        document.getElementById('message').innerText = "Bust! You Lose.";
        updateStats('loss');
        gameActive = false;
        updateUI(true);
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
        document.getElementById('message').innerText = "You Win!";
        updateStats('win');
    } else if (playerScore < dealerScore) {
        document.getElementById('message').innerText = "Dealer Wins.";
        updateStats('loss');
    } else {
        document.getElementById('message').innerText = "It's a Tie!";
        updateStats('tie');
    }
    gameActive = false;
}

function updateStats(result) {
    if (result === 'win') {
        wins++;
    } else if (result === 'loss') {
        losses++;
    } else if (result === 'tie') {
        ties++;
    }
    
    // Update the displayed stats
    document.getElementById('wins').innerText = wins;
    document.getElementById('losses').innerText = losses;
    document.getElementById('ties').innerText = ties;
}