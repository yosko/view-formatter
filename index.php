<?php
ini_set('display_errors', true);
require_once "src/ViewFormatter.php";

Yosko\ViewFormatter::$config = [
    'locale' => 'en_US',
    'timezone' => 'America/New_York',
    'currency' => 'USD',
    'date_full' => 'MM-dd-yyyy',
    'date_partial' => 'MM-dd',
    'date_on' => 'on',
    'date_from' => 'from',
    'date_to' => 'to'
];

echo '<pre>';

$number = 123456.0987;
echo "\n\nYosko\ViewFormatter::formatNumber($number) -> " . Yosko\ViewFormatter::formatNumber($number);
echo "\nYosko\ViewFormatter::formatNumber($number) -> " . Yosko\ViewFormatter::formatNumber($number, 1);
echo "\nYosko\ViewFormatter::formatCurrency($number) -> " . Yosko\ViewFormatter::formatCurrency($number);

$date = '2022-10-05';
echo "\n\nYosko\ViewFormatter::formatDate($date) -> " . Yosko\ViewFormatter::formatDate($date);

$datetime = '2022-10-05 00:50';
echo "\n\nYosko\ViewFormatter::formatDatetime($datetime) -> " . Yosko\ViewFormatter::formatDatetime($datetime);

echo "\n\n";
$serverTimeZone = Yosko\ViewFormatter::getServerTimezone();
print_r($serverTimeZone);
echo "\n\nYosko\ViewFormatter::getServerTimeZone()->getName() -> " . $serverTimeZone->getName();
echo "\nYosko\ViewFormatter::getTimezoneOffset($datetime, ".$serverTimeZone->getName().") -> " . Yosko\ViewFormatter::getTimezoneOffset($datetime, $serverTimeZone);

echo "\n\n";
$configTimeZone = Yosko\ViewFormatter::getConfigTimezone();
print_r($configTimeZone);
echo "\n\nYosko\ViewFormatter::getConfigTimezone()->getName() -> " . $configTimeZone->getName();
echo "\nYosko\ViewFormatter::getTimezoneOffset($datetime, ".$configTimeZone->getName().") -> " . Yosko\ViewFormatter::getTimezoneOffset($datetime, $configTimeZone);

echo "\n\nYosko\ViewFormatter::getTimezoneOffset($datetime, ".$configTimeZone->getName().", ".$serverTimeZone->getName().") -> " . Yosko\ViewFormatter::getTimezoneOffset($datetime, $serverTimeZone, $configTimeZone);


$start = '2022-10-05';

$end1 = '2022-10-06';
echo "\n\nYosko\ViewFormatter::formatPeriod($start, $end1) -> " . Yosko\ViewFormatter::formatPeriod($start, $end1);

$end2 = '2022-12-02';
echo "\nYosko\ViewFormatter::formatPeriod($start, $end2) -> " . Yosko\ViewFormatter::formatPeriod($start, $end2);

$end3 = '2023-03-08';
echo "\nYosko\ViewFormatter::formatPeriod($start, $end3) -> " . Yosko\ViewFormatter::formatPeriod($start, $end3);

$dateObj = new DateTime('2022-10-05');
echo "\nYosko\ViewFormatter::formatMonth($date) -> " . Yosko\ViewFormatter::formatMonth($dateObj);

echo "\n\n";
$dayNames = Yosko\ViewFormatter::getDayNames();
$weekdayNames = Yosko\ViewFormatter::getWeekDayNames();
var_export($dayNames);
var_export($weekdayNames);

$year = 2022;
$month = 10;
echo "\nYosko\ViewFormatter::getFirstDayOfMonthNumber($year, $month) -> " . $dayNames[Yosko\ViewFormatter::getFirstDayOfMonthNumber($year, $month)];
