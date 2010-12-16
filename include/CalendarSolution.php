<?php

/**
 * Calendar Solution's base class
 *
 * Calendar Solution is a trademark of The Analysis and Solutions Company.
 *
 * @package CalendarSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2002-2010
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 */

if (class_exists('DateInterval')) {
    // PHP 5.3

    if (version_compare(phpversion(), '5.3.3', '>=')) {
        /**
         * Version of PHP is cool, so just stub out the real DateTime class
         * @ignore
         * @package CalendarSolution
         */
        class CalendarSolution_DateTime extends DateTime {}
    } else {
        /**
         * Use our own date class so we can provide forward compatibility
		 *
         * Bug 49081 afflicts PHP's DateTime::diff() method
         * @ignore
         * @package CalendarSolution
         */
        class CalendarSolution_DateTime extends CalendarSolution_DateTimeDiff {}
    }
} else {
    // PHP 5.2

    /**
     * PHP 5.2 needs all of our date mojo
     * @package CalendarSolution
     */
    class CalendarSolution_DateTime extends CalendarSolution_DateTime52 {}
}


/**
 * The base class
 *
 * @package CalendarSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2002-2010
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
     * @var object SQLSolution_General
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
     * @uses CalendarSolution_View::$sql  the SQL Solution object for the
     *       database system specified by the $dbms parameter
     *
     * @throws CalendarSolution_Exception if the $dbms parameter is improper
     */
    public function __construct($dbms = CALENDAR_SOLUTION_DBMS) {
        $extension = '';
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
                $extension = 'sqlite2';
                break;
            case 'sqlite3':
                $class = 'SQLSolution_SQLite3User';
                $extension = 'sqlite3';
                break;
            case 'test':
                return;
            default:
                throw new CalendarSolution_Exception('Improper dbms');
        }

        $this->sql = new $class('Y', 'Y');

        if ($extension && $this->sql->SQLDbName == 'default') {
            $this->sql->SQLDbName = dirname(__FILE__)
                . '/CalendarSolution/sqlite/calendar_solution.' . $extension;
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
     * @return string  the HTML with the credit link
     */
    protected function get_credit() {
        return '<p class="cs_credit">Calendar produced using <a href="'
            . 'http://www.analysisandsolutions.com/software/calendar/'
            . '">Calendar Solution</a></p>' . "\n";
    }

    /**
     * Looks for a date value in $_REQUEST[$name]
     *
     * @param string $name  the $_REQUEST array's key to examine
     *
     * @return mixed  the date in YYYY-MM-DD format, NULL if the REQUEST
     *                element is not set or FALSE if the date is invalid
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
     * Looks for an integer value in $_REQUEST[$name]
     *
     * @param string $name  the $_REQUEST array's key to examine
     *
     * @return mixed  the integer, NULL if the REQUEST element
     *                is not set or FALSE if it is invalid
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
     * Is the current view from the admin section or not?
     * @return bool
     */
    public function is_admin() {
        static $answer;
        if (!isset($answer)) {
            if (strstr($_SERVER['REQUEST_URI'], '/Admin/')) {
                $answer = true;
            } else {
                $answer = false;
            }
        }
        return $answer;
    }
}
