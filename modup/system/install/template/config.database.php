<?php

echo <<<PHP

<?php 

define('DATABASE_TYPE', '{$_POST['database']['type']}');
define('DATABASE_HOST', '{$_POST['database']['host']}');
define('DATABASE_USER', '{$_POST['database']['user']}');
define('DATABASE_PASS', '{$_POST['database']['pass']}');
define('DATABASE_NAME', '{$_POST['database']['name']}');
define('DATABASE_DSN', DATABASE_TYPE.'://'.DATABASE_USER.':'.DATABASE_PASS.'@'.DATABASE_HOST.'/'.DATABASE_NAME);

?>

PHP

?>
