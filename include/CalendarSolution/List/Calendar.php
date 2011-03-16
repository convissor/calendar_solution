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
 * Intended to show all events between the first of this month and a specified
 * end date.
 *
 * The date limits should be established using set_from() and set_to().
 *
 * @see CalendarSolution_List::factory_chosen_view()
 * @see CalendarSolution_List_Calendar::get_rendering()
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
	 * Sets the "from" property to the first day of the given month
	 *
	 * CalendarSolution_List_Calendar::set_from() defaults to the first day of
	 * today's month.  CalendarSolution_List::set_from() defaults to today.
	 *
	 * NOTE: "from" is reset to "permit_history_date" if "from" is earlier than
	 * "permit_history_date"
	 *
	 * @param mixed $in  + NULL = use value of $_REQUEST['from'] though uses
	 *                   the default if it is not set or invalid
	 *                   + TRUE = use value of $_REQUEST['from'] if it is set,
	 *                   use the default if it is invalid, use FALSE if not set
	 *                   + FALSE = set the value to FALSE
	 *                   + string = a date in YYYY-MM-DD format though uses
	 *                   the default if it is invalid
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

	/**
	 * Determine how many months to display
	 *
	 * @param DateTimeSolution $current
	 * @param DateTimeSolution $to
	 */
	protected function calculate_months($current, $to) {
		$length_interval = $current->diff($to, true);
		$months = $length_interval->format('%m') + 1;
		$years = $length_interval->format('%y');
		if ($years) {
			$months += $years * 12;
		}
		return $months;
	}

	/**
	 * @return string  the HTML for the month header
	 */
	protected function get_month_open(DateTime $current_date_time) {
		$out = '<table class="cs_list_calendar">' . "\n"
			. '<caption>'
			. $current_date_time->format('F') . ' '
			. $current_date_time->format('Y')
			. '</caption>' . "\n"
			. '<tr>'
			. '<th>Sun</th>'
			. '<th>Mon</th>'
			. '<th>Tue</th>'
			. '<th>Wed</th>'
			. '<th>Thu</th>'
			. '<th>Fri</th>'
			. '<th>Sat</th>'
			. "</tr>\n"
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
		return str_repeat('<td>&nbsp;</td>' . "\n", $quantity);
	}

	/**
	 * @return string  the HTML for opening a row
	 */
	protected function get_row_open() {
		return "<tr>\n";
	}

	/**
	 * @return string  the HTML for closing a row
	 */
	protected function get_row_close() {
		return "</tr>\n";
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
		} elseif ($event['changed'] == 'Y') {
			$event['title'] = 'CHANGED: ' . $event['title'];
		}
		if ($event['status_id'] == self::STATUS_FULL) {
			$event['title'] = 'FULL: ' . $event['title'];
		}

		$out = '<div class="cs_item cs_status_' . substr($event['status'], 0, 1)
			. ' cs_is_own_event_' . $event['is_own_event']
			. ' cs_changed_' . $event['changed'] . '">';

		$out .= '<span class="cs_title">' . $this->get_link($event) . '</span>';

		if ($event['time_start']) {
			$out .= '<span class="cs_time">'
				. $this->format_date($event['time_start'], $this->time_format)
				. '</span>';
		}

		if ($event['location_start'] && $this->show_location) {
			$out .= '<span class="cs_location_start">'
				. $event['location_start'] . '</span>';
		}

		$out .= "</div>\n";

		return $out;
	}

	/**
	 * Produces a list of events laid out in a calendar grid format
	 *
	 * Cascading Style Sheet notes:  the month is contained within
	 * "table.cs_list_calendar".  Each event is wrapped by a "div.cs_item"
	 * which has additional multiple class attributes:
	 * + Status (Open, Full, Cancelled): cs_status_O, cs_status_F, cs_status_C
	 * + Changed: cs_changed_Y, cs_changed_N
	 * + Organizer: cs_is_own_event_Y, cs_is_own_event_N
	 *
	 * @return string  the HTML for displaying the events
	 *
	 * @see CalendarSolution_List::get_limit_form()
	 * @see CalendarSolution_List::get_date_navigation()
	 * @see CalendarSolution_List::get_change_view()
	 * @see CalendarSolution_List::set_show_own_events_frist()
	 * @see CalendarSolution_List_Calendar::set_show_location()
	 *
	 * @uses CalendarSolution_List::set_request_properties()  to determine the
	 *       user's intention, but only if it has not been called yet
	 * @uses CalendarSolution_List::set_permit_history_months()  to limit how
	 *       far back people can see, but only if it has not been called yet
	 * @uses CalendarSolution_List::set_permit_future_months()  to limit how
	 *       far ahead people can see, but only if it has not been called yet
	 *
	 * @uses CalendarSolution_List::set_where_sql()  to generate the WHERE
	 *       clause and cache keys
	 * @uses CalendarSolution::$cache  to cache the output of the default view,
	 *       if possible
	 * @uses CalendarSolution_List::run_query()  to obtain non-cached data
	 */
	public function get_rendering() {
		if (!$this->called_set_request_properties) {
			$this->set_request_properties();
		}
		if ($this->permit_history_date === null) {
			$this->set_permit_history_months();
		}
		if ($this->permit_future_date === null) {
			$this->set_permit_future_months();
		}

		if ($this->use_cache) {
			$this->set_where_sql();

			$cache_key = $this->cache_key . ':' . $this->view;

			$out = $this->cache->get($cache_key);
			if ($out !== false) {
				return $out;
			}
		}

		$this->run_query();

// This doesn't work.  For example, DateTime::add() fails.
//        $current_date_time = $this->from;
		$current_date_time = new DateTimeSolution($this->from->format('Y-m-d'));

		$months = $this->calculate_months($current_date_time, $this->to);
		$one_day_interval = new DateIntervalSolution('P1D');
		$out = '';
		$event = array_shift($this->data);

		for ($month_counter = 0; $month_counter < $months; $month_counter++) {
			$out .= $this->get_month_open($current_date_time);

			$days_in_month = $current_date_time->format('t');
			for ($day_counter = 1; $day_counter <= $days_in_month; $day_counter++) {
				$out .= '<td>' . $day_counter;

				$the_date = $current_date_time->format('Y-m-d');
				if ($the_date == $event['date_start']) {
					do {
						$out .= $this->get_event_formatted($event);
						$event = array_shift($this->data);
					} while ($the_date == $event['date_start']);
				}

				$out .= "</td>\n";

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

		$out .= $this->get_credit();

		if ($this->use_cache) {
			$this->cache->set($cache_key, $out);
		}

		return $out;
	}
}
