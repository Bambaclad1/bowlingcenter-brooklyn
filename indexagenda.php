<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

date_default_timezone_set('Europe/Amsterdam');

// Tijdslots (voeg hier jouw gewenste tijden toe)
$timeSlots = [
  "10:00",
  "11:00",
  "12:00",
  "13:00",
  "14:00",
  "15:00",
  "16:00",
  "17:00",
  "18:00",
  "19:00",
  "20:00",
  "21:00"
];

// Gebruik POST als die er is, anders GET (voor maandnavigatie)
$src = !empty($_POST) ? $_POST : $_GET;

// ====== Kalender: maand/jaar via GET/POST ======
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

$year  = isset($src['y']) ? (int)$src['y'] : (int)date('Y');
$month = isset($src['m']) ? (int)$src['m'] : (int)date('n');

if ($month < 1) {
  $month = 12;
  $year--;
}
if ($month > 12) {
  $month = 1;
  $year++;
}
if ($year < 1970) $year = 1970;
if ($year > 2100) $year = 2100;

$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

// 0=ma ... 6=zo
$firstDow = (int)date('N', strtotime(sprintf('%04d-%02d-01', $year, $month))) - 1;

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
$day      = isset($src['day']) ? (int)$src['day'] : (int)date('j');
$persons  = isset($src['persons']) ? (int)$src['persons'] : 4;
$children = isset($src['children']) ? (int)$src['children'] : 1;
$time     = isset($src['time']) ? (string)$src['time'] : $timeSlots[0];

if ($day < 1) $day = 1;
if ($day > $daysInMonth) $day = $daysInMonth;

$selectedLanes = (isset($src['lanes']) && is_array($src['lanes'])) ? $src['lanes'] : array(2, 3);
$selectedLanes = array_values(array_unique(array_map('intval', $selectedLanes)));
$selectedLanes = array_values(array_filter($selectedLanes, function ($v) {
  return $v >= 1 && $v <= 8;
}));

$laneCount = max(1, count($selectedLanes));

// Per baan: 6 volwassenen + 4 kinderen
$maxAdults   = $laneCount * 6;
$maxChildren = $laneCount * 4;

// Clamp
$persons  = max(1, min($maxAdults, $persons));
$children = max(0, min($maxChildren, $children));
?>
<!DOCTYPE html>
<html lang="nl">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Bowling Center Brooklyn</title>

  <!-- JOUW bestaande site CSS -->
  <link rel="stylesheet" href="style.css">

  <!-- Booking CSS apart -->
  <link rel="stylesheet" href="booking.css">
</head>

<body>

  <!-- Header met hamburger -->
  <header class="header">
    <button class="menu-btn" id="menu-btn">☰</button>
  </header>

  <!-- Slide-out menu -->
  <nav class="mobile-menu" id="mobile-menu">
    <ul>
      <li><a href="#">Over ons</a></li>
      <li><a href="#">Agenda</a></li>
      <li><a href="#">Prijzen</a></li>
      <li><a href="#">Login</a></li>
    </ul>
  </nav>

  <!-- Hero sectie -->
  <section class="hero">
    <div class="hero-content">
      <div class="logo-circle">
        <img src="afbeeldingen/bowling_logo.png" alt="Bowling Center Brooklyn">
      </div>

      <h1>Bowling Center Brooklyn</h1>
      <p class="hero-subtitle">Waar plezier altijd scoort</p>

      <a href="#reserveer" class="btn">Reserveer nu</a>
    </div>
  </section>

  <!-- ===== Reserveer sectie ===== -->
  <section class="booking-page" id="reserveer">
    <div class="container booking-card">
      <h2>Reserveer een bowlingbaan</h2>

      <form method="post" id="bookingForm">
        <!-- Maand/jaar meegeven in POST, zodat submit dezelfde maand behoudt -->
        <input type="hidden" name="m" id="mInput" value="<?= (int)$month ?>">
        <input type="hidden" name="y" id="yInput" value="<?= (int)$year ?>">

        <!-- Kalender header -->
        <div class="cal-head">
          <a class="cal-nav" href="#" onclick="goMonth(<?= (int)$prevM ?>, <?= (int)$prevY ?>); return false;">‹</a>
          <div class="cal-title"><?= htmlspecialchars($nlMonths[$month]) ?> <?= (int)$year ?></div>
          <a class="cal-nav" href="#" onclick="goMonth(<?= (int)$nextM ?>, <?= (int)$nextY ?>); return false;">›</a>
        </div>

        <!-- Weekdagen -->
        <div class="weekdays">
          <div>ma</div>
          <div>di</div>
          <div>wo</div>
          <div>do</div>
          <div>vr</div>
          <div>za</div>
          <div>zo</div>
        </div>

        <!-- Kalender grid -->
        <div class="calendar" aria-label="Kalender">
          <?php for ($i = 0; $i < $firstDow; $i++): ?>
            <div class="day empty"></div>
          <?php endfor; ?>

          <?php for ($i = 1; $i <= $daysInMonth; $i++):
            $ts = strtotime(sprintf('%04d-%02d-%02d', $year, $month, $i));
            $dow = $nlDays[(int)date('N', $ts) - 1];
          ?>
            <button type="button"
              class="day <?= ($i == $day ? 'active' : '') ?>"
              data-day="<?= (int)$i ?>">
              <div class="dow"><?= htmlspecialchars($dow) ?></div>
              <div class="date"><?= (int)$i ?></div>
            </button>
          <?php endfor; ?>
        </div>

        <input type="hidden" name="day" id="dayInput" value="<?= (int)$day ?>">

        <div class="section">
          <b>Aantal personen</b>
          <div class="counter">
            <button type="button" onclick="changePersons(-1)">−</button>
            <input type="number" id="persons" name="persons"
              value="<?= (int)$persons ?>" min="1" max="<?= (int)$maxAdults ?>">
            <button type="button" onclick="changePersons(1)">+</button>
          </div>
          <small id="personsHelp">
            Maximaal <span id="maxAdultsText"><?= (int)$maxAdults ?></span> personen;
            (<span id="laneCountText"><?= (int)$laneCount ?></span> baan/banen)
          </small>
        </div>

        <div class="section">
          <b>Aantal kinderen</b>
          <div class="counter">
            <button type="button" onclick="changeChildren(-1)">−</button>
            <input type="number" id="children" name="children"
              value="<?= (int)$children ?>" min="0" max="<?= (int)$maxChildren ?>">
            <button type="button" onclick="changeChildren(1)">+</button>
          </div>
          <small>Regel: bij 4 kinderen wordt het aantal personen max 6.</small>
        </div>

        <div class="section">
          <b>Baan</b>
          <div class="lanes" id="lanesUI">
            <?php foreach ($lanes as $l): ?>
              <div class="lane <?= in_array($l, $selectedLanes, true) ? 'active' : '' ?>"
                data-lane="<?= (int)$l ?>"
                onclick="toggleLane(<?= (int)$l ?>)">
                <?= (int)$l ?>
              </div>
            <?php endforeach; ?>
          </div>
        </div>

        <div id="lanesInputs">
          <?php foreach ($selectedLanes as $l): ?>
            <input type="hidden" name="lanes[]" value="<?= (int)$l ?>">
          <?php endforeach; ?>
        </div>

        <div class="section">
          <b>Tijd</b>
          <div class="select-wrap">
            <select name="time" id="timeSelect">
              <?php foreach ($timeSlots as $t): ?>
                <option value="<?= htmlspecialchars($t) ?>" <?= ($t === $time ? 'selected' : '') ?>>
                  <?= htmlspecialchars($t) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="submit-row">
          <button type="submit" class="btn booking-btn">Reserveren</button>
        </div>

      </form>
    </div>
  </section>

  <!-- Booking JS apart -->
  <script src="booking.js"></script>
</body>

</html>