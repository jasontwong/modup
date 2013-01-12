<?php

Admin::set('title', 'Gift Card Purchases');
Admin::set('header', 'Gift Card Purchases');

$spec = array(
    'select' => array(
        'gc.*',
    ),
    'from' => 'AetherGiftCard gc',
    'orderBy' => 'gc.created_date DESC',
);

$gift_cards = dql_exec($spec);

if (ake('delete', $_REQUEST))
{
    $gct = Doctrine::getTable('AetherGiftCard');
    $gc = $gct->find($_REQUEST['id']);
    $gc->delete();
    $gc->free();
    header("Location: ".URI_PATH);
    exit;
}

?>
