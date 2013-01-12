<?php

Admin::set('title', 'Edit Entry');

$cemt = Doctrine::getTable('ContentEntryMeta');

if (eka($_GET, 'revision'))
{
    $entry = $cemt->findEntryRevision(URI_PART_4, $_GET['revision']); 
}
elseif (URI_PARTS > 5)
{
    $entry = $cemt->findEntryRevision(URI_PART_4, URI_PART_5);
}
else
{
    $entry = $cemt->findCurrentEntry(URI_PART_4);
}

$entry_type = Content::get_entry_type_by_id(
    $entry['content_entry_type_id'],
    array('select' => array('ety.id', 'ety.name'))
);
Admin::set('header', 'Edit '.$entry_type['name']);

if ($user_access = User::has_perm('edit content entries type', 'edit content entries type-'.$entry_type['id']))
{
    $user_access_level = Content::ACCESS_EDIT;
}
elseif ($user_access = User::has_perm('view content entries type', 'view content entries type-'.$entry_type['id']))
{
    $user_access_level = Content::ACCESS_VIEW;
}
else
{
    $user_access_level = Content::ACCESS_DENY;
}

$module_access_level = Module::h('content_entry_edit_access', Module::TARGET_ALL, $entry_type['id'], URI_PART_4);
$access_level = max($module_access_level, $user_access_level);

if ($access_level === Content::ACCESS_DENY)
{
    Admin::set('title', 'Permission Denied');
    Admin::set('header', 'Permission Denied');
    $efh = '';
    return;
}

//{{{ layout
$layout = new Field();
$entry_sidebar = Module::h('content_entry_sidebar_edit', Module::TARGET_ALL, $entry);
$esides = array();
foreach ($entry_sidebar as $mod => $groups)
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
        'type' => 'text',
        'value' => array(
            'data' => $entry['title']
        )
    )
);
$layout->add_layout(
    array(
        'field' => Field::layout('hidden'),
        'name' => 'content_entry_meta_id',
        'type' => 'hidden',
        'value' => array(
            'data' => $entry['id']
        )
    )
);
$layout->add_layout(
    array(
        'field' => Field::layout('hidden'),
        'name' => 'content_entry_type_id',
        'type' => 'hidden',
        'value' => array(
            'data' => $entry['content_entry_type_id']
        )
    )
);
$layout->add_layout(
    array(
        'field' => Field::layout('text'),
        'name' => 'slug',
        'type' => 'text',
        'value' => array(
            'data' => $entry['slug']
        )
    )
);
$layout->add_layout(
    array(
        'field' => Field::layout('date'),
        'name' => 'start_date',
        'type' => 'date',
        'value' => array(
            'data' => $entry['start_date']
        )
    )
);
$layout->add_layout(
    array(
        'field' => Field::layout('date'),
        'name' => 'end_date',
        'type' => 'date',
        'value' => array(
            'data' => $entry['end_date']
        )
    )
);
$layout->add_layout(
    array(
        'field' => Field::layout('submit_reset'),
        'name' => 'submit',
        'type' => 'submit_reset'
    )
);
$layout->add_layout(
    array(
        'field' => Field::layout(
            'submit',
            array(
                'data' => array(
                    'text' => 'Delete this entry'
                )
            )
        ),
        'name' => 'delete',
        'type' => 'submit'
    )
);
$layout->add_layout(
    array(
        'field' => Field::layout(
            'submit',
            array(
                'data' => array(
                    'text' => 'Duplicate this entry'
                )
            )
        ),
        'name' => 'duplicate',
        'type' => 'submit'
    )
);
// {{{ custom fields
$cfmt = Doctrine::getTable('ContentFieldMeta');
foreach ($entry['field_groups'] as $field_group)
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
                'value' => array_merge($fval, $field['data'])
            )
        );
        if (isset($_POST['data']))
        {
            $layout->merge($_POST['data']);
            $_POST['data'][$field['id']]['_content_entry_meta_id'] = $entry['id'];
        }
        $flayout = $layout->get_layout($field['id']);
        switch ($flayout['type'])
        {
            case 'table':
                $fdata = deka('', $flayout, 'value', 'data', 0);
                $flayout['value']['data'][0] = json_encode(is_null($fdata) ? '' : $fdata);
                break;
            case 'relationship':
            case 'relationship_multiple':
                if (!deka(FALSE, $fmeta, 'data', 'meta', 'ordering'))
                {
                    break;
                }
            case 'list_double_ordered':
                $flayout['value']['data'] = deka(array(), $flayout, 'value', 'data');
                if (is_array($flayout['value']['data']))
                {
                    $flvalues = array();
                    foreach ($flayout['value']['data'] as $flid)
                    {
                        if (eka($flayout, 'field', 'data', 'options', $flid))
                        {
                            $flvalues[$flid] = $flayout['field']['data']['options'][$flid];
                        }
                    }
                    $flayout['field']['data']['options'] = $flvalues + $flayout['field']['data']['options'];
                }
            break;
            case 'file':
                $fdata = deka(array(0 => ''), $flayout, 'value', 'data');
                foreach ($fdata as $k => $v)
                {
                    if ($flayout['array'] && ake(0,$v) && strlen($v[0]))
                    {
                        $flayout['html_before']['data'][$k] = '<a href="/file/upload/'.$v[0].'" target="_blank">Open File</a><br />';
                    }
                    elseif (!$flayout['array'] && strlen($v))
                    {
                        $flayout['html_before']['data'] = '<a href="/file/upload/'.$v.'" target="_blank">Open File</a><br />';
                    }
                    else
                    {
                        $flayout['hidden']['delete'] = $flayout['array']
                            ? array($k => TRUE)
                            : TRUE;
                    }
                }
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
// }}}
//{{{ form submission
if (isset($_POST['form']))
{
    if ($access_level === Content::ACCESS_EDIT)
    {
        try
        {
            $form = $layout->acts('post', $_POST['form']);
            if (ake('delete', $form))
            {
                header('Location: /admin/module/Content/delete_entry/'.URI_PART_4.'/');
                exit;
            }
            elseif (ake('submit', $form))
            {
                $entry_data = $layout->acts('post', $_POST['entry']);
                $meta = $layout->acts('post', $_POST['meta']);
                $data = $layout->acts('save', $_POST['data'], $entry_data);
                $cemt->saveEntryRevision($entry_data, $data, $meta);

                if (isset($_POST['module']))
                {
                    Module::h('content_entry_sidebar_edit_process', Module::TARGET_ALL, $layout, $meta, $_POST['module']);
                }

                //{{{ Cache: updating block
                $entry_meta_id = $meta['content_entry_meta_id'];
                $entry_type_id = $meta['content_entry_type_id'];
                $content_type = Content::get_entry_type_details_by_id($entry_type_id);
                $content_type_name = $content_type['type']['name'];

                // Cache: update single entry
                $entry = Content::get_entry_details_by_id($entry_meta_id, FALSE);
                Cache::set('entry:'.$entry_meta_id, $entry, 0, 'Content');

                // Cache: update all entries for content type
                $entries = Content::get_entries_details_by_type_id($entry_type_id, array(), FALSE);
                foreach ($entries as &$row)
                {
                    if ($row['entry']['id'] == $entry_meta_id)
                    {
                        $row = $entry;
                        $row['entry']['type_id'] = $entry_type_id;
                    }
                }
                Cache::set($content_type_name.' - entries', $entries, 0, 'Content');

                // Cache: update ids slugs map for content type
                $ids_slugs = Content::get_entries_slugs($content_type_name, FALSE);
                foreach ($ids_slugs as &$id_slug)
                {
                    if ($id_slug['id'] == $entry['entry']['id'])
                    {
                        $id_slug['title'] = $entry['entry']['title'];
                        $id_slug['slug'] = $entry['entry']['slug'];
                    }
                }
                Cache::set($content_type['type']['name'].' - ids slugs', $ids_slugs, 0, 'Content');
                //}}}

                Module::h('content_entry_edit_finish', Module::TARGET_ALL, $meta);

                header('Location: /admin/module/Content/edit_entry/'.URI_PART_4.'/');
                exit;
            }
            elseif (ake('duplicate', $form))
            {
                $content['entry'] = $layout->acts('post', $_POST['entry']);
                $content['entry']['title'] .= ' COPY';
                $content['entry']['slug'] .= '-copy';
                $content['meta'] = $layout->acts('post', $_POST['meta']);
                $content['data'] = $layout->acts('save', $_POST['data'], $content['entry']);
        
                $entry_meta = new ContentEntryMeta;
                $entry_meta->merge($content['meta']);
                if ($entry_meta->isValid())
                {
                    $entry_meta->save();
                    $content['meta']['content_entry_meta_id'] = $eid = $entry_meta->id;
        
                    $entry_title = new ContentEntryTitle;
                    $entry_title->merge($content['entry']);
                    $entry_title->content_entry_meta_id = $eid;
                    $entry_title->save();
        
                    $fields = Doctrine::getTable('ContentFieldData');
                    $fields->saveEntryData($eid, $content['data'], 0);
        
                    Module::h('content_entry_sidebar_new_process', Module::TARGET_ALL, $layout, $content['meta'], $_POST['module']);
        
                    //{{{ Cache: updating block
                    $entry_meta_id = $eid;
                    $entry_type_id = $content['meta']['content_entry_type_id'];
                    $content_type = Content::get_entry_type_details_by_id($entry_type_id);
                    $content_type_name = $content_type['type']['name'];

                    // Cache: update single entry
                    $entry = Content::get_entry_details_by_id($entry_meta_id, FALSE);
                    Cache::set('entry:'.$entry_meta_id, $entry, 0, 'Content');

                    // Cache: update all entries for content type
                    $entries = Content::get_entries_details_by_type_id($entry_type_id, array(), FALSE);
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
        }
        catch (Doctrine_Validator_Exception $e)
        {
            var_dump($e->getMessage());
            $errors_array = $entry_title->getErrorStack()->toArray();
            $errors = array();
            foreach ($errors_array['validate'] as $error)
            {
                $errors[] = $error;
            }
            Admin::notify(Admin::TYPE_ERROR, $errors);
        }
    }
    else
    {
        Admin::notify(Admin::TYPE_ERROR, 'You do not have access to save');
    }
}
elseif (isset($_POST['do']))
{
    switch ($_POST['do'])
    {
        case 'jump':
            header('Location: /admin/module/Content/edit_entry/'.URI_PART_4.'/'.$_POST['revision'].'/');
            exit;
        break;
        case 'set':
            if ($access_level === Content::ACCESS_EDIT)
            {
                $cemt->setEntryRevision(URI_PART_4, $_POST['revision']);
                //{{{ Cache: updating block
                $entry_meta_id = URI_PART_4;
                $entry_type_info = Content::get_entry_type_by_entry_id($entry_meta_id);
                $entry_type_id = $entry_type_info['entry_type_id'];
                $content_type = Content::get_entry_type_details_by_id($entry_type_id);
                $content_type_name = $content_type['type']['name'];

                // Cache: update single entry
                $entry = Content::get_entry_details_by_id($entry_meta_id, FALSE);
                Cache::set('entry:'.$entry_meta_id, $entry, 0, 'Content');

                // Cache: update all entries for content type
                $entries = Content::get_entries_details_by_type_id($entry_type_id, array(), FALSE);
                Cache::set($content_type_name.' - entries', $entries, 0, 'Content');

                // Cache: update ids slugs map for content type
                $ids_slugs = Content::get_entries_slugs($content_type_name, FALSE);
                Cache::set($content_type['type']['name'].' - ids slugs', $ids_slugs, 0, 'Content');
                //}}}
                header('Location: /admin/module/Content/edit_entry/'.URI_PART_4.'/');
                exit;
            }
        break;
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
            ),
            array(
                'fields' => $layout->get_layout('content_entry_meta_id')
            )
        )
    ),
    'meta'
);
if ($access_level === Content::ACCESS_EDIT)
{
    $eform->add_group(
        array(
            'attr' => array(
                'class' => 'buttons'
            ),
            'rows' => array(
                array(
                    'fields' => $layout->get_layout('submit')
                ),
                array(
                    'row' => array(
                        'attr' => array(
                            'class' => 'delete'
                        )
                    ),
                    'fields' => $layout->get_layout('delete')
                ),
                array(
                    'row' => array(
                        'attr' => array(
                            'class' => 'duplicate'
                        )
                    ),
                    'fields' => $layout->get_layout('duplicate')
                )
            )
        ),
        'form'
    );
}

$efh = $eform->build();

//}}}

?>
