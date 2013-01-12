<?php

if (!User::has_perm('view ecommerce orders'))
{
    throw new Exception('You do not have access to this page');
}

$user_filter = User::setting('ecommerce', 'order_filter');

$default_filter = array(
    'page' => '1',
    'start_date' => date('Y-m-d', strtotime('-1 year')),
    'end_date' => date('Y-m-d'),
    'statuses' => array(),
    'state' => '',
    'sort' => array(
        'type' => 'created_date',
        'order' => 'DESC',
    ),
);

if (ake('filter', $_REQUEST))
{
    $filter = array_merge($filter, $_GET['filter']);
}

$filter = is_null($user_filter)
    ? $default_filter
    : array_merge($default_filter, $user_filter);
$orders_filter = $filter;
$orders_filter['start_date'] = strtotime($filter['start_date']);
$orders_filter['end_date'] = strtotime($filter['end_date']);

$columns = array(
    'order_name' => 'Order',
    'customer_name' => 'Name',
    'customer_email' => 'Email',
    'ship_to' => 'State',
    'date' => 'Date',
    'how_heard' => 'How Heard',
    'subtotal' => 'Subtotal',
    'tax' => 'Tax',
    'shipping' => 'Shipping',
    'weight' => 'Weight',
    'total' => 'Total',
    'status' => 'Status',
    'admin_comments' => 'Admin Comments',
);

$type_filter = deka('created_date', $orders_filter, 'sort', 'type');
if ($type_filter === 'created_date' || !ake($type_filter, $types))
{
    $orders_filter['sort'] = array();
}

// turn into helper function?
$orig_orders = EcommerceAPI::get_orders_paginated($orders_filter, $filter['page'], $filter['rows']);
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
    foreach ($order['Options'] as $option)
    {
        if ($option['name'] == 'how heard')
        {
            $tmp['how_heard'] = $option['data'];
        }
    }
    $tmp['order_name'] = $order['order_name'];
    $tmp['customer_name'] = $order['name'];
    $tmp['customer_email'] = $order['customer_email'];
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
    $tmp['city'] = $order['city'];
    $tmp['zip'] = $order['zip'];
    $tmp['date'] = date('Y-m-d', $order['created_date']);
    $tmp['subtotal'] = '$'.number_format($order['subtotal'], 2);
    $tmp['tax'] = '$'.number_format($order['tax'], 2);
    $tmp['shipping'] = '$'.number_format($order['shipping'], 2);
    $tmp['weight'] = $order['weight'];
    $tmp['total'] = '$'.number_format($order['total'], 2);
    $tmp['admin_comments'] = $order['admin_comments'];
    $tmp['status'] = $order['status'];
    $orders[] = $tmp;
}

$filename = 'orders-'.time().'.csv';
header("Content-type: application/octet-stream");
header('Content-Disposition: attachment; filename="'.$filename.'"');
$data = export_csv($orders, $columns, TRUE);
/*
$data .= implode(',', $columns);
foreach ($orders as $order)
{
    $data .= "\n".implode(',', $order);
}
*/
echo $data; 

?>
