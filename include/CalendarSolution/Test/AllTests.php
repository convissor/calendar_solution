<?php /** @package CalendarSolution_Test */

/**
 * Runs all Calendar Solution tests
 *
 * Usage:  phpunit AllTests
 *
 * @package CalendarSolution_Test
 */
class CalendarSolution_Test_AllTests {
    public static function suite() {
        $suite = new PHPUnit_Framework_TestSuite('Calendar Solution Unit Tests');

        $suite->addTestSuite('CalendarSolution_Test_DateTimeTest');

        $suite->addTest(CalendarSolution_Test_Detail_AllTests::suite());
        $suite->addTest(CalendarSolution_Test_List_AllTests::suite());

        return $suite;
    }
}
