<?php /** @package CalendarSolution_Test */

/**
 * Tests the CalendarSolution_List class' getter methods
 *
 * Usage:  phpunit List_ListGetterTest
 *
 * @package CalendarSolution_Test
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2002-2011
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 */
class CalendarSolution_Test_List_ListGetterTest extends PHPUnit_Framework_TestCase {
	/**
	 * The calendar class to test
	 * @var CalendarSolution_Test_List_ListHelper
	 */
	protected $calendar;

	/**
	 * Prepares the environment before running each test
	 */
	protected function setUp() {
		$this->calendar = new CalendarSolution_Test_List_ListHelper;
		$this->calendar->total_rows = 33;
	}

	protected function get_date_navigation_expected($prior_from, $prior_to,
			$next_from, $next_to, $uri = '?view=List&amp;')
	{
		$prior_link = '';
		if ($prior_from) {
			$prior_link = '<a href="' . $uri . 'from=' . $prior_from
				. '&amp;to=' . $prior_to . '">&lt; See Earlier Events</a>';
		}

		$next_link = '';
		if ($next_from) {
			$next_link = '<a href="' . $uri . 'from=' . $next_from
				. '&amp;to=' . $next_to . '">See Later Events &gt;</a>';
		}

		return '<div class="cs_date_navigation"><div class="cs_prior">'
			. $prior_link
			. '</div><div class="cs_next">' . $next_link . "</div></div>\n";
	}

	protected function get_limit_navigation_expected($prior, $next, $uri = '?') {
		$prior_link = '';
		if (is_numeric($prior)) {
			$prior_link = '<a href="' . $uri . 'limit_start=' . $prior . '">'
				. '&lt; prior</a>';
		}

		$next_link = '';
		if ($next) {
			$next_link = '<a href="' . $uri . 'limit_start=' . $next . '">'
				. 'next &gt;</a>';
		}

		return '<div class="cs_limit_navigation"><div class="cs_prior">'
			. $prior_link
			. '</div><div class="cs_next">' . $next_link . "</div></div>\n";
	}

	/**#@+
	 * get_date_navigation()
	 */
	public function test_get_date_navigation_from_false() {
		$this->calendar->set_from(false);
		$actual = $this->calendar->get_date_navigation();
		$expect = '';
		$this->assertEquals($expect, $actual);
	}
	public function test_get_date_navigation_to_false() {
		$this->calendar->set_to(false);
		$actual = $this->calendar->get_date_navigation();
		$expect = '';
		$this->assertEquals($expect, $actual);
	}
	public function test_get_date_navigation() {
		$this->calendar->set_from('2011-02-01');
		$this->calendar->set_to('2011-04-30');
		$actual = $this->calendar->get_date_navigation();
		$expect = $this->get_date_navigation_expected(
				'2010-11-01', '2011-01-31', '2011-05-01', '2011-07-31');
		$this->assertEquals($expect, $actual);
	}
	public function test_get_date_navigation_uri() {
		$_SERVER['REQUEST_URI'] = 'p?q=v';
		$this->calendar->set_uri();

		$this->calendar->set_from('2011-02-01');
		$this->calendar->set_to('2011-04-30');
		$actual = $this->calendar->get_date_navigation();
		$expect = $this->get_date_navigation_expected(
				'2010-11-01', '2011-01-31', '2011-05-01', '2011-07-31',
				'p?q=v&amp;view=List&amp;');
		$this->assertEquals($expect, $actual);
	}
	public function test_get_date_navigation_event() {
		$this->calendar->set_frequent_event_id(2);

		$this->calendar->set_from('2011-02-01');
		$this->calendar->set_to('2011-04-30');
		$actual = $this->calendar->get_date_navigation();
		$expect = $this->get_date_navigation_expected(
				'2010-11-01', '2011-01-31', '2011-05-01', '2011-07-31',
				'?frequent_event_id=2&amp;view=List&amp;');
		$this->assertEquals($expect, $actual);
	}
	/**#@-*/

	/**#@+
	 * get_limit_navigation()
	 */
	public function test_get_limit_navigation_start_none() {
		$this->calendar->set_limit(10);
		$actual = $this->calendar->get_limit_navigation();
		$expect = '';
		$this->assertEquals($expect, $actual);
	}
	public function test_get_limit_navigation_start_false() {
		$this->calendar->set_limit(10, false);
		$actual = $this->calendar->get_limit_navigation();
		$expect = '';
		$this->assertEquals($expect, $actual);
	}
	public function test_get_limit_navigation_start_0() {
		$this->calendar->set_limit(10, 0);
		$actual = $this->calendar->get_limit_navigation();
		$expect = $this->get_limit_navigation_expected('', 10);
		$this->assertEquals($expect, $actual);
	}
	public function test_get_limit_navigation_start_10_uri() {
		$_SERVER['REQUEST_URI'] = 'p?q=v';
		$this->calendar->set_uri();

		$this->calendar->set_limit(10, 10);
		$actual = $this->calendar->get_limit_navigation();
		$expect = $this->get_limit_navigation_expected(0, 20, 'p?q=v&amp;');
		$this->assertEquals($expect, $actual);
	}
	public function test_get_limit_navigation_start_3() {
		$this->calendar->set_limit(10, 3);
		$actual = $this->calendar->get_limit_navigation();
		$expect = $this->get_limit_navigation_expected(0, 13);
		$this->assertEquals($expect, $actual);
	}
	public function test_get_limit_navigation_start_23() {
		$this->calendar->set_limit(10, 23);
		$actual = $this->calendar->get_limit_navigation();
		$expect = $this->get_limit_navigation_expected(13, false);
		$this->assertEquals($expect, $actual);
	}
	/**#@-*/
}
