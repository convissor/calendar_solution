<?php

/**
 * DateTime Solution's DateTime class for the diff() method
 *
 * NOTE: only supports years, months and days (for now).
 *
 * Requires PHP 5.2.
 *
 * @package DateTimeSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2009-2011
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 */

/**
 * Provides a DateTime::diff() substitute to work around bugs in versions of
 * PHP before 5.4
 *
 * NOTE: only supports years, months and days (for now).
 *
 * Requires PHP 5.2.
 *
 * @package DateTimeSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2009-2011
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 */
class DateTimeSolution_Diff extends DateTime {
	/**
	 * Indicates which level of support the DateTime Solution is providing
	 *
	 * Can't use a property because of PHP bug 52738
	 *
	 * @return string
	 */
	public function get_datetime_solution_level() {
		return 'diff';
	}

	/**
	 * Calculates the difference between two DateTime objects
	 *
	 * This method is a workaround for PHP bug 49081.
	 *
	 * Ported from ext/date/lib/interval.c 298973 2010-05-04 15:11:41Z
	 *
	 * @param DateTimeSolution $datetime  the date to compare the current object
	 *                         to.  Generally, the current object ($this) is the
	 *                         earlier date while this parameter ($datetime)
	 *                         is the later date.
	 * @param bool $absolute  should the result always be a positive number?
	 *
	 * @return DateIntervalSolution  the interval object representing the
	 *                               difference between the two dates
	 */
	public function diff(DateTimeSolution $datetime, $absolute = false) {
		$one = new StdClass;
		$one->y = $this->format('Y');
		$one->m = $this->format('n');
		$one->d = $this->format('j');
		$one->z = $this->format('Z');
		$one->sse = $this->format('U');

		$two = new StdClass;
		$two->y = $datetime->format('Y');
		$two->m = $datetime->format('n');
		$two->d = $datetime->format('j');
		$two->z = $datetime->format('Z');
		$two->sse = $datetime->format('U');

		$rt = new StdClass;
		$rt->invert = false;
		if ($one->sse > $two->sse) {
			$swp = $two;
			$two = $one;
			$one = $swp;
			$rt->invert = true;
		}

		$dst_h_corr = ($two->z - $one->z) / 3600;
		$dst_m_corr = (($two->z - $one->z) % 3600) / 60;

		$rt->y = $two->y - $one->y;
		$rt->m = $two->m - $one->m;
		$rt->d = $two->d - $one->d;

		$var = $rt->invert ? 'one' : 'two';
		$this->timelib_do_rel_normalize($$var, $rt);

		$interval_spec = 'P' . $rt->y . 'Y' . $rt->m . 'M' . $rt->d . 'D';
		$interval = new DateIntervalSolution($interval_spec);
		if ($rt->invert && !$absolute) {
			$interval->invert = 1;
		}

		$interval->days = floor((
			abs($one->sse - $two->sse - ($dst_h_corr * 3600) - ($dst_m_corr * 60))
		) / 86400);

		return $interval;
	}

	/**
	 * Decrements the month number, and year if needed
	 *
	 * Ported from ext/date/lib/tm2unixtime.c 298973 2010-05-04 15:11:41Z
	 *
	 * @param int $y  the year
	 * @param int $m  the month
	 *
	 * @return void
	 */
	private function dec_month(&$y, &$m) {
		$m--;
		if ($m < 1) {
			$m += 12;
			$y--;
		}
	}

	/**
	 * Rolls excessive values in smaller unit places into larger unit places
	 *
	 * Ported from ext/date/lib/tm2unixtime.c 279799 2009-05-03 18:22:40Z
	 *
	 * @param int $start
	 * @param int $end
	 * @param int $adj
	 * @param int $a  the smaller unit place
	 * @param int $b  the larger unit place
	 *
	 * @return int  0
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
	 * The heart of the diff calculation
	 *
	 * Resolves PHP bug 49081 by using changes in revision 298973.
	 *
	 * Ported from ext/date/lib/tm2unixtime.c 298973 2010-05-04 15:11:41Z
	 *
	 * @param int $base_y
	 * @param int $base_m
	 * @param int $y
	 * @param int $m
	 * @param int $d
	 * @param int $invert
	 *
	 * @return void
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
	 * Increments the month number, and year if needed
	 *
	 * Ported from ext/date/lib/tm2unixtime.c 298973 2010-05-04 15:11:41Z
	 *
	 * @param int $y
	 * @param int $m
	 *
	 * @return void
	 */
	private function inc_month(&$y, &$m) {
		$m++;
		if ($m > 12) {
			$m -= 12;
			$y++;
		}
	}

	/**
	 * Rounds values in an entire date time object by rolling excessive values
	 * in smaller unit places into larger unit places
	 *
	 * Ported from ext/date/lib/tm2unixtime.c 298973 2010-05-04 15:11:41Z
	 *
	 * @param timelib_time $base
	 * @param timelib_rel_time $rt
	 *
	 * @return void
	 *
	 * @uses DateTimeSolution_Diff::do_range_limit()  to do the rounding
	 * @uses DateTimeSolution_Diff::do_range_limit_days_relative()  to do the rounding
	 */
	private function timelib_do_rel_normalize(&$base, &$rt) {
		$this->do_range_limit(0, 60, 60, $rt->s, $rt->i);
		$this->do_range_limit(0, 60, 60, $rt->i, $rt->h);
		$this->do_range_limit(0, 24, 24, $rt->h, $rt->d);
		$this->do_range_limit(0, 12, 12, $rt->m, $rt->y);

		$this->do_range_limit_days_relative($base->y, $base->m, $rt->y, $rt->m, $rt->d, $rt->invert);
		$this->do_range_limit(0, 12, 12, $rt->m, $rt->y);
	}

	/**
	 * Determines if the given year is a leap year
	 *
	 * Ported from ext/date/lib/timelib_structs.h 282169 2009-06-15 15:08:12Z
	 *
	 * @param int $y
	 *
	 * @return bool
	 */
	private function timelib_is_leap($y) {
		return (($y) % 4 == 0 && (($y) % 100 != 0 || ($y) % 400 == 0));
	}
}
