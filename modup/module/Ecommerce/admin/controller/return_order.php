<?php

if (!User::has_perm('edit ecommerce orders'))
{
    throw new Exception('You do not have access to this page');
}

Admin::set('title', 'Create Return');
Admin::set('header', 'Create Return');

if (!defined('URI_PART_4'))
{
    throw new Exception("You're not supposed to be here");
}

$eot = Doctrine::getTable('EcommerceOrder');
$eo = $eot->find(URI_PART_4);

$prods = array();
$prods = array_merge($prods, Content::get_entries_details_by_type_name('Mens Product'));
$prods = array_merge($prods, Content::get_entries_details_by_type_name('Womens Product'));
$prods = array_merge($prods, Content::get_entries_details_by_type_name('Gear Product'));

$inventories = $pnames = array();
foreach ($prods as $k => $product)
{
    $pname = Aether::filter_language_data($product['data']['Display Name'], 'EN', 'data', 0);
    $pnames[$k] = $pname;
    $inventories[strtoupper($product['data']['SKU']['data'][0])] = $product['data']['Inventory']['data'][0];
}
array_multisort($pnames, SORT_ASC, $prods);


if (ake('return', $_POST))
{
    $products = $_POST['return']['products'];
    $new_products = array();
    foreach ($products as $product)
    {
        if ($product['quantity'] != 0)
        {
            if (ake(strtoupper($product['sku']), $inventories))
            {
                $item = array();
                $item['id'] = $inventories[$product['sku']];
                $inv = Inventory::get_product($item['id']);
                foreach ($product['Options'] as $option)
                {
                    switch ($option['name'])
                    {
                        case 'color':
                            foreach ($inv['options_y'] as $opty)
                            {
                                if ($opty['name'] == $option['data'] || $opty['display_name'] == $option['data'])
                                {
                                    $item['option_y'] = $opty['id'];
                                    break;
                                }
                            }
                        break;
                        case 'size':
                            foreach ($inv['options_x'] as $optx)
                            {
                                if ($optx['name'] == $option['data'] || $optx['display_name'] == $option['data'])
                                {
                                    $item['option_x'] = $optx['id'];
                                    break;
                                }
                            }
                        break;
                    }
                }
                if (ake('option_x', $item) && is_numeric($item['option_x'])
                    && ake('option_y', $item) && is_numeric($item['option_y'])
                    && is_numeric($item['id']))
                    {
                        Inventory::adjust_quantity($item['id'], $item['option_x'], $item['option_y'], $product['quantity']);
                    }
            }
            $new_products[] = $product;
        }
    }
    $products = $new_products;
    $order = $_POST['return']['order'];
    $data = $_POST['return']['data'];
    $neo = new EcommerceOrder;
    $neo->merge($order);
    $neo->customer_email = $eo->customer_email;
    $neo->shipping_label = $eo->shipping_label;
    $neo->shipping_carrier = $eo->shipping_carrier;
    $neo->pp_transaction_id = $eo->pp_transaction_id;
    $neo->order_name = Ecommerce::get_next_order_number();
    $neo->returned_order_name = $eo->order_name;
    $address = $eo->BillingAddress->toArray();
    unset($address['id']);
    $neo->BillingAddress->merge($address);
    $address = $eo->ShippingAddress->toArray();
    unset($address['id']);
    $neo->ShippingAddress->merge($address);
    $neo->Products->fromArray($products);
    if (is_numeric($data['order_status_id']))
    {
        $neo->link('Status', $data['order_status_id']);
    }
    if (is_numeric($data['return_status_id']))
    {
        $neo->link('ReturnStatus', $data['return_status_id']);
    }
    if ($neo->isValid())
    {
        $eo->free();
        $neo->order_name = Ecommerce::use_order_number();
        $neo->save();
        Admin::notify(Admin::TYPE_SUCCESS, 'Successfully created return');
        header('Location: /admin/module/Ecommerce/view_order/'.$neo->id.'/');
        exit;
    }
    else
    {
        $neo->free();
    }
}

/*
//{{{ layout
$layout = new Field();
$carriers = array_keys(Ecommerce::get_tracking_link());
$layout->add_layout(
    array(
        'field' => Field::layout(
            'dropdown',
            array(
                'data' => array(
                    'options' => array_combine($carriers, $carriers),
                ),
            )
        ),
        'name' => 'shipping_carrier',
        'type' => 'dropdown',
        'text' => 'Leave blank to use next order number',
        'value' => array(
            'data' => $eo->shipping_carrier,
        ),
    )
);
$layout->add_layout(
    array(
        'field' => Field::layout('text'),
        'name' => 'shipping_label',
        'type' => 'text',
        'value' => array(
            'data' => $eo->shipping_label,
        ),
    )
);
$layout->add_layout(
    array(
        'field' => Field::layout('text'),
        'name' => 'order_name',
        'type' => 'text',
        'value' => array(
        ),
    )
);

$layout->add_layout(
    array(
        'field' => Field::layout('ecommerce_address'),
        'name' => 'billing_address',
        'type' => 'ecommerce_address',
        'value' => array(
            'name' => $eo->BillingAddress->name,
            'address1' => $eo->BillingAddress->address1,
            'address2' => $eo->BillingAddress->address2,
            'country' => $eo->BillingAddress->country,
            'state' => $eo->BillingAddress->state,
            'city' => $eo->BillingAddress->city,
            'phone' => $eo->BillingAddress->phone,
            'zipcode' => $eo->BillingAddress->zipcode,
        ),
    )
);
$layout->add_layout(
    array(
        'field' => Field::layout('ecommerce_address'),
        'name' => 'shipping_address',
        'type' => 'ecommerce_address',
        'value' => array(
            'name' => $eo->ShippingAddress->name,
            'address1' => $eo->ShippingAddress->address1,
            'address2' => $eo->ShippingAddress->address2,
            'country' => $eo->ShippingAddress->country,
            'state' => $eo->ShippingAddress->state,
            'city' => $eo->ShippingAddress->city,
            'phone' => $eo->ShippingAddress->phone,
            'zipcode' => $eo->ShippingAddress->zipcode,
        ),
    )
);

$coupons = Ecommerce::get_available_coupons();
$options = array();
foreach ($coupons as $coupon)
{
    $options[$coupon['id']] = $coupon['code'];
}
$values = array();
foreach ($eo->Coupons as $coupon)
{
    $values[] = $coupon->id;
}

$layout->add_layout(
    array(
        'field' => Field::layout(
            'select_multiple',
            array(
                'data' => array(
                    'options' => $options,
                ),
            )
        ),
        'name' => 'coupons',
        'type' => 'dropdown',
        'value' => array(
            'data' => $values,
        ),
    )
);

$gift_cards = Ecommerce::get_available_gift_cards();
$options = array();
foreach ($gift_cards as $gift_card)
{
    $options[$gift_card['id']] = $gift_card['code'];
}
$values = array();
foreach ($eo->GiftCards as $gift_card)
{
    $values[] = $gift_card->id;
}

$layout->add_layout(
    array(
        'field' => Field::layout(
            'select_multiple',
            array(
                'data' => array(
                    'options' => $options,
                ),
            )
        ),
        'name' => 'gift_cards',
        'type' => 'dropdown',
        'value' => array(
            'data' => $values,
        ),
    )
);

$layout->add_layout(
    array(
        'field' => Field::layout(
            'dropdown',
            array(
                'data' => array(
                    'options' => Ecommerce::get_status_options(),
                ),
            )
        ),
        'name' => 'order_status_id',
        'type' => 'dropdown',
        'value' => array(
            'data' => $eo->order_status_id,
        ),
    )
);
$layout->add_layout(
    array(
        'field' => Field::layout('textarea'),
        'name' => 'user_comments',
        'type' => 'textarea',
        'value' => array(
            'data' => $eo->user_comments,
        ),
    )
);
$layout->add_layout(
    array(
        'field' => Field::layout('textarea'),
        'name' => 'admin_comments',
        'type' => 'textarea',
        'value' => array(
            'data' => $eo->admin_comments,
        ),
    )
);
$layout->add_layout(
    array(
        'field' => Field::layout('date'),
        'name' => 'shipped_date',
        'type' => 'text',
        'value' => array(
            'data' => $eo->shipped_date,
        ),
    )
);
$layout->add_layout(
    array(
        'field' => Field::layout('text'),
        'name' => 'customer_email',
        'type' => 'text',
        'value' => array(
            'data' => $eo->customer_email,
        ),
    )
);
$layout->add_layout(
    array(
        'field' => Field::layout('text'),
        'name' => 'pp_authorization_id',
        'type' => 'text',
        'value' => array(
            'data' => $eo->pp_authorization_id,
        ),
    )
);
$layout->add_layout(
    array(
        'field' => Field::layout('text'),
        'name' => 'pp_transaction_id',
        'type' => 'text',
        'value' => array(
            'data' => $eo->pp_transaction_id,
        ),
    )
);
$layout->add_layout(
    array(
        'field' => Field::layout('text'),
        'name' => 'tracking_number',
        'type' => 'text',
        'value' => array(
            'data' => $eo->tracking_number,
        ),
    )
);

$values = array();
foreach ($eo->Products as $k => $product)
{
    // $values['id'][$k][] = $product->id;
    $values['name'][$k][] = $product->name;
    $values['price'][$k][] = $product->price;
    $values['weight'][$k][] = $product->weight;
    $values['quantity'][$k][] = $product->quantity;
    $values['discount'][$k][] = $product->discount;
    $values['tax'][$k][] = $product->tax;
    $values['shipping'][$k][] = $product->shipping;
    $values['total'][$k][] = $product->total;
}

$layout->add_layout(
    array(
        'field' => Field::layout('ecommerce_product'),
        'name' => '15',
        'array' => TRUE,
        'type' => 'ecommerce_product',
        'value' => $values,
    )
);

// cost
$layout->add_layout(
    array(
        'field' => Field::layout('text'),
        'name' => 'weight',
        'type' => 'text',
        'value' => array(
            'data' => $eo->weight,
        ),
    )
);
$layout->add_layout(
    array(
        'field' => Field::layout(
            'text',
            array(
                'data' => array(
                    'attr' => array(
                        'class' => 'text EcommerceOrderShipping',
                        'type' => 'text',
                    ),
                ),
            )
        ),
        'name' => 'shipping',
        'type' => 'text',
        'value' => array(
            'data' => $eo->shipping,
        ),
    )
);
$layout->add_layout(
    array(
        'field' => Field::layout(
            'text',
            array(
                'data' => array(
                    'attr' => array(
                        'class' => 'text EcommerceOrderDiscount',
                        'type' => 'text',
                    ),
                ),
            )
        ),
        'name' => 'discount',
        'type' => 'text',
        'value' => array(
            'data' => $eo->discount,
        ),
    )
);
$layout->add_layout(
    array(
        'field' => Field::layout(
            'text',
            array(
                'data' => array(
                    'attr' => array(
                        'class' => 'text EcommerceOrderTax',
                        'type' => 'text',
                    ),
                ),
            )
        ),
        'name' => 'tax',
        'type' => 'text',
        'value' => array(
            'data' => $eo->tax,
        ),
    )
);
$layout->add_layout(
    array(
        'field' => Field::layout(
            'text',
            array(
                'data' => array(
                    'attr' => array(
                        'class' => 'text EcommerceOrderSubtotal',
                        'type' => 'text',
                    ),
                ),
            )
        ),
        'name' => 'subtotal',
        'type' => 'text',
        'value' => array(
            'data' => $eo->subtotal,
        ),
    )
);
$layout->add_layout(
    array(
        'field' => Field::layout(
            'text',
            array(
                'data' => array(
                    'attr' => array(
                        'class' => 'text EcommerceOrderGiftCardDiscount',
                        'type' => 'text',
                    ),
                ),
            )
        ),
        'name' => 'gift_card_discount',
        'type' => 'text',
        'value' => array(
            'data' => $eo->discount,
        ),
    )
);
$layout->add_layout(
    array(
        'field' => Field::layout(
            'text',
            array(
                'data' => array(
                    'attr' => array(
                        'class' => 'text EcommerceOrderTotal',
                        'type' => 'text',
                    ),
                ),
            )
        ),
        'name' => 'total',
        'type' => 'text',
        'value' => array(
            'data' => $eo->total,
        ),
    )
);

$layout->add_layout(
    array(
        'field' => Field::layout('submit_reset'),
        'name' => 'submit',
        'type' => 'submit_reset',
    )
);

$rows = array();
$values = array();
foreach ($eo->Options as $option)
{
    // $values[$option->name.'_id'] = $option->id;
    $values[$option->name] = $option->data;
}
foreach (Ecommerce::get_order_keys() as $key)
{
    if (!strlen($key) || !ake($key, $values)) continue;
    $layout->add_layout(
        array(
            'field' => Field::layout('text'),
            'name' => '_'.$key,
            'type' => 'text',
            'value' => array(
                'data' => $values[$key],
            ),
        )
    );
    $row['fields'] = $layout->get_layout('_'.$key);
    $row['label']['text'] = ucwords($key);
    $rows[] = $row;

    $row = array();
    $row['row']['attr']['class'] = 'row_hidden';
    $row['fields'] = $layout->get_layout('_'.$key.'_id');
    $rows[] = $row;
    unset($row);
}

//}}}
// {{{ form submission
if (isset($_POST['form']))
{
    $order = $layout->acts('post', $_POST['order']);
    $data = $layout->acts('post', $_POST['data']);
    $products = $layout->acts('post', $_POST['products']);
    $options = $layout->acts('post', $_POST['options']);
    foreach ($options as $k => $v)
    {
        if (strpos($k, '_') === 0)
        {
            if (strpos($k, '_id') !== FALSE && strlen($k) > 3)
            {
                if (strlen($v))
                {
                    $order['Options'][str_replace('_id', '', substr($k, 1))]['id'] = $v;
                }
            }
            else
            {
                $order['Options'][substr($k, 1)]['name'] = substr($k, 1);
                $order['Options'][substr($k, 1)]['data'] = $v;
            }
        }
    }

    if (ake('Options', $order))
    {
        sort($order['Options']);
    }

    $neo = new EcommerceOrder();
    $neo->merge($order);
    $neo->BillingAddress->merge($data['billing_address']);
    $neo->ShippingAddress->merge($data['shipping_address']);
    $neo->Products->fromArray($products[15]);
    $neo->returned_order_name = $eo->order_name;

    if (is_numeric($data['order_status_id']))
    {
        $neo->link('Status', $data['order_status_id']);
    }
    if (count($data['coupons']))
    {
        $neo->link('Coupons', $data['coupons']);
    }
    if (count($data['gift_cards']))
    {
        $neo->link('GiftCards', $data['gift_cards']);
    }

    if (strlen($neo->order_name) < 1)
    {
        $neo->order_name = Ecommerce::use_order_number();
    }

    if ($neo->isValid())
    {
        $neo->save();
        Admin::notify(Admin::TYPE_SUCCESS, 'Successfully saved');
        header('Location: /admin/module/Ecommerce/view_order/' . $neo->id . '/');
        exit;
    }
    else
    {
        var_dump($neo->getErrorStack()->toArray());
        Admin::notify(Admin::TYPE_ERROR, 'Unsuccessful Save');
    }
}
// }}}
//{{{ form build
$eform = new FormBuilderRows;
$eform->attr = array(
    'action' => URI_PATH,
    'enctype' => 'multipart/form-data',
    'method' => 'post'
);

$eform->add_group(
    array(
        'rows' => array(
            array(
                'fields' => $layout->get_layout('order_name'),
                'label' => array(
                    'text' => 'Order Name'
                )
            ),
            array(
                'fields' => $layout->get_layout('customer_email'),
                'label' => array(
                    'text' => 'Customer Email'
                )
            ),
        )
    ),
    'order'
);
$eform->add_group(
    array(
        'rows' => array(
            array(
                'fields' => $layout->get_layout('billing_address'),
                'label' => array(
                    'text' => 'Billing Address'
                )
            ),
            array(
                'fields' => $layout->get_layout('shipping_address'),
                'label' => array(
                    'text' => 'Shipping Address'
                )
            ),
        )
    ),
    'data'
);
$eform->add_group(
    array(
        'rows' => array(
            array(
                'row' => array(
                    'attr' => array(
                        'class' => 'content_multiple'
                    )
                ),
                'fields' => $layout->get_layout('15'),
                'label' => array(
                    'text' => 'Product'
                )
            ),
        )
    ),
    'products'
);
$eform->add_group(
    array(
        'rows' => array(
            array(
                'fields' => $layout->get_layout('coupons'),
                'label' => array(
                    'text' => 'Coupons'
                )
            ),
            array(
                'fields' => $layout->get_layout('gift_cards'),
                'label' => array(
                    'text' => 'Gift Cards'
                )
            ),
        ),
    ),
    'data'
);
$eform->add_group(
    array(
        'rows' => $rows,
    ),
    'options'
);
$rows = array(
    array(
        'fields' => $layout->get_layout('user_comments'),
        'label' => array(
            'text' => 'User Comments'
        )
    ),
);

$eform->add_group(
    array(
        'rows' => $rows,
    ),
    'order'
);
$eform->add_group(
    array(
        'rows' => array(
            array(
                'fields' => $layout->get_layout('weight'),
                'label' => array(
                    'text' => 'Weight (lbs)'
                )
            ),
            array(
                'fields' => $layout->get_layout('subtotal'),
                'label' => array(
                    'text' => 'Subtotal'
                )
            ),
            array(
                'fields' => $layout->get_layout('discount'),
                'label' => array(
                    'text' => 'Discount'
                )
            ),
            array(
                'fields' => $layout->get_layout('gift_card_discount'),
                'label' => array(
                    'text' => 'Gift Card Discount'
                )
            ),
            array(
                'fields' => $layout->get_layout('tax'),
                'label' => array(
                    'text' => 'Tax'
                )
            ),
            array(
                'fields' => $layout->get_layout('shipping'),
                'label' => array(
                    'text' => 'Shipping'
                )
            ),
            array(
                'fields' => $layout->get_layout('total'),
                'label' => array(
                    'text' => 'Total'
                )
            ),
        )
    ),
    'order'
);


if (Ecommerce::is_using_paypal())
{
    $eform->add_group(
        array(
            'rows' => array(
                array(
                    'fields' => $layout->get_layout('pp_authorization_id'),
                    'label' => array(
                        'text' => 'PayPal Authorization ID'
                    )
                ),
                array(
                    'fields' => $layout->get_layout('pp_transaction_id'),
                    'label' => array(
                        'text' => 'PayPal Transaction ID'
                    )
                ),
            ),
        ),
        'order'
    );
}

$eform->add_group(
    array(
        'rows' => array(
            array(
                'fields' => $layout->get_layout('admin_comments'),
                'label' => array(
                    'text' => 'Admin Comments'
                )
            ),
            array(
                'fields' => $layout->get_layout('tracking_number'),
                'label' => array(
                    'text' => 'Tracking Number'
                )
            ),
            array(
                'fields' => $layout->get_layout('shipping_label'),
                'label' => array(
                    'text' => 'Shipping Type'
                )
            ),
            array(
                'fields' => $layout->get_layout('shipping_carrier'),
                'label' => array(
                    'text' => 'Carrier Type'
                )
            ),
            array(
                'fields' => $layout->get_layout('shipped_date'),
                'label' => array(
                    'text' => 'Shipped Date'
                )
            ),
        ),
    ),
    'order'
);

$eform->add_group(
    array(
        'rows' => array(
            array(
                'fields' => $layout->get_layout('order_status_id'),
                'label' => array(
                    'text' => 'Order Status'
                )
            ),
        ),
    ),
    'data'
);

$eform->add_group(
    array(
        'rows' => array(
            array(
                'fields' => $layout->get_layout('submit'),
            ),
        )
    ),
    'form'
);

$efh = $eform->build();
// }}}
*/

?>
