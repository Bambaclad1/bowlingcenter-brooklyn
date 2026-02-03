<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bowling Center Brooklyn</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- Header met hamburger -->
<header class="header">
    <button class="menu-btn" id="menu-btn">☰</button>
</header>

<!-- Slide-out menu -->
<nav class="mobile-menu" id="mobile-menu">
    <ul>
        <li><a href="#">Over ons</a></li>
        <li><a href="#">Agenda</a></li>
        <li><a href="#">Prijzen</a></li>
        <li><a href="#">Login</a></li>
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

<!-- Desktop sectie met 2 vakjes -->
<section class="desktop-section">
    <div class="desktop-container">
        <!-- Vakje 1 -->
        <div class="desktop-card">
            <img src="afbeeldingen/bowlen.jpeg" alt="Bowlen">
            <div class="card-text">
                <h3>Bowlen</h3>
                <p>Dagelijks 14:00 - 22:00</p>
            </div>
        </div>

        <!-- Vakje 2 -->
        <div class="desktop-card">
            <img src="afbeeldingen/magicbowling.png" alt="Magic Bowlen">
            <div class="card-text">
                <h3>Magic Bowlen</h3>
                <p><br>Elke weekend van
                <br>22:00 tot 00:00</p>
            </div>
        </div>
    </div>

<div class="contact-section">
    <!-- Logo links -->
    <div class="contact-logo">
        <img src="afbeeldingen/bowling_logo.png" alt="Logo Bowling Center Brooklyn">
    </div>

    <!-- Adres/Contact in het midden -->
    <div class="contact-middle">
        <p>
            <strong>Bowling Center Brooklyn</strong><br>
            Brooklynlaan 12<br>
            1234 AB Stadshaven<br>
            Telefoon: 010 – 123 45 67<br>
            E-mail: info@bowlingcenterbrooklyn.nl
        </p>
    </div>

    <!-- Openingstijden rechts -->
    <div class="contact-right">
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

<!-- JavaScript voor hamburger -->
<script>
const menuBtn = document.getElementById('menu-btn');
const mobileMenu = document.getElementById('mobile-menu');

menuBtn.addEventListener('click', () => {
    mobileMenu.classList.toggle('open');
});
</script>

</body>
</html>
