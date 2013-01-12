<?php

Admin::set('header', 'Inventory Report');
Admin::set('title', 'Inventory Report');

if (ake('product', $_POST))
{
    $product = $_POST['product'];
    if (is_numeric($product['id']) 
        && is_numeric($product['size_id']) 
        && is_numeric($product['color_id']) 
        && is_numeric($product['qty']))
        {
            Inventory::add_quantity($product['id'], $product['size_id'], $product['color_id'], $product['qty']);
            Admin::notify(Admin::TYPE_SUCCESS, 'Successfully modified quantity');
        }
        else
        {
            Admin::notify(Admin::TYPE_ERROR, 'There was a problem modifying quantity');
        }
}

// $quantities = Inventory::get_all_quantities();
$mens = Content::get_entries_details_by_type_name('Mens Product');
$womens = Content::get_entries_details_by_type_name('Womens Product');
$gear = Content::get_entries_details_by_type_name('Gear Product');
$products = array_merge($mens, $womens, $gear);
$categories = $names = $skus = $prices = $colors = $sizes = $qtys = $items = array();
$sitemap = array(
    'mens' => Cache::get('Mens Category', 'sitemap'),
    'womens' => Cache::get('Womens Category', 'sitemap'),
    'gear' => Cache::get('Gear Category', 'sitemap'),
);

foreach ($products as &$product)
{
    $item = array();
    foreach ($sitemap as $topic)
    {
        foreach ($topic as $cat => $ids)
        {
            if (in_array($product['entry']['id'], $ids))
            {
                $item['category'] = strtoupper($cat);
                break;
            }
        }
        if (ake('category', $item))
        {
            break;
        }
    }
    if (!ake('category', $item))
    {
        $item['category'] = 'UNCATEGORIZED';
    }
    $item['name'] = Aether::filter_language_data($product['data']['Display Name'], 'EN', 'data', 0);
    $item['cost'] = deka(0, $product, 'data', 'Cost', 'data', 0);
    $item['sku'] = $product['data']['SKU']['data'][0];
    $item['price'] = $product['data']['Price']['data'][0];
    $inventory = deka('', $product, 'data', 'Inventory', 'data', 0);
    if (!is_numeric($inventory))
    {
        continue;
    }
    $inv = Inventory::get_product($inventory);
    if (!ake('inventory', $inv))
    {
        continue;
    }
    foreach ($inv['inventory'] as $y => $inv_x)
    {
        foreach ($inv_x as $x => $qty)
        {
            $item['color'] = $inv['options_y'][$y]['display_name'];
            $item['size'] = $inv['options_x'][$x]['display_name'];
            $item['qty'] = $qty;
            $item['id'] = $inventory;
            $item['color_id'] = $inv['options_y'][$y]['id'];
            $item['size_id'] = $inv['options_x'][$x]['id'];
            $names[] = $item['name'];
            $skus[] = $item['sku'];
            $prices[] = $item['price'];
            $colors[] = $item['color'];
            $sizes[] = $item['size'];
            $qtys[] = $item['qty'];
            $categories[] = $item['category'];
            $items[] = $item;
        }
    }
}

array_multisort(
    $categories, SORT_ASC, 
    $names, SORT_ASC, 
    $skus, SORT_ASC, 
    $colors, SORT_ASC, 
    $sizes, SORT_ASC, SORT_NUMERIC, 
    $prices, SORT_ASC, SORT_NUMERIC,
    $qtys, SORT_ASC, SORT_NUMERIC,
    $items
);

if (ake('export', $_REQUEST))
{
    $filename = 'inventory-'.time().'.csv';
    header("Content-type: application/octet-stream");
    header('Content-Disposition: attachment; filename="'.$filename.'"');
    $data = export_csv($_SESSION['Aether']['inventory']['export'], array(), TRUE);
    echo $data; 
    exit;
}

?>
