<?php

/**
 * SQL Solution's PostgresSQL connection information.
 *
 * <p>This is part of the SQL Solution.  See the sql-common.inc file
 * for more information, the license, etc.</p>
 *
 * @package    SQLSolution
 * @author     Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright  The Analysis and Solutions Company, 2001-2006
 * @version    $Name:  $ $Id: sql-pg-user.inc,v 5.2 2006-03-18 20:29:40 danielc Exp $
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
require $IncludeDir . '/sql-pg.inc';


/**
 * SQL Solution's PostgresSQL connection information.
 *
 * @package    SQLSolution
 * @author     Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright  The Analysis and Solutions Company, 2001-2006
 * @version    $Name:  $
 * @link       http://www.analysisandsolutions.com/software/sql/sql.htm
 */
class SQLSolution_PostgreSQLUser extends SQLSolution_PostgreSQLSpecifics {

    /**
     *
     * @var  string
     */
    var $SQLHost = 'localhost';

    /**
     *
     * @var  string
     */
    var $SQLUser = '';

    /**
     *
     * @var  string
     */
    var $SQLPassword = '';

    /**
     *
     * @var  string
     */
    var $SQLDbName = '';

    /**
     *
     * @var  string
     */
    var $SQLPort = '';

    /**
     *
     * @var  string
     */
    var $SQLOptions = '';

    /**
     *
     * @var  string
     */
    var $SQLTTY = '';


    /**
     * 
     */
    function SQLSolution_PostgreSQLUser($Escape = 'Y', $Safe = 'N') {
        $this->SQLClassName  = get_class($this);
        $this->SQLEscapeHTML = $Escape;
        $this->SQLSafeMarkup = $Safe;
    }
}
