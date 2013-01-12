<?php

Admin::set('title', 'Send Product Notification Emails');
Admin::set('header', 'Send Product Notification Emails');


$note = Aether::get_notification_by_id(URI_PART_4);

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

if ($_POST)
{
    $type = Content::get_entry_type_by_entry_id($product['entry']['id']);
    $permalink = '/shop';
    switch ($type['name'])
    {
        case 'Mens Product':
            $permalink .= '/mens';
            $cat_type = 'Mens Category';
        break;
        case 'Womens Product':
            $permalink .= '/womens';
            $cat_type = 'Womens Category';
        break;
        case 'Gear Product':
            $permalink .= '/gear';
            $cat_type = 'Gear Category';
        break;
    }
    $sitemap = Cache::get($cat_type, 'sitemap');
    $found = FALSE;
    foreach ($sitemap as $sub_cat => $sub_cat_ids)
    {
        if (!$found && in_array($product['entry']['id'], $sub_cat_ids))
        {
            $found = TRUE;
            $permalink .= '/'.$sub_cat;
        }
    }
    $permalink .= '/'.$product['entry']['slug'].'/';
    $item_name = Aether::filter_language_data($product['data']['Display Name'], 'EN', 'data', 0);
    $subject = $item_name.' back in stock';
    $registrants = $note['emails'];
    if (!eka($_SERVER, 'KRATEDEV'))
    {
        $registrants[] = 'customerservice@aetherapparel.com';
    }

    ob_start();
    include DIR_TMPL.'/email/back-in-stock.php';
    $content = ob_get_clean();

    $mailer = new Mailer(Ecommerce::get_email_accounts('customerservice'));
    $mailer->setSubject($subject)
        ->setFrom(array('customerservice@aetherapparel.com' => 'Aether Apparel'))
        ->setBody($content, 'text/html')
        ->setBcc($registrants);
    $mailer->send();
    Aether::delete_notification_by_id(URI_PART_4);
    header('Location: /admin/module/Aether/notifications/');
    exit();
}

?>
