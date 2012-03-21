<?php /** @package CalendarSolution_Test */

/**
 * Obtain the Calendar Solution's settings and autoload function
 *
 * @internal Uses dirname(__FILE__) because "./" can be stripped by PHP's
 * safety settings and __DIR__ was introduced in PHP 5.3.
 */
require_once dirname(dirname(__FILE__)) . '/Helper.php';

/**
 * Tests the CalendarSolution_Detail_Form class
 *
 * Usage:  phpunit Detail_FormTest
 *
 * @package CalendarSolution_Test
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2002-2012
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 */
class CalendarSolution_Test_Detail_FormTest extends PHPUnit_Framework_TestCase {
	/**
	 * A string to ease finding test entries
	 */
	const PREFIX = 'CSTDFT: ';

	/**
	 * A string to ease finding test entries
	 */
	const CALENDAR_URI = 'http://calendar/';

	/**
	 * A string to ease finding test entries
	 */
	const FREQUENT_EVENT_URI = 'http://frequent/';

	/**
	 * The calendar class to test
	 * @var CalendarSolution_Test_Detail_FormHelper
	 */
	protected static $calendar;

	/**
	 * The calendar id to use for testing
	 * @var int
	 */
	protected $calendar_id;

	/**
	 * The category to use for testing
	 * @var int
	 */
	protected static $category;

	/**
	 * The category id to use for testing
	 * @var int
	 */
	protected static $category_id;

	/**
	 * The frequent event to use for testing
	 * @var int
	 */
	protected static $frequent_event;

	/**
	 * The frequent event id to use for testing
	 * @var int
	 */
	protected static $frequent_event_id;


	/**
	 * Prepares the environment before the first test is run
	 */
	public static function setUpBeforeClass() {
		self::$calendar = new CalendarSolution_Test_Detail_FormHelper;

		self::$calendar->sql->SQLQueryString = 'BEGIN';
		self::$calendar->sql->RunQuery(__FILE__, __LINE__);

		self::$frequent_event = self::PREFIX . 'Frequent Event';
		self::$calendar->sql->SQLQueryString = "INSERT INTO cs_frequent_event
			(frequent_event, frequent_event_uri) VALUES
			('" . self::$frequent_event . "','" . self::FREQUENT_EVENT_URI . "')";
		self::$calendar->sql->RunQuery(__FILE__, __LINE__);
		self::$frequent_event_id = self::$calendar->sql->InsertID(__FILE__, __LINE__);

		self::$category = self::PREFIX . 'Category';
		self::$calendar->sql->SQLQueryString = "INSERT INTO cs_category
			(category) VALUES ('" . self::$category . "')";
		self::$calendar->sql->RunQuery(__FILE__, __LINE__);
		self::$category_id = self::$calendar->sql->InsertID(__FILE__, __LINE__);
	}

	/**
	 * Destroys the environment once the final test is done
	 */
	public static function tearDownAfterClass() {
		self::$calendar->sql->SQLQueryString = 'ROLLBACK';
		self::$calendar->sql->RunQuery(__FILE__, __LINE__);
		self::$calendar->sql->Disconnect(__FILE__, __LINE__);
	}


	/**
	 * Sets the CSRF token information in $_SESSION and $_POST
	 * @return void
	 */
	protected function setToken() {
		$token = uniqid(rand(), true);
		$_SESSION[self::$calendar->csrf_token_name] = $token;
		$_POST[self::$calendar->csrf_token_name] = $token;
	}

	/**
	 * Compares the data submitted to that found in the database
	 * @param array $expect_override  specific field values to replace
	 * @return void
	 */
	protected function checkResult($expect_override = array()) {
		$expect = self::$calendar->data;
		$expect['calendar_id'] = "$this->calendar_id";
		$expect['set_from'] = 'query';
		if (empty($expect['category_id'])) {
			$expect['category'] = '';
		} else {
			$expect['category'] = self::$category;
		}
		if (!empty($expect['frequent_event_id'])
			 && empty($expect['calendar_uri']))
		{
			$expect['display_uri'] = self::FREQUENT_EVENT_URI;
			$expect['frequent_event_uri'] = '<a href="' . $expect['display_uri']
					. '">' . $expect['display_uri'] . '</a>';
		} else {
			$expect['display_uri'] = $expect['calendar_uri'];
			$expect['calendar_uri'] = '<a href="' . $expect['display_uri']
					. '">' . $expect['display_uri'] . '</a>';
		}
		if (!empty($expect['time_start'])) {
			$expect['time_start'] .= ':00';
		}
		if (!empty($expect['time_end'])) {
			$expect['time_end'] .= ':00';
		}
		$expect['status'] = 'Open';
		$expect['feature_on_page_id'] = '0';
		unset($expect['frequency']);
		unset($expect['span']);
		unset($expect['week_of_month']);
		foreach ($expect as $key => $value) {
			if (is_null($value)) {
				$expect[$key] = '';
			}
		}
		$expect = array_merge($expect, $expect_override);

		self::$calendar->set_data_from_query($this->calendar_id);
		$this->assertEquals($expect, self::$calendar->data);
	}


	/**#@+
	 * insert()
	 */
	public function test_insert_minimal() {
		$_POST = array(
			'calendar_uri' => self::CALENDAR_URI,
			'category_id' => '',
			'changed' => 'N',
			'date_start' => '2009-09-01',
			'detail' => '',
			'feature_on_page_id' => array(),
			'frequency' => '',
			'frequent_event_id' => '',
			'is_own_event' => 'Y',
			'list_link_goes_to_id' => '2',
			'location_start' => '',
			'note' => '',
			'span' => '',
			'status_id' => '1',
			'summary' => '',
			'time_end' => '',
			'time_start' => '',
			'title' => self::PREFIX . __FUNCTION__,
			'week_of_month' => '',
		);

		self::$calendar->set_data_from_post();
		if (!self::$calendar->is_valid(false)) {
			$this->fail(implode("\n", self::$calendar->errors));
		}
		$this->setToken();
		self::$calendar->insert();
		$this->calendar_id = self::$calendar->sql->InsertID(__FILE__, __LINE__);
		$this->checkResult();
	}

	public function test_insert_full() {
		$_POST = array(
			'calendar_uri' => '',
			'category_id' => self::$category_id,
			'changed' => 'N',
			'date_start' => '2009-09-01',
			'detail' => 'the details',
			'feature_on_page_id' => array('1'),
			'frequency' => '',
			'frequent_event_id' => self::$frequent_event_id,
			'is_own_event' => 'Y',
			'list_link_goes_to_id' => '2',
			'location_start' => 'the location',
			'note' => 'the note',
			'span' => '',
			'status_id' => '1',
			'summary' => 'the summary',
			'time_end' => '03:04',
			'time_start' => '01:02',
			'title' => self::PREFIX . __FUNCTION__,
			'week_of_month' => '',
		);

		self::$calendar->set_data_from_post();
		if (!self::$calendar->is_valid(false)) {
			$this->fail(implode("\n", self::$calendar->errors));
		}
		$this->setToken();
		self::$calendar->insert();
		$this->calendar_id = self::$calendar->sql->InsertID(__FILE__, __LINE__);
		$expect_override = array(
			'feature_on_page_id' => 1,
		);
		$this->checkResult($expect_override);
	}

	public function test_insert_fail_date_start() {
		$_POST = array(
			'calendar_uri' => self::CALENDAR_URI,
			'category_id' => '',
			'changed' => 'N',
			'date_start' => '',
			'detail' => '',
			'feature_on_page_id' => array(),
			'frequency' => '',
			'frequent_event_id' => '',
			'is_own_event' => 'Y',
			'list_link_goes_to_id' => '2',
			'location_start' => '',
			'note' => '',
			'span' => '',
			'status_id' => '1',
			'summary' => '',
			'time_end' => '',
			'time_start' => '',
			'title' => self::PREFIX . __FUNCTION__,
			'week_of_month' => '',
		);

		self::$calendar->set_data_from_post();
		self::$calendar->is_valid(false);
		$expect = array('Start Date is invalid');
		$this->assertEquals($expect, self::$calendar->errors);
	}
	/**#@-*/

	/**#@+
	 * get_date_starts() first x of the month
	 */
	public function test_get_date_starts__first_tuesday() {
		$_POST = array(
			'date_start' => '2009-09-01',
			'frequency' => 'Monthly',
			'week_of_month' => 'first',
			'span' => 4,
		);

		$expected = array(
			'2009-09-01',
			'2009-10-06',
			'2009-11-03',
			'2009-12-01',
		);

		self::$calendar->set_data_from_post();
		$actual = self::$calendar->get_date_starts();

		$this->assertEquals($expected, $actual);
	}

	public function test_get_date_starts__first_wednesday() {
		$_POST = array(
			'date_start' => '2009-09-02',
			'frequency' => 'Monthly',
			'week_of_month' => 'first',
			'span' => 4,
		);

		$expected = array(
			'2009-09-02',
			'2009-10-07',
			'2009-11-04',
			'2009-12-02',
		);

		self::$calendar->set_data_from_post();
		$actual = self::$calendar->get_date_starts();
		$this->assertEquals($expected, $actual);
	}

	public function test_get_date_starts__first_thursday() {
		$_POST = array(
			'date_start' => '2009-09-03',
			'frequency' => 'Monthly',
			'week_of_month' => 'first',
			'span' => 4,
		);

		$expected = array(
			'2009-09-03',
			'2009-10-01',
			'2009-11-05',
			'2009-12-03',
		);

		self::$calendar->set_data_from_post();
		$actual = self::$calendar->get_date_starts();
		$this->assertEquals($expected, $actual);
	}

	public function test_get_date_starts__first_friday() {
		$_POST = array(
			'date_start' => '2009-09-04',
			'frequency' => 'Monthly',
			'week_of_month' => 'first',
			'span' => 4,
		);

		$expected = array(
			'2009-09-04',
			'2009-10-02',
			'2009-11-06',
			'2009-12-04',
		);

		self::$calendar->set_data_from_post();
		$actual = self::$calendar->get_date_starts();
		$this->assertEquals($expected, $actual);
	}

	public function test_get_date_starts__first_saturday() {
		$_POST = array(
			'date_start' => '2009-09-05',
			'frequency' => 'Monthly',
			'week_of_month' => 'first',
			'span' => 4,
		);

		$expected = array(
			'2009-09-05',
			'2009-10-03',
			'2009-11-07',
			'2009-12-05',
		);

		self::$calendar->set_data_from_post();
		$actual = self::$calendar->get_date_starts();
		$this->assertEquals($expected, $actual);
	}

	public function test_get_date_starts__first_sunday() {
		$_POST = array(
			'date_start' => '2009-09-06',
			'frequency' => 'Monthly',
			'week_of_month' => 'first',
			'span' => 4,
		);

		$expected = array(
			'2009-09-06',
			'2009-10-04',
			'2009-11-01',
			'2009-12-06',
		);

		self::$calendar->set_data_from_post();
		$actual = self::$calendar->get_date_starts();
		$this->assertEquals($expected, $actual);
	}

	public function test_get_date_starts__first_monday() {
		$_POST = array(
			'date_start' => '2009-09-07',
			'frequency' => 'Monthly',
			'week_of_month' => 'first',
			'span' => 4,
		);

		$expected = array(
			'2009-09-07',
			'2009-10-05',
			'2009-11-02',
			'2009-12-07',
		);

		self::$calendar->set_data_from_post();
		$actual = self::$calendar->get_date_starts();
		$this->assertEquals($expected, $actual);
	}
	/**#@-*/

	/**#@+
	 * get_date_starts() second x of the month
	 */
	public function test_get_date_starts__second_tuesday() {
		$_POST = array(
			'date_start' => '2009-09-08',
			'frequency' => 'Monthly',
			'week_of_month' => 'second',
			'span' => 4,
		);

		$expected = array(
			'2009-09-08',
			'2009-10-13',
			'2009-11-10',
			'2009-12-08',
		);

		self::$calendar->set_data_from_post();
		$actual = self::$calendar->get_date_starts();

		$this->assertEquals($expected, $actual);
	}

	public function test_get_date_starts__second_wednesday() {
		$_POST = array(
			'date_start' => '2009-09-09',
			'frequency' => 'Monthly',
			'week_of_month' => 'second',
			'span' => 4,
		);

		$expected = array(
			'2009-09-09',
			'2009-10-14',
			'2009-11-11',
			'2009-12-09',
		);

		self::$calendar->set_data_from_post();
		$actual = self::$calendar->get_date_starts();
		$this->assertEquals($expected, $actual);
	}

	public function test_get_date_starts__second_thursday() {
		$_POST = array(
			'date_start' => '2009-09-10',
			'frequency' => 'Monthly',
			'week_of_month' => 'second',
			'span' => 4,
		);

		$expected = array(
			'2009-09-10',
			'2009-10-08',
			'2009-11-12',
			'2009-12-10',
		);

		self::$calendar->set_data_from_post();
		$actual = self::$calendar->get_date_starts();
		$this->assertEquals($expected, $actual);
	}

	public function test_get_date_starts__second_friday() {
		$_POST = array(
			'date_start' => '2009-09-11',
			'frequency' => 'Monthly',
			'week_of_month' => 'second',
			'span' => 4,
		);

		$expected = array(
			'2009-09-11',
			'2009-10-09',
			'2009-11-13',
			'2009-12-11',
		);

		self::$calendar->set_data_from_post();
		$actual = self::$calendar->get_date_starts();
		$this->assertEquals($expected, $actual);
	}

	public function test_get_date_starts__second_saturday() {
		$_POST = array(
			'date_start' => '2009-09-12',
			'frequency' => 'Monthly',
			'week_of_month' => 'second',
			'span' => 4,
		);

		$expected = array(
			'2009-09-12',
			'2009-10-10',
			'2009-11-14',
			'2009-12-12',
		);

		self::$calendar->set_data_from_post();
		$actual = self::$calendar->get_date_starts();
		$this->assertEquals($expected, $actual);
	}

	public function test_get_date_starts__second_sunday() {
		$_POST = array(
			'date_start' => '2009-09-13',
			'frequency' => 'Monthly',
			'week_of_month' => 'second',
			'span' => 4,
		);

		$expected = array(
			'2009-09-13',
			'2009-10-11',
			'2009-11-08',
			'2009-12-13',
		);

		self::$calendar->set_data_from_post();
		$actual = self::$calendar->get_date_starts();
		$this->assertEquals($expected, $actual);
	}

	public function test_get_date_starts__second_monday() {
		$_POST = array(
			'date_start' => '2009-09-14',
			'frequency' => 'Monthly',
			'week_of_month' => 'second',
			'span' => 4,
		);

		$expected = array(
			'2009-09-14',
			'2009-10-12',
			'2009-11-09',
			'2009-12-14',
		);

		self::$calendar->set_data_from_post();
		$actual = self::$calendar->get_date_starts();
		$this->assertEquals($expected, $actual);
	}
	/**#@-*/

	/**#@+
	 * get_date_starts() third x of the month
	 */
	public function test_get_date_starts__third_tuesday() {
		$_POST = array(
			'date_start' => '2009-09-15',
			'frequency' => 'Monthly',
			'week_of_month' => 'third',
			'span' => 4,
		);

		$expected = array(
			'2009-09-15',
			'2009-10-20',
			'2009-11-17',
			'2009-12-15',
		);

		self::$calendar->set_data_from_post();
		$actual = self::$calendar->get_date_starts();

		$this->assertEquals($expected, $actual);
	}

	public function test_get_date_starts__third_wednesday() {
		$_POST = array(
			'date_start' => '2009-09-16',
			'frequency' => 'Monthly',
			'week_of_month' => 'third',
			'span' => 4,
		);

		$expected = array(
			'2009-09-16',
			'2009-10-21',
			'2009-11-18',
			'2009-12-16',
		);

		self::$calendar->set_data_from_post();
		$actual = self::$calendar->get_date_starts();
		$this->assertEquals($expected, $actual);
	}

	public function test_get_date_starts__third_thursday() {
		$_POST = array(
			'date_start' => '2009-09-17',
			'frequency' => 'Monthly',
			'week_of_month' => 'third',
			'span' => 4,
		);

		$expected = array(
			'2009-09-17',
			'2009-10-15',
			'2009-11-19',
			'2009-12-17',
		);

		self::$calendar->set_data_from_post();
		$actual = self::$calendar->get_date_starts();
		$this->assertEquals($expected, $actual);
	}

	public function test_get_date_starts__third_friday() {
		$_POST = array(
			'date_start' => '2009-09-18',
			'frequency' => 'Monthly',
			'week_of_month' => 'third',
			'span' => 4,
		);

		$expected = array(
			'2009-09-18',
			'2009-10-16',
			'2009-11-20',
			'2009-12-18',
		);

		self::$calendar->set_data_from_post();
		$actual = self::$calendar->get_date_starts();
		$this->assertEquals($expected, $actual);
	}

	public function test_get_date_starts__third_saturday() {
		$_POST = array(
			'date_start' => '2009-09-19',
			'frequency' => 'Monthly',
			'week_of_month' => 'third',
			'span' => 4,
		);

		$expected = array(
			'2009-09-19',
			'2009-10-17',
			'2009-11-21',
			'2009-12-19',
		);

		self::$calendar->set_data_from_post();
		$actual = self::$calendar->get_date_starts();
		$this->assertEquals($expected, $actual);
	}

	public function test_get_date_starts__third_sunday() {
		$_POST = array(
			'date_start' => '2009-09-20',
			'frequency' => 'Monthly',
			'week_of_month' => 'third',
			'span' => 4,
		);

		$expected = array(
			'2009-09-20',
			'2009-10-18',
			'2009-11-15',
			'2009-12-20',
		);

		self::$calendar->set_data_from_post();
		$actual = self::$calendar->get_date_starts();
		$this->assertEquals($expected, $actual);
	}

	public function test_get_date_starts__third_monday() {
		$_POST = array(
			'date_start' => '2009-09-21',
			'frequency' => 'Monthly',
			'week_of_month' => 'third',
			'span' => 4,
		);

		$expected = array(
			'2009-09-21',
			'2009-10-19',
			'2009-11-16',
			'2009-12-21',
		);

		self::$calendar->set_data_from_post();
		$actual = self::$calendar->get_date_starts();
		$this->assertEquals($expected, $actual);
	}
	/**#@-*/

	/**#@+
	 * get_date_starts() fourth x of the month
	 */
	public function test_get_date_starts__fourth_tuesday() {
		$_POST = array(
			'date_start' => '2009-09-22',
			'frequency' => 'Monthly',
			'week_of_month' => 'fourth',
			'span' => 4,
		);

		$expected = array(
			'2009-09-22',
			'2009-10-27',
			'2009-11-24',
			'2009-12-22',
		);

		self::$calendar->set_data_from_post();
		$actual = self::$calendar->get_date_starts();

		$this->assertEquals($expected, $actual);
	}

	public function test_get_date_starts__fourth_wednesday() {
		$_POST = array(
			'date_start' => '2009-09-23',
			'frequency' => 'Monthly',
			'week_of_month' => 'fourth',
			'span' => 4,
		);

		$expected = array(
			'2009-09-23',
			'2009-10-28',
			'2009-11-25',
			'2009-12-23',
		);

		self::$calendar->set_data_from_post();
		$actual = self::$calendar->get_date_starts();
		$this->assertEquals($expected, $actual);
	}

	public function test_get_date_starts__fourth_thursday() {
		$_POST = array(
			'date_start' => '2009-09-24',
			'frequency' => 'Monthly',
			'week_of_month' => 'fourth',
			'span' => 4,
		);

		$expected = array(
			'2009-09-24',
			'2009-10-22',
			'2009-11-26',
			'2009-12-24',
		);

		self::$calendar->set_data_from_post();
		$actual = self::$calendar->get_date_starts();
		$this->assertEquals($expected, $actual);
	}

	public function test_get_date_starts__fourth_friday() {
		$_POST = array(
			'date_start' => '2009-09-25',
			'frequency' => 'Monthly',
			'week_of_month' => 'fourth',
			'span' => 4,
		);

		$expected = array(
			'2009-09-25',
			'2009-10-23',
			'2009-11-27',
			'2009-12-25',
		);

		self::$calendar->set_data_from_post();
		$actual = self::$calendar->get_date_starts();
		$this->assertEquals($expected, $actual);
	}

	public function test_get_date_starts__fourth_saturday() {
		$_POST = array(
			'date_start' => '2009-09-26',
			'frequency' => 'Monthly',
			'week_of_month' => 'fourth',
			'span' => 4,
		);

		$expected = array(
			'2009-09-26',
			'2009-10-24',
			'2009-11-28',
			'2009-12-26',
		);

		self::$calendar->set_data_from_post();
		$actual = self::$calendar->get_date_starts();
		$this->assertEquals($expected, $actual);
	}

	public function test_get_date_starts__fourth_sunday() {
		$_POST = array(
			'date_start' => '2009-09-27',
			'frequency' => 'Monthly',
			'week_of_month' => 'fourth',
			'span' => 4,
		);

		$expected = array(
			'2009-09-27',
			'2009-10-25',
			'2009-11-22',
			'2009-12-27',
		);

		self::$calendar->set_data_from_post();
		$actual = self::$calendar->get_date_starts();
		$this->assertEquals($expected, $actual);
	}

	public function test_get_date_starts__fourth_monday() {
		$_POST = array(
			'date_start' => '2009-09-28',
			'frequency' => 'Monthly',
			'week_of_month' => 'fourth',
			'span' => 4,
		);

		$expected = array(
			'2009-09-28',
			'2009-10-26',
			'2009-11-23',
			'2009-12-28',
		);

		self::$calendar->set_data_from_post();
		$actual = self::$calendar->get_date_starts();
		$this->assertEquals($expected, $actual);
	}
	/**#@-*/

	/**#@+
	 * get_date_starts() last x of the month
	 */
	public function test_get_date_starts__last_tuesday() {
		$_POST = array(
			'date_start' => '2009-09-29',
			'frequency' => 'Monthly',
			'week_of_month' => 'last',
			'span' => 4,
		);

		$expected = array(
			'2009-09-29',
			'2009-10-27',
			'2009-11-24',
			'2009-12-29',
		);

		self::$calendar->set_data_from_post();
		$actual = self::$calendar->get_date_starts();

		$this->assertEquals($expected, $actual);
	}

	public function test_get_date_starts__last_wednesday() {
		$_POST = array(
			'date_start' => '2009-09-30',
			'frequency' => 'Monthly',
			'week_of_month' => 'last',
			'span' => 4,
		);

		$expected = array(
			'2009-09-30',
			'2009-10-28',
			'2009-11-25',
			'2009-12-30',
		);

		self::$calendar->set_data_from_post();
		$actual = self::$calendar->get_date_starts();
		$this->assertEquals($expected, $actual);
	}

	public function test_get_date_starts__last_thursday() {
		$_POST = array(
			'date_start' => '2009-09-24',
			'frequency' => 'Monthly',
			'week_of_month' => 'last',
			'span' => 4,
		);

		$expected = array(
			'2009-09-24',
			'2009-10-29',
			'2009-11-26',
			'2009-12-31',
		);

		self::$calendar->set_data_from_post();
		$actual = self::$calendar->get_date_starts();
		$this->assertEquals($expected, $actual);
	}

	public function test_get_date_starts__last_friday() {
		$_POST = array(
			'date_start' => '2009-09-25',
			'frequency' => 'Monthly',
			'week_of_month' => 'last',
			'span' => 4,
		);

		$expected = array(
			'2009-09-25',
			'2009-10-30',
			'2009-11-27',
			'2009-12-25',
		);

		self::$calendar->set_data_from_post();
		$actual = self::$calendar->get_date_starts();
		$this->assertEquals($expected, $actual);
	}

	public function test_get_date_starts__last_saturday() {
		$_POST = array(
			'date_start' => '2009-09-26',
			'frequency' => 'Monthly',
			'week_of_month' => 'last',
			'span' => 4,
		);

		$expected = array(
			'2009-09-26',
			'2009-10-31',
			'2009-11-28',
			'2009-12-26',
		);

		self::$calendar->set_data_from_post();
		$actual = self::$calendar->get_date_starts();
		$this->assertEquals($expected, $actual);
	}

	public function test_get_date_starts__last_sunday() {
		$_POST = array(
			'date_start' => '2009-09-27',
			'frequency' => 'Monthly',
			'week_of_month' => 'last',
			'span' => 4,
		);

		$expected = array(
			'2009-09-27',
			'2009-10-25',
			'2009-11-29',
			'2009-12-27',
		);

		self::$calendar->set_data_from_post();
		$actual = self::$calendar->get_date_starts();
		$this->assertEquals($expected, $actual);
	}

	public function test_get_date_starts__last_monday() {
		$_POST = array(
			'date_start' => '2009-09-28',
			'frequency' => 'Monthly',
			'week_of_month' => 'last',
			'span' => 4,
		);

		$expected = array(
			'2009-09-28',
			'2009-10-26',
			'2009-11-30',
			'2009-12-28',
		);

		self::$calendar->set_data_from_post();
		$actual = self::$calendar->get_date_starts();
		$this->assertEquals($expected, $actual);
	}
	/**#@-*/
}
