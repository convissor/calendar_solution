<?php /** @package CalendarSolution_Test */

/**
 * Tests the CalendarSolution_List class' run_query method
 *
 * Usage:  phpunit List_RunQueryTest
 *
 * @package CalendarSolution_Test
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2002-2011
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 */
class CalendarSolution_Test_List_RunQueryTest extends PHPUnit_Framework_TestCase {
	/**
	 * The calendar class to test
	 * @var CalendarSolution_Test_List_ListHelper
	 */
	protected $calendar;

	/**
	 * Is caching is enabled in the calendar class?
	 * @var bool
	 */
	protected $cache_available;

	/**
	 * The year and month of the next month in "YYYY-MM" format
	 * @var string
	 */
	protected static $ym;

	/**
	 * Prepare the environment before the first test is run
	 */
	public static function setUpBeforeClass() {
		$date = new DateTimeSolution(date('Y-m-01'));
		$date->add(new DateIntervalSolution('P1M'));
		self::$ym = $date->format('Y-m');
	}

	/**
	 * Prepares the environment before each test runs
	 */
	public function setUp() {
		$this->calendar = new CalendarSolution_Test_List_ListHelper;

		$ym = self::$ym;
		$this->cache_available = $this->calendar->use_cache;

		$this->calendar->sql->SQLQueryString = 'BEGIN';
		$this->calendar->sql->RunQuery(__FILE__, __LINE__);

		// Categories
		$this->calendar->sql->SQLQueryString = "INSERT INTO cs_category
			(category_id, category) VALUES (9001, 'Nine Thousand One')";
		$this->calendar->sql->RunQuery(__FILE__, __LINE__);

		$this->calendar->sql->SQLQueryString = "INSERT INTO cs_category
			(category_id, category) VALUES (9002, 'Nine Thousand Two')";
		$this->calendar->sql->RunQuery(__FILE__, __LINE__);

		// Frequent Events
		$this->calendar->sql->SQLQueryString = "INSERT INTO cs_frequent_event
			(frequent_event_id, frequent_event) VALUES (901, 'Nine Hundred One')";
		$this->calendar->sql->RunQuery(__FILE__, __LINE__);

		$this->calendar->sql->SQLQueryString = "INSERT INTO cs_frequent_event
			(frequent_event_id, frequent_event) VALUES (902, 'Nine Hundred Two')";
		$this->calendar->sql->RunQuery(__FILE__, __LINE__);

		// Events
		$this->calendar->sql->SQLQueryString = "DELETE FROM cs_calendar";
		$this->calendar->sql->RunQuery(__FILE__, __LINE__);

		$this->calendar->sql->SQLQueryString = "INSERT INTO cs_calendar
			(category_id, frequent_event_id, date_start, feature_on_page_id,
			status_id, title)
			VALUES (9001, 901, '$ym-01', 1, 1, 'one')";
		$this->calendar->sql->RunQuery(__FILE__, __LINE__);

		$this->calendar->sql->SQLQueryString = "INSERT INTO cs_calendar
			(category_id, frequent_event_id, date_start, feature_on_page_id,
			status_id, title)
			VALUES (9002, 902, '$ym-02', null, 2, 'two')";
		$this->calendar->sql->RunQuery(__FILE__, __LINE__);

		$this->calendar->sql->SQLQueryString = "INSERT INTO cs_calendar
			(category_id, frequent_event_id, date_start, feature_on_page_id,
			status_id, title)
			VALUES (null, null, '$ym-03', null, 3, 'three canned')";
		$this->calendar->sql->RunQuery(__FILE__, __LINE__);

		$this->calendar->sql->SQLQueryString = "INSERT INTO cs_calendar
			(category_id, frequent_event_id, date_start, feature_on_page_id,
			status_id, title)
			VALUES (null, null, '$ym-04', null, 1, 'four')";
		$this->calendar->sql->RunQuery(__FILE__, __LINE__);

		if ($this->cache_available) {
			$this->calendar->cache->flush();
			$this->calendar->use_cache = false;
		}
	}

	/**
	 * Deconstructs the environment after running each test
	 */
	public function tearDown() {
		$this->calendar->sql->SQLQueryString = 'ROLLBACK';
		$this->calendar->sql->RunQuery(__FILE__, __LINE__);

		if ($this->cache_available) {
			$this->calendar->cache->flush();
		}
	}


	public function test_normal() {
		$this->calendar->run_query();
		$this->assertEquals(4, count($this->calendar->data));
	}

	public function test_category_one() {
		$this->calendar->set_category_id(9001);
		$this->calendar->run_query();

		$this->assertEquals(1, count($this->calendar->data));
		$this->assertEquals('one', $this->calendar->data[0]['title']);
	}
	public function test_category_one_two() {
		$this->calendar->set_category_id(array(9001, 9002));
		$this->calendar->run_query();

		$this->assertEquals(2, count($this->calendar->data));
		$this->assertEquals('one', $this->calendar->data[0]['title']);
		$this->assertEquals('two', $this->calendar->data[1]['title']);
	}

	public function test_frequent_event() {
		$this->calendar->set_frequent_event_id(901);
		$this->calendar->run_query();

		$this->assertEquals(1, count($this->calendar->data));
		$this->assertEquals('one', $this->calendar->data[0]['title']);
	}

	public function test_from() {
		$this->calendar->set_from(self::$ym . '-04');
		$this->calendar->run_query();

		$this->assertEquals(1, count($this->calendar->data));
		$this->assertEquals('four', $this->calendar->data[0]['title']);
	}
	public function test_to() {
		$this->calendar->set_to(self::$ym . '-01');
		$this->calendar->run_query();

		$this->assertEquals(1, count($this->calendar->data));
		$this->assertEquals('one', $this->calendar->data[0]['title']);
	}
	public function test_from_to() {
		$this->calendar->set_from(self::$ym . '-02');
		$this->calendar->set_to(self::$ym . '-03');
		$this->calendar->run_query();

		$this->assertEquals(2, count($this->calendar->data));
		$this->assertEquals('two', $this->calendar->data[0]['title']);
		$this->assertEquals('three canned', $this->calendar->data[1]['title']);
	}

	public function test_page() {
		$this->calendar->set_page_id(1);
		$this->calendar->run_query();

		$this->assertEquals(1, count($this->calendar->data));
		$this->assertEquals('one', $this->calendar->data[0]['title']);
	}

	public function test_no_cancelled() {
		$this->calendar->set_show_cancelled(false);
		$this->calendar->run_query();

		$this->assertEquals(3, count($this->calendar->data));
		$this->assertEquals('one', $this->calendar->data[0]['title']);
		$this->assertEquals('two', $this->calendar->data[1]['title']);
		$this->assertEquals('four', $this->calendar->data[2]['title']);
	}

	public function test_limit_no_cache() {
		$this->calendar->set_limit(1, 0);
		$this->calendar->run_query();
		$this->assertEquals(1, count($this->calendar->data));
		$this->assertEquals('one', $this->calendar->data[0]['title']);

		$this->calendar->set_limit(1, 1);
		$this->calendar->run_query();
		$this->assertEquals(1, count($this->calendar->data));
		$this->assertEquals('two', $this->calendar->data[0]['title']);

		$this->calendar->set_limit(1, 2);
		$this->calendar->run_query();
		$this->assertEquals(1, count($this->calendar->data));
		$this->assertEquals('three canned', $this->calendar->data[0]['title']);

		$this->calendar->set_limit(1, 3);
		$this->calendar->run_query();
		$this->assertEquals(1, count($this->calendar->data));
		$this->assertEquals('four', $this->calendar->data[0]['title']);

		$this->calendar->set_limit(2, 0);
		$this->calendar->run_query();
		$this->assertEquals(2, count($this->calendar->data));
		$this->assertEquals('one', $this->calendar->data[0]['title']);
		$this->assertEquals('two', $this->calendar->data[1]['title']);

		$this->calendar->set_limit(2, 3);
		$this->calendar->run_query();
		$this->assertEquals(1, count($this->calendar->data));
		$this->assertEquals('four', $this->calendar->data[0]['title']);

		$this->calendar->set_limit(2, 4);
		$this->calendar->run_query();
		$this->assertEquals(0, count($this->calendar->data));
	}

	public function test_cache_one() {
		if (!$this->cache_available) {
			$this->markTestSkipped();
		}

		$this->calendar->use_cache = true;
		$this->calendar->set_category_id(9001);
		$this->calendar->run_query();
		$this->assertEquals(1, count($this->calendar->data));
		$this->assertEquals('one', $this->calendar->data[0]['title']);

		$this->calendar->sql->SQLQueryString = "UPDATE cs_calendar
			SET title = 'one mod' WHERE title = 'one'";
		$this->calendar->sql->RunQuery(__FILE__, __LINE__);

		$this->calendar->use_cache = true;
		$this->calendar->set_category_id(9001);
		$this->calendar->run_query();
		$this->assertEquals(1, count($this->calendar->data));
		$this->assertEquals('one', $this->calendar->data[0]['title']);

		$this->calendar->use_cache = false;
		$this->calendar->set_category_id(9001);
		$this->calendar->run_query();
		$this->assertEquals(1, count($this->calendar->data));
		$this->assertEquals('one mod', $this->calendar->data[0]['title']);

		$this->calendar->use_cache = true;
		$this->calendar->set_category_id(9001);
		$this->calendar->run_query();
		$this->assertEquals(1, count($this->calendar->data));
		$this->assertEquals('one', $this->calendar->data[0]['title']);

		$this->calendar->use_cache = true;
		$this->calendar->cache->flush();
		$this->calendar->set_category_id(9001);
		$this->calendar->run_query();
		$this->assertEquals(1, count($this->calendar->data));
		$this->assertEquals('one mod', $this->calendar->data[0]['title']);
	}

	public function test_limit_cache() {
		if (!$this->cache_available) {
			$this->markTestSkipped();
		}
		$this->calendar->use_cache = true;

		$this->calendar->set_limit(1, 0);
		$this->calendar->run_query();
		$this->assertEquals(1, count($this->calendar->data));
		$this->assertEquals('one', $this->calendar->data[0]['title']);

		$this->calendar->set_limit(1, 0);
		$this->calendar->run_query();
		$this->assertEquals(1, count($this->calendar->data));
		$this->assertEquals('one', $this->calendar->data[0]['title']);

		$this->calendar->set_limit(1, 1);
		$this->calendar->run_query();
		$this->assertEquals(1, count($this->calendar->data));
		$this->assertEquals('two', $this->calendar->data[0]['title']);

		$this->calendar->set_limit(1, 2);
		$this->calendar->run_query();
		$this->assertEquals(1, count($this->calendar->data));
		$this->assertEquals('three canned', $this->calendar->data[0]['title']);

		$this->calendar->set_limit(1, 3);
		$this->calendar->run_query();
		$this->assertEquals(1, count($this->calendar->data));
		$this->assertEquals('four', $this->calendar->data[0]['title']);

		$this->calendar->set_limit(2, 0);
		$this->calendar->run_query();
		$this->assertEquals(2, count($this->calendar->data));
		$this->assertEquals('one', $this->calendar->data[0]['title']);
		$this->assertEquals('two', $this->calendar->data[1]['title']);

		$this->calendar->set_limit(2, 3);
		$this->calendar->run_query();
		$this->assertEquals(1, count($this->calendar->data));
		$this->assertEquals('four', $this->calendar->data[0]['title']);

		$this->calendar->set_limit(2, 4);
		$this->calendar->run_query();
		$this->assertEquals(0, count($this->calendar->data));
	}
}
