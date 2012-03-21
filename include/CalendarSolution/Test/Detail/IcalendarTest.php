<?php /** @package CalendarSolution_Test */

/**
 * Obtain the Calendar Solution's settings and autoload function
 *
 * @internal Uses dirname(__FILE__) because "./" can be stripped by PHP's
 * safety settings and __DIR__ was introduced in PHP 5.3.
 */
require_once dirname(dirname(__FILE__)) . '/Helper.php';

/**
 * Tests the CalendarSolution_Detail_Icalendar class
 *
 * Usage:  phpunit Detail_IcalendarTest
 *
 * @package CalendarSolution_Test
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2002-2012
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 */
class CalendarSolution_Test_Detail_IcalendarTest extends PHPUnit_Framework_TestCase {
	/**
	 * The calendar class to test
	 * @var CalendarSolution_Test_Detail_IcalendarHelper
	 */
	protected $calendar;

	/**
	 * Prepares the environment before running each test
	 */
	protected function setUp() {
		$this->calendar = new CalendarSolution_Test_Detail_IcalendarHelper;
	}

	/**#@+
	 * escape_for_icalendar()
	 */
	public function test_escape_for_icalendar_text_anchor() {
		$input = 'A <a href="http://uri/">text anchor</a> link';
		$expected = "A text anchor [http://uri/] link";
		$actual = $this->calendar->escape_for_icalendar($input);
		$this->assertEquals($expected, $actual);
	}
	public function test_escape_for_icalendar_uri_anchor() {
		$input = 'A <a href="http://uri/">http://uri/</a> link';
		$expected = "A http://uri/ link";
		$actual = $this->calendar->escape_for_icalendar($input);
		$this->assertEquals($expected, $actual);
	}
	public function test_escape_for_icalendar_specialchars() {
		$input = "Slash\\ CRLF\r\n LF\n Comma, Semi; Colon:";
		$expected = "Slash\\\\ CRLF\\n LF\\n Comma\, Semi\; Colon:";
		$actual = $this->calendar->escape_for_icalendar($input);
		$this->assertEquals($expected, $actual);
	}
	public function test_escape_for_icalendar_wrap() {
		$input = "Let's make some really long string and ensure it gets"
			. " wrapped correctly. It should work fine.";
		$expected = "Let's make some really long string and ensure it gets"
			. " wrapped correctly. It\r\n\t should work fine.";
		$actual = $this->calendar->escape_for_icalendar($input);
		$this->assertEquals($expected, $actual);
	}
	/**#@-*/

	/**#@+
	 * get_event_formatted_icalendar()
	 */
	public function test_get_event_formatted_icalendar_complete() {
		$event = array(
			'calendar_id' => 42,
			'date_start' => '2011-12-13',
			'time_start' => '14:15:16',
			'time_end' => '17:18:19',
			'status_id' => CalendarSolution::STATUS_OPEN,
			'title' => 'Tie One On',
			'summary' => 'Some brief',
			'detail' => 'More details',
			'note' => 'Pay attention',
			'changed' => 'Y',
			'is_own_event' => 'N',
			'location_start' => 'Starting point',
			'calendar_uri' => 'http://example.net/calendar_uri',
			'frequent_event_uri' => 'http://example.net/frequent_event_uri',
			'category' => 'Allegory',
		);
		$expected = "DTSTAMP:20010101T000000\r\n"
			. "UID:42@localhost\r\n"
			. "DTSTART:20111213T141516\r\n"
			. "DTEND:20111213T171819\r\n"
			. "STATUS:CONFIRMED\r\n"
			. "SUMMARY:Tie One On\r\n"
			. "DESCRIPTION:Some brief\\n\\nMore details\r\n"
			. "COMMENT:Pay attention\r\n"
			. "COMMENT:Event changed since first posted.\r\n"
			. "COMMENT:This event is produced by a different group.\r\n"
			. "LOCATION:Starting point\r\n"
			. "URL:http://example.net/calendar_uri\r\n"
			. "CATEGORIES:Allegory\r\n";
		$actual = $this->calendar->get_event_formatted_icalendar($event);
		$this->assertEquals($expected, $actual);
	}

	public function test_get_event_formatted_icalendar_minimal() {
		$event = array(
			'calendar_id' => 42,
			'date_start' => '2011-12-13',
			'time_start' => '',
			'status_id' => CalendarSolution::STATUS_OPEN,
			'title' => 'Tie One On',
			'summary' => '',
			'changed' => 'N',
			'is_own_event' => 'Y',
			'location_start' => '',
		);
		$expected = "DTSTAMP:20000101T000000\r\n"
			. "UID:42@localhost\r\n"
			. "DTSTART;VALUE=DATE:20111213\r\n"
			. "STATUS:CONFIRMED\r\n"
			. "SUMMARY:Tie One On\r\n";
		$actual = $this->calendar->get_event_formatted_icalendar($event);
		$this->assertEquals($expected, $actual);
	}

	public function test_get_event_formatted_icalendar_cancelled() {
		$event = array(
			'calendar_id' => 42,
			'date_start' => '2011-12-13',
			'time_start' => '',
			'status_id' => CalendarSolution::STATUS_CANCELLED,
			'title' => 'Tie One On',
			'summary' => '',
			'changed' => 'N',
			'is_own_event' => 'Y',
			'location_start' => '',
		);
		$expected = "DTSTAMP:20000101T000000\r\n"
			. "UID:42@localhost\r\n"
			. "DTSTART;VALUE=DATE:20111213\r\n"
			. "STATUS:CANCELLED\r\n"
			. "SUMMARY:Tie One On\r\n";
		$actual = $this->calendar->get_event_formatted_icalendar($event);
		$this->assertEquals($expected, $actual);
	}

	public function test_get_event_formatted_icalendar_full() {
		$event = array(
			'calendar_id' => 42,
			'date_start' => '2011-12-13',
			'time_start' => '',
			'status_id' => CalendarSolution::STATUS_FULL,
			'title' => 'Tie One On',
			'summary' => '',
			'changed' => 'N',
			'is_own_event' => 'Y',
			'location_start' => '',
		);
		$expected = "DTSTAMP:20000101T000000\r\n"
			. "UID:42@localhost\r\n"
			. "DTSTART;VALUE=DATE:20111213\r\n"
			. "STATUS:CONFIRMED\r\n"
			. "SUMMARY:FULL: Tie One On\r\n";
		$actual = $this->calendar->get_event_formatted_icalendar($event);
		$this->assertEquals($expected, $actual);
	}

	public function test_get_event_formatted_icalendar_start() {
		$event = array(
			'calendar_id' => 42,
			'date_start' => '2011-12-13',
			'time_start' => '14:15:16',
			'time_end' => '',
			'status_id' => CalendarSolution::STATUS_OPEN,
			'title' => 'Tie One On',
			'summary' => '',
			'changed' => 'N',
			'is_own_event' => 'Y',
			'location_start' => '',
		);
		$expected = "DTSTAMP:20000101T000000\r\n"
			. "UID:42@localhost\r\n"
			. "DTSTART:20111213T141516\r\n"
			. "STATUS:CONFIRMED\r\n"
			. "SUMMARY:Tie One On\r\n";
		$actual = $this->calendar->get_event_formatted_icalendar($event);
		$this->assertEquals($expected, $actual);
	}

	public function test_get_event_formatted_icalendar_end() {
		$event = array(
			'calendar_id' => 42,
			'date_start' => '2011-12-13',
			'time_start' => '',
			'time_end' => '17:18:19',
			'status_id' => CalendarSolution::STATUS_OPEN,
			'title' => 'Tie One On',
			'summary' => '',
			'changed' => 'N',
			'is_own_event' => 'Y',
			'location_start' => '',
		);
		$expected = "DTSTAMP:20000101T000000\r\n"
			. "UID:42@localhost\r\n"
			. "DTSTART;VALUE=DATE:20111213\r\n"
			. "STATUS:CONFIRMED\r\n"
			. "SUMMARY:Tie One On\r\n";
		$actual = $this->calendar->get_event_formatted_icalendar($event);
		$this->assertEquals($expected, $actual);
	}

	public function test_get_event_formatted_icalendar_summary() {
		$event = array(
			'calendar_id' => 42,
			'date_start' => '2011-12-13',
			'time_start' => '',
			'status_id' => CalendarSolution::STATUS_OPEN,
			'title' => 'Tie One On',
			'summary' => 'Some brief',
			'detail' => '',
			'changed' => 'N',
			'is_own_event' => 'Y',
			'location_start' => '',
		);
		$expected = "DTSTAMP:20000101T000000\r\n"
			. "UID:42@localhost\r\n"
			. "DTSTART;VALUE=DATE:20111213\r\n"
			. "STATUS:CONFIRMED\r\n"
			. "SUMMARY:Tie One On\r\n"
			. "DESCRIPTION:Some brief\r\n";
		$actual = $this->calendar->get_event_formatted_icalendar($event);
		$this->assertEquals($expected, $actual);
	}

	public function test_get_event_formatted_icalendar_detail() {
		$event = array(
			'calendar_id' => 42,
			'date_start' => '2011-12-13',
			'time_start' => '',
			'status_id' => CalendarSolution::STATUS_OPEN,
			'title' => 'Tie One On',
			'summary' => '',
			'detail' => 'More details',
			'changed' => 'N',
			'is_own_event' => 'Y',
			'location_start' => '',
		);
		$expected = "DTSTAMP:20000101T000000\r\n"
			. "UID:42@localhost\r\n"
			. "DTSTART;VALUE=DATE:20111213\r\n"
			. "STATUS:CONFIRMED\r\n"
			. "SUMMARY:Tie One On\r\n"
			. "DESCRIPTION:More details\r\n";
		$actual = $this->calendar->get_event_formatted_icalendar($event);
		$this->assertEquals($expected, $actual);
	}

	public function test_get_event_formatted_icalendar_frequent_event_uri() {
		$event = array(
			'calendar_id' => 42,
			'date_start' => '2011-12-13',
			'time_start' => '',
			'status_id' => CalendarSolution::STATUS_OPEN,
			'title' => 'Tie One On',
			'summary' => '',
			'detail' => 'More details',
			'changed' => 'N',
			'is_own_event' => 'Y',
			'location_start' => '',
			'frequent_event_uri' => 'http://example.net/frequent_event_uri',
		);
		$expected = "DTSTAMP:20000101T000000\r\n"
			. "UID:42@localhost\r\n"
			. "DTSTART;VALUE=DATE:20111213\r\n"
			. "STATUS:CONFIRMED\r\n"
			. "SUMMARY:Tie One On\r\n"
			. "DESCRIPTION:More details\r\n"
			. "URL:http://example.net/frequent_event_uri\r\n";
		$actual = $this->calendar->get_event_formatted_icalendar($event);
		$this->assertEquals($expected, $actual);
	}
	/**#@-*/
}
