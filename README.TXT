Calendar Solution is a set of open source PHP classes and database scripts
that provide a simple way to post events on your website or intranet.

The existing output formats include a calendar view, detailed list,
summary table and bullet list. Any PHP programmer can quickly add
custom formats as well.

Calendar Solution can use MySQL (via PHP's mysql or mysqli extensions),
PostgreSQL or SQLite (via PHP's sqlite or sqlite3 extensions) as the
database system.

Caching can be enabled if access to a Memcached instance is available.
Other cache systems can be used by writing a simple wrapper class.

Directory Structure:
calendar/  = pages to be displayed by web servers
include/   = classes to perform the tasks requested by the pages

The stable release branch is "3".  Development happens on "master".
The old release branch is "2.1".

Please see include/CalendarSolution/doc/README.TXT for more information.
