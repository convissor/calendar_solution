<?php

/**
 * Calendar Solution's means to output collections of events formatted as a
 * table with details about each entry
 *
 * @package CalendarSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2002-2011
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 */

/**
 * The means to output collections of events formatted as a table with
 * details about each entry
 *
 * Intended to show a limited number of occurrences of events in a category.
 *
 * See {@link CalendarSolution_List_DetailTable::get_rendering()} for details.
 *
 * @package CalendarSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2002-2011
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 *
 * @since Class available since version 3.4
 */
class CalendarSolution_List_DetailTable extends CalendarSolution_List {
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
	protected $view = 'DetailTable';

	/**
	 * Should the End Time field be shown or not?
	 * @var bool
	 */
	protected $show_time_end = true;


	/**
	 * Should End Time field be shown or not?
	 *
	 * @param bool $in  set it to FALSE to leave out end times
	 *
	 * @return void
	 *
	 * @uses CalendarSolution_List_DetailTable::$show_time_end  to store the
	 *       decision
	 */
	public function set_show_time_end($in) {
		$this->show_time_end = (bool) $in;
	}

	/**
	 * Provides the path and name of the needed Cascading Style Sheet file
	 *
	 * @return string  the path and name of the CSS file
	 */
	public function get_css_name() {
		return dirname(__FILE__) . '/DetailTable.css';
	}

	/**
	 * @return string  the HTML for opening a list
	 */
	protected function get_list_open() {
		$out = '<table class="cs_list_detailtable">'
			. '<tr class="cs_header"><th>Title</th><th>Date</th><th>Time</th>'
			. '<th>Location</th><th>Notes</th></tr>' . "\n";
		return $out;
	}

	/**
	 * @return string  the HTML for closing a list
	 */
	protected function get_list_close() {
		return "</table>\n";
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
	 *
	 * @return string  the HTML for one event
	 */
	protected function get_event_formatted($event) {
		/*
		 * NOTE: SQL Solution runs the output through htmlspecialchars(),
		 * so there is no need to do it here.
		 */

		$out = '<td class="cs_title">' . $this->get_link($event) . '</td>';

		$out .= '<td class="cs_day">'
			 . $this->format_date($event['date_start'], $this->date_format)
			 . '</td>';

		$out .= '<td class="cs_time">';
		if ($event['time_start']) {
			$out .= $this->format_date($event['time_start'], $this->time_format);
			if ($event['time_end'] && $this->show_time_end) {
				$out .= ' - ' . $this->format_date($event['time_end'], $this->time_format);
			}
		} else {
			$out .= '&nbsp;';
		}
		$out .= '</td>';

		$out .= '<td class="cs_location_start">'
			 . (($event['location_start']) ? $event['location_start'] : '&nbsp;')
			 . '</td>';

		$out .= '<td class="cs_note">'
			 . (($event['note']) ? $event['note'] : '&nbsp;')
			 . '</td>';

		return $out;
	}

	/**
	 * Produces a table of events showing basic info about each entry
	 *
	 * Intended to show a limited number of occurrences of a particular Frequent
	 * Event on pages containing comprehensive information about that Event.
	 *
	 * NOTE: This method does NOT automatically check web browsers' requests
	 * for what data each user is looking for.  When using
	 * {@link CalendarSolution_List::get_date_navigation()} or
	 * {@link CalendarSolution_List::get_limit_form()} on the BOTTOM of the
	 * calendar output, it is necessary to manually execute
	 * {@link CalendarSolution_List::set_request_properties()} BEFORE calling
	 * this method.
	 *
	 * Cascading Style Sheet notes:  the list is contained within
	 * "table.cs_list_detailtable".  Each event is wrapped by a "tr"
	 * element, which has multiple class attributes:
	 * + Row: cs_row_0, cs_row_1
	 * + Status (Open, Full, Cancelled): cs_status_O, cs_status_F, cs_status_C
	 * + Changed: cs_changed_Y, cs_changed_N
	 * + Organizer: cs_is_own_event_Y, cs_is_own_event_N
	 *
	 * @param int $frequent_event_id  the frequent_event_id to limit the list
	 *                                to, if any
	 *
	 * @return string  the HTML for displaying the events
	 *
	 * @see CalendarSolution_List::set_limit()
	 * @see CalendarSolution_List::get_limit_navigation()
	 * @see CalendarSolution_List::set_date_format()
	 * @see CalendarSolution_List::set_time_format()
	 * @see CalendarSolution_List::set_show_own_events_first()
	 * @see CalendarSolution_List_DetailTable::set_show_time_end()
	 *
	 * @uses CalendarSolution_List::set_from()  to default the date to today,
	 *       but only if it has not been called yet
	 * @uses CalendarSolution_List::set_permit_future_months()  to limit how
	 *       far ahead people can see, but only if it has not been called yet
	 * @uses CalendarSolution_List::set_frequent_event_id()  if
	 *       "$frequent_event_id" is passed
	 *
	 * @internal Uses {@link CalendarSolution_List::set_where_sql()} to
	 *           generate the WHERE clause and cache keys
	 * @internal Caches output in {@link CalendarSolution::$cache}, if enabled
	 * @internal Uses {@link CalendarSolution_List::run_query()} to obtain the
	 *           data if it is not in the cache yet or caching is not enabled.
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

			$cache_key = $this->cache_key . ':' . $this->view . ':'
				. $this->date_format . ':' . $this->time_format;

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
			} elseif ($event['changed'] == 'Y') {
				$event['note'] = 'CHANGED. ' . $event['note'];
			}
			if ($event['status_id'] == self::STATUS_FULL) {
				$event['note'] = 'FULL. ' . $event['note'];
			}

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
