<?php

/**
 * Calendar Solution's means to output collections of events formatted as a
 * set of iCalendar VEVENT entries
 *
 * @package CalendarSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2002-2011
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 */

/**
 * The means to output collections of events formatted as a
 * set of iCalendar VEVENT entries
 *
 * Intended to show all events between today and a specified end date.
 * Defaults to today through the last day of the month two months from now.
 *
 * The date limits can be changed using set_from() and set_to().
 *
 * @see CalendarSolution_List_Icalendar::get_rendering()
 *
 * @package CalendarSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2002-2011
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 *
 * @since Class available since version 3.3
 */
class CalendarSolution_List_Icalendar extends CalendarSolution_List {
	/**
	 * The type of view this class represents
	 * @var string
	 */
	protected $view = 'Icalendar';


	/**
	 * @return string  the HTML for opening a list
	 */
	protected function get_list_open() {
		return "BEGIN:VCALENDAR\r\n";
	}

	/**
	 * @return string  the HTML for closing a list
	 */
	protected function get_list_close() {
		return "END:VCALENDAR\r\n";
	}

	/**
	 * @return string  the HTML for opening a row
	 */
	protected function get_row_open() {
		return "BEGIN:VEVENT\r\n";
	}

	/**
	 * @return string  the HTML for closing a row
	 */
	protected function get_row_close() {
		return "END:VEVENT\r\n";
	}

	/**
	 * @param array $event  an associative array of a given event
	 *
	 * @return string  the iCalendar formatted event
	 *
	 * @uses CalendarSolution::get_event_formatted_icalendar()  for formatting
	 */
	protected function get_event_formatted($event) {
		return $this->get_event_formatted_icalendar($event);
	}

	/**
	 * Produces a list of events laid out in iCalendar format
	 *
	 * @return string  the text displaying the events
	 *
	 * @see CalendarSolution_List::set_show_own_events_frist()
	 * @see CalendarSolution_List_List::set_show_summary()
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

			$cache_key = $this->cache_key . ':' . $this->view . ':'
				. $this->date_format;

			$out = $this->cache->get($cache_key);
			if ($out !== false) {
				return $out;
			}
		}

		$original_safe_markup = $this->sql->SQLSafeMarkup;
		$original_escape_html = $this->sql->SQLEscapeHTML;
		$this->sql->SQLSafeMarkup = 'Y';
		$this->sql->SQLEscapeHTML = 'N';

		$this->run_query();

		$this->sql->SQLSafeMarkup = $original_safe_markup;
		$this->sql->SQLEscapeHTML = $original_escape_html;

		$out = $this->get_list_open();

		foreach ($this->data as $counter => $event) {
			$out .= $this->get_row_open();
			$out .= $this->get_event_formatted($event);
			$out .= $this->get_row_close();
		}

		$out .= $this->get_list_close();

		if ($this->use_cache) {
			$this->cache->set($cache_key, $out);
		}

		return $out;
	}
}
