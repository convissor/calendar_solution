<?php

/**
 * Calendar Solution's means to edit a Featured Page via an HTML form
 *
 * @package CalendarSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2002-2012
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 */

/**
 * The means to edit a Featured Page via an HTML form
 *
 * @package CalendarSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2002-2012
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 */
class CalendarSolution_FeaturedPage_Form extends CalendarSolution_FeaturedPage {
	/**
	 * Errors found by is_valid()
	 * @var array
	 */
	protected $errors = array();

	/**
	 * The names of fields on the form
	 * @var array
	 */
	protected $fields = array(
		'feature_on_page_id',
		'feature_on_page',
	);

	/**
	 * The names of fields on the form that are bitwise in the database
	 * @var array
	 */
	protected $fields_bitwise = array();


	/**
	 * Sets the CSRF token name and calls the main constructor
	 *
	 * @param string $dbms  optional override of the database extension setting
	 *                      in CALENDAR_SOLUTION_DBMS.  Values can be
	 *                      "mysql", "mysqli", "pgsql", "sqlite", "sqlite3".
	 *
	 * @uses CALENDAR_SOLUTION_DBMS  to know which database extension to use
	 * @uses CalendarSolution::__construct()  for the main instantiation tasks
	 * @uses CalendarSolution::$csrf_token_name  to hold the token's name
	 */
	public function __construct($dbms = CALENDAR_SOLUTION_DBMS) {
		parent::__construct($dbms);
		$this->csrf_token_name = 'csrf_token_' . __CLASS__;
	}

	/**
	 * Deletes the record specified by $this->data['feature_on_page_id']
	 *
	 * @return void
	 *
	 * @uses CalendarSolution::validate_csrf_token()  to check the CSRF token
	 * @throws CalendarSolution_Exception  if the form submission seems like
	 *         a Cross Site Request Forgery
	 */
	public function delete() {
		$this->validate_csrf_token();
		$this->flush_cache();

		$this->sql->SQLQueryString = 'DELETE FROM cs_feature_on_page
			WHERE feature_on_page_id = '
			. $this->sql->Escape(__FILE__, __LINE__, $this->data['feature_on_page_id']);

		$this->sql->RunQuery(__FILE__, __LINE__);
	}

	/**
	 * Inserts the posted data into the database
	 *
	 * @return void
	 *
	 * @uses CalendarSolution::validate_csrf_token()  to check the CSRF token
	 * @throws CalendarSolution_Exception  if the form submission seems like
	 *         a Cross Site Request Forgery
	 */
	public function insert() {
		$this->validate_csrf_token();
		$this->flush_cache();

//		$this->sql->SQLQueryString = 'BEGIN';
//		$this->sql->RunQuery(__FILE__, __LINE__);

		$this->sql->SQLQueryString = 'SELECT MAX(feature_on_page_id) * 2 FROM cs_feature_on_page';
		$this->sql->RunQuery(__FILE__, __LINE__);
		$row = $this->sql->RecordAsEnumArray(__FILE__, __LINE__);

		$this->sql->SQLQueryString = 'INSERT INTO cs_feature_on_page (
				feature_on_page_id,
				feature_on_page
			) VALUES ('
				. $this->sql->Escape(__FILE__, __LINE__, $row[0]) . ', '
				. $this->sql->Escape(__FILE__, __LINE__, $this->data['feature_on_page'])
			. ')';

		if (!$this->sql->RunQuery_NoDuplicates(__FILE__, __LINE__)) {
			throw new CalendarSolution_Exception('That Title already exists');
		}
	}

	/**
	 * Ensures the validity of the information in $this->data
	 *
	 * @param bool $check_feature_on_page_id  test the value of feature_on_page_id?
	 *
	 * @return bool
	 *
	 * @throws CalendarSolution_Exception on fields containing predetermined
	 *         data being manipulated
	 */
	public function is_valid($check_feature_on_page_id = true) {
		$this->errors = array();

		if ($check_feature_on_page_id) {
			if (!preg_match('/^\d{1,10}$/', $this->data['feature_on_page_id'])) {
				throw new CalendarSolution_Exception('feature_on_page_id is invalid');
			}
		}

		if (strlen($this->data['feature_on_page']) > 60) {
			$this->errors[] = 'Name is too long. We trimmed it';
			$this->data['feature_on_page'] = trim(substr($this->data['feature_on_page'], 0, 60));
		}
		$Temp = $this->data['feature_on_page'];
		$this->data['feature_on_page'] = preg_replace('/::\/?\w+::/', '', $this->data['feature_on_page']);
		if ($this->data['feature_on_page'] != $Temp) {
			$this->errors[] = 'Safe Markup is not allowed in the Name field';
		} elseif ($this->data['feature_on_page'] == '') {
			$this->errors[] = 'Name is blank';
		}

		return empty($this->errors);
	}

	/**
	 * Produces the HTML form for editing an event
	 *
	 * @param int $feature_on_page_id  the id number of the item to get
	 *                                (defaults to $_REQUEST['feature_on_page_id'])
	 *
	 * @return string  the complete HTML of the desired event
	 *
	 * @throws CalendarSolution_Exception if $this->data is not populated
	 */
	public function get_rendering() {
		if (empty($this->data)) {
			throw new CalendarSolution_Exception('$data has not been populated');
		}

		if ($this->data['set_from'] == 'post') {
			$this->escape_data_for_html();
		}

		$out = '<form class="cs_form" method="post">' . "\n";
		$out .= ' <table summary="Featured Page Entry Form. Left ';
		$out .= "column has field names. Right column has data entry fields.\">\n";


		// ------------------------------------------------------
		$out .= '  <tr>' . "\n";
		$out .= "   <td nowrap>T<u>i</u>tle</td>\n";
		$out .= "   <td>\n";
		$out .= "    <small>* <em>Required.</em>\n";
		$out .= '    <br /><input accesskey="i" type="text" name="feature_on_page" size="60" maxlength="60" value="' . $this->data['feature_on_page'] . "\" />\n";
		$out .= "   </td>\n";
		$out .= "  </tr>\n";


		// ------------------------------------------------------
		$out .= '  <tr>' . "\n";
		$out .= "   <td nowrap>\n";
		$out .= "    Submit\n";
		$out .= "   </td>\n";
		$out .= "   <td>\n";

		if ($this->data['feature_on_page_id']) {
			$out .= "    Update:\n";
			$out .= '    <input type="submit" name="submit" value="Update" />' . "\n";
		}

		if ($this->data['feature_on_page_id']) {
			$out .= "    Copy this as new item:\n";
		} else {
			$out .= "    New:\n";
		}
		$out .= '    <input type="submit" name="submit" value="Add" />' . "\n";

		if ($this->data['feature_on_page_id']) {
			$out .= "    Delete:\n";
			$out .= '    <input type="submit" name="submit" value="Delete" />' . "\n";
		}

		$token_value = uniqid(rand(), true);
		$_SESSION[$this->csrf_token_name] = $token_value;
		$out .= '    <input type="hidden" name="' . $this->csrf_token_name
			. '" value="' . $token_value . "\" />\n";

		$out .= '    <input type="hidden" name="feature_on_page_id" value="';
		$out .=      $this->data['feature_on_page_id'] . "\" />\n";
		$out .= "   </td>\n";
		$out .= "  </tr>\n";


		$out .= ' </table>';
		$out .= "\n</form>\n\n";

		$out .= $this->get_credit();

		return $out;
	}

	/**
	 * Updates the record with the posted data
	 *
	 * @return void
	 *
	 * @uses CalendarSolution::validate_csrf_token()  to check the CSRF token
	 * @throws CalendarSolution_Exception  if the form submission seems like
	 *         a Cross Site Request Forgery
	 */
	public function update() {
		$this->validate_csrf_token();
		$this->flush_cache();

		$this->sql->SQLQueryString = 'UPDATE cs_feature_on_page SET
			feature_on_page = ' . $this->sql->Escape(__FILE__, __LINE__, $this->data['feature_on_page']) . '
			WHERE feature_on_page_id = ' . $this->sql->Escape(__FILE__, __LINE__, $this->data['feature_on_page_id']);

		if (!$this->sql->RunQuery_NoDuplicates(__FILE__, __LINE__)) {
			throw new CalendarSolution_Exception('That Title already exists');
		}
	}
}
