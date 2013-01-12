<?php

//{{{ prepare database data from file and or POST
$dbtypes = array(
    'mysql',
    'pgsql',
    'sqlite'
);
$db = array(
    'type' => '',
    'host' => '',
    'user' => '',
    'pass' => '',
    'name' => '' 
);

$dbfile = DIR_SYS.'/config.database.php';
$valid['writeable'] = is_writeable($dbfile);
if (is_readable($dbfile))
{
    include_once $dbfile;
    $db = $dbf = array(
        'type' => defined('DATABASE_TYPE') ? DATABASE_TYPE : 'mysql',
        'host' => defined('DATABASE_HOST') ? DATABASE_HOST : 'localhost',
        'user' => defined('DATABASE_USER') ? DATABASE_USER : '',
        'pass' => defined('DATABASE_PASS') ? DATABASE_PASS : '',
        'name' => defined('DATABASE_NAME') ? DATABASE_NAME : ''
    );
    $valid['file'] = test_db_settings($dbf);
}
else
{
    $valid['file'] = FALSE;
}

$layout = new Field();
$layout->add_layout(
    array(
        'field' => Field::layout(
            'dropdown', 
            array(
                'data' => array(
                    'options' => array_combine($dbtypes, $dbtypes)
                )
            )
        ),
        'name' => 'type',
        'type' => 'dropdown',
        'value' => array('data' => $db['type'])
    )
);
$layout->add_layout(
    array(
        'field' => Field::layout('text'),
        'name' => 'host',
        'type' => 'text',
        'value' => array('data' => $db['host'])
    )
);
$layout->add_layout(
    array(
        'field' => Field::layout('text'),
        'name' => 'user',
        'type' => 'text',
        'value' => array('data' => $db['user'])
    )
);
$layout->add_layout(
    array(
        'field' => Field::layout('password'),
        'name' => 'pass',
        'type' => 'password',
        'value' => array('data' => $db['pass'])
    )
);
$layout->add_layout(
    array(
        'field' => Field::layout('text'),
        'name' => 'name',
        'type' => 'text',
        'value' => array('data' => $db['name'])
    )
);
$layout->add_layout(
    array(
        'field' => Field::layout(
            'submit_reset', 
            array(
                'submit' => array(
                    'text' => 'Save',
                    'label' => ''
                )
            )
        ),
        'name' => 'submit',
        'type' => 'submit_reset'
    )
);

if (isset($_POST['database']))
{
    $db = $dbp = &$_POST['database'];
    //{{{ process $_POST if good
    if ($valid['post'] = test_db_settings($dbp))
    {
        ob_start();
        include DIR_SYS.'/install/template/config.database.php';
        file_put_contents(DIR_SYS.'/config.database.php', ob_get_clean());
    }

    //}}}
}
else
{
    $valid['post'] = FALSE;
}

//}}}
//{{{ conclusion?
if ($valid['writeable'])
{
    if (isset($_POST['database']))
    {
        if ($valid['post'])
        {
            $is_valid = TRUE;
            $messages['success'][] = 'The settings have been saved.';
        }
        else
        {
            if ($valid['file'])
            {
                $is_valid = TRUE;
                $messages['success'][] = 'The settings are not good. But the details in the conf/database.php file are. If you wish to continue using the settings in that file, click proceed.';
            }
            else
            {
                $is_valid = FALSE;
                if (is_readable($dbfile))
                {
                    $messages['notice'][] = 'The settings submitted and the details in the conf/database.php are not good. Please enter in the correct details below.';
                }
                else
                {
                    $messages['notice'][] = 'The settings submitted are not good. Please try again.';
                }
            }
        }
    }
    else
    {
        if ($valid['file'])
        {
            $is_valid = TRUE;
            $messages['success'][] = 'The settings in conf/database.php are good. You may proceed or enter other settings with the form below.';
        }
        else
        {
            $is_valid = FALSE;
            $messages['notice'][] = 'Please enter the database settings below.';
        }
    }
}
else
{
    if (isset($_POST['database']))
    {
        if ($valid['post'])
        {
            if ($valid['file'])
            {
                $is_valid = TRUE;
                $messages['notice'][] = 'The settings in conf/database.php are good, but they cannot be overwritten with your submission. You may proceed with the settings in the file if you would like.';
            }
            else
            {
                $is_valid = FALSE;
                $messages['notice'][] = 'The settings submitted are good. But the conf/database.php file is not writeable. Please grant write permissions for that file.';
            }
        }
        else
        {
            if ($valid['file'])
            {
                $is_valid = TRUE;
                $messages['notice'][] = 'The settings submitted are not good. But the conf/database.php settings are. To use these settings click proceed.';
            }
            else
            {
                $is_valid = FALSE;
                $messages['notice'][] = 'The settings submitted are not good. Please try again.';
                $messages['notice'][] = 'The conf/database.php file is not writeable. Please grant write permissions to this file.';
            }
        }
    }
    else
    {
        if ($valid['file'])
        {
            $is_valid = TRUE;
            $messages['success'][] = 'The settings in conf/database.php are good. If you want to use the file settings click proceed.';
        }
        else
        {
            $is_valid = FALSE;
            $messages['notice'][] = 'The settings in conf/database.php are not good, and cannot be written. Please either give write permissions or change the settings for that file.';
        }
    }
}

//}}}
//{{{ build form
$form = new FormBuilderRows;
$form->attr = array(
    'action' => '/install/database/',
    'method' => 'POST'
);
$form->add_group(
    array(
        'rows' => array(
            array(
                'label' => array(
                    'text' => 'Type'
                ),
                'fields' => $layout->get_layout('type')
            ),
            array(
                'label' => array(
                    'text' => 'Host'
                ),
                'fields' => $layout->get_layout('host')
            ),
            array(
                'label' => array(
                    'text' => 'User'
                ),
                'fields' => $layout->get_layout('user')
            ),
            array(
                'label' => array(
                    'text' => 'Pass'
                ),
                'fields' => $layout->get_layout('pass')
            ),
            array(
                'label' => array(
                    'text' => 'Host'
                ),
                'fields' => $layout->get_layout('name')
            ),
        )
    ),
    'database'
);
$form->add_group(
    array(
        'rows' => array(
            array(
                'fields' => $layout->get_layout('submit')
            )
        )
    )
);
$fh = $form->build();

//}}}

?>
