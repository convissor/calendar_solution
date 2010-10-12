<?php

/**
 * PHP's DateTime class has shortcomings, so use these instead
 *
 * The particular class utilized depends on the server's PHP version.
 * The choice is made in CalendarSolution.php.
 *
 * @package CalendarSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2009-2010
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 */

/**
 * Provides DateTime::diff() functionality because of bug 49081 in PHP
 *
 * Only support years, months and days
 *
 * @package CalendarSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2009-2010
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 */
class CalendarSolution_DateTimeDiff extends DateTime {
    /**
     * Fixes PHP bug 49081
     *
     * Our version only supports years, months and days
     *
     * Generally, $this is the earlier date while the $datetime parameter
     * is the later date.
     *
     * Ported from ext/date/lib/interval.c 298973 2010-05-04 15:11:41Z
     *
     * @param DateTime $datetime
     * @param bool $absolute
     *
     * @return DateInterval
     */
    public function diff($datetime, $absolute = false) {
        $one = new StdClass;
        $one->y = $this->format('Y');
        $one->m = $this->format('n');
        $one->d = $this->format('j');

        $two = new StdClass;
        $two->y = $datetime->format('Y');
        $two->m = $datetime->format('n');
        $two->d = $datetime->format('j');

        $rt = new StdClass;
        $rt->invert = false;
        if ($this->format('U') > $datetime->format('U')) {
            $swp = $two;
            $two = $one;
            $one = $swp;
            $rt->invert = true;
        }

        $rt->y = $two->y - $one->y;
        $rt->m = $two->m - $one->m;
        $rt->d = $two->d - $one->d;

        $this->timelib_do_rel_normalize($rt->invert ? $one : $two, $rt);

        $interval_spec = 'P' . $rt->y . 'Y' . $rt->m . 'M' . $rt->d . 'D';
        $interval = new DateInterval($interval_spec);
        if ($rt->invert) {
            $interval->invert = 1;
        }
        return $interval;
    }

    /**
     * Ported from ext/date/lib/tm2unixtime.c 279799 2009-05-03 18:22:40Z
     */
    private function do_range_limit($start, $end, $adj, &$a, &$b) {
        if ($a < $start) {
            $b -= floor(($start - $a - 1) / $adj) + 1;
            $a += $adj * (floor(($start - $a - 1) / $adj) + 1);
        }
        if ($a >= $end) {
            $b += floor($a / $adj);
            $a -= $adj * floor($a / $adj);
        }
        return 0;
    }

    /**
     * Ported from ext/date/lib/tm2unixtime.c 298973 2010-05-04 15:11:41Z
     */
    private function inc_month(&$y, &$m) {
        $m++;
        if ($m > 12) {
            $m -= 12;
            $y++;
        }
    }

    /**
     * Ported from ext/date/lib/tm2unixtime.c 298973 2010-05-04 15:11:41Z
     */
    private function dec_month(&$y, &$m) {
        $m--;
        if ($m < 1) {
            $m += 12;
            $y--;
        }
    }

    /**
     * Resolves PHP bug 49081 by using changes in revision 298973.
     *
     * Ported from ext/date/lib/tm2unixtime.c 298973 2010-05-04 15:11:41Z
     */
    private function do_range_limit_days_relative(&$base_y, &$base_m, &$y, &$m, &$d, $invert) {
        //                           dec  jan  feb  mrt  apr  may  jun  jul  aug  sep  oct  nov  dec
        $days_in_month_leap = array(  31,  31,  29,  31,  30,  31,  30,  31,  31,  30,  31,  30,  31);
        $days_in_month      = array(  31,  31,  28,  31,  30,  31,  30,  31,  31,  30,  31,  30,  31);

        $this->do_range_limit(1, 13, 12, $base_m, $base_y);

        $year = $base_y;
        $month = $base_m;

        if (!$invert) {
            while ($d < 0) {
                $this->dec_month($year, $month);
                $leapyear = $this->timelib_is_leap($year);
                $days = $leapyear ? $days_in_month_leap[$month] : $days_in_month[$month];

                $d += $days;
                $m--;
            }
        } else {
            while ($d < 0) {
                $leapyear = $this->timelib_is_leap($year);
                $days = $leapyear ? $days_in_month_leap[$month] : $days_in_month[$month];

                $d += $days;
                $m--;
                $this->inc_month($year, $month);
            }
        }
    }

    /**
     * Ported from ext/date/lib/tm2unixtime.c 298973 2010-05-04 15:11:41Z
     */
    private function timelib_do_rel_normalize($base, $rt) {
        while ($this->do_range_limit(0, 12, 12, $rt->m, $rt->y));

        $this->do_range_limit_days_relative($base->y, $base->m, $rt->y, $rt->m, $rt->d, $rt->invert);
        while ($this->do_range_limit(0, 12, 12, $rt->m, $rt->y));
    }

    /**
     * Ported from ext/date/lib/timelib_structs.h 282169 2009-06-15 15:08:12Z
     */
    private function timelib_is_leap($y) {
        return (($y) % 4 == 0 && (($y) % 100 != 0 || ($y) % 400 == 0));
    }
}

/**
 * Provides DateTime::add(), DateTime::sub() and DateTime::modify()
 * methods for versions of PHP before 5.3
 *
 * Our special functionality only support years, months and days
 *
 * @package CalendarSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2009-2010
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 */
class CalendarSolution_DateTime52 extends CalendarSolution_DateTimeDiff {
    private $weeks = array(
        'first' => 1,
        'second' => 2,
        'third' => 3,
        'fourth' => 4,
        'last' => 'last',
    );

    private $days = array(
        'sunday' => 0,
        'monday' => 1,
        'tuesday' => 2,
        'wednesday' => 3,
        'thursday' => 4,
        'friday' => 5,
        'saturday' => 6,

        'sun' => 0,
        'mon' => 1,
        'tue' => 2,
        'wed' => 3,
        'thu' => 4,
        'fri' => 5,
        'sat' => 6,
    );

    /**
     * Subtracts the specified quantity of time to this object
     *
     * @param DateInterval $interval
     *
     * @return DateTime
     */
    public function add($interval) {
        if ($interval->y) {
            parent::modify("+$interval->y year");
        }
        if ($interval->m) {
            parent::modify("+$interval->m month");
        }
        if ($interval->d) {
            parent::modify("+$interval->d day");
        }
        if ($interval->h) {
            parent::modify("+$interval->h hour");
        }
        if ($interval->i) {
            parent::modify("+$interval->i minute");
        }
        if ($interval->s) {
            parent::modify("+$interval->s second");
        }
        return $this;
    }

    /**
     * Subtracts the specified quantity of time from this object
     *
     * @param DateInterval $interval
     *
     * @return DateTime
     */
    public function sub($interval) {
        if ($interval->y) {
            parent::modify("-$interval->y year");
        }
        if ($interval->m) {
            parent::modify("-$interval->m month");
        }
        if ($interval->d) {
            parent::modify("-$interval->d day");
        }
        if ($interval->h) {
            parent::modify("-$interval->h hour");
        }
        if ($interval->i) {
            parent::modify("-$interval->i minute");
        }
        if ($interval->s) {
            parent::modify("-$interval->s second");
        }
        return $this;
    }

    /**
     * Tweaks some DateTime::modify() functionality because of bugs in PHP 5.2
     *
     * @param string $modify  the explanation of how to modify the date.
     *                        Supported values include:
     *                         + "+<integer> <units>" (eg: "+2 days")
     *                         + "-<integer> <units>" (eg: "-2 days")
     *                         + "first day of this month"
     *                         + "last day of this month"
     *                         + "<week_number_word> <day_name> of this month"
     *                           (eg: "second sunday of this month",
     *                           "last friday of this month", etc)
     * @return DateTime
     */
    public function modify($modify) {
        static $regex;

        if ($modify == 'first day of this month') {
            $this->setDate($this->format('Y'), $this->format('m'), 1);
        } elseif ($modify == 'last day of this month') {
            $this->setDate($this->format('Y'), $this->format('m'), $this->format('t'));
        } else {
            if (!isset($regex)) {
                $regex = '/^';
                $regex .= '(' . implode('|' , array_keys($this->weeks)) . ') ';
                $regex .= '(' . implode('|' , array_keys($this->days)) . ') ';
                $regex .= 'of this month$/i';
            }

            if (preg_match($regex, $modify, $match)) {
                $this->modify_week_and_day_of_month($match[1], $match[2]);
            } else {
                parent::modify($modify);
            }
        }
        return $this;
    }

    /**
     * Changes the date to the given week and day of the present month
     * (such as the "second saturday of this month")
     *
     * This is necessary because PHP's DateTime::modify() doesn't handle
     * this syntax correctly in PHP 5.2.
     *
     * @param string $week_word  the week to jump to ("first", "second",
     *                           "third", "fourth", "last")
     * @param string $day_word  the day to jump to ("monday", "mon", "tuesday",
     *                          "tue", etc...)
     * @return DateTime
     */
    private function modify_week_and_day_of_month($week_word, $day_word)
    {
        $week_word = strtolower($week_word);
        if (!array_key_exists($week_word, $this->weeks)) {
            return $this;
        }

        $day_word = strtolower($day_word);
        if (!array_key_exists($day_word, $this->days)) {
            return $this;
        }

        $week = $this->weeks[$week_word];
        $day_of_week = $this->days[$day_word];

        $year = $this->format('Y');
        $month = $this->format('m');

        if (is_numeric($week)) {
            // first through fourth.

            // first 1, second 8, third 15, fourth 22.
            $weeks_first_day = ($week - 1) * 7 + 1;
            $this->setDate($year, $month, $weeks_first_day);
            $weeks_first_day_of_week = $this->format('w');

            $day_of_month = $weeks_first_day +
                    ((7 + $day_of_week - $weeks_first_day_of_week) % 7);
        } else {
            // last.

            $last_day_of_month = $this->format('t');
            $this->setDate($year, $month, $last_day_of_month);
            $diff = $day_of_week - $this->format('w');

            if ($diff > 0) {
                $day_of_month = $last_day_of_month - (7 - $diff);
            } else {
                $day_of_month = $last_day_of_month + $diff;
            }
        }

        $this->setDate($year, $month, $day_of_month);
        return $this;
    }
}
