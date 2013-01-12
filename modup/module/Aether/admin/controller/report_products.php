<?php

Admin::set('header', 'Product Sales Report');
Admin::set('title', 'Product Sales Report');

$items = array();
$filters = array_fill_keys(
    array('start_date', 'end_date', 'state'),
    ''
);
$filters['type'] = 'rands';
$filters['product_type'] = array();
$checkboxes = array(
    'Accessories' => 'Accessories',
    'Gear' => 'Gear',
    'Mens' => 'Mens',
    'Womens' => 'Womens',
);
$radios = array(
    'rands' => 'Returns and Sales',
    'r' => 'Returns Only',
    's' => 'Sales Only',
);

if (ake('filters', $_REQUEST))
{
    $data = $filters = array_merge($filters, $_REQUEST['filters']);
    $data['start_date'] = strtotime($filters['start_date']);
    $data['end_date'] = strtotime($filters['end_date']);
    $data['sort']['type'] = 'order_name';
    $data['sort']['order'] = 'ASC';
    $products = EcommerceAPI::get_products_paginated($data, 1, 'all');
    $products = $products['items'];
    $color_totals = $totals = array();
    foreach ($products as $product)
    {
        if ($filters['type'] === 'r')
        {
            // if (!strlen($product['Order']['returned_order_name']))
            if ($product['quantity'] >= 0)
            {
                continue;
            }
        }
        if ($filters['type'] === 's')
        {
            // if (strlen($product['Order']['returned_order_name']))
            if ($product['quantity'] <= 0)
            {
                continue;
            }
        }
        $options = array();
        foreach ($product['Options'] as $option)
        {
            $options[$option['name']] = $option['data'];
        }
        if (ake('color', $options) && ake('size', $options))
        {
            $items[$product['name']][$product['sku']][(string)abs($product['price'])][$options['color']][$options['size']] = deka(0, $items, $product['name'], $product['sku'], (string)abs($product['price']), $options['color'], $options['size']) + $product['quantity'];
            $totals[$product['name']][$product['sku']][(string)abs($product['price'])] = deka(0, $totals, $product['name'], $product['sku'], (string)abs($product['price'])) + $product['quantity'];
            $color_totals[$product['name']][$product['sku']][(string)abs($product['price'])][$options['color']] = deka(0, $color_totals, $product['name'], $product['sku'], (string)abs($product['price']), $options['color']) + $product['quantity'];
        }
    }
}

if (ake('export', $_REQUEST))
{
    $type = deka('', $_REQUEST, 'type');
    switch ($type)
    {
        case 'pdf':
            require_once DIR_LIB.'/fpdf/fpdf.php';
            $filename = 'product-sales-'.time().'.pdf';
            $cols = 3;
            $col_width = array(
                130, 30, 30
            );
            $col_total_width = 190;
            $col_height = 10;
            $pdf = new FPDF();
            $pdf->AddPage();
            foreach ($_SESSION['Aether']['products']['export'] as &$row)
            {
                if (strlen($row[0]))
                {
                    if (strlen($row[1]) && strlen($row[2]))
                    {
                        // color, size, qty
                        $pdf->SetFont('Arial', '', 12);
                        $left = TRUE;
                        foreach ($row as $k => &$col)
                        {
                            $pdf->Cell($col_width[$k], $col_height, $col, 'B', 0, $left ? 'L' : 'R');
                            $left = FALSE;
                        }
                    }
                    else
                    {
                        // header or product
                        $is_product = is_numeric(strpos($row[0], ':'));
                        $pdf->SetFont('Arial', 'B', 12);
                        $pdf->SetFillColor(200, 200, 200);
                        $pdf->Cell($col_total_width, $col_height, $row[0], 0, 0, 'L', !$is_product);
                    }
                }
                else
                {
                    // sizes and qty numbers
                    $pdf->SetFont('Arial', '', 12);
                    $left = TRUE;
                    foreach ($row as $k => &$col)
                    {
                        $pdf->Cell($col_width[$k], $col_height - 3, $col, 0, 0, $left ? 'L' : 'R');
                        $left = FALSE;
                    }
                }
                $pdf->Ln();
            }
            // header("Content-type: application/pdf");
            // header('Content-Disposition: attachment; filename="'.$filename.'"');
            $pdf->Output($filename, 'I');
            exit;
        break;
        default:
            $filename = 'product-sales-'.time().'.csv';
            header("Content-type: application/octet-stream");
            header('Content-Disposition: attachment; filename="'.$filename.'"');
            export_csv($_SESSION['Aether']['products']['export']);
            exit;
    }
    /*
    $filename = 'product-sales-'.time().'.csv';
    header("Content-type: application/octet-stream");
    header('Content-Disposition: attachment; filename="'.$filename.'"');
    export_csv($_SESSION['Aether']['products']['export']);
    exit;
    */
}

?>
