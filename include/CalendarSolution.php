<?php

/**
 * Calendar Solution's base class
 *
 * The Calendar Solution provides a simple way to post events on your website
 * or intranet.
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
 * @see CalendarSolution_List::factory_chosen_view()
 * @see CalendarSolution_List_Calendar::get_rendering()
 * @see CalendarSolution_List_List::get_rendering()
 * @see CalendarSolution_List_MonthTitle::get_rendering()
 * @see CalendarSolution_List_QuickTable::get_rendering()
 * @see CalendarSolution_List_Title::get_rendering()
 * @see CalendarSolution_List_Ul::get_rendering()
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
	const DATE_FORMAT_NAME_NUMBER = 'l jS';
	const DATE_FORMAT_MEDIUM = 'D, n/j';
	const DATE_FORMAT_SHORT = 'n/j';
	const DATE_FORMAT_ICALENDAR = 'Ymd';
	const DATE_FORMAT_TIME_12AP = 'g:i\&\n\b\s\p\;a';
	const DATE_FORMAT_TIME_24 = 'H:i';
	const DATE_FORMAT_TIME_ICALENDAR = 'Ymd\THis';
	/**#@-*/

	/**#@+
	 * ID numbers used by the "list_link_goes_to_id" field
	 */
	const LINK_TO_NONE = 1;
	const LINK_TO_DETAIL_PAGE = 2;
	const LINK_TO_FREQUENT_EVENT_URI = 3;
	const LINK_TO_CALENDAR_URI = 4;
	/**#@-*/

	/**#@+
	 * ID numbers used by the "status_id" field
	 */
	const STATUS_OPEN = 1;
	const STATUS_FULL = 2;
	const STATUS_CANCELLED = 3;
	/**#@-*/

	/**
	 * The cache object
	 * @var CalendarSolution_Cache
	 */
	protected $cache;

	/**
	 * Is caching available?
	 * @var bool
	 */
	protected $cache_available = false;

	/**
	 * The name of the token used for protecting our admin forms against
	 * Cross Site Request Forgeries
	 * @var string
	 */
	protected $csrf_token_name;

	/**
	 * An associative array of the given item's data
	 * @var array
	 */
	protected $data = array();

	/**
	 * The HTTP_HOST, set in __construct()
	 *
	 * Defaults to $_SERVER['HTTP_HOST'] and falls back to
	 * CALENDAR_SOLUTION_HTTP_HOST if that's not set.
	 *
	 * @var string
	 */
	protected $http_host;

	/**
	 * @var SQLSolution_General
	 */
	protected $sql;

	/**
	 * Data from the REQUEST_URI broken into an associative array containing
	 * the 'path' as a string and the 'query' broken into a sub-array
	 * @var array
	 */
	protected $uri;

	/**
	 * Should the current request use caching?
	 * @var bool
	 */
	protected $use_cache;


	/**
	 * Instantiates the database and cache classes then sets the $http_host
	 * property
	 *
	 * @param string $dbms  optional override of the database extension setting
	 *                      in CALENDAR_SOLUTION_DBMS.  Values can be
	 *                      "mysql", "mysqli", "pgsql", "sqlite", "sqlite3".
	 *
	 * @uses CALENDAR_SOLUTION_DBMS  to know which database extension to use
	 * @uses CalendarSolution::$sql  to store the SQL Solution object
	 *       instantiated by the Calendar Solution's constructor
	 * @uses CALENDAR_SOLUTION_CACHE_CLASS  to know which cache class to use
	 * @uses $GLOBALS['cache_servers']  to know where the cache servers are
	 * @uses CalendarSolution::$cache  to store the Calendar Solution Cache
	 *       object instantiated by the Calendar Solution's constructor
	 * @uses CALENDAR_SOLUTION_HTTP_HOST  in case $_SERVER['HTTP_HOST] is empty
	 * @uses CalendarSolution::$http_host  to store the HTTP_HOST string
	 *
	 * @throws CalendarSolution_Exception if the $dbms parameter or
	 *         CALENDAR_SOLUTION_CACHE_CLASS is improper
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

		if ($GLOBALS['cache_servers']) {
			$class = CALENDAR_SOLUTION_CACHE_CLASS;

			$this->cache = new $class;
			if (!$this->cache instanceof CalendarSolution_Cache) {
				throw new CalendarSolution_Exception('Improper cache class');
			}

			$this->cache_available = true;
			if (!$this->is_admin()) {
				$this->use_cache = true;
			}
		}

		if (empty($_SERVER['HTTP_HOST'])) {
			$this->http_host = CALENDAR_SOLUTION_HTTP_HOST;
		} else {
			$this->http_host = $_SERVER['HTTP_HOST'];
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
	 * Sanitizes the input for iCalendar formats
	 *
	 * The steps are:
	 *  + Break hyperlinks down into anchor text and URI text
	 *  + Strip HTML tags
	 *  + Escape iCalendar special characters
	 *  + Wrap text at 75 characters
	 *
	 * @param string $text  the string to be escaped
	 *
	 * @return string  the sanitized data
	 *
	 * @since Method available since version 3.3
	 */
	protected function escape_for_icalendar($text) {
		// Break hyperlinks down into anchor text and URI text,
		// but don't touch hyperlinks that have URI's as the anchor text
		// because strip tags will do the right thing with them.
		$text = preg_replace('@<a href="([^"]+)">((?!http)(.+))</a>@', '\2 [\1]', $text);

		$text = strip_tags($text);
		$text = str_replace(
			array('\\', "\r\n", "\n", ',', ';'),
			array('\\\\', '\n', '\n', '\,', '\;'),
			$text
		);

		return wordwrap($text, 75, "\r\n\t ");
	}

	/**
	 * Flushes the system's cache
	 *
	 * @return bool
	 *
	 * @uses CalendarSolution_Cache::flush()  to perform the flush
	 */
	public function flush_cache() {
		if (!$this->cache_available) {
			return false;
		}
		return $this->cache->flush();
	}

	/**
	 * Formats a date/time string
	 *
	 * This route is necessary because of the need to provide portability
	 * across different database management systems.
	 *
	 * @param string $in  the date to format
	 * @param string $format  the format to use (for PHP's date() function).
	 *                        Use the DATE_FORMAT_* constants in this class
	 *                        or use a format of your choosing.
	 *
	 * @return string  the formatted date
	 *
	 * @see http://php.net/date
	 */
	protected function format_date($in, $format) {
		if ($in === null || $in === '') {
			return '';
		}
		return date($format, strtotime($in));
	}

	/**
	 * Generates the HTML needed to access administrative functions
	 * @return string  the HTML with the admin links
	 */
	public function get_admin_navigation() {
		return '<p class="cs_admin_navigation">
			<a href="calendar.php">Events</a> |
			<a href="calendar-detail.php">Add Event</a> ||
			<a href="category.php">Categories</a> |
			<a href="category-detail.php">Add Category</a> ||
			<a href="frequent_event.php">Frequent Events</a> |
			<a href="frequent_event-detail.php">Add Frequent Event</a> ||
			<a href="featured_page.php">Featured Pages</a> |
			<a href="featured_page-detail.php">Add Featured Page</a> ||
			<a href="flush.php">Flush Cache</a>
			</p>';
	}

	/**
	 * Produces the HTML with a link to the Calendar Solution's home page
	 * @return string  the HTML with the credit link
	 */
	protected function get_credit() {
		return '<p class="cs_credit">Calendar produced using <a href="'
			. 'http://www.analysisandsolutions.com/software/calendar/'
			. '">Calendar Solution</a></p>' . "\n";
	}

	/**
	 * Provides the Cascading Style Sheet data, for use between <style> tags
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
	 * Formats event data for iCalendar output
	 *
	 * @param array $event  an associative array of a given event
	 *
	 * @return string  the iCalendar formatted event
	 *
	 * @uses CalendarSolution::escape_for_icalendar()  to sanitize the data
	 * @uses CalendarSolution::$http_host  as the UID's domain
	 *
	 * @since Method available since version 3.3
	 */
	protected function get_event_formatted_icalendar($event) {
		// We don't track creation/modification time.  Fake it.
		if ($event['changed'] == 'Y') {
			$out = "DTSTAMP:20010101T000000\r\n";
		} else {
			$out = "DTSTAMP:20000101T000000\r\n";
		}

		$out .= 'UID:' . $event['calendar_id'] . '@' . $this->http_host . "\r\n";

		if ($event['time_start']) {
			$out .= 'DTSTART:' . $this->format_date(
				$event['date_start'] . ' ' . $event['time_start'],
				self::DATE_FORMAT_TIME_ICALENDAR) . "\r\n";

			if ($event['time_end']) {
				$out .= 'DTEND:' . $this->format_date(
					$event['date_start'] . ' ' . $event['time_end'],
					self::DATE_FORMAT_TIME_ICALENDAR) . "\r\n";
			}
		} else {
			$out .= 'DTSTART;VALUE=DATE:' . $this->format_date(
				$event['date_start'], self::DATE_FORMAT_ICALENDAR) . "\r\n";
		}

		if ($event['status_id'] == self::STATUS_CANCELLED) {
			$out .= "STATUS:CANCELLED\r\n";
		} else {
			if ($event['status_id'] == self::STATUS_FULL) {
				$event['title'] = 'FULL: ' . $event['title'];
			}
			$out .= "STATUS:CONFIRMED\r\n";
		}

		$out .= 'SUMMARY:' . $this->escape_for_icalendar($event['title'])
			. "\r\n";

		$description = '';
		if ($event['summary']) {
			$description .= $this->escape_for_icalendar($event['summary']);
		}
		if (!empty($event['detail'])) {
			if ($description) {
				$description .= '\n\n';
			}
			$description .= $this->escape_for_icalendar($event['detail']);
		}
		if ($description) {
			$out .= 'DESCRIPTION:' . $description . "\r\n";
		}

		if (!empty($event['note'])) {
			$out .= 'COMMENT:'
				. $this->escape_for_icalendar($event['note']) . "\r\n";
		}

		if ($event['changed'] == 'Y') {
			$out .= "COMMENT:Event changed since first posted.\r\n";
		}

		if ($event['is_own_event'] == 'N') {
			$out .= "COMMENT:This event is produced by a different group.\r\n";
		}

		if ($event['location_start']) {
			$out .= 'LOCATION:'
				. $this->escape_for_icalendar($event['location_start'])
				. "\r\n";
		}

		if (!empty($event['calendar_uri'])) {
			$out .= 'URL:' . $this->escape_for_icalendar(
				$event['calendar_uri']) . "\r\n";
		} elseif (!empty($event['frequent_event_uri'])) {
			$out .= 'URL:' . $this->escape_for_icalendar(
				$event['frequent_event_uri']) . "\r\n";
		}

		if (!empty($event['category'])) {
			$out .= 'CATEGORIES:'
				. $this->escape_for_icalendar($event['category']) . "\r\n";
		}

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
	 * Looks for a string value in $_REQUEST[$name]
	 *
	 * @param string $name  the $_REQUEST array's key to examine
	 *
	 * @return mixed  the string, NULL if the REQUEST
	 *                element is not set, NULL if $_GET['remove_limit'] is set,
	 *                or FALSE if the input is invalid
	 */
	protected function get_string_from_request($name) {
		if (!empty($_GET['remove_limit'])) {
			return null;
		}
		if (!array_key_exists($name, $_REQUEST)) {
			return null;
		}
		if (!is_scalar($_REQUEST[$name])) {
			return false;
		}
		return $_REQUEST[$name];
	}

	/**
	 * Is the current view from the admin section or not?
	 * @return bool
	 */
	public function is_admin() {
		static $answer;
		if (!isset($answer)) {
			if (strpos($_SERVER['SCRIPT_FILENAME'], '/Admin/') !== false) {
				$answer = true;
			} else {
				$answer = false;
			}
		}
		return $answer;
	}

	/**
	 * Is caching available?
	 * @return bool
	 */
	public function is_cache_available() {
		return $this->cache_available;
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
	 * + Non-scalar entries get set to NULL (see below for exceptions).
	 * + Values are passed through trim().
	 * + Empty strings are converted to NULL.
	 *
	 * Fields expected to be arrays (i.e. fields listed in "$fields_bitwise")
	 * have their array values passed through the process listed above.
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

	/**
	 * Breaks up the REQUEST_URI into usable parts
	 *
	 * @return void
	 *
	 * @uses CalendarSolution::$uri  to store the data
	 * @since Method moved to CalendarSolution class in version 3.3
	 */
	protected function set_uri() {
		$this->uri = array('path' => '', 'query' => array());
		if (!empty($_SERVER['REQUEST_URI'])) {
			$request = explode('?', $_SERVER['REQUEST_URI']);
			$this->uri['path'] = empty($request[0]) ? '' : $request[0];
			if (!empty($request[1])) {
				parse_str($request[1], $this->uri['query']);
			}
		}
	}

	/**
	 * Checks the Cross Site Request Forgery token to improve security
	 *
	 * @return void
	 * @throws CalendarSolution_Exception  if the proper CSRF token is missing
	 */
	protected function validate_csrf_token() {
		if (empty($_SESSION[$this->csrf_token_name])) {
			// Token missing from session...

			$session_name = session_name();

			if (empty($_COOKIE[$session_name])) {
				// ... and the session ID cookie doesn't exist...
				if (ini_get('session.use_only_cookies')) {
					// ... but the session cookie should exist.
					throw new CalendarSolution_Exception(
						'Please enable cookies');
				} else {
					// ... well, the server isn't forcing session cookies...
					if (empty($_POST[$session_name])) {
						// ... and the session ID isn't in the POST, either.
						throw new CalendarSolution_Exception('Invalid POST');
					} else {
						// ... and the session ID is in the POST.
						throw new CalendarSolution_Exception(
							'Token missing from post session');
					}
				}
			} else {
				// ... but session cookie exists.
				throw new CalendarSolution_Exception(
					'Token missing from cookie session');
			}
		}

		if (empty($_POST[$this->csrf_token_name])) {
			throw new CalendarSolution_Exception('Token missing from post');
		}
		if ($_SESSION[$this->csrf_token_name] != $_POST[$this->csrf_token_name]) {
			throw new CalendarSolution_Exception('Invalid token');
		}
	}
}
