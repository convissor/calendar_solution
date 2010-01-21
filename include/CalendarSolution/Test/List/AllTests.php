<?php /** @package CalendarSolution_Test */

/**
 * Gather the PHPUnit Framework
 */
require_once 'PHPUnit/Framework.php';

/**#@+
 * Gather the test files in this directory
 */
require_once $GLOBALS['IncludeDir'] . '/CalendarSolution/Test/List/CalendarTest.php';
/**#@-*/


/**
 * Runs all Calendar Solution Detial sub-class tests
 *
 * Usage:  phpunit List_AllTests
 *
 * @package CalendarSolution_Test
 */
class List_AllTests {
    public static function suite() {
        $suite = new PHPUnit_Framework_TestSuite('Calendar Solution List sub-class Unit Tests');

        $suite->addTestSuite('List_CalendarTest');

        return $suite;
    }
}
