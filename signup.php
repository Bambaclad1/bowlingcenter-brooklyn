<!DOCTYPE html>
<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $canLogin = true;
    $fname = trim($_POST['fname'] ?? '');
    $lname = trim($_POST['lname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    if ($fname === '' || $lname === '' || $email === '' || $password === '' || $password_confirm === '') {
        echo "<script>alert('Fill in all fields');</script>";
        $canLogin = false;
    }

    if ($password !== $password_confirm) {
        echo "<script>alert('Passwords do not match');</script>";
        $canLogin = false;
    }

    if ($canLogin) {
        $fullName = $fname . ' ' . $lname;
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO `users` (`name`, `email`, `password_hash`, `role`, `created_at`)
        VALUES (?, ?, ?, 'user', NOW())";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param('sss', $fullName, $email, $passwordHash);
        $stmt->execute();

        $stmt->close();
        $conn->close();
        echo "<script>alert('You may now login with your credentials.');</script>";
        header(header: 'Location: login.php?signup=success');
        exit;
    } else {
        $conn->close();
    }
}
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bowling Center Brooklyn</title>
    <link rel="stylesheet" href="login.css">
</head>

<body>

    <section class="login">
        <div class="header-bg">
            <div class="login-top">
                <div class="logo-circle">
                    <img src="afbeeldingen/bowling_logo.png" alt="Bowling Center Brooklyn" width="180.5" height="205.5">
                </div>
                <h1>Welkom, maak een account aan.</h1>
            </div>
        </div>
    </section>

    <form class="login-card" method="POST">
        <p><strong>First Name</strong></p>
        <input type="text" name="fname"><br><br>
        <p><strong>Last Name</strong></p>
        <input type="text" name="lname"><br><br>
        <p><strong>Password</strong></p>
        <input type="password" name="password"><br><br>
        <p><strong>Password (Confirmation)</strong></p>
        <input type="password" name="password_confirm"><br><br>
        <p><strong>Email</strong></p>
        <input type="email" name="email"><br><br>
        <button type="submit">Sign Up</button><br><br>
        <a href="login.php">Switch to Log In</a>
    </form>


</body>


</html>