<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu & Footer</title>

    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">

    <!-- Jouw CSS bestand -->
    <link rel="stylesheet" href="style1.css">
</head>
<body>

<!-- ===== HEADER ===== -->
<header class="header">
    <button class="menu-btn" onclick="toggleMenu()">☰</button>
</header>

<!-- ===== MENU ===== -->
<nav class="mobile-menu" id="mobile-menu">
    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="over_ons.php">Over ons</a></li>
        <li><a href="#">Agenda</a></li>
        <li><a href="prijzen.php">Prijzen</a></li>
    <?php if (!isset($_SESSION['user_id'])): ?>
        <li><a href="login.php">Login</a></li>
    <?php else: ?>
        <li><a href="logout.php">Logout</a></li>
    <?php endif; ?>
    </ul>
</nav>


<!-- ===== FOOTER ===== -->
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


<!-- ===== SCRIPT (nodig voor open class in CSS) ===== -->
<script>
function toggleMenu() {
    document.getElementById("mobileMenu").classList.toggle("open");
}
</script>

</body>
</html>
