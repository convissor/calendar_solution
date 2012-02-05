<?php /** @package CalendarSolution_Test */

/**
 * Tests the CalendarSolution class' convert_windows_characters method
 *
 * Usage:  phpunit WindowsCharacterTest
 *
 * @package CalendarSolution_Test
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2002-2011
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 */
class CalendarSolution_Test_WindowsCharacterTest extends PHPUnit_Framework_TestCase {
	/**
	 * The calendar class to test
	 * @var CalendarSolution_Test_Helper
	 */
	protected $calendar;

	/**
	 * Prepares the environment before running each test
	 */
	protected function setUp() {
		$this->calendar = new CalendarSolution_Test_Helper;
	}


	/**#@+
	 * convert_windows_characters()
	 */
	public function test_hard_tab() {
		$actual = $this->calendar->convert_windows_characters("\x09");
		$this->assertEquals(' ', $actual);
	}
	public function test_elipsis() {
		$actual = $this->calendar->convert_windows_characters("\x85");
		$this->assertEquals('...', $actual);
	}
	public function test_single_quote_left() {
		$actual = $this->calendar->convert_windows_characters("\x91");
		$this->assertEquals("'", $actual);
	}
	public function test_single_quote_right() {
		$actual = $this->calendar->convert_windows_characters("\x92");
		$this->assertEquals("'", $actual);
	}
	public function test_double_quote_left() {
		$actual = $this->calendar->convert_windows_characters("\x93");
		$this->assertEquals('"', $actual);
	}
	public function test_double_quote_right() {
		$actual = $this->calendar->convert_windows_characters("\x94");
		$this->assertEquals('"', $actual);
	}
	public function test_bullet() {
		$actual = $this->calendar->convert_windows_characters("\x95");
		$this->assertEquals('*', $actual);
	}
	public function test_dash() {
		$actual = $this->calendar->convert_windows_characters("\x96");
		$this->assertEquals('-', $actual);
	}
	public function test_emdash() {
		$actual = $this->calendar->convert_windows_characters("\x97");
		$this->assertEquals('--', $actual);
	}
	public function test_quarter() {
		$actual = $this->calendar->convert_windows_characters("\xBC");
		$this->assertEquals('1/4', $actual);
	}
	public function test_half() {
		$actual = $this->calendar->convert_windows_characters("\xBD");
		$this->assertEquals('1/2', $actual);
	}
	public function test_three_quarter() {
		$actual = $this->calendar->convert_windows_characters("\xBE");
		$this->assertEquals('3/4', $actual);
	}
	/**#@-*/
}
