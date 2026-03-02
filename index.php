<!DOCTYPE html>
<html lang="nl">
<?php
    session_start();

    if (isset($_POST["unset"])) {
        session_unset();
        session_destroy();
    }

    $current_page = basename($_SERVER['PHP_SELF']);
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bowling Center Brooklyn</title>
    <link rel="stylesheet" href="style1.css">
</head>
<body>

<!-- Developer Debug -->
<p><b>DEVELOPER DEBUG (Not for production)</b></p>
<p>User ID: <?= $_SESSION['user_id'] ?? 'not logged in' ?></p>
<p>Email: <?= $_SESSION['user_email'] ?? '—' ?></p>
<p>Name: <?= $_SESSION['user_name'] ?? '—' ?></p>
<p>Role: <?= $_SESSION['user_role'] ?? '—' ?></p>
<form method="POST">
    <button type="submit" name="unset">session_unset()</button>
</form>

<!-- Header -->
<header class="header">
    <button class="menu-btn" id="menu-btn">☰</button>
</header>

<!-- Mobile menu -->
<nav class="mobile-menu" id="mobile-menu">
    <ul>
        <li><a href="index.php" class="<?= ($current_page == 'index.php') ? 'active' : '' ?>">Home</a></li>
        <li><a href="over_ons.php" class="<?= ($current_page == 'over_ons.php') ? 'active' : '' ?>">Over ons</a></li>
        <li><a href="booking/beta.php" class="<?= ($current_page == 'beta.php') ? 'active' : '' ?>">Agenda</a></li>
        <li><a href="prijzen.php" class="<?= ($current_page == 'prijzen.php') ? 'active' : '' ?>">Prijzen</a></li>

        <?php if (!isset($_SESSION['user_id'])): ?>
            <!-- Niet ingelogd -->
            <li>
                <a href="login.php" class="<?= ($current_page == 'login.php') ? 'active' : '' ?>">
                    Login
                </a>
            </li>

        <?php else: ?>
            <!-- Wel ingelogd -->

            <?php if ($_SESSION['user_role'] === 'user'): ?>
                <li>
                    <a href="reservatiezien.php" 
                       class="<?= ($current_page == 'reservatiezien.php') ? 'active' : '' ?>">
                        Dashboard
                    </a>
                </li>

            <?php elseif ($_SESSION['user_role'] === 'medewerker' || $_SESSION['user_role'] === 'owner'): ?>
                <li>
                    <a href="dashboard/medewerker.php">
                        Dashboard
                    </a>
                </li>
            <?php endif; ?>

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
        <a href="booking/beta.php" class="btn">Reserveer nu</a>
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
        <div class="footer-logo">
            <img src="afbeeldingen/bowling_logo.png" alt="Logo">
        </div>
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
menuBtn.addEventListener('click', () => { 
    mobileMenu.classList.toggle('open'); 
});
</script>

</body>
</html>