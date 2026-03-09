<?php
// booking.php - Bevestigingspagina

// Haal data uit URL
$day = isset($_GET['day']) ? intval($_GET['day']) : 1;
$month = isset($_GET['m']) ? intval($_GET['m']) : 1;
$year = isset($_GET['y']) ? intval($_GET['y']) : date('Y');

$start_time = isset($_GET['start_time']) ? htmlspecialchars($_GET['start_time']) : '14:00';
$end_time = isset($_GET['end_time']) ? htmlspecialchars($_GET['end_time']) : '15:00';

$persons = isset($_GET['persons']) ? intval($_GET['persons']) : 1;
$children = isset($_GET['children']) ? intval($_GET['children']) : 0;
$lanes = isset($_GET['lanes']) ? $_GET['lanes'] : [1]; // array van banen

$snackpakket = isset($_GET['snackpakket']) ? htmlspecialchars($_GET['snackpakket']) : 'nee';
$kinderpartij = isset($_GET['kinderpartij']) ? htmlspecialchars($_GET['kinderpartij']) : 'nee';
$vrijgezellenfeest = isset($_GET['vrijgezellenfeest']) ? htmlspecialchars($_GET['vrijgezellenfeest']) : 'nee';

// Datum in leesbaar formaat
$nlMonths = ["januari", "februari", "maart", "april", "mei", "juni", "juli", "augustus", "september", "oktober", "november", "december"];
$formattedDate = sprintf('%02d %s %04d', $day, $nlMonths[$month - 1], $year);

// Eenvoudige prijsberekening
$pricePerLane = 50;
$priceBasis = ($snackpakket === 'basis') ? 10 * $persons : 0;
$priceLuxe = ($snackpakket === 'luxe') ? 20 * $persons : 0;
$priceTotal = (count($lanes) * $pricePerLane) + $priceBasis + $priceLuxe;
?>

<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmed - Bowling Center Brooklyn</title>
    <link rel="stylesheet" href="booking.css">
</head>

<body>

    <header class="header">
        <a href="index.php">
            <img src="afbeeldingen/bowling_logo.png" alt="Logo" class="logo-pfp">
        </a>
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
                <p>🕒 <span id="booking-datetime"><?= $formattedDate ?> - <?= $start_time ?> - <?= $end_time ?></span></p>
                <p>👤 Volwassenen: <span id="booking-adults"><?= $persons ?></span></p>
                <p>🧒 Kids: <span id="booking-kids"><?= $children ?></span></p>
                <p>🏟 Baan: <span id="booking-lanes"><?= implode(', ', $lanes) ?></span></p>
                <p>🍔 Snackpakket: <?= ucfirst($snackpakket) ?></p>
                <p>Kinderpartij: <?= ucfirst($kinderpartij) ?></p>
                <p>Vrijgezellenfeest: <?= ucfirst($vrijgezellenfeest) ?></p>
                <p>💰 Totaal: € <?= number_format($priceTotal, 2, ',', '') ?></p>
            </div>

            <div class="payment-overview">
                <h2>Betaling Overzicht</h2>
                <div class="line"></div>
                <p>Banen: € <?= count($lanes) * $pricePerLane ?></p>
                <p>Snackpakket Basis: € <?= $priceBasis ?></p>
                <p>Snackpakket Luxe: € <?= $priceLuxe ?></p>
                <div class="line"></div>
                <p class="total">Totaal: € <?= $priceTotal ?></p>
            </div>
        </section>
    </main>

    <!-- FOOTER -->
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

        menuBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('open');
        });
    </script>

</body>

</html>