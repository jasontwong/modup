<?php

ob_start('ob_gzhandler');
//{{{ defining constants
define('MODUP_SESSION', 'modup');
define('CMS_DEVELOPER', TRUE);
define('WEB_DIR_INSTALL', '/install');
define('DIR_WEB', dirname(__FILE__));
define('DIR_FILE', DIR_WEB.'/file');

define('DIR_MODUP', dirname(__FILE__).'/modup');
define('DIR_SYS', DIR_MODUP.'/system');
define('DIR_EXT', DIR_MODUP.'/extension');
define('DIR_SCR', DIR_SYS.'/scratch');
define('DIR_COM', DIR_SYS.'/scratch/compiler');
define('DIR_CTRL', DIR_MODUP.'/controller');
define('DIR_LIB', DIR_MODUP.'/library');
define('DIR_MODEL', DIR_MODUP.'/model');
define('DIR_MODULE', DIR_MODUP.'/module');
define('DIR_TMPL', DIR_MODUP.'/template');
define('DIR_VIEW', DIR_MODUP.'/view');
define('MODUP_VERSION', '0.5.0');
error_reporting(CMS_DEVELOPER ? E_ALL : 0);

//}}}
//{{{ disecting the URI
$ru = &$_SERVER['REQUEST_URI'];
$qmp = strpos($ru, '?');
list($path, $params) = $qmp === FALSE
    ? array($ru, NULL)
    : array(substr($ru, 0, $qmp), substr($ru, $qmp + 1));
$parts = explode('/', $path);
$i = 0;
foreach ($parts as $part)
{
    if (strlen($part) && $part !== '..' && $part !== '.')
    {
        define('URI_PART_'.$i++, $part);
    }
}
define('URI_PARAM', isset($params) ? '' : $params);
define('URI_PARTS', $i);
define('URI_PATH', $path);
define('URI_REQUEST', $_SERVER['REQUEST_URI']);

//}}}
//{{{ start Doctrine ORM, data manager, and other model directories
include DIR_SYS.'/config.database.php';
include DIR_LIB.'/doctrine/Doctrine.php';
spl_autoload_register(array('Doctrine', 'autoload'));
$dbm = Doctrine_Manager::getInstance();
if (URI_PARTS === 0 || (URI_PARTS > 0 && strpos(URI_PATH, WEB_DIR_INSTALL) !== 0))
{
    $dbm->setAttribute(Doctrine::ATTR_AUTOLOAD_TABLE_CLASSES, TRUE);
}
Doctrine::loadModels(DIR_SYS.'/model');
if (is_dir(DIR_MODEL))
{
    Doctrine::loadModels(DIR_MODEL);
}
$dbm->connection(DATABASE_DSN);
$dbm->getcurrentConnection()->setAttribute(Doctrine::ATTR_VALIDATE, Doctrine::VALIDATE_CONSTRAINTS);
$dbm->getCurrentConnection()->addRecordListener(new ModelHookListener());

//}}}
//{{{ loading modules
include DIR_SYS.'/function.php';
spl_autoload_register('modup_autoload');
if ($modules = Data::query('_System', 'modules'))
{
    if (is_array($modules))
    {
        foreach ($modules as $mod)
        {
            $dir = DIR_MODULE.'/'.$mod.'/model';
            if (is_dir($dir))
            {
                Doctrine::loadModels($dir);
            }
        }
    }
}
$tz = Data::query('_Site', 'time_zone');
date_default_timezone_set(is_null($tz) ? 'America/New_York' : $tz);

//}}}
//{{{ routing and other init
if (is_file(DIR_SYS.'/config.misc.php'))
{
    include DIR_SYS.'/config.misc.php';
}
$installed = !is_null(Data::query('_System', 'version'));
if (CMS_DEVELOPER || !$installed)
{
    Router::add(
        '#^'.WEB_DIR_INSTALL.'/([^/]+/)?$#', 
        DIR_SYS.'/install/index.php', 
        Router::ROUTE_PCRE
    );
    Router::add(
        '#^'.WEB_DIR_INSTALL.'/static/([^/]+)/$#', 
        DIR_SYS.'/install/controller/static/${1}.php', 
        Router::ROUTE_PCRE
    );
    if ((URI_PARTS > 1 && '/'.URI_PART_1 !== WEB_DIR_INSTALL) && !CMS_DEVELOPER)
    {
        header('Location: '.WEB_DIR_INSTALL.'/start/');
        exit;
    }
}
if ($installed)
{
    session_name(MODUP_SESSION);
    session_start();
    Module::h('active');
    $routes = Module::h('routes');
    foreach ($routes as $mod => $rs)
    {
        foreach ($rs as $r)
        {
            Router::add(
                $r[0],
                $r[1],
                deka(Router::ROUTE_STATIC, $r, 2),
                deka(Router::PRIORITY_NORMAL, $r, 3),
                $mod
            );
        }
    }
}

include DIR_SYS.'/config.routes.php';

Module::h('start');
if ($ctrl = Router::controller()) 
{
    include $ctrl;
}
else 
{
    header("HTTP/1.0 404 Not Found");
    if (is_file(DIR_CTRL . '/404.php'))
    {
        include DIR_CTRL . '/404.php';
    }
}
Module::h('end');

Data::save();

//}}}

?>
