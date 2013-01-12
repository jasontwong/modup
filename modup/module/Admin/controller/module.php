<?php

Module::h('admin_start');
$page = Module::h('admin_module_page', URI_PART_2);

include DIR_MODULE.'/Admin/view/module.php';
Module::h('admin_end');

?>
