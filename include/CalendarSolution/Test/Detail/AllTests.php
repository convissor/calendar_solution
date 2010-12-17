<?php /** @package CalendarSolution_Test */

/**
 * Runs all Calendar Solution Detial sub-class tests
 *
 * Usage:  phpunit Detail_AllTests
 *
 * @package CalendarSolution_Test
 */
class CalendarSolution_Test_Detail_AllTests {
	public static function suite() {
		$suite = new PHPUnit_Framework_TestSuite('Calendar Solution Detail sub-class Unit Tests');

		$suite->addTestSuite('CalendarSolution_Test_Detail_FormTest');

		return $suite;
	}
}
