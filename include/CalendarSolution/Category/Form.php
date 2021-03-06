<?php

/**
 * Calendar Solution's means to edit a Category via an HTML form
 *
 * @package CalendarSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2002-2012
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 */

/**
 * The means to edit a Category via an HTML form
 *
 * @package CalendarSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2002-2012
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 */
class CalendarSolution_Category_Form extends CalendarSolution_Category {
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
		'category_id',
		'category',
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
	 * Deletes the record specified by $this->data['category_id']
	 *
	 * @return void
	 *
	 * @uses CalendarSolution::validate_csrf_token()  to check the CSRF token
	 * @throws CalendarSolution_Exception  if the form submission seems like
	 *         a Cross Site Request Forgery
	 */
	public function delete() {
		$this->validate_csrf_token();

		$this->sql->SQLQueryString = 'DELETE FROM cs_category
			WHERE category_id = '
			. $this->sql->Escape(__FILE__, __LINE__, $this->data['category_id']);

		$this->sql->RunQuery(__FILE__, __LINE__);

		$this->flush_cache();
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

		$this->sql->SQLQueryString = 'INSERT INTO cs_category (
				category
			) VALUES ('
				. $this->sql->Escape(__FILE__, __LINE__, $this->data['category'])
			. ')';

		if (!$this->sql->RunQuery_NoDuplicates(__FILE__, __LINE__)) {
			throw new CalendarSolution_Exception('That Title already exists');
		}

		$this->flush_cache();
	}

	/**
	 * Ensures the validity of the information in $this->data
	 *
	 * @param bool $check_category_id  test the value of category_id?
	 *
	 * @return bool
	 *
	 * @throws CalendarSolution_Exception on fields containing predetermined
	 *         data being manipulated
	 */
	public function is_valid($check_category_id = true) {
		$this->errors = array();

		if ($check_category_id) {
			if (!preg_match('/^\d{1,10}$/', $this->data['category_id'])) {
				throw new CalendarSolution_Exception('category_id is invalid');
			}
		}

		if (strlen($this->data['category']) > 60) {
			$this->errors[] = 'Name is too long. We trimmed it';
			$this->data['category'] = trim(substr($this->data['category'], 0, 60));
		}
		$Temp = $this->data['category'];
		$this->data['category'] = preg_replace('/::\/?\w+::/', '', $this->data['category']);
		if ($this->data['category'] != $Temp) {
			$this->errors[] = 'Safe Markup is not allowed in the Name field';
		} elseif ($this->data['category'] == '') {
			$this->errors[] = 'Name is blank';
		}

		return empty($this->errors);
	}

	/**
	 * Produces the HTML form for editing an event
	 *
	 * @param int $category_id  the id number of the item to get
	 *                                (defaults to $_REQUEST['category_id'])
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
		$out .= ' <table summary="Category Entry Form. Left ';
		$out .= "column has field names. Right column has data entry fields.\">\n";


		// ------------------------------------------------------
		$out .= '  <tr>' . "\n";
		$out .= "   <td nowrap>T<u>i</u>tle</td>\n";
		$out .= "   <td>\n";
		$out .= "    <small>* <em>Required.</em>\n";
		$out .= '    <br /><input accesskey="i" type="text" name="category" size="60" maxlength="60" value="' . $this->data['category'] . "\" />\n";
		$out .= "   </td>\n";
		$out .= "  </tr>\n";


		// ------------------------------------------------------
		$out .= '  <tr>' . "\n";
		$out .= "   <td nowrap>\n";
		$out .= "    Submit\n";
		$out .= "   </td>\n";
		$out .= "   <td>\n";

		if ($this->data['category_id']) {
			$out .= "    Update:\n";
			$out .= '    <input type="submit" name="submit" value="Update" />' . "\n";
		}

		if ($this->data['category_id']) {
			$out .= "    Copy this as new item:\n";
		} else {
			$out .= "    New:\n";
		}
		$out .= '    <input type="submit" name="submit" value="Add" />' . "\n";

		if ($this->data['category_id']) {
			$out .= "    Delete:\n";
			$out .= '    <input type="submit" name="submit" value="Delete" />' . "\n";
		}

		$token_value = uniqid(rand(), true);
		$_SESSION[$this->csrf_token_name] = $token_value;
		$out .= '    <input type="hidden" name="' . $this->csrf_token_name
			. '" value="' . $token_value . "\" />\n";

		$out .= '    <input type="hidden" name="category_id" value="';
		$out .=      $this->data['category_id'] . "\" />\n";
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

		$this->sql->SQLQueryString = 'UPDATE cs_category SET
			category = ' . $this->sql->Escape(__FILE__, __LINE__, $this->data['category']) . '
			WHERE category_id = ' . $this->sql->Escape(__FILE__, __LINE__, $this->data['category_id']);

		if (!$this->sql->RunQuery_NoDuplicates(__FILE__, __LINE__)) {
			throw new CalendarSolution_Exception('That Title already exists');
		}

		$this->flush_cache();
	}
}
