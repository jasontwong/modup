<?php

set_time_limit(0);
$products = Doctrine_Query::create()
    ->from('EcommerceProduct ep')
    ->where('ep.id NOT IN (SELECT nep.id FROM EcommerceProduct nep WHERE nep.Options.name = "type")')
    ->orderBy('ep.id ASC')
    ->execute();

foreach ($products as &$product)
{
    $type = Aether::get_product_type($product->sku);
    if ($type)
    {
        $product->Options;
        $data = $product->toArray(TRUE);
        $has_type = FALSE;
        foreach ($data['Options'] as &$option)
        {
            if ($option['name'] === 'type')
            {
                $has_type = TRUE;
                $option['data'] = $type;
                break;
            }
        }
        if (!$has_type)
        {
            $data['Options'][] = array(
                'name' => 'type',
                'data' => $type,
            );
        }
        $product->Options->synchronizeWithArray($data['Options']);
        if ($product->Options->isModified())
        {
            $product->Options->save();
        }
    }
    $product->free();
}
