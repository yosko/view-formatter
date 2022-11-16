<?php

namespace Yosko;

use DateTime;
use DateTimeZone;
use Exception;
use IntlDateFormatter;
use NumberFormatter;
use RuntimeException;

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
    private static NumberFormatter $percentFormatter;
    private static IntlDateFormatter $dateFormatter;

    /**
     * initialize the number formatter if it is not already set, then returns it
     * @return NumberFormatter
     */
    protected static function getNumberFormatter(): NumberFormatter
    {
        if (!isset(self::$numberFormatter)) {
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
     * initialize the percent formatter if it is not already set, then returns it
     * @return NumberFormatter
     */
    protected static function getPercentFormatter(): NumberFormatter
    {
        if (!isset(self::$percentFormatter)) {
            self::$percentFormatter = new NumberFormatter(self::$config['locale'], NumberFormatter::PERCENT);
            self::$percentFormatter->setAttribute(\NumberFormatter::MAX_FRACTION_DIGITS, 1);
        }

        return self::$percentFormatter;
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

    /**
     * TODO: The $digits parameter doesn't seem to work...
     */
    public static function formatNumber($value, $digits = null): string
    {
        $nbF = self::getNumberFormatter();
        if (!is_null($digits)) {
            $digitsOld = $nbF->getAttribute(\NumberFormatter::MAX_FRACTION_DIGITS);
            $nbF->setAttribute(\NumberFormatter::MAX_FRACTION_DIGITS, $digits);
        }
        $parsed = $nbF->parse(round($value, $digits));
        if ($parsed === false) {
            throw new RuntimeException(sprintf('Could not parse number "%s". NumberFormatter returned error: %s', $value, $nbF->getErrorMessage()),$nbF->getErrorCode());
        }
        $result = $nbF->format($parsed);
        if (!is_null($digits)) {
            $nbF->setAttribute(\NumberFormatter::MAX_FRACTION_DIGITS, $digitsOld);
        }
        return $result;
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
     * @param string|float $value
     * @return string
     */
    public static function formatPercent($value): string
    {
        return self::getPercentFormatter()->format($value);
    }

    /**
     * Gives the server's timezone
     * @return DateTimeZone
     */
    public static function getServerTimezone():DateTimeZone {
        return (new DateTime())->getTimezone();
    }

    /**
     * Gives the server's timezone
     * @return DateTimeZone
     */
    public static function getConfigTimezone():DateTimeZone {
        return new DateTimeZone(self::$config['timezone']);
    }

    /**
     * Gives the time offset to GMT from a given timezone on a given datetime
     * @param string $datetimeStr
     * @param DateTimeZone $timezone
     * @param ?DateTimeZone $secondTimezone
     * @return string|int HH:ii:ss or seconds
     */
    public static function getTimezoneOffset(string $datetimeStr, DateTimeZone $timezone, ?DateTimeZone $secondTimezone = null, $inSeconds = false):string {
        $datetime = new DateTime($datetimeStr);
        $offsetInSeconds = $timezone->getOffset($datetime);

        if (!empty($secondTimezone)) {
            $offsetInSeconds = $secondTimezone->getOffset($datetime) - $offsetInSeconds;
        }

        if ($inSeconds) {
            return $offsetInSeconds;
        }

        return sprintf('%02d:%02d:%02d', ($offsetInSeconds/3600),($offsetInSeconds/60%60), $offsetInSeconds%60);
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

    /**
     * TODO: accept both string AND DateTime as input?
     */
    public static function formatMonth(DateTime $date, $short = false): string
    {
        self::getDateFormatter()->setPattern($short ? 'MMM yy' : 'MMMM yyyy');
        return self::getDateFormatter()->format($date);
    }

    protected static function getMondayWeekPosition(): int {
        return (int) self::getDateFormatter()->formatObject(new DateTime('next Monday'),'e');
    }

    public static function getDayNames($dayNumbers = []): array
    {
        if (empty($dayNumbers)) {
            $mondayPos = self::getMondayWeekPosition();
            $dayNumbers = range(1-$mondayPos, 1-$mondayPos+6);
        }

        $formatter = self::getDateFormatter();
        $formatter->setPattern('EEEE');
        return array_map(
            function ($day) use ($formatter) {
                //time
                $formatter->setTimeZone(self::getServerTimezone());
                $str = $formatter->format(strtotime('next Monday +' . $day . ' days'));
                $formatter->setTimeZone(self::getConfigTimezone());
                
                return $str;
            },
            $dayNumbers
        );
    }

    public static function getWeekDayNames(): array
    {
        $mondayPos = self::getMondayWeekPosition();
        return self::getDayNames(range(0, 4));
    }

    public static function getFirstDayOfMonthNumber(int $month, int $year): int
    {
        $date = new DateTime();
        $date->setDate($year, $month, 1);
        // self::getDateFormatter()->setPattern('e');
        // var_dump(self::getDateFormàatter()->format($date));
        return $date->format('N');
    }
}