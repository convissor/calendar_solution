<?php

/**
 * Calendar Solution's means to view a specific event in iCalendar format
 *
 * @package CalendarSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2002-2012
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 */

/**
 * The means to view a specific event in iCalendar format
 *
 * Formatting information came from http://tools.ietf.org/html/rfc5545
 *
 * @package CalendarSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2002-2012
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 *
 * @since Class available since version 3.3
 */
class CalendarSolution_Detail_Icalendar extends CalendarSolution_Detail {
	/**
	 * Produces the HTML to display the event the user wants to see
	 *
	 * @param int $calendar_id  the id number of the item to get (defaults to
	 *                          $_REQUEST['calendar_id'])
	 *
	 * @return string  the complete HTML of the desired event
	 *
	 * @uses CalendarSolution_Detail::run_query()  to get the info from the
	 *       database if it hasn't been extracted yet
	 * @uses CalendarSolution::get_event_formatted_icalendar()  for formatting
	 */
	public function get_rendering($calendar_id = null) {
		$this->set_data_from_query($calendar_id, true, false);

		$out = "BEGIN:VCALENDAR\r\nBEGIN:VEVENT\r\n";
		$out .= $this->get_event_formatted_icalendar($this->data);
		$out .= "END:VEVENT\r\nEND:VCALENDAR\r\n";

		return $out;
	}
}
