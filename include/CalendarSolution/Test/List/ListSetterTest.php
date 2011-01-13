<?php /** @package CalendarSolution_Test */

/**
 * Tests the CalendarSolution_List class' setter methods
 *
 * Usage:  phpunit List_ListSetterTest
 *
 * @package CalendarSolution_Test
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2002-2011
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 */
class CalendarSolution_Test_List_ListSetterTest extends PHPUnit_Framework_TestCase {
	/**
	 * The calendar class to test
	 * @var CalendarSolution_Test_List_ListHelper
	 */
	protected $calendar;

	/**
	 * The expected default value for the "to" property
	 * @var string
	 */
	protected $to_default;

	/**
	 * Prepares the environment before running each test
	 */
	protected function setUp() {
		$this->calendar = new CalendarSolution_Test_List_ListHelper;
		$to = new DateTimeSolution;
		$to->add(new DateIntervalSolution('P2M'));
		$this->to_default = $to->format('Y-m-t');
	}


	/**#@+
	 * set_category_id()
	 */
	public function test_category_id_request_good_array() {
		$_REQUEST = array('category_id' => array(2, 3));
		$this->calendar->set_category_id();
		$this->assertEquals(array(2, 3), $this->calendar->category_id);
	}
	public function test_category_id_input_good_array() {
		$_REQUEST = array('category_id' => array(2, 3));
		$this->calendar->set_category_id(array(4, 5));
		$this->assertEquals(array(4, 5), $this->calendar->category_id);
	}
	public function test_category_id_request_good_int() {
		$_REQUEST = array('category_id' => 2);
		$this->calendar->set_category_id();
		$this->assertEquals(array(2), $this->calendar->category_id);
	}
	public function test_category_id_input_good_int() {
		$_REQUEST = array('category_id' => 2);
		$this->calendar->set_category_id(4);
		$this->assertEquals(array(4), $this->calendar->category_id);
	}
	public function test_category_id_request_bad_array() {
		$_REQUEST = array('category_id' => array('some string'));
		$this->calendar->set_category_id();
		$this->assertEquals(false, $this->calendar->category_id);
	}
	public function test_category_id_input_bad_array() {
		$_REQUEST = array('category_id' => array('some string'));
		$this->calendar->set_category_id(array('some string'));
		$this->assertEquals(false, $this->calendar->category_id);
	}
	public function test_category_id_request_bad_int() {
		$_REQUEST = array('category_id' => 'some string');
		$this->calendar->set_category_id();
		$this->assertEquals(false, $this->calendar->category_id);
	}
	public function test_category_id_input_bad_int() {
		$_REQUEST = array('category_id' => 'some string');
		$this->calendar->set_category_id('some string');
		$this->assertEquals(false, $this->calendar->category_id);
	}
	/**#@-*/

	/**#@+
	 * set_frequent_event_id()
	 */
	public function test_frequent_event_id_request_good() {
		$_REQUEST = array('frequent_event_id' => 2);
		$this->calendar->set_frequent_event_id();
		$this->assertEquals(2, $this->calendar->frequent_event_id);
	}
	public function test_frequent_event_id_input_good() {
		$_REQUEST = array('frequent_event_id' => 2);
		$this->calendar->set_frequent_event_id(4);
		$this->assertEquals(4, $this->calendar->frequent_event_id);
	}
	public function test_frequent_event_id_request_bad_array() {
		$_REQUEST = array('frequent_event_id' => array('some string'));
		$this->calendar->set_frequent_event_id();
		$this->assertEquals(false, $this->calendar->frequent_event_id);
	}
	public function test_frequent_event_id_input_bad_array() {
		$_REQUEST = array('frequent_event_id' => array('some string'));
		$this->calendar->set_frequent_event_id(array('some string'));
		$this->assertEquals(false, $this->calendar->frequent_event_id);
	}
	public function test_frequent_event_id_request_bad() {
		$_REQUEST = array('frequent_event_id' => 'some string');
		$this->calendar->set_frequent_event_id();
		$this->assertEquals(false, $this->calendar->frequent_event_id);
	}
	public function test_frequent_event_id_input_bad() {
		$_REQUEST = array('frequent_event_id' => 'some string');
		$this->calendar->set_frequent_event_id('some string');
		$this->assertEquals(false, $this->calendar->frequent_event_id);
	}
	/**#@-*/

	/**#@+
	 * set_from()
	 */
	public function test_from_request_good() {
		$_REQUEST = array('from' => '2011-12-13');
		$this->calendar->set_from();
		$this->assertEquals('2011-12-13', $this->calendar->from->format('Y-m-d'));
	}
	public function test_from_input_good() {
		$_REQUEST = array('from' => '2011-12-13');
		$this->calendar->set_from('2009-10-11');
		$this->assertEquals('2009-10-11', $this->calendar->from->format('Y-m-d'));
	}
	public function test_from_request_bad_array() {
		$_REQUEST = array('from' => array('some string'));
		$this->calendar->set_from();
		$this->assertEquals(date('Y-m-d'), $this->calendar->from->format('Y-m-d'));
	}
	public function test_from_input_bad_array() {
		$_REQUEST = array('from' => array('some string'));
		$this->calendar->set_from(array('some string'));
		$this->assertEquals(date('Y-m-d'), $this->calendar->from->format('Y-m-d'));
	}
	public function test_from_request_bad() {
		$_REQUEST = array('from' => 'some string');
		$this->calendar->set_from();
		$this->assertEquals(date('Y-m-d'), $this->calendar->from->format('Y-m-d'));
	}
	public function test_from_input_bad() {
		$_REQUEST = array('from' => 'some string');
		$this->calendar->set_from('some string');
		$this->assertEquals(date('Y-m-d'), $this->calendar->from->format('Y-m-d'));
	}
	public function test_from_true_good() {
		$_REQUEST = array('from' => '2011-12-13');
		$this->calendar->set_from(true);
		$this->assertEquals('2011-12-13', $this->calendar->from->format('Y-m-d'));
	}
	public function test_from_true_bad_array() {
		$_REQUEST = array('from' => array('some string'));
		$this->calendar->set_from(true);
		$this->assertEquals(date('Y-m-d'), $this->calendar->from->format('Y-m-d'));
	}
	public function test_from_true_bad() {
		$_REQUEST = array('from' => 'some string');
		$this->calendar->set_from(true);
		$this->assertEquals(date('Y-m-d'), $this->calendar->from->format('Y-m-d'));
	}
	public function test_from_true_unset() {
		$_REQUEST = array();
		$this->calendar->set_from(true);
		$this->assertEquals(false, $this->calendar->from);
	}
	/**#@-*/

	/**#@+
	 * set_to()
	 */
	public function test_to_request_good() {
		$_REQUEST = array('to' => '2011-12-13');
		$this->calendar->set_to();
		$this->assertEquals('2011-12-13', $this->calendar->to->format('Y-m-d'));
	}
	public function test_to_input_good() {
		$_REQUEST = array('to' => '2011-12-13');
		$this->calendar->set_to('2009-10-11');
		$this->assertEquals('2009-10-11', $this->calendar->to->format('Y-m-d'));
	}
	public function test_to_request_bad_array() {
		$_REQUEST = array('to' => array('some string'));
		$this->calendar->set_to();
		$this->assertEquals($this->to_default, $this->calendar->to->format('Y-m-d'));
	}
	public function test_to_input_bad_array() {
		$_REQUEST = array('to' => array('some string'));
		$this->calendar->set_to(array('some string'));
		$this->assertEquals($this->to_default, $this->calendar->to->format('Y-m-d'));
	}
	public function test_to_request_bad() {
		$_REQUEST = array('to' => 'some string');
		$this->calendar->set_to();
		$this->assertEquals($this->to_default, $this->calendar->to->format('Y-m-d'));
	}
	public function test_to_input_bad() {
		$_REQUEST = array('to' => 'some string');
		$this->calendar->set_to('some string');
		$this->assertEquals($this->to_default, $this->calendar->to->format('Y-m-d'));
	}
	public function test_to_true_good() {
		$_REQUEST = array('to' => '2011-12-13');
		$this->calendar->set_to(true);
		$this->assertEquals('2011-12-13', $this->calendar->to->format('Y-m-d'));
	}
	public function test_to_true_bad_array() {
		$_REQUEST = array('to' => array('some string'));
		$this->calendar->set_to(true);
		$this->assertEquals($this->to_default, $this->calendar->to->format('Y-m-d'));
	}
	public function test_to_true_bad() {
		$_REQUEST = array('to' => 'some string');
		$this->calendar->set_to(true);
		$this->assertEquals($this->to_default, $this->calendar->to->format('Y-m-d'));
	}
	public function test_to_true_unset() {
		$_REQUEST = array();
		$this->calendar->set_to(true);
		$this->assertEquals(false, $this->calendar->to);
	}
	/**#@-*/
}
