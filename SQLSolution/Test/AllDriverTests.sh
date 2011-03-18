#! /bin/bash

# Executes the phpunit tests for each DBMS the SQL Solution supports
#
# Usage:  ./AllDriverTests.sh
#
# Author: Daniel Convissor <danielc@analysisandsolutions.com>
# Copyright: The Analysis and Solutions Company, 2001-2011
# License: http://www.analysisandsolutions.com/software/license.htm Simple Public License
# Link: http://www.analysisandsolutions.com/software/sql/sql.htm


dbmss="MySQL
MySQLi
ODBC
PostgreSQL
SQLite3
SQLite"

for dbms in $dbmss
do
	echo ""
	echo "=============   ABOUT TO TEST $dbms   ============="
	test=Driver_"$dbms"Test
	phpunit $test
done
