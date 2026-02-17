<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

date_default_timezone_set('Europe/Amsterdam');
require_once 'Reservation.php';

$reservation = new Reservation();

date_default_timezone_set('Europe/Amsterdam');
$timeSlots = [
  "14:00","15:00","16:00","17:00","18:00","19:00","20:00","21:00","22:00","23:00","00:00"
];


$src = !empty($_POST) ? $_POST : $_GET;


$nlMonths = array(
  1=>"januari",2=>"februari",3=>"maart",4=>"april",5=>"mei",6=>"juni",
  7=>"juli",8=>"augustus",9=>"september",10=>"oktober",11=>"november",12=>"december"
);
$nlDays = array('ma','di','wo','do','vr','za','zo');

$year  = isset($src['y']) ? (int)$src['y'] : (int)date('Y');
$month = isset($src['m']) ? (int)$src['m'] : (int)date('n');

if ($month < 1) { $month = 12; $year--; }
if ($month > 12){ $month = 1;  $year++;  }
if ($year < 1970) $year = 1970;
if ($year > 2100) $year = 2100;


$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);


$firstDow = (int)date('N', strtotime(sprintf('%04d-%02d-01', $year, $month))) - 1;


$prevM = $month - 1; $prevY = $year;
$nextM = $month + 1; $nextY = $year;
if ($prevM < 1) { $prevM = 12; $prevY--; }
if ($nextM > 12){ $nextM = 1;  $nextY++;  }

$lanes = range(1, 8);


$day      = isset($src['day']) ? (int)$src['day'] : (int)date('j');
$persons  = isset($src['persons']) ? (int)$src['persons'] : 4;
$children = isset($src['children']) ? (int)$src['children'] : 1;
$time     = isset($src['time']) ? (string)$src['time'] : $timeSlots[0];

if ($day < 1) $day = 1;
if ($day > $daysInMonth) $day = $daysInMonth;

$selectedLanes = (isset($src['lanes']) && is_array($src['lanes'])) ? $src['lanes'] : array(1);
$selectedLanes = array_values(array_unique(array_map('intval', $selectedLanes)));
$selectedLanes = array_values(array_filter($selectedLanes, function($v){
  return $v >= 1 && $v <= 8;
}));

$laneCount = max(1, count($selectedLanes));


$maxAdults   = $laneCount * 6;
$maxChildren = $laneCount * 4;


$persons  = max(1, min($maxAdults, $persons));
$children = max(0, min($maxChildren, $children));

// Extra opties (defaults)
$snackpakket = isset($src['snackpakket']) ? (string)$src['snackpakket'] : 'nee'; // nee|basis|luxe
if (!in_array($snackpakket, ['nee','basis','luxe'], true)) $snackpakket = 'nee';

$kinderpartij = isset($src['kinderpartij']) ? (string)$src['kinderpartij'] : 'nee'; // nee|ja
if (!in_array($kinderpartij, ['nee','ja'], true)) $kinderpartij = 'nee';

$vrijgezellenfeest = isset($src['vrijgezellenfeest']) ? (string)$src['vrijgezellenfeest'] : 'nee'; // nee|ja
if (!in_array($vrijgezellenfeest, ['nee','ja'], true)) $vrijgezellenfeest = 'nee';
?>
<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Bowling Center Brooklyn</title>


  <link rel="stylesheet" href="style.css">

  <style>
    /* (jouw bestaande booking CSS, ongewijzigd, maar zonder dubbele stukken) */
    .booking-page{ padding:80px 16px; background:#000; position:relative; }
    .booking-page::before{
      content:""; position:absolute; inset:0;
      background:
        radial-gradient(700px 380px at 20% 20%, rgba(255,47,220,.18), transparent 60%),
        radial-gradient(680px 360px at 80% 30%, rgba(140,60,255,.14), transparent 60%),
        radial-gradient(800px 420px at 50% 100%, rgba(255,47,220,.10), transparent 65%);
      pointer-events:none;
    }
    .booking-page .container{ max-width:1000px; margin:0 auto; position:relative; }
    .booking-card{
      background: rgba(20, 20, 40, 0.60);
      backdrop-filter: blur(12px);
      border: 1px solid rgba(255,255,255,0.10);
      box-shadow: 0 10px 40px rgba(0,0,0,.65);
      border-radius: 18px;
      padding: 22px;
    }
    .booking-page h2{ text-align:center; font-size:32px; font-weight:800; margin-bottom:18px; }

    .booking-page .cal-head{ display:flex; align-items:center; justify-content:space-between; gap:12px; margin:8px 0 12px; }
    .booking-page .cal-title{ font-weight:800; font-size:18px; text-transform:lowercase; }
    .booking-page .cal-nav{
      width:42px;height:42px;border-radius:14px;display:inline-flex;align-items:center;justify-content:center;
      text-decoration:none;cursor:pointer;color:rgba(255,255,255,.95);
      background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.10);
    }

    .booking-page .weekdays{ display:grid; grid-template-columns:repeat(7,1fr); gap:10px; margin-bottom:10px; text-align:center; font-weight:700; opacity:.85; }
    .booking-page .calendar{ display:grid; grid-template-columns:repeat(7,1fr); gap:10px; }
    .booking-page .day{
      min-height:58px;border-radius:14px;border:1px solid rgba(255,255,255,0.10);
      background:rgba(255,255,255,0.06);color:rgba(255,255,255,.95);
      cursor:pointer;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:2px;
    }
    .booking-page .day.empty{ background:transparent;border:1px dashed rgba(255,255,255,0.08);cursor:default;opacity:.35; }
    .booking-page .day.active{ background:rgba(255,47,220,0.22); border-color:rgba(255,47,220,0.55); }

    .booking-page .section{ margin-top:20px; }
    .booking-page b{ display:block; font-size:16px; font-weight:800; margin-bottom:8px; }
    .booking-page .counter{ display:flex; gap:10px; align-items:center; }
    .booking-page .counter button{
      width:42px;height:42px;border-radius:14px;border:1px solid rgba(255,255,255,0.12);
      background:rgba(255,255,255,0.06);color:rgba(255,255,255,.95);font-size:20px;font-weight:900;cursor:pointer;
    }
    .booking-page .counter input{
      width:96px;height:42px;text-align:center;border-radius:14px;border:1px solid rgba(255,255,255,0.12);
      background:rgba(0,0,0,0.35);color:rgba(255,255,255,.95);font-weight:900;font-size:16px;outline:none;
    }

    .booking-page .lanes{ display:flex; gap:10px; flex-wrap:wrap; }
    .booking-page .lane{
      width:42px;height:42px;border-radius:14px;border:1px solid rgba(255,255,255,0.12);
      background:rgba(255,255,255,0.06);display:flex;align-items:center;justify-content:center;font-weight:900;cursor:pointer;
    }
    .booking-page .lane.active{ background:rgba(255,47,220,0.22); border-color:rgba(255,47,220,0.55); }

    .booking-page .select-wrap{ position:relative; max-width:360px; }
    .booking-page .select-wrap select{
      width:100%;height:46px;border-radius:14px;border:1px solid rgba(0, 0, 0, 0.12);
      background:rgba(23, 20, 209, 0.06);color:rgba(255, 255, 255, 0.95);font-weight:900;padding:0 44px 0 14px;
      appearance:none;outline:none;cursor:pointer;
    }

    .booking-page .submit-row{ margin-top:22px; display:flex; justify-content:center; }
    .booking-page .booking-btn{ border-radius:14px; padding:12px 18px; font-weight:800; }

    /* Snack (3 knoppen) */
    .segmented{ display:flex; gap:10px; flex-wrap:wrap; }
    .seg-btn{
      height:42px;padding:0 16px;border-radius:14px;border:1px solid rgba(255,255,255,0.12);
      background:rgba(255,255,255,0.06);color:rgba(255,255,255,.95);font-weight:900;cursor:pointer;
    }
    .seg-btn.active{ background:rgba(255,47,220,0.22); border-color:rgba(255,47,220,0.55); }

    /* Nee/Ja toggle */
    .toggle-2{ display:flex; gap:10px; }
    .toggle-btn{
      width:64px;height:42px;border-radius:14px;border:1px solid rgba(255,255,255,0.12);
      background:rgba(255,255,255,0.06);color:rgba(255,255,255,.95);font-weight:900;cursor:pointer;
    }
    .toggle-btn.active{ background:rgba(255,47,220,0.22); border-color:rgba(255,47,220,0.55); }
  </style>
</head>




<body>


  <header class="header">
    <button class="menu-btn" id="menu-btn">☰</button>
  </header>


  <nav class="mobile-menu" id="mobile-menu">
    <ul>
      <li><a href="#">Over ons</a></li>
      <li><a href="#">Agenda</a></li>
      <li><a href="#">Prijzen</a></li>
      <li><a href="#">Login</a></li>
    </ul>
  </nav>


  <section class="booking-page" id="reserveer">
    <div class="container booking-card">
      <h2>Reserveer een bowlingbaan</h2>


     
     <form method="post" id="bookingForm">
        <input type="hidden" name="m" id="mInput" value="<?= $reservation->getMonth() ?>">
        <input type="hidden" name="y" id="yInput" value="<?= (int)$year ?>">


        <div class="cal-head">
          <a class="cal-nav" href="#" onclick="goMonth(<?= (int)$prevM ?>, <?= (int)$prevY ?>); return false;">‹</a>
          <div class="cal-title"><?= htmlspecialchars($nlMonths[$month]) ?> <?= (int)$year ?></div>
          <a class="cal-nav" href="#" onclick="goMonth(<?= (int)$nextM ?>, <?= (int)$nextY ?>); return false;">›</a>
        </div>


        <div class="weekdays">
          <div>ma</div><div>di</div><div>wo</div><div>do</div><div>vr</div><div>za</div><div>zo</div>
        </div>


        <div class="calendar" aria-label="Kalender">
          <?php for ($i=0; $i<$firstDow; $i++): ?>
            <div class="day empty"></div>
          <?php endfor; ?>

          <?php for($i=1;$i<=$daysInMonth;$i++):
            $ts = strtotime(sprintf('%04d-%02d-%02d', $year, $month, $i));
            $dow = $nlDays[(int)date('N', $ts)-1];
          ?>
            <button type="button" class="day <?= ($i==$day?'active':'') ?>" data-day="<?= (int)$i ?>">
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
            <input type="number" id="persons" name="persons" value="<?= (int)$persons ?>" min="1" max="<?= (int)$maxAdults ?>">
            <button type="button" onclick="changePersons(1)">+</button>
          </div>
          <small>
            Maximaal <span id="maxAdultsText"><?= (int)$maxAdults ?></span> personen;
            (<span id="laneCountText"><?= (int)$laneCount ?></span> baan/banen)
          </small>
        </div>

        <div class="section">
          <b>Aantal kinderen</b>
          <div class="counter">
            <button type="button" onclick="changeChildren(-1)">−</button>
            <input type="number" id="children" name="children" value="<?= (int)$children ?>" min="0" max="<?= (int)$maxChildren ?>">
            <button type="button" onclick="changeChildren(1)">+</button>
          </div>

        </div>

        <div class="section">
          <b>Baan</b>
          <div class="lanes" id="lanesUI">
            <?php foreach($lanes as $l): ?>
              <div class="lane <?= in_array($l,$selectedLanes,true)?'active':'' ?>"
                   data-lane="<?= (int)$l ?>"
                   onclick="toggleLane(<?= (int)$l ?>)">
                <?= (int)$l ?>
              </div>
            <?php endforeach; ?>
          </div>
        </div>

        <div id="lanesInputs">
          <?php foreach($selectedLanes as $l): ?>
            <input type="hidden" name="lanes[]" value="<?= (int)$l ?>">
          <?php endforeach; ?>
        </div>

<div class="section">
  <b>Starttijd</b>
  <div class="select-wrap">
    <select name="start_time" id="startTime">
      <?php foreach($timeSlots as $t): ?>
        <option value="<?= htmlspecialchars($t) ?>">
          <?= htmlspecialchars($t) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>
</div>

<div class="section">
  <b>Eindtijd</b>
  <div class="select-wrap">
    <select name="end_time" id="endTime">
      <!-- wordt automatisch gevuld -->
    </select>
  </div>
</div>


        <!-- EXTRA OPTIES -->
        <div class="section">
          <b>Snackpakket</b>
          <div class="segmented" id="snackSegment">
            <button type="button" class="seg-btn <?= $snackpakket==='nee'?'active':'' ?>" data-value="nee">Nee</button>
            <button type="button" class="seg-btn <?= $snackpakket==='basis'?'active':'' ?>" data-value="basis">Basis</button>
            <button type="button" class="seg-btn <?= $snackpakket==='luxe'?'active':'' ?>" data-value="luxe">Luxe</button>
          </div>
          <small>: <span id="snackLabel"><?= htmlspecialchars(ucfirst($snackpakket)) ?></span></small>
          <input type="hidden" name="snackpakket" id="snackpakket" value="<?= htmlspecialchars($snackpakket) ?>">
        </div>

        <div class="section">
          <b>Kinderpartij</b>
          <div class="toggle-2">
            <button type="button" class="toggle-btn <?= $kinderpartij==='nee'?'active':'' ?>" data-input="kinderpartij" data-value="nee">Nee</button>
            <button type="button" class="toggle-btn <?= $kinderpartij==='ja'?'active':'' ?>" data-input="kinderpartij" data-value="ja">Ja</button>
          </div>
          <input type="hidden" name="kinderpartij" id="kinderpartij" value="<?= htmlspecialchars($kinderpartij) ?>">
        </div>

        <div class="section">
          <b>Vrijgezellenfeest</b>
          <div class="toggle-2">
            <button type="button" class="toggle-btn <?= $vrijgezellenfeest==='nee'?'active':'' ?>" data-input="vrijgezellenfeest" data-value="nee">Nee</button>
            <button type="button" class="toggle-btn <?= $vrijgezellenfeest==='ja'?'active':'' ?>" data-input="vrijgezellenfeest" data-value="ja">Ja</button>
          </div>
          <input type="hidden" name="vrijgezellenfeest" id="vrijgezellenfeest" value="<?= htmlspecialchars($vrijgezellenfeest) ?>">
        </div>

        <div class="submit-row">
          <button type="submit" class="btn booking-btn">Reserveren</button>
        </div>

      </form>
    </div>
  </section>

  <script>
    // hamburger menu
    const menuBtn = document.getElementById('menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    if (menuBtn && mobileMenu) menuBtn.addEventListener('click', () => mobileMenu.classList.toggle('open'));

    const personsEl = document.getElementById('persons');
    const childrenEl = document.getElementById('children');
    const lanesWrap = document.getElementById('lanesInputs');

    function clamp(n, min, max){ return Math.max(min, Math.min(max, n)); }

    function getSelectedLanes(){
      const vals = [];
      lanesWrap.querySelectorAll('input[name="lanes[]"]').forEach(i => vals.push(parseInt(i.value,10)));
      vals.sort((a,b)=>a-b);
      return vals;
    }

    function computeLimits(){
      const laneCount = Math.max(1, getSelectedLanes().length);
      return { laneCount, maxAdults: laneCount*6, maxKids: laneCount*4 };
    }

    function applyLimits(){
      const info = computeLimits();
      personsEl.max = String(info.maxAdults);
      childrenEl.max = String(info.maxKids);

      personsEl.value  = String(clamp(parseInt(personsEl.value||"1",10), 1, info.maxAdults));
      childrenEl.value = String(clamp(parseInt(childrenEl.value||"0",10), 0, info.maxKids));

      const maxAdultsText = document.getElementById('maxAdultsText');
      const laneCountText = document.getElementById('laneCountText');
      if (maxAdultsText) maxAdultsText.textContent = String(info.maxAdults);
      if (laneCountText) laneCountText.textContent = String(info.laneCount);
    }

    function toggleLane(n){
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
      applyLimits();
    }

    window.toggleLane = toggleLane;

    window.changePersons = function(delta){
      applyLimits();
      personsEl.value = String(clamp(parseInt(personsEl.value||"1",10)+delta, 1, parseInt(personsEl.max||"6",10)));
    };

    window.changeChildren = function(delta){
      applyLimits();
      childrenEl.value = String(clamp(parseInt(childrenEl.value||"0",10)+delta, 0, parseInt(childrenEl.max||"4",10)));
    };

    // kalender dag selecteren
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

    // maand navigatie
    window.goMonth = function(m, y){
      const params = new URLSearchParams();
      params.set('m', String(m));
      params.set('y', String(y));
      params.set('day', document.getElementById('dayInput').value || '1');
      params.set('persons', personsEl.value || '1');
      params.set('children', childrenEl.value || '0');

      const timeSelect = document.getElementById('timeSelect');
      if (timeSelect) params.set('time', timeSelect.value);

      getSelectedLanes().forEach(l => params.append('lanes[]', String(l)));

      // extra opties meenemen
      params.set('snackpakket', document.getElementById('snackpakket').value || 'nee');
      params.set('kinderpartij', document.getElementById('kinderpartij').value || 'nee');
      params.set('vrijgezellenfeest', document.getElementById('vrijgezellenfeest').value || 'nee');

      window.location.href = window.location.pathname + '?' + params.toString() + '#reserveer';
    };

    // snackpakket (3 knoppen)
    const snackSegment = document.getElementById('snackSegment');
    const snackInput = document.getElementById('snackpakket');
    const snackLabel = document.getElementById('snackLabel');
    if (snackSegment && snackInput && snackLabel) {
      snackSegment.addEventListener('click', (e) => {
        const btn = e.target.closest('.seg-btn');
        if (!btn) return;
        snackSegment.querySelectorAll('.seg-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        snackInput.value = btn.dataset.value;
        snackLabel.textContent = btn.textContent;
      });
    }

    // nee/ja toggles
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

    // init
    applyLimits();

    const timeSlots = <?= json_encode($timeSlots) ?>;
const startSelect = document.getElementById('startTime');
const endSelect   = document.getElementById('endTime');

function updateEndTimes() {
  const startIndex = startSelect.selectedIndex;

  endSelect.innerHTML = '';

  for (let i = startIndex + 1; i < timeSlots.length; i++) {
    const opt = document.createElement('option');
    opt.value = timeSlots[i];
    opt.textContent = timeSlots[i];
    endSelect.appendChild(opt);
  }
}

// init
updateEndTimes();

// update bij wijziging starttijd
startSelect.addEventListener('change', updateEndTimes);

  </script>

</body>
</html>
