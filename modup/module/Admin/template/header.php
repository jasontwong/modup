<?php 
$title = Admin::get('title');
$header = Admin::get('header');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en-US">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link type="image/x-icon" href="/favicon.ico" rel="icon"/>
    <title><?php echo is_null($title) ? '' : $title.' &mdash; ' ?>Admin Interface (<?php echo Data::query('_Site', 'title') ?>)</title>
    <?php echo Module::h('admin_js_header') ?>
    <?php echo Module::h('admin_css') ?>
</head>

<body>

<?php Module::h('body_start') ?>

<div id='container'>

<div id='header' class='clear'>
    <?php if (strlen(Data::query('_Site', 'title'))): ?>
        <div class='site'>
        <a href='/admin/'>
            <?php if (is_file(Data::query('Admin', 'logo', 'tmp_name'))): ?>
                <img src='/file/upload/admin_logo.jpg' />
            <?php else: ?>
                <p class='title'><?php echo Data::query('_Site', 'title'); ?></p>
                <?php if (strlen(Data::query('_Site', 'description'))): ?>
                    <p class='description'><?php echo Data::query('_Site', 'description') ?></p>
                <?php endif ?>
            <?php endif; ?>
        </a>
        </div>
    <?php endif ?>
    <div class='user_ctrl'>
        Currently logged in as <strong><?php echo User::i('nice_name'); ?></strong>.<br />
        <a href='/admin/logout/'>&larr; logout</a>
    </div>
    <div class='admin'>
        <ul>
            <li id='quicklinks'>
                &darr; Quicklinks
                <ul>
                <?php if (!is_null($quicklinks = User::setting('admin', 'quicklinks'))): ?>
                    <?php foreach ($quicklinks as $href => $label): ?>
                    <li><a href='<?php echo $href; ?>'><?php echo $label; ?></a></li>
                    <?php endforeach; ?>
                <?php endif; ?>
                </ul>
            </li>
            <li><a href='/admin/'>&larr; Admin Dashboard</a></li>
        </ul>
    </div>
</div>

<div id='body' class='clear'>

    <?php include DIR_MODULE.'/Admin/template/notifications.php'; ?>
    <div class='header'><?php echo is_null($title) ? '' : $title; ?></div>
    <?php include DIR_MODULE.'/Admin/template/nav.php'; ?>

    <div id='content'>

    <?php if (!is_null($header)): ?><h1><?php echo $header ?></h1><?php endif ?>
