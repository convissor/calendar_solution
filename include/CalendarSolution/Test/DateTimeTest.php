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
    protected function examine_diff($end_date, $start_date, $expect) {
        $start = new CalendarSolution_DateTime($start_date);
        $end = new CalendarSolution_DateTime($end_date);
        $interval = $start->diff($end);
        $result = $interval->format('P%R%yY%mM%dDT%hH%iM%sS');

        // Also make sure add()/sub() works the same way as diff().
        $end_date_from_expect = $this->arithmatic_from_interval($start_date, $expect);
        $end_date_from_result = $this->arithmatic_from_interval($start_date, $result);

        $expect_full = "$end_date - $start_date = $expect. "
            . "$start_date + $expect = $end_date_from_expect";
        $result_full = "$end_date - $start_date = $result. "
            . "$start_date + $result = $end_date_from_result";

        $this->assertEquals($expect_full, $result_full);
    }

    /**
     * Provides a consistent interface for addition or subtraction
     * using our interval format
     */
    protected function arithmatic_from_interval($start_date, $our_interval_spec) {
        $start = new CalendarSolution_DateTime($start_date);
        preg_match('/^P([+-])(.+)$/', $our_interval_spec, $atom);
        $interval = new DateInterval('P' . $atom[2]);
        if ($atom[1] == '+') {
            $date = $start->add($interval);
        } else {
            $date = $start->sub($interval);
        }
        return $date->format('Y-m-d');
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
        $this->examine_diff('2009-01-14', '2009-01-07', 'P+0Y0M7DT0H0M0S');
    }
    public function test_diff_years_positive__7_by_0_day() {
        $this->examine_diff('2007-02-07', '2000-02-07', 'P+7Y0M0DT0H0M0S');
    }
    public function test_diff_years_positive__7_by_1_day() {
        $this->examine_diff('2007-02-08', '2000-02-07', 'P+7Y0M1DT0H0M0S');
    }
    public function test_diff_years_positive__6_shy_1_day() {
        $this->examine_diff('2007-02-06', '2000-02-07', 'P+6Y11M28DT0H0M0S');
    }
    public function test_diff_years_positive__7_by_1_month() {
        $this->examine_diff('2007-03-07', '2000-02-07', 'P+7Y1M0DT0H0M0S');
    }
    public function test_diff_years_positive__6_shy_1_month() {
        $this->examine_diff('2007-01-07', '2000-02-07', 'P+6Y11M0DT0H0M0S');
    }
    public function test_diff_years_positive__7_by_1_month_split_newyear() {
        $this->examine_diff('2007-01-07', '1999-12-07', 'P+7Y1M0DT0H0M0S');
    }
    public function test_diff_years_positive__6_shy_1_month_split_newyear() {
        $this->examine_diff('2006-12-07', '2000-01-07', 'P+6Y11M0DT0H0M0S');
    }
    /**#@-*/


    /**#@+
     * diff() tests from PHP bug 49081
     */
    public function test_diff_bug_49081__1() {
        $this->examine_diff('2010-03-31', '2010-03-01', 'P+0Y0M30DT0H0M0S');
    }
    public function test_diff_bug_49081__2() {
        $this->examine_diff('2010-04-01', '2010-03-01', 'P+0Y1M0DT0H0M0S');
    }
    public function test_diff_bug_49081__3() {
        $this->examine_diff('2010-04-01', '2010-03-31', 'P+0Y0M1DT0H0M0S');
    }
    public function test_diff_bug_49081__4() {
        $this->examine_diff('2010-04-29', '2010-03-31', 'P+0Y0M29DT0H0M0S');
    }
    public function test_diff_bug_49081__5() {
        $this->examine_diff('2010-04-30', '2010-03-31', 'P+0Y0M30DT0H0M0S');
    }
    public function test_diff_bug_49081__6() {
        $this->examine_diff('2010-04-30', '2010-03-30', 'P+0Y1M0DT0H0M0S');
    }
    public function test_diff_bug_49081__7() {
        $this->examine_diff('2010-04-30', '2010-03-29', 'P+0Y1M1DT0H0M0S');
    }
    public function test_diff_bug_49081__8() {
        $this->examine_diff('2010-01-29', '2010-01-01', 'P+0Y0M28DT0H0M0S');
    }
    public function test_diff_bug_49081__9() {
        $this->examine_diff('2010-01-30', '2010-01-01', 'P+0Y0M29DT0H0M0S');
    }
    public function test_diff_bug_49081__10() {
        $this->examine_diff('2010-01-31', '2010-01-01', 'P+0Y0M30DT0H0M0S');
    }
    public function test_diff_bug_49081__11() {
        $this->examine_diff('2010-02-01', '2010-01-01', 'P+0Y1M0DT0H0M0S');
    }
    public function test_diff_bug_49081__12() {
        $this->examine_diff('2010-02-01', '2010-01-31', 'P+0Y0M1DT0H0M0S');
    }
    public function test_diff_bug_49081__13() {
        $this->examine_diff('2010-02-27', '2010-01-31', 'P+0Y0M27DT0H0M0S');
    }
    public function test_diff_bug_49081__14() {
        $this->examine_diff('2010-02-28', '2010-01-31', 'P+0Y0M28DT0H0M0S');
    }
    public function test_diff_bug_49081__15() {
        $this->examine_diff('2010-02-28', '2010-01-30', 'P+0Y0M29DT0H0M0S');
    }
    public function test_diff_bug_49081__16() {
        $this->examine_diff('2010-02-28', '2010-01-29', 'P+0Y0M30DT0H0M0S');
    }
    public function test_diff_bug_49081__17() {
        $this->examine_diff('2010-02-28', '2010-01-28', 'P+0Y1M0DT0H0M0S');
    }
    public function test_diff_bug_49081__18() {
        $this->examine_diff('2010-02-28', '2010-01-27', 'P+0Y1M1DT0H0M0S');
    }
    public function test_diff_bug_49081__19() {
        $this->examine_diff('2010-03-01', '2010-01-01', 'P+0Y2M0DT0H0M0S');
    }
    public function test_diff_bug_49081__20() {
        $this->examine_diff('2010-03-01', '2010-01-31', 'P+0Y1M1DT0H0M0S');
    }
    public function test_diff_bug_49081__21() {
        $this->examine_diff('2010-03-27', '2010-01-31', 'P+0Y1M27DT0H0M0S');
    }
    public function test_diff_bug_49081__22() {
        $this->examine_diff('2010-03-28', '2010-01-31', 'P+0Y1M28DT0H0M0S');
    }
    public function test_diff_bug_49081__23() {
        $this->examine_diff('2010-03-29', '2010-01-31', 'P+0Y1M29DT0H0M0S');
    }
    public function test_diff_bug_49081__24() {
        $this->examine_diff('2010-03-30', '2010-01-31', 'P+0Y1M30DT0H0M0S');
    }
    public function test_diff_bug_49081__25() {
        $this->examine_diff('2010-03-31', '2010-01-31', 'P+0Y2M0DT0H0M0S');
    }
    public function test_diff_bug_49081__26() {
        $this->examine_diff('2010-03-31', '2010-01-30', 'P+0Y2M1DT0H0M0S');
    }
    public function test_diff_bug_49081__27() {
        $this->examine_diff('2009-01-31', '2009-01-01', 'P+0Y0M30DT0H0M0S');
    }
    /**#@-*/

    /**#@+
     * diff() negative
     */
    public function test_diff_negative__7() {
        $this->examine_diff('2009-01-07', '2009-01-14', 'P-0Y0M7DT0H0M0S');
    }
    public function test_diff_years_negative__7_by_0_day() {
        $this->examine_diff('2000-02-07', '2007-02-07', 'P-7Y0M0DT0H0M0S');
    }
    public function test_diff_years_negative__7_by_1_day() {
        $this->examine_diff('2000-02-07', '2007-02-08', 'P-7Y0M1DT0H0M0S');
    }
    public function test_diff_years_negative__6_shy_1_day() {
        $this->examine_diff('2000-02-07', '2007-02-06', 'P-6Y11M28DT0H0M0S');
    }
    public function test_diff_years_negative__7_by_1_month() {
        $this->examine_diff('2000-02-07', '2007-03-07', 'P-7Y1M0DT0H0M0S');
    }
    public function test_diff_years_negative__6_shy_1_month() {
        $this->examine_diff('2000-02-07', '2007-01-07', 'P-6Y11M0DT0H0M0S');
    }
    public function test_diff_years_negative__7_by_1_month_split_newyear() {
        $this->examine_diff('1999-12-07', '2007-01-07', 'P-7Y1M0DT0H0M0S');
    }
    public function test_diff_years_negative__6_shy_1_month_split_newyear() {
        $this->examine_diff('2000-01-07', '2006-12-07', 'P-6Y11M0DT0H0M0S');
    }
    /**#@-*/

    /**#@+
     * diff() negative tests based on stuff from PHP bug 49081
     */
    public function test_diff_bug_49081_negative__1() {
        $this->examine_diff('2010-03-01', '2010-03-31', 'P-0Y0M30DT0H0M0S');
    }
    public function test_diff_bug_49081_negative__2() {
        $this->examine_diff('2010-03-01', '2010-04-01', 'P-0Y1M0DT0H0M0S');
    }
    public function test_diff_bug_49081_negative__3() {
        $this->examine_diff('2010-03-31', '2010-04-01', 'P-0Y0M1DT0H0M0S');
    }
    public function test_diff_bug_49081_negative__4() {
        $this->examine_diff('2010-03-31', '2010-04-29', 'P-0Y0M29DT0H0M0S');
    }
    public function test_diff_bug_49081_negative__5() {
        $this->examine_diff('2010-03-31', '2010-04-30', 'P-0Y0M30DT0H0M0S');
    }
    public function test_diff_bug_49081_negative__6() {
        $this->examine_diff('2010-03-30', '2010-04-30', 'P-0Y1M0DT0H0M0S');
    }
    public function test_diff_bug_49081_negative__7() {
        $this->examine_diff('2010-03-29', '2010-04-30', 'P-0Y1M1DT0H0M0S');
    }
    public function test_diff_bug_49081_negative__8() {
        $this->examine_diff('2010-01-01', '2010-01-29', 'P-0Y0M28DT0H0M0S');
    }
    public function test_diff_bug_49081_negative__9() {
        $this->examine_diff('2010-01-01', '2010-01-30', 'P-0Y0M29DT0H0M0S');
    }
    public function test_diff_bug_49081_negative__10() {
        $this->examine_diff('2010-01-01', '2010-01-31', 'P-0Y0M30DT0H0M0S');
    }
    public function test_diff_bug_49081_negative__11() {
        $this->examine_diff('2010-01-01', '2010-02-01', 'P-0Y1M0DT0H0M0S');
    }
    public function test_diff_bug_49081_negative__12() {
        $this->examine_diff('2010-01-31', '2010-02-01', 'P-0Y0M1DT0H0M0S');
    }
    public function test_diff_bug_49081_negative__13() {
        $this->examine_diff('2010-01-31', '2010-02-27', 'P-0Y0M27DT0H0M0S');
    }
    public function test_diff_bug_49081_negative__14() {
        $this->examine_diff('2010-01-31', '2010-02-28', 'P-0Y0M28DT0H0M0S');
    }
    public function test_diff_bug_49081_negative__15() {
        $this->examine_diff('2010-01-30', '2010-02-28', 'P-0Y0M29DT0H0M0S');
    }
    public function test_diff_bug_49081_negative__16() {
        $this->examine_diff('2010-01-29', '2010-02-28', 'P-0Y0M30DT0H0M0S');
    }
    public function test_diff_bug_49081_negative__17() {
        $this->examine_diff('2010-01-28', '2010-02-28', 'P-0Y1M0DT0H0M0S');
    }
    public function test_diff_bug_49081_negative__18() {
        $this->examine_diff('2010-01-27', '2010-02-28', 'P-0Y1M1DT0H0M0S');
    }
    public function test_diff_bug_49081_negative__19() {
        $this->examine_diff('2010-01-01', '2010-03-01', 'P-0Y2M0DT0H0M0S');
    }
    public function test_diff_bug_49081_negative__20() {
        $this->examine_diff('2010-01-31', '2010-03-01', 'P-0Y1M1DT0H0M0S');
    }
    public function test_diff_bug_49081_negative__21() {
        $this->examine_diff('2010-01-31', '2010-03-27', 'P-0Y1M27DT0H0M0S');
    }
    public function test_diff_bug_49081_negative__22() {
        $this->examine_diff('2010-01-31', '2010-03-28', 'P-0Y1M28DT0H0M0S');
    }
    public function test_diff_bug_49081_negative__23() {
        $this->examine_diff('2010-01-31', '2010-03-29', 'P-0Y1M29DT0H0M0S');
    }
    public function test_diff_bug_49081_negative__24() {
        $this->examine_diff('2010-01-31', '2010-03-30', 'P-0Y1M30DT0H0M0S');
    }
    public function test_diff_bug_49081_negative__25() {
        $this->examine_diff('2010-01-31', '2010-03-31', 'P-0Y2M0DT0H0M0S');
    }
    public function test_diff_bug_49081_negative__26() {
        $this->examine_diff('2010-01-30', '2010-03-31', 'P-0Y2M1DT0H0M0S');
    }
    public function test_diff_bug_49081_negative__27() {
        $this->examine_diff('2009-01-01', '2009-01-31', 'P-0Y0M30DT0H0M0S');
    }

    public function test_diff_bug_49081_negative__28() {
        $this->examine_diff('2010-02-28', '2010-03-27', 'P-0Y0M27DT0H0M0S');
    }
    public function test_diff_bug_49081_negative__29() {
        $this->examine_diff('2010-02-28', '2010-03-28', 'P-0Y1M0DT0H0M0S');
    }
    public function test_diff_bug_49081_negative__30() {
        $this->examine_diff('2010-02-28', '2010-03-29', 'P-0Y1M1DT0H0M0S');
    }
    public function test_diff_bug_49081_negative__31() {
        $this->examine_diff('2010-02-27', '2010-03-27', 'P-0Y1M0DT0H0M0S');
    }
    public function test_diff_bug_49081_negative__32() {
        $this->examine_diff('2010-02-26', '2010-03-27', 'P-0Y1M1DT0H0M0S');
    }
    /**#@-*/
}
