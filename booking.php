<!DOCTYPE html>
<?php

?>
<html lang="nl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Booking Confirmed - Bowling Center Brooklyn</title>
<link rel="stylesheet" href="booking.css">
</head>
<body>

<!-- HEADER -->
<header class="header">
    <img src="afbeeldingen/bowling_logo.png" alt="Logo" class="logo-pfp">
    <button class="menu-btn" id="menu-btn">☰</button>
</header>

<!-- MOBILE MENU -->
<nav class="mobile-menu" id="mobile-menu">
    <ul>
        <li><a href="#">Over ons</a></li>
        <li><a href="#">Agenda</a></li>
        <li><a href="#">Prijzen</a></li>
        <li><a href="#">Login</a></li>
    </ul>
</nav>

<!-- CONFIRMATION -->
<main class="confirmation">
    <section class="booking-confirmed">
        <div class="check-icon">✔</div>
        <h1>Booking Confirmed!</h1>

        <div class="booking-details">
            <p>🕒 <span id="booking-datetime" data-datetime="2025-01-14T14:00">14 Januari 2025 - 14:00 - 15:00</span></p>
            <p>👤 Volwassenen: <span id="booking-adults" data-value="2">2</span></p>
            <p>🧒 Kids: <span id="booking-kids" data-value="2">2</span></p>
            <p>🍔 Snackbox Basis <span id="snack-basis" data-value="1">1x</span> | Snackbox Luxe <span id="snack-luxe" data-value="3">3x</span></p>
            <p>€ <span id="booking-total" data-value="98.99">98,99</span>-</p>
        </div>

        <div class="payment-overview">
            <h2>Betaling Overzicht</h2>
            <div class="line"></div>
            <p>BAAN 5: € <span id="price-baan" data-value="50">50</span></p>
            <p>Snackpakket Basis: € <span id="price-basis" data-value="89">89</span></p>
            <p>Snackpakket Luxe: € <span id="price-luxe" data-value="519">519</span></p>
            <p>BTW (21%): € <span id="price-vat" data-value="12.59">12,59</span></p>
            <div class="line"></div>
            <p class="total">Totaal: € <span id="price-total" data-value="67.37">67,37</span></p>
        </div>
    </section>
</main>

<!-- FOOTER -->
<footer>
    <div class="footer-content">
        <div class="footer-left">
            <img src="afbeeldingen/bowling_logo.png" alt="Logo" class="footer-logo">
            <p>Bowling Center Brooklyn</p>
            <p>Brooklynlaan 12<br>1234 AB Stadhaven</p>
            <p>Telefoon: 010 - 123 45 67</p>
            <p>E-mail: info@bowlingcenterbrooklyn.nl</p>
        </div>
        <div class="footer-right">
            <h3>Openingstijden</h3>
            <p>Maandag: gesloten</p>
            <p>Dinsdag t/m donderdag: 14:00 - 22:00</p>
            <p>Vrijdag: 14:00 - 23:00</p>
            <p>Zaterdag: 12:00 - 23:00</p>
            <p>Zondag: 12:00 - 21:00</p>
        </div>
    </div>
</footer>

<!-- SCRIPT -->
<script>
const menuBtn = document.getElementById('menu-btn');
const mobileMenu = document.getElementById('mobile-menu');

menuBtn.addEventListener('click', () => {
    mobileMenu.classList.toggle('open');
});
</script>

</body>
</html>
