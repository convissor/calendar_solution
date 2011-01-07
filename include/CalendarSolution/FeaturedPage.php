<?php

/**
 * Calendar Solution's parent class for viewing and editing a Featured Page
 *
 * @package CalendarSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2002-2011
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 */

/**
 * The parent class for viewing and editing a Featured Page
 *
 * @package CalendarSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2002-2011
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 */
abstract class CalendarSolution_FeaturedPage extends CalendarSolution {
	/**
	 * Assembles the query string, executes it and stores the result in
	 * $this->data
	 *
	 * @param int $feature_on_page_id  the id number of the item to get
	 *                                 (defaults to $_REQUEST['feature_on_page_id'])
	 * @return void
	 *
	 * @uses SQLSolution_General::RunQuery()  to access the database
	 * @uses CalendarSolution::get_int_from_request()  to determine the
	 *       user's intention
	 *
	 * @throws CalendarSolution_Exception on an invalid $feature_on_page_id or if
	 *         no matching record is found
	 */
	protected function run_query($feature_on_page_id = null) {
		if ($feature_on_page_id === null) {
			$feature_on_page_id = $this->get_int_from_request('feature_on_page_id');
		}

		if (empty($feature_on_page_id)
			|| !preg_match('/^\d{1,10}$/', $feature_on_page_id))
		{
			throw new CalendarSolution_Exception('Invalid $feature_on_page_id');
		}

		$this->sql->SQLQueryString = "SELECT
			feature_on_page_id,
			feature_on_page
			FROM cs_feature_on_page
			WHERE feature_on_page_id = $feature_on_page_id";

		$this->sql->RunQuery(__FILE__, __LINE__);

		if ($this->sql->SQLRecordSetRowCount == 0) {
			throw new CalendarSolution_Exception('No pages match your criteria');
		}
	}

	/**
	 * Populates $this->data with a record from the database
	 *
	 * @param int $feature_on_page_id  the id number of the item to get
	 * @param bool $safe_markup  should Safe Markup be converted to HTML?
	 *
	 * @return void
	 *
	 * @uses CalendarSolution::$data  to hold the data
	 */
	public function set_data_from_query($feature_on_page_id = null, $safe_markup = true) {
		$this->run_query($feature_on_page_id);

		$original_safe_markup = $this->sql->SQLSafeMarkup;
		$this->sql->SQLSafeMarkup = $safe_markup ? 'Y' : 'N';

		$this->data = $this->sql->RecordAsAssocArray(__FILE__, __LINE__);
		$this->data['set_from'] = 'query';

		$this->sql->SQLSafeMarkup = $original_safe_markup;
	}
}
