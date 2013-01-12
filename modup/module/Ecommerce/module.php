<?php

// {{{ class Ecommerce
class Ecommerce
{
    //{{{ constants
    const MODULE_AUTHOR = 'Jason T. Wong';
    const MODULE_DESCRIPTION = 'Ecommerce system. Product agnostic.';
    const MODULE_WEBSITE = 'http://www.jasontwong.com/';
    const MODULE_DEPENDENCY = '';

    //}}}
    //{{{ properties
    public static $templates_dir = '';
    protected static $paypal = FALSE;
    protected static $paypal_sandbox = FALSE;
    protected static $paypal_api = array();
    protected static $paypal_api_sandbox = array();
    protected static $exchange_rates = array();
    protected static $ups_services = array(
        '01' => 'UPS Next Day Air',
        '02' => 'UPS Second Day Air',
        '03' => 'UPS Ground',
        '07' => 'UPS Worldwide Express',
        '08' => 'UPS Worldwide Expedited',
        '11' => 'UPS Standard',
        '12' => 'UPS Three-Day Select',
        '13' => 'Next Day Air Saver',
        '14' => 'UPS Next Day Air Early AM',
        '54' => 'UPS Worldwide Express Plus',
        '59' => 'UPS Second Day Air AM',
        '65' => 'UPS Saver',
    );
    protected static $tracking_links = array(
        'FedEx' => 'http://www.fedex.com/cgi-bin/tracking?action=track&tracknumbers=%s',
        'Airborne' => 'http://track.airborne.com/atrknav.asp?ShipmentNumber=%s',
        'DHL' => 'http://track.airborne.com/atrknav.asp?ShipmentNumber=%s',
        'UPS' => 'http://wwwapps.ups.com/etracking/tracking.cgi?TypeOfInquiryNumber=T&InquiryNumber1=%s',
        'USPS' => 'https://tools.usps.com/go/TrackConfirmAction.action?tLabels=%s&FindTrackLabelorReceiptNumber=Find',
        // 'USPS' => 'http://trkcnfrm1.smi.usps.com/netdata-cgi/db2www/cbd_243.d2w/output?CAMEFROM=OK&strOrigTrackNum=%s',
    );
    protected static $currencies = array(
        'AUD' => 'Australian Dollar',
        'BRL' => 'Brazilian Real',
        'CAD' => 'Canadian Dollar',
        'CZK' => 'Czech Koruna',
        'DKK' => 'Danish Krone',
        'EUR' => 'Euro',
        'HKD' => 'Hong Kong Dollar',
        'HUF' => 'Hungarian Forint',
        'ILS' => 'Israeli New Sheqel',
        'JPY' => 'Japanese Yen',
        'MYR' => 'Malaysian Ringgit',
        'MXN' => 'Mexican Peso',
        'NOK' => 'Norwegian Krone',
        'NZD' => 'New Zealand Dollar',
        'PHP' => 'Philippine Peso',
        'PLN' => 'Polish Zloty',
        'GBP' => 'Pound Sterling',
        'SGD' => 'Singapore Dollar',
        'SEK' => 'Swedish Krona',
        'CHF' => 'Swiss Franc',
        'TWD' => 'Taiwan New Dollar',
        'THB' => 'Thai Baht',
        'USD' => 'U.S. Dollar',
    );
    //}}}
    //{{{ constructors
    public function __construct()
    {
    }
    //}}}
    //{{{ public function hook_active()
    public function hook_active()
    {
        self::$templates_dir = dirname(__FILE__).'/templates';
        self::$paypal = self::is_using_paypal();
        self::$paypal_sandbox = self::is_using_paypal('sandbox');
        self::$paypal_api = self::get_paypal_credentials();
        self::$paypal_api_sandbox = self::get_paypal_credentials('sandbox');
    }
    //}}}
    //{{{ public function hook_admin_css()
    public function hook_admin_css()
    {
        $css = array();
        if (strpos(URI_PATH, '/admin/module/Ecommerce/') !== FALSE)
        {
            $css['screen'][] = '/admin/static/Ecommerce/screen.css/';
            $css['screen'][] = '/admin/static/Ecommerce/field.css/';
        }
        return $css;
    }

    //}}}
    //{{{ public function hook_admin_js()
    public function hook_admin_js()
    {
        $js = array();

        if (strpos(URI_PATH, '/admin/module/Ecommerce/') !== FALSE)
        {
            $js[] = '/admin/static/Ecommerce/field.js/';
            $js[] = '/admin/static/Content/field.js/';
        }
        if (strpos(URI_PATH, '/admin/module/Ecommerce/return_order/') !== FALSE)
        {
            $js[] = '/admin/static/Ecommerce/return_order.js/';
        }

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
        $uri = '/admin/module/Ecommerce';
        $links = array();
        if (User::has_perm('add ecommerce orders'))
        {
            $links['Ecommerce'][] = '<a href="'.$uri.'/add_order/">Add Order</a>';
        }
        if (User::has_perm('view ecommerce products'))
        {
            $links['Ecommerce'][] = '<a href="'.$uri.'/products/">Products</a>';
        }
        if (User::has_perm('view ecommerce orders'))
        {
            $links['Ecommerce'][] = '<a href="'.$uri.'/orders/">Orders</a>';
        }
        if (User::has_perm('edit ecommerce gift cards'))
        {
            $links['Ecommerce'][] = '<a href="'.$uri.'/gift_cards/">Gift Cards</a>';
        }
        if (User::has_perm('edit ecommerce coupons'))
        {
            $links['Ecommerce'][] = '<a href="'.$uri.'/coupons/">Coupons</a>';
        }
        if (User::has_perm('edit ecommerce statuses'))
        {
            $links['Ecommerce'][] = '<a href="'.$uri.'/statuses/">Statuses</a>';
        }
        return $links;
    }

    //}}}
    //{{{ public function hook_admin_settings()
    public function hook_admin_settings()
    {
        $fields = array();
        $fields[] = array(
            'field' => Field::layout(
                'text',
                array(
                    'data' => array(
                        'label' => 'Next order number'
                    )
                )
            ),
            'name' => 'order_number',
            'type' => 'text',
            'value' => array(
                'data' => self::get_next_order_number()
            )
        );
        $fields[] = array(
            'field' => Field::layout(
                'textarea_array',
                array(
                    'data' => array(
                        'label' => 'Order Options'
                    )
                )
            ),
            'name' => 'order_keys',
            'type' => 'textarea_array',
            'value' => array(
                'data' => self::get_order_keys()
            )
        );
        $fields[] = array(
            'field' => Field::layout(
                'textarea_array',
                array(
                    'data' => array(
                        'label' => 'Product Options'
                    )
                )
            ),
            'name' => 'product_keys',
            'type' => 'textarea_array',
            'value' => array(
                'data' => self::get_product_keys()
            )
        );
        // {{{ paypal fields
        $fields['Paypal'][] = array(
            'field' => Field::layout(
                'checkbox_boolean',
                array(
                    'data' => array(
                        'label' => 'Integrate Paypal'
                    )
                )
            ),
            'name' => 'paypal',
            'type' => 'checkbox_boolean',
            'value' => array(
                'data' => self::$paypal
            )
        );
        $fields['Paypal'][] = array(
            'field' => Field::layout(
                'text',
                array(
                    'data' => array(
                        'label' => 'Paypal API Username'
                    )
                )
            ),
            'name' => 'paypal_api_user',
            'type' => 'text',
            'value' => array(
                'data' => self::$paypal_api['username']
            )
        );
        $fields['Paypal'][] = array(
            'field' => Field::layout(
                'text',
                array(
                    'data' => array(
                        'label' => 'Paypal API Password'
                    )
                )
            ),
            'name' => 'paypal_api_pass',
            'type' => 'text',
            'value' => array(
                'data' => self::$paypal_api['password']
            )
        );
        $fields['Paypal'][] = array(
            'field' => Field::layout(
                'text',
                array(
                    'data' => array(
                        'label' => 'Paypal API Signature'
                    )
                )
            ),
            'name' => 'paypal_api_sig',
            'type' => 'text',
            'value' => array(
                'data' => self::$paypal_api['signature']
            )
        );
        // }}}
        // {{{ paypal sandbox fields
        $fields['Paypal Sandbox'][] = array(
            'field' => Field::layout(
                'checkbox_boolean',
                array(
                    'data' => array(
                        'label' => 'Enable Sandbox'
                    )
                )
            ),
            'name' => 'paypal_sandbox',
            'type' => 'checkbox_boolean',
            'value' => array(
                'data' => self::$paypal_sandbox
            )
        );
        $fields['Paypal Sandbox'][] = array(
            'field' => Field::layout(
                'text',
                array(
                    'data' => array(
                        'label' => 'Paypal API Username'
                    )
                )
            ),
            'name' => 'paypal_api_sandbox_user',
            'type' => 'text',
            'value' => array(
                'data' => self::$paypal_api_sandbox['username']
            )
        );
        $fields['Paypal Sandbox'][] = array(
            'field' => Field::layout(
                'text',
                array(
                    'data' => array(
                        'label' => 'Paypal API Password'
                    )
                )
            ),
            'name' => 'paypal_api_sandbox_pass',
            'type' => 'text',
            'value' => array(
                'data' => self::$paypal_api_sandbox['password']
            )
        );
        $fields['Paypal Sandbox'][] = array(
            'field' => Field::layout(
                'text',
                array(
                    'data' => array(
                        'label' => 'Paypal API Signature'
                    )
                )
            ),
            'name' => 'paypal_api_sandbox_sig',
            'type' => 'text',
            'value' => array(
                'data' => self::$paypal_api_sandbox['signature']
            )
        );
        // }}}
        // {{{ ups fields
        $ups_creds = self::get_ups_credentials();
        $fields['UPS'][] = array(
            'field' => Field::layout(
                'text',
                array(
                    'data' => array(
                        'label' => 'UPS Username',
                    )
                )
            ),
            'name' => 'ups_username',
            'type' => 'text',
            'value' => array(
                'data' => $ups_creds['username'],
            )
        );
        $fields['UPS'][] = array(
            'field' => Field::layout(
                'text',
                array(
                    'data' => array(
                        'label' => 'UPS Password',
                    )
                )
            ),
            'name' => 'ups_password',
            'type' => 'text',
            'value' => array(
                'data' => $ups_creds['password'],
            )
        );
        $fields['UPS'][] = array(
            'field' => Field::layout(
                'text',
                array(
                    'data' => array(
                        'label' => 'UPS Access Key',
                    )
                )
            ),
            'name' => 'ups_access_key',
            'type' => 'text',
            'value' => array(
                'data' => $ups_creds['access_key'],
            )
        );
        $fields['UPS'][] = array(
            'field' => Field::layout(
                'text',
                array(
                    'data' => array(
                        'label' => 'UPS Shipper Number',
                    )
                )
            ),
            'name' => 'ups_shipper_number',
            'type' => 'text',
            'value' => array(
                'data' => $ups_creds['shipper_number'],
            )
        );
        $fields['UPS'][] = array(
            'field' => Field::layout(
                'text',
                array(
                    'data' => array(
                        'label' => 'UPS Shipper Zip',
                    )
                )
            ),
            'name' => 'ups_shipper_zip',
            'type' => 'text',
            'value' => array(
                'data' => Data::query('Ecommerce', 'ups_shipper_zip'),
            )
        );
        $fields['UPS'][] = array(
            'field' => Field::layout(
                'checkbox',
                array(
                    'data' => array(
                        'label' => 'Active UPS Services',
                        'options' => self::$ups_services,
                    )
                )
            ),
            'name' => 'ups_services',
            'type' => 'checkbox',
            'value' => array(
                'data' => array_keys(self::get_ups_services()),
            )
        );
        // }}}
        return $fields;
    }

    //}}}
    //{{{ public function hook_admin_settings_validate($name, $data)
    public function hook_admin_settings_validate($name, $data)
    {
        $success = TRUE;
        switch ($name)
        {
            case 'order_number':
                $success = is_numeric($data);
            break;
        }
        return array(
            'success' => $success,
            'data' => $data
        );
    }

    //}}}
    //{{{ public function hook_routes()
    public function hook_routes()
    {
        $ctrl = dirname(__FILE__).'/admin/controller';
        $routes = array(
            array('/admin/Ecommerce/orders-export/', $ctrl.'/orders-export.php'),
            array('/admin/Ecommerce/products-export/', $ctrl.'/products-export.php'),
        );
        return $routes;
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
    //{{{ public function hook_user_perm()
    public function hook_user_perm()
    {
        $perms = array();
        $perms['Ecommerce']['add ecommerce orders'] = 'Add ecommerce orders';
        $perms['Ecommerce']['edit ecommerce orders'] = 'Edit ecommerce orders';
        $perms['Ecommerce']['view ecommerce orders'] = 'View ecommerce orders';
        $perms['Ecommerce']['edit ecommerce coupons'] = 'Edit ecommerce coupons';
        $perms['Ecommerce']['edit ecommerce gift cards'] = 'Edit ecommerce gift cards';
        $perms['Ecommerce']['edit ecommerce statuses'] = 'Edit ecommerce statuses';
        $perms['Ecommerce']['view ecommerce products'] = 'View ecommerce products';
        return $perms;
    }

    //}}}

    //{{{ protected function _rpc_status($data)
    protected function _rpc_status($data)
    {
        $success = FALSE;
        $action = $data['action'];
        switch ($action)
        {
            // {{{ case 'delete'
            case 'delete':
                $success = EcommerceAPI::delete_status_by_id($data['id']);
            break;
            // }}}
        }
        echo json_encode(
            array(
                'success' => $success,
                'action' => $action,
            )
        );
    }

    //}}}
    //{{{ protected function _rpc_coupon($data)
    protected function _rpc_coupon($data)
    {
        $success = FALSE;
        $action = $data['action'];
        switch ($action)
        {
            // {{{ case 'delete'
            case 'delete':
                $success = EcommerceAPI::delete_coupon_by_id($data['id']);
            break;
            // }}}
        }
        echo json_encode(
            array(
                'success' => $success,
                'action' => $action,
            )
        );
    }

    //}}}
    //{{{ protected function _rpc_gift_card($data)
    protected function _rpc_gift_card($data)
    {
        $success = FALSE;
        $action = $data['action'];
        switch ($action)
        {
            // {{{ case 'delete'
            case 'delete':
                $success = EcommerceAPI::delete_gift_card_by_id($data['id']);
            break;
            // }}}
        }
        echo json_encode(
            array(
                'success' => $success,
                'action' => $action,
            )
        );
    }

    //}}}

    // {{{ public static function get_available_coupons()
    public static function get_available_coupons()
    {
        $coupons = EcommerceAPI::get_coupons();
        $data = array();
        foreach ($coupons as $coupon)
        {
            if (self::is_valid_coupon($coupon['code']))
            {
                $data[] = $coupon;
            }
        }
        return $data;
    }

    //}}}
    // {{{ public static function get_expired_coupons()
    public static function get_expired_coupons()
    {
        $coupons = EcommerceAPI::get_coupons();
        $data = array();
        foreach ($coupons as $coupon)
        {
            if (!self::is_valid_coupon($coupon['code']))
            {
                $data[] = $coupon;
            }
        }
        return $data;
    }

    //}}}
    // {{{ public static function is_valid_coupon($code)
    public static function is_valid_coupon($code)
    {
        $coupon = EcommerceAPI::get_coupon_by_code($code);
        if (empty($coupon))
        {
            return FALSE;
        }
        $time = time();
        $data = array();
        if ($coupon['uses'] == 0 
            || $time < $coupon['start_date'] 
            || $time > strtotime('+1 day', $coupon['end_date']))
            {
                return FALSE;
            }
        return TRUE;
    }

    //}}}
    // {{{ public static function use_coupon($code)
    public static function use_coupon($code)
    {
        $data = FALSE;
        if (self::is_valid_coupon($code))
        {
            $ect = Doctrine::getTable('EcommerceCoupon');
            $ec = $ect->findOneByCode($code);
            if ($ec->uses > 0)
            {
                $ec->uses--;
                $ec->save();
            }
            $data = $ec->toArray();
            $ec->free();
        }
        return $data;
    }

    //}}}

    // {{{ public static function get_available_gift_cards()
    public static function get_available_gift_cards()
    {
        $gift_cards = EcommerceAPI::get_gift_cards();
        $time = strtotime('now');
        $data = array();
        foreach ($gift_cards as $gift_card)
        {
            if ($gift_card['uses'] == 0 
                || $time > $gift_card['end_date']
                || $gift_card['balance'] == 0)
                {
                    continue;
                }
            $data[] = $gift_card;
        }
        return $data;
    }

    //}}}
    // {{{ public static function get_expired_gift_cards()
    public static function get_expired_gift_cards()
    {
        $gift_cards = EcommerceAPI::get_gift_cards();
        $time = strtotime('now');
        $data = array();
        foreach ($gift_cards as $gift_card)
        {
            if ($gift_card['uses'] == 0 
                || $time > $gift_card['end_date']
                || $gift_card['balance'] == 0)
                {
                    $data[] = $gift_card;
                }
        }
        return $data;
    }

    //}}}
    // {{{ public static function is_valid_gift_card($code)
    public static function is_valid_gift_card($code)
    {
        $gift_card = EcommerceAPI::get_gift_card_by_code($code);
        if (empty($gift_card))
        {
            return FALSE;
        }
        $time = strtotime('now');
        $data = array();
        if ($gift_card['uses'] == 0
            || $gift_card['balance'] <= 0 
            || $time > $gift_card['end_date'])
            {
                return FALSE;
            }
        return TRUE;
    }

    //}}}
    // {{{ public static function use_gift_card($code, $amount, $data = array())
    public static function use_gift_card($code, $amount, $data = array())
    {
        $data = FALSE;
        if (self::is_valid_gift_card($code) && is_numeric($amount))
        {
            $egct = Doctrine::getTable('EcommerceGiftCard');
            $egc = $egct->findOneByCode($code);
            $misc = (array)$egc->misc;
            if (!empty($data))
            {
                $misc[] = $data;
            }
            $egc->balance -= $amount;
            $egc->uses--;
            if ($egc->isModified() && $egc->isValid())
            {
                $egc->save();
                $data = $egc->toArray();
                $egc->free();
            }
        }
        return $data;
    }

    //}}}

    // {{{ public static function get_email_accounts($account = NULL)
    public static function get_email_accounts($account = NULL)
    {
        require 'config.accounts.php';
        return is_null($account)
            ? $_accounts
            : deka(array(), $_accounts, $account);
    }

    //}}}

    // {{{ public static function get_order_keys()
    public static function get_order_keys()
    {
        $keys = Data::query('Ecommerce', 'order_keys');
        if (!is_array($keys))
        {
            $keys = array();
        }
        return $keys;
    }

    //}}}
    // {{{ public static function get_next_order_number()
    public static function get_next_order_number()
    {
        $number = Data::query('Ecommerce', 'order_number');
        if (is_null($number))
        {
            $number = 10001;
        }
        return $number;
    }

    //}}}
    // {{{ public static function use_order_number()
    public static function use_order_number()
    {
        $number = Data::query('Ecommerce', 'order_number');
        if (is_null($number))
        {
            $number = 10001;
        }
        Data::update('Ecommerce', 'order_number', (string)($number + 1));
        Data::save();
        return $number;
    }

    //}}}
    // {{{ public static function get_product_keys()
    public static function get_product_keys()
    {
        $keys = Data::query('Ecommerce', 'product_keys');
        if (!is_array($keys))
        {
            $keys = array();
        }
        return $keys;
    }

    //}}}

    // {{{ public static function get_status_options()
    public static function get_status_options()
    {
        $statuses = EcommerceAPI::get_statuses();
        $options = array();
        foreach ($statuses as $status)
        {
            $options[$status['type']][$status['id']] = $status['name'];
        }
        return empty($options)
            ? array('None' => array('' => 'None'))
            : $options;
    }

    //}}}
    // {{{ public static function get_month_options()
    public static function get_month_options()
    {
        return array(
            '01' => 'January',
            '02' => 'February',
            '03' => 'March',
            '04' => 'April',
            '05' => 'May',
            '06' => 'June',
            '07' => 'July',
            '08' => 'August',
            '09' => 'September',
            '10' => 'October',
            '11' => 'November',
            '12' => 'December',
        );
    }

    //}}}
    // {{{ public static function get_ca_provinces()
    public static function get_ca_provinces()
    {
        $provinces = array(
            "AB" => 'Alberta',
            "BC" => 'British Columbia',
            "MB" => 'Manitoba',
            "NB" => 'New Brunswick',
            "NF" => 'Newfoundland',
            "NT" => 'Northwest Territories',
            "NS" => 'Nova Scotia',
            "NU" => 'Nunavut',
            "ON" => 'Ontario',
            "PE" => 'Prince Edward Island',
            "QC" => 'Quebec',
            "SK" => 'Saskatchewan',
            "YT" => 'Yukon Territory',
        );

        asort($provinces);
        return $provinces;
    }

    //}}}
    // {{{ public static function get_us_states()
    public static function get_us_states()
    {
        $states = array(
            "AK" => 'Alaska',
            "AL" => 'Alabama',
            "AR" => 'Arkansas',
            "AZ" => 'Arizona',
            "CA" => 'California',
            "CO" => 'Colorado',
            "CT" => 'Connecticut',
            "DC" => 'District of Columbia',
            "DE" => 'Delaware',
            "FL" => 'Florida',
            "GA" => 'Georgia',
            "HI" => 'Hawaii',
            "IA" => 'Iowa',
            "ID" => 'Idaho',
            "IL" => 'Illinois',
            "IN" => 'Indiana',
            "KS" => 'Kansas',
            "KY" => 'Kentucky',
            "LA" => 'Louisiana',
            "MA" => 'Massachusetts',
            "MD" => 'Maryland',
            "ME" => 'Maine',
            "MI" => 'Michigan',
            "MN" => 'Minnesota',
            "MO" => 'Missouri',
            "MS" => 'Mississippi',
            "MT" => 'Montana',
            "NC" => 'North Carolina',
            "ND" => 'North Dakota',
            "NE" => 'Nebraska',
            "NH" => 'New Hampshire',
            "NJ" => 'New Jersey',
            "NM" => 'New Mexico',
            "NV" => 'Nevada',
            "NY" => 'New York',
            "OH" => 'Ohio',
            "OK" => 'Oklahoma',
            "OR" => 'Oregon',
            "PA" => 'Pennsylvania',
            "PR" => 'Puerto Rico',
            "RI" => 'Rhode Island',
            "SC" => 'South Carolina',
            "SD" => 'South Dakota',
            "TN" => 'Tennessee',
            "TX" => 'Texas',
            "UT" => 'Utah',
            "VA" => 'Virginia',
            "VT" => 'Vermont',
            "WA" => 'Washington',
            "WI" => 'Wisconsin',
            "WV" => 'West Virginia',
            "WY" => 'Wyoming',
        );

        asort($states);
        return $states;
    }

    //}}}

    // {{{ public static function get_ups_credentials()
    public static function get_ups_credentials()
    {
        return array(
            'username' => is_null(Data::query('Ecommerce', 'ups_username'))
                ? ''
                : Data::query('Ecommerce', 'ups_username'),
            'password' => is_null(Data::query('Ecommerce', 'ups_password'))
                ? ''
                : Data::query('Ecommerce', 'ups_password'),
            'access_key' => is_null(Data::query('Ecommerce', 'ups_access_key'))
                ? ''
                : Data::query('Ecommerce', 'ups_access_key'),
            'shipper_number' => is_null(Data::query('Ecommerce', 'ups_shipper_number'))
                ? ''
                : Data::query('Ecommerce', 'ups_shipper_number'),
        );

    }
    //}}}
    // {{{ public static function get_ups_rates($ship_to, $dimensions, $services = array(), $ship_from = array(), $shipper = array())
    public static function get_ups_rates($ship_to, $dimensions, $services = array(), $ship_from = array(), $shipper = array())
    {
        $attr_defaults = array(
            'Length' => 0,
            'Width' => 0,
            'Height' => 0,
            'Weight' => 0,
        );
        $dimensions = array_merge($attr_defaults, $dimensions);
        $creds = self::get_ups_credentials();
        include_once dirname(__FILE__).'/libs/upsRate.php';
        $rate = new upsRate;
        $rate->setCredentials($creds['access_key'], $creds['username'], $creds['password'], $creds['shipper_number']);
        $rates = array();

        if (empty($shipper))
        {
            // defaults
            $shipper = array(
                'AddressLine1' => '6100 Melrose',
                'City' => 'Los Angeles',
                'StateProvinceCode' => 'CA',
                'PostalCode' => self::get_ups_zip(),
                'CountryCode' => 'US',
            );
        }
        if (empty($ship_from))
        {
            // defaults
            $ship_from = array(
                'AddressLine1' => '6100 Melrose',
                'City' => 'Los Angeles',
                'StateProvinceCode' => 'CA',
                'PostalCode' => self::get_ups_zip(),
                'CountryCode' => 'US',
            );
        }
        if (is_string($ship_to))
        {
            $ship_to = array(
                'PostalCode' => $ship_to,
                'CountryCode' => 'US',
            );
        }

        // get all rates
        if (empty($services))
        {
            $services = array_keys(self::get_ups_services());
            $data = $rate->getRate($shipper, $ship_from, $ship_to, '03', $dimensions);
            foreach ($services as $id)
            {
                if (ake($id, self::$ups_services) && ake($id, $data))
                {
                    $rates[$id]['price'] = $data[$id];
                    $rates[$id]['name'] = self::$ups_services[$id];
                }
            }
        }
        else
        {
            foreach ($services as $id)
            {
                if (ake($id, self::$ups_services))
                {
                    $rates[$id]['price'] = $rate->getRate($shipper, $ship_from, $ship_to, $id, $dimensions);
                    $rates[$id]['name'] = self::$ups_services[$id];
                }
            }
        }
        return $rates;
    }
    //}}}
    // {{{ public static function get_ups_services()
    public static function get_ups_services()
    {
        $data = Data::query('Ecommerce', 'ups_services');
        $services = array();
        if (is_null(Data::query('Ecommerce', 'ups_services')))
        {
            $services = self::$ups_services;
        }
        else
        {
            foreach (self::$ups_services as $id => $service)
            {
                if (array_search($id, $data) !== FALSE)
                {
                    $services[$id] = $service;
                }
            }
        }
        return $services;
    }
    //}}}
    // {{{ public static function get_ups_zip()
    public static function get_ups_zip()
    {
        return is_null(Data::query('Ecommerce', 'ups_shipper_zip'))
            ? ''
            : Data::query('Ecommerce', 'ups_shipper_zip');
    }
    //}}}

    // {{{ public static function set_exchange_rate($from_code, $to_code)
    public static function set_exchange_rate($from_code, $to_code)
    {
        $rates = Data::query('Ecommerce', 'exchange_rates');
        $rates_last_updated = Data::query('Ecommerce', 'exchange_rates_updated');
        if (is_null($rates))
        {
            $rates = array();
        }
        $rate = deka(0, $rates, $from_code, $to_code);
        $last_updated = deka(0, $rates_last_updated, $from_code, $to_code);
        if ($rate === 0 || $last_updated < (time() - (60 * 60 * 24)))
        {
            $google_url = "http://www.google.com/ig/calculator?hl=en&q=1$from_code=?$to_code";
            $contents = file_get_contents($google_url);
        }
         
        //Get and Store API results into a variable
        if (isset($contents) && $contents !== FALSE)
        {
            $result = (array)json_decode(str_replace(array('lhs','rhs','error','icc'), array('"lhs"','"rhs"','"error"','"icc"'), $contents));
            $rate = number_format($result['rhs'], 2);
            $rates[$from_code][$to_code] = $rate;
            $rates_last_updated[$from_code][$to_code] = time();
            Data::update('Ecommerce', 'exchange_rates', $rates);
            Data::update('Ecommerce', 'exchange_rates_updated', $rates_last_updated);
            Data::save();
        }
        return $rate;
    }
    //}}}
    // {{{ public static function get_currencies()
    public static function get_currencies()
    {
        return self::$currencies;
    }
    //}}}
    // {{{ public static function get_currency_conversion($price, $from_code, $to_code)
    public static function get_currency_conversion($price, $from_code, $to_code = NULL)
    {
        $data = array();
        if (is_null($to_code))
        {
            $to_code = array_keys(self::get_currencies());
        }
        foreach ($to_code as $code)
        {
            if ($from_code === $code)
            {
                continue;
            }
            $rate = self::set_exchange_rate($from_code, $code);
            if ($rate !== 0)
            {
                $data[$code] = number_format($price * $rate, 2);
            }
        }
        return $data;
    }
    //}}}

    // {{{ public static function get_tracking_link($number = NULL, $service = NULL)
    public static function get_tracking_link($number = NULL, $service = NULL)
    {
        $data = array();
        if (is_null($service))
        {
            foreach (self::$tracking_links as $k => $v)
            {
                $data[$k] = is_null($number)
                    ? $v
                    : sprintf($v, $number);
            }
        }
        else
        {
            $data = '';
            $link = deka(NULL, self::$tracking_links, $service);
            if (!is_null($link))
            {
                $data = is_null($number)
                    ? $link
                    : sprintf($link, $number);
            }
        }
        return $data;
    }
    //}}}
    //{{{ public static function get_available_templates()
    public static function get_available_templates()
    {
        $templates = scandir(self::$templates_dir);
        if (!is_array($templates))
        {
            $templates = array();
        }
        foreach ($templates as $k => $v)
        {
            if (!(strpos($v, '.tpl.php') === FALSE))
            {
                $name = ucwords(str_replace('_', ' ', str_replace('.tpl.php', '', $v)));
                $templates[$v] = $name;
            }
            unset($templates[$k]);
        }
        return $templates;
    }

    //}}}

    // {{{ public static function get_paypal_countries($filtered_countries = array())
    public static function get_paypal_countries($filtered_countries = array())
    {
        $countries = array(
            'AX' => 'ÅLAND ISLANDS',
            'AL' => 'ALBANIA',
            'DZ' => 'ALGERIA',
            'AS' => 'AMERICAN SAMOA',
            'AD' => 'ANDORRA',
            'AI' => 'ANGUILLA',
            'AQ' => 'ANTARCTICA',
            'AG' => 'ANTIGUA AND BARBUDA',
            'AR' => 'ARGENTINA',
            'AM' => 'ARMENIA',
            'AW' => 'ARUBA',
            'AU' => 'AUSTRALIA',
            'AT' => 'AUSTRIA',
            'BS' => 'BAHAMAS',
            'BH' => 'BAHRAIN',
            'BB' => 'BARBADOS',
            'BE' => 'BELGIUM',
            'BZ' => 'BELIZE',
            'BJ' => 'BENIN',
            'BM' => 'BERMUDA',
            'BT' => 'BHUTAN',
            'BW' => 'BOTSWANA',
            'BV' => 'BOUVET ISLAND',
            'BR' => 'BRAZIL',
            'IO' => 'BRITISH INDIAN OCEAN TERRITORY',
            'BN' => 'BRUNEI DARUSSALAM',
            'BG' => 'BULGARIA',
            'BF' => 'BURKINA FASO',
            'CA' => 'CANADA',
            'CV' => 'CAPE VERDE',
            'KY' => 'CAYMAN ISLANDS',
            'CF' => 'CENTRAL AFRICAN REPUBLIC',
            'CL' => 'CHILE',
            'CN' => 'CHINA',
            'CX' => 'CHRISTMAS ISLAND',
            'CC' => 'COCOS (KEELING) ISLANDS',
            'CO' => 'COLOMBIA',
            'CK' => 'COOK ISLANDS',
            'CR' => 'COSTA RICA',
            'CY' => 'CYPRUS',
            'CZ' => 'CZECH REPUBLIC',
            'DK' => 'DENMARK',
            'DJ' => 'DJIBOUTI',
            'DM' => 'DOMINICA',
            'DO' => 'DOMINICAN REPUBLIC',
            'EG' => 'EGYPT',
            'SV' => 'EL SALVADOR',
            'EE' => 'ESTONIA',
            'FK' => 'FALKLAND ISLANDS (MALVINAS)',
            'FO' => 'FAROE ISLANDS',
            'FJ' => 'FIJI',
            'FI' => 'FINLAND',
            'FR' => 'FRANCE',
            'GF' => 'FRENCH GUIANA',
            'PF' => 'FRENCH POLYNESIA',
            'TF' => 'FRENCH SOUTHERN TERRITORIES',
            'GM' => 'GAMBIA',
            'GE' => 'GEORGIA',
            'DE' => 'GERMANY',
            'GH' => 'GHANA',
            'GI' => 'GIBRALTAR',
            'GR' => 'GREECE',
            'GL' => 'GREENLAND',
            'GD' => 'GRENADA',
            'GP' => 'GUADELOUPE',
            'GU' => 'GUAM',
            'GG' => 'GUERNSEY',
            'HM' => 'HEARD ISLAND AND MCDONALD ISLANDS',
            'VA' => 'HOLY SEE (VATICAN CITY STATE)',
            'HN' => 'HONDURAS',
            'HK' => 'HONG KONG',
            'HU' => 'HUNGARY',
            'IS' => 'ICELAND',
            'IN' => 'INDIA',
            'ID' => 'INDONESIA',
            'IE' => 'IRELAND',
            'IM' => 'ISLE OF MAN',
            'IL' => 'ISRAEL',
            'IT' => 'ITALY',
            'JM' => 'JAMAICA',
            'JP' => 'JAPAN',
            'JE' => 'JERSEY',
            'JO' => 'JORDAN',
            'KZ' => 'KAZAKHSTAN',
            'KI' => 'KIRIBATI',
            'KR' => 'KOREA, REPUBLIC OF',
            'KW' => 'KUWAIT',
            'KG' => 'KYRGYZSTAN',
            'LV' => 'LATVIA',
            'LS' => 'LESOTHO',
            'LI' => 'LIECHTENSTEIN',
            'LT' => 'LITHUANIA',
            'LU' => 'LUXEMBOURG',
            'MO' => 'MACAO',
            'MW' => 'MALAWI',
            'MY' => 'MALAYSIA',
            'MT' => 'MALTA',
            'MH' => 'MARSHALL ISLANDS',
            'MQ' => 'MARTINIQUE',
            'MR' => 'MAURITANIA',
            'MU' => 'MAURITIUS',
            'YT' => 'MAYOTTE',
            'MX' => 'MEXICO',
            'FM' => 'MICRONESIA, FEDERATED STATES OF',
            'MD' => 'MOLDOVA, REPUBLIC OF',
            'MC' => 'MONACO',
            'MN' => 'MONGOLIA',
            'MS' => 'MONTSERRAT',
            'MA' => 'MOROCCO',
            'MZ' => 'MOZAMBIQUE',
            'NA' => 'NAMIBIA',
            'NR' => 'NAURU',
            'NP' => 'NEPAL',
            'NL' => 'NETHERLANDS',
            'AN' => 'NETHERLANDS ANTILLES',
            'NC' => 'NEW CALEDONIA',
            'NZ' => 'NEW ZEALAND',
            'NI' => 'NICARAGUA',
            'NE' => 'NIGER',
            'NU' => 'NIUE',
            'NF' => 'NORFOLK ISLAND',
            'MP' => 'NORTHERN MARIANA ISLANDS',
            'NO' => 'NORWAY',
            'OM' => 'OMAN',
            'PW' => 'PALAU',
            'PA' => 'PANAMA',
            'PY' => 'PARAGUAY',
            'PE' => 'PERU',
            'PH' => 'PHILIPPINES',
            'PN' => 'PITCAIRN',
            'PL' => 'POLAND',
            'PT' => 'PORTUGAL',
            'PR' => 'PUERTO RICO',
            'QA' => 'QATAR',
            'RE' => 'REUNION',
            'RO' => 'ROMANIA',
            'SH' => 'SAINT HELENA',
            'KN' => 'SAINT KITTS AND NEVIS',
            'LC' => 'SAINT LUCIA',
            'PM' => 'SAINT PIERRE AND MIQUELON',
            'VC' => 'SAINT VINCENT AND THE GRENADINES',
            'WS' => 'SAMOA',
            'SM' => 'SAN MARINO',
            'ST' => 'SAO TOME AND PRINCIPE',
            'SA' => 'SAUDI ARABIA',
            'SN' => 'SENEGAL',
            'SC' => 'SEYCHELLES',
            'SG' => 'SINGAPORE',
            'SK' => 'SLOVAKIA',
            'SI' => 'SLOVENIA',
            'SB' => 'SOLOMON ISLANDS',
            'ZA' => 'SOUTH AFRICA',
            'GS' => 'SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS',
            'ES' => 'SPAIN',
            'SR' => 'SURINAME',
            'SJ' => 'SVALBARD AND JAN MAYEN',
            'SZ' => 'SWAZILAND',
            'SE' => 'SWEDEN',
            'CH' => 'SWITZERLAND',
            'TW' => 'TAIWAN, PROVINCE OF CHINA',
            'TZ' => 'TANZANIA, UNITED REPUBLIC OF',
            'TH' => 'THAILAND',
            'TK' => 'TOKELAU',
            'TO' => 'TONGA',
            'TT' => 'TRINIDAD AND TOBAGO',
            'TN' => 'TUNISIA',
            'TR' => 'TURKEY',
            'TC' => 'TURKS AND CAICOS ISLANDS',
            'TV' => 'TUVALU',
            'UA' => 'UKRAINE',
            'AE' => 'UNITED ARAB EMIRATES',
            'GB' => 'UNITED KINGDOM',
            'US' => 'UNITED STATES',
            'UM' => 'UNITED STATES MINOR OUTLYING ISLANDS',
            'UY' => 'URUGUAY',
            'VN' => 'VIET NAM',
            'VG' => 'VIRGIN ISLANDS, BRITISH',
            'VI' => 'VIRGIN ISLANDS, U.S.',
            'WF' => 'WALLIS AND FUTUNA',
            'ZM ' => 'ZAMBIA',
        );

        asort($countries);
        if (!empty($filtered_countries))
        {
            $countries = array_uintersect_uassoc($filtered_countries, $countries, 'strcasecmp', 'strcasecmp');
        }
        return $countries;
    }

    //}}}
    // {{{ public static function get_paypal_credentials($environment = 'live')
    public static function get_paypal_credentials($environment = 'live')
    {
        if ($environment === 'sandbox' || $environment === 'beta-sandbox')
        {
            $paypal['username'] = is_null(Data::query('Ecommerce', 'paypal_api_sandbox_user'))
                ? ''
                : Data::query('Ecommerce', 'paypal_api_sandbox_user');
            $paypal['password'] = is_null(Data::query('Ecommerce', 'paypal_api_sandbox_pass'))
                ? ''
                : Data::query('Ecommerce', 'paypal_api_sandbox_pass');
            $paypal['signature'] = is_null(Data::query('Ecommerce', 'paypal_api_sandbox_sig'))
                ? ''
                : Data::query('Ecommerce', 'paypal_api_sandbox_sig');
            $paypal['endpoint'] = "https://api-3t.$environment.paypal.com/nvp";
        }
        else
        {
            $paypal['username'] = is_null(Data::query('Ecommerce', 'paypal_api_user'))
                ? ''
                : Data::query('Ecommerce', 'paypal_api_user');
            $paypal['password'] = is_null(Data::query('Ecommerce', 'paypal_api_pass'))
                ? ''
                : Data::query('Ecommerce', 'paypal_api_pass');
            $paypal['signature'] = is_null(Data::query('Ecommerce', 'paypal_api_sig'))
                ? ''
                : Data::query('Ecommerce', 'paypal_api_sig');
            $paypal['endpoint'] = "https://api-3t.paypal.com/nvp";
        }
        return $paypal;
    }

    //}}}
    // {{{ public static function is_using_paypal($environment = 'live')
    public static function is_using_paypal($environment = 'live')
    {
        $paypal_key = $environment === 'sandbox' || $environment === 'beta-sandbox'
            ? 'paypal_sandbox'
            : 'paypal';
        return is_null(Data::query('Ecommerce', $paypal_key))
            ? FALSE
            : Data::query('Ecommerce', $paypal_key);
    }

    //}}}
    // {{{ public static function paypal_request($method_name, $nvp_query, $environment = 'live', $api_version = 65.1)
    /**
     * Send HTTP POST Request
     *
     * @param	string	The API method name
     * @param	string	The POST Message fields in &name=value pair format
     * @param	string	The environment settings to use
     * @param	array   The credentials override
     * @param	float   The version of the API to use
     * @return	array	Parsed HTTP Response body
     */
    public static function paypal_request($method_name, $nvp_query, $environment = 'live', $credentials = array(), $api_version = 65.1)
    {
        // Set up your API credentials, PayPal end point, and API version.
        if (empty($credentials))
        {
            $credentials = self::get_paypal_credentials($environment);
        }
        $version = urlencode($api_version);

        // Set the curl parameters.
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $credentials['endpoint']);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);

        // Turn off the server and peer verification (TrustManager Concept).
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);

        // Set the API operation, version, and API signature in the request.
        $nvpreq = "METHOD=$method_name&VERSION=$version&PWD={$credentials['password']}&USER={$credentials['username']}&SIGNATURE={$credentials['signature']}$nvp_query";

        // Set the request as a POST FIELD for curl.
        curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);

        // Get response from the server.
        $http_response = curl_exec($ch);

        if (!$http_response) 
        {
            throw new Exception ("$method_name failed: ".curl_error($ch).'('.curl_errno($ch).')');
        }

        // Extract the response details.
        $http_response_array = explode("&", $http_response);

        $http_parsed_response_array = array();
        foreach ($http_response_array as $i => $value)
        {
            $tmpAr = explode("=", $value);
            if (sizeof($tmpAr) > 1) 
            {
                $http_parsed_response_array[$tmpAr[0]] = $tmpAr[1];
            }
        }

        if ((0 == sizeof($http_parsed_response_array)) || !array_key_exists('ACK', $http_parsed_response_array)) 
        {
            throw new Exception ("Invalid HTTP Response for POST request($nvpreq) to $API_Endpoint.");
        }

        return $http_parsed_response_array;
    }
    // }}}
}
// }}}
// {{{ class EcommerceAPI
class EcommerceAPI
{
    // {{{ public static function get_cart($id)
    public static function get_cart($id)
    {
        $ect = Doctrine::getTable('EcommerceCart');
        $ec = $ect->findOneByIdentifier($id);
        if ($ec === FALSE)
        {
            return NULL;
        }
        else
        {
            $data = $ec->data;
            $ec->free();
            return $data;
        }
    }
    // }}}
    // {{{ public static function get_coupon_by_code($code)
    public static function get_coupon_by_code($code)
    {
        $params = array();
        $spec = array(
            'select' => array(
                '*'
            ),
            'from' => 'EcommerceCoupon ec',
        );
        $spec['where'] = 'ec.code = ?';
        $params[] = $code;

        $coupon = dql_exec($spec, $params);
        return count($coupon)
            ? $coupon[0]
            : array();
    }
    // }}}
    // {{{ public static function get_coupons($type = NULL)
    public static function get_coupons($type = NULL)
    {
        $params = array();
        $spec = array(
            'select' => array(
                '*'
            ),
            'from' => 'EcommerceCoupon ec',
            'orderBy' => 'ec.code ASC',
        );
        if (is_string($type))
        {
            $spec['where'] = 'ec.type = ?';
            $params[] = $type;
        }
        $coupons = dql_exec($spec, $params);
        return $coupons;
    }
    // }}}
    // {{{ public static function get_gift_card_by_code($code)
    public static function get_gift_card_by_code($code)
    {
        $params = array();
        $spec = array(
            'select' => array(
                '*'
            ),
            'from' => 'EcommerceGiftCard egc',
        );
        $spec['where'] = 'egc.code = ?';
        $params[] = $code;

        $gift_card = dql_exec($spec, $params);
        return count($gift_card)
            ? $gift_card[0]
            : array();
    }
    // }}}
    // {{{ public static function get_gift_cards()
    public static function get_gift_cards()
    {
        $params = array();
        $spec = array(
            'select' => array(
                '*'
            ),
            'from' => 'EcommerceGiftCard egc',
            'orderBy' => 'egc.code ASC',
        );
        $gift_cards = dql_exec($spec, $params);
        return $gift_cards;
    }
    // }}}
    // {{{ public static function get_order_by_id($id)
    public static function get_order_by_id($id)
    {
        $eot = Doctrine::getTable('EcommerceOrder');
        $order = $eot->find($id, Doctrine::HYDRATE_ARRAY);
        return $order;
    }
    // }}}
    // {{{ public static function get_order_details_by_id($id)
    public static function get_order_details_by_id($id)
    {
        $eot = Doctrine::getTable('EcommerceOrder');
        $eo = $eot->find($id);
        $eo->Products;
        $eo->BillingAddress;
        $eo->ShippingAddress;
        $eo->Options;
        $eo->Coupons;
        $eo->GiftCards;
        $eo->Status;

        foreach ($eo->Products as $product)
        {
            $product->Options;
        }

        $order = $eo->toArray(TRUE);
        $eo->free();

        foreach ($order['Products'] as &$product)
        {
            foreach ($product['Options'] as $option)
            {
                $product['options'][$option['name']] = $option['data'];
            }
        }
        foreach ($order['Options'] as &$option)
        {
            $order['options'][$option['name']] = $option['data'];
        }
        return $order;
    }
    // }}}
    // {{{ public static function get_orders()
    public static function get_orders()
    {
        $params = array();
        $spec = array(
            'select' => array(
                '*'
            ),
            'from' => 'EcommerceOrder eo',
        );
        $orders = dql_exec($spec, $params);
        return $orders;
    }
    // }}}
    // {{{ public static function get_orders_paginated($filters = array(), $page = 1, $rows = 25)
    public static function get_orders_paginated($filters = array(), $page = 1, $rows = 25)
    {
        $spec = array(
            'select' => array(
                'eo.*', 'sa.state as state', 'sa.country as country', 
                'sa.address1 as address1', 'sa.city as city', 'sa.zipcode as zip', 
                'ba.name as name', 'st.name as status',
                'rst.name as return_status', 'op.*'
            ),
            'from' => 'EcommerceOrder eo',
            'leftJoin' => array(
                'eo.ShippingAddress sa',
                'eo.BillingAddress ba',
                'eo.Status st',
                'eo.ReturnStatus rst',
                'eo.Options op',
            ),
        );
        if (is_numeric($filters['start_date']))
        {
            $spec['addWhere'][] = array('eo.created_date >= ?' => (int)$filters['start_date']);
        }
        if (is_numeric($filters['end_date']))
        {
            $spec['addWhere'][] = array('eo.created_date <= ?' => strtotime('+1 day', (int)$filters['end_date']));
        }
        if (!empty($filters['return_statuses']))
        {
            $where = '( ';
            $key = array_search('NULL', $filters['return_statuses']);
            if ($key !== FALSE) 
            {
                $where .= 'eo.return_status_id IS NULL OR ';
                // $filters['return_statuses'][$key] = NULL;
                // $spec['andWhere'][] = 'eo.return_status_id IS NULL';
                // unset($filters['return_statuses'][$key]);
            }
            // $spec['andWhereIn'][] = array('eo.return_status_id' => $filters['return_statuses']);
            $where .= 'eo.return_status_id IN (' . implode(',', $filters['return_statuses']) .'))';
            $spec['addWhere'][] = $where;
        }
        if (!empty($filters['statuses']))
        {
            $spec['andWhereIn'][] = array('eo.order_status_id' => $filters['statuses']);
        }
        if (strlen($filters['name']))
        {
            $spec['addWhere'][] = array('ba.name LIKE ?' => '%' . $filters['name'] . '%');
        }
        if (strlen($filters['order']))
        {
            $spec['addWhere'][] = array('eo.order_name LIKE ?' => '%' . $filters['order'] . '%');
        }
        if (strlen($filters['email']))
        {
            $spec['addWhere'][] = array('eo.customer_email LIKE ?' => '%' . $filters['email'] . '%');
        }
        if (strlen($filters['state']))
        {
            $spec['addWhere'][] = array('sa.state = ?' => $filters['state']);
        }
        if (eka($filters, 'sort', 'type'))
        {
            $spec['orderBy'][] = 'eo.'.$filters['sort']['type'].' '.$filters['sort']['order'];
        }
        $spec['orderBy'][] = 'eo.created_date DESC';
        $dql = dql_build($spec);

        if (!is_numeric($rows))
        {
            $orders['items'] = $dql->execute(array(), Doctrine::HYDRATE_ARRAY);
            $orders['total_items'] = count($orders['items']);
        }
        else
        {
            $pager = new Doctrine_Pager($dql, $page, $rows);
            $orders['items'] = $pager->execute(array(), Doctrine::HYDRATE_ARRAY);
            $orders['total_items'] = $pager->getNumResults();
        }

        return $orders;
    }
    // }}}
    // {{{ public static function get_products_paginated($filters = array(), $page = 1, $rows = 25)
    public static function get_products_paginated($filters = array(), $page = 1, $rows = 25)
    {
        $default = array_fill_keys(
            array('start_date', 'end_date', 'state', 'name', 'sort'),
            ''
        );
        $default['product_type'] = array();
        $filters = array_merge($default, $filters);
        $spec = array(
            'select' => array(
                'ep.*', 'eo.order_name as order_name', 'eo.id as order_id',
                'eo.customer_email as customer_email', 'eo.created_date as created_date',
                'eo.subtotal as subtotal', 'eo.discount as c_discount',
                'eo.gift_card_discount as gc_discount', 'eo.total as total',
                '(eo.gift_card_discount + eo.discount) as discount',
                'eo.returned_order_name',
                'sa.state as state', 'sa.country as country', 
                'sa.city as city', 'sa.zipcode as zip', 
                'ba.name as customer_name', 'eop.*', 'eoo.*',
            ),
            'from' => 'EcommerceProduct ep',
            'leftJoin' => array(
                'ep.Options eop',
                'ep.Order eo',
                'eo.Options eoo',
                'eo.ShippingAddress sa',
                'eo.BillingAddress ba',
            ),
        );
        if (is_numeric($filters['start_date']))
        {
            $spec['addWhere'][] = array('eo.created_date >= ?' => (int)$filters['start_date']);
        }
        if (is_numeric($filters['end_date']))
        {
            $spec['addWhere'][] = array('eo.created_date <= ?' => strtotime('+1 day', (int)$filters['end_date']));
        }
        if (strlen($filters['state']))
        {
            $spec['addWhere'][] = array('sa.state = ?' => $filters['state']);
        }
        if (strlen($filters['name']))
        {
            $spec['addWhere'][] = array('ep.name LIKE ?' => '%'.$filters['name'].'%');
        }
        if (strlen(eka('', $filters, 'sku')))
        {
            $spec['addWhere'][] = array('ep.sku LIKE ?' => '%'.$filters['sku'].'%');
        }
        if ($filters['product_type'])
        {
            $spec['addWhere'][] = 'ep.id IN (SELECT nep.id FROM EcommerceProduct nep WHERE nep.Options.name = "type" AND nep.Options.data IN ("' . implode('","', $filters['product_type']) . '"))';
        }
        if (eka($filters, 'sort', 'type'))
        {
            $spec['orderBy'][] = $filters['sort']['type'].' '.$filters['sort']['order'];
        }
        $spec['orderBy'][] = 'eo.created_date DESC';
        $dql = dql_build($spec);

        if (!is_numeric($rows))
        {
            $products['items'] = $dql->execute(array(), Doctrine::HYDRATE_ARRAY);
            $products['total_items'] = count($products['items']);
        }
        else
        {
            $pager = new Doctrine_Pager($dql, $page, $rows);
            $products['items'] = $pager->execute(array(), Doctrine::HYDRATE_ARRAY);
            $products['total_items'] = $pager->getNumResults();
        }

        return $products;
    }
    // }}}
    // {{{ public static function get_status_types()
    public static function get_status_types()
    {
        $spec = array(
            'select' => array(
                'DISTINCT eos.type as type'
            ),
            'from' => 'EcommerceOrderStatus eos',
            'orderBy' => 'type ASC',
        );
        $types = dql_exec($spec);
        $tmp = array();
        foreach ($types as $type)
        {
            $tmp[] = $type['type'];
        }
        return $tmp;
    }
    // }}}
    // {{{ public static function get_status_by_type_and_name($type, $name)
    public static function get_status_by_type_and_name($type, $name)
    {
        $spec = array(
            'select' => array(
                '*'
            ),
            'addWhere' => array(
                'eos.type = ?',
                'eos.name = ?',
            ),
            'from' => 'EcommerceOrderStatus eos',
            'orderBy' => 'eos.type ASC, eos.name ASC',
        );
        $params[] = $type;
        $params[] = $name;
        $statuses = dql_exec($spec, $params);
        return count($statuses)
            ? $statuses[0]
            : array();
    }
    // }}}
    // {{{ public static function get_statuses()
    public static function get_statuses()
    {
        $params = array();
        $spec = array(
            'select' => array(
                '*'
            ),
            'from' => 'EcommerceOrderStatus eos',
            'orderBy' => 'eos.type ASC, eos.name ASC',
        );
        $statuses = dql_exec($spec, $params);
        return $statuses;
    }
    // }}}
    // {{{ public static function get_statuses_by_type($type)
    public static function get_statuses_by_type($type)
    {
        $spec = array(
            'select' => array(
                '*'
            ),
            'where' => 'eos.type = ?',
            'from' => 'EcommerceOrderStatus eos',
            'orderBy' => 'eos.type ASC, eos.name ASC',
        );
        $params[] = $type;
        $statuses = dql_exec($spec, $params);
        return $statuses;
    }
    // }}}

    // {{{ public static function set_cart($id, $data)
    public static function set_cart($id, $data)
    {
        $cart = self::get_cart($id);
        if (is_null($cart))
        {
            $ec = new EcommerceCart;
            $ec->identifier = $id;
        }
        else
        {
            $ect = Doctrine::getTable('EcommerceCart');
            $ec = $ect->findOneByIdentifier($id);
            $ec->data = $data;
        }
        $ec->data = $data;
        if ($ec->isValid())
        {
            $ec->save();
            $ec->free();
            return TRUE;
        }
        else
        {
            $ec->free();
            return FALSE;
        }
    }
    // }}}

    // {{{ public static function delete_cart($id)
    public static function delete_cart($id)
    {
        $ect = Doctrine::getTable('EcommerceCart');
        $ec = $ect->findOneByIdentifier($id);
        if ($ec !== FALSE)
        {
            $ec->delete();
            $ec->free();
            return TRUE;
        }
        return FALSE;
    }
    // }}}
    // {{{ public static function delete_coupon_by_id($id)
    public static function delete_coupon_by_id($id)
    {
        try
        {
            $query = Doctrine_Query::create()
                ->delete('EcommerceOrderCoupons')
                ->addWhere('coupon_id = ?', $id);
            $deleted = $query->execute();
            
            $eost = Doctrine::getTable('EcommerceCoupon');
            $coupon = $eost->find($id);
            $coupon->delete();
            return TRUE;
        }
        catch (Exception $e)
        {
            return FALSE;
        }
    }
    // }}}
    // {{{ public static function delete_gift_card_by_id($id)
    public static function delete_gift_card_by_id($id)
    {
        try
        {
            $query = Doctrine_Query::create()
                ->delete('EcommerceOrderGiftCards')
                ->addWhere('gift_card_id = ?', $id);
            $deleted = $query->execute();

            $eost = Doctrine::getTable('EcommerceGiftCard');
            $gift_card = $eost->find($id);
            $gift_card->delete();
            return TRUE;
        }
        catch (Exception $e)
        {
            return FALSE;
        }
    }
    // }}}
    // {{{ public static function delete_status_by_id($id)
    public static function delete_status_by_id($id)
    {
        try
        {
            $eost = Doctrine::getTable('EcommerceOrderStatus');
            $status = $eost->find($id);
            $status->delete();
            return TRUE;
        }
        catch (Exception $e)
        {
            return FALSE;
        }
    }
    // }}}
}
// }}}

?>
