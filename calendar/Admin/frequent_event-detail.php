<?php

//  LEAVE THIS STUFF AT THE TOP ALONE  ..........................

/**
 * The point of entry for editing an event in the Calendar Solution
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

try {
	$calendar = new CalendarSolution_FrequentEvent_Form;
} catch (Exception $e) {
	die('EXCEPTION: ' . $e->getMessage());
}


//  BEGIN YOUR PAGE SPECIFIC LAYOUT BELOW HERE ..................


?>

<html>
<head>
<title>Calendar Solution Admin Frequent Event Details</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="admin.css" />
</head>
<body>


<?php

echo $calendar->get_admin_navigation();

try {
	if (empty($_POST['submit'])) {
		if (empty($_GET['frequent_event_id'])) {
			$calendar->set_data_empty();
		} else {
			$calendar->set_data_from_query($_GET['frequent_event_id'], false);
		}
		echo $calendar->get_rendering();
	} else {
		$calendar->set_data_from_post();

		$saved = true;
		switch ($_POST['submit']) {
			case 'Add':
				if ($calendar->is_valid(false)) {
					$calendar->insert();
				} else {
					echo $calendar->get_errors();
					echo $calendar->get_rendering();
					$saved = false;
				}
				break;
			case 'Update':
				if ($calendar->is_valid()) {
					$calendar->update();
				} else {
					echo $calendar->get_errors();
					echo $calendar->get_rendering();
					$saved = false;
				}
				break;
			case 'Delete':
				$calendar->delete();
				break;
			default:
				throw new CalendarSolution_Exception('Invalid submit option');
		}

		if ($saved) {
			echo '<p class="notice"><big class="notice">Your changes were saved.</big></p>';
		}
	}
} catch (Exception $e) {
	echo 'EXCEPTION: ' . $e->getMessage();
}


?>

</body>
</html>
