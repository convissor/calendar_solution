<?php /** @package CalendarSolution_Test */

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
