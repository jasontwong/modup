<?php

if (User::perm('admin access'))
{
    Admin::set('title', 'Admin Dashboard');
    $dashboard = Module::h('admin_dashboard');

    Module::h('admin_start');
    include DIR_MODULE.'/Admin/view/index.php';
    Module::h('admin_end');
}
else
{
    header('Location: /admin/login/');
}

?>
