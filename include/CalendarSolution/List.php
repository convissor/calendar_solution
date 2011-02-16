<?php

/**
 * Calendar Solution's parent class for displaying collections of events
 *
 * @package CalendarSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2002-2011
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 */

/**
 * The parent class for displaying collections of events
 *
 * @package CalendarSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2002-2011
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 */
abstract class CalendarSolution_List extends CalendarSolution {
	/**
	 * The cache key identifying the number of rows found in a data set
	 * @var string
	 */
	protected $cache_count_key;

	/**
	 * The data cache key for the current view
	 * @var string
	 */
	protected $cache_key;

	/**
	 * The category ids to show events for
	 * @var array
	 */
	protected $category_id;

	/**
	 * The records to be displayed
	 * @var array
	 */
	protected $data;

	/**
	 * The frequent event id to show events for
	 * @var int
	 */
	protected $frequent_event_id;

	/**
	 * The start of the date range to show events for in the current request
	 * @var DateTimeSolution
	 */
	protected $from;

	/**
	 * Should the output include the limit navigation?
	 * @var bool
	 */
	protected $show_limit_navigation = false;

	/**
	 * The DateIntervalSolution specification for how many months to show at once
	 * @var string
	 */
	protected $interval_spec;

	/**
	 * The number of items to show
	 * @var int
	 */
	protected $limit_quantity;

	/**
	 * The zero-based row index to start the limit on
	 * @var int
	 */
	protected $limit_start;

	/**
	 * The start of the date range to show events for if the user navigates
	 * to later events
	 * @var DateTimeSolution
	 */
	protected $next_from;

	/**
	 * The end of the date range to show events for if the user navigates
	 * to later events
	 * @var DateTimeSolution
	 */
	protected $next_to;

	/**
	 * The page id to show events for
	 * @var int
	 */
	protected $page_id;

	/**
	 * The start of the date range to show events for if the user navigates
	 * to earlier events
	 * @var DateTimeSolution
	 */
	protected $prior_from;

	/**
	 * The end of the date range to show events for if the user navigates
	 * to earlier events
	 * @var DateTimeSolution
	 */
	protected $prior_to;

	/**
	 * Should cancelled events be shown or not?
	 * @var bool
	 */
	protected $show_cancelled = true;

	/**
	 * Should the Summary field be shown or not when viewing "List" mode?
	 * @var bool
	 */
	protected $show_summary = true;

	/**
	 * The end of the date range to show events for in the current request
	 * @var DateTimeSolution
	 */
	protected $to;

	/**
	 * How many rows the entire result set has
	 * @var int
	 */
	protected $total_rows;

	/**
	 * The SQL WHERE clause for the current view
	 * @var string
	 */
	protected $where_sql;


	/**
	 * Calls the parent constructor then populates the $interval_spec property
	 *
	 * @param integer $months  how many months should be shown at once
	 * @param string $dbms  optional override of the database extension setting
	 *                      in CALENDAR_SOLUTION_DBMS.  Values can be
	 *                      "mysql", "mysqli", "pgsql", "sqlite", "sqlite3".
	 *
	 * @uses CalendarSolution::__construct()  to instantiate the database class
	 * @uses CalendarSolution_List::$interval_spec  is set based on the
	 *       $months parameter; which is used later when creating new
	 *       DateIntervalSolution objects
	 */
	public function __construct($months = 3, $dbms = CALENDAR_SOLUTION_DBMS) {
		parent::__construct($dbms);
		$this->interval_spec = 'P' . ($months - 1) . 'M';
	}

	/**
	 * Looks at $_REQUEST['view'] then $_COOKIE['CalendarSolution']
	 * to determine whether to return a CalendarSolution_List_Calendar
	 * (the default) or CalendarSolution_List_List object
	 *
	 * WARNING: this function sets a cookie, so must be called before any
	 * output is sent to the browser.
	 *
	 * @param integer $months  how many months should be shown at once
	 * @param string $dbms  optional override of the database extension setting
	 *                      in CALENDAR_SOLUTION_DBMS.  Values can be
	 *                      "mysql", "mysqli", "pgsql", "sqlite", "sqlite3".
	 *
	 * @return CalendarSolution_List_Calendar|CalendarSolution_List_List
	 */
	public static function factory_chosen_view($months = 3,
		$dbms = CALENDAR_SOLUTION_DBMS)
	{
		$set_cookie = false;

		if (empty($_REQUEST['view'])) {
			if (empty($_COOKIE['CalendarSolution'])) {
				$view = 'Calendar';
				$set_cookie = true;
			} elseif ($_COOKIE['CalendarSolution'] == 'List') {
				$view = 'List';
			} else {
				$view = 'Calendar';
			}
		} elseif ($_REQUEST['view'] == 'List') {
			$view = 'List';
			if (empty($_COOKIE['CalendarSolution'])
				|| $_COOKIE['CalendarSolution'] == 'Calendar')
			{
				$set_cookie = true;
			}
		} else {
			$view = 'Calendar';
			if (empty($_COOKIE['CalendarSolution'])
				|| $_COOKIE['CalendarSolution'] == 'List')
			{
				$set_cookie = true;
			}
		}

		if ($set_cookie) {
			// Expires in 2038.
			setcookie('CalendarSolution', $view, 2147483647, '/');
		}

		$class = __CLASS__ . '_' . $view;
		return new $class($months, $dbms);
	}

	/**
	 * Provides the path and name of the needed Cascading Style Sheet file
	 *
	 * @return string  the path and name of the CSS file
	 */
	public function get_css_name() {
		return dirname(__FILE__) . '/List.css';
	}

	/**
	 * Produces the HTML for navigating to earlier and later events
	 * as well as changing between list and calendar views
	 *
	 * @return string  the navigation section's HTML
	 *
	 * @uses CalendarSolution_List::$view
	 * @uses CalendarSolution_List::$category_id
	 * @uses CalendarSolution_List::$frequent_event_id
	 * @uses CalendarSolution_List::$from
	 * @uses CalendarSolution_List::$to
	 * @uses CalendarSolution_List::$prior_from
	 * @uses CalendarSolution_List::$prior_to
	 * @uses CalendarSolution_List::$next_from
	 * @uses CalendarSolution_List::$next_to
	 */
	protected function get_date_navigation() {
		$query = array();
		if (empty($_SERVER['REQUEST_URI'])) {
			$path = '';
		} else {
			$request = explode('?', $_SERVER['REQUEST_URI']);
			$path = empty($request[0]) ? '' : $request[0];
			if (!empty($request[1])) {
				parse_str($request[1], $query);
			}
		}

		$query['category_id'] = empty($this->category_id)
				? null : $this->category_id;
		$query['frequent_event_id'] = empty($this->frequent_event_id)
				? null : $this->frequent_event_id;
		$query['view'] = $this->view;

		$query['from'] = $this->prior_from->format('Y-m-d');
		$query['to'] = $this->prior_to->format('Y-m-d');

		$out = '<table class="cs_nav" width="100%">' . "\n"
			 . " <tr>\n"
			 . '  <td>' . "\n"
			 . '   <a href="'
			 . $path . '?' . http_build_query($query, '', '&amp;')
			 . '">&lt; See Earlier Events</a>'
			 . "  </td>\n";

		$query['from'] = $this->next_from->format('Y-m-d');
		$query['to'] = $this->next_to->format('Y-m-d');

		$out .= '  <td align="right">' . "\n"
			 . '   <a href="'
			 . $path . '?' . http_build_query($query, '', '&amp;')
			 . '">See Later Events &gt;</a>' . "\n"
			 . "  </td>\n"
			 . " </tr>\n";

		$query['from'] = $this->from->format('Y-m-d');
		$query['to'] = $this->to->format('Y-m-d');
		unset($query['view']);

		$out .= " <tr>\n"
			 . '  <td colspan="2" align="center">' . "\n"
			 . 'View the events in '
			 . '   <a href="'
			 . $path . '?' . http_build_query($query, '', '&amp;')
			 . '&amp;view='
			 . (($this->view == 'Calendar') ? 'List">List' : 'Calendar">Calendar')
			 . '</a> format.' . "\n";

		$out .= "  </td>\n"
			 . " </tr>\n"
			 . "</table>\n";

		return $out;
	}

	/**
	 * Produces the HTML for the form people can use to pick date ranges
	 * and particular events
	 *
	 * @return string  the HTML for the limit form
	 *
	 * @uses CalendarSolution_List::$view
	 * @uses CalendarSolution_List::$category_id
	 * @uses CalendarSolution_List::$frequent_event_id
	 * @uses CalendarSolution_List::$from
	 * @uses CalendarSolution_List::$to
	 */
	protected function get_limit_form() {
		$out = '<form class="cs_limit" method="get">';

		$out .= '<table>'
			 . "\n" . ' <tr>' . "\n"
			 . "  <td>\n\n";

		$out .= "\n\n" . '<p class="cs_instructions"><small>'
			 . "\n You can limit the list of events by dates\n"
			 . " or the event type or a combination thereof."
			 . "<br />To view all events, click on the\n"
			 . " &quot;Remove All Limits&quot; button.\n"
			 . "</small></p>\n\n";

		$out .= '<p class="cs_date"><small><u>L</u>imit'
			 . ' to dates between';

		$out .= ' <input type="text" size="11" maxlength="10"'
			 . ' name="from" accesskey="l" value="'
			 . $this->from->format('Y-m-d') . '" />' . "\n";

		$out .= ' and <input type="text" size="11" maxlength="10" '
			 . 'name="to" value="'
			 . $this->to->format('Y-m-d') . '" /> (inclusive).</small></p>' . "\n";

		$out .= '<p class="cs_bottom"><small>';

		$out .= '<label for="category_id">'
			 . 'Categories:</label>' . "\n";

		if ($this->use_cache) {
			$cache_key = 'category_list:' . $this->category_id;
			$list = $this->cache->get($cache_key);
			$memcache_result = ($list !== false);
		} else {
			$memcache_result = null;
		}
		if (!$memcache_result) {
			$opt = array(
				'id'           => 'category_id',
				'table'        => 'cs_category',
				'visiblefield' => 'category',
				'keyfield'     => 'category_id',
				'name'         => 'category_id',
				'orderby'      => 'category',
				'where'        => '1 = 1',
				'multiple'     => 'Y',
				'size'         => '2',
				'default'      => $this->category_id,
				'add'          => array('' => 'Pick Categories, if you want to')
			);

			$list = $this->sql->GetOptionListGenerator(__FILE__, __LINE__, $opt);

			if ($this->use_cache) {
				$this->cache->set($cache_key, $list);
			}
		}
		$out .= $list;

		$out .= "</p>\n";

		$out .= '<p class="cs_bottom"><small>';

		$out .= '<label for="frequent_event_id" accesskey="r">'
			 . 'F<u>r</u>equent Events:</label>' . "\n";

		if ($this->use_cache) {
			$cache_key = 'frequent_event_list:' . $this->frequent_event_id;
			$list = $this->cache->get($cache_key);
			$memcache_result = ($list !== false);
		} else {
			$memcache_result = null;
		}
		if (!$memcache_result) {
			$opt = array(
				'id'           => 'frequent_event_id',
				'table'        => 'cs_frequent_event',
				'visiblefield' => 'frequent_event',
				'keyfield'     => 'frequent_event_id',
				'name'         => 'frequent_event_id',
				'orderby'      => 'frequent_event',
				'where'        => '1 = 1',
				'multiple'     => 'N',
				'size'         => '0',
				'default'      => $this->frequent_event_id,
				'add'          => array('' => 'Pick a Frequent Event, if you want to')
			);

			$list = $this->sql->GetOptionListGenerator(__FILE__, __LINE__, $opt);

			if ($this->use_cache) {
				$this->cache->set($cache_key, $list);
			}
		}
		$out .= $list;

		$out .= '   <input type="hidden" name="view" value="'
			 . $this->view . '" />' . "\n";

		$out .= '<input type="submit" name="limit" value="Limit" />
			<input type="submit" name="remove_limit" value="Remove All Limits" />
			</small></p>
		  </td>
		 </tr>
		</table>
		</form>';

		return $out;
	}

	/**
	 * Produces the prior/next links for the "QuickTable" and "Title" formats
	 *
	 * @return string  the navigation elements if the $start parameter is
	 *                 enabled in set_limit(), an empty string if not
	 *
	 * @see CalendarSolution_List::set_limit()
	 * @see CalendarSolution_List::set_show_limit_navigation()
	 */
	protected function get_limit_navigation() {
		if (!is_numeric($this->limit_start)) {
			return '';
		}

		$query = array();
		if (empty($_SERVER['REQUEST_URI'])) {
			$path = '';
		} else {
			$request = explode('?', $_SERVER['REQUEST_URI']);
			$path = empty($request[0]) ? '' : $request[0];
			if (!empty($request[1])) {
				parse_str($request[1], $query);
			}
		}

		$prior_link = '&lt; prior';
		$prior_start = $this->limit_start - $this->limit_quantity;
		if ($prior_start > -$this->limit_quantity) {
			if ($prior_start < 0) {
				$prior_start = 0;
			}
			$query['limit_start'] = $prior_start;
			$prior_link = '<a href="' . $path . '?'
				. http_build_query($query, '', '&amp;') . '">' . $prior_link . '</a>';
		}

		$next_link = 'next &gt;';
		$next_start = $this->limit_start + $this->limit_quantity;
		if ($next_start < $this->total_rows) {
			$query['limit_start'] = $next_start;
			$next_link = '<a href="' . $path . '?'
				. http_build_query($query, '', '&amp;') . '">' . $next_link . '</a>';
		}

		return '<div class="cs_prior">' . $prior_link
			. '</div><div class="cs_next">' . $next_link . '</div>';
	}

	/**
	 * Figures out what link should be shown for this item
	 *
	 * @param array $event  the associative array of the current item
	 *
	 * @return string  the hyperlink and/or title
	 */
	public function get_link($event) {
		if ($this->is_admin()) {
			$uri = 'calendar-detail.php?calendar_id=' . $event['calendar_id'];
		} else {
			switch ($event['list_link_goes_to_id']) {
				case self::LINK_TO_DETAIL_PAGE:
					$uri = 'calendar-detail.php?calendar_id=' . $event['calendar_id'];
					break;
				case self::LINK_TO_FREQUENT_EVENT_URI:
					$uri = $event['frequent_event_uri'];
					break;
				case self::LINK_TO_CALENDAR_URI:
					$uri = $event['calendar_uri'];
					break;
				default:
					return $event['title'];
			}
		}

		return '<a href="' . $uri . '">' . $event['title'] . '</a>';
	}

	/**
	 * Creates a DateIntervalSolution object indicating how many months should be
	 * displayed at one time
	 *
	 * @return DateIntervalSolution
	 *
	 * @uses CalendarSolution_List::$interval_spec  to know how long the
	 *       interval should be
	 */
	protected function interval_singleton() {
		static $out;
		if (!isset($out)) {
			$out = new DateIntervalSolution($this->interval_spec);
		}
		return $out;
	}

	/**
	 * Assembles the query string then executes it
	 *
	 * @return void
	 *
	 * @uses CalendarSolution_List::set_where_sql()  to generate the SQL's
	 *       WHERE clause and needed cache keys
	 * @uses CalendarSolution_List::$limit_quantity  to limit the number of
	 *       rows shown
	 * @uses CalendarSolution_List::$limit_start  to determine where to start
	 *       the number of rows to be shown
	 * @uses CalendarSolution::$cache  to cache the results, if possible
	 * @uses CalendarSolution_List::$data  to hold the results
	 * @uses CalendarSolution_List::$where_sql  as the query's WHERE clause
	 * @uses CalendarSolution_List::$cache_key  to know where the data is stored
	 * @uses CalendarSolution_List::$cache_count_key  to know where the row
	 *       count is stored
	 *
	 * @throws CalendarSolution_Exception if to is later than from
	 */
	protected function run_query() {
		if (!$this->where_sql) {
			$this->set_where_sql();
		}

		$start_set = is_numeric($this->limit_start);

		/*
		 * Get data from cache, if available.
		 */

		if ($this->use_cache) {
			$this->data = $this->cache->get($this->cache_key);
			if ($this->data !== false) {
				if ($start_set) {
					$this->total_rows = $this->cache->get($this->cache_count_key);
				}
				return;
			}
		}

		/*
		 * Compose LIMIT and run COUNT if needed.
		 */

		$limit_sql = '';
		if ($this->limit_quantity) {
			$limit_sql = "
				LIMIT " . $this->limit_quantity
				. " OFFSET " . (int) $this->limit_start;

			if ($start_set) {
				$this->sql->SQLQueryString = "SELECT COUNT(*) AS ct
					FROM cs_calendar
					$this->where_sql";

				$this->sql->RunQuery(__FILE__, __LINE__);
				$event = $this->sql->RecordAsEnumArray(__FILE__, __LINE__);
				$this->total_rows = $event[0];
			}
		}

		/*
		 * Construct the SQL string.
		 */

		$this->sql->SQLQueryString = "SELECT
			calendar_id,
			calendar_uri,
			changed,
			date_start,
			frequent_event_uri,
			list_link_goes_to_id,
			location_start,
			note,
			status,
			cs_calendar.status_id AS status_id,
			summary,
			time_end,
			time_start,
			title
			FROM cs_calendar
			LEFT JOIN cs_frequent_event USING (frequent_event_id)
			LEFT JOIN cs_status
				ON (cs_status.status_id = cs_calendar.status_id)";

		$this->sql->SQLQueryString .= $this->where_sql;

		$this->sql->SQLQueryString .= "
			ORDER BY date_start, time_start, title,
				cs_calendar.frequent_event_id";

		$this->sql->SQLQueryString .= $limit_sql;

		$this->sql->RunQuery(__FILE__, __LINE__);

		/*
		 * Process database results.
		 */

		$this->data = array();
		$skip_markup = array(
			'calendar_id',
			'calendar_uri',
			'changed',
			'date_start',
			'frequent_event_uri',
			'list_link_goes_to_id',
			'status_id',
			'time_end',
			'time_start',
			'title',
		);
		while ($event = $this->sql->RecordAsAssocArray(
				__FILE__, __LINE__, $skip_markup))
		{
			$this->data[] = $event;
		}
		$this->sql->ReleaseRecordSet(__FILE__, __LINE__);

		if ($this->use_cache) {
			$this->cache->set($this->cache_key, $this->data);
			if ($start_set) {
				$this->cache->set($this->cache_count_key, $this->total_rows);
			}
		}
	}

	/**
	 * Sets the "category_id" property to the appropriate value
	 *
	 * @param mixed $in  + NULL = use value of $_REQUEST['category_id']
	 *                   though set it to FALSE if it is not set or invalid
	 *                   + FALSE = set the value to FALSE
	 *                   + integer = an integer, falling back to FALSE if
	 *                   the input is is invalid
	 *                   + array of integers = if each element is not an integer
	 *                   then the value will be set to FALSE
	 * @return void
	 *
	 * @uses CalendarSolution::get_int_array_from_request()  to determine the
	 *       user's intention
	 * @uses CalendarSolution_List::$category_id  to store the data
	 */
	public function set_category_id($in = null) {
		if ($in === null) {
			$out = $this->get_int_array_from_request('category_id');
		} else {
			if (!is_array($in)) {
				$in = array($in);
			}
			$out = array();
			foreach ($in as $value) {
				if (!is_scalar($value)
					|| !preg_match('/^\d{1,10}$/', $value))
				{
					$this->category_id = false;
					return;
				}
				$out[] = $value;
			}
		}

		$this->category_id = $out;
	}

	/**
	 * Sets the date format to be used by our format_date() method
	 *
	 * @param int $in  the format used by PHP's date() function
	 *
	 * @return void
	 *
	 * @uses CalendarSolution_List::$date_format_id  to store the data
	 * @see CalendarSolution::format_date()
	 */
	public function set_date_format($in) {
		$this->date_format = $in;
	}

	/**
	 * Sets the "frequent_event_id" property to the appropriate value
	 *
	 * @param mixed $in  + NULL = use value of $_REQUEST['frequent_event_id']
	 *                   though set it to FALSE if it is not set or invalid
	 *                   + FALSE = set the value to FALSE
	 *                   + integer = an integer, falling back to FALSE if
	 *                   the input is is invalid
	 * @return void
	 *
	 * @uses CalendarSolution::get_int_from_request()  to determine the
	 *       user's intention
	 * @uses CalendarSolution_List::$frequent_event_id  to store the data
	 */
	public function set_frequent_event_id($in = null) {
		if ($in === null) {
			$in = $this->get_int_from_request('frequent_event_id');
		}
		if (!is_scalar($in) || !preg_match('/^\d{1,10}$/', $in)) {
			$in = false;
		}

		$this->frequent_event_id = $in;
	}

	/**
	 * Sets the "from" property (defaults to today)
	 *
	 * @param mixed $in  + NULL = use value of $_REQUEST['from'] though uses
	 *                   the default if it is not set or invalid
	 *                   + TRUE = use value of $_REQUEST['from'] if it is set,
	 *                   use the default if it is invalid, use FALSE if not set
	 *                   + FALSE = set the value to FALSE
	 *                   + string = a date in YYYY-MM-DD format though uses
	 *                   the default if it is invalid
	 * @return void
	 *
	 * @uses CalendarSolution::get_date_from_request()  to determine the
	 *       user's intention
	 * @uses CalendarSolution_List::$from  to store the data
	 */
	public function set_from($in = null) {
		if ($in === null) {
			$in = $this->get_date_from_request('from');
		} elseif ($in === true) {
			$in = $this->get_date_from_request('from');
			if ($in === null) {
				$this->from = false;
				return;
			}
		} elseif ($in === false) {
			$this->from = false;
			return;
		}

		try {
			$this->from = new DateTimeSolution($in);
		} catch (Exception $e) {
			$this->from = new DateTimeSolution;
		}
	}

	/**
	 * Sets the "limit_quantity" and "limit_start" properties
	 *
	 * To have lists show only the first ten events: <kbd>set_limit(10)</kbd>
	 *
	 * To have lists show the first ten events and possibly show navigation
	 * to later events: <kbd>set_limit(10, null)</kbd>.  One must also call
	 * <kbd>set_show_limit_navigation()</kbd> to get the navigation to show up.
	 *
	 * @param mixed $quantity  the number of rows to show
	 *              + NULL = use value of $_REQUEST['limit_quantity']
	 *              though set it to FALSE if it is not set or invalid
	 *              + FALSE = set the value to FALSE
	 *              + integer = an integer, falling back to FALSE if
	 *              the input is is invalid
	 * @param mixed $start  the row to start on.  Is zero indexed, so 0 starts
	 *              on the first row, 10 starts on the 11th row, etc.
	 *              + NULL = use value of $_REQUEST['limit_start']
	 *              though set it to 0 if it is not set or FALSE if invalid
	 *              + FALSE = set the value to FALSE
	 *              + integer = an integer, falling back to FALSE if
	 *              the input is is invalid
	 *              + Gets set to FALSE if $quantity is empty()
	 *
	 * @return void
	 *
	 * @see CalendarSolution_List::set_show_limit_navigation()
	 *
	 * @uses CalendarSolution::get_int_from_request()  to determine the
	 *       user's intention
	 * @uses CalendarSolution_List::$limit_quantity  to store the data
	 * @uses CalendarSolution_List::$limit_start  to store the data
	 */
	public function set_limit($quantity, $start = false) {
		if ($quantity === null) {
			$quantity = $this->get_int_from_request('limit_quantity');
		}
		if (!is_scalar($quantity) || !preg_match('/^\d{1,10}$/', $quantity)) {
			$quantity = false;
		}
		$this->limit_quantity = $quantity;

		if (!$quantity || $start === false) {
			$this->limit_start = false;
			return;
		}

		if ($start === null) {
			$start = $this->get_int_from_request('limit_start');
			if ($start === null) {
				$this->limit_start = 0;
				return;
			}
		}
		if (!is_scalar($start) || !preg_match('/^\d{1,10}$/', $start)) {
			$start = false;
		}
		$this->limit_start = $start;
	}

	/**
	 * Sets the Featured Page "page_id" property to the appropriate value
	 *
	 * @param int $in  the featured page id to get the list for
	 *
	 * @return void
	 *
	 * @uses CalendarSolution_List::$page_id  to store the data
	 */
	public function set_page_id($in) {
		if (!is_scalar($in) || !preg_match('/^\d{1,10}$/', $in)) {
			$in = false;
		}
		$this->page_id = $in;
	}

	/**
	 * Sets the properties used later when generating the navigation elements
	 * for getting to earlier and later events
	 *
	 * @return void
	 *
	 * @uses CalendarSolution_List::$from
	 * @uses CalendarSolution_List::$to
	 * @uses CalendarSolution_List::interval_singleton()
	 * @uses CalendarSolution_List::$prior_from
	 * @uses CalendarSolution_List::$prior_to
	 * @uses CalendarSolution_List::$next_from
	 * @uses CalendarSolution_List::$next_to
	 *
	 * @throws CalendarSolution_Exception if $this->from hasn't been set yet
	 */
	protected function set_prior_and_next_dates() {
		if ($this->from === false) {
			return;
		} elseif ($this->from === null || $this->to === null) {
			throw new CalendarSolution_Exception('set_from() and set_to()'
				. ' must be called before set_prior_and_next_dates()');
		}

		$one_day_interval = new DateIntervalSolution('P1D');

		$this->prior_to = new DateTimeSolution($this->from->format('Y-m-d'));
		$this->prior_to->sub($one_day_interval);

		$this->prior_from = new DateTimeSolution($this->prior_to->format('Y-m-d'));
		$this->prior_from->modify('first day of this month');
		$this->prior_from->sub($this->interval_singleton());

		$this->next_from = new DateTimeSolution($this->to->format('Y-m-d'));
		$this->next_from->add($one_day_interval);

		$this->next_to = new DateTimeSolution($this->next_from->format('Y-m-d'));
		$this->next_to->add($this->interval_singleton());
		$this->next_to->modify('last day of this month');
	}

	/**
	 * Sets all properties that can be populated with $_REQUEST data, but
	 * does so only for properties that have not been set yet
	 *
	 * @return void
	 *
	 * @uses CalendarSolution_List_Calendar::set_from()  to set the "from" date
	 *       when in Calendar view
	 * @uses CalendarSolution_List_List::set_from()  to set the "from" date
	 *       when in List view
	 * @uses CalendarSolution_List::set_to()  to set the "to" date
	 * @uses CalendarSolution_List::set_category_id()  to set the category id
	 * @uses CalendarSolution_List::set_frequent_event_id()  to set the frequent
	 *       event id
	 */
	public function set_request_properties() {
		if ($this->from === null) {
			$this->set_from();
		}

		if ($this->to === null) {
			$this->set_to();
		}

		if ($this->category_id === null) {
			$this->set_category_id();
		}

		if ($this->frequent_event_id === null) {
			$this->set_frequent_event_id();
		}
	}

	/**
	 * Should cancelled events be shown or not?
	 *
	 * @param bool $in  set it to FALSE to leave out cancelled events
	 *
	 * @return void
	 *
	 * @uses CalendarSolution_List::$show_cancelled  to store the decision
	 */
	public function set_show_cancelled($in) {
		$this->show_cancelled = (bool) $in;
	}

	/**
	 * Turns the paging navigation on or off in the "QuickTable" and "Title"
	 * formats
	 *
	 * NOTE: The navigation elements will only show up if set_limit() has
	 * been called and its $start parameter has been enabled.  For example:
	 * <kbd>set_limit(10, null)</kbd>
	 *
	 * @param bool $in  set it to TRUE to include the navigation
	 *
	 * @return void
	 *
	 * @see CalendarSolution_List::set_limit()
	 *
	 * @uses CalendarSolution_List::$show_limit_navigation  to store the
	 *       decision
	 */
	public function set_show_limit_navigation($in) {
		$this->show_limit_navigation = (bool) $in;
	}

	/**
	 * Turns the Summary field on or off when showing the "List" format
	 *
	 * @param bool $in  set it to FALSE to turn it off, is on by default
	 *
	 * @return void
	 *
	 * @uses CalendarSolution_List::$show_summary  to store the decision
	 */
	public function set_show_summary($in) {
		$this->show_summary = (bool) $in;
	}

	/**
	 * Sets the "to" property (defaults to the last day of the month
	 * occurring $interval_spec months from today)
	 *
	 * @param mixed $in  + NULL = use value of $_REQUEST['to'] though uses
	 *                   the default if it is not set or invalid
	 *                   + TRUE = use value of $_REQUEST['to'] if it is set,
	 *                   use the default if it is invalid, use FALSE if not set
	 *                   + FALSE = set the value to FALSE
	 *                   + string = a date in YYYY-MM-DD format though uses
	 *                   the default if it is invalid
	 * @return void
	 *
	 * @uses CalendarSolution_List::$to  to store the data
	 * @uses CalendarSolution::get_date_from_request()  to determine the
	 *       user's intention
	 * @uses CalendarSolution_List::interval_singleton()  if the value needs
	 *       to be set to the default
	 */
	public function set_to($in = null) {
		if ($in === null) {
			$in = $this->get_date_from_request('to');
		} elseif ($in === true) {
			$in = $this->get_date_from_request('to');
			if ($in === null) {
				$this->to = false;
				return;
			}
		} elseif ($in === false) {
			$this->to = false;
			return;
		}

		if ($in) {
			$add_months = false;
		} else {
			$add_months = true;
		}

		try {
			$this->to = new DateTimeSolution($in);
		} catch (Exception $e) {
			$add_months = true;
			$this->to = new DateTimeSolution;
		}

		if ($add_months) {
			$this->to->add($this->interval_singleton());
			$this->to->modify('last day of this month');
		}
	}

	/**
	 * Generates the SQL WHERE clause and cache keys for data retrieval
	 *
	 * This has been separated out from run_query() to facilitate cache
	 * lookups by the various get_rendering() methods.
	 *
	 * @uses CalendarSolution_List::$where_sql  to hold the WHERE clause
	 * @uses CalendarSolution_List::$cache_key  to indicate where the data
	 *       should be stored
	 * @uses CalendarSolution_List::$cache_count_key  to indicate where the row
	 *       count should be stored
	 * @uses CalendarSolution_List::$from  to know the start of the date range
	 * @uses CalendarSolution_List::$to  to know the end of the date range
	 * @uses CalendarSolution_List::$category_id  to limit entries to a
	 *       particular category if so desired
	 * @uses CalendarSolution_List::$frequent_event_id  to limit entries to a
	 *       particular event if so desired
	 * @uses CalendarSolution_List::$page_id  to limit entries to those that
	 *       are set to be featured on the given page id
	 */
	protected function set_where_sql() {
		/*
		 * Establish WHERE elements.
		 */

		$where = array();

		if ($this->from && $this->to) {
			if ($this->from > $this->to) {
				throw new CalendarSolution_Exception("'from' can not be after 'to'");
			}

			$where[] = "date_start BETWEEN '"
				. $this->from->format('Y-m-d')
				. "' AND '" . $this->to->format('Y-m-d') . "'";
		} elseif ($this->from) {
			$where[] = "date_start >= '"
				. $this->from->format('Y-m-d') . "'";
		} elseif ($this->to) {
			$where[] = "date_start <= '"
				. $this->to->format('Y-m-d') . "'";
		}

		if ($this->category_id) {
			$where[] = "cs_calendar.category_id IN ("
				. implode(', ', $this->category_id) . ")";
		}

		if ($this->frequent_event_id) {
			$where[] = "cs_calendar.frequent_event_id = "
				. (int) $this->frequent_event_id;
		}

		if ($this->page_id) {
			if ($this->sql->SQLClassName == 'SQLSolution_PostgreSQLUser') {
				$where[] = "CAST(feature_on_page_id & " . (int) $this->page_id
					. " AS BOOLEAN)";
			} else {
				$where[] = "feature_on_page_id & " . (int) $this->page_id;
			}
		}

		if (!$this->show_cancelled) {
			$where[] = "cs_calendar.status_id <> "
				. $this->sql->Escape(__FILE__, __LINE__, self::STATUS_CANCELLED);
		}

		if ($where) {
			$this->where_sql = "\n WHERE " . implode(' AND ', $where);
		} else {
			$this->where_sql = '';
		}

		if ($this->use_cache) {
			$where_md5 = md5($this->where_sql);
			$this->cache_key = 'calendar_solution:' . $where_md5;

			if ($this->limit_quantity) {
				$this->cache_key .= ':' . $this->limit_quantity
					. '@' . (int) $this->limit_start;
				$this->cache_count_key = 'calendar_solution:count:' . $where_md5;
			}
		}
	}
}
