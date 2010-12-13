Overview
========

The SQL Solution is a set of open source PHP classes to simplify integrating
databases with web pages.  Provides a powerful, user friendly, platform
independent API (Application Programming Interface) for MySQL, PostgreSQL,
SQLite, SQLite3 and ODBC database management systems.  Output is XHTML
compliant and handicapped accessible.

Some features include:

* Automatically connecting to hosts and databases.
* Querying.
* Trapping errors and providing detailed descriptions about them.
* Escaping HTML characters before output.
* Converting Safe Markup tags and URI's into real HTML.
* Formatting results into HTML tables or lists.
* Generating HTML form elements based on actual database content.
* Producing output that's XML/XHTML compliant and accessible by disabled persons.
* Fostering a portable and ungradable framework.


Installation
============
Location
--------
Place the `autoload.php` file and the `SQLSolution` directory in your include
directory.

Autoload
--------
The SQL Solution's files follow the PEAR naming convention, where the "_"
in class names become "/" in the file paths.  So, for example, the
SQLSolution_General class can be found in the file named
`<include_dir>/SQLSolution/General.php`.  This permits the use of a standard
autoload function.

In fact, the SQL Solution requires the use of an autoloader.  A sample
function is provided in `sql_solution/autoload.php`.  The given function
checks the current directory and subdirectories first, then tries via the
include_path.

Settings
--------
Connection settings for each Database Management System (DBMS) type are
stored in the `<DBMS>User.php` (e.g. `PostgreSQLUser.php`) files in the
`sql_solution/SQLSolution` directory.

More Info
---------
The full manual is on line at
http://www.analysisandsolutions.com/software/sql/sql-man.htm
