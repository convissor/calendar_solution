<?php /** @package CalendarSolution_Test */

/**
 * Runs all Calendar Solution tests
 *
 * Usage:  phpunit AllTests
 *
 * @package CalendarSolution_Test
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2002-2011
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 */
class CalendarSolution_Test_AllTests {
	public static function suite() {
		$suite = new PHPUnit_Framework_TestSuite('Calendar Solution Unit Tests');

		$suite->addTestSuite('CalendarSolution_Test_RequestDataTest');

		$suite->addTestSuite('CalendarSolution_Test_Detail_AllTests');
		$suite->addTestSuite('CalendarSolution_Test_List_AllTests');

		return $suite;
	}
}
