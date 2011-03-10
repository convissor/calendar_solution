<?php

/**
 * Calendar Solution's means to output collections of events formatted as a
 * list of the date and name of each event
 *
 * @package CalendarSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2002-2011
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 */

/**
 * The means to output collections of events formatted as a list
 * of the date and name of each event
 *
 * Intended to show a limited number of occurrences of Featured Events on
 * Home Pages or other Featured Pages
 *
 * The limit should be established using set_limit().
 *
 * @see CalendarSolution_List_Title::get_rendering()
 *
 * @package CalendarSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2002-2011
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 */
class CalendarSolution_List_Title extends CalendarSolution_List {
	/**
	 * Format for PHP's date() function, to be used by our format_date() method
	 *
	 * @see CalendarSolution_List::set_date_format()
	 * @see CalendarSolution::format_date()
	 * @var string
	 */
	protected $date_format = self::DATE_FORMAT_SHORT;

	/**
	 * The type of view this class represents
	 * @var string
	 */
	protected $view = 'Title';


	/**
	 * Provides the path and name of the needed Cascading Style Sheet file
	 *
	 * @return string  the path and name of the CSS file
	 */
	public function get_css_name() {
		return dirname(__FILE__) . '/Title.css';
	}

	/**
	 * @return string  the HTML for opening a list
	 */
	protected function get_list_open() {
		return '<table class="cs_list_title">' . "\n";
	}

	/**
	 * @return string  the HTML for closing a list
	 */
	protected function get_list_close() {
		return "</table>\n";
	}

	/**
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
	 *
	 * @return string  the HTML for one event
	 */
	protected function get_event_formatted($event) {
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
	 * Produces a list of events laid out in a short list format
	 *
	 * NOTE: this view never shows cancelled events.
	 *
	 * Cascading Style Sheet notes:  the list is contained within
	 * "table.cs_list_title".  Each event is wrapped by a "tr"
	 * element, which has multiple class attributes:
	 * + Row: cs_row_0, cs_row_1
	 * + Status (Open, Full, Cancelled): cs_status_O, cs_status_F, cs_status_C
	 * + Changed: cs_changed_Y, cs_changed_N
	 * + Organizer: cs_is_own_event_Y, cs_is_own_event_N
	 *
	 * @param int $page_id  the feature_on_page_id to limit the list to, if any
	 *
	 * @return string  the HTML for displaying the events
	 *
	 * @see CalendarSolution_List::set_limit()
	 * @see CalendarSolution_List::get_limit_navigation()
	 * @see CalendarSolution_List::set_date_format()
	 * @see CalendarSolution_List::set_show_own_events_frist()
	 *
	 * @uses CalendarSolution_List::set_from()  to default the date to today,
	 *       but only if it has not been called yet
	 * @uses CalendarSolution_List::set_permit_future_months()  to limit how
	 *       far ahead people can see, but only if it has not been called yet
	 * @uses CalendarSolution_List::set_show_cancelled()  to drop cancelled
	 *       events from the display
	 * @uses CalendarSolution_List::set_page_id()  if "$page_id" is passed
	 *
	 * @uses CalendarSolution_List::set_where_sql()  to generate the WHERE
	 *       clause and cache keys
	 * @uses CalendarSolution::$cache  to cache the output, if possible
	 * @uses CalendarSolution_List::run_query()  to obtain non-cached data
	 */
	public function get_rendering($page_id = null) {
		if ($page_id) {
			$this->set_page_id($page_id);
		}

		if ($this->from === null) {
			$this->set_from(date('Y-m-d'));
		}
		if ($this->permit_future_date === null) {
			$this->set_permit_future_months();
		}

		$this->set_show_cancelled(false);

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

		foreach ($this->data as $counter => $event) {
			$class = 'cs_status_' . substr($event['status'], 0, 1)
				. ' cs_is_own_event_' . $event['is_own_event']
				. ' cs_changed_' . $event['changed']
				. ' cs_row_' . ($counter % 2);

			$out .= $this->get_row_open($class);
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
