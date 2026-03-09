<?php
// overzicht_gebruiker.php
session_start();
require_once "db.php"; // verwacht $conn = new mysqli(...)

function e($s)
{
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

// CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(24));
}
$csrf = $_SESSION['csrf_token'];

// =====================
// Alleen owner toegang
// =====================

// Hier gaan we ervan uit dat je bij login de rol in de session opslaat, bijvoorbeeld:
// $_SESSION['user_role'] = 'owner'; of 'employee' of 'customer'

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'owner') {
    // Redirect naar homepage of andere pagina
    header("Location: index.php"); // of je login pagina
    exit;
}

$errors = [];
$success = '';
$allowed_roles = ['customer', 'employee', 'owner'];


/* Handle POST actions */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $posted_csrf = $_POST['csrf_token'] ?? '';
    if (!hash_equals($csrf, $posted_csrf)) {
        $errors[] = "Ongeldige sessie (CSRF mismatch).";
    } else {
        $action = $_POST['action'] ?? '';
        if ($action === 'add') {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $role = $_POST['role'] ?? 'customer';

            if ($name === '' || $email === '' || $password === '') {
                $errors[] = "Naam, e-mail en wachtwoord zijn verplicht.";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Ongeldig e-mail adres.";
            } elseif (!in_array($role, $allowed_roles)) {
                $errors[] = "Ongeldige rol.";
            } else {
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO users (name, email, password_hash, role, created_at) VALUES (?, ?, ?, ?, NOW())");
                if ($stmt) {
                    $stmt->bind_param("ssss", $name, $email, $password_hash, $role);
                    if ($stmt->execute()) {
                        $success = "Gebruiker toegevoegd!";
                    } else {
                        if ($conn->errno === 1062) $errors[] = "E-mail is al in gebruik.";
                        else $errors[] = "DB error: " . $conn->error;
                    }
                    $stmt->close();
                } else {
                    $errors[] = "DB error: " . $conn->error;
                }
            }
        } elseif ($action === 'edit') {
            $id = intval($_POST['id'] ?? 0);
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $role = $_POST['role'] ?? 'customer';
            $new_password = $_POST['new_password'] ?? '';

            if ($id <= 0 || $name === '' || $email === '') {
                $errors[] = "ID, naam en e-mail zijn verplicht.";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Ongeldig e-mail adres.";
            } elseif (!in_array($role, $allowed_roles)) {
                $errors[] = "Ongeldige rol.";
            } else {
                if ($new_password !== '') {
                    $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                    $stmt = $conn->prepare("UPDATE users SET name=?, email=?, password_hash=?, role=? WHERE id=?");
                    $stmt->bind_param("ssssi", $name, $email, $password_hash, $role, $id);
                } else {
                    $stmt = $conn->prepare("UPDATE users SET name=?, email=?, role=? WHERE id=?");
                    $stmt->bind_param("sssi", $name, $email, $role, $id);
                }
                if ($stmt->execute()) {
                    $success = "Gebruiker bijgewerkt!";
                } else {
                    if ($conn->errno === 1062) $errors[] = "E-mail is al in gebruik.";
                    else $errors[] = "DB error: " . $conn->error;
                }
                $stmt->close();
            }
        } elseif ($action === 'delete') {
            $id = intval($_POST['id'] ?? 0);
            if ($id > 0) {
                $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
                $stmt->bind_param("i", $id);
                if ($stmt->execute()) $success = "Gebruiker verwijderd.";
                else $errors[] = "DB error: " . $conn->error;
                $stmt->close();
            } else $errors[] = "Ongeldige ID voor verwijderen.";
        }
    }
}

/* Fetch users */
$users = [];
$res = $conn->query("SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC");
if ($res) {
    while ($row = $res->fetch_assoc()) $users[] = $row;
    $res->free();
} else $errors[] = "DB error (fetch users): " . $conn->error;
?>
<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gebruikers Overzicht</title>
    <link rel="stylesheet" href="overzicht_gebruiker.css">
</head>

<body>

    <div class="admin-wrapper">
        <nav class="nav-tabs">
            <div class="hamburger" id="hamburger">&#9776;</div>
            <div class="nav-buttons">
                <button>Overzicht</button>
                <button>Reservering</button>
                <button class="active">Gebruikers</button>
                <button class="logout-btn">Uitloggen</button>
            </div>
        </nav>

        <main class="admin-content">
            <h1>Gebruikers Overzicht</h1>

            <?php if ($success): ?>
                <div class="msg success"><?php echo e($success); ?></div>
            <?php endif; ?>
            <?php if (!empty($errors)): foreach ($errors as $err): ?>
                    <div class="msg error"><?php echo e($err); ?></div>
            <?php endforeach;
            endif; ?>

            <!-- Add user -->
            <section class="add-user">
                <form method="post" class="form-inline">
                    <input type="hidden" name="csrf_token" value="<?php echo e($csrf); ?>">
                    <input type="hidden" name="action" value="add">
                    <input type="text" name="name" placeholder="Naam" required>
                    <input type="email" name="email" placeholder="E-mail" required>
                    <input type="password" name="password" placeholder="Wachtwoord" required>
                    <select name="role" required>
                        <option value="customer">Customer</option>
                        <option value="employee">Medewerker</option>
                        <option value="owner">Owner</option>
                    </select>
                    <button type="submit">Voeg Gebruiker Toe</button>
                </form>
            </section>

            <!-- Users table -->
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Naam</th>
                            <th>E-mail</th>
                            <th>Rol</th>
                            <th>Aangemaakt</th>
                            <th>Acties</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($users) === 0): ?>
                            <tr>
                                <td colspan="5">Geen gebruikers gevonden.</td>
                            </tr>
                            <?php else: foreach ($users as $u): ?>
                                <tr>
                                    <td><?php echo e($u['name']); ?></td>
                                    <td><?php echo e($u['email']); ?></td>
                                    <td><?php echo e($u['role']); ?></td>
                                    <td><?php echo e(date('d-m-Y H:i', strtotime($u['created_at']))); ?></td>
                                    <td>
                                        <a href="?edit=<?php echo e($u['id']); ?>"><button class="edit-btn">Bewerken</button></a>
                                        <form method="post" class="action-form" onsubmit="return confirm('Weet je zeker?');">
                                            <input type="hidden" name="csrf_token" value="<?php echo e($csrf); ?>">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo e($u['id']); ?>">
                                            <button type="submit" class="delete-btn">🗑</button>
                                        </form>
                                    </td>
                                </tr>
                        <?php endforeach;
                        endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Edit form -->
            <?php
            if (isset($_GET['edit'])):
                $edit_id = intval($_GET['edit']);
                $stmt = $conn->prepare("SELECT id,name,email,role FROM users WHERE id=?");
                $stmt->bind_param("i", $edit_id);
                $stmt->execute();
                $res = $stmt->get_result();
                $user_edit = $res->fetch_assoc();
                $stmt->close();
                if ($user_edit):
            ?>
                    <section style="margin-top:20px;">
                        <h2>Bewerk gebruiker: <?php echo e($user_edit['name']); ?></h2>
                        <form method="post" class="form-inline">
                            <input type="hidden" name="csrf_token" value="<?php echo e($csrf); ?>">
                            <input type="hidden" name="action" value="edit">
                            <input type="hidden" name="id" value="<?php echo e($user_edit['id']); ?>">

                            <input type="text" name="name" value="<?php echo e($user_edit['name']); ?>" required>
                            <input type="email" name="email" value="<?php echo e($user_edit['email']); ?>" required>
                            <select name="role" required>
                                <?php foreach ($allowed_roles as $r): ?>
                                    <option value="<?php echo e($r); ?>" <?php echo ($user_edit['role'] === $r) ? 'selected' : ''; ?>><?php echo e($r); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <input type="password" name="new_password" placeholder="Nieuw wachtwoord (optioneel)">
                            <button type="submit" class="edit-btn">Opslaan</button>
                            <a href="overzicht_gebruiker.php"><button type="button">Annuleer</button></a>
                        </form>
                    </section>
            <?php
                endif;
            endif;
            ?>
        </main>
    </div>

    <script>
        const hamburger = document.getElementById('hamburger');
        const navButtons = document.querySelector('.nav-buttons');
        if (hamburger) hamburger.addEventListener('click', () => navButtons.classList.toggle('show'));
    </script>
</body>

</html>