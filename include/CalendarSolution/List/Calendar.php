<?php

/**
 * Calendar Solution's means to output collections of events formatted as a
 * calendar with brief info about each event
 *
 * @package CalendarSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2002-2011
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 */

/**
 * The means to output collections of events formatted as a calendar with
 * brief info about each event
 *
 * @package CalendarSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2002-2011
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 */
class CalendarSolution_List_Calendar extends CalendarSolution_List {
	/**
	 * The type of view this class represents
	 * @var string
	 */
	protected $view = 'Calendar';


	/**
	 * Determine how many months to display
	 *
	 * @param DateTimeSolution $current
	 * @param DateTimeSolution $to
	 */
	protected function calculate_months($current, $to)
	{
		$length_interval = $current->diff($to, true);
		$months = $length_interval->format('%m') + 1;
		$years = $length_interval->format('%y');
		if ($years) {
			$months += $years * 12;
		}
		return $months;
	}

	/**
	 * @return string  the HTML for one event
	 */
	protected function get_event_formatted($event) {
		/*
		 * NOTE: SQL Solution runs the output through htmlspecialchars(),
		 * so there is no need to do it here.
		 */

		if ($event['status_id'] == self::STATUS_CANCELLED) {
			$event['title'] = 'CANCELLED: ' . $event['title'];
			$class = 'X';
		} elseif ($event['changed'] == 'Y') {
			$event['title'] = 'CHANGED: ' . $event['title'];
			$class = 'Y';
		} else {
			$class = 'N';
		}

		if ($event['status_id'] == self::STATUS_FULL) {
			$event['title'] = 'FULL: ' . $event['title'];
		}

		$out = '<div class="item' . $class . '">';
		$out .= '<span class="title">' . $this->get_link($event) . '</span>';

		$out .= ($event['time_start'] ?
			'<br /><span class="time">'
			. $this->format_date($event['time_start'], self::DATE_FORMAT_TIME_12AP)
			. '</span>' : '');

		$out .= ($event['location_start'] ?
				'<br /><span class="location_start">' . $event['location_start'] . '</span>' : '');
		$out .= '</div>';

		return $out;
	}

	/**
	 * @return string  the HTML for the month header
	 */
	protected function get_month_open(DateTime $current_date_time) {
		$out = '<table class="cs_list_calendar">' . "\n"
			. ' <caption>'
			. $current_date_time->format('F') . ' '
			. $current_date_time->format('Y')
			. '</caption>' . "\n"
			. " <tr>\n"
			. '  <th>Sun</th>' . "\n"
			. '  <th>Mon</th>' . "\n"
			. '  <th>Tue</th>' . "\n"
			. '  <th>Wed</th>' . "\n"
			. '  <th>Thu</th>' . "\n"
			. '  <th>Fri</th>' . "\n"
			. '  <th>Sat</th>' . "\n"
			. " </tr>\n"
			. $this->get_row_open()
			. $this->get_month_pad_start($current_date_time->format('w'));

		return $out;
	}

	/**
	 * @return string  the HTML closing out a month
	 */
	protected function get_month_close(DateTime $current_date_time) {
		$out = $this->get_month_pad_end($current_date_time->format('w'))
			. $this->get_row_close()
			. "</table>\n";

		return $out;
	}

	/**
	 * @return string  the HTML for opening a row
	 */
	protected function get_row_open() {
		return " <tr>\n";
	}

	/**
	 * @return string  the HTML for closing a row
	 */
	protected function get_row_close() {
		return " </tr>\n";
	}

	/**
	 * @param int $w  the number in the week of the first day of the given month
	 * @return string  the HTML for filling blanks at the start of a calendar
	 * @uses CalendarSolution_List_Calendar::get_pad()
	 */
	protected function get_month_pad_start($w) {
		return $this->get_pad(($w + 1) - 1);
	}

	/**
	 * @param int $w  the number in the week of the last day of the given month
	 * @return string  the HTML for filling blanks at the end of a calendar
	 * @uses CalendarSolution_List_Calendar::get_pad()
	 */
	protected function get_month_pad_end($w) {
		return $this->get_pad(6 - $w);
	}

	/**
	 * @param int $quantity  the number of cells to create
	 * @return string  the HTML containing $quantity of blank cells
	 */
	protected function get_pad($quantity) {
		return str_repeat('  <td>&nbsp;</td>' . "\n", $quantity);
	}

	/**
	 * Produces a list of events laid out in a calendar format
	 *
	 * @return string  the complete HTML of the events and the related interface
	 *
	 * @uses CalendarSolution_List::set_request_properties()  to automatically
	 *       set properties to $_REQUEST data
	 * @uses CalendarSolution_List::set_prior_and_next_dates()
	 * @uses CalendarSolution_List::get_limit_form()
	 * @uses CalendarSolution_List::get_navigation()
	 * @uses CalendarSolution_List::run_query()
	 */
	public function get_rendering() {
		$this->set_request_properties();
		$this->set_prior_and_next_dates();

// This doesn't work.  For example, DateTime::add() fails.
//        $current_date_time = $this->from;

		$current_date_time = new DateTimeSolution($this->from->format('Y-m-d'));

		$months = $this->calculate_months($current_date_time, $this->to);

		$one_day_interval = new DateIntervalSolution('P1D');

		$out = $this->get_navigation();

		$this->run_query();
		$event = $this->sql->RecordAsAssocArray(__FILE__, __LINE__,
			array('calendar_uri', 'frequent_event_uri'));

		for ($month_counter = 0; $month_counter < $months; $month_counter++) {
			$out .= $this->get_month_open($current_date_time);

			$days_in_month = $current_date_time->format('t');
			for ($day_counter = 1; $day_counter <= $days_in_month; $day_counter++) {
				$out .= '  <td>' . $day_counter;

				$the_date = $current_date_time->format('Y-m-d');
				if ($the_date == $event['date_start']) {
					do {
						$out .= $this->get_event_formatted($event);
						$event = $this->sql->RecordAsAssocArray(__FILE__, __LINE__,
							array('calendar_uri', 'frequent_event_uri'));
					} while ($the_date == $event['date_start']);
				}

				$out .= '</td>' . "\n";

				if ($day_counter < $days_in_month) {
					if ($current_date_time->format('w') == 6) {
						$out .= $this->get_row_close();
						$out .= $this->get_row_open();
					}
					$current_date_time->add($one_day_interval);
				}
			}

			$out .= $this->get_month_close($current_date_time);
			$current_date_time->add($one_day_interval);
		}

		$out .= $this->get_limit_form();
		$out .= $this->get_credit();

		return $out;
	}

	/**
	 * Sets the "from" property to the first day of the given month (the
	 * default is the first day of the current month)
	 *
	 * @param mixed $in  see CalendarSolution_List::set_from()
	 * @return void
	 *
	 * @uses CalendarSolution_List::set_from()  to determine the
	 *       user's intention and perform the initial setting
	 */
	public function set_from($in = null) {
		parent::set_from($in);
		if ($this->from) {
			$this->from->modify('first day of this month');
		}
	}
}
