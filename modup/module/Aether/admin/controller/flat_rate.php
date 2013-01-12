<?php

Admin::set('title', 'Handling Charges');
Admin::set('header', 'Handling Charges');

$data = Aether::get_flat_rates();

if (ake('rates', $_POST) && ake('skus', $_POST))
{
    $rates = array();

    foreach ($_POST['rates'] as $k => $v)
    {
        $sku = $_POST['skus'][$k];
        if (strlen($v) && strlen($sku))
        {
            $rates[$sku] = $v;
        }
    }
    
    Data::update('Aether', 'flat_rates', $rates);
    Data::save();

    $data = &$rates;
}

?>
