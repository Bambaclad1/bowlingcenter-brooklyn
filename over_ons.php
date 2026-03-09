<!DOCTYPE html>
<html lang="nl">
<?php
session_start();

// Huidige pagina bepalen voor actieve link
$current_page = basename($_SERVER['PHP_SELF']);
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bowling Center Brooklyn - Over Ons</title>
    <link rel="stylesheet" href="style1.css">
</head>

<body>

    <!-- Header met hamburger -->
    <header class="header">
        <button class="menu-btn" id="menu-btn">☰</button>
    </header>

    <!-- Slide-out menu -->
    <nav class="mobile-menu" id="mobile-menu">
        <ul>
            <li><a href="index.php" class="<?= ($current_page == 'index.php') ? 'active' : '' ?>">Home</a></li>
            <li><a href="over_ons.php" class="<?= ($current_page == 'over_ons.php') ? 'active' : '' ?>">Over ons</a></li>
            <li><a href="booking/beta.php" class="<?= ($current_page == 'beta.php') ? 'active' : '' ?>">Agenda</a></li>
            <li><a href="prijzen.php" class="<?= ($current_page == 'prijzen.php') ? 'active' : '' ?>">Prijzen</a></li>
            <?php if (!isset($_SESSION['user_id'])): ?>
                <li><a href="login.php" class="<?= ($current_page == 'login.php') ? 'active' : '' ?>">Login</a></li>
            <?php elseif (isset($_SESSION['user_role']) && ($_SESSION['user_role'] === 'medewerker' || $_SESSION['user_role'] === 'owner')): ?>
                <li><a href="dashboard/medewerker.php">Dashboard</a></li>
            <?php else: ?>
                <li><a href="logout.php">Logout</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <!-- Main content met achtergrondafbeelding -->
    <main class="over-ons-main">
        <!-- Hero / Banner -->
        <section class="banner_one">
            <img src="afbeeldingen/bowling_banner_mevrouw.jpg" alt="Bowling Center Brooklyn">
            <div class="banner_text">
                <h2>Over ons</h2>
                <p>
                    BowlingBrooklyn, voor gezelligheid, actie en heerlijk eten!
                    We vertellen je graag nog meer over wie wij zijn.
                </p>
            </div>
        </section>

        <!-- Afbeeldingen + tekst -->
        <section class="hero-images-vertical">

            <div class="hero-image-text">
                <img src="afbeeldingen/bowlingbar.png" alt="Bowlingbar">
                <div class="text-block">
                    <h3>Onze gezellige bar</h3>
                    <p>
                        Onze bar is het gezellige ontmoetingspunt van de bowlingbaan, waar jong en oud samen kunnen genieten. Of je nu even pauzeert tijdens het bowlen, wacht op je beurt of gezellig wilt napraten, bij onze bar voel je je meteen thuis.
                        We bieden een ruime keuze aan frisdranken, warme dranken en lekkere snacks voor het hele gezin. Voor kinderen zijn er leuke drankjes en tussendoortjes, terwijl ouders kunnen ontspannen met een kop koffie of thee.
                        Dankzij de warme sfeer, comfortabele zitplaatsen en het uitzicht op de bowlingbanen is onze bar de perfecte plek om samen te komen. Het is een fijne omgeving waar gezinnen, vrienden en groepen zich welkom voelen.
                        Onze bar staat voor gezelligheid, ontspanning en gastvrijheid – een plek waar iedereen kan genieten, met of zonder een potje bowlen.
                    </p>
                </div>
            </div>

            <div class="hero-image-text reverse">
                <img src="afbeeldingen/bowlingteam.png" alt="Bowlingteam">
                <div class="text-block">
                    <h3>Welkom bij Bowlingcenter Brooklyn</h3>
                    <p>
                        Welkom bij Bowlingcenter Brooklyn, het gezelligste bowlingcenter voor het hele gezin! Bij ons draait alles om samen plezier maken. Of je nu komt met je kinderen, familie, vrienden of opa en oma: iedereen is welkom om te genieten van een ontspannen en leuke bowlingervaring.
                        Ons bowlingcenter beschikt over 8 moderne bowlingbanen, speciaal ingericht voor zowel jong als oud. Dankzij kindvriendelijke voorzieningen zoals bumpers, lichte ballen en een veilige omgeving kunnen ook de jongste bowlers volop meedoen. Voor ouders en begeleiders is er een comfortabele zitplek met goed zicht op de banen.
                        Naast bowlen kun je bij ons terecht voor een hapje en een drankje. Bowlingcenter Brooklyn is dé ideale locatie voor kinderfeestjes, familie-uitjes, verjaardagen en gezellige middagen of avonden samen. Wij zorgen voor een warme sfeer, persoonlijke aandacht en vooral veel plezier.
                    </p>
                </div>
            </div>

        </section>
    </main>

    <!-- Footer -->
    <footer class="footer-contact">
        <div class="footer-bar">

            <!-- Logo links -->
            <div class="footer-logo">
                <img src="afbeeldingen/bowling_logo.png" alt="Logo Bowling Center Brooklyn">
            </div>

            <!-- Adres/Contact in het midden -->
            <div class="footer-middle">
                <p>
                    <strong>Bowling Center Brooklyn</strong><br>
                    Brooklynlaan 12<br>
                    1234 AB Stadshaven<br>
                    Telefoon: 010 – 123 45 67<br>
                    E-mail: info@bowlingcenterbrooklyn.nl
                </p>
            </div>

            <!-- Openingstijden rechts -->
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

    <!-- Hamburger script -->
    <script>
        const menuBtn = document.getElementById('menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');

        menuBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('open');
        });
    </script>

</body>

</html>