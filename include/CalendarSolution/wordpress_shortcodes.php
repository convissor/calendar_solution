<?php

/**
 * Calendar Solution's API for WordPress Shortcodes
 *
 * WordPress has Shortcode functionality built in as of version 2.5.
 *
 * <ul>
 * <li> calendar_solution_calendar: {@link calendar_solution_calendar()}</li>
 * <li> calendar_solution_detailtable: {@link calendar_solution_detailtable()}</li>
 * <li> calendar_solution_list: {@link calendar_solution_list()}</li>
 * <li> calendar_solution_monthtitle: {@link calendar_solution_monthtitle()}</li>
 * <li> calendar_solution_quicktable: {@link calendar_solution_quicktable()}</li>
 * <li> calendar_solution_title: {@link calendar_solution_title()}</li>
 * <li> calendar_solution_ul: {@link calendar_solution_ul()}</li>
 * </ul>
 *
 * The following Attributes are available for use with all Shortcodes.
 * Some formats have additional Attributes; see the documentation for each
 * function for more details.
 *
 * <ul>
 * <li> Attributes to get data by ID:  <ul>
 *   <li> category_id: {@link CalendarSolution_List::set_category_id()}</li>
 *   <li> frequent_event_id: {@link CalendarSolution_List::set_frequent_event_id()}</li>
 *   <li> page_id: {@link CalendarSolution_List::set_page_id()}</li>
 * </ul></li>
 *
 * <li> Attributes to limit the number of results:  <ul>
 *   <li> limit: {@link CalendarSolution_List::set_limit()}</li>
 * </ul></li>
 *
 * <li> Attributes to get data a user is seeking:  <ul>
 *   <li> request_properties: {@link CalendarSolution_List::set_request_properties()}</li>
 * </ul></li>
 *
 * <li> Attributes for displaying navigation elements:  <ul>
 *   <li> date_navigation_top / date_navigation_bottom: {@link CalendarSolution_List::get_date_navigation()}</li>
 *   <li> limit_form_top / limit_form_bottom: {@link CalendarSolution_List::get_limit_form()}</li>
 *   <li> limit_navigation_bottom: {@link CalendarSolution_List::get_limit_navigation()}</li>
 * </ul></li>
 *
 * <li> Attributes for formatting data:  <ul>
 *   <li> date_format: {@link CalendarSolution_List::set_date_format()}</li>
 *   <li> time_format: {@link CalendarSolution_List::set_time_format()}</li>
 *   <li> show_cancelled: {@link CalendarSolution_List::set_show_cancelled()}</li>
 *   <li> is_own_event: {@link CalendarSolution_List::set_is_own_event()}</li>
 *   <li> show_own_events_first: {@link CalendarSolution_List::set_show_own_events_first()}</li>
 * </ul></li>
 *
 * <li> Attributes for limiting data by date (the defaults are sane; use only
 * when necessary):  <ul>
 *   <li> from: {@link CalendarSolution_List::set_from()}</li>
 *   <li> to: {@link CalendarSolution_List::set_to()}</li>
 *   <li> permit_history_months: {@link CalendarSolution_List::set_permit_history_months()}</li>
 *   <li> permit_future_months: {@link CalendarSolution_List::set_permit_future_months()}</li>
 * </ul></li></ul>
 *
 * <pre>
 * # Examples
 * # MonthTitle, date dropdown list on top, date navigation on bottom.
 * [calendar_solution_monthtitle limit_form_top="datelist" date_navigation_bottom=""]
 *
 * # QuickTable, five rows, navigation for more events on the bottom.
 * [calendar_solution_quicktable frequent_event_id="6" limit="5,null" limit_navigation_bottom=""]
 * </pre>
 *
 * Calendar Solution is a trademark of The Analysis and Solutions Company.
 *
 * @link http://codex.wordpress.org/Shortcode_API
 *
 * @package CalendarSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2002-2012
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 */

/**
 * Obtain the Calendar Solution's settings and autoload function
 *
 * @internal Uses dirname(__FILE__) because "./" can be stripped by PHP's
 * safety settings and __DIR__ was introduced in PHP 5.3.
 */
require dirname(dirname(__FILE__)) . '/calendar_solution_settings.php';


/*
 * LIST OUTPUT FORMATS
 */

/**
 * Shortcode handler for producing a list of events laid out in a calendar
 * grid format
 *
 * See {@link CalendarSolution_List_Calendar::get_rendering()} for more
 * details regarding this format.
 *
 * See {@link wordpress_shortcodes.php} for a list of
 * attributes that can be used with this shortcode.
 *
 * This shortcode has additional optional attributes:
 * + show_location: {@link CalendarSolution_List::set_show_location()}
 * + months: {@link CalendarSolution_List::__construct()}
 *
 * @param array $atts  the attributes used in the current shortcode call
 * @return string  the desired HTML
 *
 * @since Function available since version 3.3
 */
add_shortcode('calendar_solution_calendar', 'calendar_solution_calendar');
function calendar_solution_calendar($atts) {
	try {
		$months = calendar_solution_months_helper($atts);
		$calendar = new CalendarSolution_List_Calendar($months);

		calendar_solution_list_attribute_handler($calendar, $atts);
		if (!empty($atts['show_location'])) {
			$atts['show_location'] = calendar_solution_parameter_casting($atts['show_location']);
			$calendar->set_show_location($atts['show_location']);
		}

		$out = calendar_solution_navigation_top($calendar, $atts);
		$out .= $calendar->get_rendering();
		$out .= calendar_solution_navigation_bottom($calendar, $atts);
		return $out;
	} catch (Exception $e) {
		return 'CALENDAR SOLUTION EXCEPTION: ' . $e->getMessage();
	}
}

/**
 * Shortcode handler for producing a table of events showing details
 * about each entry
 *
 * See {@link CalendarSolution_List_DetailTable::get_rendering()} for more
 * details regarding this format.
 *
 * See {@link wordpress_shortcodes.php} for a list of
 * attributes that can be used with this shortcode.
 *
 * This shortcode has additional optional attributes:
 * + show_time_end: {@link CalendarSolution_List::set_show_time_end()}
 *
 * @param array $atts  the attributes used in the current shortcode call
 * @return string  the desired HTML
 *
 * @since Function available since version 3.4
 */
add_shortcode('calendar_solution_detailtable', 'calendar_solution_detailtable');
function calendar_solution_detailtable($atts) {
	try {
		$calendar = new CalendarSolution_List_DetailTable;

		calendar_solution_list_attribute_handler($calendar, $atts);
		if (array_key_exists('show_time_end', $atts)) {
			$atts['show_time_end'] = calendar_solution_parameter_casting($atts['show_time_end']);
			$calendar->set_show_time_end($atts['show_time_end']);
		}

		$out = calendar_solution_navigation_top($calendar, $atts);
		$out .= $calendar->get_rendering();
		$out .= calendar_solution_navigation_bottom($calendar, $atts);
		return $out;
	} catch (Exception $e) {
		return 'CALENDAR SOLUTION EXCEPTION: ' . $e->getMessage();
	}
}

/**
 * Shortcode handler for producing a table of events showing significant
 * info about each event
 *
 * See {@link CalendarSolution_List_List::get_rendering()} for more
 * details regarding this format.
 *
 * See {@link wordpress_shortcodes.php} for a list of
 * attributes that can be used with this shortcode.
 *
 * This shortcode has additional optional attributes:
 * + show_summary: {@link CalendarSolution_List::set_show_summary()}
 * + months: {@link CalendarSolution_List::__construct()}
 *
 * @param array $atts  the attributes used in the current shortcode call
 * @return string  the desired HTML
 *
 * @since Function available since version 3.3
 */
add_shortcode('calendar_solution_list', 'calendar_solution_list');
function calendar_solution_list($atts) {
	try {
		$months = calendar_solution_months_helper($atts);
		$calendar = new CalendarSolution_List_List($months);

		calendar_solution_list_attribute_handler($calendar, $atts);
		if (!empty($atts['show_summary'])) {
			$atts['show_summary'] = calendar_solution_parameter_casting($atts['show_summary']);
			$calendar->set_show_summary($atts['show_summary']);
		}

		$out = calendar_solution_navigation_top($calendar, $atts);
		$out .= $calendar->get_rendering();
		$out .= calendar_solution_navigation_bottom($calendar, $atts);
		return $out;
	} catch (Exception $e) {
		return 'CALENDAR SOLUTION EXCEPTION: ' . $e->getMessage();
	}
}

/**
 * Shortcode handler for producing a table of events showing their dates
 * and names, grouped by month
 *
 * See {@link CalendarSolution_List_MonthTitle::get_rendering()} for more
 * details regarding this format.
 *
 * See {@link wordpress_shortcodes.php} for a list of
 * attributes that can be used with this shortcode.
 *
 * This shortcode has additional optional attributes:
 * + months: {@link CalendarSolution_List::__construct()}
 *
 * @param array $atts  the attributes used in the current shortcode call
 * @return string  the desired HTML
 *
 * @since Function available since version 3.3
 */
add_shortcode('calendar_solution_monthtitle', 'calendar_solution_monthtitle');
function calendar_solution_monthtitle($atts) {
	try {
		$months = calendar_solution_months_helper($atts);
		$calendar = new CalendarSolution_List_MonthTitle($months);

		calendar_solution_list_attribute_handler($calendar, $atts);

		$out = calendar_solution_navigation_top($calendar, $atts);
		$out .= $calendar->get_rendering();
		$out .= calendar_solution_navigation_bottom($calendar, $atts);
		return $out;
	} catch (Exception $e) {
		return 'CALENDAR SOLUTION EXCEPTION: ' . $e->getMessage();
	}
}

/**
 * Shortcode handler for producing a table of events showing basic info
 * about each entry
 *
 * See {@link CalendarSolution_List_QuickTable::get_rendering()} for more
 * details regarding this format.
 *
 * See {@link wordpress_shortcodes.php} for a list of
 * attributes that can be used with this shortcode.
 *
 * This shortcode has additional optional attributes:
 * + show_time_end: {@link CalendarSolution_List::set_show_time_end()}
 *
 * @param array $atts  the attributes used in the current shortcode call
 * @return string  the desired HTML
 *
 * @since Function available since version 3.3
 */
add_shortcode('calendar_solution_quicktable', 'calendar_solution_quicktable');
function calendar_solution_quicktable($atts) {
	try {
		$calendar = new CalendarSolution_List_QuickTable;

		calendar_solution_list_attribute_handler($calendar, $atts);
		if (array_key_exists('show_time_end', $atts)) {
			$atts['show_time_end'] = calendar_solution_parameter_casting($atts['show_time_end']);
			$calendar->set_show_time_end($atts['show_time_end']);
		}

		$out = calendar_solution_navigation_top($calendar, $atts);
		$out .= $calendar->get_rendering();
		$out .= calendar_solution_navigation_bottom($calendar, $atts);
		return $out;
	} catch (Exception $e) {
		return 'CALENDAR SOLUTION EXCEPTION: ' . $e->getMessage();
	}
}

/**
 * Shortcode handler for producing a table of events showing their dates
 * and names
 *
 * See {@link CalendarSolution_List_Title::get_rendering()} for more
 * details regarding this format.
 *
 * See {@link wordpress_shortcodes.php} for a list of
 * attributes that can be used with this shortcode.
 *
 * @param array $atts  the attributes used in the current shortcode call
 * @return string  the desired HTML
 *
 * @since Function available since version 3.3
 */
add_shortcode('calendar_solution_title', 'calendar_solution_title');
function calendar_solution_title($atts) {
	try {
		$calendar = new CalendarSolution_List_Title;

		calendar_solution_list_attribute_handler($calendar, $atts);

		$out = calendar_solution_navigation_top($calendar, $atts);
		$out .= $calendar->get_rendering();
		$out .= calendar_solution_navigation_bottom($calendar, $atts);
		return $out;
	} catch (Exception $e) {
		return 'CALENDAR SOLUTION EXCEPTION: ' . $e->getMessage();
	}
}

/**
 * Shortcode handler for producing an "unordered list" of events showing
 * their dates and names
 *
 * See {@link CalendarSolution_List_Ul::get_rendering()} for more
 * details regarding this format.
 *
 * See {@link wordpress_shortcodes.php} for a list of
 * attributes that can be used with this shortcode.
 *
 * @param array $atts  the attributes used in the current shortcode call
 * @return string  the desired HTML
 *
 * @since Function available since version 3.3
 */
add_shortcode('calendar_solution_ul', 'calendar_solution_ul');
function calendar_solution_ul($atts) {
	try {
		$calendar = new CalendarSolution_List_Ul;

		calendar_solution_list_attribute_handler($calendar, $atts);

		$out = calendar_solution_navigation_top($calendar, $atts);
		$out .= $calendar->get_rendering();
		$out .= calendar_solution_navigation_bottom($calendar, $atts);
		return $out;
	} catch (Exception $e) {
		return 'CALENDAR SOLUTION EXCEPTION: ' . $e->getMessage();
	}
}


/*
 * HELPER FUNCTIONS
 */

/**
 * @ignore
 *
 * Gets the "months" attribute if set, returns the default (3) if not
 *
 * @param array $atts  the Shortcode API function's $atts parameter
 *
 * @return int  the number of months to show
 */
function calendar_solution_months_helper($atts) {
	if (empty($atts['months'])) {
		return 3;
	} else {
		return $atts['months'];
	}
}

/**
 * @ignore
 *
 * Processes attributes requesting navigataion elements for the top
 *
 * @param CalendarSolution_List $calendar  the calendar object being used
 * @param array $atts  the Shortcode API function's $atts parameter
 *
 * @return string  the desired HTML
 */
function calendar_solution_navigation_top($calendar, $atts) {
	$possible = array(
		'date_navigation_top',
		'limit_form_top',
	);
	return calendar_solution_navigation_helper($calendar, $atts, $possible);
}

/**
 * @ignore
 *
 * Processes attributes requesting navigataion elements for the bottom
 *
 * @param CalendarSolution_List $calendar  the calendar object being used
 * @param array $atts  the Shortcode API function's $atts parameter
 *
 * @return string  the desired HTML
 */
function calendar_solution_navigation_bottom($calendar, $atts) {
	$possible = array(
		'date_navigation_bottom',
		'limit_form_bottom',
		'limit_navigation_bottom',
	);
	return calendar_solution_navigation_helper($calendar, $atts, $possible);
}

/**
 * @ignore
 *
 * Generates the HTML for the navigataion attribute functions
 *
 * @param CalendarSolution_List $calendar  the calendar object being used
 * @param array $atts  the Shortcode API function's $atts parameter
 * @param array $possible  the shortcode attributes allowed for this area
 *
 * @return string  the desired HTML
 */
function calendar_solution_navigation_helper($calendar, $atts, $possible) {
	$out = '';
	$navs = array_intersect(array_keys($atts), $possible);
	foreach ($navs as $nav) {
		$method = 'get_' . preg_replace('/^(.+)(_top|_bottom)$/', '\1', $nav);

		$params = explode(',', $atts[$nav]);
		if ($params === array('')) {
			$out .= call_user_func(array($calendar, $method));
		} elseif ($method == 'get_limit_form') {
			$out .= call_user_func(array($calendar, $method), $params);
		} else {
			$out .= call_user_func_array(array($calendar, $method), $params);
		}
	}
	return $out;
}

/**
 * @ignore
 *
 * Converts strings "true", "false", and "null" to native data types
 *
 * @param mixed $param  the string to cast: "true", "false", "null"
 *
 * @return mixed  the native value: TRUE, FALSE, NULL
 */
function calendar_solution_parameter_casting($param) {
	switch ($param) {
		case 'null':
		case 'NULL':
			return null;
		case 'false':
		case 'FALSE':
			return false;
		case 'true':
		case 'TRUE':
			return true;
		default:
			return $param;
	}
}

/**
 * @ignore
 *
 * Acts upon the attributes passed to the shortcode
 *
 * @param CalendarSolution_List $calendar  the calendar object being used
 * @param array $atts  the Shortcode API function's $atts parameter
 *
 * @return void
 */
function calendar_solution_list_attribute_handler($calendar, $atts) {
	// NOTE:  use array_key_exists() where "" or "0" are legitimate values.

	if (!empty($atts['category_id'])) {
		$params = explode(',', $atts['category_id']);
		$params[0] = calendar_solution_parameter_casting($params[0]);
		$calendar->set_category_id($params);
	}
	if (!empty($atts['date_format'])) {
		$calendar->set_date_format($atts['date_format']);
	}
	if (!empty($atts['frequent_event_id'])) {
		$atts['frequent_event_id'] = calendar_solution_parameter_casting($atts['frequent_event_id']);
		$calendar->set_frequent_event_id($atts['frequent_event_id']);
	}
	if (!empty($atts['from'])) {
		$atts['from'] = calendar_solution_parameter_casting($atts['from']);
		$calendar->set_from($atts['from']);
	}
	if (!empty($atts['is_own_event'])) {
		$atts['is_own_event'] = calendar_solution_parameter_casting($atts['is_own_event']);
		$calendar->set_is_own_event($atts['is_own_event']);
	}
	if (!empty($atts['limit'])) {
		$params = explode(',', $atts['limit']);
		$params[0] = calendar_solution_parameter_casting($params[0]);
		if (array_key_exists(1, $params)) {
			$params[1] = calendar_solution_parameter_casting($params[1]);
		} else {
			$params[1] = false;
		}
		$calendar->set_limit($params[0], $params[1]);
	}
	if (!empty($atts['page_id'])) {
		$calendar->set_page_id($atts['page_id']);
	}
	if (array_key_exists('permit_future_months', $atts)) {
		$atts['permit_future_months'] = calendar_solution_parameter_casting($atts['permit_future_months']);
		$calendar->set_permit_future_months($atts['permit_future_months']);
	}
	if (array_key_exists('permit_history_months', $atts)) {
		$atts['permit_history_months'] = calendar_solution_parameter_casting($atts['permit_history_months']);
		$calendar->set_permit_history_months($atts['permit_history_months']);
	}
	if (!empty($atts['show_cancelled'])) {
		$atts['show_cancelled'] = calendar_solution_parameter_casting($atts['show_cancelled']);
		$calendar->set_show_cancelled($atts['show_cancelled']);
	}
	if (!empty($atts['show_own_events_first'])) {
		$atts['show_own_events_first'] = calendar_solution_parameter_casting($atts['show_own_events_first']);
		$calendar->set_show_own_events_first($atts['show_own_events_first']);
	}
	if (!empty($atts['time_format'])) {
		$calendar->set_time_format($atts['time_format']);
	}
	if (!empty($atts['to'])) {
		$atts['to'] = calendar_solution_parameter_casting($atts['to']);
		$calendar->set_to($atts['to']);
	}
	if (array_key_exists('request_properties', $atts)) {
		$calendar->set_request_properties();
	}
}
