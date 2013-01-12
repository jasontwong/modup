<?php

Admin::set('header', 'UPT Sales Report');
Admin::set('title', 'UPT Sales Report');

$orders = array();
$filters = array_fill_keys(
    array('start_date', 'end_date', 'state'),
    ''
);

if (ake('filters', $_POST))
{
    $data = $filters = array_merge($filters, $_POST['filters']);
    $data['start_date'] = strtotime($filters['start_date']);
    $data['end_date'] = strtotime($filters['end_date']);
    $data['sort']['type'] = 'eo.order_name';
    $data['sort']['order'] = 'ASC';
    $products = EcommerceAPI::get_products_paginated($data, 1, 'all');
    foreach ($products['items'] as $product)
    {
        if (strlen($product['Order']['returned_order_name']) 
            || strpos(strtolower($product['name']), 'gift card') !== FALSE)
            {
                continue;
            }
        if (!ake($product['Order']['order_name'], $orders))
        {
            $orders[$product['Order']['order_name']] = 0;
        }

        $orders[$product['Order']['order_name']] += $product['quantity'];
    }
}

if (ake('export', $_REQUEST))
{
    $filename = 'upt-sales-'.time().'.csv';
    header("Content-type: application/octet-stream");
    header('Content-Disposition: attachment; filename="'.$filename.'"');
    $data = export_csv($_SESSION['Aether']['upt']['export'], array(), TRUE);
    /*
    $data = '';
    foreach ($_SESSION['Aether']['upt']['export'] as $k => $line)
    {
        $data .= $k === 0
            ? implode(',', $line)
            : "\n".implode(',', $line);
    }
    */
    echo $data; 
    exit;
}

?>
