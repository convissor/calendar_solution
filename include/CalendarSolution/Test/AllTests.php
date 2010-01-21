<?php /** @package CalendarSolution_Test */

/**
 * Gather the PHPUnit Framework
 */
require_once 'PHPUnit/Framework.php';

/**#@+
 * Gather child test suites
 */
require_once $GLOBALS['IncludeDir'] . '/CalendarSolution/Test/Detail/AllTests.php';
require_once $GLOBALS['IncludeDir'] . '/CalendarSolution/Test/List/AllTests.php';
/**#@-*/

/**#@+
 * Gather the test files in this directory
 */
require_once $GLOBALS['IncludeDir'] . '/CalendarSolution/Test/DateTimeTest.php';
/**#@-*/


/**
 * Runs all Calendar Solution tests
 *
 * Usage:  phpunit AllTests
 *
 * @package CalendarSolution_Test
 */
class AllTests {
    public static function suite() {
        $suite = new PHPUnit_Framework_TestSuite('Calendar Solution Unit Tests');

        $suite->addTestSuite('DateTimeTest');

        $suite->addTest(Detail_AllTests::suite());
        $suite->addTest(List_AllTests::suite());

        return $suite;
    }
}
