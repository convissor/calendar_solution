<?php /** @package CalendarSolution_Test */

/**
 * Tests the CalendarSolution_List_Calendar class' setter methods
 *
 * Usage:  phpunit List_CalendarSetterTest
 *
 * @package CalendarSolution_Test
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2002-2012
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 */
class CalendarSolution_Test_List_CalendarSetterTest extends PHPUnit_Framework_TestCase {
	/**
	 * The calendar class to test
	 * @var CalendarSolution_Test_List_CalendarHelper
	 */
	protected $calendar;

	/**
	 * Prepares the environment before running each test
	 */
	protected function setUp() {
		$this->calendar = new CalendarSolution_Test_List_CalendarHelper;
	}


	/**#@+
	 * set_from()
	 */
	public function test_from_request_good() {
		$_REQUEST = array('from' => '2011-12-13');
		$this->calendar->set_from();
		$this->assertEquals('2011-12-01', $this->calendar->from->format('Y-m-d'));
	}
	public function test_from_input_good() {
		$_REQUEST = array('from' => '2011-12-13');
		$this->calendar->set_from('2009-10-11');
		$this->assertEquals('2009-10-01', $this->calendar->from->format('Y-m-d'));
	}
	public function test_from_request_bad_array() {
		$_REQUEST = array('from' => array('some string'));
		$this->calendar->set_from();
		$this->assertEquals(date('Y-m-01'), $this->calendar->from->format('Y-m-d'));
	}
	public function test_from_input_bad_array() {
		$_REQUEST = array('from' => array('some string'));
		$this->calendar->set_from(array('some string'));
		$this->assertEquals(date('Y-m-01'), $this->calendar->from->format('Y-m-d'));
	}
	public function test_from_request_bad() {
		$_REQUEST = array('from' => 'some string');
		$this->calendar->set_from();
		$this->assertEquals(date('Y-m-01'), $this->calendar->from->format('Y-m-d'));
	}
	public function test_from_input_bad() {
		$_REQUEST = array('from' => 'some string');
		$this->calendar->set_from('some string');
		$this->assertEquals(date('Y-m-01'), $this->calendar->from->format('Y-m-d'));
	}
	public function test_from_true_good() {
		$_REQUEST = array('from' => '2011-12-13');
		$this->calendar->set_from(true);
		$this->assertEquals('2011-12-01', $this->calendar->from->format('Y-m-d'));
	}
	public function test_from_true_bad_array() {
		$_REQUEST = array('from' => array('some string'));
		$this->calendar->set_from(true);
		$this->assertEquals(date('Y-m-01'), $this->calendar->from->format('Y-m-d'));
	}
	public function test_from_true_bad() {
		$_REQUEST = array('from' => 'some string');
		$this->calendar->set_from(true);
		$this->assertEquals(date('Y-m-01'), $this->calendar->from->format('Y-m-d'));
	}
	public function test_from_true_unset() {
		$_REQUEST = array();
		$this->calendar->set_from(true);
		$this->assertEquals(false, $this->calendar->from);
	}
	/**#@-*/

	/**#@+
	 * set_prior_and_next_dates()
	 */
	public function test_prior_and_next_dates() {
		// Prevent test from failing in the future.
		$this->calendar->set_permit_history_months(false);

		$this->calendar->set_from('2011-02-01');
		$this->calendar->set_to('2011-04-30');
		$this->calendar->set_prior_and_next_dates();
		$this->assertEquals('2010-11-01', $this->calendar->prior_from->format('Y-m-d'), 'prior_from');
		$this->assertEquals('2011-01-31', $this->calendar->prior_to->format('Y-m-d'), 'prior_to');
		$this->assertEquals('2011-05-01', $this->calendar->next_from->format('Y-m-d'), 'next_from');
		$this->assertEquals('2011-07-31', $this->calendar->next_to->format('Y-m-d'), 'next_to');
	}
	public function test_prior_and_next_dates_from_false() {
		$this->calendar->set_from(false);
		$this->calendar->set_prior_and_next_dates();
		$this->assertEquals(null, $this->calendar->prior_from, 'prior_from');
		$this->assertEquals(null, $this->calendar->prior_to, 'prior_to');
		$this->assertEquals(null, $this->calendar->next_from, 'next_from');
		$this->assertEquals(null, $this->calendar->next_to, 'next_to');
	}
	public function test_prior_and_next_dates_to_false() {
		$this->calendar->set_to(false);
		$this->calendar->set_prior_and_next_dates();
		$this->assertEquals(null, $this->calendar->prior_from, 'prior_from');
		$this->assertEquals(null, $this->calendar->prior_to, 'prior_to');
		$this->assertEquals(null, $this->calendar->next_from, 'next_from');
		$this->assertEquals(null, $this->calendar->next_to, 'next_to');
	}
	public function test_prior_and_next_dates_from_unset() {
		$this->calendar->set_to('2011-02-28');
		$this->setExpectedException('CalendarSolution_Exception');
		$this->calendar->set_prior_and_next_dates();
	}
	public function test_prior_and_next_dates_to_unset() {
		$this->calendar->set_from('2011-02-01');
		$this->setExpectedException('CalendarSolution_Exception');
		$this->calendar->set_prior_and_next_dates();
	}
	/**#@-*/
}
