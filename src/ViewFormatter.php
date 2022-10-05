<?php

namespace Yosko;

use DateTime;
use Exception;
use IntlDateFormatter;
use NumberFormatter;

class ViewFormatter
{
    public static array $config = [
        'locale' => 'fr_FR',
        'timezone' => 'Europe/Paris',
        'currency' => 'EUR',
        'date_full' => 'dd/MM/yyyy',
        'date_partial' => 'dd/MM',
        'date_on' => 'le',
        'date_from' => 'du',
        'date_to' => 'au'
    ];

    private static NumberFormatter $numberFormatter;
    private static NumberFormatter $currencyFormatter;
    private static IntlDateFormatter $dateFormatter;

    /**
     * initialize the number formatter if it is not already set, then returns it
     * @return NumberFormatter
     */
    protected static function getNumberFormatter(): NumberFormatter
    {
        if (!isset(self::$currencyFormatter)) {
            self::$numberFormatter = new NumberFormatter(self::$config['locale'], NumberFormatter::DECIMAL);
            //self::$numberFormatter->setAttribute(\NumberFormatter::MIN_FRACTION_DIGITS, 0);
            //self::$numberFormatter->setAttribute(\NumberFormatter::MAX_FRACTION_DIGITS, 3);
        }

        return self::$numberFormatter;
    }

    /**
     * initialize the currency formatter if it is not already set, then returns it
     * @return NumberFormatter
     */
    protected static function getCurrencyFormatter(): NumberFormatter
    {
        if (!isset(self::$currencyFormatter)) {
            self::$currencyFormatter = new NumberFormatter(self::$config['locale'], NumberFormatter::CURRENCY);
        }

        return self::$currencyFormatter;
    }

    /**
     * initialize the date formatter if it is not already set, then returns it
     * @return IntlDateFormatter
     */
    protected static function getDateFormatter(): IntlDateFormatter
    {
        if (!isset(self::$dateFormatter)) {
            self::$dateFormatter = new IntlDateFormatter(
                self::$config['locale'],
                IntlDateFormatter::FULL,
                IntlDateFormatter::NONE,
                self::$config['timezone'],
                IntlDateFormatter::GREGORIAN
            );
        }

        return self::$dateFormatter;
    }

    public static function formatNumber($value): string
    {
        return self::getNumberFormatter()->format($value);
    }

    /**
     * @param string|float $value
     * @return string
     */
    public static function formatCurrency($value): string
    {
        return self::getCurrencyFormatter()->formatCurrency($value, self::$config['currency']);
    }

    /**
     * formats a date for display
     * @param string|DateTime  $value date in ISO format or as a DateTime object
     * @return string
     */
    public static function formatDate($value, bool $includeTime = false, bool $includeDayName = true): string
    {
        try {
            if ($value instanceof DateTime) {
                $date = $value;
            } else {
                $date = new DateTime($value);
            }
        } catch (Exception $e) {
            $date = new DateTime();
        }
        $format = self::$config['date_full'];
        if ($includeDayName) {
            $format = 'E ' . $format;
        }
        if ($includeTime) {
            $format .= ' kk:mm';
        }
        self::getDateFormatter()->setPattern($format);
        return self::getDateFormatter()->format($date);
    }

    /**
     * formats a date and time for display
     * @param string $value
     * @return string
     */
    public static function formatDateTime(string $value): string
    {
        return self::formatDate($value, true);
    }

    /**
     * formats a period (with starting date and ending date) for display
     * @param string $from beginning date (ISO format)
     * @param string $to end date (ISO format)
     * @return string
     * @throws Exception
     */
    public static function formatPeriod(string $from, string $to): string
    {
        $dateFrom = new DateTime($from);
        $dateTo = new DateTime($to);

        if ($from == $to) {
            $result = self::$config['date_on'].' ';
        } else {
            if ($dateFrom->format('Y') != $dateTo->format('Y')) {
                $pattern = self::$config['date_full'];
                
            } elseif ($dateFrom->format('m') != $dateTo->format('m')) {
                $pattern = self::$config['date_partial'];
            } else {
                $pattern = 'dd';
            }
            self::getDateFormatter()->setPattern('E '.$pattern);
            $result = self::$config['date_from'].' ' . self::getDateFormatter()->format($dateFrom) . ' '.self::$config['date_to'].' ';
        }

        self::getDateFormatter()->setPattern('E '.self::$config['date_full']);
        return $result . self::getDateFormatter()->format($dateTo);
    }

    public static function formatMonth(DateTime $date): string
    {
        self::getDateFormatter()->setPattern('MMMM yyyy');
        return self::getDateFormatter()->format($date);
    }

    public static function getDayNames($dayNumbers = []): array
    {
        if (empty($dayNumbers)) {
            $dayNumbers = range(0, 6);
        }

        $formatter = self::getDateFormatter();
        $formatter->setPattern('EEEE');
        return array_map(
            function ($day) use ($formatter) {
                return $formatter->format(strtotime('next Monday +' . $day . ' days'));
            },
            $dayNumbers
        );
    }

    public static function getWeekDayNames(): array
    {
        return self::getDayNames(range(0, 4));
    }

    public static function getFirstDayOfMonthNumber(int $month, int $year): int
    {
        $date = new DateTime();
        $date->setDate($year, $month, 1);
        return $date->format('N');
    }
}