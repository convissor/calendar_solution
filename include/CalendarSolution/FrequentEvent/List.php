<?php

/**
 * Calendar Solution's means to list Frequent Events
 *
 * @package CalendarSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2002-2011
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 */

/**
 * The means to output a list of Frequent Events
 *
 * @package CalendarSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2002-2011
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 */
class CalendarSolution_FrequentEvent_List extends CalendarSolution_FrequentEvent {
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

		$out = '  <td class="cs_title">'
			. '<a href="frequent_event-detail.php?frequent_event_id='
			. $event['frequent_event_id'] . '">'
			. $event['frequent_event']
			. "</a></td>\n";

		$out .= '  <td class="cs_uri">'
			 . (($event['frequent_event_uri']) ? $event['frequent_event_uri'] : '&nbsp;')
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
		return '<table class="cs_list">' . "\n";
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
	 * @uses CalendarSolution_FrequentEvent_List::run_query()
	 */
	public function get_rendering() {
		$this->run_query();

		$class = 'cs_row';
		$out = $this->get_list_open();

		for ($counter = 0; $counter < $this->sql->SQLRecordSetRowCount; $counter++) {
			$event = $this->sql->RecordAsAssocArray(__FILE__, __LINE__,
				array('frequent_event_uri'));

			$out .= $this->get_row_open($class);
			$out .= $this->get_event_formatted($event);
			$out .= $this->get_row_close();
		}

		$out .= $this->get_list_close();

		return $out;
	}

	/**
	 * Assembles the query string then executes it
	 *
	 * @return void
	 *
	 * @throws CalendarSolution_Exception if to is later than from
	 */
	protected function run_query() {
		$this->sql->SQLQueryString = "SELECT
			frequent_event_id,
			frequent_event,
			frequent_event_uri
			FROM cs_frequent_event
			ORDER BY frequent_event";

		$this->sql->RunQuery(__FILE__, __LINE__);
	}
}
