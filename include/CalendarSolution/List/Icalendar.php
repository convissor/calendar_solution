<?php

/**
 * Calendar Solution's means to output collections of events formatted as a
 * set of iCalendar VEVENT entries
 *
 * @package CalendarSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2002-2012
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 */

/**
 * The means to output collections of events formatted as a
 * set of iCalendar VEVENT entries
 *
 * Intended to show all events in a date range.
 *
 * See {@link CalendarSolution_List_Icalendar::get_rendering()} for details.
 *
 * @package CalendarSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2002-2012
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
	 * Produces a list of events in iCalendar format
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
	 * @return string  the text displaying the events
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
