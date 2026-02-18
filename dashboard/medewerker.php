<!DOCTYPE html>
<?php
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] === 'user') {
  header("Location: ../index.php");
}

?>

<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="dashboard.css">
  <link rel="stylesheet" href="agenda.css">
  <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.20/index.global.min.js'></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      var calendarEl = document.getElementById('calendar');

      var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        initialDate: '2026-02-07',
        headerToolbar: {
          left: 'prev,next today',
          center: 'title',
          right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: [
          {
            title: 'All Day Event',
            start: '2026-02-01'
          },
          {
            title: 'Long Event',
            start: '2026-02-07',
            end: '2026-02-10'
          },
          {
            groupId: '999',
            title: 'Repeating Event',
            start: '2026-02-09T16:00:00'
          },
          {
            groupId: '999',
            title: 'Repeating Event',
            start: '2026-02-16T16:00:00'
          },
          {
            title: 'Conference',
            start: '2026-02-11',
            end: '2026-02-13'
          },
          {
            title: 'Meeting',
            start: '2026-02-12T10:30:00',
            end: '2026-02-12T12:30:00'
          },
          {
            title: 'Lunch',
            start: '2026-02-12T12:00:00'
          },
          {
            title: 'Meeting',
            start: '2026-02-12T14:30:00'
          },
          {
            title: 'Birthday Party',
            start: '2026-02-13T07:00:00'
          },
          {
            title: 'Click for Google',
            url: 'https://google.com/',
            start: '2026-02-28'
          }
        ]
      });

      calendar.render();
    });
  </script>
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
<div class="calendar-background">
  <div class="calendar-location">
    <div id="calendar"></div>
  </div>
</div>

    </div>
  </section>
</body>

</html>