<!DOCTYPE html>
<?php
session_start();
require_once "../db.php"; // $conn is hier de mysqli verbinding

// Ophalen van gegevens uit de URL
$m = $_GET['m'] ?? '';
$y = $_GET['y'] ?? '';
$day = $_GET['day'] ?? '';
$persons = $_GET['persons'] ?? '';
$children = $_GET['children'] ?? '';
$lanes = $_GET['lanes'] ?? []; // Array
$start_time = $_GET['start_time'] ?? '';
$end_time = $_GET['end_time'] ?? '';
$snackpakket = $_GET['snackpakket'] ?? '';
$kinderpartij = $_GET['kinderpartij'] ?? '';
$vrijgezellenfeest = $_GET['vrijgezellenfeest'] ?? '';

// Banen array naar string voor display
$banenNaarString = array_map(fn($index) => $index + 1, array_keys($lanes));

$confirmed = false;
$message = "";

if (isset($_POST['fname']) || isset($_SESSION['user_id'])) {

    if (isset($_SESSION['user_id'])) {
        // Try to use the session user
        $user_id = $_SESSION['user_id'];
        $name_full = $_SESSION['user_name'] ?? 'Onbekend';
        $email = $_SESSION['user_email'] ?? '';
        $number = $_SESSION['user_number'] ?? '';

        // Check if user exists in DB
        $stmt_check_user = $conn->prepare("SELECT id FROM users WHERE id = ?");
        $stmt_check_user->bind_param("i", $user_id);
        $stmt_check_user->execute();
        $stmt_check_user->store_result();

        if ($stmt_check_user->num_rows === 0) {
            // User does NOT exist: create account automatically
            $password_hash = password_hash('placeholder123', PASSWORD_DEFAULT);
            $role = 'customer';
            $created_at = date('Y-m-d H:i:s');

            $stmt_create = $conn->prepare("INSERT INTO users (id, name, email, password_hash, role, created_at) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt_create->bind_param("isssss", $user_id, $name_full, $email, $password_hash, $role, $created_at);
            if (!$stmt_create->execute()) {
                die("Fout bij automatisch aanmaken gebruiker: " . $stmt_create->error);
            }
            $stmt_create->close();
        }

        $stmt_check_user->close();

    } else {
        // Regular registration flow from form
        $fname = trim($_POST['fname']);
        $lname = trim($_POST['lname']);
        $email = trim($_POST['email']);
        $number = trim($_POST['number']);

        $password_hash = password_hash('placeholder123', PASSWORD_DEFAULT);
        $role = 'customer';
        $created_at = date('Y-m-d H:i:s');

        // Check if email exists
        $stmt_check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt_check->bind_param("s", $email);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            die("<p style='color:red;'>Deze email is al geregistreerd!</p>");
        } else {
            $name_full = $fname . ' ' . $lname;
            $stmt = $conn->prepare("INSERT INTO users (name, email, password_hash, role, created_at) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $name_full, $email, $password_hash, $role, $created_at);
            if (!$stmt->execute()) die("Fout bij registratie: " . $stmt->error);
            $user_id = $stmt->insert_id;
            $stmt->close();
        }

        $stmt_check->close();
    }

    // Insert reservation (user_id is now guaranteed to exist)
    $reservation_date = "$y-$m-$day";
    $adults = $persons;
    $snackbox_basic = ($snackpakket === 'basic') ? 1 : 0;
    $snackbox_luxury = ($snackpakket === 'luxury') ? 1 : 0;
    $subtotal = 0;
    $vat = 0;
    $total_amount = 0;
    $is_paid = 0;
    $created_at_res = date('Y-m-d H:i:s');

    $stmt_res = $conn->prepare("INSERT INTO reservations
        (user_id, reservation_date, timeslot_start, timeslot_end, adults, children, snackbox_basic, snackbox_luxury, subtotal, vat, total_amount, is_paid, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt_res->bind_param(
        "isssiiiidddis",
        $user_id,
        $reservation_date,
        $start_time,
        $end_time,
        $adults,
        $children,
        $snackbox_basic,
        $snackbox_luxury,
        $subtotal,
        $vat,
        $total_amount,
        $is_paid,
        $created_at_res
    );

    if ($stmt_res->execute()) {
        $message = "<p style='color:green;'>Reservering succesvol toegevoegd! Bedankt, $name_full.</p>";
    } else {
        die("<p style='color:red;'>Fout bij toevoegen reservering: " . $stmt_res->error . "</p>");
    }

    $stmt_res->close();

    parse_str($_SERVER['QUERY_STRING'], $paramsconfirmed);
    header("Location: ../booking.php?" . http_build_query($paramsconfirmed));
    exit;

    } else {
        $message = "<p style='color:red;'>Fout bij registratie: " . $stmt->error . "</p>";
    }

    $stmt->close();


$stmt_check->close();


?>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bowling Center Brooklyn</title>
    <link rel="stylesheet" href="confirm2.css">
</head>

<body>
    <div class="booking-page">
        <div class="container">

            <div class="booking-card confirm-part-one">
                <h1>Reservatie Bevestiging Overzicht</h1>
                <p>Datum: <span class="highlight">
                        <?= "$day-$m-$y" ?>
                    </span></p>
                <p>Tijd: <span class="highlight">
                        <?= "Van: $start_time tot $end_time" ?>
                    </span></p>
                <p>Volwassenen: <span class="highlight">
                        <?= $persons ?>
                    </span></p>
                <p>Kinderen: <span class="highlight">
                        <?= $children ?>
                    </span></p>
                <p>Banen: <span class="highlight">
                        <?= implode(', ', $banenNaarString) ?>
                    </span></p>
                <p>Snackpakket: <span class="highlight">
                        <?= $snackpakket ?>
                    </span></p>
                <?php if ($kinderpartij === 'ja'): ?>
                    <p class="highlight">Kinderfeestje is aangetikt!</p>
                <?php endif; ?>
                <?php if ($vrijgezellenfeest === 'ja'): ?>
                    <p class="highlight">Vrijgezellenfeest is aangetikt!</p>
                <?php endif; ?>

                <form method="POST">
                    <p>Ziet dit er goed uit?</p>
                    <div style="display:flex; gap:16px;">
                        <button type="submit" name="confirm" value="ja">Ja</button>
                        <button type="submit" name="confirm" value="nee">Nee</button>
                    </div>
                </form>
            </div>

            <?php if ($confirmed == true): ?>
                <div class="booking-card confirm-part-two">
                    <form method="POST">
                        <h3>Vul deze informatie in</h3>

                        <input type="text" name="fname" placeholder="Voornaam" value="<?= $_SESSION['user_name'] ?? '' ?>"
                            required>
                        <input type="text" name="lname" placeholder="Achternaam"
                            value="<?= $_SESSION['user_lastname'] ?? '' ?>" required>
                        <input type="email" name="email" placeholder="Email" value="<?= $_SESSION['user_email'] ?? '' ?>"
                            required>
                        <input type="number" name="number" placeholder="Telefoon Nummer"
                            value="<?= $_SESSION['user_number'] ?? '' ?>" required>
                        <button type="submit">Reserveren</button>
                    </form>
                </div>
            <?php endif; ?>

        </div>
    </div>
</body>

</html>