<?php

require_once "../db.php"; // db connectie voor dubbele booking check

// reserveer.php (PHP 7.3 compatible)
ini_set('display_errors', 1);
error_reporting(E_ALL);

date_default_timezone_set('Europe/Amsterdam');

$current_page = basename($_SERVER['PHP_SELF']);

// Tijdslots (basis lijst)
$timeSlots = [
  "14:00",
  "15:00",
  "16:00",
  "17:00",
  "18:00",
  "19:00",
  "20:00",
  "21:00",
  "22:00",
  "23:00",
  "00:00"
];

// Gebruik GET als die er is, anders GET (voor maandnavigatie)
$src = !empty($_GET) ? $_GET : $_GET;

// ====== Kalender: maand/jaar via GET/GET ======
$nlMonths = array(
  1 => "januari",
  2 => "februari",
  3 => "maart",
  4 => "april",
  5 => "mei",
  6 => "juni",
  7 => "juli",
  8 => "augustus",
  9 => "september",
  10 => "oktober",
  11 => "november",
  12 => "december"
);
$nlDays = array('ma', 'di', 'wo', 'do', 'vr', 'za', 'zo');

$year = isset($src['y']) ? (int) $src['y'] : (int) date('Y');
$month = isset($src['m']) ? (int) $src['m'] : (int) date('n');

if ($month < 1) {
  $month = 12;
  $year--;
}
if ($month > 12) {
  $month = 1;
  $year++;
}
if ($year < 1970)
  $year = 1970;
if ($year > 2100)
  $year = 2100;

$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

// 0=ma ... 6=zo
$firstDow = (int) date('N', strtotime(sprintf('%04d-%02d-01', $year, $month))) - 1;

// prev/next month
$prevM = $month - 1;
$prevY = $year;
$nextM = $month + 1;
$nextY = $year;
if ($prevM < 1) {
  $prevM = 12;
  $prevY--;
}
if ($nextM > 12) {
  $nextM = 1;
  $nextY++;
}

$lanes = range(1, 8);

// ====== Form waarden ======
$todayY = (int) date('Y');
$todayM = (int) date('n');
$todayD = (int) date('j');

if (isset($src['day'])) {
  $day = (int) $src['day'];
} else {
  // alleen "vandaag" als je ook echt in huidige maand zit
  $day = ($year === $todayY && $month === $todayM) ? $todayD : 1;
}
$persons = isset($src['persons']) ? (int) $src['persons'] : 1;
$children = isset($src['children']) ? (int) $src['children'] : 0;

$start_time = isset($src['start_time']) ? (string) $src['start_time'] : $timeSlots[0];
$end_time = isset($src['end_time']) ? (string) $src['end_time'] : $timeSlots[1];

// dag clamp
if ($day < 1)
  $day = 1;
if ($day > $daysInMonth)
  $day = $daysInMonth;

// lanes default = baan 1
$selectedLanes = (isset($src['lanes']) && is_array($src['lanes'])) ? $src['lanes'] : array(1);
$selectedLanes = array_values(array_unique(array_map('intval', $selectedLanes)));
$selectedLanes = array_values(array_filter($selectedLanes, function ($v) {
  return $v >= 1 && $v <= 8;
}));

$laneCount = max(1, count($selectedLanes));

/*
  NIEUWE REGELS :
  - Kinderen max: 32 (8 banen * 4 kinderen)
  - Volwassenen max:
      * kinderen > 0 => floor(kinderen * 1.5)
      * kinderen = 0 => 64
  - Banen auto omhoog + omlaag:
      * volwassenen: 8 per baan
      * kinderen:    4 per baan
*/
// ===== NIEUWE REGELS (tabel) =====
$maxAdultsPerLane = 8;
$maxKidsPerLane = 4;

// Safety clamps
$persons = max(1, (int) $persons);
$children = max(0, (int) $children);

// Hard max kinderen 32
$children = min($children, 32);

// Functie: max volwassenen bij lanes + kinderen
$calcMaxAdults = function (int $lanes, int $kids) use ($maxAdultsPerLane): int {
  $penalty = (int) ceil($kids / 2); // elke 2 kids = -1 volwassene
  return ($lanes * $maxAdultsPerLane) - $penalty;
};

// Kies automatisch het MINIMUM aantal lanes dat past (en dus ook omlaag kan)
$neededLanes = 8; // fallback
for ($L = 1; $L <= 8; $L++) {
  $maxKidsL = $L * $maxKidsPerLane;
  $maxAdultsL = $calcMaxAdults($L, $children);

  if ($children <= $maxKidsL && $persons <= $maxAdultsL) {
    $neededLanes = $L;
    break;
  }
}

$selectedLanes = range(1, $neededLanes);
$laneCount = $neededLanes;

// Maxima berekenen voor UI (op basis van gekozen lanes)
$maxChildren = min(32, $laneCount * $maxKidsPerLane);
$children = min($children, $maxChildren);

$maxAdults = $calcMaxAdults($laneCount, $children);
$maxAdults = max(1, min(64, $maxAdults));
$persons = min($persons, $maxAdults);

// Extra opties
$snackpakket = isset($src['snackpakket']) ? (string) $src['snackpakket'] : 'Nee';
if (!in_array($snackpakket, ['nee', 'basis', 'luxe'], true))
  $snackpakket = 'nee';

$kinderpartij = isset($src['kinderpartij']) ? (string) $src['kinderpartij'] : 'nee';
if (!in_array($kinderpartij, ['nee', 'ja'], true))
  $kinderpartij = 'nee';

$vrijgezellenfeest = isset($src['vrijgezellenfeest']) ? (string) $src['vrijgezellenfeest'] : 'nee';
if (!in_array($vrijgezellenfeest, ['nee', 'ja'], true))
  $vrijgezellenfeest = 'nee';

if (isset($_GET['action']) && $_GET['action'] === 'submit') {
    $day = (int) ($_GET['day'] ?? 0);
    $month = (int) ($_GET['m'] ?? date('n'));
    $year = (int) ($_GET['y'] ?? date('Y'));
    $start_time = trim($_GET['start_time'] ?? '');
    $end_time = trim($_GET['end_time'] ?? '');
    $lanes = $_GET['lanes'] ?? [];

    // 🔥 PAD TIJDEN NAAR MYSQL FORMaat
    $start_time = substr($start_time, 0, 5) . ':00';
    $end_time = substr($end_time, 0, 5) . ':00';

    if ($day < 1 || strlen($start_time) < 8 || strlen($end_time) < 8 || empty($lanes)) {
        echo "<p style='color:orange;'>check invoer.</p>";
    } else {
        $reservationDate = sprintf('%04d-%02d-%02d', $year, $month, $day);
        $laneIds = array_values(array_unique(array_filter(array_map('intval', $lanes))));

        $placeholders = implode(',', array_fill(0, count($laneIds), '?'));
        $sql = "
            SELECT r.id, r.timeslot_start, r.timeslot_end, GROUP_CONCAT(rl.lane_id) as conflicting_lanes
            FROM reservations r
            JOIN reservations_lane rl ON rl.reservation_id = r.id
            WHERE r.reservation_date = ?
              AND rl.lane_id IN ($placeholders)
              AND r.timeslot_start < ?
              AND r.timeslot_end > ?
            GROUP BY r.id
            LIMIT 5
        ";

        $stmt = $conn->prepare($sql);
        $types = 's' . str_repeat('i', count($laneIds)) . 'ss';
        $params = array_merge([$reservationDate], $laneIds, [$end_time, $start_time]);

        $bindParams = [$types];
        foreach ($params as $k => $v) {
            $bindParams[] = &$params[$k];
        }
        call_user_func_array([$stmt, 'bind_param'], $bindParams);

        $stmt->execute();
        $result = $stmt->get_result();
        
        $conflicts = [];
        while ($row = $result->fetch_assoc()) {
            $conflicts[] = $row;
        }
        $stmt->close();

      if (!empty($conflicts)) {
          echo "<div class='booking-alert error'>";
          echo "<h3>Baan bezet!</h3>";
          echo "<p>deze banen zijn al bezet:</p>";
          foreach ($conflicts as $c) {
              echo "<div class='conflict-item'>";
              echo "booking #{$c['id']}: {$c['timeslot_start']}–{$c['timeslot_end']} (banen: {$c['conflicting_lanes']})";
              echo "</div>";
          }
          echo "</div>";
      } else {
          echo "<div class='booking-alert success'>";
          echo "Success!";
          echo "</div>";
          echo "<script>setTimeout(function(){ 
              window.location.href = 'confirm.php?" . http_build_query($_GET) . "'; 
          }, 1000);</script>";
      }
    }
}

$sql = "SELECT timeslot_start, timeslot_end,  FROM";
?>
<!DOCTYPE html>
<html lang="nl">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Bowling Center Brooklyn</title>

  <!-- Hamburger knop -->
  <button class="menu-btn" id="menuBtn" aria-label="Open menu">
    &#9776; <!-- de klassieke hamburger -->
  </button>

  <!-- Mobile menu -->
  <nav class="mobile-menu" id="mobile-menu">
    <ul>
      <li><a href="../index.php" class="<?= ($current_page == 'index.php') ? 'active' : '' ?>">Home</a></li>
      <li><a href="../over_ons.php" class="<?= ($current_page == 'over_ons.php') ? 'active' : '' ?>">Over ons</a></li>
      <li><a href="beta.php" class="<?= ($current_page == 'beta.php') ? 'active' : '' ?>">Agenda</a></li>
      <li><a href="../prijzen.php" class="<?= ($current_page == 'prijzen.php') ? 'active' : '' ?>">Prijzen</a></li>
      <?php if (!isset($_SESSION['user_id'])): ?>
        <li><a href="../login.php" class="<?= ($current_page == 'login.php') ? 'active' : '' ?>">Login</a></li>
      <?php elseif (isset($_SESSION['user_role']) && ($_SESSION['user_role'] === 'medewerker' || $_SESSION['user_role'] === 'owner')): ?>
        <li><a href="../dashboard/medewerker.php">Dashboard</a></li>
      <?php else: ?>
        <li><a href="../logout.php">Logout</a></li>
      <?php endif; ?>
    </ul>
  </nav>

  <script>
    const menuBtn = document.getElementById('menuBtn');
    const mobileMenu = document.getElementById('mobile-menu');
    menuBtn.addEventListener('click', () => {
      mobileMenu.classList.toggle('open');
    });
  </script>

  <style>
    body {
      margin: 0;
      /*this fixes the white padding outside the booking page*/
    }

    .booking-page {
      padding: 80px 16px;
      background: #000;
      position: relative;
    }

    .booking-page::before {
      content: "";
      position: absolute;
      inset: 0;
      background:
        radial-gradient(700px 380px at 20% 20%, rgba(255, 47, 220, .18), transparent 60%),
        radial-gradient(680px 360px at 80% 30%, rgba(140, 60, 255, .14), transparent 60%),
        radial-gradient(800px 420px at 50% 100%, rgba(255, 47, 220, .10), transparent 65%);
      pointer-events: none;
    }

    .booking-page .container {
      max-width: 1200px;
      margin: 0 auto;
      position: relative;
    }

    .booking-card {
      background: rgba(20, 20, 40, 0.60);
      backdrop-filter: blur(12px);
      border: 1px solid rgba(255, 255, 255, 0.10);
      box-shadow: 0 10px 40px rgba(0, 0, 0, .65);
      border-radius: 18px;
      padding: 22px;
    }

    .booking-page h2 {
      text-align: center;
      font-size: 32px;
      font-weight: 800;
      margin-bottom: 18px;
    }

    .cal-head {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      margin: 8px 0 12px;
    }

    .cal-title {
      font-weight: 800;
      font-size: 18px;
      text-transform: lowercase;
    }

    .cal-nav {
      width: 42px;
      height: 42px;
      border-radius: 14px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      text-decoration: none;
      cursor: pointer;
      color: rgba(255, 255, 255, .95);
      background: rgba(255, 255, 255, 0.06);
      border: 1px solid rgb(255, 255, 255);
    }

    .weekdays {
      display: grid;
      grid-template-columns: repeat(7, 1fr);
      gap: 10px;
      margin-bottom: 10px;
      text-align: center;
      font-weight: 700;
      opacity: .85;
    }

    .calendar {
      display: grid;
      grid-template-columns: repeat(7, 1fr);
      gap: 10px;
    }

    .day {
      min-height: 58px;
      border-radius: 14px;
      border: 1px solid rgba(255, 255, 255, 0.10);
      background: rgba(255, 255, 255, 0.06);
      color: rgba(255, 255, 255, .95);
      cursor: pointer;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      gap: 2px;
    }

    .day.empty {
      background: transparent;
      border: 1px dashed rgba(255, 255, 255, 0.08);
      cursor: default;
      opacity: .35;
    }

    .day.active {
      background: rgba(255, 47, 220, 0.22);
      border-color: rgba(255, 47, 220, 0.55);
    }

    .section {
      margin-top: 20px;
    }

    b {
      display: block;
      font-size: 16px;
      font-weight: 800;
      margin-bottom: 8px;
    }

    .counter {
      display: flex;
      gap: 10px;
      align-items: center;
    }

    .counter button {
      width: 42px;
      height: 42px;
      border-radius: 14px;
      border: 1px solid rgba(255, 255, 255, 0.12);
      background: rgba(255, 255, 255, 0.06);
      color: rgba(255, 255, 255, .95);
      font-size: 20px;
      font-weight: 900;
      cursor: pointer;
    }

    .counter input {
      width: 96px;
      height: 42px;
      text-align: center;
      border-radius: 14px;
      border: 1px solid rgba(255, 255, 255, 0.12);
      background: rgba(0, 0, 0, 0.35);
      color: rgba(255, 255, 255, .95);
      font-weight: 900;
      font-size: 16px;
      outline: none;
    }

    .lanes {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
    }

    .lane {
      width: 42px;
      height: 42px;
      border-radius: 14px;
      border: 1px solid rgba(255, 255, 255, 0.12);
      background: rgba(255, 255, 255, 0.06);
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 900;
      cursor: pointer;
    }

    .lane.active {
      background: rgba(255, 47, 220, 0.22);
      border-color: rgba(255, 47, 220, 0.55);
    }

    .select-wrap {
      position: relative;
      max-width: 360px;
    }

    .select-wrap select {
      width: 100%;
      height: 46px;
      border-radius: 14px;
      border: 1px solid rgba(255, 255, 255, 0.12);
      background: rgba(255, 255, 255, 0.06);
      color: rgba(255, 255, 255, .95);
      font-weight: 900;
      padding: 0 14px;
      appearance: none;
      outline: none;
      cursor: pointer;
    }

    /* dropdown optie background */
    .select-wrap select option {
      background: #0b0b14;
      color: #fff;
    }

    .submit-row {
      margin-top: 22px;
      display: flex;
      justify-content: center;
    }

    .booking-btn {
      border-radius: 14px;
      padding: 12px 18px;
      font-weight: 800;
    }

    /* Snack (3 knoppen) */
    .segmented {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
    }

    .seg-btn {
      height: 42px;
      padding: 0 16px;
      border-radius: 14px;
      border: 1px solid rgba(255, 255, 255, 0.12);
      background: rgba(255, 255, 255, 0.06);
      color: rgba(255, 255, 255, .95);
      font-weight: 900;
      cursor: pointer;
    }

    .seg-btn.active {
      background: rgba(255, 47, 220, 0.22);
      border-color: rgba(255, 47, 220, 0.55);
    }

    /* Nee/Ja toggle */
    .toggle-2 {
      display: flex;
      gap: 10px;
    }

    .toggle-btn {
      width: 64px;
      height: 42px;
      border-radius: 14px;
      border: 1px solid rgba(255, 255, 255, 0.12);
      background: rgba(255, 255, 255, 0.06);
      color: rgba(255, 255, 255, .95);
      font-weight: 900;
      cursor: pointer;
    }

    .toggle-btn.active {
      background: rgba(255, 47, 220, 0.22);
      border-color: rgba(255, 47, 220, 0.55);
    }

    /* FORCE: ALLE TEKST WIT IN DE RESERVEER SECTIE */
    .booking-page,
    .booking-page {
      color: #fff !important;
    }

    /* dropdown opties (anders blijven die soms zwart) */
    .booking-page select,
    .booking-page option {
      color: #fff !important;
    }

    .btn booking-btn {
      color: black !important;

    }

    .booking-page select option {
      background: #0b0b14 !important;
    }

    /* ===== JOUW BOWLING RESERVERING CSS (zoals gegeven) ===== */
    .booking-page {
      padding: 80px 16px;
      background: #000;
      position: relative;
    }

    .booking-page::before {
      content: "";
      position: absolute;
      inset: 0;
      background:
        radial-gradient(700px 380px at 20% 20%, rgba(255, 47, 220, .18), transparent 60%),
        radial-gradient(680px 360px at 80% 30%, rgba(140, 60, 255, .14), transparent 60%),
        radial-gradient(800px 420px at 50% 100%, rgba(255, 47, 220, .10), transparent 65%);
      pointer-events: none;
    }

    .booking-page .container {
      max-width: 1000px;
      margin: 0 auto;
      position: relative;
    }

    .booking-card {
      background: rgba(20, 20, 40, 0.60);
      backdrop-filter: blur(12px);
      border: 1px solid rgba(255, 255, 255, 0.10);
      box-shadow: 0 10px 40px rgba(0, 0, 0, .65);
      border-radius: 18px;
      padding: 22px;
    }

    /* ... (alle overige CSS van je reserveringsform) ... */

    /* ===== MOBILE MENU (slide-in) ===== */
    .mobile-menu {
      position: fixed;
      top: 0;
      right: -260px;
      width: 260px;
      height: 100%;
      background: rgba(20, 20, 40, 0.65);
      backdrop-filter: blur(12px);
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.6);
      transition: right 0.35s ease;
      z-index: 9998;
      font-family: 'Poppins', Arial, sans-serif;
    }

    .mobile-menu a.active {
      color: #ff2fdc;
      transform: scale(1.05);
    }

    .mobile-menu a {
      display: inline-block;
    }

    .mobile-menu.open {
      right: 0;
    }

    .mobile-menu ul {
      list-style: none;
      padding: 100px 0 0;
      text-align: center;
    }

    .mobile-menu li {
      margin: 30px 0;
    }

    .mobile-menu a {
      text-decoration: none;
      color: rgba(255, 255, 255, 0.95);
      font-size: 26px;
      font-weight: 700;
      letter-spacing: 1.5px;
      text-shadow: 0 0 12px rgba(255, 47, 220, 0.4);
      transition: color 0.3s, transform 0.3s;
    }

    /* Hover-stijl */
    .mobile-menu a:hover {
      color: #ff2fdc;
      transform: scale(1.05);
    }

    /* Actieve pagina krijgt dezelfde stijl als hover */
    .mobile-menu a.active {
      color: #ff2fdc;
      transform: scale(1.05);

    }

    /* ===== DESKTOP MENU FIX ===== */
    @media (min-width: 1024px) {
      .menu-btn {
        display: none;
      }

      .mobile-menu {
        display: flex;
        position: fixed;
        top: 20px;
        right: 20px;
        width: auto;
        height: auto;
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(8px) brightness(0.7);
        padding: 12px 40px;
        border-radius: 50px;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
        flex-direction: row;
        gap: 50px;
        z-index: 9999;
        transition: none;
      }

      .mobile-menu.open {
        right: 20px;
      }

      .mobile-menu ul {
        display: flex;
        flex-direction: row;
        gap: 50px;
        padding: 0;
        margin: 0;
      }

      .mobile-menu li {
        margin: 0;
      }

      .mobile-menu a {
        font-size: 30px;
        color: rgba(255, 255, 255, 0.95);
      }
    }

.booking-alert {
  padding: 24px;
  margin: 24px 0;
  border-radius: 16px;
  text-align: center;
  font-weight: 600;
}

.booking-alert.error {
  background: #ff4444;
  color: white;
}

.booking-alert.success {
  background: #44ff44;
  color: black;
}

.conflict-item {
  margin: 10px 0;
  padding: 12px;
  background: rgba(255,255,255,0.2);
  border-radius: 8px;
}


  </style>
</head>

<body>

  <section class="booking-page" id="reserveer">
    <div class="container booking-card">
      <h2>Reserveer een bowlingbaan</h2>

      <form method="GET" id="bookingForm">
        <input type="hidden" name="m" id="mInput" value="<?= (int) $month ?>">
        <input type="hidden" name="y" id="yInput" value="<?= (int) $year ?>">

        <div class="cal-head">
          <a class="cal-nav" href="#" onclick="goMonth(<?= (int) $prevM ?>, <?= (int) $prevY ?>); return false;">‹</a>
          <div class="cal-title"><?= htmlspecialchars($nlMonths[$month]) ?> <?= (int) $year ?></div>
          <a class="cal-nav" href="#" onclick="goMonth(<?= (int) $nextM ?>, <?= (int) $nextY ?>); return false;">›</a>
        </div>

        <div class="weekdays">
          <div>ma</div>
          <div>di</div>
          <div>wo</div>
          <div>do</div>
          <div>vr</div>
          <div>za</div>
          <div>zo</div>
        </div>

        <div class="calendar" aria-label="Kalender">
          <?php for ($i = 0; $i < $firstDow; $i++): ?>
            <div class="day empty"></div>
          <?php endfor; ?>

          <?php for ($i = 1; $i <= $daysInMonth; $i++):
            $ts = strtotime(sprintf('%04d-%02d-%02d', $year, $month, $i));
            $dow = $nlDays[(int) date('N', $ts) - 1];
            ?>
            <button type="button" class="day <?= ($i == $day ? 'active' : '') ?>" data-day="<?= (int) $i ?>">
              <div class="dow"><?= htmlspecialchars($dow) ?></div>
              <div class="date"><?= (int) $i ?></div>
            </button>
          <?php endfor; ?>
        </div>

        <input type="hidden" name="day" id="dayInput" value="<?= (int) $day ?>">

        <div class="section">
          <b>Aantal personen</b>
          <div class="counter">
            <button type="button" onclick="changePersons(-1)">−</button>
            <input type="number" id="persons" name="persons" value="<?= (int) $persons ?>" min="1"
              max="<?= (int) $maxAdults ?>">
            <button type="button" onclick="changePersons(1)">+</button>
          </div>

        </div>

        <div class="section">
          <b>Aantal kinderen</b>
          <div class="counter">
            <button type="button" onclick="changeChildren(-1)">−</button>
            <input type="number" id="children" name="children" value="<?= (int) $children ?>" min="0"
              max="<?= (int) $maxChildren ?>">
            <button type="button" onclick="changeChildren(1)">+</button>
          </div>
        </div>

        <div class="section">
          <b>Baan</b>
          <div class="lanes" id="lanesUI">
            <?php foreach ($lanes as $l): ?>
              <div class="lane <?= in_array($l, $selectedLanes, true) ? 'active' : '' ?>" data-lane="<?= (int) $l ?>"
                onclick="toggleLane(<?= (int) $l ?>)">
                <?= (int) $l ?>
              </div>
            <?php endforeach; ?>
          </div>
        </div>

        <div id="lanesInputs">
          <?php foreach ($selectedLanes as $l): ?>
            <input type="hidden" name="lanes[]" value="<?= (int) $l ?>">
          <?php endforeach; ?>
        </div>

        <!-- 2 TIJDSELECTS -->
        <div class="section">
          <b>Starttijd</b>
          <div class="select-wrap">
            <select name="start_time" id="startTime">
              <?php foreach ($timeSlots as $t): ?>
                <option value="<?= htmlspecialchars($t) ?>" <?= ($t === $start_time ? 'selected' : '') ?>>
                  <?= htmlspecialchars($t) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="section">
          <b>Eindtijd</b>
          <div class="select-wrap">
            <select name="end_time" id="endTime"></select>
          </div>
        </div>

        <!-- EXTRA OPTIES -->
        <div class="section">
          <b>Snackpakket</b>
          <div class="segmented" id="snackSegment">
            <button type="button" class="seg-btn <?= $snackpakket === 'nee' ? 'active' : '' ?>" data-value="nee">Nee</button>
            <button type="button" class="seg-btn <?= $snackpakket === 'basis' ? 'active' : '' ?>"
              data-value="basis">Basis</button>
            <button type="button" class="seg-btn <?= $snackpakket === 'luxe' ? 'active' : '' ?>"
              data-value="luxe">Luxe</button>
          </div>
          <input type="hidden" name="snackpakket" id="snackpakket" value="<?= htmlspecialchars($snackpakket) ?>">
        </div>

        <div class="section">
          <b>Kinderpartij</b>
          <div class="toggle-2">
            <button type="button" class="toggle-btn <?= $kinderpartij === 'nee' ? 'active' : '' ?>" data-input="kinderpartij"
              data-value="nee">Nee</button>
            <button type="button" class="toggle-btn <?= $kinderpartij === 'ja' ? 'active' : '' ?>" data-input="kinderpartij"
              data-value="ja">Ja</button>
          </div>
          <input type="hidden" name="kinderpartij" id="kinderpartij" value="<?= htmlspecialchars($kinderpartij) ?>">
        </div>

        <div class="section">
          <b>Vrijgezellenfeest</b>
          <div class="toggle-2">
            <button type="button" class="toggle-btn <?= $vrijgezellenfeest === 'nee' ? 'active' : '' ?>"
              data-input="vrijgezellenfeest" data-value="nee">Nee</button>
            <button type="button" class="toggle-btn <?= $vrijgezellenfeest === 'ja' ? 'active' : '' ?>"
              data-input="vrijgezellenfeest" data-value="ja">Ja</button>
          </div>
          <input type="hidden" name="vrijgezellenfeest" id="vrijgezellenfeest"
            value="<?= htmlspecialchars($vrijgezellenfeest) ?>">
        </div>

        <div class="submit-row">
          <input type="hidden" name="action" value="submit">
          <button type="submit" class="btn booking-btn">Reserveren</button>
        </div>
      </form>
    </div>
  </section>

  <script>
    /* =======================
       HELPERS
    ======================= */
    function clamp(n, min, max) { return Math.max(min, Math.min(max, n)); }

    /* =======================
       PERSONS / CHILDREN / LANES
    ======================= */
    let AUTO_LANE_LOCK = false;
    let MANUAL_LANES = 0;       // 0 = geen manual override
    let LAST_AUTO_LANES = 1;    // laatste auto nodig

    const personsEl = document.getElementById('persons');
    const childrenEl = document.getElementById('children');
    const lanesWrap = document.getElementById('lanesInputs');

    function getSelectedLanes() {
      const vals = [];
      lanesWrap.querySelectorAll('input[name="lanes[]"]').forEach(i => vals.push(parseInt(i.value, 10)));
      vals.sort((a, b) => a - b);
      return vals;
    }

    function setLanes(n) {
      lanesWrap.innerHTML = '';
      for (let i = 1; i <= n; i++) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'lanes[]';
        input.value = String(i);
        lanesWrap.appendChild(input);
      }

      document.querySelectorAll('.booking-page .lane').forEach(el => {
        const laneNr = parseInt(el.dataset.lane, 10);
        el.classList.toggle('active', laneNr <= n);
      });
    }

    function getAutoNeededLanes() {
      const maxAdultsPerLane = 8;
      const maxKidsPerLane = 4;

      const p = Math.max(1, parseInt(personsEl.value || "1", 10));
      const k = clamp(parseInt(childrenEl.value || "0", 10), 0, 32);

      for (let L = 1; L <= 8; L++) {
        const maxKidsL = L * maxKidsPerLane;
        const penalty = Math.ceil(k / 2); // jouw tabel
        const maxAdultsL = (L * maxAdultsPerLane) - penalty;

        if (k <= maxKidsL && p <= maxAdultsL) return L;
      }
      return 8;
    }

    function computeLimits() {
      const laneCount = Math.max(1, getSelectedLanes().length);

      const maxAdultsPerLane = 8;
      const maxKidsPerLane = 4;

      const maxKids = Math.min(32, laneCount * maxKidsPerLane);

      const kids = clamp(parseInt(childrenEl.value || "0", 10), 0, maxKids);
      const penalty = Math.ceil(kids / 2);

      let maxAdults = (laneCount * maxAdultsPerLane) - penalty;
      maxAdults = Math.max(1, Math.min(64, maxAdults));

      return { laneCount, maxAdults, maxKids };
    }

    function ensureLanes() {
      const needed = getAutoNeededLanes();
      LAST_AUTO_LANES = needed;

      const target = clamp(Math.max(needed, MANUAL_LANES || 0), 1, 8);
      const current = Math.max(1, getSelectedLanes().length);

      if (target !== current) {
        AUTO_LANE_LOCK = true;
        setLanes(target);
        AUTO_LANE_LOCK = false;
      }

      // manual reset als het niet meer "extra" is
      if (MANUAL_LANES && MANUAL_LANES <= needed) MANUAL_LANES = 0;
    }

    function applyLimits() {
      ensureLanes();

      const info = computeLimits();

      personsEl.max = String(info.maxAdults);
      childrenEl.max = String(info.maxKids);

      const p = parseInt(personsEl.value || "1", 10);
      const k = parseInt(childrenEl.value || "0", 10);

      childrenEl.value = String(clamp(k, 0, info.maxKids));
      personsEl.value = String(clamp(p, 1, info.maxAdults));
    }

    function toggleLane(n) {
      const current = Math.max(1, getSelectedLanes().length);

      // opnieuw klikken op huidige "max lane" -> 1 eraf (maar niet onder auto)
      if (n === current && n > LAST_AUTO_LANES) {
        const next = Math.max(LAST_AUTO_LANES, n - 1);
        MANUAL_LANES = (next === LAST_AUTO_LANES) ? 0 : next;
      } else {
        MANUAL_LANES = n;
        if (MANUAL_LANES <= LAST_AUTO_LANES) MANUAL_LANES = 0;
      }

      if (!AUTO_LANE_LOCK) applyLimits();
    }
    window.toggleLane = toggleLane;

    window.changePersons = function (delta) {
      const cur = parseInt(personsEl.value || "1", 10);
      personsEl.value = String(Math.max(1, cur + delta));
      applyLimits();
    };

    window.changeChildren = function (delta) {
      const cur = parseInt(childrenEl.value || "0", 10);
      childrenEl.value = String(Math.max(0, cur + delta));
      applyLimits();
    };

    /* =======================
       KALENDER DAG SELECT
    ======================= */
    const calendarEl = document.querySelector('.booking-page .calendar');
    if (calendarEl) {
      calendarEl.addEventListener('click', (e) => {
        const btn = e.target.closest('button.day');
        if (!btn || btn.classList.contains('empty')) return;

        const d = parseInt(btn.dataset.day, 10);
        if (!d) return;

        document.getElementById('dayInput').value = d;

        document.querySelectorAll('.booking-page button.day').forEach(x => x.classList.remove('active'));
        btn.classList.add('active');
      });
    }

    /* =======================
       MAAND NAVIGATIE
    ======================= */
    window.goMonth = function (m, y) {
      const params = new URLSearchParams();

      params.set('m', String(m));
      params.set('y', String(y));

      params.set('day', document.getElementById('dayInput').value || '1');
      params.set('persons', personsEl.value || '1');
      params.set('children', childrenEl.value || '0');

      // tijden mee
      const startTime = document.getElementById('startTime');
      const endTime = document.getElementById('endTime');
      if (startTime) params.set('start_time', startTime.value || '0');
      if (endTime) params.set('end_time', endTime.value || '1');

      // lanes mee
      getSelectedLanes().forEach(l => params.append('lanes[]', String(l)));

      // extra opties mee
      const snack = document.getElementById('snackpakket');
      const kidp = document.getElementById('kinderpartij');
      const vrij = document.getElementById('vrijgezellenfeest');
      if (snack) params.set('snackpakket', snack.value || 'nee');
      if (kidp) params.set('kinderpartij', kidp.value || 'nee');
      if (vrij) params.set('vrijgezellenfeest', vrij.value || 'nee');

      window.location.href = window.location.pathname + '?' + params.toString() + '#reserveer';
    };

    /* =======================
       SNACK BUTTONS
    ======================= */
    const snackSegment = document.getElementById('snackSegment');
    const snackInput = document.getElementById('snackpakket');

    if (snackSegment && snackInput) {
      snackSegment.addEventListener('click', (e) => {
        const btn = e.target.closest('.seg-btn');
        if (!btn) return;

        snackSegment.querySelectorAll('.seg-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        snackInput.value = btn.dataset.value;
      });
    }

    /* =======================
       NEE/JA TOGGLES
    ======================= */
    document.querySelectorAll('.toggle-2').forEach(group => {
      const buttons = group.querySelectorAll('.toggle-btn');

      buttons.forEach(btn => {
        btn.addEventListener('click', () => {
          const inputId = btn.dataset.input;
          const value = btn.dataset.value;
          const hiddenInput = document.getElementById(inputId);

          buttons.forEach(b => b.classList.remove('active'));
          btn.classList.add('active');

          if (hiddenInput) hiddenInput.value = value;
        });
      });
    });

    /* =======================
       2 TIJDSELECTS: END = NA START
    ======================= */
    const timeSlots = <?= json_encode($timeSlots) ?>;
    const startSelect = document.getElementById('startTime');
    const endSelect = document.getElementById('endTime');
    const savedEnd = <?= json_encode($end_time) ?>;

    function updateEndTimes() {
      if (!startSelect || !endSelect) return;

      const startIndex = startSelect.selectedIndex;
      const prev = endSelect.value || savedEnd;

      endSelect.innerHTML = '';

      for (let i = startIndex + 1; i < timeSlots.length; i++) {
        const opt = document.createElement('option');
        opt.value = timeSlots[i];
        opt.textContent = timeSlots[i];
        endSelect.appendChild(opt);
      }

      if (prev) {
        const found = Array.from(endSelect.options).some(o => o.value === prev);
        if (found) endSelect.value = prev;
      }

      if (!endSelect.value && endSelect.options.length) {
        endSelect.selectedIndex = 0;
      }
    }

    if (startSelect) startSelect.addEventListener('change', updateEndTimes);

    /* =======================
       INIT
    ======================= */
    applyLimits();
    updateEndTimes();

    personsEl.addEventListener('input', applyLimits);
    childrenEl.addEventListener('input', applyLimits);
  </script>



</body>

</html>