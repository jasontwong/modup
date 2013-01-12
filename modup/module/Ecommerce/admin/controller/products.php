<?php

set_time_limit(120);

if (!User::has_perm('view ecommerce products'))
{
    throw new Exception('You do not have access to this page');
}

Admin::set('title', 'Products');
Admin::set('header', 'Products');

$page = defined('URI_PART_4') ? URI_PART_4 : 1;

$user_filter = User::setting('ecommerce', 'product_filter');

$default_filter = array(
    'page' => $page,
    'statuses' => array(),
    'return_statuses' => array(),
    'name' => '',
    'sku' => '',
    'state' => '',
    'sort' => array(
        'type' => 'created_date',
        'order' => 'DESC',
    ),
);

$filter = is_null($user_filter)
    ? $default_filter
    : array_merge($default_filter, $user_filter);

if (ake('filter', $_REQUEST))
{
    $filter['statuses'] = deka(array(), $_REQUEST, 'filter', 'statuses');
    $filter['return_statuses'] = deka(array(), $_REQUEST, 'filter', 'statuses');
    $filter = array_merge($filter, $_REQUEST['filter']);
}
else
{
    $filter['start_date'] = date('Y-m-d', strtotime('-1 year'));
    $filter['end_date'] = date('Y-m-d');
    $filter['rows'] = 25;
}

User::update('setting', 'ecommerce', 'product_filter', $filter);
User::update('setting', 'ecommerce', 'product_filter', $filter);

if (ake('submit', $_REQUEST))
{
    unset($_REQUEST['submit']);
    if ($page > 1)
    {
        header('Location: /admin/module/Ecommerce/products/?'.http_build_query($_REQUEST));
        exit;
    }
}

$products_filter = $filter;
$products_filter['start_date'] = strtotime($filter['start_date']);
$products_filter['end_date'] = strtotime($filter['end_date']);

$columns = array(
    'name' => 'Product',
    'color' => 'Color',
    'size' => 'Size',
    'order_name' => 'Order',
    'customer_name' => 'Name',
    'customer_email' => 'Email',
    'ship_to' => 'State',
    'country' => 'Country',
    'city' => 'City',
    'zip' => 'Zipcode',
    'date' => 'Date',
    'how heard' => 'How Heard',
    'subtotal' => 'Subtotal',
    'discount' => 'Discount',
    'total' => 'Total',
);
$types = array(
    'name' => 'Product',
    'order_name' => 'Order',
    'customer_email' => 'Email',
    'state' => 'State',
    'created_date' => 'Date',
    'subtotal' => 'Subtotal',
    'discount' => 'Discount',
    'total' => 'Total',
);
$rows = array(25, 50, 100, 'All');

$type_filter = deka('created_date', $products_filter, 'sort', 'type');
if ($type_filter === 'created_date' || !ake($type_filter, $types))
{
    $products_filter['sort'] = array();
}

// turn into helper function?
$orig_products = EcommerceAPI::get_products_paginated($products_filter, $page, $filter['rows']);
$num_pages = is_numeric($filter['rows'])
    ? (int)floor($orig_products['total_items'] / $filter['rows'])
    : 1;
if (is_numeric($filter['rows']) && $orig_products['total_items'] % $filter['rows'] > 0)
{
    $num_pages++;
}
$states = Ecommerce::get_us_states();
$provinces = Ecommerce::get_ca_provinces();
$countries = Ecommerce::get_paypal_countries();
$products = array();
foreach ($orig_products['items'] as $product)
{
    $tmp['name'] = $product['name'];
    $tmp['color'] = '';
    $tmp['size'] = '';
    foreach ($product['Options'] as $option)
    {
        $tmp[$option['name']] = $option['data'];
    }
    $tmp['order_name'] = ake('export', $_REQUEST)
        ? $product['order_name']
        : '<a href="/admin/module/Ecommerce/view_order/'.$product['order_id'].'/">'.$product['order_name'].'</a>';
    $tmp['customer_name'] = $product['customer_name'];
    $tmp['customer_email'] = $product['customer_email'];
    $tmp['ship_to'] = $product['state'];
    $tmp['country'] = $countries[$product['country']];
    if (strlen($tmp['ship_to']))
    {
        if ($product['country'] === 'US')
        {
            $tmp['ship_to'] = $states[strtoupper($product['state'])];
        }
        if ($product['country'] === 'CA')
        {
            $tmp['ship_to'] = $provinces[strtoupper($product['state'])];
        }
    }
    $tmp['city'] = $product['city'];
    $tmp['zip'] = $product['zip'];
    $tmp['date'] = date('Y-m-d', $product['created_date']);
    $tmp['how heard'] = '';
    foreach ($product['Order']['Options'] as $option)
    {
        $tmp[$option['name']] = $option['data'];
    }
    $tmp['subtotal'] = '$'.number_format($product['subtotal'], 2);
    $tmp['discount'] = '$'.number_format($product['discount'] * -1, 2);
    $tmp['total'] = '$'.number_format($product['total'], 2);
    $products[] = $tmp;
}

if (ake('export', $_REQUEST))
{
    $filename = 'products-'.time().'.csv';
    header("Content-type: application/octet-stream");
    header('Content-Disposition: attachment; filename="'.$filename.'"');
    $data = export_csv($products, $columns, TRUE);
    echo $data; 
    exit;
}

?>
