<?php

Admin::set('title', 'Create New Entry');

$cett = Doctrine::getTable('ContentEntryType');
$entry_type = $cett->find(URI_PART_4);
if (!$entry_type)
{
    header('Location: /admin/');
    exit;
}
Admin::set('header', 'Add a new '.$entry_type->name);
$cfmt = Doctrine::getTable('ContentFieldMeta');
$field_groups = $cett->fieldLayout(URI_PART_4);

if ($user_access = User::has_perm('add content entries type', 'add content entries type-'.$entry_type->id))
{
    $user_access_level = Content::ACCESS_ALLOW;
}
else
{
    $user_access_level = Content::ACCESS_DENY;
}

$module_access_level = Module::h('content_entry_add_access', Module::TARGET_ALL, $entry_type->id);
$access_level = max($module_access_level, $user_access_level);

if ($access_level < Content::ACCESS_ALLOW)
{
    Admin::set('title', 'Permission Denied');
    Admin::set('header', 'Permission Denied');
    $efh = '';
    return;
}

//{{{ layout
$layout = new Field();
$layout_sidebar = Module::h('content_entry_sidebar_new', Module::TARGET_ALL, URI_PART_4);
$esides = array();
foreach ($layout_sidebar as $mod => $groups)
{
    if (!is_array($groups))
    {
        continue;
    }
    foreach ($groups as $group)
    {
        $esides[] = $group;
        $glayout = $group['fields'];
        $layout->add_layout($glayout, $glayout['name']);
    }
}
$layout->add_layout(
    array(
        'field' => Field::layout('text'),
        'name' => 'title',
        'type' => 'text'
    )
);
$layout->add_layout(
    array(
        'field' => Field::layout('hidden'),
        'name' => 'content_entry_type_id',
        'type' => 'hidden',
        'value' => array(
            'data' => URI_PART_4
        )
    )
);
$layout->add_layout(
    array(
        'field' => Field::layout('text'),
        'name' => 'slug',
        'type' => 'text'
    )
);
$layout->add_layout(
    array(
        'field' => Field::layout('date'),
        'name' => 'start_date',
        'type' => 'date'
    )
);
$layout->add_layout(
    array(
        'field' => Field::layout('date'),
        'name' => 'end_date',
        'type' => 'date'
    )
);
$layout->add_layout(
    array(
        'field' => Field::layout('submit_reset'),
        'name' => 'submit',
        'type' => 'submit_reset'
    )
);
// {{{ custom fields
foreach ($field_groups as $field_group)
{
    $rows = array();
    foreach ($field_group['fields'] as $fid => $field)
    {
        $cfm = $cfmt->findByContentFieldTypeId($field['id'])->toArray();
        $fmeta = $fval = array();
        foreach ($cfm as $fm)
        {
            $fmeta[$fm['name']]['meta'] = $fm['meta'];
            if (strlen($fm['label']))
            {
                $fmeta[$fm['name']]['label'] = $fm['label'];
            }
            /* This isn't doing anything...
            $fmeta[$fm['name']]['class'] = $fm['required']
                ? 'required_field'
                : '';
            */
            $fval[$fm['name']] = $fm['default_data'];
        }
        $layout->add_layout(
            array(
                'field' => Field::layout($field['type'], $fmeta),
                'name' => $field['id'],
                'type' => $field['type'],
                'array' => (boolean)$field['multiple'],
                'value' => $fval
            )
        );
        if (isset($_POST['data']))
        {
            $layout->merge($_POST['data']);
        }
        $flayout = $layout->get_layout($field['id']);
        switch ($flayout['type'])
        {
            case 'file':
                $flayout['hidden']['delete'] = $flayout['array']
                    ? array(0 => TRUE)
                    : TRUE;
            break;
        }
        $row['fields'] = $flayout;
        $row['label']['text'] = $field['name'];
        if (strlen($field['description']))
        {
            $row['description']['text'] = $field['description'];
        }
        if ($flayout['array'])
        {
            $row['row']['attr']['class'] = 'content_multiple';
        }
        $rows[] = $row;
        unset($row);
    }
    if (!empty($rows))
    {
        $cfgroups[] = array(
            'attr' => array(
                'class' => 'clear tabbed'
            ),
            'label' => array(
                'text' => $field_group['name']
            ),
            'rows' => $rows
        );
    }
}

// }}}
//}}}
//{{{ form submission
if (isset($_POST['entry']))
{
    try
    {
        $content['entry'] = $layout->acts('post', $_POST['entry']);
        $content['meta'] = $layout->acts('post', $_POST['meta']);
        if (!isset($_POST['data']))
        {
            $_POST['data'] = array();
        }
        $content['data'] = $layout->acts('save', $_POST['data'], $content['entry']);
        $layout->merge($_POST['entry']);
        $layout->merge($_POST['meta']);
        $layout->merge($_POST['data']);
        $eid = Content::save_entry($content);
        if ($eid !== FALSE)
        {
            $content['meta']['content_entry_meta_id'] = $eid;
            Module::h('content_entry_sidebar_new_process', Module::TARGET_ALL, $layout, $content['meta'], $_POST['module']);

            //{{{ Cache: updating block
            $content_type = Content::get_entry_type_details_by_id($content['meta']['content_entry_type_id']);
            $content_type_name = $content_type['type']['name'];

            // Cache: update single entry
            $entry = Content::get_entry_details_by_id($eid, FALSE);
            Cache::set('entry:'.$eid, $entry, 0, 'Content');

            // Cache: update all entries for content type
            $entries = Content::get_entries_details_by_type_id($content['meta']['content_entry_type_id'], array(), FALSE);
            Cache::set($content_type_name.' - entries', $entries, 0, 'Content');

            // Cache: update ids slugs map for content type
            $ids_slugs = Content::get_entries_slugs($content_type_name, FALSE);
            Cache::set($content_type['type']['name'].' - ids slugs', $ids_slugs, 0, 'Content');
            //}}}

            Module::h('content_entry_new_finish', Module::TARGET_ALL, $content['meta']);
            header('Location: /admin/module/Content/edit_entry/'.$eid.'/');
            exit;
        }
    }
    catch (Doctrine_Validator_Exception $e)
    {
        $errors_array = $entry_title->getErrorStack()->toArray();
        $errors = array();
        foreach ($errors_array['validate'] as $error)
        {
            $errors[] = $error;
        }
        Admin::notify(Admin::TYPE_ERROR, $errors);
    }
}
//}}}
//{{{ form build
$eform = new FormBuilderRows;
$eform->attr = array(
    'action' => URI_PATH,
    'enctype' => 'multipart/form-data',
    'method' => 'post'
);

//$form_sidebar = Module::h('content_entry_sidebar_new', Module::TARGET_ALL, URI_PART_4);

foreach ($esides as $eside)
{
    $class = slugify($eside['label']['text']);
    $class .= $class === 'taxonomy'
        ? ' collapsible'
        : '';
    $eform->add_group(
        array(
            'attr' => array(
                'class' => $class
            ),
            'rows' => array(
                $eside
            )
        ),
        'module'
    );
}
$eform->add_group(
    array(
        'attr' => array(
            'class' => 'tsc'
        ),
        'rows' => array(
            array(
                'row' => array(
                    'attr' => array(
                        'class' => 'title'
                    )
                ),
                'fields' => $layout->get_layout('title'),
                'label' => array(
                    'text' => 'Title'
                )
            ),
            array(
                'row' => array(
                    'attr' => array(
                        'class' => 'slug'
                    )
                ),
                'fields' => $layout->get_layout('slug'),
                'label' => array(
                    'text' => 'URL Slug'
                )
            )
        )
    ),
    'entry'
);
if (isset($cfgroups))
{
foreach ($cfgroups as $cfgroup)
{
    $eform->add_group($cfgroup, 'data');
}
}
$eform->add_group(
    array(
        'attr' => array(
            'class' => 'hiddens'
        ),
        'rows' => array(
            array(
                'fields' => $layout->get_layout('content_entry_type_id')
            )
        )
    ),
    'meta'
);
/*
if ($entry_type->status || $entry_type->flagging)
{
    $srows[] = array(
        'fields' => $layout->get_layout('date_control'),
        'label' => array(
            'text' => 'Option to use with date'
        )
    );
    $srows[] = array(
        'fields' => $layout->get_layout('start_date'),
        'label' => array(
            'text' => 'Begin with this date'
        )
    );
    $srows[] = array(
        'fields' => $layout->get_layout('end_date'),
        'label' => array(
            'text' => 'End with this date'
        )
    );
}
if (isset($srows))
{
    $eform->add_group(
        array(
            'attr' => array(
                'class' => 'status_flags'
            ),
            'rows' => $srows,
        ),
        'meta'
    );
}
*/

$eform->add_group(
    array(
        'rows' => array(
            array(
                'fields' => $layout->get_layout('submit')
            )
        )
    ),
    'form'
);

$efh = $eform->build();

//}}}

?>
