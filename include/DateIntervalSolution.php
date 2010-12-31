<?php

/**
 * Declares the DateIntervalSolution class based on the PHP version being used
 *
 * PHP Version:
 * + 5.3: DateIntervalSolution extends DateInterval
 * + 5.2: DateIntervalSolution extends DateTimeSolution_DateInterval
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

	/**
	 * PHP's DateInterval class exists; just stub it out
	 * @ignore
	 * @package DateTimeSolution
	 */
	class DateIntervalSolution extends DateInterval {
		/**
		 * Indicates which level of support the DateTime Solution is providing
		 * @var string
		 */
		public $datetime_solution_level = 'native';
	}
} else {
	// PHP 5.2

	/**
	 * PHP 5.2 lacks DateInterval; provide one for forward compatibility
	 *
	 * @package DateTimeSolution
	 * @author Daniel Convissor <danielc@analysisandsolutions.com>
	 * @copyright The Analysis and Solutions Company, 2009-2010
	 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
	 * @link http://www.analysisandsolutions.com/software/datetime_solution/
	 */
	class DateIntervalSolution extends DateTimeSolution_DateInterval {}
}
