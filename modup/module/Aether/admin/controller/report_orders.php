<?php

if (!User::has_perm('view ecommerce orders'))
{
    throw new Exception('You do not have access to this page');
}

Admin::set('title', 'Orders Report');
Admin::set('header', 'Orders Report');

$page = defined('URI_PART_4') ? URI_PART_4 : 1;

$user_filter = NULL; // User::setting('ecommerce', 'order_filter');

$default_filter = array(
    'page' => $page,
    'statuses' => array(),
    'return_statuses' => array(),
    'state' => '',
    'name' => '',
    'email' => '',
    'order' => '',
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
    $filter['return_statuses'] = deka(array(), $_REQUEST, 'filter', 'return_statuses');
    $filter = array_merge($filter, $_REQUEST['filter']);
}
else
{
    $filter['start_date'] = date('Y-m-d', strtotime('-1 year'));
    $filter['end_date'] = date('Y-m-d');
    $filter['rows'] = 25;
}

if (ake('submit', $_REQUEST))
{
    unset($_REQUEST['submit']);
    if ($page > 1)
    {
        header('Location: /admin/module/Ecommerce/orders/?'.http_build_query($_REQUEST));
        exit;
    }
}

$orders_filter = $filter;
$orders_filter['start_date'] = strtotime($filter['start_date']);
$orders_filter['end_date'] = strtotime($filter['end_date']);

$columns = array(
    'order_name' => 'Order',
    'customer_name' => 'Name',
    'customer_email' => 'Email',
    'address1' => 'Address',
    'city' => 'City',
    'ship_to' => 'State',
    'country' => 'Country',
    'zip' => 'Zipcode',
    'date' => 'Date',
    'how_heard' => 'How Heard',
    'newsletter' => 'Newsletter?',
    'payment' => 'Payment',
    'subtotal' => 'Subtotal',
    'discount' => 'Discount',
    'tax' => 'Tax',
    'shipping' => 'Shipping',
    'weight' => 'Weight',
    'total' => 'Total',
    'status' => 'Status',
    'return_status' => 'Return Status',
    'admin_comments' => 'Admin Comments',
    'edit' => '',
);
$types = array(
    'order_name' => 'Order',
    'customer_email' => 'Email',
    'state' => 'State',
    'created_date' => 'Date',
    'subtotal' => 'Subtotal',
    'discount' => 'Discount',
    'tax' => 'Tax',
    'shipping' => 'Shipping',
    'weight' => 'Weight',
    'total' => 'Total',
    'order_status_id' => 'Status',
    'return_status_id' => 'Return Status',
);
$rows = array(25, 50, 100, 'All');

$type_filter = deka('created_date', $orders_filter, 'sort', 'type');
if ($type_filter === 'created_date' || !ake($type_filter, $types))
{
    $orders_filter['sort'] = array();
}

// turn into helper function?
$orig_orders = EcommerceAPI::get_orders_paginated($orders_filter, $page, $filter['rows']);
$num_pages = is_numeric($filter['rows'])
    ? (int)floor($orig_orders['total_items'] / $filter['rows'])
    : 1;
if (is_numeric($filter['rows']) && $orig_orders['total_items'] % $filter['rows'] > 0)
{
    $num_pages++;
}
$states = Ecommerce::get_us_states();
$provinces = Ecommerce::get_ca_provinces();
$orders = array();
foreach ($orig_orders['items'] as $order)
{
    $tmp['order_name'] = ake('export', $_REQUEST)
        ? $order['order_name']
        : '<a href="/admin/module/Ecommerce/view_order/'.$order['id'].'/">'.$order['order_name'].'</a>';
    $tmp['customer_name'] = $order['name'];
    $tmp['customer_email'] = $order['customer_email'];
    $tmp['address1'] = $order['address1'];
    $tmp['city'] = $order['city'];
    $tmp['ship_to'] = $order['state'];
    if (strlen($tmp['ship_to']))
    {
        if ($order['country'] === 'US')
        {
            $tmp['ship_to'] = $states[strtoupper($order['state'])];
        }
        if ($order['country'] === 'CA')
        {
            $tmp['ship_to'] = $provinces[$order['state']];
        }
    }
    $tmp['country'] = $order['country'];
    $tmp['zip'] = $order['zip'];
    $tmp['date'] = date('Y-m-d', $order['created_date']);
    $tmp['how_heard'] = '';
    $tmp['newsletter'] = '';
    $tmp['payment'] = '';
    foreach ($order['Options'] as $option)
    {
        switch ($option['name'])
        {
            case 'payment':
                $tmp['payment'] = $option['data'];
            break;
            case 'newsletter':
                $tmp['newsletter'] = $option['data'] ? 'Yes' : 'No';
            break;
            case 'how heard':
                $tmp['how_heard'] = $option['data'];
            break;
        }
    }
    $tmp['subtotal'] = '$'.number_format($order['subtotal'], 2);
    $tmp['discount'] = '$'.number_format($order['discount'], 2);
    $tmp['tax'] = '$'.number_format($order['tax'], 2);
    $tmp['shipping'] = '$'.number_format($order['shipping'], 2);
    $tmp['weight'] = $order['weight'];
    $tmp['total'] = '$'.number_format($order['total'], 2);
    $tmp['status'] = $order['status'];
    $tmp['return_status'] = $order['return_status'];
    $tmp['admin_comments'] = $order['admin_comments'];
    if (!ake('export', $_REQUEST))
    {
        $tmp['edit'] = '<a href="/admin/module/Ecommerce/edit_order/'.$order['id'].'/">[edit]</a>';
    }
    $orders[] = $tmp;
}

if (ake('export', $_REQUEST))
{
    unset($columns['edit']);
    $filename = 'orders-'.time().'.csv';
    header("Content-type: application/octet-stream");
    header('Content-Disposition: attachment; filename="'.$filename.'"');
    export_csv($orders, $columns);
    exit;
}

?>
