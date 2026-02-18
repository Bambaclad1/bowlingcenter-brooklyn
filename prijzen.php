<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prijzen - Bowling Center Brooklyn</title>
    <link rel="stylesheet" href="style1.css">
</head>
<body>

<header class="header">
    <button class="menu-btn" id="menu-btn">☰</button>
</header>

<nav class="mobile-menu" id="mobile-menu">
    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="over_ons.php">Over ons</a></li>
        <li><a href="booking/beta.php">Agenda</a></li>
        <li><a href="prijzen.php">Prijzen</a></li>
        <li><a href="login.php">Login</a></li>
    </ul>
</nav>

<section class="prijzen-hero">
    <h1 class="prijzen-title">Prijzen</h1>
    <p class="prijzen-subtitle">Reserveer onze baan voor een leuke avond bowlen</p>

    <div class="prijs-blokken">

    <!-- Blok 1 -->
    <div class="blok blok1">
        <p class="prijs-tekst blok1-tekst">
            <strong>Bowlen</strong><br><br>

            Maandag t/m donderdag: <strong>€24,-</strong> per uur<br>
            Vrijdag t/m zondag: <strong>€28,-</strong> per uur<br>
            Na 18:00: <strong>€33,50,-</strong> per uur
        </p>
    </div>

    <!-- Blok 2 -->
    <div class="blok blok2">
        <p class="prijs-tekst blok2-tekst">
            <strong>Magic Bowlen</strong><br><br>

            Elke vrijdag t/m zondag<br>
            Vanaf <strong>18:00</strong> t/m <strong>24:00</strong>
        </p>
    </div>

    <!-- Blok 3 -->
    <div class="blok blok3">
        <p class="prijs-tekst blok3-tekst">
            <strong>Snackpakketten</strong><br><br>

            <strong>Basis Pakket €32,-</strong><br>
            • Friet met saus naar keuze<br>
            • 1 Snack (Frikandel / Kipcorn)<br>
            • Fris naar keuze<br><br>

            <strong>Luxe Pakket €84,-</strong><br>
            • Friet met saus naar keuze<br>
            • 2 Snacks (Frikandel / Kipcorn)<br>
            • Fris naar keuze<br>
            • Chips naar keuze
        </p>
    </div>

</div>

</section>

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
