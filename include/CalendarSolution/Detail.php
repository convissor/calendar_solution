<?php

/**
 * Calendar Solution's parent class for viewing and editing a specific event
 *
 * @package CalendarSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2002-2010
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 */

/**
 * The parent class for viewing and editing a specific event
 *
 * @package CalendarSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2002-2010
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 */
abstract class CalendarSolution_Detail extends CalendarSolution {
    /**
     * An associative array of the given event's data
     * @var array
     */
    protected $data = array();

    /**
     * The names of fields on the form
     * @var array
     */
    protected $fields = array(
        'calendar_id',
        'calendar_uri',
        'changed',
        'date_start',
        'detail',
        'feature_on_page_id',
        'frequency',
        'frequent_event_id',
        'frequent_event_uri',
        'list_link_goes_to_id',
        'location_start',
        'note',
        'span',
        'status_id',
        'summary',
        'time_end',
        'time_start',
        'title',
        'week_of_month',
    );

    /**
     * The names of fields on the form that are bitwise in the database
     * @var array
     */
    protected $fields_bitwise = array(
        'feature_on_page_id',
    );


    /**
     * Converts a bitwise number into an array of values that are a power of 2
     *
     * @param int $in  the number to convert
     *
     * @return array  the array of converted information
     */
    protected function get_array_from_bitwise($in) {
        if (is_array($in)) {
            return $in;
        }

        $in = (int) $in;
        $out = array();
        for ($i = 1; $i <= $in; $i *= 2) {
            if ($in & $i) {
                $out[] = $i;
            }
        }
        return $out;
    }

    /**
     * Converts array values to a bitwise value
     *
     * @param array $in  the array of integers to convert
     *
     * @return int  the sum of the individual values
     */
    protected function get_bitwise_from_array($in) {
        if (!is_array($in)) {
            return null;
        }
        return array_sum($in);
    }

    /**
     * Provides the Cascading Style Sheet data, for use between <style> tags
     *
     * @return string  the CSS
     *
     * @uses CalendarSolution_Detail_Form::get_css_name()  to know where the CSS is
     * @uses CalendarSolution_Detail_Html::get_css_name()  to know where the CSS is
     */
    public function get_css() {
        return file_get_contents($this->get_css_name());
    }

    /**
     * Assembles the query string, executes it and stores the result in
     * $this->data
     *
     * @param int $calendar_id  the id number of the item to get (defaults to
     *                          $_REQUEST['calendar_id'])
     * @return void
     *
     * @uses SQLSolution_General::RunQuery()  to access the database
     * @uses CalendarSolution::get_int_from_request()  to determine the
     *       user's intention
     *
     * @throws CalendarSolution_Exception on an invalid $calendar_id or if
     *         no matching record is found
     */
    protected function run_query($calendar_id = null, $safe_markup = true) {
        if ($calendar_id === null) {
            $calendar_id = $this->get_int_from_request('calendar_id');
        }

        if (empty($calendar_id)
            || !preg_match('/^\d{1,10}$/', $calendar_id))
        {
            throw new CalendarSolution_Exception('Invalid $calendar_id');
        }

        if (!$safe_markup) {
            $this->sql->SQLSafeMarkup = 'N';
        }

        $this->sql->SQLQueryString = "SELECT
            calendar_id,
            calendar_uri,
            changed,
            date_start,
            detail,
            COALESCE(calendar_uri, frequent_event_uri) AS display_uri,
            feature_on_page_id,
            cs_calendar.frequent_event_id AS frequent_event_id,
            frequent_event_uri,
            list_link_goes_to_id,
            location_start,
            note,
            status,
            cs_calendar.status_id,
            summary,
            time_end,
            time_start,
            title
            FROM cs_calendar
            LEFT JOIN cs_frequent_event USING (frequent_event_id)
            LEFT JOIN cs_status
                ON (cs_status.status_id = cs_calendar.status_id)
            WHERE calendar_id = $calendar_id";

        $this->sql->RunQuery(__FILE__, __LINE__);

        if ($this->sql->SQLRecordSetRowCount == 0) {
            throw new CalendarSolution_Exception('No events match your criteria');
        }
    }

    /**
     * Populates $this->data with a record from the database
     *
     * @param int $calendar_id  the id number of the item to get
     *
     * @return void
     *
     * @uses CalendarSolution_Detail::$data  to hold the data
     * @uses CalendarSolution_Detail::$fields  as the list of field names
     */
    public function set_data_from_query($calendar_id = null, $safe_markup = true) {
        $this->run_query($calendar_id, $safe_markup);
        $this->data = $this->sql->RecordAsAssocArray(__FILE__, __LINE__,
            array('display_uri'));
        $this->data['set_from'] = 'query';
    }
}
