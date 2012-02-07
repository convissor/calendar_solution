<?php

/**
 * Calendar Solution's parent class for viewing and editing a Frequent Event
 *
 * @package CalendarSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2002-2012
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 */

/**
 * The parent class for viewing and editing a Frequent Event
 *
 * @package CalendarSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2002-2012
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 */
abstract class CalendarSolution_FrequentEvent extends CalendarSolution {
	/**
	 * Assembles the query string, executes it and stores the result in
	 * $this->data
	 *
	 * @param int $frequent_event_id  the id number of the item to get
	 *                                (defaults to $_REQUEST['frequent_event_id'])
	 * @return void
	 *
	 * @uses SQLSolution_General::RunQuery()  to access the database
	 * @uses CalendarSolution::get_int_from_request()  to determine the
	 *       user's intention
	 *
	 * @throws CalendarSolution_Exception on an invalid $frequent_event_id or if
	 *         no matching record is found
	 */
	protected function run_query($frequent_event_id = null) {
		if ($frequent_event_id === null) {
			$frequent_event_id = $this->get_int_from_request('frequent_event_id');
		}

		if (empty($frequent_event_id)
			|| !preg_match('/^\d{1,10}$/', $frequent_event_id))
		{
			throw new CalendarSolution_Exception('Invalid $frequent_event_id');
		}

		$this->sql->SQLQueryString = "SELECT
			frequent_event_id,
			frequent_event,
			frequent_event_uri
			FROM cs_frequent_event
			WHERE frequent_event_id = $frequent_event_id";

		$this->sql->RunQuery(__FILE__, __LINE__);

		if ($this->sql->SQLRecordSetRowCount == 0) {
			throw new CalendarSolution_Exception('No events match your criteria');
		}
	}

	/**
	 * Populates $this->data with a record from the database
	 *
	 * @param int $frequent_event_id  the id number of the item to get
	 * @param bool $safe_markup  should Safe Markup be converted to HTML?
	 *
	 * @return void
	 *
	 * @uses CalendarSolution::$data  to hold the data
	 */
	public function set_data_from_query($frequent_event_id = null, $safe_markup = true) {
		$this->run_query($frequent_event_id);

		$original_safe_markup = $this->sql->SQLSafeMarkup;
		$this->sql->SQLSafeMarkup = $safe_markup ? 'Y' : 'N';

		$this->data = $this->sql->RecordAsAssocArray(__FILE__, __LINE__);
		$this->data['set_from'] = 'query';

		$this->sql->SQLSafeMarkup = $original_safe_markup;
	}
}
