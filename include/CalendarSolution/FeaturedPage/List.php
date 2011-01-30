<?php

/**
 * Calendar Solution's means to list Featured Pages
 *
 * @package CalendarSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2002-2011
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 */

/**
 * The means to output a list of Featured Pages
 *
 * @package CalendarSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2002-2011
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 */
class CalendarSolution_FeaturedPage_List extends CalendarSolution_FeaturedPage {
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

		$out = '  <td class="id">' . $event['feature_on_page_id'] . "</td>\n";

		$out .= '  <td class="title">'
			. '<a href="featured_page-detail.php?feature_on_page_id='
			. $event['feature_on_page_id'] . '">'
			. $event['feature_on_page']
			. "</a></td>\n";

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
	 * @uses CalendarSolution_FeaturedPage_List::run_query()
	 */
	public function get_rendering() {
		$this->run_query();

		$class = 'row';
		$out = $this->get_list_open();

		for ($counter = 0; $counter < $this->sql->SQLRecordSetRowCount; $counter++) {
			$event = $this->sql->RecordAsAssocArray(__FILE__, __LINE__,
				array('feature_on_page_uri'));

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
			feature_on_page_id,
			feature_on_page
			FROM cs_feature_on_page
			ORDER BY feature_on_page";

		$this->sql->RunQuery(__FILE__, __LINE__);
	}
}
