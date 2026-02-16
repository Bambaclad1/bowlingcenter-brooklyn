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

    <!-- Navigatie -->
    <nav class="nav-tabs">
        <div class="hamburger" id="hamburger">&#9776;</div>
    <div class="nav-buttons">
        <button>Overzicht</button>
        <button>Reservering</button>
        <button class="active">Gebruikers</button>
        <button class="logout-btn">Uitloggen</button> <!-- toegevoegde knop -->
    </div>

    </nav>

    <!-- Content -->
    <main class="admin-content">

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Naam</th>
                        <th>Rol</th>
                        <th>Acties</th>
                    </tr>
                </thead>
                <tbody id="userTable">
                    <!-- Voorbeeld rij -->
                    <tr data-id="1">
                        <td>Naam hier</td>
                        <td><span class="role employee">Medewerker</span></td>
                        <td>
                            <button class="edit-btn" data-id="1">Bewerken</button>
                            <button class="delete-btn" data-id="1">🗑</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="add-user">
            <button class="add-btn">Voeg Gebruiker Toe</button>
        </div>

    </main>

</div>

<script>
    // Hamburger menu toggle
    const hamburger = document.getElementById('hamburger');
    const navButtons = document.querySelector('.nav-buttons');

    hamburger.addEventListener('click', () => {
        navButtons.classList.toggle('show');
    });
</script>

</body>
</html>
