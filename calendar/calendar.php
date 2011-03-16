<?php

//  LEAVE THIS STUFF AT THE TOP ALONE  ..........................

/**
 * The means of viewing the main list of events for the Calendar Solution
 *
 * @package CalendarSolution
 */

/**
 * Obtain the Calendar Solution's settings and autoload function
 *
 * Uses dirname(__FILE__) because "./" can be stripped by PHP's safety
 * settings and __DIR__ was introduced in PHP 5.3.
 */
require dirname(__FILE__) . '/../include/calendar_solution_settings.php';

/**
 * Manually require List class because PHP 5.2 doesn't use autoload for static
 * methods
 *
 * Another lesson learned the hard way. :)
 */
require dirname(__FILE__) . '/../include/CalendarSolution/List.php';


/*
 * Instantiate the calendar class appropriate for the view the user wants.
 */
try {
	$calendar = CalendarSolution_List::factory_chosen_view();
} catch (Exception $e) {
	die('EXCEPTION: ' . $e->getMessage());
}


//  BEGIN YOUR PAGE SPECIFIC LAYOUT BELOW HERE ..................


?>
<html>
<head>
<title>Calendar Solution</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<style type="text/css">

<?php echo $calendar->get_css(); ?>

</style>

<?php

if ($calendar->is_admin()) {
	echo '<link rel="stylesheet" type="text/css" href="admin.css" />';
}

?>

</head>
<body>

<?php

/*
 * All that the admin section's list page does is include the file you are
 * editing now.  Doing so avoids the need to maintain duplicate code.  But the
 * admin list page needs a link to add a new event.  So the three lines below
 * generate that link if the person is viewing this script via the admin page.
 */
if ($calendar->is_admin()) {
	$calendar->set_permit_history_months(false);
	$calendar->set_permit_future_months(false);
	echo $calendar->get_admin_navigation();
}


/*
 * Display the calendar.
 */
try {
	echo $calendar->get_date_navigation();
	echo $calendar->get_change_view();
	echo $calendar->get_rendering();
	echo $calendar->get_limit_form();
} catch (Exception $e) {
	die('EXCEPTION: ' . $e->getMessage());
}


?>

</body>
</html>
