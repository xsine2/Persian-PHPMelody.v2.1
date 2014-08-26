<?php

//-- MySQL Settings --//
/** MySQL database name */
$db_name = 'exe.ir';

/** MySQL database username */
$db_user = 'root';

/** MySQL database password */
$db_pass = '';

/** MySQL hostname */
$db_host = 'localhost';

// Full URL without any trailing slash (e.g http://www.example.com)
define('_URL', 'http://exe.ir');	

//-- Customer ID --//
define('_CUSTOMER_ID', 'YOUR_CUSTOMER_ID');	

error_reporting(E_ALL & ~E_NOTICE &  ~E_STRICT); // Production
//error_reporting(E_ALL & ~E_NOTICE); // Development

// ========================================================= //
//-- MySQL Backup Directory --//
define('BKUP_DIR', 'temp');	//	WITHOUT any trailing slash
define('_POWEREDBY', 1);

@header('CONTENT-TYPE: text/html; charset=utf-8');
define('ABSPATH', dirname(__FILE__).'/'); 
require_once( ABSPATH.'include/settings.php');
require_once( ABSPATH.'include/Smarty/plugins/jdf.class.php');