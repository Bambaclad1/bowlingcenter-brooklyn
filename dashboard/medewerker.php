<!DOCTYPE html>
<?php
session_start();
require_once "../db.php";
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] === 'user') {
  header("Location: ../index.php");
}
$reservation_date = $_GET['reservation_date'] ?? date('Y-m-d');

// prepares SQL query and searches DB for query
$stmt = $conn->prepare("SELECT * FROM reservations WHERE reservation_date = ?");
$stmt->bind_param("s", $reservation_date);
$stmt->execute();
$result = $stmt->get_result();

$noReservations = false;
if ($result->num_rows > 0) {
  $noReservations = true;
}

?>

<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="dashboard.css">
</head>

<body>
  <!-- Nav Menu -->
  <section class="login">
    <div class="header-bg">
      <div class="top-menu">
        <div class="top-menu-text">
          <h1>Bowlingcenter Brooklyn Reservatie Systeem</h1>
          <h1>Welkom, <?= $_SESSION['user_name'] ?> </h1>
        </div>

        <nav class="top-nav">
          <ul>
            <li><a href="index.php">Overzicht</a></li>
            <li><a href="over_ons.php">Reservering</a></li>
            <li><a href="../overzicht_gebruiker.php">Gebruikers</a></li>
          </ul>
        </nav>
      </div>
      <!-- Reservations -->
      <div class="calendar-background">
        <div class="calendar-location">
          <h2>Welkom in het reservatie overzicht</h2>
          <p>Als er geen reservaties zijn dan komt er niks verschijnen.</p>
          <!-- Filter knopje stuurt datum naar URL met GET form voor datum filter-->
          <form method="GET">
            <label for="reservation_date">Filter op datum:</label>
            <input type="date" name="reservation_date" value="<?= $reservation_date ?>">
            <button type="submit" class="filter-btn">Filter</button>
          </form>'
          <div class="table-wrapper">
            <table class="excel-table">
              <thead>
                <tr>
                  <?php
                  // Pakt de columns van de reservaties en gooit ze in een tabel
                  while ($field = $result->fetch_field()) {
                    echo "<th>{$field->name}</th>";
                  }
                  ?>
                </tr>
              </thead>
              <tbody>
                <?php
                // Maakt <td> kopjes aan als er data is, zo niet, else statement runt
                if ($result->num_rows > 0) {
                  while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    foreach ($row as $value) {
                      echo "<td>$value</td>";
                    }
                    echo "</tr>";
                  }
                } else {
                  echo "<tr><td colspan='10'><strong>Geen reservaties op deze datum!</strong></td></tr>";
                }
                ?>
              </tbody>
            </table>
          </div>
          <p></p>
        </div>
      </div>

    </div>
  </section>
</body>

</html>