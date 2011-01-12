<?php /** @package CalendarSolution_Test */

/**
 * Tests the CalendarSolution_Detail_Form class
 *
 * Usage:  phpunit Detail_FormTest
 *
 * @package CalendarSolution_Test
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2002-2011
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 */
class CalendarSolution_Test_Detail_FormTest extends PHPUnit_Framework_TestCase {
	/**
	 * The calendar class to test
	 * @var CalendarSolution_Test_Detail_FormHelper
	 */
	protected $calendar;

	/**
	 * Prepares the environment before running each test
	 */
	protected function setUp() {
		$this->calendar = new CalendarSolution_Test_Detail_FormHelper;
	}


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

		$this->calendar->set_data_from_post();
		$actual = $this->calendar->get_date_starts();

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

		$this->calendar->set_data_from_post();
		$actual = $this->calendar->get_date_starts();
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

		$this->calendar->set_data_from_post();
		$actual = $this->calendar->get_date_starts();
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

		$this->calendar->set_data_from_post();
		$actual = $this->calendar->get_date_starts();
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

		$this->calendar->set_data_from_post();
		$actual = $this->calendar->get_date_starts();
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

		$this->calendar->set_data_from_post();
		$actual = $this->calendar->get_date_starts();
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

		$this->calendar->set_data_from_post();
		$actual = $this->calendar->get_date_starts();
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

		$this->calendar->set_data_from_post();
		$actual = $this->calendar->get_date_starts();

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

		$this->calendar->set_data_from_post();
		$actual = $this->calendar->get_date_starts();
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

		$this->calendar->set_data_from_post();
		$actual = $this->calendar->get_date_starts();
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

		$this->calendar->set_data_from_post();
		$actual = $this->calendar->get_date_starts();
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

		$this->calendar->set_data_from_post();
		$actual = $this->calendar->get_date_starts();
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

		$this->calendar->set_data_from_post();
		$actual = $this->calendar->get_date_starts();
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

		$this->calendar->set_data_from_post();
		$actual = $this->calendar->get_date_starts();
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

		$this->calendar->set_data_from_post();
		$actual = $this->calendar->get_date_starts();

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

		$this->calendar->set_data_from_post();
		$actual = $this->calendar->get_date_starts();
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

		$this->calendar->set_data_from_post();
		$actual = $this->calendar->get_date_starts();
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

		$this->calendar->set_data_from_post();
		$actual = $this->calendar->get_date_starts();
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

		$this->calendar->set_data_from_post();
		$actual = $this->calendar->get_date_starts();
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

		$this->calendar->set_data_from_post();
		$actual = $this->calendar->get_date_starts();
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

		$this->calendar->set_data_from_post();
		$actual = $this->calendar->get_date_starts();
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

		$this->calendar->set_data_from_post();
		$actual = $this->calendar->get_date_starts();

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

		$this->calendar->set_data_from_post();
		$actual = $this->calendar->get_date_starts();
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

		$this->calendar->set_data_from_post();
		$actual = $this->calendar->get_date_starts();
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

		$this->calendar->set_data_from_post();
		$actual = $this->calendar->get_date_starts();
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

		$this->calendar->set_data_from_post();
		$actual = $this->calendar->get_date_starts();
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

		$this->calendar->set_data_from_post();
		$actual = $this->calendar->get_date_starts();
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

		$this->calendar->set_data_from_post();
		$actual = $this->calendar->get_date_starts();
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

		$this->calendar->set_data_from_post();
		$actual = $this->calendar->get_date_starts();

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

		$this->calendar->set_data_from_post();
		$actual = $this->calendar->get_date_starts();
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

		$this->calendar->set_data_from_post();
		$actual = $this->calendar->get_date_starts();
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

		$this->calendar->set_data_from_post();
		$actual = $this->calendar->get_date_starts();
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

		$this->calendar->set_data_from_post();
		$actual = $this->calendar->get_date_starts();
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

		$this->calendar->set_data_from_post();
		$actual = $this->calendar->get_date_starts();
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

		$this->calendar->set_data_from_post();
		$actual = $this->calendar->get_date_starts();
		$this->assertEquals($expected, $actual);
	}
	/**#@-*/
}
