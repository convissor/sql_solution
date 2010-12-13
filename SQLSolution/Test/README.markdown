Creating Test Databases
=======================
MySQL
-----
	mysql -u root -p mysql
	CREATE DATABASE sqlsolution;
	GRANT ALL ON sqlsolution.* TO sqlsolution@localhost IDENTIFIED BY 'pw';
	FLUSH PRIVILEGES;

PostgreSQL
----------
	psql template1
	CREATE USER sqlsolution;
	\password sqlsolution
	CREATE DATABASE sqlsolution OWNER sqlsolution;

SQLite
------
The test suite automatically creates the needed databases.


Settings
========
Connection settings for each Database Management System (DBMS) type are
stored in the `<DBMS>User.php` (e.g. `PostgreSQLUser.php`) files in the
`sql_solution/SQLSolution` directory.


Uncomment User Error Function
=============================
The unit testing framework depends on receiving PHP error messages when
problems happen.  But displaying error messages during normal operation
is a security problem, so they are disabled.

So before running PHPUnit, go to the bottom of `SQLSolution/General.php`
and uncomment the `user_error()` call.  Don't forget to re-comment the
line before deployment.

Tip: use our Git repository when obtaining the source code and then create
a branch for testing (`git checkout -b test`) in which you can store the
uncommented `user_error()` can and the authentication information.


Location to Run Test Files From
===============================
	cd sql_solution/SQLSolution/Test


General Tests
=============
The general tests cover the package's shared, non-database specific,
methods.

Some parts use database results, which are obtained using the MySQLi
extension/class.  If you need to use a different DBMS, change the class
name instantiated at the beginning of the `SQLSolution_Test_General::setUp()`
method found in `sql_solution/SQLSolution/Test/General.php`.

Individual Tests
----------------
	phpunit General_ParseSafeMarkupTest

All Tests
---------
	phpunit AllGeneralTests


Driver Tests
============
There is also a series of tests that can be run against each of this
package's DBMS classes.

Individual Tests
----------------
To run unit tests for a particular DBMS, call the test directly:

	phpunit Driver_PostgreSQLTest

All Tests
---------
The ambitious among us can run the tests for multiple DBMS's in one call:

	./AllDriverTests.sh

The test suite checks the settings in each <DBMS>User.php file and only
runs tests where connection information is filled in.
