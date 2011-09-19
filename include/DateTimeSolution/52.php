<?php

/**
 * DateTime Solution's DateTime class for use if PHP < 5.3
 *
 * @package DateTimeSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2009-2011
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 */

/**
 * Provides DateTime::add(), DateTime::sub(), and DateTime::modify()
 * methods for versions of PHP before 5.3
 *
 * @package DateTimeSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2009-2011
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 */
class DateTimeSolution_52 extends DateTimeSolution_Diff {
	private $week_words = array(
		'first' => 1,
		'second' => 2,
		'third' => 3,
		'fourth' => 4,
		'last' => 'last',
	);

	private $day_words = array(
		'sunday' => 0,
		'monday' => 1,
		'tuesday' => 2,
		'wednesday' => 3,
		'thursday' => 4,
		'friday' => 5,
		'saturday' => 6,
	);


	/**
	 * Indicates which level of support the DateTime Solution is providing
	 *
	 * Can't use a property because of PHP bug 52738
	 *
	 * @return string
	 */
	public function get_datetime_solution_level() {
		return '52';
	}

	/**
	 * Subtracts the specified quantity of time to this object
	 *
	 * Method did not exist in PHP 5.2
	 *
	 * @param DateIntervalSolution $interval
	 *
	 * @return DateTimeSolution
	 */
	public function add($interval) {
		if ($interval->invert) {
			$absolute = clone $interval;
			$absolute->invert = 0;
			return $this->sub($absolute);
		}

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
	 * Method did not exist in PHP 5.2
	 *
	 * @param DateIntervalSolution $interval
	 *
	 * @return DateTimeSolution
	 */
	public function sub($interval) {
		if ($interval->invert) {
			$absolute = clone $interval;
			$absolute->invert = 0;
			return $this->add($absolute);
		}

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
	 * Alters the timestamp of the object by incrementing or decrementing
	 * in a format accepted by strtotime()
	 *
	 * This method provides workarounds for bugs in PHP 5.2's DateTime::modify()
	 *
	 * @param string $modify  the explanation of how to modify the date.
	 *                        Supported values include:
	 *                         + "+<integer> <units>" (eg: "+2 day", "+2 month", etc)
	 *                         + "-<integer> <units>" (eg: "-2 day", "+2 month", etc)
	 *                         + "first day of this month"
	 *                         + "last day of this month"
	 *                         + "<week_number_word> <day_name> of this month"
	 *                           (eg: "second sunday of this month",
	 *                           "last friday of this month", etc)
	 * @return DateTimeSolution
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
				$regex .= '(' . implode('|' , array_keys($this->week_words)) . ') ';
				$regex .= '(' . implode('|' , array_keys($this->day_words)) . ') ';
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
	 * @return DateTimeSolution
	 */
	private function modify_week_and_day_of_month($week_word, $day_word)
	{
		$week_word = strtolower($week_word);
		if (!array_key_exists($week_word, $this->week_words)) {
			return $this;
		}

		$day_word = strtolower($day_word);
		if (!array_key_exists($day_word, $this->day_words)) {
			return $this;
		}

		$week = $this->week_words[$week_word];
		$day_of_week = $this->day_words[$day_word];

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
