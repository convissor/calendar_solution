<?php

/**
 * Calendar Solution's parent class for viewing and editing a Category
 *
 * @package CalendarSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2002-2011
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 */

/**
 * The parent class for viewing and editing a Category
 *
 * @package CalendarSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2002-2011
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 */
abstract class CalendarSolution_Category extends CalendarSolution {
	/**
	 * Assembles the query string, executes it and stores the result in
	 * $this->data
	 *
	 * @param int $category_id  the id number of the item to get
	 *                                (defaults to $_REQUEST['category_id'])
	 * @return void
	 *
	 * @uses SQLSolution_General::RunQuery()  to access the database
	 * @uses CalendarSolution::get_int_from_request()  to determine the
	 *       user's intention
	 *
	 * @throws CalendarSolution_Exception on an invalid $category_id or if
	 *         no matching record is found
	 */
	protected function run_query($category_id = null) {
		if ($category_id === null) {
			$category_id = $this->get_int_from_request('category_id');
		}

		if (empty($category_id)
			|| !preg_match('/^\d{1,10}$/', $category_id))
		{
			throw new CalendarSolution_Exception('Invalid $category_id');
		}

		$this->sql->SQLQueryString = "SELECT
			category_id,
			category
			FROM cs_category
			WHERE category_id = $category_id";

		$this->sql->RunQuery(__FILE__, __LINE__);

		if ($this->sql->SQLRecordSetRowCount == 0) {
			throw new CalendarSolution_Exception('No events match your criteria');
		}
	}

	/**
	 * Populates $this->data with a record from the database
	 *
	 * @param int $category_id  the id number of the item to get
	 * @param bool $safe_markup  should Safe Markup be converted to HTML?
	 *
	 * @return void
	 *
	 * @uses CalendarSolution::$data  to hold the data
	 */
	public function set_data_from_query($category_id = null, $safe_markup = true) {
		$this->run_query($category_id);

		$original_safe_markup = $this->sql->SQLSafeMarkup;
		$this->sql->SQLSafeMarkup = $safe_markup ? 'Y' : 'N';

		$this->data = $this->sql->RecordAsAssocArray(__FILE__, __LINE__);
		$this->data['set_from'] = 'query';

		$this->sql->SQLSafeMarkup = $original_safe_markup;
	}
}
