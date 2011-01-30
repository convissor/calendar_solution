<?php

//  LEAVE THIS STUFF AT THE TOP ALONE  ..........................

/**
 * The means to flush the Calendar Solution's cache
 *
 * @package CalendarSolution
 */

/**
 * Obtain the Calendar Solution's settings and autoload function
 *
 * Uses dirname(__FILE__) because "./" can be stripped by PHP's safety
 * settings and __DIR__ was introduced in PHP 5.3.
 */
require dirname(__FILE__) . '/../../include/calendar_solution_settings.php';

/*
 * Instantiate the calendar class appropriate for the view the user wants.
 */
try {
	$calendar = new CalendarSolution;
} catch (Exception $e) {
	die('EXCEPTION: ' . $e->getMessage());
}


//  BEGIN YOUR PAGE SPECIFIC LAYOUT BELOW HERE ..................


?>
<html>
<head>
<title>Calendar Solution Cache Flusher</title>
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
	if ($calendar->is_cache_available()) {
		if (!empty($_POST['proceed'])
			|| (!empty($_SERVER['argv'][1]) && $_SERVER['argv'][1] == 'proceed'))
		{
			if ($calendar->flush_cache()) {
				echo '<p>The cache has been flushed.</p>';
				if (!empty($_SERVER['argv'][1])) {
					exit(0);
				}
			} else {
				echo '<p>Hmm... There were problems flushing the cache.</p>';
				if (!empty($_SERVER['argv'][1])) {
					exit(1);
				}
			}
		} else {
			echo '<form method="post">';
			echo '<input type="submit" name="proceed" value="Flush Cache" />';
			echo '</form>';
		}
	} else {
		echo '<p>Caching is not available on this installation.</p>';
	}
} catch (Exception $e) {
	die('EXCEPTION: ' . $e->getMessage());
}


?>

</body>
</html>
