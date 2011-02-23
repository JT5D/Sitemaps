<?php

/**
 * 
 * Crawler Configuration File
 *
 */

 

 /**
  * MySQL Connection Settings
  */

 $mysql_server = '';
 $mysql_user = '';
 $mysql_pass = ''; 
 $mysql_db = '';
 

/**
 * No Need to Edit below here
 */
 
 /**
 * Check to ensure settings are not defaults
 */


 if ($mysql_server == ''|$mysql_user == ''|$mysql_pass==''|$mysql_db=='') die('You must enter MySQL information in config.php before continuing');


/**
 * Initiate database connection
 */

$db=mysql_connect ($mysql_server, $mysql_user, $mysql_pass) or die ('I cannot connect to the database because: ' . mysql_error());



/**
 * Select DB
 */

mysql_select_db ($mysql_db);
