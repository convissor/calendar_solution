<?php /** @package CalendarSolution_Test */

/**
 * Extend the class to be tested so we can have access to protected elements
 * @package CalendarSolution_Test
 */
class CalendarSolution_Test_List_Calendar extends CalendarSolution_List_Calendar {
	public function __call($method, $args) {
		return call_user_func_array(array($this, $method), $args);
	}
	public function __get($property) {
		return $this->$property;
	}
	public function get_data_element($key) {
		return $this->data[$key];
	}
}


/**
 * Tests the CalendarSolution_List_Calendar class
 *
 * Usage:  phpunit List_CalendarTest
 *
 * @package CalendarSolution_Test
 */
class CalendarSolution_Test_List_CalendarTest extends PHPUnit_Framework_TestCase {
	/**
	 * The calendar class to test
	 * @var CalendarSolution_List_Calendar
	 */
	protected $calendar;


	/**
	 * Prepares the environment before running a test
	 */
	protected function setUp() {
		parent::setUp();
		$this->calendar = new CalendarSolution_Test_List_Calendar;
	}

	/**#@+
	 * calculate_months()
	 */
	public function test_calculate_months__jan_mar() {
		$current = new DateTimeSolution('2010-01-01');
		$to = new DateTimeSolution('2010-03-31');
		$months = $this->calendar->calculate_months($current, $to);
		$this->assertEquals(3, $months);
	}

	public function test_calculate_months__feb_apr() {
		$current = new DateTimeSolution('2010-02-01');
		$to = new DateTimeSolution('2010-04-30');
		$months = $this->calendar->calculate_months($current, $to);
		$this->assertEquals(3, $months);
	}

	public function test_calculate_months__mar_may() {
		$current = new DateTimeSolution('2010-03-01');
		$to = new DateTimeSolution('2010-05-31');
		$months = $this->calendar->calculate_months($current, $to);
		$this->assertEquals(3, $months);
	}

	public function test_calculate_months__apr_jun() {
		$current = new DateTimeSolution('2010-04-01');
		$to = new DateTimeSolution('2010-06-30');
		$months = $this->calendar->calculate_months($current, $to);
		$this->assertEquals(3, $months);
	}

	public function test_calculate_months__may_jul() {
		$current = new DateTimeSolution('2010-05-01');
		$to = new DateTimeSolution('2010-07-31');
		$months = $this->calendar->calculate_months($current, $to);
		$this->assertEquals(3, $months);
	}

	public function test_calculate_months__jun_aug() {
		$current = new DateTimeSolution('2010-06-01');
		$to = new DateTimeSolution('2010-08-31');
		$months = $this->calendar->calculate_months($current, $to);
		$this->assertEquals(3, $months);
	}

	public function test_calculate_months__jul_sep() {
		$current = new DateTimeSolution('2010-07-01');
		$to = new DateTimeSolution('2010-09-30');
		$months = $this->calendar->calculate_months($current, $to);
		$this->assertEquals(3, $months);
	}

	public function test_calculate_months__aug_oct() {
		$current = new DateTimeSolution('2010-08-01');
		$to = new DateTimeSolution('2010-10-31');
		$months = $this->calendar->calculate_months($current, $to);
		$this->assertEquals(3, $months);
	}

	public function test_calculate_months__sep_nov() {
		$current = new DateTimeSolution('2010-09-01');
		$to = new DateTimeSolution('2010-11-30');
		$months = $this->calendar->calculate_months($current, $to);
		$this->assertEquals(3, $months);
	}

	public function test_calculate_months__oct_dec() {
		$current = new DateTimeSolution('2010-10-01');
		$to = new DateTimeSolution('2010-12-31');
		$months = $this->calendar->calculate_months($current, $to);
		$this->assertEquals(3, $months);
	}

	public function test_calculate_months__nov_jan() {
		$current = new DateTimeSolution('2010-11-01');
		$to = new DateTimeSolution('2011-01-31');
		$months = $this->calendar->calculate_months($current, $to);
		$this->assertEquals(3, $months);
	}

	public function test_calculate_months__dec_feb() {
		$current = new DateTimeSolution('2010-12-01');
		$to = new DateTimeSolution('2011-02-28');
		$months = $this->calendar->calculate_months($current, $to);
		$this->assertEquals(3, $months);
	}


	public function test_calculate_months__dec_mar() {
		$current = new DateTimeSolution('2010-12-01');
		$to = new DateTimeSolution('2011-03-31');
		$months = $this->calendar->calculate_months($current, $to);
		$this->assertEquals(4, $months);
	}

	public function test_calculate_months__apr_jul() {
		$current = new DateTimeSolution('2010-04-01');
		$to = new DateTimeSolution('2010-07-31');
		$months = $this->calendar->calculate_months($current, $to);
		$this->assertEquals(4, $months);
	}

	public function test_calculate_months__jan_apr() {
		$current = new DateTimeSolution('2010-01-01');
		$to = new DateTimeSolution('2010-04-30');
		$months = $this->calendar->calculate_months($current, $to);
		$this->assertEquals(4, $months);
	}

	public function test_calculate_months__jan_may() {
		$current = new DateTimeSolution('2010-01-01');
		$to = new DateTimeSolution('2010-05-31');
		$months = $this->calendar->calculate_months($current, $to);
		$this->assertEquals(5, $months);
	}


	public function test_calculate_months__jan_nextjan() {
		$current = new DateTimeSolution('2010-01-01');
		$to = new DateTimeSolution('2011-01-31');
		$months = $this->calendar->calculate_months($current, $to);
		$this->assertEquals(13, $months);
	}

	public function test_calculate_months__feb_nextfeb() {
		$current = new DateTimeSolution('2010-02-01');
		$to = new DateTimeSolution('2011-02-28');
		$months = $this->calendar->calculate_months($current, $to);
		$this->assertEquals(13, $months);
	}


	public function test_calculate_months__jan_jan() {
		$current = new DateTimeSolution('2010-01-01');
		$to = new DateTimeSolution('2010-01-31');
		$months = $this->calendar->calculate_months($current, $to);
		$this->assertEquals(1, $months);
	}

	public function test_calculate_months__feb_feb() {
		$current = new DateTimeSolution('2010-02-01');
		$to = new DateTimeSolution('2010-02-28');
		$months = $this->calendar->calculate_months($current, $to);
		$this->assertEquals(1, $months);
	}
	/**#@-*/
}
