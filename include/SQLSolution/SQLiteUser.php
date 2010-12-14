<?php

/**
 * SQL Solution's SQLite connection information.
 *
 * <p>This is part of the SQL Solution.  See the sql-common.inc file
 * for more information, the license, etc.</p>
 *
 * @package    SQLSolution
 * @author     Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright  The Analysis and Solutions Company, 2001-2006
 * @version    $Name:  $ $Id: sql-lite-user.inc,v 5.2 2006-03-18 20:29:39 danielc Exp $
 * @link       http://www.analysisandsolutions.com/software/sql/sql.htm
 * @see        sql-common.inc
 */

/**
 * Obtain the code common to all DBMS'.
 */
require $IncludeDir . '/sql-common.inc';

/**
 * Obtain user defined code.
 */
require $IncludeDir . '/sql-custom.inc';

/**
 * Obtain DBMS specfic code.
 */
require $IncludeDir . '/sql-lite.inc';


/**
 * SQL Solution's SQLite connection information.
 *
 * @package    SQLSolution
 * @author     Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright  The Analysis and Solutions Company, 2001-2006
 * @version    $Name:  $
 * @link       http://www.analysisandsolutions.com/software/sql/sql.htm
 */
class SQLSolution_SQLiteUser extends SQLSolution_SQLiteSpecifics {

    /**
     * Local path and name of the database file
     *
     * NOTE: Calendar Solution automatically adds $IncludeDir
     * to the front of the name.
     *
     * @var  string
     */
    var $SQLDbName = '/CalendarSolution/sqlite/calendar_solution.sqlite2';

    /**
     *
     * @var  string
     */
    var $SQLPermissions = '0600';


    /**
     *
     */
    function SQLSolution_SQLiteUser($Escape = 'Y', $Safe = 'N') {
        $this->SQLClassName  = get_class($this);
        $this->SQLEscapeHTML = $Escape;
        $this->SQLSafeMarkup = $Safe;
    }
}
