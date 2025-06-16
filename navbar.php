<?php
// Debug-Ausgabe im HTML-Kommentar
$page = $_GET['page'] ?? 'home';
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="#">Casino Loyal</a>
        <span class="mx-3"></span>
        <a class="navbar-brand" href="#">Willkommen, <?= get_username($_SESSION['user_id']); ?>!</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link <?= ($page === 'home') ? 'active' : '' ?>" href="index.php?page=home">Startseite</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($page === 'profile') ? 'active' : '' ?>" href="index.php?page=profile">Profil</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($page === 'logout') ? 'active' : '' ?>" href="index.php?page=logout">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>