<?php

/**
 * Declares the DateIntervalSolution class based on the PHP version being used
 *
 * PHP Version:
 * + 5.4: DateIntervalSolution extends DateInterval
 * + 5.3 & 5.2: DateIntervalSolution extends DateTimeSolution_DateInterval
 *
 * DateTime Solution is a trademark of The Analysis and Solutions Company.
 *
 * @package DateTimeSolution
 * @author Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright The Analysis and Solutions Company, 2009-2011
 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
 * @link http://www.analysisandsolutions.com/software/datetime_solution/
 */

if (version_compare(phpversion(), '5.4', '<')) {
	/**
	 * PHP 5.2 lacks DateInterval while in PHP 5.3 properties aren't reliably
	 * writable; provide one for forward compatibility
	 *
	 * For example, PHP Bug 53634.
	 *
	 * @package DateTimeSolution
	 * @author Daniel Convissor <danielc@analysisandsolutions.com>
	 * @copyright The Analysis and Solutions Company, 2009-2011
	 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
	 * @link http://www.analysisandsolutions.com/software/datetime_solution/
	 */
	class DateIntervalSolution extends DateTimeSolution_DateInterval {}
} else {
	/**
	 * PHP 5.4 has everything; just stub out PHP's DateInterval
	 * @ignore
	 * @package DateTimeSolution
	 */
	class DateIntervalSolution extends DateInterval {
		/**
		 * Indicates which level of support the DateTime Solution is providing
		 * @return string
		 */
		public function get_datetime_solution_level() {
			return 'native';
		}
	}
}
