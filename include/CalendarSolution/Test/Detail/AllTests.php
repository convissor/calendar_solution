<?php /** @package CalendarSolution_Test */

/**
 * Gather the PHPUnit Framework
 */
require_once 'PHPUnit/Framework.php';

/**#@+
 * Gather the test files in this directory
 */
require_once $GLOBALS['IncludeDir'] . '/CalendarSolution/Test/Detail/FormTest.php';
/**#@-*/


/**
 * Runs all Calendar Solution Detial sub-class tests
 *
 * Usage:  phpunit Detail_AllTests
 *
 * @package CalendarSolution_Test
 */
class Detail_AllTests {
    public static function suite() {
        $suite = new PHPUnit_Framework_TestSuite('Calendar Solution Detail sub-class Unit Tests');

        $suite->addTestSuite('Detail_FormTest');

        return $suite;
    }
}
