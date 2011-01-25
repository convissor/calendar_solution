<?php

/**
 * Calendar Solution's means to output collections of events formatted as a
 * list of the date and name of each event
 *
 * Intended for use to display Featured Events on Home Pages or other top
 * level pages.
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
 * Intended for use to display Featured Events on Home Pages or other top
 * level pages.
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
	 * @param array $event  an associative array of a given event
	 *
	 * @return string  the HTML for one event
	 */
	protected function get_event_formatted($event) {
		/*
		 * NOTE: SQL Solution runs the output through htmlspecialchars(),
		 * so there is no need to do it here.
		 */

		$out =  '<td class="day">'
			. $this->format_date($event['date_start'], $this->date_format)
			. '</td> '
			. '<td class="title">' . $this->get_link($event) . '</td>';

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
		return '<table class="cs_list_title">' . "\n";
	}

	/**
	 * Produces a list of events laid out in a short list format
	 *
	 * @param int $page_id  the feature_on_page_id to limit the list to, if any
	 *
	 * @return string  the complete HTML of the events and the related interface
	 *
	 * @see CalendarSolution_List::set_date_format()
	 * @uses CalendarSolution_List::set_page_id()  if $page_id is passed
	 * @uses CalendarSolution_List::set_from()  to default the date to today
	 * @uses CalendarSolution_List::set_show_cancelled()  to drop cancelled
	 *       events from the display
	 * @uses CalendarSolution_List::run_query()  to obtain the data
	 * @uses CalendarSolution_List_Title::get_list_open()  to open the set
	 * @uses CalendarSolution_List_Title::get_row_open()  to open the element
	 * @uses CalendarSolution_List_Title::get_event_formatted()  to format
	 *       each event
	 * @uses CalendarSolution_List_Title::get_row_close()  to close the element
	 * @uses CalendarSolution_List_Title::get_list_close()  to close the set
	 */
	public function get_rendering($page_id = null) {
		if ($page_id) {
			$this->set_page_id($page_id);
		}

		if ($this->from === null) {
			$this->set_from();
		}

		$this->set_show_cancelled(false);

		$this->run_query();

		$out = $this->get_list_open();

		foreach ($this->data as $event) {
			$out .= $this->get_row_open();
			$out .= $this->get_event_formatted($event);
			$out .= $this->get_row_close();
		}

		$out .= $this->get_list_close();

		return $out;
	}

	/**
	 * @return string  the HTML for closing a row
	 */
	protected function get_row_close() {
		return "</tr>\n";
	}

	/**
	 * @return string  the HTML for opening a row
	 */
	protected function get_row_open() {
		return ' <tr>';
	}
}
