ViewFormatter
=====

PHP library to handle date, time, numbers and currency formatting destined to be displayed.

It relies on the following native formatting classes:
* [NumberFormatter](https://www.php.net/manual/en/class.numberformatter.php) for numbers and currency ;
* [IntlDateFormatter](https://www.php.net/manual/en/class.intldateformatter.php) for dates and time.

## Requirements
The latest version has been tested on **PHP 7.4**.

## How to use

### Configuration
```ViewFormatter``` is a static class, but it uses a default configuration (in **french/France**) you can override. Here is an exemple to override with an **english/US** configuration:

  ```php
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
  ```
The following examples are all based on this config.

### Numbers and currencies

  ```php
$number = 123456.0987;
echo Yosko\ViewFormatter::formatNumber($number) // result: 123,456.099 (defaults to 3 digits)

// you should be able to specify the number of digits (CURRENTLY DOESN'T WORK)
echo Yosko\ViewFormatter::formatNumber($number) // result: 123,456.1

echo Yosko\ViewFormatter::formatCurrency($number) // result: $123,456.10
  ```

### Date and time

 * Possible input date formats: the ones supported by ```\DateTime```
 * **Warning** : all dates given in string format should be relative to the server's timezone. If it defers from the config's timezone, the offset will be applied to the resulting formatted string.

    In the following examples, the server uses **Europe/Paris** (GMT+2 for this period of time). Since the config uses **America/New_York** (GMT-4), the offset will be **minus 6 hours**.

## Simple uses

  ```php
$date = '2022-10-05';
echo Yosko\ViewFormatter::formatDate($date);
// result: Tue 10-04-2022


$datetime = '2022-10-05 00:50';
echo Yosko\ViewFormatter::formatDatetime($datetime);
// result: Tue 10-04-2022 18:50 (offset: minus 6 hours due to timezones)
  ```

## Periode of time (begin and end dates)

  ```php
$start = '2022-10-05';

$end1 = '2022-10-06';
echo Yosko\ViewFormatter::formatPeriod($start, $end1);
// result: from Tue 04 to Wed 10-05-2022

$end2 = '2022-12-02';
echo Yosko\ViewFormatter::formatPeriod($start, $end2);
// result: from Tue 10-04 to Thu 12-01-2022

$end3 = '2023-03-08';
echo Yosko\ViewFormatter::formatPeriod($start, $end3);
// result: from Tue 10-04-2022 to Tue 03-07-2023
  ```

## Date names and other tools

  ```php
$dateObj = new DateTime('2022-10-05');
echo Yosko\ViewFormatter::formatMonth($dateObj);
// result: October 2022

var_export(Yosko\ViewFormatter::getDayNames());
// result: 
// array (
//   0 => 'Sunday',
//   1 => 'Monday',
//   2 => 'Tuesday',
//   3 => 'Wednesday',
//   4 => 'Thursday',
//   5 => 'Friday',
//   6 => 'Saturday',
// )

var_export(Yosko\ViewFormatter::getWeekDayNames());
// result: 
// array (
//   0 => 'Monday',
//   1 => 'Tuesday',
//   2 => 'Wednesday',
//   3 => 'Thursday',
//   4 => 'Friday',
// )

  ```

## Timezones
To get information on the server's and config's timezones:

  ```php
//gives DateTimeZone objects
$serverTimeZone = Yosko\ViewFormatter::getServerTimezone();
$configTimeZone = Yosko\ViewFormatter::getConfigTimezone();
var_dump(
  $serverTimeZone->getName(),
  $configTimeZone->getName()
);


  ```

## Licence

This library is a work by Yosko, all wright reserved.

It is licensed under the [GNU LGPL](http://www.gnu.org/licenses/lgpl.html) license.
