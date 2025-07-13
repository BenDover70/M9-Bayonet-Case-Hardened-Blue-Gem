<?php

/**
 * Die home.php ist deine Startseite, die ein Benutzer zu sehen bekommt, wenn er eingeloggt ist.
 * Hier kannst du beliebige Inhalte einfügen, die nur für eingeloggte Benutzer sichtbar sein sollen.
 */

require_once 'functions.php';
require_once 'navbar.php';
?>

<div class="container px-0 mt-5">
  <div class="row justify-content-center gx-4 gy-4">
        <!-- Black Jack -->
            <div class="col-md-4">
                <div class="p-0 border bg-light rounded-4 shadow d-flex flex-column overflow-hidden" style="height: 570px; background-image: url('pictures/blackjack-bnr.jpg'); background-size: cover; background-position: center;">
                    <!-- Dunkler Hintergrundbereich oben -->
                    <div class="text-white text-center d-flex flex-column justify-content-center align-items-center" style="background: rgba(0, 0, 0, 0.6); height: 160px; padding: 1.5rem;">
                        <h2 class="mb-2">Black Jack</h2>
                        <p class="mb-0">Hier Blackjack spielen!</p>
                    </div>

                    <!-- Platzhalter damit Button unten bleibt -->
                    <div class="flex-grow-1"></div>

                    <!-- Button -->
                    <div class="text-center mb-3">
                        <a href="index.php?page=blackjack">
                            <button class="btn btn-success">Zum Blackjack-Spiel</button>
                        </a>
                    </div>
                </div>
            </div>

        <!-- Slotmaschine -->
            <div class="col-md-4">
                <div class="p-0 border bg-light rounded-4 shadow d-flex flex-column overflow-hidden" style="height: 570px; background-image: url('pictures/slotmaschine-bnr.jpg'); background-size: cover; background-position: center;">
                    <!-- Dunkler Hintergrundbereich oben -->
                    <div class="text-white text-center d-flex flex-column justify-content-center align-items-center" style="background: rgba(0, 0, 0, 0.6); height: 160px; padding: 2rem;">
                        <h2 class="mb-2">Slotmaschine</h2>
                        <p class="mb-0">Hier Slotmaschine spielen!</p>
                    </div>

                    <!-- Platzhalter damit Button unten bleibt -->
                    <div class="flex-grow-1"></div>

                    <!-- Button -->
                    <div class="text-center mb-3">
                        <a href="index.php?page=slot">
                            <button class="btn btn-success">Zur Slotmaschine</button>
                        </a>
                    </div>
                </div>
            </div>     

        <!-- Plinko -->
             <div class="col-md-4">
                <div class="p-0 border bg-light rounded-4 shadow d-flex flex-column overflow-hidden" style="height: 570px; background-image: url('pictures/plinko-bnr.jpg'); background-size: cover; background-position: center;">
                    <!-- Dunkler Hintergrundbereich oben -->
                    <div class="text-white text-center d-flex flex-column justify-content-center align-items-center" style="background: rgba(0, 0, 0, 0.6); height: 160px; padding: 1.5rem;">
                        <h2 class="mb-2">Plinko</h2>
                        <p class="mb-0">Hier Plinko spielen!</p>
                    </div>

                    <!-- Platzhalter damit Button unten bleibt -->
                    <div class="flex-grow-1"></div>

                    <!-- Button -->
                    <div class="text-center mb-3">
                        <a href="index.php?page=plinko">
                            <button class="btn btn-success">Zum Plinko-Spiel</button>
                        </a>
                    </div>
                </div>
            </div>     
  </div>
</div>
