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

### Numbers
2. Use it for a single query:

  ```php
$sg->select('MyTable');
$data = $sg->execute();
  ```

## Licence

This library is a work by Yosko, all wright reserved.

It is licensed under the [GNU LGPL](http://www.gnu.org/licenses/lgpl.html) license.
