<?php

//  LEAVE THIS STUFF AT THE TOP ALONE  ..........................

/**
 * The means of viewing the list Frequent Events for the Calendar Solution
 *
 * @package CalendarSolution
 */

/**
 * Obtain the Calendar Solution's settings and autoload function
 *
 * @internal Uses dirname(__FILE__) because "./" can be stripped by PHP's
 * safety settings and __DIR__ was introduced in PHP 5.3.
 */
require dirname(__FILE__) . '/../../include/calendar_solution_settings.php';

/*
 * Instantiate the calendar class appropriate for the view the user wants.
 */
try {
	$calendar = new CalendarSolution_FrequentEvent_List;
} catch (Exception $e) {
	die('EXCEPTION: ' . $e->getMessage());
}


//  BEGIN YOUR PAGE SPECIFIC LAYOUT BELOW HERE ..................


?>
<html>
<head>
<title>Calendar Solution Admin Frequent Events</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="admin.css" />
</head>
<body>

<?php

echo $calendar->get_admin_navigation();

/*
 * Display the calendar.
 */
try {
	echo $calendar->get_rendering();
} catch (Exception $e) {
	die('EXCEPTION: ' . $e->getMessage());
}


?>

</body>
</html>
