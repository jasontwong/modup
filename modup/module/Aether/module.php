<?php

class Aether
{
    //{{{ properties
    public static $languages = array(
        'EN' => 'English', 
        'FR' => 'French', 
        'DE' => 'German', 
        'JP' => 'Japanese'
    );
    public static $currencies = array(
        'USD' => 'USD', 
        'YEN' => 'YEN', 
        'GBP' => 'GBP'
    );
    //}}}
    //{{{ constants 
    const MODULE_AUTHOR = 'Jason T. Wong';
    const MODULE_DESCRIPTION = 'Aether Custom Module';
    const MODULE_WEBSITE = '';
    const MODULE_DEPENDENCY = '';
    //}}}
    //{{{ constructor
    /**
     * @param int $state current state of module manager
     */
    public function __construct()
    {
    }

    //}}}
    //{{{ public function hook_active()
    public function hook_active()
    {
        if (!eka($_SESSION, 'aether', 'visited'))
        {
            $_SESSION['aether']['visited'] = array();
        }
        if (!eka($_SESSION, 'aether', 'language'))
        {
            $_SESSION['aether']['language'] = array_shift(array_keys(self::$languages));
        }
        if (!eka($_SESSION, 'cart', 'items'))
        {
            $_SESSION['cart']['items'] = array();
        }
        if (!eka($_SESSION, 'cart', 'data'))
        {
            $_SESSION['cart']['data'] = array();
        }
        if (eka($_GET, 'language'))
        {
            if (eka(self::$languages, $_GET['language']))
            {
                $_SESSION['aether']['language'] = $_GET['language'];
            }
            header('Location: '.URI_PATH);
            exit;
        }
    }

    //}}}
    //{{{ public function hook_admin_css()
    public function hook_admin_css()
    {
        $css = array();
        if (strpos(URI_PATH, '/admin/module/Aether/') === 0)
        {
            $css['screen'][] = '/admin/static/Aether/admin.css/';
            $css['print'][] = '/admin/static/Aether/report.css/';
        }
        if (strpos(URI_PATH, '/admin/module/Aether/report_orders/') === 0)
        {
            $css['screen'][] = '/admin/static/Ecommerce/screen.css/';
        }
        return $css;
    }
    //}}}
    //{{{ public function hook_admin_js()
    public function hook_admin_js()
    {
        $js = array();
        $js[] = '/admin/static/Aether/admin.js/';
        $js[] = '/admin/static/Aether/field.js/';
        return $js;
    }
    //}}}
    //{{{ public function hook_admin_module_page($page)
    public function hook_admin_module_page($page)
    {
    }
    
    //}}}
    //{{{ public function hook_admin_nav()
    public function hook_admin_nav()
    {
        $nav['Aether'] = array(
            '<a href="/admin/module/Aether/menus/">Nav Menus</a>',
            '<a href="/admin/module/Aether/gift_cards/">Gift Cards</a>',
            '<a href="/admin/module/Aether/flat_rate/">Handling Charges</a>',
        );
        $nav['Reports'] = array(
            '<a href="/admin/module/Aether/report_products/">Product Sales Report</a>',
            '<a href="/admin/module/Aether/report_upt/">UPT Report</a>',
            '<a href="/admin/module/Aether/report_inventory/">Inventory Report</a>',
            '<a href="/admin/module/Aether/report_orders/">Orders Report</a>',
        );
        $nav['Inventory'] = array(
            '<a href="/admin/module/Aether/notifications/">Notification Registrations</a>',
        );
        return $nav;
    }
    //}}}
    //{{{ public function hook_content_entry_new_finish($meta)
    public function hook_content_entry_new_finish($meta)
    {
        $type = Content::get_entry_type_by_entry_id($meta['content_entry_meta_id']);
        $cats = array('Mens Category', 'Womens Category', 'Gear Category');
        $prods = array('Mens Product', 'Womens Product', 'Gear Product');
        if (in_array($type['name'], $cats))
        {
            $entry = Content::get_entry_details_by_id($meta['content_entry_meta_id']);
            $slug = $entry['entry']['slug'];
            if ($slug != 'landing')
            {
                $urls = Cache::get($type['name'], 'sitemap');
                if (!is_array($urls))
                {
                    $urls = array();
                }
                $urls[$slug] = $entry['data']['Display Products']['data'];
                if (!is_array($urls[$slug]))
                {
                    $urls[$slug] = array();
                }
                Cache::set($type['name'], $urls, 0, 'sitemap');
            }
            else
            {
                $data = !is_null(Data::query('Aether', 'all'))
                    ? Data::query('Aether', 'all')
                    : array();
                $data[$type['name']] = $entry['data']['Enable Featured']['data'][0] == 'No';
                Data::update('Aether', 'all', $data);
                Data::save();
            }
        }
        if (in_array($type['name'], $prods))
        {
            $entry = Content::get_entry_details_by_id($meta['content_entry_meta_id']);
            $return_data['type'] = $type;
            $return_data['entry'] = $entry;
            $search_data[] = strip_tags(self::filter_language_data($entry['data']['Display Name'], 'EN', 'data', 0));
            $search_data[] = strip_tags(self::filter_language_data($entry['data']['Description'], 'EN', 'content', 0));
            $search_data[] = strip_tags(self::filter_language_data($entry['data']['Description'], 'EN', 'more', 0));
            $search_data[] = strip_tags(deka('', $entry, 'data', 'Additional Search Keywords', 'data', 0));
            SearchAPI::set_data($meta['content_entry_meta_id'], $return_data, implode(' ', $search_data), $type['name']);
        }
        if ($type['name'] === 'Gear Product')
        {
            if (!isset($entry))
            {
                $entry = Content::get_entry_details_by_id($meta['content_entry_meta_id']);
            }
            $data = Cache::get($type['name']);
            if (!is_array($data))
            {
                $data = array();
            }
            $sku = $entry['data']['SKU']['data'][0];
            if (array_search($sku, $data) === FALSE)
            {
                $data[] = $sku;
            }
            Cache::set($type['name'], $data, 0);
        }

        return TRUE;
    }

    //}}}
    //{{{ public function hook_content_entry_edit_finish($meta)
    public function hook_content_entry_edit_finish($meta)
    {
        $type = Content::get_entry_type_by_entry_id($meta['content_entry_meta_id']);
        $cats = array('Mens Category', 'Womens Category', 'Gear Category');
        $prods = array('Mens Product', 'Womens Product', 'Gear Product');
        if (in_array($type['name'], $cats))
        {
            $entry = Content::get_entry_details_by_id($meta['content_entry_meta_id']);
            $slug = $entry['entry']['slug'];
            if ($slug != 'landing')
            {
                $urls = Cache::get($type['name'], 'sitemap');
                if (!is_array($urls))
                {
                    $urls = array();
                }
                $urls[$slug] = $entry['data']['Display Products']['data'];
                if (!is_array($urls[$slug]))
                {
                    $urls[$slug] = array();
                }
                Cache::set($type['name'], $urls, 0, 'sitemap');
            }
            else
            {
                $data = !is_null(Data::query('Aether', 'all'))
                    ? Data::query('Aether', 'all')
                    : array();
                $data[$type['name']] = $entry['data']['Enable Featured']['data'][0] == 'No';
                Data::update('Aether', 'all', $data);
                Data::save();
            }
        }
        $cats[] = 'Page';
        if (in_array($type['name'], $cats))
        {
            self::create_footer();
            self::create_header();
        }
        if (in_array($type['name'], $prods))
        {
            $entry = Content::get_entry_details_by_id($meta['content_entry_meta_id']);
            $return_data['type'] = $type;
            $return_data['entry'] = $entry;
            $search_data[] = strip_tags(self::filter_language_data($entry['data']['Display Name'], 'EN', 'data', 0));
            $search_data[] = strip_tags(self::filter_language_data($entry['data']['Description'], 'EN', 'content', 0));
            $search_data[] = strip_tags(self::filter_language_data($entry['data']['Description'], 'EN', 'more', 0));
            $search_data[] = strip_tags(deka('', $entry, 'data', 'Additional Search Keywords', 'data', 0));
            SearchAPI::set_data($meta['content_entry_meta_id'], $return_data, implode(' ', $search_data), $type['name']);
        }
        if ($type['name'] === 'Gear Product')
        {
            if (!isset($entry))
            {
                $entry = Content::get_entry_details_by_id($meta['content_entry_meta_id']);
            }
            $data = Cache::get($type['name']);
            if (!is_array($data))
            {
                $data = array();
            }
            $sku = $entry['data']['SKU']['data'][0];
            if (array_search($sku, $data) === FALSE)
            {
                $data[] = $sku;
            }
            Cache::set($type['name'], $data, 0);
        }
        return TRUE;
    }

    //}}}
    //{{{ public function hook_content_entry_delete_start($meta)
    public function hook_content_entry_delete_start($meta)
    {
        $type = Content::get_entry_type_by_entry_id($meta['content_entry_meta_id']);
        $cats = array('Mens Category', 'Womens Category', 'Gear Category');
        $prods = array('Mens Product', 'Womens Product', 'Gear Product');
        if (in_array($type['name'], $cats))
        {
            $entry = Content::get_entry_details_by_id($meta['content_entry_meta_id']);
            $slug = $entry['entry']['slug'];
            if ($slug != 'landing')
            {
                $urls = Cache::get($type['name'], 'sitemap');
                if (!is_array($urls))
                {
                    $urls = array();
                }
                if (ake($slug, $urls))
                {
                    unset($urls[$slug]);
                }
                Cache::set($type['name'], $urls, 0, 'sitemap');
            }
            else
            {
                $data = !is_null(Data::query('Aether', 'all'))
                    ? Data::query('Aether', 'all')
                    : array();
                $data[$type['name']] = FALSE;
                Data::update('Aether', 'all', $data);
                Data::save();
            }
        }
        if (in_array($type['name'], $prods))
        {
            SearchAPI::delete_data($meta['content_entry_meta_id'], $type['name']);
        }
        if ($type['name'] === 'Gear Product')
        {
            if (!isset($entry))
            {
                $entry = Content::get_entry_details_by_id($meta['content_entry_meta_id']);
            }
            $data = Cache::get($type['name']);
            if (!is_array($data))
            {
                $data = array();
            }
            $sku = $entry['data']['SKU']['data'][0];
            $key = array_search($sku, $data);
            if ($key !== FALSE)
            {
                unset($data[$key]);
            }
            Cache::set($type['name'], array_values($data), 0);
        }
        return TRUE;
    }

    //}}}
    //{{{ public function hook_rpc($action, $params = NULL)
    /**
     * Implementation of hook_rpc
     *
     * This looks at the action and checks for the method _rpc_<action> and
     * passes the parameters to that. There is no limit on parameters.
     *
     * @param string $action action name
     * @return string
     */
    public function hook_rpc($action)
    {
        $method = '_rpc_'.$action;
        $caller = array($this, $method);
        $args = array_slice(func_get_args(), 1);
        return method_exists($this, $method) 
            ? call_user_func_array($caller, $args)
            : '';
    }

    //}}}

    // {{{ protected function _rpc_inventory($data)
    protected function _rpc_inventory($data)
    {
        $success = FALSE;
        $defaults = array_fill_keys(
            array('id', 'option_x', 'option_y', 'quantity'),
            ''
        );
        $data = array_merge($defaults, $data);
        if (is_numeric($data['id'])
            && is_numeric($data['option_x'])
            && is_numeric($data['option_y'])
            && is_numeric($data['quantity']))
            {
                switch ($data['type'])
                {
                    case 'add':
                        $success = Inventory::add_quantity($data['id'], $data['option_x'], $data['option_y'], $data['quantity']);
                    break;
                    case 'deduct':
                        $success = Inventory::deduct_quantity($data['id'], $data['option_x'], $data['option_y'], $data['quantity']);
                    break;
                }
            }
        return json_encode(
            array(
                'success' => $success,
            )
        );
    }
    //}}}
    // {{{ protected function _rpc_update_notification($data)
    protected function _rpc_update_notification($data)
    {
        $response['success'] = FALSE;
        switch ($data['action'])
        {
            case 'remove':
                $remove = explode(',', $data['notification']['emails']);
                $response['success'] = self::remove_notification_email($data['notification']['id'], $remove);
            break;
        }
        
        return json_encode($response);
    }
    //}}}

    //{{{ public static function filter_language($data)
    /**
     * Filters out the language data for aether specific language fields based
     * on session data and falls back to EN if no language is found.
     *
     * If more than one parameter is specified, it is used in a manner similar
     * to array_drill to return the specific value.
     */
    public static function filter_language($data)
    {
        $lang = $_SESSION['aether']['language'];
        $lang_data = array();
        $lang_data_backup = array();
        foreach ($data as $row)
        {
            if (!$lang_data)
            {
                if ($row['language'][0] === $lang)
                {
                    $lang_data = $row;
                }
                elseif ($row['language'][0] === 'EN')
                {
                    $lang_data_backup = $row;
                }
            }
        }
        $lang_data = $lang_data ? $lang_data : $lang_data_backup;
        if (func_num_args() === 1)
        {
            return $lang_data;
        }
        else
        {
            $keys = array_slice(func_get_args(), 1);
            return array_drill($lang_data, $keys);
        }
    }
    //}}}
    //{{{ public static function filter_language_data($data, $lang = NULL)
    /**
     * Filters out the language data for aether specific language fields based
     * on session data and falls back to EN if no language is found.
     *
     * If more than one parameter is specified, it is used in a manner similar
     * to array_drill to return the specific value.
     */
    public static function filter_language_data($data, $lang = NULL)
    {
        if (is_null($lang))
        {
            $lang = $_SESSION['aether']['language'];
        }
        $lang_data = array();
        foreach ($data as $row)
        {
            if (!$lang_data)
            {
                if ($row['language'][0] === $lang)
                {
                    $lang_data = $row;
                }
                elseif ($row['language'][0] === 'EN')
                {
                    $lang_data_backup = $row;
                }
            }
        }
        $lang_data = $lang_data ? $lang_data : $lang_data_backup;
        if (func_num_args() === 2)
        {
            return $lang_data;
        }
        else
        {
            $keys = array_slice(func_get_args(), 2);
            return array_drill($lang_data, $keys);
        }
    }
    //}}}
    //{{{ public static function get_languages()
    public static function get_languages()
    {
        return self::$languages;
    }
    //}}}
    //{{{ public static function get_currencies()
    public static function get_currencies()
    {
        return self::$currencies;
    }
    //}}}
    //{{{ public static function get_nav_menus()
    public static function get_nav_menus()
    {
        $nav = !is_null(Data::query('Aether', 'nav_menus'))
            ? Data::query('Aether', 'nav_menus')
            : array();
        $nav = array_merge(
            array(
                'info' => array(),
                'footer_info' => array(),
                'contact' => array(),
                'subhead' => array(),
                'mens-1' => array(),
                'mens-2' => array(),
                'mens-3' => array(),
                'mens-4' => array(),
                'womens-1' => array(),
                'womens-2' => array(),
                'womens-3' => array(),
                'womens-4' => array(),
                'gear-1' => array(),
                'gear-2' => array(),
                'gear-3' => array(),
                'gear-4' => array(),
            ),
            $nav
        );

        $nav['subhead'] = array_merge(
            array(
                'mens-1' => '',
                'mens-2' => '',
                'mens-3' => '',
                'mens-4' => '',
                'womens-1' => '',
                'womens-2' => '',
                'womens-3' => '',
                'womens-4' => '',
                'gear-1' => '',
                'gear-2' => '',
                'gear-3' => '',
                'gear-4' => '',
            ),
            $nav['subhead']
        );

        foreach ($nav as $key => &$item)
        {
            if ($key !== 'subhead')
            {
                foreach ($item as &$data)
                {
                    $entry = Content::get_entry_details_by_id($data['id']);
                    foreach (self::get_languages() as $lang => $text)
                    {
                        $data[strtolower($lang)] = self::filter_language_data($entry['data']['Display Name'], $lang, 'data', 0);
                    }
                }
            }
        }

        return $nav;
    }
    //}}}
    //{{{ public static function get_hc_translation($text, $language = NULL)
    public static function get_hc_translation($text, $language = NULL)
    {
        static $translation_data = array();

        if (is_null($language))
        {
            $language = $_SESSION['aether']['language'];
        }

        if (!eka($translation_data, $language))
        {
            $data_file = dirname(__FILE__).'/translations/'.$language.'.php';
            if (is_file($data_file))
            {
                include $data_file;
                if (isset($d))
                {
                    $translation_data[$language] = $d;
                }
            }
        }

        $key = strtolower($text);
        return deka($text, $translation_data, $language, $key);
    }
    //}}}
    //{{{ public static function ght($text)
    /**
     * Shortcut to get_hc_translation() but always uses session language. It 
     * also sends string value to vsprintf, so additional parameters will be 
     * used to substitute the placeholder strings such as %s and %d.
     */
    public static function ght($text)
    {
        $params = func_get_args();
        $params = array_slice($params, 1);
        $string = self::get_hc_translation($text);
        $translation = vsprintf($string, $params);
        return $translation;
    }
    //}}}

    // notification API
    //{{{ public static function get_notification($product_id, $color_id, $size_id)
    public static function get_notification($product_id, $color_id, $size_id)
    {
        $dql = Doctrine_Query::create()
               ->select('id, product_id, color_id, size_id, emails')
               ->from('AetherNotificationRegistration')
               ->where('product_id = ?')
               ->andWhere('color_id = ?')
               ->andWhere('size_id = ?');
        $params = array($product_id, $color_id, $size_id);
        $result = $dql->execute($params, Doctrine::HYDRATE_ARRAY);
        $result = $result[0];
        if (!empty($result))
        {
            $result['emails'] = unserialize($result['emails']);
        }
        return $result;
    }
    //}}}
    //{{{ public static function get_notification_by_id($id)
    public static function get_notification_by_id($id)
    {
        $dql = Doctrine_Query::create()
               ->select('id, product_id, color_id, size_id, emails')
               ->from('AetherNotificationRegistration')
               ->where('id = ?');
        $result = $dql->execute(array($id), Doctrine::HYDRATE_ARRAY);
        $note = array();
        if (!empty($result))
        {
            $note = $result[0];
            $note['emails'] = unserialize($note['emails']);
        }
        return $note;
    }
    //}}}
    //{{{ public static function get_notifications()
    public static function get_notifications()
    {
        $dql = Doctrine_Query::create()
               ->select('id, product_id, color_id, size_id, emails')
               ->from('AetherNotificationRegistration');
        $rows = $dql->execute(array(), Doctrine::HYDRATE_ARRAY);
        if ($rows)
        {
            foreach ($rows as &$row)
            {
                $row['emails'] = unserialize($row['emails']);
            }
        }
        return $rows;
    }
    //}}}
    //{{{ public static function delete_notification_by_id($id)
    public static function delete_notification_by_id($id)
    {
        $dql = Doctrine_Query::create()
               ->delete('AetherNotificationRegistration')
               ->where('id = ?')
               ->execute(array($id));
        return $dql;
    }
    //}}}
    //{{{ public static function add_notification_email($product_id, $color_id, $size_id, $email)
    public static function add_notification_email($product_id, $color_id, $size_id, $email)
    {
        $note = self::get_notification($product_id, $color_id, $size_id);
        if (empty($note))
        {
            $nr = new AetherNotificationRegistration;
            $nr->product_id = $product_id;
            $nr->color_id = $color_id;
            $nr->size_id = $size_id;
            $nr->emails = serialize(array($email));
            $nr->save();
        }
        else
        {
            $emails = $note['emails'];
            if (!in_array($email, $emails))
            {
                $emails[] = $email;
                $dql = Doctrine_Query::create()
                       ->update('AetherNotificationRegistration')
                       ->set('emails', '?', serialize($emails))
                       ->where('id = ?', $note['id'])
                       ->execute();
            }
        }
    }
    //}}}
    //{{{ public static function remove_notification_email($id, $email)
    public static function remove_notification_email($id, $email)
    {
        $note = self::get_notification_by_id($id);
        if (!empty($note))
        {
            if (!is_array($email))
            {
                $email = array($email);
            }
            $emails = array_diff($email, $note['emails']);
            $dql = Doctrine_Query::create()
                   ->update('AetherNotificationRegistration')
                   ->set('emails', '?', serialize($emails))
                   ->where('id = ?', $note['id'])
                   ->execute();
            return TRUE;
        }
        return FALSE;
    }
    //}}}

    //{{{ public static function create_footer()
    public static function create_footer()
    {
        $nav = self::get_nav_menus();
        
        $menus = array_fill_keys(
            array('mens', 'womens', 'gear'),
            array()
        );

        $language = $_SESSION['aether']['language'];
        foreach (self::get_languages() as $language => $text_language)
        {
            $file = substr(__FILE__, 0, strpos(__FILE__, '/modup')).'/sitemap.tpl.'.$language.'.php';
            unset($menus['info']);
            unset($menus['contact']);
            foreach (array_keys($menus) as $cat)
            {
                $tmp = array();
                for ($i = 1; $i <= 4; $i++)
                {
                    foreach ($nav[$cat.'-'.$i] as $item)
                    {
                        $name = ake(strtolower($language), $item) && $item[strtolower($language)] != ''
                            ? $item[strtolower($language)]
                            : $item['en'];
                        $tmp['/shop/'.$cat.'/'.$item['slug'].'/'] = $name;
                    }
                }
                $menus[$cat] = $tmp;
                unset($tmp);
            }
            foreach ($nav['footer_info'] as $item)
            {
                $name = ake(strtolower($language), $item) && $item[strtolower($language)] != ''
                    ? $item[strtolower($language)]
                    : $item['en'];
                $menus['info']['/info/'.$item['slug'].'/'] = $name;
            }
            foreach ($nav['contact'] as $item)
            {
                $name = ake(strtolower($language), $item) && $item[strtolower($language)] != ''
                    ? $item[strtolower($language)]
                    : $item['en'];
                $menus['contact']['/contact/'.$item['slug'].'/'] = $name;
            }

            ob_start();
            ?>
                <div class="sitemap cleared">
                    <div class="shop">
                        <h3><?php echo self::get_hc_translation('Shop', $language); ?></h3>
                        <ul>
                            <li><a href="/shop/mens/"><strong><?php echo self::get_hc_translation('Mens', $language); ?></strong></a></li>
                            <?php foreach ($menus['mens'] as $url => $name): ?>
                            <li><a href="<?php echo $url; ?>"><?php echo $name; ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                        <ul>
                            <li><a href="/shop/womens/"><strong><?php echo self::get_hc_translation('Womens', $language); ?></strong></a></li>
                            <?php foreach ($menus['womens'] as $url => $name): ?>
                            <li><a href="<?php echo $url; ?>"><?php echo $name; ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                        <ul>
                            <li><a href="/shop/gear/"><strong><?php echo self::get_hc_translation('Gear', $language); ?></strong></a></li>
                            <?php foreach ($menus['gear'] as $url => $name): ?>
                            <li><a href="<?php echo $url; ?>"><?php echo $name; ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <div class="other">
                        <div>
                            <h3><?php echo self::get_hc_translation('Journal', $language); ?></h3>
                            <ul>
                                <li><a href="/blog/"><?php echo self::get_hc_translation('Featured', $language); ?></a></li>
                            </ul>
                        </div>
                        <div>
                            <h3><?php echo self::get_hc_translation('Info', $language); ?></h3>
                            <ul>
                                <?php foreach ($menus['info'] as $url => $name): ?>
                                <li><a href="<?php echo $url; ?>"><?php echo $name; ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <div>
                            <h3><?php echo self::get_hc_translation('Contact', $language); ?></h3>
                            <ul>
                                <?php foreach ($menus['contact'] as $url => $name): ?>
                                <li><a href="<?php echo $url; ?>"><?php echo $name; ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                    <div class="misc">
                        <div class="social cleared">
                            <a target="_blank" href="http://www.facebook.com/aetherapparel"><div><img src="/images/ico-facebook-sm.png" alt="Facebook" /></div></a>
                            <a target="_blank" href="http://twitter.com/#!/aetherapparel"><div><img src="/images/ico-twitter-sm.png" alt="Twitter" /></div></a>
                        </div>
                        <div class="search">
                            <div class="viewer">
                                <form method="get" action="/shop/search/">
                                    <input tabindex="-1" type="text" name="query" />
                                    <img src="/images/search-mag-glass.png" alt="search" />
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php
            $footer = ob_get_contents();
            ob_end_clean();

            file_put_contents($file, $footer);
        }
    }
    //}}}
    //{{{ public static function create_header()
    public static function create_header()
    {
        self::create_nav();
        self::create_site_functions();
    }
    //}}}
    // {{{ public static function create_nav()
    public static function create_nav()
    {
        $nav = self::get_nav_menus();
        
        $language = $_SESSION['aether']['language'];
        foreach (self::get_languages() as $language => $text_language)
        {
            $file = DIR_WEB.'/nav.tpl.'.$language.'.php';

            foreach ($nav['info'] as $item)
            {
                $name = ake(strtolower($language), $item) && $item[strtolower($language)] != ''
                    ? $item[strtolower($language)]
                    : $item['en'];
                $menus['info']['/info/'.$item['slug'].'/'] = $name;
            }
            foreach ($nav['contact'] as $item)
            {
                $name = ake(strtolower($language), $item) && $item[strtolower($language)] != ''
                    ? $item[strtolower($language)]
                    : $item['en'];
                $menus['contact']['/contact/'.$item['slug'].'/'] = $name;
            }

            ob_start();
            ?>
                <div class="nav cleared">
                    <ul>
                        <?php echo '<?php if (defined("URI_PART_0") && URI_PART_0 === "shop"): ?>'; ?>
                        <li><a class="selected" href="/shop/"><?php echo self::get_hc_translation('Shop', $language); ?></a></li>
                        <?php echo '<?php else: ?>'; ?>
                        <li><a href="/shop/"><?php echo self::get_hc_translation('Shop', $language); ?></a></li>
                        <?php echo '<?php endif; ?>'; ?>

                        <?php echo '<?php if (defined("URI_PART_0") && URI_PART_0 === "blog"): ?>'; ?>
                        <li><a class="selected" href="/blog/"><?php echo self::get_hc_translation('Journal', $language); ?></a></li>
                        <?php echo '<?php else: ?>'; ?>
                        <li><a href="/blog/"><?php echo self::get_hc_translation('Journal', $language); ?></a></li>
                        <?php echo '<?php endif; ?>'; ?>

                        <?php echo '<?php if (defined("URI_PART_0") && URI_PART_0 === "info"): ?>'; ?>
                        <li><a class="selected" href="/info/"><?php echo self::get_hc_translation('Info', $language); ?></a>
                        <?php echo '<?php else: ?>'; ?>
                        <li><a href="/info/"><?php echo self::get_hc_translation('Info', $language); ?></a>
                        <?php echo '<?php endif; ?>'; ?>
                        <?php if (!empty($menus['info'])): ?>
                        <ul>
                        <?php foreach ($menus['info'] as $url => $name): ?>
                            <li><a href="<?php echo $url; ?>"><?php echo $name; ?></a></li>
                        <?php endforeach; ?>
                        </ul>
                        <?php endif; ?>
                        </li>

                        <?php echo '<?php if (defined("URI_PART_0") && URI_PART_0 === "contact"): ?>'; ?>
                        <li><a class="selected" href="/contact/"><?php echo self::get_hc_translation('Contact', $language); ?></a>
                        <?php echo '<?php else: ?>'; ?>
                        <li><a href="/contact/"><?php echo self::get_hc_translation('Contact', $language); ?></a>
                        <?php echo '<?php endif; ?>'; ?>
                        <?php if (!empty($menus['contact'])): ?>
                        <ul>
                        <?php foreach ($menus['contact'] as $url => $name): ?>
                            <li><a href="<?php echo $url; ?>"><?php echo $name; ?></a></li>
                        <?php endforeach; ?>
                        </ul>
                        <?php endif; ?>
                        </li>
                        <?php echo '<?php if (defined("URI_PART_1") && URI_PART_1 === "aetherstream"): ?>'; ?>
                        <li class="right"><a class="selected" href="/blog/aetherstream/"><?php echo self::get_hc_translation('Aetherstream', $language); ?></a></li>
                        <?php echo '<?php else: ?>'; ?>
                        <li class="right"><a href="/blog/aetherstream/"><?php echo self::get_hc_translation('Aetherstream', $language); ?></a></li>
                        <?php echo '<?php endif; ?>'; ?>
                    </ul>
                </div>
            <?php

            $content = ob_get_contents();
            ob_end_clean();

            file_put_contents($file, $content);
        }
    }
    // }}}
    // {{{ public static function create_site_functions()
    public static function create_site_functions()
    {
        $language = $_SESSION['aether']['language'];
        foreach (self::get_languages() as $language => $text_language)
        {
            $file = DIR_WEB.'/site-functions.tpl.'.$language.'.php';

            ob_start();
            ?>
                <div class="site-functions">
                    <div class="search">
                        <div class="viewer">
                            <form method="get" action="/shop/search/">
                                <input tabindex="-1" type="text" name="query" />&nbsp;
                                <img src="/images/search-mag-glass.png" alt="<?php echo hsc(self::get_hc_translation('Search', $language)); ?>" />
                            </form>
                        </div>
                    </div>
                    <div class="language" id='language-switcher'>
                        <img src="/images/flags/medium/<?php echo '<?php echo $_SESSION["aether"]["language"] ?>'; ?>.off.jpg" alt="" />
                        <div>
                            <?php 
                            foreach (self::$languages as $lang => $lang_text)
                            {
                                echo '<img data-language="'.$lang.'" src="/images/flags/small/'.$lang.'.on.jpg" alt="'.hsc($lang_text).'" />';
                            }
                            ?>
                        </div>
                    </div>
                    <div class="cart-items">
                        <?php echo '<?php $items = isset($_SESSION["cart"]["data"]) ? count($_SESSION["cart"]["data"]) : 0; ?>' ?>
                        <a href="/cart/"><?php echo hsc(self::get_hc_translation("Cart (", $language)); ?><?php echo '<?php echo str_pad($items, $items ? 2 : 1, 0, STR_PAD_LEFT); ?>'; ?><?php echo self::get_hc_translation(')', $language); ?></a>
                    </div>
                </div>
            <?php

            $content = ob_get_contents();
            ob_end_clean();

            file_put_contents($file, $content);
        }
    }
    // }}}

    // {{{ public static function get_flat_rates()
    public static function get_flat_rates()
    {
        return !is_null(Data::query('Aether', 'flat_rates'))
            ? Data::query('Aether', 'flat_rates')
            : array();
    }
    // }}}
    // {{{ public static function get_product_type($sku)
    public static function get_product_type($sku)
    {
        $char = substr($sku, 0, 1);
        $type = FALSE;
        switch ($char)
        {
            case 'M':
                $type = 'Mens';
            break;
            case 'W':
                $type = 'Womens';
            break;
            case 'A':
                $type = 'Accessories';
            break;
            case 'G':
                $type = 'Gear';
            break;
        }
        return $type;
    }
    // }}}
}

?>
