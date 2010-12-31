<?php

/**
 * DateTime Solution's DateTime class for use if PHP < 5.3.5
 *
 * NOTE: only supports years, months and days (for now).
 *
 * Requires PHP 5.2.
 *
 * @package DateTimeSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2009-2010
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 */

/**
 * Provides a DateTime::diff() substitute to work around bugs in versions of
 * PHP before 5.3.5
 *
 * NOTE: only supports years, months and days (for now).
 *
 * Requires PHP 5.2.
 *
 * @package DateTimeSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2009-2010
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 */
class DateTimeSolution_Diff extends DateTime {
	const DAYS_PER_LYEAR = 366;
	const DAYS_PER_YEAR = 365;
	const SECS_PER_DAY = 86400;

	/**
	 * Indicates which level of support the DateTime Solution is providing
	 * @var string
	 */
	public $datetime_solution_level = 'diff';

	protected $transitions;


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
		$this->transitions = array();
		$dst_h_corr = 0;
		$dst_m_corr = 0;

		$one = new StdClass;
		$one->y = $this->format('Y');
		$one->m = $this->format('n');
		$one->d = $this->format('j');
		$one->h = $this->format('G');
		$one->i = $this->format('i');
		$one->s = $this->format('s');
		$one->z = $this->format('Z');
		$one->sse = $this->format('U');
		$one->tz_info = $this->getTimezone();
		$one->tz_name = $one->tz_info->getName();

		$two = new StdClass;
		$two->y = $datetime->format('Y');
		$two->m = $datetime->format('n');
		$two->d = $datetime->format('j');
		$two->h = $datetime->format('G');
		$two->i = $datetime->format('i');
		$two->s = $datetime->format('s');
		$two->z = $datetime->format('Z');
		$two->sse = $datetime->format('U');
		$two->tz_info = $datetime->getTimezone();
		$two->tz_name = $two->tz_info->getName();

		// Disable time comparisons for the moment.
		$compare_time = false;

		$rt = new StdClass;
		$rt->invert = false;
		if ($one->sse > $two->sse) {
			$swp = $two;
			$two = $one;
			$one = $swp;
			$rt->invert = true;
		}

		if ($compare_time) {
			// zone_type not in userland, use getTransitions() as flag.
			// Getting transitions is very expensive, store it for later use.
			$this->transitions[$one->tz_name][$one->sse] =
					$one->tz_info->getTransitions($one->sse, $one->sse);
			$this->transitions[$two->tz_name][$two->sse] =
					$two->tz_info->getTransitions($two->sse, $two->sse);

			/* Calculate correction for DST change over, but only if the TZ type is ID
			 * and it's the same */
			if (!empty($this->transitions[$one->tz_name][$one->sse])
				&& !empty($this->transitions[$two->tz_name][$two->sse])
				&& $one->tz_name == $two->tz_name
				&& $one->z != $two->z)
			{
				$dst_h_corr = ($two->z - $one->z) / 3600;
				$dst_m_corr = (($two->z - $one->z) % 3600) / 60;
			}

			$this->timelib_apply_localtime($one, 0);
			$this->timelib_apply_localtime($two, 0);
		}

		$rt->y = $two->y - $one->y;
		$rt->m = $two->m - $one->m;
		$rt->d = $two->d - $one->d;
		$rt->h = $two->h - $one->h + $dst_h_corr;
		$rt->i = $two->i - $one->i + $dst_m_corr;
		$rt->s = $two->s - $one->s;

		$var = $rt->invert ? 'one' : 'two';
		$this->timelib_do_rel_normalize($$var, $rt);

		if ($compare_time) {
			$this->timelib_apply_localtime($one, 1);
			$this->timelib_apply_localtime($two, 1);
		}

		$interval_spec = 'P' . $rt->y . 'Y' . $rt->m . 'M' . $rt->d . 'D';
		$interval = new DateIntervalSolution($interval_spec);
		if ($rt->invert && !$absolute) {
			$interval->invert = 1;
		}

		/*
		 * Damn!  Writing to $days throws fatal error.  Bug 53634.
		$interval->days = abs(floor(($one->sse - $two->sse - ($dst_h_corr * 3600) - ($dst_m_corr * 60)) / 86400));
		 */

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
	 * Passes calls to the requested function
	 *
	 * Ported from ext/date/lib/unixtime2tm.c 293036 2010-01-03 09:23:27Z
	 *
	 * @param timelib_time $t
	 * @param int $localtime  0 to apply local time, 1 to apply GMT time
	 *
	 * @return int  0 on success, -1 if $t lacks tz_info property
	 *
	 * @uses DateTimeSolution_Diff::timelib_unixtime2local()  to apply local time
	 * @uses DateTimeSolution_Diff::timelib_unixtime2gmt()  to apply GMT time
	 */
	private function timelib_apply_localtime(&$t, $localtime) {
		if ($localtime) {
			if (!$t->tz_info) {
				return -1;
			}
			$this->timelib_unixtime2local($t, $t->sse);
		} else {
			$this->timelib_unixtime2gmt($t, $t->sse);
		}
		return 0;
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
		while ($this->do_range_limit(0, 60, 60, $rt->s, $rt->i));
		while ($this->do_range_limit(0, 60, 60, $rt->i, $rt->h));
		while ($this->do_range_limit(0, 24, 24, $rt->h, $rt->d));
		while ($this->do_range_limit(0, 12, 12, $rt->m, $rt->y));

		$this->do_range_limit_days_relative($base->y, $base->m, $rt->y, $rt->m, $rt->d, $rt->invert);
		while ($this->do_range_limit(0, 12, 12, $rt->m, $rt->y));
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

	/**
	 * Ported from ext/date/lib/parse_tz.c 293036 2010-01-03 09:23:27Z
	 *
	 * NOTE: leap_secs exists in original but DateTimeSolution does not need
	 * it, so don't bother calculating it here.
	 *
	 * @param int $ts
	 * @param timelib_tzinfo $tz
	 *
	 * @return timelib_time_offset
	 */
	private function timelib_get_time_zone_info($ts, &$tz) {
		$tmp = new StdClass;

		if (!empty($this->transitions[$tz->getName()][$ts])) {
			$to = $this->transitions[$tz->getName()][$ts];
			$offset = $to[0]['offset'];
			$abbr = $to[0]['abbr'];
			$tmp->is_dst = $to[0]['isdst'];
		} else {
			$offset = 0;
			$abbr = $tz->getName();
			$tmp->is_dst = 0;
		}

		$tmp->offset = $offset;
		$tmp->abbr = $abbr ? $abbr : 'GMT';

		return $tmp;
	}

	/**
	 * Populates the given time object's properties with values in the GMT
	 * time zone corresponding to the given timestamp
	 *
	 * Ported from ext/date/lib/unixtime2tm.c 293036 2010-01-03 09:23:27Z
	 *
	 * @param timelib_time $tm
	 * @param int $ts
	 *
	 * @return void
	 */
	private function timelib_unixtime2gmt(&$tm, $ts) {
		$month_tab_leap = array(-1, 30, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334);
        $month_tab      = array( 0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334);
		$cur_year = 1970;

		$days = floor($ts / self::SECS_PER_DAY);
		$remainder = $ts - ($days * self::SECS_PER_DAY);
		if ($ts < 0 && $remainder == 0) {
			$days++;
			$remainder -= self::SECS_PER_DAY;
		}

		if ($ts >= 0) {
			$tmp_days = $days + 1;
			while ($tmp_days >= self::DAYS_PER_LYEAR) {
				$cur_year++;
				if ($this->timelib_is_leap($cur_year)) {
					$tmp_days -= self::DAYS_PER_LYEAR;
				} else {
					$tmp_days -= self::DAYS_PER_YEAR;
				}
			}
		} else {
			$tmp_days = $days;
			while ($tmp_days <= 0) {
				if ($tmp_days < -1460970) {
					$cur_year -= 4000;
					$tmp_days += 1460970;
				} else {
					$cur_year--;
					if ($this->timelib_is_leap($cur_year)) {
						$tmp_days += self::DAYS_PER_LYEAR;
					} else {
						$tmp_days += self::DAYS_PER_YEAR;
					}
				}
			}
			$remainder += self::SECS_PER_DAY;
		}

		$months = $this->timelib_is_leap($cur_year) ? $month_tab_leap : $month_tab;
		if ($this->timelib_is_leap($cur_year) && $cur_year < 1970) {
			$tmp_days--;
		}
		$i = 11;
		while ($i > 0) {
			if ($tmp_days > $months[$i]) {
				break;
			}
			$i--;
		}

		/* That was the date, now we do the tiiiime */
		$hours = floor($remainder / 3600);
		$minutes = floor(($remainder - $hours * 3600) / 60);
		$seconds = $remainder % 60;

		$tm->y = $cur_year;
		$tm->m = $i + 1;
		$tm->d = $tmp_days - $months[$i];
		$tm->h = $hours;
		$tm->i = $minutes;
		$tm->s = $seconds;
		$tm->z = 0;
		$tm->dst = 0;
		$tm->sse = $ts;
		$tm->sse_uptodate = 1;
		$tm->tim_uptodate = 1;
		$tm->is_localtime = 0;
	}

	/**
	 * Populates the given time object's properties with values in the object's
	 * time zone corresponding to the given timestamp
	 *
	 * Ported from ext/date/lib/unixtime2tm.c 293036 2010-01-03 09:23:27Z
	 *
	 * @param timelib_time $tm
	 * @param int $ts
	 *
	 * @return void
	 */
	private function timelib_unixtime2local(&$tm, $ts) {
		$tz = $tm->tz_info;

		// zone_type not available in userland, use getTransitions() as indicator.
		if (empty($this->transitions[$tm->tz_name][$tm->sse])) {
			// TIMELIB_ZONETYPE_ABBR
			// TIMELIB_ZONETYPE_OFFSET

			$z = $tm->z;
			$dst = $tm->dst;

			$this->timelib_unixtime2gmt($tm, $ts - ($tm->z * 60));

			$tm->z = $z;
			$tm->dst = $dst;
		} else {
			// TIMELIB_ZONETYPE_ID

			$gmt_offset = $this->timelib_get_time_zone_info($ts, $tz);
			$this->timelib_unixtime2gmt($tm, $ts + $gmt_offset->offset);

			/* we need to reset the sse here as unixtime2gmt modifies it */
			$tm->sse = $ts;
			$tm->dst = $gmt_offset->is_dst;
			$tm->z = $gmt_offset->offset;
			$tm->tz_info = $tz;

// Seems this is not needed in userland.
//			timelib_time_tz_abbr_update($tm, $gmt_offset->abbr);

			unset($gmt_offset);
		}

		$tm->is_localtime = 1;
		$tm->have_zone = 1;
	}
}
