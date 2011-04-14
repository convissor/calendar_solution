<html>
<head>
<title>Calendar Solution Benchmark: calendar view</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body>

<?php

/**
 * Script for benchmarking the main list of events
 *
 * @package CalendarSolution_Test
 */

/**
 * Obtain the Calendar Solution's settings and autoload function
 *
 * @internal Uses dirname(__FILE__) because "./" can be stripped by PHP's
 * safety settings and __DIR__ was introduced in PHP 5.3.
 */
require dirname(__FILE__) . '/../../include/calendar_solution_settings.php';

try {
	$calendar = new CalendarSolution_List_Calendar;
	$calendar->set_from('2011-02-01');
	$calendar->set_to('2011-04-30');
	echo $calendar->get_rendering();
} catch (Exception $e) {
	die('EXCEPTION: ' . $e->getMessage());
}


?>

</body>
</html>
