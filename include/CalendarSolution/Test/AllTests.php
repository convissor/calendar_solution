<?php /** @package CalendarSolution_Test */

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
