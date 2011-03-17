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
	 * Has set_prior_and_next_dates() been called?
	 * @var bool
	 */
	protected $called_set_prior_and_next_dates = false;

	/**
	 * Has set_request_properties() been called?
	 * @var bool
	 */
	protected $called_set_request_properties = false;

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
	 * The DateIntervalSolution specification for how many months to show at once
	 * @var string
	 */
	protected $interval_spec;

	/**
	 * Only show events sponsored by you ("Y") or only those sponsored by
	 * another organization ("N")?
	 *
	 * All events are shown if this is empty.
	 *
	 * @var string  ("Y" or "N")
	 */
	protected $is_own_event;

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
	 * The furthest date in the future users are allowed to see
	 * @var DateTimeSolution
	 */
	protected $permit_future_date;

	/**
	 * The furthest date in the past users are allowed to see
	 * @var DateTimeSolution
	 */
	protected $permit_history_date;

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
	 * Should the Location field be shown or not when viewing "Calendar" mode?
	 *
	 * NOTE: needs to be in CalendarSolution_List class due to ability switch
	 * between Calendar and List views.
	 *
	 * @var bool
	 */
	protected $show_location = true;

	/**
	 * Show your own events before events produced by other organizations?
	 * @var bool
	 */
	protected $show_own_events_first = false;

	/**
	 * Should the Summary field be shown or not when viewing "List" mode?
	 *
	 * NOTE: needs to be in CalendarSolution_List class due to ability switch
	 * between Calendar and List views.
	 *
	 * @var bool
	 */
	protected $show_summary = true;

	/**
	 * Format for PHP's date() function, to be used by our format_date() method
	 *
	 * @see CalendarSolution_List::set_time_format()
	 * @see CalendarSolution::format_date()
	 * @var string
	 */
	protected $time_format = self::DATE_FORMAT_TIME_12AP;

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
	 * Data from the REQUEST_URI broken into an associative array containing
	 * the 'path' as a string and the 'query' broken into a sub-array
	 * @var array
	 */
	protected $uri;

	/**
	 * The SQL WHERE clause for the current view
	 * @var string
	 */
	protected $where_sql;


	/**
	 * Calls the parent constructor and set_uri() then populates the
	 * "$interval_spec" property
	 *
	 * @param integer $months  how many months should be shown at once
	 * @param string $dbms  optional override of the database extension setting
	 *                      in CALENDAR_SOLUTION_DBMS.  Values can be
	 *                      "mysql", "mysqli", "pgsql", "sqlite", "sqlite3".
	 *
	 * @uses CalendarSolution::__construct()  to instantiate the database and
	 *       cache classes
	 * @uses CalendarSolution_List::$interval_spec  is set based on the
	 *       "$months" parameter; is used later when creating new
	 *       DateIntervalSolution objects
	 * @uses CalendarSolution_List::set_uri()  to set the "$uri" property
	 */
	public function __construct($months = 3, $dbms = CALENDAR_SOLUTION_DBMS) {
		parent::__construct($dbms);
		$this->interval_spec = 'P' . ($months - 1) . 'M';
		$this->set_uri();
	}

	/**
	 * Looks at $_REQUEST['view'] then $_COOKIE['CalendarSolution']
	 * to determine whether to return a CalendarSolution_List_Calendar
	 * (the default) or CalendarSolution_List_List object
	 *
	 * WARNING: this function sets a cookie, so MUST be called before ANY
	 * output is sent to the browser.
	 *
	 * @param integer $months  how many months should be shown at once
	 * @param string $dbms  optional override of the database extension setting
	 *                      in CALENDAR_SOLUTION_DBMS.  Values can be
	 *                      "mysql", "mysqli", "pgsql", "sqlite", "sqlite3".
	 *
	 * @return CalendarSolution_List_Calendar|CalendarSolution_List_List
	 *
	 * @todo Adjust cookie expiration date as the year 2038 approaches.
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
	 * Produces the HTML for changing between "List" and "Calendar" formats
	 *
	 * @param string $phrase  the sprintf formatted string to be displayed.
	 *                        Must contain one "%s" conversion specification,
	 *                        where the "$list" or "$calendar" parameter text
	 *                        will be inserted by this method as appropriate
	 * @param string $list  the text for the "list" view.  The value is passed
	 *                      through htmlspecialchars().
	 * @param string $calendar  the text for the "calendar" view.  The value is
	 *                          passed through htmlspecialchars().
	 *
	 * @return string  the HTML for switching views
	 *
	 * @uses CalendarSolution_List::set_request_properties()  to determine the
	 *       user's intent
	 * @uses CalendarSolution_List::$view  to know the current view
	 * @uses CalendarSolution_List::$category_id  to know the current category
	 * @uses CalendarSolution_List::$frequent_event_id  to know the curren event
	 * @uses CalendarSolution_List::$from  to know the current from date
	 * @uses CalendarSolution_List::$to  to know the current to date
	 * @uses CalendarSolution_List::$uri  to know the current URI
	 *
	 * @throws CalendarSolution_Exception if not in Calendar or List view
	 *
	 * @since Method available since version 3.0.0
	 */
	public function get_change_view($phrase = 'View the events in %s format',
			$list = 'list', $calendar = 'calendar')
	{
		if (!$this->called_set_request_properties) {
			$this->set_request_properties();
		}

		$uri = $this->uri;

		$uri['query']['category_id'] = empty($this->category_id)
				? null : $this->category_id;
		$uri['query']['frequent_event_id'] = empty($this->frequent_event_id)
				? null : $this->frequent_event_id;
		$uri['query']['from'] = empty($this->from)
				? null : $this->from->format('Y-m-d');
		$uri['query']['to'] = empty($this->to)
				? null : $this->to->format('Y-m-d');

		if ($this->view == 'Calendar') {
			$uri['query']['view'] = 'List';
			$anchor = htmlspecialchars($list);
		} elseif ($this->view == 'List') {
			$uri['query']['view'] = 'Calendar';
			$anchor = htmlspecialchars($calendar);
		} else {
			throw new CalendarSolution_Exception('get_change_view() for use'
					. ' only with Calendar or List views');
		}

		$link = '<a href="'
			. htmlspecialchars($uri['path'] . '?' . http_build_query($uri['query']))
			. '">' . $anchor . '</a>';

		$format = '<div class="cs_change_view">' . $phrase . "</div>\n";
		return sprintf($format, $link);
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
	 *
	 * Inteneded for use with the "Calendar" and "List" formats.
	 *
	 * NOTE: will return an empty string if "$from" or "$to" are false.
	 *
	 * @param string $prior_link  the text for the "prior" link.  The value is
	 *                      passed through htmlspecialchars().
	 * @param string $next_link  the text for the "next" link.  The value is
	 *                      passed through htmlspecialchars().
	 *
	 * @return string  the navigation section's HTML
	 *
	 * @uses CalendarSolution_List::set_request_properties()  to determine the
	 *       user's intent
	 * @uses CalendarSolution_List::set_prior_and_next_dates()  to determine
	 *       dates for the prior and next links
	 * @uses CalendarSolution_List::set_permit_history_months()  to limit how
	 *       far back people can go
	 * @uses CalendarSolution_List::set_permit_future_months()  to limit how
	 *       far in the future people can go
	 * @uses CalendarSolution_List::$view  to know the current view
	 * @uses CalendarSolution_List::$category_id  to know the current category
	 * @uses CalendarSolution_List::$frequent_event_id  to know the curren event
	 * @uses CalendarSolution_List::$from  to know the current from date
	 * @uses CalendarSolution_List::$to  to know the current to date
	 * @uses CalendarSolution_List::$prior_from  for the "prior" link's from date
	 * @uses CalendarSolution_List::$prior_to  for the "prior" link's to date
	 * @uses CalendarSolution_List::$next_from  for the "next" link's from date
	 * @uses CalendarSolution_List::$next_to  for the "next" link's to date
	 * @uses CalendarSolution_List::$uri  to know the current URI
	 *
	 * @since Method available since version 3.0.0
	 */
	public function get_date_navigation($prior_link = '< See Earlier Events',
			$next_link = 'See Later Events >')
	{
		if (!$this->called_set_request_properties) {
			$this->set_request_properties();
		}
		if ($this->from === false || $this->to === false) {
			return '';
		}
		if (!$this->called_set_prior_and_next_dates) {
			$this->set_prior_and_next_dates();
		}
		if ($this->permit_history_date === null) {
			$this->set_permit_history_months();
		}
		if ($this->permit_future_date === null) {
			$this->set_permit_future_months();
		}

		$uri = $this->uri;

		$uri['query']['category_id'] = empty($this->category_id)
				? null : $this->category_id;
		$uri['query']['frequent_event_id'] = empty($this->frequent_event_id)
				? null : $this->frequent_event_id;
		$uri['query']['view'] = $this->view;

		$uri['query']['from'] = $this->prior_from->format('Y-m-d');
		$uri['query']['to'] = $this->prior_to->format('Y-m-d');

		$out = '<div class="cs_date_navigation">';
		$out .= '<div class="cs_prior">';
		if ($this->prior_to > $this->permit_history_date) {
			$out .= '<a href="'
				. htmlspecialchars($uri['path'] . '?' . http_build_query($uri['query']))
				. '">' . htmlspecialchars($prior_link) . '</a>';
		}
		$out .= '</div>';

		$uri['query']['from'] = $this->next_from->format('Y-m-d');
		$uri['query']['to'] = $this->next_to->format('Y-m-d');

		$out .= '<div class="cs_next">';
		if ($this->next_from < $this->permit_future_date
			|| $this->permit_future_date === false)
		{
			$out .= '<a href="'
				. htmlspecialchars($uri['path'] . '?' . http_build_query($uri['query']))
				. '">' . htmlspecialchars($next_link) . '</a>';
		}
		$out .= '</div>';

		$out .= "</div>\n";

		return $out;
	}

	/**
	 * Produces the HTML for the form people can use to pick date ranges
	 * and particular events
	 *
	 * NOTE: "datelist" will not be displayed if "$from", "$to",
	 * "$permit_history_date", or "$permit_future_date" are false.
	 *
	 * @param array $show  a list of elements to show ("datebox", "datelist",
	 *                     "category", "event", "remove")
	 *
	 * @return string  the HTML for the limit form
	 *
	 * @uses CalendarSolution_List::$category_id  to set the default category
	 * @uses CalendarSolution_List::$frequent_event_id  to set the default event
	 * @uses CalendarSolution_List::$from  to set the default "datebox" date
	 * @uses CalendarSolution_List::$to  to set the default "datebox" date
	 * @uses CalendarSolution_List::$permit_history_date  to determine the
	 *       earliest value in the "datelist" dropdown boxes
	 * @uses CalendarSolution_List::$permit_future_date  to determine the
	 *       latest value in the "datelist" dropdown boxes
	 * @uses CalendarSolution_List::$uri  to know the current URI
	 * @uses CalendarSolution_List::set_request_properties()  to determine the
	 *       users intentions
	 *
	 * @since Method available since version 3.0.0
	 */
	public function get_limit_form(
			$show = array('datebox', 'category', 'event', 'remove'))
	{
		if (!$this->called_set_request_properties) {
			$this->set_request_properties();
		}

		$uri = $this->uri;

		$uri['query']['limit'] = null;
		$uri['query']['from'] = null;
		$uri['query']['to'] = null;
		$uri['query']['category_id'] = null;
		$uri['query']['frequent_event_id'] = null;

		$action = htmlspecialchars($uri['path'] . '?' . http_build_query($uri['query']));

 		$out = '<form class="cs_limit" method="get" action="' . $action . '">'
			. '<div class="cs_limit_form">';

		if (in_array('datebox', $show)) {
			if ($this->from) {
				$from == $this->from->format('Y-m-d');
			} else {
				$from == '';
			}

			if ($this->to) {
				$to == $this->to->format('Y-m-d');
			} else {
				$to == '';
			}

			$out .= '<div class="cs_date_limit_box">'
				. '<label for="from">Limit to dates between </label>'
				. '<input id="from" type="text" size="11" maxlength="10"'
				. ' name="from" value="' . $from . '" />'
				. '<label for="to"> and </label>'
				. '<input id="to" type="text" size="11" maxlength="10" '
				. 'name="to" value="' . $to . '" />' . "\n";
		}

		if (in_array('datelist', $show)
			&& $this->from !== false
			&& $this->to !== false
			&& $this->permit_history_date !== false
			&& $this->permit_future_date !== false)
		{
			if ($this->permit_history_date === null) {
				$this->set_permit_history_months();
			}
			$from = $this->permit_history_date;

			if ($this->permit_future_date === null) {
				$this->set_permit_future_months();
			}

			$one_month = new DateIntervalSolution('P1M');
			$from_list = '';
			$to_list = '';
			$from_view = $this->from->format('Y-m-d');
			$to_view = $this->to->format('Y-m-d');

			while ($from < $this->permit_future_date) {
				$from_i = $from->format('Y-m-d');
				$to_i = $from->format('Y-m-t');

				if ($from_view == $from_i) {
					$from_selected = '" selected="selected';
				} else {
					$from_selected = '';
				}

				if ($to_view == $to_i) {
					$to_selected = '" selected="selected';
				} else {
					$to_selected = '';
				}

				$from_list .= '<option value="' . $from_i . $from_selected
					. '">' . $from_i . "</option>\n";
				$to_list .= '<option value="' . $to_i . $to_selected
					. '">' . $to_i . "</option>\n";

				$from->add($one_month);
			}

			$out .= '<div class="cs_date_limit_list">'
				. '<label for="from">Limit to dates between </label>'
				. '<select id="from" size="0" name="from">'
				. "\n" . $from_list . '</select>';

			$out .= '<label for="to"> and </label>'
				. '<select id="to" size="0" name="to">'
				. "\n" . $to_list . '</select>';

			$out .= "</div>\n";
		}

		if (in_array('category', $show)) {
			$out .= '<div class="cs_category_limit"><label for="category_id">'
				. 'Categories: </label>';

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
					'multiple'     => 'N',
					'size'         => '0',
					'default'      => $this->category_id,
					'add'          => array('' => 'Pick a Category, if you want to')
				);

				$list = $this->sql->GetOptionListGenerator(__FILE__, __LINE__, $opt);

				if ($this->use_cache) {
					$this->cache->set($cache_key, $list);
				}
			}
			$out .= "$list</div>\n";
		}

		if (in_array('event', $show)) {
			$out .= '<div class="cs_frequent_event_limit">'
				. '<label for="frequent_event_id">'
				. 'Frequent Events: </label>';

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
			$out .= "$list</div>\n";
		}

		$out .= '<div class="cs_submit_limit">'
			. '<input type="submit" name="limit" value="Limit" />';

		if (in_array('remove', $show)) {
			$out .= '<input type="submit" name="remove_limit" value="Remove Limits" />';
		}
		
		$out .= "</div></div>\n";

		return $out;
	}

	/**
	 * Produces the prior/next links for the "QuickTable" and "Title" formats
	 *
	 * NOTE: This must be called after get_rendering().
	 *
	 * NOTE: will return an empty string if "$limit_start" is not numeric.
	 *
	 * @param string $prior_link  the text for the "prior" link.  The value is
	 *                      passed through htmlspecialchars().
	 * @param string $next_link  the text for the "next" link.  The value is
	 *                      passed through htmlspecialchars().
	 *
	 * @return string  the navigation elements if the $start parameter is
	 *                 enabled in set_limit(), an empty string if not
	 *
	 * @see CalendarSolution_List::set_limit()
	 * @uses CalendarSolution_List::$uri  to know the current URI
	 *
	 * @since Method available since version 3.0.0
	 */
	public function get_limit_navigation($prior_link = '< prior',
			$next_link = 'next >')
	{
		if (!is_numeric($this->limit_start)) {
			return '';
		}

		$uri = $this->uri;

		$out = '<div class="cs_limit_navigation"><div class="cs_prior">';
		$prior_start = $this->limit_start - $this->limit_quantity;
		if ($prior_start > -$this->limit_quantity) {
			if ($prior_start < 0) {
				$prior_start = 0;
			}
			$uri['query']['limit_start'] = $prior_start;
			$out .= '<a href="'
				. htmlspecialchars($uri['path'] . '?' . http_build_query($uri['query']))
				. '">' . htmlspecialchars($prior_link) . '</a>';
		}
		$out .= '</div>';

		$out .= '<div class="cs_next">';
		$next_start = $this->limit_start + $this->limit_quantity;
		if ($next_start < $this->total_rows) {
			$uri['query']['limit_start'] = $next_start;
			$out .= '<a href="'
				. htmlspecialchars($uri['path'] . '?' . http_build_query($uri['query']))
				. '">' . htmlspecialchars($next_link) . '</a>';
		}
		$out .= "</div></div>\n";

		return $out;
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
	 * Says which view class is being used
	 *
	 * @return string  the view being used
	 *
	 * @since Method available since version 3.0.0
	 */
	public function get_view() {
		return $this->view;
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

		if ($this->show_own_events_first) {
			$is_own_event = 'is_own_event DESC,';
		} else {
			$is_own_event = '';
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
			is_own_event,
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
			ORDER BY date_start, $is_own_event time_start, title,
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
	 *
	 * @since Method available since version 3.0.0
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
	 * @uses CalendarSolution_List::$date_format  to store the data
	 * @see CalendarSolution::format_date()
	 *
	 * @since Method available since version 3.0.0
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
	 * Sets the "from" property
	 *
	 * CalendarSolution_List::set_from() defaults to today.
	 * CalendarSolution_List_Calendar::set_from() defaults to the first day of
	 * today's month.
	 *
	 * NOTE: "from" is reset to "permit_history_date" if "from" is earlier than
	 * "permit_history_date"
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
	 * @see CalendarSolution_List_Calendar::set_from()
	 *
	 * @uses CalendarSolution::get_date_from_request()  to determine the
	 *       user's intention
	 * @uses CalendarSolution_List::$from  to store the data
	 * @uses CalendarSolution_List::$permit_history_date  for validation
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

		if ($this->from < $this->permit_history_date) {
			$this->from = $this->permit_history_date;
		}
	}

	/**
	 * Sets the "is_own_event" property to the appropriate value
	 *
	 * "Y" means only show events sponsored by your organization.
	 * "N" means only show events sponsored by other organizations.
	 * All events are shown if this is empty/unset.
	 *
	 * @param mixed $in  + NULL = use value of $_REQUEST['is_own_event']
	 *                   though set it to FALSE if it is not set or invalid
	 *                   + FALSE = set the value to FALSE
	 *                   + "Y" or "N" = falling back to FALSE if
	 *                   the input is is invalid
	 * @return void
	 *
	 * @uses CalendarSolution::get_string_from_request()  to determine the
	 *       user's intention
	 * @uses CalendarSolution_List::$is_own_event  to store the data
	 *
	 * @since Method available since version 3.0.0
	 */
	public function set_is_own_event($in = null) {
		if ($in === null) {
			$in = $this->get_string_from_request('is_own_event');
		}
		if (!is_scalar($in) || !preg_match('/^[YN]$/', $in)) {
			$in = false;
		}

		$this->is_own_event = $in;
	}

	/**
	 * Sets the "limit_quantity" and "limit_start" properties
	 *
	 * Intended for use with the "QuickTable" and "Title" formats.
	 *
	 * To have lists show only the first ten events: <kbd>set_limit(10)</kbd>
	 *
	 * To have lists show the first ten events and possibly show navigation
	 * to later events: <kbd>set_limit(10, null)</kbd>.  One must also call
	 * <kbd>get_limit_navigation()</kbd> to get the navigation to show up.
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
	 * @see CalendarSolution_List::get_limit_navigation()
	 *
	 * @uses CalendarSolution::get_int_from_request()  to determine the
	 *       user's intention
	 * @uses CalendarSolution_List::$limit_quantity  to store the data
	 * @uses CalendarSolution_List::$limit_start  to store the data
	 *
	 * @since $start parameter added in version 3.0.0
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
	 * Sets the furthest date in the future users are allowed to see
	 *
	 * NOTE: if the "to" property is later than this, "to" is reset to this.
	 *
	 * @param mixed $months  + integer = the number of months users can see,
	 *                         including the current month.  Default is 12.
	 *                         Falls back to the $months passed to
	 *                         CalendarSolution_List::__construct() if this
	 *                         parameter is less than or equal to that one.
	 *                       + FALSE = unlimited
	 * @return void
	 *
	 * @uses CalendarSolution_List::$permit_future_date  to store the data
	 * @uses CalendarSolution_List::interval_singleton()  if the value needs
	 *       to be set to the default
	 * @uses CalendarSolution_List::$to  for validation
	 *
	 * @since Method available since version 3.0.0
	 */
	public function set_permit_future_months($months = 12) {
		if ($months === false) {
			$this->permit_future_date = false;
			return;
		}

		$this->permit_future_date = new DateTimeSolution;
		$interval = $this->interval_singleton();
		if ($months > $interval->format('%m')) {
			$interval = new DateIntervalSolution('P' . ($months - 1) . 'M');
		}

		$this->permit_future_date->modify('first day of this month');
		$this->permit_future_date->add($interval);
		$this->permit_future_date->modify('last day of this month');

		if ($this->to > $this->permit_future_date) {
			$this->to = $this->permit_future_date;
		}
	}

	/**
	 * Sets the furthest date in the past users are allowed to see
	 *
	 * NOTE: if the "from" property is earlier than this, "from" is reset to
	 * this.
	 *
	 * @param mixed $months  + integer = the number of months users can see,
	 *                         including the current month.  Default is 12.
	 *                         0 = today, 1 = first day of this month.
	 *                       + FALSE = unlimited
	 * @return void
	 *
	 * @uses CalendarSolution_List::$permit_history_date  to store the data
	 * @uses CalendarSolution_List::$from  for validation
	 *
	 * @since Method available since version 3.0.0
	 */
	public function set_permit_history_months($months = 12) {
		if ($months === false) {
			$this->permit_history_date = false;
			return;
		}

		$this->permit_history_date = new DateTimeSolution;
		if ($months > 0) {
			$this->permit_history_date->modify('first day of this month');
			if ($months > 1) {
				$interval = new DateIntervalSolution('P' . ($months - 1) . 'M');
				$this->permit_history_date->sub($interval);
			}
		}

		if ($this->from < $this->permit_history_date) {
			$this->from = $this->permit_history_date;
		}
	}

	/**
	 * Sets the properties used later when generating the navigation elements
	 * for getting to earlier and later events
	 *
	 * NOTE: Does nothing if "$from" or "$to" are false.
	 *
	 * @return void
	 *
	 * @uses CalendarSolution_List::$from  as the basis for the calculations
	 * @uses CalendarSolution_List::$to  as the basis for the calculations
	 * @uses CalendarSolution_List::interval_singleton()
	 * @uses CalendarSolution_List::$prior_from  for the "prior" link's from date
	 * @uses CalendarSolution_List::$prior_to  for the "prior" link's to date
	 * @uses CalendarSolution_List::$next_from  for the "next" link's from date
	 * @uses CalendarSolution_List::$next_to  for the "next" link's to date
	 * @uses CalendarSolution_List::set_permit_history_months() to set bounds on
	 *       how far back the dates can go
	 * @uses CalendarSolution_List::set_permit_future_months() to set bounds on
	 *       how far in the future the dates can go
	 *
	 * @throws CalendarSolution_Exception if "$from" or "$to" has not been set
	 */
	protected function set_prior_and_next_dates() {
		$this->called_set_prior_and_next_dates = true;

		if ($this->from === false || $this->to === false) {
			return;
		} elseif ($this->from === null || $this->to === null) {
			throw new CalendarSolution_Exception('set_from() and set_to()'
				. ' must be called before set_prior_and_next_dates()');
		}

		if ($this->permit_history_date === null) {
			$this->set_permit_history_months();
		}
		if ($this->permit_future_date === null) {
			$this->set_permit_future_months();
		}

		$one_day_interval = new DateIntervalSolution('P1D');

		$this->prior_to = new DateTimeSolution($this->from->format('Y-m-d'));
		$this->prior_to->sub($one_day_interval);

		$this->prior_from = new DateTimeSolution($this->prior_to->format('Y-m-d'));
		$this->prior_from->modify('first day of this month');
		$this->prior_from->sub($this->interval_singleton());

		if ($this->prior_from < $this->permit_history_date) {
			$this->prior_from = $this->permit_history_date;
		}
		if ($this->prior_to < $this->permit_history_date) {
			$this->prior_to = $this->permit_history_date;
		}

		$this->next_from = new DateTimeSolution($this->to->format('Y-m-d'));
		$this->next_from->add($one_day_interval);

		$this->next_to = new DateTimeSolution($this->next_from->format('Y-m-d'));
		$this->next_to->add($this->interval_singleton());
		$this->next_to->modify('last day of this month');

		if ($this->permit_future_date !== false
			&& $this->next_to > $this->permit_future_date)
		{
			$this->next_to = $this->permit_future_date;
		}
		if ($this->permit_future_date !== false
			&& $this->next_from > $this->permit_future_date)
		{
			$this->next_from = $this->permit_future_date;
		}
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
	 * @uses CalendarSolution_List::set_is_own_event()  to set the own event
	 *       flag
	 *
	 * @since Method available since version 3.0.0
	 */
	public function set_request_properties() {
		$this->called_set_request_properties = true;

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

		if ($this->is_own_event === null) {
			$this->set_is_own_event();
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
	 * Turns the Location field on or off when showing the "Calendar" format
	 *
	 * NOTE: needs to be in CalendarSolution_List class due to ability switch
	 * between Calendar and List views.
	 *
	 * @param bool $in  set it to FALSE to turn it off, is on by default
	 *
	 * @return void
	 *
	 * @uses CalendarSolution_List_List::$show_location  to store the decision
	 *
	 * @since Method available since version 3.0.0
	 */
	public function set_show_location($in) {
		$this->show_location = (bool) $in;
	}

	/**
	 * Show your own events before events produced by other organizations?
	 *
	 * Items are normally sorted by date then start time.  Enabling this option
	 * still sorts items by date, but within each day your events are shown
	 * first, sorted by time, then events by organizations are shown sorted by
	 * time.
	 *
	 * @param bool $in  set it to TRUE to enable it, is FALSE by default
	 *
	 * @return void
	 *
	 * @uses CalendarSolution_List::$show_own_events_first  to store the decision
	 *
	 * @since Method available since version 3.0.0
	 */
	public function set_show_own_events_frist($in) {
		$this->show_own_events_first = (bool) $in;
	}

	/**
	 * Turns the Summary field on or off when showing the "List" format
	 *
	 * NOTE: needs to be in CalendarSolution_List class due to ability switch
	 * between Calendar and List views.
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
	 * Sets the time format to be used by our format_date() method
	 *
	 * @param int $in  the format used by PHP's date() function
	 *
	 * @return void
	 *
	 * @uses CalendarSolution_List::$time_format  to store the data
	 * @see CalendarSolution::format_date()
	 *
	 * @since Method available since version 3.0.0
	 */
	public function set_time_format($in) {
		$this->time_format = $in;
	}

	/**
	 * Sets the "to" property (defaults to the last day of the month
	 * occurring $interval_spec months from today)
	 *
	 * NOTE: "to" is reset to "permit_future_date" if "to" is later than
	 * "permit_future_date".
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
	 * @uses CalendarSolution_List::$permit_future_date  for validation
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
			$this->to->modify('first day of this month');
			$this->to->add($this->interval_singleton());
			$this->to->modify('last day of this month');
		}

		if ($this->permit_future_date && $this->to > $this->permit_future_date) {
			$this->to = $this->permit_future_date;
		}
	}

	/**
	 * Breaks up the REQUEST_URI into usable parts
	 *
	 * @return void
	 *
	 * @uses CalendarSolution_List::$uri  to store the data
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
	 * @uses CalendarSolution_List::$is_own_event  to limit entries to those
	 *       sponsored by you or those that are not
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

		if ($this->is_own_event) {
			$where[] = "is_own_event = '$this->is_own_event'";
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
