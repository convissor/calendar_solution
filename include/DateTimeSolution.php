<?php

/**
 * Declares the DateTimeSolution class based on the PHP version being used
 *
 * PHP Version:
 * + >= 5.3.5: DateTimeSolution extends DateTime
 * + 5.3 < 5.3.5: DateTimeSolution extends DateTimeSolution_Diff extends DateTime
 * + 5.2: DateTimeSolution extends DateTimeSolution_52 extends
 *        DateTimeSolution_Diff extends DateTime
 *
 * DateTime Solution is a trademark of The Analysis and Solutions Company.
 *
 * @package DateTimeSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2009-2010
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 * @link http://www.analysisandsolutions.com/software/datetime_solution/
 */

if (class_exists('DateInterval')) {
	// PHP 5.3

	if (version_compare(phpversion(), '5.3.5', '>=')) {
		/**
		 * Bug should be fixed in 5.3.5; just stub PHP's DateTime class
		 * @ignore
		 * @package DateTimeSolution
		 */
		class DateTimeSolution extends DateTime {
			/**
			 * Indicates which level of support the DateTime Solution is providing
			 * @var string
			 */
			public $datetime_solution_level = 'native';
		}
	} else {
		/**
		 * Bugs afflict DateTime::diff(); provide working version
		 * @ignore
		 * @package DateTimeSolution
		 */
		class DateTimeSolution extends DateTimeSolution_Diff {}
	}
} else {
	// PHP 5.2

	/**
	 * PHP 5.2 needs the complete DateTimeSolution mojo
	 *
	 * @package DateTimeSolution
	 * @author Daniel Convissor <danielc@analysisandsolutions.com>
	 * @copyright The Analysis and Solutions Company, 2009-2010
	 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
	 * @link http://www.analysisandsolutions.com/software/datetime_solution/
	 */
	class DateTimeSolution extends DateTimeSolution_52 {}
}
