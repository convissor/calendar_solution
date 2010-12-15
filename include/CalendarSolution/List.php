<?php

/**
 * Calendar Solution's parent class for displaying collections of events
 *
 * @package CalendarSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2002-2010
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 */

/**
 * The parent class for displaying collections of events
 *
 * @package CalendarSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2002-2010
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 */
abstract class CalendarSolution_List extends CalendarSolution {
    /**
     * The frequent event id to show events for
     * @var int
     */
    protected $frequent_event_id;

    /**
     * The start of the date range to show events for in the current request
     * @var DateTime
     */
    protected $from;

    /**
     * The DateInterval specification for how many months to show at once
     * @var string
     */
    protected $interval_spec;

    /**
     * The number of items to show
     * @var int
     */
    protected $limit;

    /**
     * The start of the date range to show events for if the user navigates
     * to later events
     * @var DateTime
     */
    protected $next_from;

    /**
     * The end of the date range to show events for if the user navigates
     * to later events
     * @var DateTime
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
     * @var DateTime
     */
    protected $prior_from;

    /**
     * The end of the date range to show events for if the user navigates
     * to earlier events
     * @var DateTime
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
     * @var DateTime
     */
    protected $to;


    /**
     * Calls the parent constructor then populates the $interval_spec property
     *
     * @param string $dbms  "mysql", "mysqli", "pgsql", "sqlite", "sqlite3"
     * @param integer $months  how many months should be shown at once
     *
     * @uses CalendarSolution::__construct()  to instantiate the database class
     * @uses CalendarSolution_List::$interval_spec  is set based on the
     *       $months parameter; which is used later when creating new
     *       DateInterval objects
     */
    public function __construct($dbms, $months = 3) {
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
     * @param string $dbms  "mysql", "mysqli", "pgsql", "sqlite", "sqlite3"
     * @param integer $months  how many months should be shown at once
     *
     * @return CalendarSolution_List_Calendar|CalendarSolution_List_List
     */
    public static function factory_chosen_view($dbms, $months = 3) {
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
        return new $class($dbms, $months);
    }

    /**
     * Provides the Cascading Style Sheet data, for use between <style> tags
     *
     * @return string  the CSS
     *
     * @uses CalendarSolution_List::get_css_name()  to know where the CSS is
     * @uses CalendarSolution_List_QuickTable::get_css_name()  to know where the CSS is
     * @uses CalendarSolution_List_Title::get_css_name()  to know where the CSS is
     */
    public function get_css() {
        return file_get_contents($this->get_css_name());
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
     * Produces the HTML for the form people can use to pick date ranges
     * and particular events
     *
     * @return string  the HTML for the limit form
     *
     * @uses CalendarSolution_List::$view
     * @uses CalendarSolution_List::$frequent_event_id
     * @uses CalendarSolution_List::$from
     * @uses CalendarSolution_List::$to
     */
    protected function get_limit_form() {
        $out = '<form class="cs_limit" method="get">';

        $out .= '<table>'
             . "\n" . ' <tr>' . "\n"
             . "  <td>\n\n";

        $out .= "\n\n" . '<p class="instructions"><small>'
             . "\n You can limit the list of events by dates\n"
             . " or the event type or a combination thereof."
             . "<br />To view all events, click on the\n"
             . " &quot;Remove All Limits&quot; button.\n"
             . "</small></p>\n\n";

        $out .= '<p class="date"><small><u>L</u>imit'
             . ' to dates between';

        $out .= ' <input type="text" size="11" maxlength="10"'
             . ' name="from" accesskey="l" value="'
             . $this->from->format('Y-m-d') . '" />' . "\n";

        $out .= ' and <input type="text" size="11" maxlength="10" '
             . 'name="to" value="'
             . $this->to->format('Y-m-d') . '" /> (inclusive).</small></p>' . "\n";

        $out .= '<p class="bottom"><small>';

        $out .= '<label for="frequent_event_id" accesskey="r">'
             . 'F<u>r</u>equent Events:</label>' . "\n";

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

        ob_start();
        $this->sql->OptionListGenerator(__FILE__, __LINE__, $opt);
        $out .= ob_get_contents();
        ob_end_clean();

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
                    $uri = '';
            }
        }

        if ($uri) {
            return '<a href="' . $uri . '">' . $event['title'] . '</a>';
        } else {
            return $event['title'];
        }
    }

    /**
     * Produces the HTML for navigating to earlier and later events
     * as well as changing between list and calendar views
     *
     * @return string  the navigation section's HTML
     *
     * @uses CalendarSolution_List::$view
     * @uses CalendarSolution_List::$frequent_event_id
     * @uses CalendarSolution_List::$from
     * @uses CalendarSolution_List::$to
     * @uses CalendarSolution_List::$prior_from
     * @uses CalendarSolution_List::$prior_to
     * @uses CalendarSolution_List::$next_from
     * @uses CalendarSolution_List::$next_to
     */
    protected function get_navigation() {
        $out = '<table class="cs_nav" width="100%">' . "\n"
             . " <tr>\n"
             . '  <td>' . "\n"
             . '   <a href="calendar.php?from=' . $this->prior_from->format('Y-m-d')
             . '&amp;to=' . $this->prior_to->format('Y-m-d')
             . '&amp;frequent_event_id=' . $this->frequent_event_id
             . '&amp;view=' . $this->view . '">&lt; See Earlier Events</a>'
             . "  </td>\n";

        $out .= '  <td align="right">' . "\n"
             . '<a href="calendar.php?from=' . $this->next_from->format('Y-m-d')
             . '&amp;to=' . $this->next_to->format('Y-m-d')
             . '&amp;frequent_event_id=' . $this->frequent_event_id
             . '&amp;view=' . $this->view . '">See Later Events &gt;</a>' . "\n"
             . "  </td>\n"
             . " </tr>\n";

        $out .= " <tr>\n"
             . '  <td colspan="2" align="center">' . "\n"
             . 'View the events in  <a href="calendar.php?from='
             . $this->from->format('Y-m-d')
             . '&amp;to=' . $this->to->format('Y-m-d')
             . '&amp;frequent_event_id=' . $this->frequent_event_id
             . '&amp;view='
             . (($this->view == 'Calendar') ? 'List">List' : 'Calendar">Calendar')
             . '</a> format.' . "\n";

        $out .= "  </td>\n"
             . " </tr>\n"
             . "</table>\n";

        return $out;
    }

    /**
     * Creates a DateInterval object indicating how many months should be
     * displayed at one time
     *
     * @return DateInterval
     *
     * @uses CalendarSolution_List::$interval_spec  to know how long the
     *       interval should be
     */
    protected function interval_singleton() {
        static $out;
        if (!isset($out)) {
            $out = new DateInterval($this->interval_spec);
        }
        return $out;
    }

    /**
     * Assembles the query string then executes it
     *
     * @return void
     *
     * @uses CalendarSolution_List::$from  to know the start of the date range
     * @uses CalendarSolution_List::$to  to know the end of the date range
     * @uses CalendarSolution_List::$frequent_event_id  to limit entries to a
     *       particular event if so desired
     * @uses CalendarSolution_List::$page_id  to limit entries to those that
     *       are set to be featured on the given page id
     *
     * @throws CalendarSolution_Exception if to is later than from
     */
    protected function run_query() {
        /*
         * Establish WHERE elements.
         */

        $where = array();

        if (!empty($this->from) && !empty($this->to)) {
            if ($this->from > $this->to) {
                throw new CalendarSolution_Exception("'from' can not be after 'to'");
            }

            $where[] = "date_start BETWEEN '"
                . $this->from->format('Y-m-d')
                . "' AND '" . $this->to->format('Y-m-d') . "'";
        } elseif (!empty($this->from)) {
            $where[] = "date_start >= '"
                . $this->from->format('Y-m-d') . "'";
        } elseif (!empty($this->to)) {
            $where[] = "date_start <= '"
                . $this->to->format('Y-m-d') . "'";
        }

        if (!empty($this->frequent_event_id)) {
            $where[] = "cs_calendar.frequent_event_id = "
                . $this->sql->Escape(__FILE__, __LINE__, $this->frequent_event_id);
        }

        if (!empty($this->page_id)) {
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


        /*
         * Construct the SQL string.
         */

        $this->sql->SQLQueryString = "SELECT
            calendar_id,
            calendar_uri,
            changed,
            date_start,
            cs_calendar.frequent_event_id AS frequent_event_id,
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

        if ($where) {
            $this->sql->SQLQueryString .= "\n WHERE "
                . implode(' AND ', $where);
        }

        $this->sql->SQLQueryString .= "
            ORDER BY date_start, time_start, title,
                cs_calendar.frequent_event_id";

        if (!empty($this->limit)) {
            $this->sql->SQLQueryString .= "
                LIMIT " . (int) $this->limit;
        }

        $this->sql->RunQuery(__FILE__, __LINE__);
    }

    /**
     * Sets the "page_id" property to the appropriate value
     *
     * @param int $in  the page id to get the list for
     *
     * @return void
     *
     * @uses CalendarSolution_List::$page_id  to store the data
     */
    public function set_page_id($in) {
        $this->page_id = (int) $in;
    }

    /**
     * Sets the "frequent_event_id" property to the appropriate value
     *
     * @param mixed $in  + NULL = use value of $_REQUEST['frequent_event_id']
     *                   though use FALSE if it is not set or invalid
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
        if ($in === false || !preg_match('/^\d{1,10}$/', $in)) {
            $this->frequent_event_id = false;
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
            if ($in === false) {
                $this->from = false;
                return;
            }
        } elseif ($in === false) {
            $this->from = false;
            return;
        }

        try {
            $this->from = new CalendarSolution_DateTime($in);
        } catch (Exception $e) {
            $this->from = new CalendarSolution_DateTime;
        }
    }

    /**
     * Sets the "limit" property
     *
     * @param mixed $in  the number of rows to show
     *
     * @return void
     *
     * @uses CalendarSolution_List::$limit  to store the data
     */
    public function set_limit($in) {
        $this->limit = (int) $in;
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
        } elseif ($this->from === null) {
            throw new CalendarSolution_Exception('set_from() and set_to()'
                . ' must be called before set_prior_and_next_dates()');
        }

        $one_day_interval = new DateInterval('P1D');

        $this->prior_to = new CalendarSolution_DateTime($this->from->format('Y-m-d'));
        $this->prior_to->sub($one_day_interval);

        $this->next_from = new CalendarSolution_DateTime($this->to->format('Y-m-d'));
        $this->next_from->add($one_day_interval);

        $this->prior_from = new CalendarSolution_DateTime($this->prior_to->format('Y-m-d'));
        $this->prior_from->sub($this->interval_singleton());
        $this->prior_from->modify('first day of this month');

        $this->next_to = new CalendarSolution_DateTime($this->next_from->format('Y-m-d'));
        $this->next_to->add($this->interval_singleton());
        $this->next_to->modify('last day of this month');
    }

    /**
     * Should cancelled events be shown or not?
     *
     * @param bool $in  set it to FALSE to leave out cancelled events
     *
     * @return void
     *
     * @uses CalendarSolution_List_List::$show_cancelled  to store the decision
     */
    public function set_show_cancelled($in) {
        $this->show_cancelled = $in;
    }

    /**
     * Turns the Summary field on or off when showing the "List" format
     *
     * @param bool $in  set it to FALSE to turn it off, is on by default
     *
     * @return void
     *
     * @uses CalendarSolution_List_List::$show_summary  to store the decision
     */
    public function set_show_summary($in) {
        $this->show_summary = $in;
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
            if ($in === false) {
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
            $this->to = new CalendarSolution_DateTime($in);
        } catch (Exception $e) {
            $this->to = new CalendarSolution_DateTime;
        }

        if ($add_months) {
            $this->to->add($this->interval_singleton());
            $this->to->modify('last day of this month');
        }
    }
}
