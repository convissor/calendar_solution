<?php /** @package CalendarSolution_Test */

/**
 * Tests the CalendarSolution_List_Calendar class' Getter methods
 *
 * Usage:  phpunit List_CalendarGetterTest
 *
 * @package CalendarSolution_Test
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2002-2011
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 */
class CalendarSolution_Test_List_CalendarGetterTest extends PHPUnit_Framework_TestCase {
	/**
	 * The calendar class to test
	 * @var CalendarSolution_Test_List_CalendarHelper
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
		$this->calendar = new CalendarSolution_Test_List_CalendarHelper;

		$to = new DateTimeSolution;
		$to->add(new DateIntervalSolution('P2M'));
		$this->to_default = $to->format('Y-m-t');
	}

	protected function get_change_view_expected($head, $view, $tail, $from,
			$to, $uri_head = '?')
	{
		if ($from) {
			$from = 'from=' . $from . '&amp;';
		}
		if ($to) {
			$to = 'to=' . $to . '&amp;';
		}

		return '<div class="cs_change_view">' . $head . '<a href="' . $uri_head
			. $from . $to . 'view=List">' . $view . '</a>'
			. $tail . "</div>\n";
	}


	/**#@+
	 * get_change_view()
	 */
	public function test_get_change_view_default() {
		$actual = $this->calendar->get_change_view();
		$expect = $this->get_change_view_expected('View the events in ',
			'list', ' format', date('Y-m-01'), $this->to_default);
		$this->assertEquals($expect, $actual);
	}
	public function test_get_change_view_from_to_false() {
		$this->calendar->set_from(false);
		$this->calendar->set_to(false);

		$actual = $this->calendar->get_change_view();
		$expect = $this->get_change_view_expected('View the events in ',
			'list', ' format', '', '');
		$this->assertEquals($expect, $actual);
	}
	public function test_get_change_view() {
		$this->calendar->set_from('2001-01-01');
		$this->calendar->set_to('2001-01-31');

		$actual = $this->calendar->get_change_view();
		$expect = $this->get_change_view_expected('View the events in ',
			'list', ' format', '2001-01-01', '2001-01-31');
		$this->assertEquals($expect, $actual);
	}
	public function test_get_change_view_parameters() {
		$this->calendar->set_from('2001-01-01');
		$this->calendar->set_to('2001-01-31');

		$actual = $this->calendar->get_change_view('the %s way', 'LIST', 'CAL');
		$expect = $this->get_change_view_expected('the ',
			'LIST', ' way', '2001-01-01', '2001-01-31');
		$this->assertEquals($expect, $actual);
	}
	public function test_get_change_view_uri() {
		$_SERVER['REQUEST_URI'] = 'p?q=v';
		$this->calendar->set_uri();

		$this->calendar->set_from('2001-01-01');
		$this->calendar->set_to('2001-01-31');

		$actual = $this->calendar->get_change_view();
		$expect = $this->get_change_view_expected('View the events in ',
			'list', ' format', '2001-01-01', '2001-01-31', 'p?q=v&amp;');
		$this->assertEquals($expect, $actual);
	}
	public function test_get_change_properties() {
		$this->calendar->set_category_id(2);
		$this->calendar->set_frequent_event_id(2);

		$this->calendar->set_from('2001-01-01');
		$this->calendar->set_to('2001-01-31');

		$actual = $this->calendar->get_change_view();
		$expect = $this->get_change_view_expected('View the events in ',
			'list', ' format', '2001-01-01', '2001-01-31',
			'?category_id%5B0%5D=2&amp;frequent_event_id=2&amp;');
		$this->assertEquals($expect, $actual);
	}
	/**#@-*/
}
