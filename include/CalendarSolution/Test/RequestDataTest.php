<?php /** @package CalendarSolution_Test */

/**
 * Extend the class to be tested so we can have access to protected elements
 * @package CalendarSolution_Test
 */
class CalendarSolution_Test_RequestData extends CalendarSolution {
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
class CalendarSolution_Test_RequestDataTest extends PHPUnit_Framework_TestCase {
	/**
	 * The calendar class to test
	 * @var CalendarSolution
	 */
	protected $calendar;


	/**
	 * Prepares the environment before running a test
	 */
	protected function setUp() {
		$this->calendar = new CalendarSolution_Test_RequestData;
	}

	protected function setGetGood() {
		$_REQUEST = array(
			'category_id' => array(2, 3),
			'frequent_event_id' => 2,
			'from' => '2011-12-13',
		);
	}


	/**#@+
	 * get_date_from_request()
	 */
	public function test_date_good() {
		$input = '2011-12-13';
		$_REQUEST = array('from' => $input);
		$actual = $this->calendar->get_date_from_request('from');
		$this->assertEquals($input, $actual);
	}
	public function test_date_bad_semi_date() {
		$input = '11-12-13';
		$_REQUEST = array('from' => $input);
		$actual = $this->calendar->get_date_from_request('from');
		$this->assertEquals(false, $actual);
	}
	public function test_date_bad_array() {
		$input = array('2011-12-13');
		$_REQUEST = array('from' => $input);
		$actual = $this->calendar->get_date_from_request('from');
		$this->assertEquals(false, $actual);
	}
	public function test_date_nothing() {
		$_REQUEST = array();
		$actual = $this->calendar->get_date_from_request('from');
		$this->assertEquals(null, $actual);
	}
	public function test_date_remove_limit() {
		$input = '2011-12-13';
		$_REQUEST = array('from' => $input);
		$_GET = array('remove_limit' => 'Remove All Limits');
		$actual = $this->calendar->get_date_from_request('from');
		$this->assertEquals(null, $actual);
	}
	/**#@-*/

	/**#@+
	 * get_int_from_request()
	 */
	public function test_int_good() {
		$input = 3;
		$_REQUEST = array('frequent_event_id' => $input);
		$actual = $this->calendar->get_int_from_request('frequent_event_id');
		$this->assertEquals($input, $actual);
	}
	public function test_int_bad_string() {
		$input = 'some string';
		$_REQUEST = array('frequent_event_id' => $input);
		$actual = $this->calendar->get_int_from_request('frequent_event_id');
		$this->assertEquals(false, $actual);
	}
	public function test_int_bad_array() {
		$input = array(3);
		$_REQUEST = array('frequent_event_id' => $input);
		$actual = $this->calendar->get_int_from_request('frequent_event_id');
		$this->assertEquals(false, $actual);
	}
	public function test_int_nothing() {
		$_REQUEST = array();
		$actual = $this->calendar->get_int_from_request('frequent_event_id');
		$this->assertEquals(null, $actual);
	}
	public function test_int_remove_limit() {
		$input = 3;
		$_REQUEST = array('frequent_event_id' => $input);
		$_GET = array('remove_limit' => 'Remove All Limits');
		$actual = $this->calendar->get_int_from_request('frequent_event_id');
		$this->assertEquals(null, $actual);
	}
	/**#@-*/

	/**#@+
	 * get_int_array_from_request()
	 */
	public function test_int_array_array_good() {
		$input = array(2, 3);
		$_REQUEST = array('category_id' => $input);
		$actual = $this->calendar->get_int_array_from_request('category_id');
		$this->assertEquals($input, $actual);
	}
	public function test_int_array_int_good() {
		$input = 3;
		$_REQUEST = array('category_id' => $input);
		$actual = $this->calendar->get_int_array_from_request('category_id');
		$this->assertEquals(array(3), $actual);
	}
	public function test_int_array_bad_string() {
		$input = 'some string';
		$_REQUEST = array('category_id' => $input);
		$actual = $this->calendar->get_int_array_from_request('category_id');
		$this->assertEquals(false, $actual);
	}
	public function test_int_array_bad_array() {
		$input = array('foobar');
		$_REQUEST = array('category_id' => $input);
		$actual = $this->calendar->get_int_array_from_request('category_id');
		$this->assertEquals(false, $actual);
	}
	public function test_int_array_nothing() {
		$_REQUEST = array();
		$actual = $this->calendar->get_int_array_from_request('category_id');
		$this->assertEquals(null, $actual);
	}
	public function test_int_array_remove_limit() {
		$input = 3;
		$_REQUEST = array('category_id' => $input);
		$_GET = array('remove_limit' => 'Remove All Limits');
		$actual = $this->calendar->get_int_array_from_request('category_id');
		$this->assertEquals(null, $actual);
	}
	/**#@-*/
}
