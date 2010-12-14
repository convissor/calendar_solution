<?php

/**
 * SQL Solution's MySQLi connection information.
 *
 * <p>This is part of the SQL Solution.  See the sql-common.inc file
 * for more information, the license, etc.</p>
 *
 * @package    SQLSolution
 * @author     Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright  The Analysis and Solutions Company, 2001-2008
 * @version    $Name:  $ $Id: sql-myi-user.inc,v 5.1 2009-02-06 16:57:43 danielc Exp $
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
require $IncludeDir . '/sql-myi.inc';


/**
 * SQL Solution's MySQL connection information.
 *
 * @package    SQLSolution
 * @author     Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright  The Analysis and Solutions Company, 2001-2008
 * @version    $Name:  $
 * @link       http://www.analysisandsolutions.com/software/sql/sql.htm
 */
class SQLSolution_MySQLiUser extends SQLSolution_MySQLiSpecifics {

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
     * @var  int
     */
    var $SQLPort = null;

    /**
     *
     * @var  string
     */
    var $SQLSocket = null;


    /**
     *
     */
    function SQLSolution_MySQLiUser($Escape = 'Y', $Safe = 'N') {
        $this->SQLClassName  = get_class($this);
        $this->SQLEscapeHTML = $Escape;
        $this->SQLSafeMarkup = $Safe;
    }
}
