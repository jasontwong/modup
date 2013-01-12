<?php

/**
 * Email configuration mainly for Swift Mailer
 */

// smtp, sendmail, mail
define('EMAIL_TRANSPORT', 'smtp');

// only needed for smtp emailing
define('EMAIL_HOSTNAME', 'smtp.gmail.com');
define('EMAIL_PORT', 465);
define('EMAIL_USERNAME', 'username@gmail.com');
define('EMAIL_PASSWORD', 'password');
define('EMAIL_ENCRYPTION', 'ssl');

?>
