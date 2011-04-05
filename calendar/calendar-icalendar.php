<?php

//  LEAVE THIS STUFF AT THE TOP ALONE  ..........................

/**
 * The means of viewing the main list of events for the Calendar Solution
 * in iCalendar format
 *
 * @package CalendarSolution
 * @since File available since version 3.3
 */

/**
 * Obtain the Calendar Solution's settings and autoload function
 *
 * Uses dirname(__FILE__) because "./" can be stripped by PHP's safety
 * settings and __DIR__ was introduced in PHP 5.3.
 */
require dirname(__FILE__) . '/../include/calendar_solution_settings.php';

/*
 * Display the event.
 */
try {
	$calendar = new CalendarSolution_List_Icalendar;
	header('Content-Type: text/calendar; charset=UTF-8');
	echo $calendar->get_rendering();
} catch (Exception $e) {
	die('EXCEPTION: ' . $e->getMessage());
}
