<?php

//  LEAVE THIS STUFF AT THE TOP ALONE  ..........................

/**
 * The means of viewing a particular event in the Calendar Solution
 *
 * @package CalendarSolution
 */

/**
 * Set $IncludeDir and $dbms
 *
 * Use dirname(__FILE__) because "./" can be stripped by PHP's safety
 * settings and __DIR__ was introduced in PHP 5.3.
 */
require dirname(__FILE__) . '/directory.inc';

/**
 * Gather the calendar class
 */
require $GLOBALS['IncludeDir'] . '/CalendarSolution/Detail/Html.php';

/*
 * Instantiate the calendar detail HTML class.
 */
try {
    $calendar = new CalendarSolution_Detail_Html($dbms);

    // Calendar Solution runs htmlspecialchars() on the output.
    $event_title = $calendar->get_title();
} catch (Exception $e) {
    die('EXCEPTION: ' . $e->getMessage());
}


//  BEGIN YOUR PAGE SPECIFIC LAYOUT BELOW HERE ..................


?>

<html>
<head>
<title>Calendar Solution: <?php echo $event_title; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<style type="text/css">

<?php echo $calendar->get_css(); ?>

</style>
</head>
<body>

<?php

/*
 * Display the event.
 */
try {
    echo $calendar->get_rendering();
} catch (Exception $e) {
    die('EXCEPTION: ' . $e->getMessage());
}

?>

</body>
</html>
