<?php

/**
 * Calendar Solution's means to output collections of events formatted as a
 * table with significant info about each event
 *
 * @package CalendarSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2002-2011
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 */

/**
 * The means to output collections of events formatted as a table with
 * significant info about each event
 *
 * @package CalendarSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2002-2011
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 */
class CalendarSolution_List_List extends CalendarSolution_List {
	/**
	 * Format for PHP's date() function, to be used by our format_date() method
	 *
	 * @see CalendarSolution_List::set_date_format()
	 * @see CalendarSolution::format_date()
	 * @var string
	 */
	protected $date_format = self::DATE_FORMAT_LONG;

	/**
	 * The type of view this class represents
	 * @var string
	 */
	protected $view = 'List';


	/**
	 * @param array $event  an associative array of a given event
	 * @param string $class  the CSS class name for this row
	 *
	 * @return string  the HTML for one event
	 */
	protected function get_event_formatted($event, $class) {
		/*
		 * NOTE: SQL Solution runs the output through htmlspecialchars(),
		 * so there is no need to do it here.
		 */

		$out = '  <td class="cs_title">'
			. $this->get_link($event)
			. "</td>\n";

		$out .= '  <td class="cs_day">'
			 . $this->format_date($event['date_start'], $this->date_format)
			 . "</td>\n";

		$out .= '  <td class="cs_time">'
			 . (($event['time_start']) ? $this->format_date($event['time_start'], self::DATE_FORMAT_TIME_12AP) : '&nbsp;')
			 . (($event['time_end']) ? ' to ' . $this->format_date($event['time_end'], self::DATE_FORMAT_TIME_12AP) : '')
			 . "</td>\n";

		$out .= '  <td class="cs_location_start">'
			 . (($event['location_start']) ? $event['location_start'] : '&nbsp;')
			 . "</td>\n";

		$out .= '  <td class="cs_status">' . $event['status'];
		if ($event['changed'] == 'Y'
			&& $event['status_id'] != self::STATUS_CANCELLED)
		{
			$out .= ' &amp; Changed';
		}
		$out .= "</td>\n";

		if ($event['summary'] != '' && $this->show_summary) {
			$out .= $this->get_row_close()
				 . $this->get_row_open($class)
				 . '  <td class="cs_summary" colspan="5">'
				 . $event['summary'] . "</td>\n";
		}

		return $out;
	}

	/**
	 * @return string  the HTML for closing a list
	 */
	protected function get_list_close() {
		return "</table>\n";
	}

	/**
	 * @return string  the HTML for opening a list
	 */
	protected function get_list_open() {
		return '<table class="cs_list_list">' . "\n";
	}

	/**
	 * @return string  the HTML closing out a month
	 */
	protected function get_month_close() {
		return "\n";
	}

	/**
	 * @return string  the HTML of the month header
	 */
	protected function get_month_open(DateTime $current_date_time) {
		$out = " <tr>\n"
			. '  <td class="cs_month" colspan="4">' . "\n"
			. '   <big><b>'
			. $current_date_time->format('F') . ' '
			. $current_date_time->format('Y')
			. "</b></big>\n  </td>\n </tr>\n";

		return $out;
	}

	/**
	 * @return string  the HTML for closing a row
	 */
	protected function get_row_close() {
		return " </tr>\n";
	}

	/**
	 * @param string $class  the CSS class name for this row
	 * @return string  the HTML for opening a row
	 */
	protected function get_row_open($class) {
		return ' <tr class="' . $class . '">' . "\n";
	}

	/**
	 * Produces a list of events laid out in a list format
	 *
	 * @return string  the complete HTML of the events and the related interface
	 *
	 * @see CalendarSolution_List::set_date_format()
	 * @see CalendarSolution_List::get_limit_form()
	 * @see CalendarSolution_List::get_date_navigation()
	 * @see CalendarSolution_List::get_change_view()
	 *
	 * @uses CalendarSolution_List::run_query()
	 */
	public function get_rendering() {
		if (!$this->called_set_request_properties) {
			$this->set_request_properties();
		}

		$this->run_query();

		$out = $this->get_list_open();

		$prior_event_month = '';

		foreach ($this->data as $counter => $event) {
			if ($event['status_id'] == self::STATUS_CANCELLED) {
				$class = 'cs_X';
			} elseif ($event['changed'] == 'Y') {
				$class = 'cs_Y';
			} else {
				$class = 'cs_N';
			}
			$class .= ($counter % 2);

			$event_month = substr($event['date_start'], 0, 7);
			if ($prior_event_month != $event_month) {
				$event_date = new DateTimeSolution($event['date_start']);
				if ($counter == 0) {
					$out .= $this->get_month_open($event_date);
				} else {
					$out .= $this->get_month_close();
					$out .= $this->get_month_open($event_date);
				}
			}
			$prior_event_month = $event_month;

			$out .= $this->get_row_open($class);
			$out .= $this->get_event_formatted($event, $class);
			$out .= $this->get_row_close();
		}

		$out .= $this->get_list_close();
		$out .= $this->get_credit();

		return $out;
	}
}
