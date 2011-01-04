<?php

/**
 * Calendar Solution's means to view a specific event in HTML format
 *
 * @package CalendarSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2002-2010
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 */

/**
 * The means to view a specific event in HTML format
 *
 * @package CalendarSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2002-2010
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 */
class CalendarSolution_Detail_Html extends CalendarSolution_Detail {
	/**
	 * Provides the path and name of the needed Cascading Style Sheet file
	 *
	 * @return string  the path and name of the CSS file
	 */
	public function get_css_name() {
		return dirname(__FILE__) . '/Html.css';
	}

	/**
	 * Obtains the title of the event the user wants to see
	 *
	 * @return string  the event's title
	 *
	 * @uses CalendarSolution_Detail::run_query()  to get the info from the
	 *       database if it hasn't been extracted yet
	 */
	public function get_title() {
		if (empty($this->data)) {
			$this->set_data_from_query();
		}
		return $this->data['title'];
	}

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
	 */
	public function get_rendering($calendar_id = null) {
		if (empty($this->data)) {
			$this->set_data_from_query($calendar_id);
		}

		/*
		 * NOTE: SQL Solution runs the output through htmlspecialchars(),
		 * so there is no need to do it here.
		 */

		$out = '<table class="cs_detail_html"';
		$out .= ' summary="Calender item details. Left column contains';
		$out .= " field headings. Right column contains the information.\">\n";

		$out .= ' <tr><td scope="row" class="cs_label">Title:</td>'
			 . '<td class="cs_value" nowrap="nowrap">';
		if ($this->data['display_uri']) {
			$out .= '<a href="' . $this->data['display_uri'] . '">'
				. $this->data['title'] . '</a>';
		} else {
			$out .= $this->data['title'];
		}
		$out .= "</td></tr>\n";

		$out .= ' <tr><td scope="row" class="cs_label">Status:</td>'
			 . '<td class="cs_value">';
		if ($this->data['status_id'] == self::STATUS_OPEN) {
			$out .= $this->data['status'];
		} else {
			$out .= '<em>' . $this->data['status'] . '</em>';
		}
		$out .= "</td></tr>\n";

		if ($this->data['changed'] == 'Y') {
			$out .= ' <tr><td scope="row" class="cs_label">Changed:</td>'
				 . '<td class="cs_value"><em>NOTICE: changes have been made to this event since it was first posted.</em></td></tr>' . "\n";
		}

		$out .= ' <tr><td scope="row" class="cs_label">Date:</td>'
			 . '<td class="cs_value">'
			 . $this->format_date($this->data['date_start'], self::DATE_FORMAT_FULL)
			 . "</td></tr>\n";

		if ($this->data['time_start']) {
			$out .= ' <tr><td scope="row" class="cs_label" nowrap="nowrap">Time:</td>'
				 . '<td class="cs_value">'
				 . $this->format_date($this->data['time_start'], self::DATE_FORMAT_TIME_12AP);
			if ($this->data['time_end']) {
				$out .= ' to ' . $this->format_date($this->data['time_end'], self::DATE_FORMAT_TIME_12AP);
			}
			$out .= "</td></tr>\n";
		}

		if ($this->data['location_start']) {
			$out .= ' <tr><td scope="row" class="cs_label">Location:</td>'
				 . '<td class="cs_value">' . $this->data['location_start'] . "</td></tr>\n";
		}

		if ($this->data['summary']) {
			$out .= ' <tr><td scope="row" class="cs_label">Summary:</td>'
				 . '<td class="cs_value">' . $this->data['summary'] . "</td></tr>\n";
		}

		if ($this->data['detail']) {
			$out .= ' <tr><td scope="row" class="cs_label">Details:</td>'
				 . '<td class="cs_value">' . $this->data['detail'] . "</td></tr>\n";
		}

		if ($this->data['note']) {
			$out .= ' <tr><td scope="row" class="cs_label">Notes:</td>'
				 . '<td class="cs_value">' . $this->data['note'] . "</td></tr>\n";
		}

		if ($this->data['category']) {
			$out .= ' <tr><td scope="row" class="cs_label">Category:</td>'
				 . '<td class="cs_value">' . $this->data['category'] . "</td></tr>\n";
		}

		$out .= '</table>';

		$out .= '<p class="cs_view_all"><a href="calendar.php">View All Events</a>';

		$out .= $this->get_credit();

		return $out;
	}
}
