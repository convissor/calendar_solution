<?php

//  LEAVE THIS STUFF AT THE TOP ALONE  ..........................

/**
 * An example of integrating a ul list of Featured Events into
 * a Home Page
 *
 * @package CalendarSolution
 */

/**
 * Obtain the Calendar Solution's settings and autoload function
 *
 * @internal Uses dirname(__FILE__) because "./" can be stripped by PHP's
 * safety settings and __DIR__ was introduced in PHP 5.3.
 */
require dirname(__FILE__) . '/../include/calendar_solution_settings.php';

/*
 * Instantiate the ul class.
 */
try {
	$calendar = new CalendarSolution_List_Ul;
} catch (Exception $e) {
	die('EXCEPTION: ' . $e->getMessage());
}


//  BEGIN YOUR PAGE SPECIFIC LAYOUT BELOW HERE ..................


?>
<html>
<head>
<title>Calendar Solution Example: UL format for Home Pages</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<style type="text/css">

<?php echo $calendar->get_css(); ?>

</style>
</head>
<body>

<?php

/*
 * Display the list.
 */
try {
	// 1 = Home Page
	$calendar->set_limit(3);
	echo $calendar->get_rendering(1);
} catch (Exception $e) {
	die('EXCEPTION: ' . $e->getMessage());
}


?>

</body>
</html>
