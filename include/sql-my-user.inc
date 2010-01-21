<?php

/**
 * SQL Solution's MySQL connection information.
 *
 * <p>This is part of the SQL Solution.  See the sql-common.inc file
 * for more information, the license, etc.</p>
 *
 * @package    SQLSolution
 * @author     Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright  The Analysis and Solutions Company, 2001-2006
 * @version    $Name:  $ $Id: sql-my-user.inc,v 5.5 2006-03-18 20:29:39 danielc Exp $
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
require $IncludeDir . '/sql-my.inc';


/**
 * SQL Solution's MySQL connection information.
 *
 * @package    SQLSolution
 * @author     Daniel Convissor <danielc@analysisandsolutions.com>
 * @copyright  The Analysis and Solutions Company, 2001-2006
 * @version    $Name:  $
 * @link       http://www.analysisandsolutions.com/software/sql/sql.htm
 */
class SQLSolution_MySQLUser extends SQLSolution_MySQLSpecifics {

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
     * Should calling connect with the same host/username/password
     * combination cause a new link to be created?
     *
     * If false, the existing link is returned.  If true, a new connection
     * is formed.
     *
     * Used only if PHP is at version 4.3.0 or greater.
     *
     * @var  boolean
     */
    var $SQLNewLink = false;

    /**
     * MySQL configuration options.
     *
     * This is optional.  Use 0 for none or any combination of the following
     * bitwised constants: MYSQL_CLIENT_COMPRESS, MYSQL_CLIENT_IGNORE_SPACE
     * or MYSQL_CLIENT_INTERACTIVE.
     *
     * Used only if PHP is at version 4.3.0 or greater.
     *
     * @link http://php.net/ref.mysql
     * @var  integer
     */
    var $SQLClientFlags = 0;


    /**
     *
     */
    function SQLSolution_MySQLUser($Escape = 'Y', $Safe = 'N') {
        $this->SQLClassName  = get_class($this);
        $this->SQLEscapeHTML = $Escape;
        $this->SQLSafeMarkup = $Safe;
    }
}
