<?php

if (ake('code', $_REQUEST))
{
    $query = Doctrine_Query::create()
        ->select('eo.order_name, eo.id')
        ->from('EcommerceOrder eo')
        ->leftJoin('eo.Coupons ec')
        ->where('ec.code = ?')
        ->orderBy('eo.order_name');

    $results = $query->execute(array($_REQUEST['code']));
    $orders = $results->toArray(TRUE);
}
else
{
    header('Location: /admin/module/Ecommerce/coupons/');
    exit;
}

Admin::set('title', 'Coupon Lookup');
Admin::set('header', 'Coupon Lookup');
