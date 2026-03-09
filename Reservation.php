<?php

class Reservation
{
    public array $src;

    // kalender
    public int $year;
    public int $month;
    public int $daysInMonth;
    public int $firstDow; // 0=ma ... 6=zo
    public int $prevM;
    public int $prevY;
    public int $nextM;
    public int $nextY;

    public array $nlMonths = [
        1 => 'januari',
        2 => 'februari',
        3 => 'maart',
        4 => 'april',
        5 => 'mei',
        6 => 'juni',
        7 => 'juli',
        8 => 'augustus',
        9 => 'september',
        10 => 'oktober',
        11 => 'november',
        12 => 'december',
    ];
    public array $nlDays = ['ma', 'di', 'wo', 'do', 'vr', 'za', 'zo'];

    // tijden
    public array $timeSlots = [
        '10:00',
        '11:00',
        '12:00',
        '13:00',
        '14:00',
        '15:00',
        '16:00',
        '17:00',
        '18:00',
        '19:00',
        '20:00',
        '21:00',
    ];

    // form waarden
    public int $day;
    public int $persons;
    public int $children;

    // banen
    public array $lanes = [];
    public array $selectedLanes = [];
    public int $laneCount = 1;

    public int $maxAdults = 6;
    public int $maxChildren = 4;

    // extra opties
    public string $snackpakket = 'nee';       // nee|basis|luxe
    public string $kinderpartij = 'nee';      // nee|ja
    public string $vrijgezellenfeest = 'nee'; // nee|ja

    // tijd selectie (2 tabellen)
    public string $startTime = '';
    public string $endTime = '';

    public function __construct()
    {
        date_default_timezone_set('Europe/Amsterdam');

        $this->src = !empty($_POST) ? $_POST : $_GET;

        $this->initCalendar();
        $this->initFormValues();
        $this->initExtras();
    }

    private function initCalendar(): void
    {
        $this->year = isset($this->src['y']) ? (int) $this->src['y'] : (int) date('Y');
        $this->month = isset($this->src['m']) ? (int) $this->src['m'] : (int) date('n');

        if ($this->month < 1) {
            $this->month = 12;
            --$this->year;
        }
        if ($this->month > 12) {
            $this->month = 1;
            ++$this->year;
        }

        if ($this->year < 1970) {
            $this->year = 1970;
        }
        if ($this->year > 2100) {
            $this->year = 2100;
        }

        $this->daysInMonth = cal_days_in_month(CAL_GREGORIAN, $this->month, $this->year);

        $this->firstDow = (int) date('N', strtotime(sprintf('%04d-%02d-01', $this->year, $this->month))) - 1;

        $this->prevM = $this->month - 1;
        $this->prevY = $this->year;
        $this->nextM = $this->month + 1;
        $this->nextY = $this->year;
        if ($this->prevM < 1) {
            $this->prevM = 12;
            --$this->prevY;
        }
        if ($this->nextM > 12) {
            $this->nextM = 1;
            ++$this->nextY;
        }
    }

    private function initFormValues(): void
    {
        $this->lanes = range(1, 8);

        $this->day = isset($this->src['day']) ? (int) $this->src['day'] : 16;
        $this->persons = isset($this->src['persons']) ? (int) $this->src['persons'] : 4;
        $this->children = isset($this->src['children']) ? (int) $this->src['children'] : 1;

        if ($this->day < 1) {
            $this->day = 1;
        }
        if ($this->day > $this->daysInMonth) {
            $this->day = $this->daysInMonth;
        }

        $this->selectedLanes = (isset($this->src['lanes']) && is_array($this->src['lanes']))
            ? $this->src['lanes']
            : [1];

        $this->selectedLanes = array_values(array_unique(array_map('intval', $this->selectedLanes)));
        $this->selectedLanes = array_values(array_filter($this->selectedLanes, function ($v) {
            return $v >= 1 && $v <= 8;
        }));

        $this->laneCount = max(1, count($this->selectedLanes));

        $this->maxAdults = $this->laneCount * 6;
        $this->maxChildren = $this->laneCount * 4;

        $this->persons = max(1, min($this->maxAdults, $this->persons));
        $this->children = max(0, min($this->maxChildren, $this->children));

        // 2 tijd selects
        $this->startTime = isset($this->src['start_time']) ? (string) $this->src['start_time'] : $this->timeSlots[0];
        if (!in_array($this->startTime, $this->timeSlots, true)) {
            $this->startTime = $this->timeSlots[0];
        }

        $this->endTime = isset($this->src['end_time']) ? (string) $this->src['end_time'] : '';
        if ($this->endTime !== '' && !in_array($this->endTime, $this->timeSlots, true)) {
            $this->endTime = '';
        }
    }

    private function initExtras(): void
    {
        $sp = isset($this->src['snackpakket']) ? (string) $this->src['snackpakket'] : 'nee';
        if (!in_array($sp, ['nee', 'basis', 'luxe'], true)) {
            $sp = 'nee';
        }
        $this->snackpakket = $sp;

        $kp = isset($this->src['kinderpartij']) ? (string) $this->src['kinderpartij'] : 'nee';
        if (!in_array($kp, ['nee', 'ja'], true)) {
            $kp = 'nee';
        }
        $this->kinderpartij = $kp;

        $vz = isset($this->src['vrijgezellenfeest']) ? (string) $this->src['vrijgezellenfeest'] : 'nee';
        if (!in_array($vz, ['nee', 'ja'], true)) {
            $vz = 'nee';
        }
        $this->vrijgezellenfeest = $vz;
    }

    public function dayName(int $day): string
    {
        $ts = strtotime(sprintf('%04d-%02d-%02d', $this->year, $this->month, $day));

        return $this->nlDays[(int) date('N', $ts) - 1];
    }
}
