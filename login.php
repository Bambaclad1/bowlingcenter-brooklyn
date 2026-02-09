<!DOCTYPE html>

<?php
session_start();
require_once "db.php";

$signupMessage = "";
$badMessage = "";

// show signup success from redirect
if (isset($_GET['signup']) && $_GET['signup'] === 'success') {
    $signupMessage = "Account successvol gemaakt. U kan nu inloggen.";
}

// POST because GET shows password in URL
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($email === "" || $password === "") {
        $signupMessage = "Fill in all fields.";
    } else {
        // prepares SQL query and searches DB for query
        $sql = "SELECT id, name, email, password_hash, role, created_at FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();

        $user = $result->fetch_assoc();

        // login logic
        if (!$user) {
            $badMessage = "Email not found.";
        } elseif (!password_verify($password, $user["password_hash"])) {
            $badMessage = "Incorrect password.";
        } else {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];

            header("Location: index.php");
            exit;
        }
        $stmt->close();
    }
    $conn->close();
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
                <h1>Welkom</h1>
            </div>
        </div>
    </section>

    <form class="login-card" method="POST">
        <?php if ($signupMessage !== ""): ?>
            <p class="success-message"><?php echo $signupMessage; ?></p>
        <?php endif; ?>
        <?php if ($badMessage !== ""): ?>
            <p class="bad-message"><?php echo $badMessage; ?></p>
        <?php endif; ?>
        <p><strong>Email</strong></p>
        <input type="email" name="email"><br><br>
        <p><strong>Password</strong></p>
        <input type="password" name="password"><br><br>
        <button type="submit" name="login_clicked">Sign In</button>
        <a href="https://www.example.com/forgot.php">Forgot password?</a><br><br>
        <a href="signup.php">Switch to Sign Up</a>
    </form>

</body>

</html>