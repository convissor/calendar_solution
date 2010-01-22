<?php /** @package CalendarSolution_Test */

/**
 * Gather the PHPUnit Framework
 */
require_once 'PHPUnit/Framework.php';

/**
 * Gather the class these tests work on
 */
require_once $GLOBALS['IncludeDir'] . '/CalendarSolution.php';


/**
 * Tests the CalendarSolution_DateTime class
 *
 * Usage:  phpunit DateTimeTest
 *
 * @package CalendarSolution_Test
 */
class DateTimeTest extends PHPUnit_Framework_TestCase {
    /**
     * Provides a consistent interface for executing date diff tests
     */
    protected function calculate_diff($end_date, $start_date) {
        $start = new CalendarSolution_DateTime($start_date);
        $end = new CalendarSolution_DateTime($end_date);
        $interval = $start->diff($end);
        return $interval->format('P%R%yY%mM%dDT%hH%iM%sS');
    }


    /**
     * Invalid date
     */
    public function test_add__bogus_date() {
        $this->setExpectedException('Exception');
        $date = new CalendarSolution_DateTime('2008-33-33');
    }


    /**#@+
     * add() days
     */
    public function test_add__plus_0() {
        $date = new CalendarSolution_DateTime('2008-06-12');
        $interval = new DateInterval('P0D');
        $date->add($interval);
        $this->assertEquals('2008-06-12', $date->format('Y-m-d'));
    }

    public function test_add__plus_1() {
        $date = new CalendarSolution_DateTime('2008-06-12');
        $interval = new DateInterval('P1D');
        $date->add($interval);
        $this->assertEquals('2008-06-13', $date->format('Y-m-d'));
    }

    public function test_add__plus_2() {
        $date = new CalendarSolution_DateTime('2008-06-12');
        $interval = new DateInterval('P2D');
        $date->add($interval);
        $this->assertEquals('2008-06-14', $date->format('Y-m-d'));
    }
    /**#@-*/


    /**#@+
     * add() months
     */
    public function test_add_months__plus_2() {
        $date = new CalendarSolution_DateTime('2008-06-12');
        $interval = new DateInterval('P2M');
        $date->add($interval);
        $this->assertEquals('2008-08-12', $date->format('Y-m-d'));
    }

    public function test_add_months__31_plus_1_to_jun_round_first() {
        $date = new CalendarSolution_DateTime('2008-05-31');
        $interval = new DateInterval('P1M');
        $date->add($interval);
        $this->assertEquals('2008-07-01', $date->format('Y-m-d'));
    }

    public function test_add_months__29_plus_1_to_feb_no_leap_round_first() {
        $date = new CalendarSolution_DateTime('2007-01-29');
        $interval = new DateInterval('P1M');
        $date->add($interval);
        $this->assertEquals('2007-03-01', $date->format('Y-m-d'));
    }

    public function test_add_months__29_plus_1_to_feb_leap_round_first() {
        $date = new CalendarSolution_DateTime('2008-01-29');
        $interval = new DateInterval('P1M');
        $date->add($interval);
        $this->assertEquals('2008-02-29', $date->format('Y-m-d'));
    }

    public function test_add_months__29_plus_1_to_feb_leap_round_last() {
        $date = new CalendarSolution_DateTime('2008-01-29');
        $interval = new DateInterval('P1M');
        $date->add($interval);
        $this->assertEquals('2008-02-29', $date->format('Y-m-d'));
    }

    public function test_add_months__30_plus_1_to_feb_leap_round_first() {
        $date = new CalendarSolution_DateTime('2008-01-30');
        $interval = new DateInterval('P1M');
        $date->add($interval);
        $this->assertEquals('2008-03-01', $date->format('Y-m-d'));
    }
    /**#@-*/


    /**#@+
     * diff()
     */
    public function test_diff__7() {
        $actual = $this->calculate_diff('2009-01-14', '2009-01-07');
        $this->assertEquals('P+0Y0M7DT0H0M0S', $actual);
    }
    public function test_diff_years_positive__7_by_0_day() {
        $actual = $this->calculate_diff('2007-02-07', '2000-02-07');
        $this->assertEquals('P+7Y0M0DT0H0M0S', $actual);
    }
    public function test_diff_years_positive__7_by_1_day() {
        $actual = $this->calculate_diff('2007-02-08', '2000-02-07');
        $this->assertEquals('P+7Y0M1DT0H0M0S', $actual);
    }
    public function test_diff_years_positive__6_shy_1_day() {
        $actual = $this->calculate_diff('2007-02-06', '2000-02-07');
        $this->assertEquals('P+6Y11M28DT0H0M0S', $actual);
    }
    public function test_diff_years_positive__7_by_1_month() {
        $actual = $this->calculate_diff('2007-03-07', '2000-02-07');
        $this->assertEquals('P+7Y1M0DT0H0M0S', $actual);
    }
    public function test_diff_years_positive__6_shy_1_month() {
        $actual = $this->calculate_diff('2007-01-07', '2000-02-07');
        $this->assertEquals('P+6Y11M0DT0H0M0S', $actual);
    }
    public function test_diff_years_positive__7_by_1_month_split_newyear() {
        $actual = $this->calculate_diff('2007-01-07', '1999-12-07');
        $this->assertEquals('P+7Y1M0DT0H0M0S', $actual);
    }
    public function test_diff_years_positive__6_shy_1_month_split_newyear() {
        $actual = $this->calculate_diff('2006-12-07', '2000-01-07');
        $this->assertEquals('P+6Y11M0DT0H0M0S', $actual);
    }
    /**#@-*/


    /**#@+
     * diff() tests from PHP bug 49081
     */
    public function test_diff_bug_49081__1() {
        $actual = $this->calculate_diff('2010-03-31', '2010-03-01');
        $this->assertEquals('P+0Y0M30DT0H0M0S', $actual);
    }
    public function test_diff_bug_49081__2() {
        $actual = $this->calculate_diff('2010-04-01', '2010-03-01');
        $this->assertEquals('P+0Y1M0DT0H0M0S', $actual);
    }
    public function test_diff_bug_49081__3() {
        $actual = $this->calculate_diff('2010-04-01', '2010-03-31');
        $this->assertEquals('P+0Y0M1DT0H0M0S', $actual);
    }
    public function test_diff_bug_49081__4() {
        $actual = $this->calculate_diff('2010-04-29', '2010-03-31');
        $this->assertEquals('P+0Y0M29DT0H0M0S', $actual);
    }
    public function test_diff_bug_49081__5() {
        $actual = $this->calculate_diff('2010-04-30', '2010-03-31');
        $this->assertEquals('P+0Y0M30DT0H0M0S', $actual);
    }
    public function test_diff_bug_49081__6() {
        $actual = $this->calculate_diff('2010-04-30', '2010-03-30');
        $this->assertEquals('P+0Y1M0DT0H0M0S', $actual);
    }
    public function test_diff_bug_49081__7() {
        $actual = $this->calculate_diff('2010-04-30', '2010-03-29');
        $this->assertEquals('P+0Y1M1DT0H0M0S', $actual);
    }
    public function test_diff_bug_49081__8() {
        $actual = $this->calculate_diff('2010-01-29', '2010-01-01');
        $this->assertEquals('P+0Y0M28DT0H0M0S', $actual);
    }
    public function test_diff_bug_49081__9() {
        $actual = $this->calculate_diff('2010-01-30', '2010-01-01');
        $this->assertEquals('P+0Y0M29DT0H0M0S', $actual);
    }
    public function test_diff_bug_49081__10() {
        $actual = $this->calculate_diff('2010-01-31', '2010-01-01');
        $this->assertEquals('P+0Y0M30DT0H0M0S', $actual);
    }
    public function test_diff_bug_49081__11() {
        $actual = $this->calculate_diff('2010-02-01', '2010-01-01');
        $this->assertEquals('P+0Y1M0DT0H0M0S', $actual);
    }
    public function test_diff_bug_49081__12() {
        $actual = $this->calculate_diff('2010-02-01', '2010-01-31');
        $this->assertEquals('P+0Y0M1DT0H0M0S', $actual);
    }
    public function test_diff_bug_49081__13() {
        $actual = $this->calculate_diff('2010-02-27', '2010-01-31');
        $this->assertEquals('P+0Y0M27DT0H0M0S', $actual);
    }
    public function test_diff_bug_49081__14() {
        $actual = $this->calculate_diff('2010-02-28', '2010-01-31');
        $this->assertEquals('P+0Y0M28DT0H0M0S', $actual);
    }
    public function test_diff_bug_49081__15() {
        $actual = $this->calculate_diff('2010-02-28', '2010-01-30');
        $this->assertEquals('P+0Y0M29DT0H0M0S', $actual);
    }
    public function test_diff_bug_49081__16() {
        $actual = $this->calculate_diff('2010-02-28', '2010-01-29');
        $this->assertEquals('P+0Y0M30DT0H0M0S', $actual);
    }
    public function test_diff_bug_49081__17() {
        $actual = $this->calculate_diff('2010-02-28', '2010-01-28');
        $this->assertEquals('P+0Y1M0DT0H0M0S', $actual);
    }
    public function test_diff_bug_49081__18() {
        $actual = $this->calculate_diff('2010-02-28', '2010-01-27');
        $this->assertEquals('P+0Y1M1DT0H0M0S', $actual);
    }
    public function test_diff_bug_49081__19() {
        $actual = $this->calculate_diff('2010-03-01', '2010-01-01');
        $this->assertEquals('P+0Y2M0DT0H0M0S', $actual);
    }
    public function test_diff_bug_49081__20() {
        $actual = $this->calculate_diff('2010-03-01', '2010-01-31');
        $this->assertEquals('P+0Y1M1DT0H0M0S', $actual);
    }
    public function test_diff_bug_49081__21() {
        $actual = $this->calculate_diff('2010-03-27', '2010-01-31');
        $this->assertEquals('P+0Y1M27DT0H0M0S', $actual);
    }
    public function test_diff_bug_49081__22() {
        $actual = $this->calculate_diff('2010-03-28', '2010-01-31');
        $this->assertEquals('P+0Y1M28DT0H0M0S', $actual);
    }
    public function test_diff_bug_49081__23() {
        $actual = $this->calculate_diff('2010-03-29', '2010-01-31');
        $this->assertEquals('P+0Y1M29DT0H0M0S', $actual);
    }
    public function test_diff_bug_49081__24() {
        $actual = $this->calculate_diff('2010-03-30', '2010-01-31');
        $this->assertEquals('P+0Y1M30DT0H0M0S', $actual);
    }
    public function test_diff_bug_49081__25() {
        $actual = $this->calculate_diff('2010-03-31', '2010-01-31');
        $this->assertEquals('P+0Y2M0DT0H0M0S', $actual);
    }
    public function test_diff_bug_49081__26() {
        $actual = $this->calculate_diff('2010-03-31', '2010-01-30');
        $this->assertEquals('P+0Y2M1DT0H0M0S', $actual);
    }
    public function test_diff_bug_49081__27() {
        $actual = $this->calculate_diff('2009-01-31', '2009-01-01');
        $this->assertEquals('P+0Y0M30DT0H0M0S', $actual);
    }
    /**#@-*/

    /**#@+
     * diff() negative
     */
    public function test_diff_negative__7() {
        $actual = $this->calculate_diff('2009-01-07', '2009-01-14');
        $this->assertEquals('P-0Y0M7DT0H0M0S', $actual);
    }
    public function test_diff_years_negative__7_by_0_day() {
        $actual = $this->calculate_diff('2000-02-07', '2007-02-07');
        $this->assertEquals('P-7Y0M0DT0H0M0S', $actual);
    }
    public function test_diff_years_negative__7_by_1_day() {
        $actual = $this->calculate_diff('2000-02-07', '2007-02-08');
        $this->assertEquals('P-7Y0M1DT0H0M0S', $actual);
    }
    public function test_diff_years_negative__6_shy_1_day() {
        $actual = $this->calculate_diff('2000-02-07', '2007-02-06');
        $this->assertEquals('P-6Y11M28DT0H0M0S', $actual);
    }
    public function test_diff_years_negative__7_by_1_month() {
        $actual = $this->calculate_diff('2000-02-07', '2007-03-07');
        $this->assertEquals('P-7Y1M0DT0H0M0S', $actual);
    }
    public function test_diff_years_negative__6_shy_1_month() {
        $actual = $this->calculate_diff('2000-02-07', '2007-01-07');
        $this->assertEquals('P-6Y11M0DT0H0M0S', $actual);
    }
    public function test_diff_years_negative__7_by_1_month_split_newyear() {
        $actual = $this->calculate_diff('1999-12-07', '2007-01-07');
        $this->assertEquals('P-7Y1M0DT0H0M0S', $actual);
    }
    public function test_diff_years_negative__6_shy_1_month_split_newyear() {
        $actual = $this->calculate_diff('2000-01-07', '2006-12-07');
        $this->assertEquals('P-6Y11M0DT0H0M0S', $actual);
    }
    /**#@-*/

    /**#@+
     * diff() negative tests based on stuff from PHP bug 49081
     */
    public function test_diff_bug_49081_negative__1() {
        $actual = $this->calculate_diff('2010-03-01', '2010-03-31');
        $this->assertEquals('P-0Y0M30DT0H0M0S', $actual);
    }
    public function test_diff_bug_49081_negative__2() {
        $actual = $this->calculate_diff('2010-03-01', '2010-04-01');
        $this->assertEquals('P-0Y1M0DT0H0M0S', $actual);
    }
    public function test_diff_bug_49081_negative__3() {
        $actual = $this->calculate_diff('2010-03-31', '2010-04-01');
        $this->assertEquals('P-0Y0M1DT0H0M0S', $actual);
    }
    public function test_diff_bug_49081_negative__4() {
        $actual = $this->calculate_diff('2010-03-31', '2010-04-29');
        $this->assertEquals('P-0Y0M29DT0H0M0S', $actual);
    }
    public function test_diff_bug_49081_negative__5() {
        $actual = $this->calculate_diff('2010-03-31', '2010-04-30');
        $this->assertEquals('P-0Y0M30DT0H0M0S', $actual);
    }
    public function test_diff_bug_49081_negative__6() {
        $actual = $this->calculate_diff('2010-03-30', '2010-04-30');
        $this->assertEquals('P-0Y1M0DT0H0M0S', $actual);
    }
    public function test_diff_bug_49081_negative__7() {
        $actual = $this->calculate_diff('2010-03-29', '2010-04-30');
        $this->assertEquals('P-0Y1M1DT0H0M0S', $actual);
    }
    public function test_diff_bug_49081_negative__8() {
        $actual = $this->calculate_diff('2010-01-01', '2010-01-29');
        $this->assertEquals('P-0Y0M28DT0H0M0S', $actual);
    }
    public function test_diff_bug_49081_negative__9() {
        $actual = $this->calculate_diff('2010-01-01', '2010-01-30');
        $this->assertEquals('P-0Y0M29DT0H0M0S', $actual);
    }
    public function test_diff_bug_49081_negative__10() {
        $actual = $this->calculate_diff('2010-01-01', '2010-01-31');
        $this->assertEquals('P-0Y0M30DT0H0M0S', $actual);
    }
    public function test_diff_bug_49081_negative__11() {
        $actual = $this->calculate_diff('2010-01-01', '2010-02-01');
        $this->assertEquals('P-0Y1M0DT0H0M0S', $actual);
    }
    public function test_diff_bug_49081_negative__12() {
        $actual = $this->calculate_diff('2010-01-31', '2010-02-01');
        $this->assertEquals('P-0Y0M1DT0H0M0S', $actual);
    }
    public function test_diff_bug_49081_negative__13() {
        $actual = $this->calculate_diff('2010-01-31', '2010-02-27');
        $this->assertEquals('P-0Y0M27DT0H0M0S', $actual);
    }
    public function test_diff_bug_49081_negative__14() {
        $actual = $this->calculate_diff('2010-01-31', '2010-02-28');
        $this->assertEquals('P-0Y0M28DT0H0M0S', $actual);
    }
    public function test_diff_bug_49081_negative__15() {
        $actual = $this->calculate_diff('2010-01-30', '2010-02-28');
        $this->assertEquals('P-0Y0M29DT0H0M0S', $actual);
    }
    public function test_diff_bug_49081_negative__16() {
        $actual = $this->calculate_diff('2010-01-29', '2010-02-28');
        $this->assertEquals('P-0Y0M30DT0H0M0S', $actual);
    }
    public function test_diff_bug_49081_negative__17() {
        $actual = $this->calculate_diff('2010-01-28', '2010-02-28');
        $this->assertEquals('P-0Y1M0DT0H0M0S', $actual);
    }
    public function test_diff_bug_49081_negative__18() {
        $actual = $this->calculate_diff('2010-01-27', '2010-02-28');
        $this->assertEquals('P-0Y1M1DT0H0M0S', $actual);
    }
    public function test_diff_bug_49081_negative__19() {
        $actual = $this->calculate_diff('2010-01-01', '2010-03-01');
        $this->assertEquals('P-0Y2M0DT0H0M0S', $actual);
    }
    public function test_diff_bug_49081_negative__20() {
        $actual = $this->calculate_diff('2010-01-31', '2010-03-01');
        $this->assertEquals('P-0Y1M1DT0H0M0S', $actual);
    }
    public function test_diff_bug_49081_negative__21() {
        $actual = $this->calculate_diff('2010-01-31', '2010-03-27');
        $this->assertEquals('P-0Y1M27DT0H0M0S', $actual);
    }
    public function test_diff_bug_49081_negative__22() {
        $actual = $this->calculate_diff('2010-01-31', '2010-03-28');
        $this->assertEquals('P-0Y1M28DT0H0M0S', $actual);
    }
    public function test_diff_bug_49081_negative__23() {
        $actual = $this->calculate_diff('2010-01-31', '2010-03-29');
        $this->assertEquals('P-0Y1M29DT0H0M0S', $actual);
    }
    public function test_diff_bug_49081_negative__24() {
        $actual = $this->calculate_diff('2010-01-31', '2010-03-30');
        $this->assertEquals('P-0Y1M30DT0H0M0S', $actual);
    }
    public function test_diff_bug_49081_negative__25() {
        $actual = $this->calculate_diff('2010-01-31', '2010-03-31');
        $this->assertEquals('P-0Y2M0DT0H0M0S', $actual);
    }
    public function test_diff_bug_49081_negative__26() {
        $actual = $this->calculate_diff('2010-01-30', '2010-03-31');
        $this->assertEquals('P-0Y2M1DT0H0M0S', $actual);
    }
    public function test_diff_bug_49081_negative__27() {
        $actual = $this->calculate_diff('2009-01-01', '2009-01-31');
        $this->assertEquals('P-0Y0M30DT0H0M0S', $actual);
    }

    public function test_diff_bug_49081_negative__28() {
        $actual = $this->calculate_diff('2010-02-28', '2010-03-27');
        $this->assertEquals('P-0Y0M27DT0H0M0S', $actual);
    }
    public function test_diff_bug_49081_negative__29() {
        $actual = $this->calculate_diff('2010-02-28', '2010-03-28');
        $this->assertEquals('P-0Y1M0DT0H0M0S', $actual);
    }
    public function test_diff_bug_49081_negative__30() {
        $actual = $this->calculate_diff('2010-02-28', '2010-03-29');
        $this->assertEquals('P-0Y1M1DT0H0M0S', $actual);
    }
    public function test_diff_bug_49081_negative__31() {
        $actual = $this->calculate_diff('2010-02-27', '2010-03-27');
        $this->assertEquals('P-0Y1M0DT0H0M0S', $actual);
    }
    public function test_diff_bug_49081_negative__32() {
        $actual = $this->calculate_diff('2010-02-26', '2010-03-27');
        $this->assertEquals('P-0Y1M1DT0H0M0S', $actual);
    }
    /**#@-*/
}
