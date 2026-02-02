<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bowling Center Brooklyn</title>
    <link rel="stylesheet" href="prijzen.css">
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

<!-- PRIJZEN -->
<section class="prijzen-hero">
    <h1 class="prijzen-title">Prijzen</h1>
    <p class="prijzen-subtitle">Reserveer onze baan voor een leuke avond bowlen</p>

    <div class="prijs-blokken">

        <div class="blok">
            <p class="prijs-tekst blok1-tekst">
                Maandag t/m donderdag: € 24,- per uur<br>
                Vrijdag t/m zondag: € 28,- per uur<br>
                Na 18:00: € 33,50,- per uur
            </p>
        </div>

        <div class="blok">
            <p class="prijs-tekst blok2-tekst">
                Magic Bowlen<br>
                Elke vrijdag t/m zondag<br>
                Vanaf 18:00 t/m 24:00
            </p>
        </div>

        <div class="blok">
            <p class="prijs-tekst blok3-tekst">
                <strong>Snackpakketten</strong><br><br>
                <strong>Basis Pakket € 32,-</strong><br>
                Friet met saus naar keuze<br>
                1 Snack (Frikandel/Kipcorn)<br>
                Fris naar keuze<br><br>
                <strong>Luxe Pakket € 84,-</strong><br>
                Friet met saus naar keuze<br>
                2 Snacks (Frikandel/Kipcorn)<br>
                Fris naar keuze<br>
                Chips naar keuze
            </p>
        </div>

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