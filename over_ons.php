<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bowling Center Brooklyn - Over Ons</title>
    <link rel="stylesheet" href="over_ons.css">
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
            <!-- Banner -->
            <div class="banner_one">
                <img src="afbeeldingen/bowling_banner_mevrouw.jpg" alt="Bowling Center Brooklyn">
                <div class="banner_text">
                    <h2>Over ons</h2>
                    <p>BowlingBrooklyn, voor gezelligheid, actie en heerlijk eten! We vertellen je graag nog meer over wie wij zijn!</p>
                </div>
            </div>

            <!-- Twee verticale afbeeldingen -->
            <div class="hero-images-vertical">
                <img src="afbeeldingen/bowlingbar.png" alt="Bowlingbar">
                <img src="afbeeldingen/bowlingteam.png" alt="Bowlingteam">
            </div>

            <p class="hero-subtitle">Succes met het editen van deze template! Verwijder de hero sectie en je hebt een nette template.</p>
        </div>
    </section>

    <!-- JavaScript voor hamburger menu -->
    <script>
        const menuBtn = document.getElementById('menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');

        menuBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('open');
        });
    </script>
    
</body>
</html>
