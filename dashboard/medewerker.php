<!DOCTYPE html>
<?php
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] === 'user') {
    header("Location: ../index.php");
}

?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../login.css">

</head>
<body>
    <!-- Nav Menu -->
    <section class="login">
        <div class="header-bg">
            <div class="top-menu">
                <div class="top-menu-text">
                    <h1>Bowlingcenter Brooklyn Reservatie Systeem</h1>
                    <h1>Welkom, <?= $_SESSION['user_name']?> </h1>
                </div>

                <nav class="top-nav">
                    <ul>
                        <li><a href="index.php">Overzicht</a></li>
                        <li><a href="over_ons.php">Reservering</a></li>
                        <li><a href="#">Gebruikers</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </section>

    <form class="login-card" >
        <p><strong>Email</strong></p>
        <input type="email" name="email"><br><br>
    </form></body>
</html>