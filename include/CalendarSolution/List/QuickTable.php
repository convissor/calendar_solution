<?php

/**
 * Calendar Solution's means to output collections of events formatted as a
 * table with brief info about each entry
 *
 * Intended for use on pages that provide comprehensive information about
 * a particular Frequent Event.
 *
 * @package CalendarSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2002-2011
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 */

/**
 * The means to output collections of events formatted as a table with
 * brief info about each entry
 *
 * Intended for use on pages that provide comprehensive information about
 * a particular Frequent Event.
 *
 * @see CalendarSolution_List_QuickTable::get_rendering()
 *
 * @package CalendarSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2002-2011
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 */
class CalendarSolution_List_QuickTable extends CalendarSolution_List {
	/**
	 * Format for PHP's date() function, to be used by our format_date() method
	 *
	 * @see CalendarSolution_List::set_date_format()
	 * @see CalendarSolution::format_date()
	 * @var string
	 */
	protected $date_format = self::DATE_FORMAT_MEDIUM;

	/**
	 * The type of view this class represents
	 * @var string
	 */
	protected $view = 'QuickTable';

	/**
	 * Should the Summary field be shown or not?
	 * @var bool
	 */
	protected $show_summary = true;


	/**
	 * Provides the path and name of the needed Cascading Style Sheet file
	 *
	 * @return string  the path and name of the CSS file
	 */
	public function get_css_name() {
		return dirname(__FILE__) . '/QuickTable.css';
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

		$out = '  <td class="cs_day">'
			 . $this->format_date($event['date_start'], $this->date_format)
			 . "</td>\n";

		$out .= '  <td class="cs_time">'
			 . (($event['time_start']) ? $this->format_date($event['time_start'], self::DATE_FORMAT_TIME_12AP) : '&nbsp;')
			 . (($event['time_end']) ? ' - ' . $this->format_date($event['time_end'], self::DATE_FORMAT_TIME_12AP) : '')
			 . "</td>\n";

		$out .= '  <td class="cs_location_start">'
			 . (($event['location_start']) ? $event['location_start'] : '&nbsp;')
			 . "</td>\n";

		$out .= '  <td class="cs_note">'
			 . (($event['note']) ? $event['note'] : '&nbsp;')
			 . "</td>\n";

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
		$out = '<table class="cs_list_quicktable">' . "\n";
		$out .= ' <tr><th>Date</th><th>Time</th>'
			. '<th>Location</th><th>Note</th></tr>' . "\n";
		return $out;
	}

	/**
	 * Produces a list of events laid out in a short table format
	 *
	 * @param int $frequent_event_id  the frequent_event_id to limit the list
	 *                                to, if any
	 *
	 * @return string  the HTML for displaying the events
	 *
	 * @see CalendarSolution_List::set_limit()
	 * @see CalendarSolution_List::get_limit_navigation()
	 * @see CalendarSolution_List::set_date_format()
	 *
	 * @uses CalendarSolution_List::set_from()  to default the date to today,
	 *       but only if it has not been called yet
	 * @uses CalendarSolution_List::set_permit_future_months()  to limit how
	 *       far ahead people can see, but only if it has not been called yet
	 * @uses CalendarSolution_List::set_frequent_event_id()  if
	 *       "$frequent_event_id" is passed
	 *
	 * @uses CalendarSolution_List::set_where_sql()  to generate the WHERE
	 *       clause and cache keys
	 * @uses CalendarSolution::$cache  to cache the output, if possible
	 * @uses CalendarSolution_List::run_query()  to obtain non-cached data
	 */
	public function get_rendering($frequent_event_id = null) {
		if ($frequent_event_id) {
			$this->set_frequent_event_id($frequent_event_id);
		}

		if ($this->from === null) {
			$this->set_from(date('Y-m-d'));
		}
		if ($this->permit_future_date === null) {
			$this->set_permit_future_months();
		}

		if ($this->use_cache) {
			$this->set_where_sql();

			$cache_key = $this->cache_key . ':quicktable:'
					. $this->date_format;

			$out = $this->cache->get($cache_key);
			if ($out !== false) {
				return $out;
			}
		}

		$this->run_query();

		$out = $this->get_list_open();

		foreach ($this->data as $counter => $event) {
			if ($event['status_id'] == self::STATUS_CANCELLED) {
				$event['note'] = 'CANCELLED. ' . $event['note'];
				$class = 'cs_X';
			} elseif ($event['changed'] == 'Y') {
				$event['note'] = 'CHANGED. ' . $event['note'];
				$class = 'cs_Y';
			} else {
				$class = 'cs_N';
			}
			$class .= ($counter % 2);

			if ($event['status_id'] == self::STATUS_FULL) {
				$event['note'] = 'FULL. ' . $event['note'];
			}

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
}
