<?php

define('DATABASE_TYPE', 'mysql');
define('DATABASE_HOST', 'localhost');
define('DATABASE_USER', 'user');
define('DATABASE_PASS', 'password');
define('DATABASE_NAME', 'name');
define('DATABASE_DSN', DATABASE_TYPE.'://'.DATABASE_USER.':'.DATABASE_PASS.'@'.DATABASE_HOST.'/'.DATABASE_NAME);

?>
