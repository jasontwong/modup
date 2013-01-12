<?php

Admin::set('title', 'Delete Product Notification Registration');
Admin::set('header', 'Delete Product Notification Registration');


if ($_POST)
{
    Aether::delete_notification_by_id($_POST['id']);
    header('Location: /admin/module/Aether/notifications/');
    exit;
}
else
{
    $note = Aether::get_notification_by_id(URI_PART_4);
}

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

$product = Content::get_entry_details_by_id($note['product_id']);
$note['product'] = array(
    'name' => $product['entry']['title']
);
$note['color'] = deka(NULL, $options, 'Colors', $note['color_id']);
$note['size'] = deka(NULL, $options, 'Sizes', $note['size_id']);

?>
