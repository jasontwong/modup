<?php

Admin::set('title', 'Product Notification Registrations');
Admin::set('header', 'Product Notification Registrations');

$notes = Aether::get_notifications();
$option_groups = Inventory::get_option_groups();

$options = array();
foreach ($option_groups as &$group)
{
    $group_options = Inventory::get_options($group['id']);
    $options_rows = array();
    foreach ($group_options as $group_option)
    {
        $id = (int)$group_option['id'];
        $options_rows[$id] = $group_option;
    }
    $options[$group['name']] = $options_rows;
}

$names = $colors = $sizes = array();

foreach ($notes as $k => &$note)
{
    $product = Content::get_entry_details_by_id($note['product_id']);
    $note['product'] = array(
        'name' => $product['entry']['title']
    );
    $note['color'] = deka(NULL, $options, 'Colors', $note['color_id']);
    $note['size'] = deka(NULL, $options, 'Sizes', $note['size_id']);
    $names[$k] = $note['product']['name'];
    $colors[$k] = $note['color']['name'];
    $sizes[$k] = $note['size']['name'];
}

array_multisort($names, $colors, $sizes, $notes);

?>
