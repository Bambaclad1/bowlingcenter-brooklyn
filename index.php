<!DOCTYPE html>
<html lang="nl">
<?php
    session_start();

    if (isset($_POST["unset"])) {
        session_unset();
        session_destroy();
    }
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bowling Center Brooklyn</title>
    <link rel="stylesheet" href="style1.css">
</head>
<body>

<!-- Header -->
<header class="header">
    <button class="menu-btn" id="menu-btn">☰</button>
</header>

<!-- Mobile menu -->
<nav class="mobile-menu" id="mobile-menu">
    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="over_ons.php">Over ons</a></li>
        <li><a href="booking/beta.php">Agenda</a></li>
        <li><a href="prijzen.php">Prijzen</a></li>
    <?php if (!isset($_SESSION['user_id'])): ?>
        <li><a href="login.php">Login</a></li>
    <?php elseif (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'medewerker'): ?>
        <li><a href="dashboard/medewerker.php">Dashboard</a></li>
    <?php elseif (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'owner'): ?>
        <li><a href="owner.php">Dashboard</a></li>
    <?php else: ?>
        <li><a href="logout.php">Logout</a></li>
    <?php endif; ?>
    </ul>
</nav>


<!-- Hero sectie -->
<section class="hero">
    <div class="hero-content">
        <div class="logo-circle">
            <img src="afbeeldingen/bowling_logo.png" alt="Bowling Center Brooklyn">
        </div>
        <h1>Bowling Center Brooklyn</h1>
        <p class="hero-subtitle">Waar plezier altijd scoort</p>
        <a href="#" class="btn">Reserveer nu</a>
    </div>
</section>

<!-- Desktop cards -->
<section class="desktop-section">
    <div class="desktop-container">
        <div class="desktop-card_blue">
            <img src="afbeeldingen/bowlen.jpeg" alt="Bowlen">
            <div class="card-text">
                <h3>Bowlen</h3>
                <p>Dagelijks 14:00 - 22:00</p>
            </div>
        </div>

        <div class="desktop-card_pink">
            <img src="afbeeldingen/magicbowling.png" alt="Magic Bowlen">
            <div class="card-text">
                <h3>Magic Bowlen</h3>
                <p>Elke weekend van 22:00 tot 00:00</p>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="footer-contact">
    <div class="footer-bar">
        <div class="footer-logo"><img src="afbeeldingen/bowling_logo.png" alt="Logo"></div>
        <div class="footer-middle">
            <p>
                <strong>Bowling Center Brooklyn</strong><br>
                Brooklynlaan 12<br>
                1234 AB Stadshaven<br>
                Telefoon: 010 – 123 45 67<br>
                E-mail: info@bowlingcenterbrooklyn.nl
            </p>
        </div>
        <div class="footer-right">
            <p>
                <strong>Openingstijden</strong><br>
                Maandag: gesloten<br>
                Dinsdag t/m donderdag: 14:00 – 22:00<br>
                Vrijdag: 14:00 – 23:00<br>
                Zaterdag: 12:00 – 23:00<br>
                Zondag: 12:00 – 21:00
            </p>
        </div>
    </div>
</footer>

<script>
const menuBtn = document.getElementById('menu-btn');
const mobileMenu = document.getElementById('mobile-menu');
menuBtn.addEventListener('click', () => { mobileMenu.classList.toggle('open'); });
</script>

</body>
</html>