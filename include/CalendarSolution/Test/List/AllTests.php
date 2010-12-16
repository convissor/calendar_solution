<?php /** @package CalendarSolution_Test */

/**
 * Runs all Calendar Solution Detial sub-class tests
 *
 * Usage:  phpunit List_AllTests
 *
 * @package CalendarSolution_Test
 */
class CalendarSolution_Test_List_AllTests {
    public static function suite() {
        $suite = new PHPUnit_Framework_TestSuite('Calendar Solution List sub-class Unit Tests');

        $suite->addTestSuite('CalendarSolution_Test_List_CalendarTest');

        return $suite;
    }
}
