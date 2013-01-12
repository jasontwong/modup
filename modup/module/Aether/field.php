<?php

class AetherField
{
    //{{{ public static function field_fieldtype_aether_inv_image($data)
    public static function field_fieldtype_aether_inv_image($data)
    {
        return array(
            array(
                'name' => 'data',
                'meta' => array(
                    'option_group' => deka(0, $data, 'option_group'),
                    'single' => deka(FALSE, $data, 'single'),
                ),
                'default_data' => '',
            ),
            array(
                'name' => 'color',
                'meta' => '',
                'default_data' => '',
            ),
        );
    }
    //}}}
    //{{{ public static function field_fieldtype_aether_lang_image($data)
    public static function field_fieldtype_aether_lang_image($data)
    {
        return array(
            array(
                'name' => 'data',
                'meta' => array(
                    'single' => deka(FALSE, $data, 'single'),
                ),
                'default_data' => '',
            ),
            array(
                'name' => 'color',
                'meta' => '',
                'default_data' => '',
            ),
        );
    }
    //}}}
    //{{{ public static function field_layout_aether_content($meta = array())
    public static function field_layout_aether_content($meta = array())
    {
        return array(
            'language' => array(
                'element' => Field::ELEMENT_SELECT,
                'options' => Aether::get_languages(),
            ),
            'content' => array(
                'attr' => array(
                    'class' => 'rte',
                ),
                'element' => Field::ELEMENT_TEXTAREA,
            ),
        );
    }

    //}}}
    //{{{ public static function field_layout_aether_text($meta = array())
    public static function field_layout_aether_text($meta = array())
    {
        return array(
            'language' => array(
                'element' => Field::ELEMENT_SELECT,
                'options' => Aether::get_languages(),
            ),
            'data' => array(
                'attr' => array(
                    'class' => 'text',
                    'type' => 'text',
                ),
                'element' => Field::ELEMENT_INPUT,
            ),
        );
    }

    //}}}
    //{{{ public static function field_layout_aether_double_content($meta = array())
    public static function field_layout_aether_double_content($meta = array())
    {
        return array(
            'language' => array(
                'element' => Field::ELEMENT_SELECT,
                'options' => Aether::get_languages(),
            ),
            'content' => array(
                'attr' => array(
                    'class' => 'rte',
                ),
                'element' => Field::ELEMENT_TEXTAREA,
            ),
            'more' => array(
                'attr' => array(
                    'class' => 'rte',
                ),
                'element' => Field::ELEMENT_TEXTAREA,
            ),
        );
    }

    //}}}
    //{{{ public static function field_layout_aether_lang_image($meta = array())
    public static function field_layout_aether_lang_image($meta = array())
    {
        $field['language'] = array(
            'element' => Field::ELEMENT_SELECT,
            'options' => Aether::get_languages(),
        );
        $field += Field::layout('filemanager_image', $meta);
        return $field;
    }

    //}}}
    //{{{ public static function field_layout_aether_inv_image($meta = array())
    public static function field_layout_aether_inv_image($meta = array())
    {
        $group_id = deka(0, $meta,'data','meta','option_group');
        $options = array();
        if ($group_id > 0)
        {
            $colors = Inventory::get_options($group_id);
            foreach ($colors as $color)
            {
                $options[$color['id']] = $color['display_name'];
            }
        }

        $field['color'] = array(
            'element' => Field::ELEMENT_SELECT,
            'options' => $options,
        );

        $field += Field::layout('filemanager_image', $meta);

        return $field;
    }

    //}}}
    //{{{ public static function field_layout_table()
    public static function field_layout_table()
    {
        return array(
            'data' => array(
                'attr' => array(
                    'class' => 'hidden table',
                    'type' => 'hidden',
                ),
                'element' => Field::ELEMENT_INPUT,
            )
        );
    }

    //}}}
    //{{{ public static function field_meta_aether_inv_image()
    public static function field_meta_aether_inv_image()
    {
        $option_groups = Inventory::get_option_groups();
        $field = '<select>';
        foreach ($option_groups as $group)
        {
            $field .= "<option value='{$group['id']}'>{$group['name']}</option>";
        }
        $field .= '</select>';
        return array(
            'option_group' => array(
                'description' => 'Option Group',
                'field' => $field,
                'type' => 'dropdown'
            ),
            'single' => array(
                'description' => '',
                'field' => "<label><input type='checkbox' value='1' /> Single Image?</label>",
                'type' => 'checkbox_boolean'
            ),
        );
    }

    //}}}
    //{{{ public static function field_meta_aether_lang_image()
    public static function field_meta_aether_lang_image()
    {
        return array(
            'single' => array(
                'description' => '',
                'field' => "<label><input type='checkbox' value='1' /> Single Image?</label>",
                'type' => 'checkbox_boolean'
            ),
        );
    }

    //}}}
    //{{{ public static function field_public_aether_content()
    public static function field_public_aether_content()
    {
        return array(
            'description' => 'Aether Content with Language',
            'meta' => FALSE,
            'name' => 'RTE with language',
        );
    }

    //}}}
    //{{{ public static function field_public_aether_text()
    public static function field_public_aether_text()
    {
        return array(
            'description' => 'Aether Title with Language',
            'meta' => FALSE,
            'name' => 'Text with language',
        );
    }

    //}}}
    //{{{ public static function field_public_aether_double_content()
    public static function field_public_aether_double_content()
    {
        return array(
            'description' => 'Aether Product Content with Language',
            'meta' => FALSE,
            'name' => '2 RTE with language',
        );
    }

    //}}}
    //{{{ public static function field_public_aether_inv_image()
    public static function field_public_aether_inv_image()
    {
        return array(
            'description' => 'Aether Image with Inventory Option',
            'meta' => TRUE,
            'name' => 'Image with Inventory Option',
        );
    }

    //}}}
    //{{{ public static function field_public_aether_lang_image()
    public static function field_public_aether_lang_image()
    {
        return array(
            'description' => 'Aether Image with Language',
            'meta' => TRUE,
            'name' => 'Image with Language',
        );
    }

    //}}}
    //{{{ public static function field_public_table()
    public static function field_public_table()
    {
        return array(
            'description' => 'Table creator (javascript based)',
            'meta' => FALSE,
            'name' => 'Table',
        );
    }

    //}}}
    //{{{ public static function field_read_table($key, $data)
    public static function field_read_table($key, $data)
    {
        $results = array();
        if ($data)
        {
            foreach ($data as $k => $v)
            {
                foreach ($v as $nk => $nv)
                {
                    foreach ($nv as $nnk => $nnv)
                    {
                        $results[$k][$nnv['akey']][] = !ake('bdata', $nnv) || is_null($nnv['bdata'])
                            ? json_decode($nnv['cdata'], TRUE)
                            : $nnv['bdata'];
                    }
                }
            }
        }
        return $results;
    }

    //}}}
}

?>
