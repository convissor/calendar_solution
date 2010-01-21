<?php

/**
 * Calendar Solution's means to output collections of events formatted as a
 * bullet list of the date and name of each event
 *
 * Intended for use to display Featured Events on Home Pages or other top
 * level pages.
 *
 * @package CalendarSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2002-2009
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 */


/**
 * Gather the parent class
 */
require_once $GLOBALS['IncludeDir'] . '/CalendarSolution/List.php';


/**
 * The means to output collections of events formatted as a bullet list
 * of the date and name of each event
 *
 * Intended for use to display Featured Events on Home Pages or other top
 * level pages.
 *
 * @package CalendarSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2002-2009
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 */
class CalendarSolution_List_Title extends CalendarSolution_List {
    /**
     * The type of view this class represents
     * @var string
     */
    protected $view = 'Title';


    /**
     * Provides the path and name of the needed Cascading Style Sheet file
     *
     * @return string  the path and name of the CSS file
     *
     * @uses $GLOBALS['IncludeDir']  to know where include files reside
     */
    public function get_css_name() {
        return $GLOBALS['IncludeDir'] . '/CalendarSolution/List/Title.css';
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

        $out =  '<span class="day">'
            . $this->format_date($event['date_start'], self::DATE_FORMAT_SHORT)
            . '</span> '
            . '<span class="title">' . $this->get_link($event) . '</span>';

        return $out;
    }

    /**
     * @return string  the HTML for closing a list
     */
    protected function get_list_close() {
        return "</ul>\n";
    }

    /**
     * @return string  the HTML for opening a list
     */
    protected function get_list_open() {
        return '<ul class="cs_list_title">' . "\n";
    }

    /**
     * Produces a list of events laid out in a short list format
     *
     * @param int $page_id  the feature_on_page_id to limit the list to, if any
     *
     * @return string  the complete HTML of the events and the related interface
     *
     * @uses CalendarSolution_List::set_page_id()  if $page_id is passed
     * @uses CalendarSolution_List::set_from()  to default the date to today
     * @uses CalendarSolution_List::run_query()  to obtain the data
     * @uses CalendarSolution_List_Title::get_list_open()  to open the set
     * @uses CalendarSolution_List_Title::get_row_open()  to open the element
     * @uses CalendarSolution_List_Title::get_event_formatted()  to format
     *       each event
     * @uses CalendarSolution_List_Title::get_row_close()  to close the element
     * @uses CalendarSolution_List_Title::get_list_close()  to close the set
     */
    public function get_rendering($page_id = null) {
        if ($page_id) {
            $this->set_page_id($page_id);
        }

        if ($this->from === null) {
            $this->set_from();
        }

        $this->run_query();

        $out = $this->get_list_open();

        for ($counter = 0; $counter < $this->sql->SQLRecordSetRowCount; $counter++) {
            $event = $this->sql->RecordAsAssocArray(__FILE__, __LINE__,
                array('calendar_uri', 'frequent_event_uri'));

            if ($event['changed'] == 'Y') {
                $event['title'] = 'CHANGED: ' . $event['title'];
                $class = 'Y' . ($counter % 2);
            } else {
                $class = 'N' . ($counter % 2);
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
        return "</li>\n";
    }

    /**
     * @param string $class  the CSS class name for this row
     * @return string  the HTML for opening a row
     */
    protected function get_row_open($class) {
        return ' <li class="' . $class . '">';
    }
}
