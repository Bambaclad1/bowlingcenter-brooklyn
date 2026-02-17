//menu
const menuBtn = document.getElementById('menu-btn');
const mobileMenu = document.getElementById('mobile-menu');
if (menuBtn && mobileMenu) {
  menuBtn.addEventListener('click', () => mobileMenu.classList.toggle('open'));
}

// ===== Booking logic =====
const personsEl = document.getElementById('persons');
const childrenEl = document.getElementById('children');
const lanesWrap = document.getElementById('lanesInputs');

function clamp(n, min, max){
  return Math.max(min, Math.min(max, n));
}

function getSelectedLanes(){
  const vals = [];
  lanesWrap.querySelectorAll('input[name="lanes[]"]').forEach(i => vals.push(parseInt(i.value,10)));
  vals.sort((a,b)=>a-b);
  return vals;
}

function computeLimits(){
  const laneCount = Math.max(1, getSelectedLanes().length);
  const maxAdults = laneCount * 6;   // volwassenen
  const maxKids   = laneCount * 4;   // kinderen
  const maxTotal  = laneCount * 10;  // totaal (optioneel)
  return { laneCount, maxAdults, maxKids, maxTotal };
}

function applyLimits(){
  const info = computeLimits();

  // update input maxima
  personsEl.max = String(info.maxAdults);
  childrenEl.max = String(info.maxKids);

  // huidige waarden clampen
  const p = parseInt(personsEl.value || "1", 10);
  const k = parseInt(childrenEl.value || "0", 10);
  personsEl.value  = String(clamp(p, 1, info.maxAdults));
  childrenEl.value = String(clamp(k, 0, info.maxKids));

  // update tekstjes
  const maxAdultsText = document.getElementById('maxAdultsText');
  const laneCountText = document.getElementById('laneCountText');
  if (maxAdultsText) maxAdultsText.textContent = String(info.maxAdults);
  if (laneCountText) laneCountText.textContent = String(info.laneCount);
}

// Selecteer automatisch banen 1..n
function toggleLane(n){
  // 1) nieuwe hidden inputs
  lanesWrap.innerHTML = '';
  for (let i = 1; i <= n; i++) {
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'lanes[]';
    input.value = String(i);
    lanesWrap.appendChild(input);
  }

  // 2) active classes
  document.querySelectorAll('.booking-page .lane').forEach(el => {
    const laneNr = parseInt(el.dataset.lane, 10);
    if (laneNr <= n) el.classList.add('active');
    else el.classList.remove('active');
  });

  // 3) limieten herberekenen
  applyLimits();
}

function changePersons(delta){
  applyLimits();
  const cur = parseInt(personsEl.value || "1", 10);
  const max = parseInt(personsEl.max || "6", 10);
  personsEl.value = String(clamp(cur + delta, 1, max));
}

function changeChildren(delta){
  applyLimits();
  const cur = parseInt(childrenEl.value || "0", 10);
  const max = parseInt(childrenEl.max || "4", 10);
  childrenEl.value = String(clamp(cur + delta, 0, max));
}

// Maand navigatie met behoud van keuzes
function goMonth(m, y){
  const params = new URLSearchParams();
  params.set('m', String(m));
  params.set('y', String(y));

  params.set('day', document.getElementById('dayInput').value || '1');
  params.set('persons', personsEl.value || '1');
  params.set('children', childrenEl.value || '0');

  const timeSelect = document.getElementById('timeSelect');
  if (timeSelect) params.set('time', timeSelect.value);

  getSelectedLanes().forEach(l => params.append('lanes[]', String(l)));

  window.location.href = window.location.pathname + '?' + params.toString() + '#reserveer';
}

// Kalender click (dag selecteren)
const calendarEl = document.querySelector('.booking-page .calendar');
if (calendarEl) {
  calendarEl.addEventListener('click', (e) => {
    const btn = e.target.closest('button.day');
    if (!btn) return;
    if (btn.classList.contains('empty')) return;

    const d = parseInt(btn.dataset.day, 10);
    if (!d) return;

    document.getElementById('dayInput').value = d;

    document.querySelectorAll('.booking-page button.day').forEach(x => x.classList.remove('active'));
    btn.classList.add('active');
  });
}

// init
applyLimits();
