<?php

/**
 * Calendar Solution's means to output collections of events formatted as a
 * table of the date and name of each event and is grouped by month
 *
 * @package CalendarSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2002-2012
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 */

/**
 * The means to output collections of events formatted as a table
 * of the date and name of each event and is grouped by month
 *
 * Intended to show all events in a date range.
 *
 * See {@link CalendarSolution_List_MonthTitle::get_rendering()} for details.
 *
 * @package CalendarSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2002-2012
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 *
 * @since Class available since version 3.0
 */
class CalendarSolution_List_MonthTitle extends CalendarSolution_List {
	/**
	 * Format for PHP's date() function, to be used by our format_date() method
	 *
	 * @see CalendarSolution_List::set_date_format()
	 * @see CalendarSolution::format_date()
	 * @var string
	 */
	protected $date_format = self::DATE_FORMAT_NAME_NUMBER;

	/**
	 * The type of view this class represents
	 * @var string
	 */
	protected $view = 'MonthTitle';


	/**
	 * Provides the path and name of the needed Cascading Style Sheet file
	 *
	 * @return string  the path and name of the CSS file
	 */
	public function get_css_name() {
		return dirname(__FILE__) . '/MonthTitle.css';
	}

	/**
	 * @return string  the HTML for opening a list
	 */
	protected function get_list_open() {
		return '<table class="cs_list_monthtitle">' . "\n";
	}

	/**
	 * @return string  the HTML for closing a list
	 */
	protected function get_list_close() {
		return "</table>\n";
	}

	/**
	 * @return string  the HTML of the month header
	 */
	protected function get_month_open(DateTime $current_date_time) {
		$out = '<tr><td class="cs_month" colspan="2">'
			. $current_date_time->format('F') . ' '
			. $current_date_time->format('Y')
			. "</td></tr>\n";

		return $out;
	}

	/**
	 * @return string  the HTML closing out a month
	 */
	protected function get_month_close() {
		return "\n";
	}

	/**
	 * @param string $class  the CSS class name for this row
	 * @return string  the HTML for opening a row
	 */
	protected function get_row_open($class) {
		return '<tr class="' . $class . '">';
	}

	/**
	 * @return string  the HTML for closing a row
	 */
	protected function get_row_close() {
		return "</tr>\n";
	}

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

		$out =  '<td class="cs_day">'
			. $this->format_date($event['date_start'], $this->date_format)
			. '</td>'
			. '<td class="cs_title">' . $this->get_link($event) . '</td>';

		return $out;
	}

	/**
	 * Produces a table of events showing their dates and names, grouped by
	 * month
	 *
	 * Intended to show all events in a date range.
	 *
	 * Defaults to showing all events between today and the last day of the
	 * month two months from today.  The date range can be adjusted using
	 * set_from() and set_to().
	 *
	 * This method automatically checks web browsers' requests to determine
	 * what data each user is looking for.  See
	 * {@link CalendarSolution_List::set_request_properties()} for specifics.
	 *
	 * Cascading Style Sheet notes:  the list is contained within
	 * "table.cs_list_monthtitle".  Each event is wrapped by a "tr"
	 * element, which has multiple class attributes:
	 * + Row: cs_row_0, cs_row_1
	 * + Status (Open, Full, Cancelled): cs_status_O, cs_status_F, cs_status_C
	 * + Changed: cs_changed_Y, cs_changed_N
	 * + Organizer: cs_is_own_event_Y, cs_is_own_event_N
	 *
	 * @return string  the HTML for displaying the events
	 *
	 * @see CalendarSolution_List::set_date_format()
	 * @see CalendarSolution_List::get_limit_form()
	 * @see CalendarSolution_List::get_date_navigation()
	 * @see CalendarSolution_List::set_show_own_events_first()
	 *
	 * @uses CalendarSolution_List::set_request_properties()  to determine the
	 *       user's intention, but only if it has not been called yet
	 * @uses CalendarSolution_List::set_permit_history_months()  to limit how
	 *       far back people can see, but only if it has not been called yet
	 * @uses CalendarSolution_List::set_permit_future_months()  to limit how
	 *       far ahead people can see, but only if it has not been called yet
	 *
	 * @internal Uses {@link CalendarSolution_List::set_where_sql()} to
	 *           generate the WHERE clause and cache keys
	 * @internal Caches output in {@link CalendarSolution::$cache}, if enabled
	 * @internal Uses {@link CalendarSolution_List::run_query()} to obtain the
	 *           data if it is not in the cache yet or caching is not enabled.
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

			$cache_key = $this->cache_key . ':' . $this->view . ':'
				. $this->date_format;

			$out = $this->cache->get($cache_key);
			if ($out !== false) {
				return $out;
			}
		}

		$this->run_query();

		$out = $this->get_list_open();

		$prior_event_month = '';

		foreach ($this->data as $counter => $event) {
			$class = 'cs_status_' . substr($event['status'], 0, 1)
				. ' cs_is_own_event_' . $event['is_own_event']
				. ' cs_changed_' . $event['changed']
				. ' cs_row_' . ($counter % 2);

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

		if ($this->use_cache) {
			$this->cache->set($cache_key, $out);
		}

		return $out;
	}
}
