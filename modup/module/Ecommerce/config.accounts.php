<?php

// this config requires at least one key to be defined
$_accounts['default'] = array();

$_accounts['orders'] = array(
    'transport' => 'smtp',
    'hostname' => 'smtp.gmail.com',
    'port' => 465,
    'username' => 'orders@example.com',
    'password' => 'password',
    'encryption' => 'ssl',
);
