<?php

/**
 * Declares the DateTimeSolution class based on the PHP version being used
 *
 * PHP Version:
 * + 5.4: DateTimeSolution extends DateTime
 * + 5.3 & 5.2: DateTimeSolution extends DateTimeSolution_52 extends
 *        DateTimeSolution_Diff extends DateTime
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
	 * PHP 5.2 lacks diff(), add(), and sub() and
	 * PHP 5.3 is afflicted by bugs in diff(); provide working versions
	 *
	 * @package DateTimeSolution
	 * @author Daniel Convissor <danielc@analysisandsolutions.com>
	 * @copyright The Analysis and Solutions Company, 2009-2011
	 * @license http://www.analysisandsolutions.com/software/license.htm Simple Public License
	 * @link http://www.analysisandsolutions.com/software/datetime_solution/
	 */
	class DateTimeSolution extends DateTimeSolution_52 {}
} else {
	/**
	 * PHP 5.4 should have everything right; just stub out PHP's DateTime
	 * @ignore
	 * @package DateTimeSolution
	 */
	class DateTimeSolution extends DateTime {
		/**
		 * Indicates which level of support the DateTime Solution is providing
		 * @return string
		 */
		public function get_datetime_solution_level() {
			return 'native';
		}
	}
}
