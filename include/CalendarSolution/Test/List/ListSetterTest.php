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
	public function test_category_id_input_false() {
		$_REQUEST = array('category_id' => 2);
		$this->calendar->set_category_id(false);
		$this->assertEquals(false, $this->calendar->category_id);
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
	public function test_frequent_event_id_input_false() {
		$_REQUEST = array('frequent_event_id' => 2);
		$this->calendar->set_frequent_event_id(false);
		$this->assertEquals(false, $this->calendar->frequent_event_id);
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
	public function test_from_input_false() {
		$_REQUEST = array('from' => '2011-12-13');
		$this->calendar->set_from(false);
		$this->assertEquals(false, $this->calendar->from);
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
	public function test_from_permit_history_months_3_reset() {
		$this->calendar->set_permit_history_months(3);
		$this->calendar->set_from('1900-01-01');
		$expect = date('Y-m-01', strtotime('-2 month'));
		$this->assertEquals($expect, $this->calendar->from->format('Y-m-d'));
	}
	public function test_from_permit_history_months_unset() {
		$this->calendar->set_from('3000-01-31');
		$this->assertEquals('3000-01-31', $this->calendar->from->format('Y-m-d'));
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
	public function test_to_input_false() {
		$_REQUEST = array('to' => '2011-12-13');
		$this->calendar->set_to(false);
		$this->assertEquals(false, $this->calendar->to);
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
	public function test_to_permit_future_months_3_reset() {
		$this->calendar->set_permit_future_months(3);
		$this->calendar->set_to('3000-01-31');
		$this->assertEquals($this->to_default, $this->calendar->to->format('Y-m-d'));
	}
	public function test_to_permit_future_months_unset() {
		$this->calendar->set_to('3000-01-31');
		$this->assertEquals('3000-01-31', $this->calendar->to->format('Y-m-d'));
	}
	/**#@-*/

	/**#@+
	 * set_limit()
	 */
	public function test_limit_request_good_both() {
		$_REQUEST = array('limit_quantity' => 2, 'limit_start' => 4);
		$this->calendar->set_limit(null, null);
		$this->assertEquals(2, $this->calendar->limit_quantity, 'Quantity');
		$this->assertEquals(4, $this->calendar->limit_start, 'Start');
	}
	public function test_limit_request_good_quantity() {
		$_REQUEST = array('limit_quantity' => 2);
		$this->calendar->set_limit(null, null);
		$this->assertEquals(2, $this->calendar->limit_quantity, 'Quantity');
		$this->assertEquals(0, $this->calendar->limit_start, 'Start');
	}
	public function test_limit_request_good_start() {
		$_REQUEST = array('limit_start' => 4);
		$this->calendar->set_limit(null, null);
		$this->assertEquals(false, $this->calendar->limit_quantity, 'Quantity');
		$this->assertEquals(false, $this->calendar->limit_start, 'Start');
	}

	public function test_limit_input_good_both() {
		$_REQUEST = array('limit_quantity' => 2, 'limit_start' => 4);
		$this->calendar->set_limit(6, 12);
		$this->assertEquals(6, $this->calendar->limit_quantity, 'Quantity');
		$this->assertEquals(12, $this->calendar->limit_start, 'Start');
	}
	public function test_limit_input_quantity_request_start() {
		$_REQUEST = array('limit_quantity' => 2, 'limit_start' => 4);
		$this->calendar->set_limit(6, null);
		$this->assertEquals(6, $this->calendar->limit_quantity, 'Quantity');
		$this->assertEquals(4, $this->calendar->limit_start, 'Start');
	}
	public function test_limit_request_quantity_input_start() {
		$_REQUEST = array('limit_quantity' => 2, 'limit_start' => 4);
		$this->calendar->set_limit(null, 12);
		$this->assertEquals(2, $this->calendar->limit_quantity, 'Quantity');
		$this->assertEquals(12, $this->calendar->limit_start, 'Start');
	}

	public function test_limit_input_quantity_false_start() {
		$_REQUEST = array('limit_quantity' => 2, 'limit_start' => 4);
		$this->calendar->set_limit(6, false);
		$this->assertEquals(6, $this->calendar->limit_quantity, 'Quantity');
		$this->assertEquals(false, $this->calendar->limit_start, 'Start');
	}
	public function test_limit_false_quantity_request_start() {
		$_REQUEST = array('limit_quantity' => 2, 'limit_start' => 4);
		$this->calendar->set_limit(false);
		$this->assertEquals(false, $this->calendar->limit_quantity, 'Quantity');
		$this->assertEquals(false, $this->calendar->limit_start, 'Start');
	}

	public function test_limit_request_bad_array_both() {
		$_REQUEST = array('limit_quantity' => array('some'), 'limit_start' => array('string'));
		$this->calendar->set_limit(null, null);
		$this->assertEquals(false, $this->calendar->limit_quantity, 'Quantity');
		$this->assertEquals(false, $this->calendar->limit_start, 'Start');
	}
	public function test_limit_request_bad_array_quantity() {
		$_REQUEST = array('limit_quantity' => array('some'), 'limit_start' => 4);
		$this->calendar->set_limit(null, null);
		$this->assertEquals(false, $this->calendar->limit_quantity, 'Quantity');
		$this->assertEquals(false, $this->calendar->limit_start, 'Start');
	}
	public function test_limit_request_bad_array_start() {
		$_REQUEST = array('limit_quantity' => 2, 'limit_start' => array('string'));
		$this->calendar->set_limit(null, null);
		$this->assertEquals(2, $this->calendar->limit_quantity, 'Quantity');
		$this->assertEquals(false, $this->calendar->limit_start, 'Start');
	}

	public function test_limit_input_bad_array() {
		$_REQUEST = array('limit_quantity' => 'some', 'limit_start' => 'string');
		$this->calendar->set_limit(array('some'), array('string'));
		$this->assertEquals(false, $this->calendar->limit_quantity, 'Quantity');
		$this->assertEquals(false, $this->calendar->limit_start, 'Start');
	}
	public function test_limit_input_bad_array_start() {
		$_REQUEST = array('limit_quantity' => 'some', 'limit_start' => 'string');
		$this->calendar->set_limit(6, array('string'));
		$this->assertEquals(6, $this->calendar->limit_quantity, 'Quantity');
		$this->assertEquals(false, $this->calendar->limit_start, 'Start');
	}
	public function test_limit_input_bad_array_quantity() {
		$_REQUEST = array('limit_quantity' => 'some', 'limit_start' => 'string');
		$this->calendar->set_limit(array('some'), 12);
		$this->assertEquals(false, $this->calendar->limit_quantity, 'Quantity');
		$this->assertEquals(false, $this->calendar->limit_start, 'Start');
	}

	public function test_limit_request_bad() {
		$_REQUEST = array('limit_quantity' => 'some', 'limit_start' => 'string');
		$this->calendar->set_limit(null, null);
		$this->assertEquals(false, $this->calendar->limit_quantity, 'Quantity');
		$this->assertEquals(false, $this->calendar->limit_start, 'Start');
	}
	public function test_limit_input_bad() {
		$_REQUEST = array('limit_quantity' => 'some', 'limit_start' => 'string');
		$this->calendar->set_limit('some', 'string');
		$this->assertEquals(false, $this->calendar->limit_quantity, 'Quantity');
		$this->assertEquals(false, $this->calendar->limit_start, 'Start');
	}
	public function test_limit_input_bad_start() {
		$_REQUEST = array('limit_quantity' => 'some', 'limit_start' => 'string');
		$this->calendar->set_limit(6, 'string');
		$this->assertEquals(6, $this->calendar->limit_quantity, 'Quantity');
		$this->assertEquals(false, $this->calendar->limit_start, 'Start');
	}
	public function test_limit_input_bad_quantity() {
		$_REQUEST = array('limit_quantity' => 'some', 'limit_start' => 'string');
		$this->calendar->set_limit('some', 12);
		$this->assertEquals(false, $this->calendar->limit_quantity, 'Quantity');
		$this->assertEquals(false, $this->calendar->limit_start, 'Start');
	}
	/**#@-*/

	/**#@+
	 * set_page_id()
	 */
	public function test_page_id_input_good() {
		$this->calendar->set_page_id(4);
		$this->assertEquals(4, $this->calendar->page_id);
	}
	public function test_page_id_input_false() {
		$this->calendar->set_page_id(false);
		$this->assertEquals(false, $this->calendar->page_id);
	}
	public function test_page_id_input_bad_array_string() {
		$this->calendar->set_page_id(array('some string'));
		$this->assertEquals(false, $this->calendar->page_id);
	}
	public function test_page_id_input_bad_array_int() {
		$this->calendar->set_page_id(array(3));
		$this->assertEquals(false, $this->calendar->page_id);
	}
	public function test_page_id_input_bad_string() {
		$this->calendar->set_page_id('some string');
		$this->assertEquals(false, $this->calendar->page_id);
	}
	public function test_page_id_input_odd_numberstring() {
		$this->calendar->set_page_id('l33t');
		$this->assertEquals(false, $this->calendar->page_id);
	}
	/**#@-*/

	/**#@+
	 * set_permit_future_months()
	 */
	public function test_permit_future_months_0() {
		$this->calendar->set_permit_future_months(0);
		$this->assertEquals($this->to_default,
				$this->calendar->permit_future_date->format('Y-m-d'));
	}
	public function test_permit_future_months_1() {
		$this->calendar->set_permit_future_months(1);
		$this->assertEquals($this->to_default,
				$this->calendar->permit_future_date->format('Y-m-d'));
	}
	public function test_permit_future_months_2() {
		$this->calendar->set_permit_future_months(2);
		$this->assertEquals($this->to_default,
				$this->calendar->permit_future_date->format('Y-m-d'));
	}
	public function test_permit_future_months_3() {
		$this->calendar->set_permit_future_months(3);
		$this->assertEquals($this->to_default,
				$this->calendar->permit_future_date->format('Y-m-d'));
	}
	public function test_permit_future_months_13() {
		$this->calendar->set_permit_future_months(13);
		$expect = date('Y-m-t', strtotime(date('Y') + 1 . date('-m') . '-01'));
		$this->assertEquals($expect,
				$this->calendar->permit_future_date->format('Y-m-d'));
	}
	public function test_permit_future_months_false() {
		$this->calendar->set_permit_future_months(false);
		$this->assertEquals(false, $this->calendar->permit_future_date);
	}
	public function test_permit_future_months_3_reset_to() {
		$this->calendar->set_to('3000-01-31');
		$this->calendar->set_permit_future_months(3);
		$this->assertEquals($this->to_default,
				$this->calendar->to->format('Y-m-d'), 'to did not get reset');
	}
	public function test_permit_future_months_3_dont_reset_to() {
		$date = date('Y-m-d');
		$this->calendar->set_to($date);
		$this->calendar->set_permit_future_months(3);
		$this->assertEquals($date,
				$this->calendar->to->format('Y-m-d'), 'to got reset');
	}
	/**#@-*/

	/**#@+
	 * set_permit_history_months()
	 */
	public function test_permit_history_months_0() {
		$this->calendar->set_permit_history_months(0);
		$this->assertEquals(date('Y-m-d'),
				$this->calendar->permit_history_date->format('Y-m-d'));
	}
	public function test_permit_history_months_1() {
		$this->calendar->set_permit_history_months(1);
		$this->assertEquals(date('Y-m-01'),
				$this->calendar->permit_history_date->format('Y-m-d'));
	}
	public function test_permit_history_months_2() {
		$this->calendar->set_permit_history_months(2);
		$expect = date('Y-m-01', strtotime('-1 month'));
		$this->assertEquals($expect,
				$this->calendar->permit_history_date->format('Y-m-d'));
	}
	public function test_permit_history_months_3() {
		$this->calendar->set_permit_history_months(3);
		$expect = date('Y-m-01', strtotime('-2 month'));
		$this->assertEquals($expect,
				$this->calendar->permit_history_date->format('Y-m-d'));
	}
	public function test_permit_history_months_13() {
		$this->calendar->set_permit_history_months(13);
		$expect = date('Y') - 1 . date('-m') . '-01';
		$this->assertEquals($expect,
				$this->calendar->permit_history_date->format('Y-m-d'));
	}
	public function test_permit_history_months_false() {
		$this->calendar->set_permit_history_months(false);
		$this->assertEquals(false, $this->calendar->permit_history_date);
	}
	public function test_permit_history_months_3_reset_from() {
		$this->calendar->set_from('1900-01-01');
		$this->calendar->set_permit_history_months(3);
		$this->assertEquals($this->calendar->permit_history_date->format('Y-m-d'),
				$this->calendar->from->format('Y-m-d'), 'from did not get reset');
	}
	public function test_permit_history_months_3_dont_reset_from() {
		$date = date('Y-m-d');
		$this->calendar->set_from($date);
		$this->calendar->set_permit_history_months(3);
		$this->assertEquals($date,
				$this->calendar->from->format('Y-m-d'), 'from got reset');
	}
	/**#@-*/

	/**#@+
	 * set_request_properties()
	 */
	public function test_request_properties_request_full() {
		$_REQUEST = array(
			'category_id' => 22,
			'frequent_event_id' => 33,
			'from' => '2001-02-03',
			'to' => '2004-05-06',
		);
		$this->calendar->set_request_properties();
		$this->assertEquals(array(22), $this->calendar->category_id);
		$this->assertEquals(33, $this->calendar->frequent_event_id);
		$this->assertEquals('2001-02-03', $this->calendar->from->format('Y-m-d'));
		$this->assertEquals('2004-05-06', $this->calendar->to->format('Y-m-d'));
	}
	public function test_request_properties_request_category() {
		$_REQUEST = array(
			'category_id' => 22,
		);
		$this->calendar->set_request_properties();
		$this->assertEquals(array(22), $this->calendar->category_id);
		$this->assertEquals(null, $this->calendar->frequent_event_id);
		$this->assertEquals(date('Y-m-d'), $this->calendar->from->format('Y-m-d'));
		$this->assertEquals($this->to_default, $this->calendar->to->format('Y-m-d'));
	}
	public function test_request_properties_request_frequent_event() {
		$_REQUEST = array(
			'frequent_event_id' => 33,
		);
		$this->calendar->set_request_properties();
		$this->assertEquals(null, $this->calendar->category_id);
		$this->assertEquals(33, $this->calendar->frequent_event_id);
		$this->assertEquals(date('Y-m-d'), $this->calendar->from->format('Y-m-d'));
		$this->assertEquals($this->to_default, $this->calendar->to->format('Y-m-d'));
	}
	public function test_request_properties_request_from() {
		$_REQUEST = array(
			'from' => '2001-02-03',
		);
		$this->calendar->set_request_properties();
		$this->assertEquals(null, $this->calendar->category_id);
		$this->assertEquals(null, $this->calendar->frequent_event_id);
		$this->assertEquals('2001-02-03', $this->calendar->from->format('Y-m-d'));
		$this->assertEquals($this->to_default, $this->calendar->to->format('Y-m-d'));
	}
	public function test_request_properties_request_to() {
		$_REQUEST = array(
			'to' => '2004-05-06',
		);
		$this->calendar->set_request_properties();
		$this->assertEquals(null, $this->calendar->category_id);
		$this->assertEquals(null, $this->calendar->frequent_event_id);
		$this->assertEquals(date('Y-m-d'), $this->calendar->from->format('Y-m-d'));
		$this->assertEquals('2004-05-06', $this->calendar->to->format('Y-m-d'));
	}

	public function test_request_properties_request_category_false() {
		$_REQUEST = array(
			'category_id' => 22,
			'frequent_event_id' => 33,
			'from' => '2001-02-03',
			'to' => '2004-05-06',
		);
		$this->calendar->set_category_id(false);

		$this->calendar->set_request_properties();
		$this->assertEquals(false, $this->calendar->category_id);
		$this->assertEquals(33, $this->calendar->frequent_event_id);
		$this->assertEquals('2001-02-03', $this->calendar->from->format('Y-m-d'));
		$this->assertEquals('2004-05-06', $this->calendar->to->format('Y-m-d'));
	}
	public function test_request_properties_request_frequent_event_false() {
		$_REQUEST = array(
			'category_id' => 22,
			'frequent_event_id' => 33,
			'from' => '2001-02-03',
			'to' => '2004-05-06',
		);
		$this->calendar->set_frequent_event_id(false);

		$this->calendar->set_request_properties();
		$this->assertEquals(array(22), $this->calendar->category_id);
		$this->assertEquals(false, $this->calendar->frequent_event_id);
		$this->assertEquals('2001-02-03', $this->calendar->from->format('Y-m-d'));
		$this->assertEquals('2004-05-06', $this->calendar->to->format('Y-m-d'));
	}
	public function test_request_properties_request_from_false() {
		$_REQUEST = array(
			'category_id' => 22,
			'frequent_event_id' => 33,
			'from' => '2001-02-03',
			'to' => '2004-05-06',
		);
		$this->calendar->set_from(false);

		$this->calendar->set_request_properties();
		$this->assertEquals(array(22), $this->calendar->category_id);
		$this->assertEquals(33, $this->calendar->frequent_event_id);
		$this->assertEquals(false, $this->calendar->from);
		$this->assertEquals('2004-05-06', $this->calendar->to->format('Y-m-d'));
	}
	public function test_request_properties_request_to_false() {
		$_REQUEST = array(
			'category_id' => 22,
			'frequent_event_id' => 33,
			'from' => '2001-02-03',
			'to' => '2004-05-06',
		);
		$this->calendar->set_to(false);

		$this->calendar->set_request_properties();
		$this->assertEquals(array(22), $this->calendar->category_id);
		$this->assertEquals(33, $this->calendar->frequent_event_id);
		$this->assertEquals('2001-02-03', $this->calendar->from->format('Y-m-d'));
		$this->assertEquals(false, $this->calendar->to);
	}
	/**#@-*/
}
