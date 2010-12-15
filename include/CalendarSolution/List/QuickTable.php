<?php

/**
 * Calendar Solution's means to output collections of events formatted as a
 * table with brief info about each entry
 *
 * Intended for use on pages that provide comprehensive information about
 * a particular Frequent Event.
 *
 * @package CalendarSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2002-2010
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 */

/**
 * The means to output collections of events formatted as a table with
 * brief info about each entry
 *
 * Intended for use on pages that provide comprehensive information about
 * a particular Frequent Event.
 *
 * @package CalendarSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2002-2010
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 */
class CalendarSolution_List_QuickTable extends CalendarSolution_List {
    /**
     * The type of view this class represents
     * @var string
     */
    protected $view = 'QuickTable';

    /**
     * Should the Summary field be shown or not?
     * @var bool
     */
    protected $show_summary = true;


    /**
     * Provides the path and name of the needed Cascading Style Sheet file
     *
     * @return string  the path and name of the CSS file
     */
    public function get_css_name() {
        return dirname(__FILE__) . '/QuickTable.css';
    }

    /**
     * @param array $event  an associative array of a given event
     *
     * @return string  the HTML for one event
     */
    protected function get_event_formatted($event) {
        /*
         * NOTE: SQL Solution runs the output through htmlspecialchars(),
         * so there is no need to do it here.
         */

        $out = '  <td class="day">'
             . $this->format_date($event['date_start'], self::DATE_FORMAT_MEDIUM)
             . "</td>\n";

        $out .= '  <td class="time">'
             . (($event['time_start']) ? $this->format_date($event['time_start'], self::DATE_FORMAT_TIME_12AP) : '&nbsp;')
             . (($event['time_end']) ? ' - ' . $this->format_date($event['time_end'], self::DATE_FORMAT_TIME_12AP) : '')
             . "</td>\n";

        $out .= '  <td class="location_start">'
             . (($event['location_start']) ? $event['location_start'] : '&nbsp;')
             . "</td>\n";

        $out .= '  <td class="note">'
             . (($event['note']) ? $event['note'] : '&nbsp;')
             . "</td>\n";

        return $out;
    }

    /**
     * @return string  the HTML for closing a list
     */
    protected function get_list_close() {
        return "</table>\n";
    }

    /**
     * @return string  the HTML for opening a list
     */
    protected function get_list_open() {
        $out = '<table class="cs_list_quicktable">' . "\n";
        $out .= ' <tr><th>Date</th><th>Time</th>'
            . '<th>Location</th><th>Note</th></tr>' . "\n";
        return $out;
    }

    /**
     * Produces a list of events laid out in a short table format
     *
     * @param int $frequent_event_id  the frequent_event_id to limit the list
     *                                to, if any
     *
     * @return string  the complete HTML of the events and the related interface
     *
     * @uses CalendarSolution_List::set_frequent_event_id()  if
     *       $frequent_event_id is passed
     * @uses CalendarSolution_List::set_from()  to default the date to today
     * @uses CalendarSolution_List::run_query()  to obtain the data
     * @uses CalendarSolution_List_Title::get_list_open()  to open the set
     * @uses CalendarSolution_List_Title::get_event_formatted()  to format
     *       each event
     * @uses CalendarSolution_List_Title::get_list_close()  to close the set
     */
    public function get_rendering($frequent_event_id = null) {
        if ($frequent_event_id) {
            $this->set_frequent_event_id($frequent_event_id);
        }

        if ($this->from === null) {
            $this->set_from();
        }

        $this->run_query();

        $out = $this->get_list_open();

        for ($counter = 0; $counter < $this->sql->SQLRecordSetRowCount; $counter++) {
            $event = $this->sql->RecordAsAssocArray(__FILE__, __LINE__,
                array('calendar_uri', 'frequent_event_uri'));

            if ($event['status_id'] == self::STATUS_CANCELLED) {
                $event['note'] = 'CANCELLED. ' . $event['note'];
                $class = 'X';
            } elseif ($event['changed'] == 'Y') {
                $event['note'] = 'CHANGED. ' . $event['note'];
                $class = 'Y';
            } else {
                $class = 'N';
            }
            $class .= ($counter % 2);

            if ($event['status_id'] == self::STATUS_FULL) {
                $event['note'] = 'FULL. ' . $event['note'];
            }

            $out .= $this->get_row_open($class);
            $out .= $this->get_event_formatted($event);
            $out .= $this->get_row_close();
        }

        $out .= $this->get_list_close();

        return $out;
    }

    /**
     * @return string  the HTML for closing a row
     */
    protected function get_row_close() {
        return " </tr>\n";
    }

    /**
     * @param string $class  the CSS class name for this row
     * @return string  the HTML for opening a row
     */
    protected function get_row_open($class) {
        return ' <tr class="' . $class . '">' . "\n";
    }
}
