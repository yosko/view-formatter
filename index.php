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
echo "\nYosko\ViewFormatter::formatCurrency($number) -> " . Yosko\ViewFormatter::formatCurrency($number);

$date = '2022-10-05';
echo "\n\nYosko\ViewFormatter::formatDate($date) -> " . Yosko\ViewFormatter::formatDate($date);

$datetime = '2022-10-05 00:50';
echo "\n\nYosko\ViewFormatter::formatDatetime($datetime) -> " . Yosko\ViewFormatter::formatDatetime($datetime);
