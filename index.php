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
