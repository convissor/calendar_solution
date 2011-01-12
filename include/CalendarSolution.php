<?php

/**
 * Calendar Solution's base class
 *
 * Calendar Solution is a trademark of The Analysis and Solutions Company.
 *
 * @package CalendarSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2002-2011
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 */

/**
 * The base class
 *
 * @package CalendarSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2002-2011
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 */
class CalendarSolution {
	/**#@+
	 * Format for PHP's date() function, to be used by our format_date() method
	 *
	 * @see CalendarSolution::format_date()
	 */
	const DATE_FORMAT_FULL = 'l, F jS, Y';
	const DATE_FORMAT_LONG = 'D, M jS';
	const DATE_FORMAT_MEDIUM = 'D, n/j';
	const DATE_FORMAT_SHORT = 'n/j';
	const DATE_FORMAT_TIME_12AP = 'g:i\&\n\b\s\p\;a';
	const DATE_FORMAT_TIME_24 = 'H:i';
	/**#@-*/

	/**#@+
	 * ID numbers used by the list_link_goes_to_id field
	 */
	const LINK_TO_NONE = 1;
	const LINK_TO_DETAIL_PAGE = 2;
	const LINK_TO_FREQUENT_EVENT_URI = 3;
	const LINK_TO_CALENDAR_URI = 4;
	/**#@-*/

	/**#@+
	 * Status ID numbers used by the status_id field
	 */
	const STATUS_OPEN = 1;
	const STATUS_FULL = 2;
	const STATUS_CANCELLED = 3;
	/**#@-*/

	/**
	 * An associative array of the given item's data
	 * @var array
	 */
	protected $data = array();

	/**
	 * Errors found by is_valid()
	 * @var array
	 */
	protected $errors = array();

	/**
	 * The names of fields on the form
	 * @var array
	 */
	protected $fields = array();

	/**
	 * The names of fields on the form that are bitwise in the database
	 * @var array
	 */
	protected $fields_bitwise = array();

	/**
	 * @var SQLSolution_General
	 */
	protected $sql;


	/**
	 * Instantiates the database class and stores it in the $sql property
	 *
	 * @param string $dbms  optional override of the database extension setting
	 *                      in CALENDAR_SOLUTION_DBMS.  Values can be
	 *                      "mysql", "mysqli", "pgsql", "sqlite", "sqlite3".
	 *
	 * @uses CALENDAR_SOLUTION_DBMS  to know which database extension to use
	 * @uses CalendarSolution::$sql  the SQL Solution object for the
	 *       database system specified by the $dbms parameter
	 *
	 * @throws CalendarSolution_Exception if the $dbms parameter is improper
	 */
	public function __construct($dbms = CALENDAR_SOLUTION_DBMS) {
		$file = '';
		switch ($dbms) {
			case 'mysql':
				$class = 'SQLSolution_MySQLUser';
				break;
			case 'mysqli':
				$class = 'SQLSolution_MySQLiUser';
				break;
			case 'pgsql':
				$class = 'SQLSolution_PostgreSQLUser';
				break;
			case 'sqlite':
				$class = 'SQLSolution_SQLiteUser';
				$file = 'calendar_solution.sqlite2';
				break;
			case 'sqlite3':
				$class = 'SQLSolution_SQLite3User';
				$file = 'calendar_solution.sqlite3';
				break;
			case 'test':
				return;
			default:
				throw new CalendarSolution_Exception('Improper dbms');
		}

		$this->sql = new $class('Y', 'Y');

		if ($file && $this->sql->SQLDbName == 'default') {
			$this->sql->SQLDbName = dirname(__FILE__)
				. '/CalendarSolution/sqlite/' . $file;
		}
	}

	/**
	 * Sanitizes the data in $this->data via htmlspecialchars()
	 *
	 * @return void
	 *
	 * @uses CalendarSolution::$data  as the data to sanitize
	 */
	protected function escape_data_for_html() {
		foreach ($this->data as $key => $value) {
			if (is_array($value)) {
				foreach ($value as $sub_key => $sub_value) {
					$this->data[$key][$sub_key] = htmlspecialchars($sub_value);
				}
			} else {
				$this->data[$key] = htmlspecialchars($value);
			}
		}
	}

	/**
	 * Formats a date/time string
	 *
	 * This route is necessary because of the need to provide portability
	 * across different database management systems.
	 *
	 * @param string $in  the date to format
	 * @param string $format  the format to use (for PHP's date() function).
	 *                        Use the DATE_FORMAT_* constants in this class.
	 *
	 * @return string  the formatted date
	 */
	protected function format_date($in, $format) {
		if ($in === null || $in === '') {
			return '';
		}
		return date($format, strtotime($in));
	}

	/**
	 * @return string  the HTML with the admin links
	 */
	public function get_admin_navigation() {
		return '<p><small>
			<a href="calendar.php">Events</a> |
			<a href="calendar-detail.php">Add Event</a> ||
			<a href="category.php">Categories</a> |
			<a href="category-detail.php">Add Category</a> ||
			<a href="frequent_event.php">Frequent Events</a> |
			<a href="frequent_event-detail.php">Add Frequent Event</a> ||
			<a href="featured_page.php">Featured Pages</a> |
			<a href="featured_page-detail.php">Add Featured Page</a>
			</small></p>';
	}

	/**
	 * @return string  the HTML with the credit link
	 */
	protected function get_credit() {
		return '<p class="cs_credit">Calendar produced using <a href="'
			. 'http://www.analysisandsolutions.com/software/calendar/'
			. '">Calendar Solution</a></p>' . "\n";
	}

	/**
	 * Provides the Cascading Style Sheet data, for use between <style> tags
	 *
	 * @return string  the CSS
	 */
	public function get_css() {
		$file = $this->get_css_name();
		if ($file) {
			return file_get_contents($file);
		} else {
			return '';
		}
	}

	/**
	 * Looks for a date value in $_REQUEST[$name]
	 *
	 * @param string $name  the $_REQUEST array's key to examine
	 *
	 * @return mixed  the date in YYYY-MM-DD format, NULL if the REQUEST
	 *                element is not set, NULL if $_GET['remove_limit'] is set,
	 *                or FALSE if the input is invalid
	 */
	protected function get_date_from_request($name) {
		if (!empty($_GET['remove_limit'])) {
			return null;
		}
		if (empty($_REQUEST[$name])) {
			return null;
		}
		if (!is_scalar($_REQUEST[$name])
			|| !preg_match('/^\d{4}-\d{2}-\d{2}$/', $_REQUEST[$name]))
		{
			return false;
		}
		return $_REQUEST[$name];
	}

	/**
	 * Produces an HTML list explaining the errors found by is_valid()
	 *
	 * @return string  the HTML containing the list of problems
	 *
	 * @see CalendarSolution_Detail_Form::is_valid()
	 */
	public function get_errors() {
		$out = '<p class="cs_notice"><big>'
			. 'Your submission was NOT saved.<br />'
			. 'Please fix the following errors and try again:</big></p>'
			. "\n<ul>\n";
		foreach ($this->errors AS $error) {
			$out .= " <li>$error.</li>\n";
		}
		$out .= "</ul>\n";

		return $out;
	}

	/**
	 * Looks for an integer value in $_REQUEST[$name]
	 *
	 * @param string $name  the $_REQUEST array's key to examine
	 *
	 * @return mixed  the integer, NULL if the REQUEST
	 *                element is not set, NULL if $_GET['remove_limit'] is set,
	 *                or FALSE if the input is invalid
	 */
	protected function get_int_from_request($name) {
		if (!empty($_GET['remove_limit'])) {
			return null;
		}
		if (!array_key_exists($name, $_REQUEST)) {
			return null;
		}
		if (!is_scalar($_REQUEST[$name])
			|| !preg_match('/^\d{1,10}$/', $_REQUEST[$name]))
		{
			return false;
		}
		return $_REQUEST[$name];
	}

	/**
	 * Extracts an integer or an array of integers in $_REQUEST[$name]
	 *
	 * @param string $name  the $_REQUEST array's key to examine
	 *
	 * @return mixed  the array of integers, NULL if the REQUEST
	 *                element is not set, NULL if $_GET['remove_limit'] is set,
	 *                or FALSE if the input is invalid
	 */
	protected function get_int_array_from_request($name) {
		if (!empty($_GET['remove_limit'])) {
			return null;
		}
		if (!array_key_exists($name, $_REQUEST)) {
			return null;
		}

		if (is_array($_REQUEST[$name])) {
			$tmp = $_REQUEST[$name];
		} else {
			$tmp = array($_REQUEST[$name]);
		}

		$out = array();
		foreach ($tmp as $value) {
			if (!is_scalar($value)
				|| !preg_match('/^\d{1,10}$/', $value))
			{
				return false;
			}
			$out[] = $value;
		}
		return $out;
	}

	/**
	 * Is the current view from the admin section or not?
	 * @return bool
	 */
	public function is_admin() {
		static $answer;
		if (!isset($answer)) {
			if (strpos($_SERVER['REQUEST_URI'], '/Admin/') !== false) {
				$answer = true;
			} else {
				$answer = false;
			}
		}
		return $answer;
	}

	/**
	 * Populates $this->data with the requisite keys and sets values to NULL
	 *
	 * @return void
	 *
	 * @uses CalendarSolution::$data  to hold the data
	 * @uses CalendarSolution::$fields  as the list of field names
	 */
	public function set_data_empty() {
		$this->data = array();
		foreach ($this->fields as $field) {
			$this->data[$field] = null;
		}
		$this->data['set_from'] = 'empty';
	}

	/**
	 * Populates $this->data with the information from $_POST
	 *
	 * The following transformations also occur:
	 * + Missing keys are created and their values set to NULL.
	 * + Non-scalar entries get set to NULL.
	 * + Values are passed through trim().
	 * + Empty strings are converted to NULL.
	 *
	 * Fields expected to be arrays have their values passed through the
	 * process listed above.
	 *
	 * @return void
	 *
	 * @uses CalendarSolution::$data  to hold the data
	 * @uses CalendarSolution::$fields  as the list of field names
	 * @uses CalendarSolution::$fields_bitwise  to know which fields to
	 *       handle differently
	 */
	public function set_data_from_post() {
		$this->data = array();
		foreach ($this->fields as $field) {
			if (array_key_exists($field, $_POST)) {
				if (in_array($field, $this->fields_bitwise)) {
					if (is_array($_POST[$field])) {
						foreach ($_POST[$field] as $key => $value) {
							if (is_scalar($value)) {
								$this->data[$field][$key] = trim($_POST[$field][$key]);
								if ($this->data[$field][$key] === '') {
									$this->data[$field][$key] = null;
								}
							} else {
								$this->data[$field][$key] = null;
							}
						}
					} else {
						$this->data[$field] = array();
					}
				} else {
					if (is_scalar($_POST[$field])) {
						$this->data[$field] = trim($_POST[$field]);
						if ($this->data[$field] === '') {
							$this->data[$field] = null;
						}
					} else {
						$this->data[$field] = null;
					}
				}
			} else {
				if (in_array($field, $this->fields_bitwise)) {
					$this->data[$field] = array();
				} else {
					$this->data[$field] = null;
				}
			}
		}
		$this->data['set_from'] = 'post';
	}
}
