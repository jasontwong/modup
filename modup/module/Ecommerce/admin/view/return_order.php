<?php // echo $efh; ?>
<?php $tax_rate = 0.0875; ?>
<form action="<?php echo URI_PATH; ?>" method="post" />

<div id="ecommerce_view_order">
    <div class="info">
        <div class="data cleared">
            <div class="col1">
                <p>Order Name: <?php echo $eo->order_name; ?></p>
                <p>Order Date: <?php echo date('Y-m-d', $eo->created_date); ?></p>
                <p>Status: <?php echo $eo->Status->name; ?></p>
                <p>Customer Email: <a href="mailto:<?php echo $eo->customer_email; ?>"><?php echo $eo->customer_email; ?></a></p>
            </div>
        </div>
        <div class="address cleared">
            <div class="col1">
                <h2>Billing Address</h2>
                <ul>
                    <li><?php echo $eo->BillingAddress->name; ?></li>
                    <li><?php echo $eo->BillingAddress->address1; ?></li>
                    <li><?php echo $eo->BillingAddress->address2; ?></li>
                    <li><?php echo $eo->BillingAddress->city; ?></li>
                    <li><?php echo $eo->BillingAddress->state; ?></li>
                    <li><?php echo $eo->BillingAddress->country; ?></li>
                    <li><?php echo $eo->BillingAddress->zipcode; ?></li>
                    <li><?php echo $eo->BillingAddress->phone; ?></li>
                </ul>
            </div>
            <div class="col2">
                <h2>Shipping Address</h2>
                <ul>
                    <li><?php echo $eo->ShippingAddress->name; ?></li>
                    <li><?php echo $eo->ShippingAddress->address1; ?></li>
                    <li><?php echo $eo->ShippingAddress->address2; ?></li>
                    <li><?php echo $eo->ShippingAddress->city; ?></li>
                    <li class="order-state"><?php echo $eo->ShippingAddress->state; ?></li>
                    <li class="order-country"><?php echo $eo->ShippingAddress->country; ?></li>
                    <li><?php echo $eo->ShippingAddress->zipcode; ?></li>
                    <li><?php echo $eo->ShippingAddress->phone; ?></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="items">
        <table>
            <thead>
                <tr>
                    <th class="name">Name</th>
                    <th>Weight (lbs)</th>
                    <th>Price</th>
                    <th>Qty</th>
                    <th>Discount</th>
                    <th>Tax</th>
                    <th>Shipping</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
            <?php $index = 0; ?>
            <?php $subtotal = 0; ?>
            <?php $tax = 0; ?>
            <?php foreach ($eo->Products as $product): ?>
                <?php if ($product->quantity < 1) continue; ?>
                <?php $subtotal += $product->price * $product->quantity; ?>
                <?php $tax += $product->tax; ?>
                <tr class="product-item">
                    <td class="product-name">
                        <input type="hidden" name="return[products][<?php echo $index; ?>][name]" value="<?php echo $product->name; ?>" />
                        <input type="hidden" name="return[products][<?php echo $index; ?>][sku]" value="<?php echo $product->sku; ?>" />
                        <p><span class="title"><?php echo $product->name; ?></span> <a class="omit" href="javascript:;"></a> <a class="subtract-quantity" href="javascript:;"></a><br />
                        <span class="sku">SKU: <?php echo $product->sku; ?></span><br />
                        <?php foreach ($product->Options as $k => $option): ?>
                            <input type="hidden" name="return[products][<?php echo $index; ?>][Options][<?php echo $k; ?>][name]" value="<?php echo $option->name; ?>" />
                            <input type="hidden" name="return[products][<?php echo $index; ?>][Options][<?php echo $k; ?>][data]" value="<?php echo $option->data; ?>" />
                            <?php if ($option->name !== 'image'): ?>
                                <span class="<?php echo $option->name; ?>"><?php echo $option->name.': '.$option->data.' '; ?></span>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        </p>
                    </td>
                    <td class="product-weight">
                        <span><?php echo $product->weight; ?></span>
                        <input type="hidden" name="return[products][<?php echo $index; ?>][weight]" value="<?php echo $product->weight; ?>" />
                    </td>
                    <td class="product-price">
                        <span>$<?php echo $product->price; ?></span>
                        <input type="hidden" name="return[products][<?php echo $index; ?>][price]" value="<?php echo $product->price; ?>" />
                    </td>
                    <td class="product-quantity">
                        <span>-<?php echo $product->quantity; ?></span>
                        <input type="hidden" name="return[products][<?php echo $index; ?>][quantity]" value="<?php echo $product->quantity * -1; ?>" />
                    </td>
                    <td class="product-discount">
                        <span>$<?php echo $product->discount; ?></span>
                        <input type="hidden" name="return[products][<?php echo $index; ?>][discount]" value="0" />
                    </td>
                    <td class="product-tax">
                        <span>$<?php echo number_format(($eo->ShippingAddress->state === 'CA' && $product->tax == 0 ? $product->price * $product->quantity * $tax_rate : $product->tax) * -1, 2, '.', ''); ?></span>
                        <input type="hidden" name="return[products][<?php echo $index; ?>][tax]" value="<?php echo number_format(($eo->ShippingAddress->state === 'CA' && $product->tax == 0 ? $product->price * $product->quantity * $tax_rate : $product->tax) * -1, 2, '.', ''); ?>" />
                    </td>
                    <td class="product-shipping">
                        <span>$<?php echo $product->shipping; ?></span>
                        <input type="hidden" name="return[products][<?php echo $index; ?>][shipping]" value="0" />
                    </td>
                    <td class="product-total">
                        <span>$<?php echo $product->total * -1; ?></span>
                        <input type="hidden" name="return[products][<?php echo $index; ?>][total]" value="<?php echo $product->total * -1; ?>" />
                    </td>
                </tr>
                <?php $index++; ?>
            <?php endforeach; ?>
                <?php if ($index > 0): ?>
                <tr>
                    <td class="product-add" colspan="8">
                        <input class="index" type="hidden" value="<?php echo $index; ?>" />
                        <input class="qty" type="text" placeholder="quantity" />
                        <select class="select">
                        <option selected="selected" value="">Select a product</option>
                        <?php foreach ($prods as &$product): ?>
                            <?php $pname = Aether::filter_language_data($product['data']['Display Name'], 'EN', 'data', 0); ?>
                            <?php $sku = $product['data']['SKU']['data'][0]; ?>
                            <?php $name = $pname.' - '.$sku; ?>
                            <optgroup label="<?php echo $name; ?>">
                            <?php $inv = Inventory::get_product($product['data']['Inventory']['data'][0]); ?>
                            <?php $images = $product['data']['Product Display']; ?>
                            <?php $img = $images[0]['data'][0]; ?>
                            <?php if (is_array($inv) && ake('inventory', $inv)): ?>
                                <?php $options = array(); ?>
                                <?php $colors = array(); ?>
                                <?php foreach ($inv['inventory'] as $y => $inv_x): ?>
                                    <?php foreach ($inv_x as $x => $qty): ?>
                                        <?php if ($qty > 0): ?>
                                            <?php $color = $inv['options_y'][$y]['display_name']; ?>
                                            <?php $size = $inv['options_x'][$x]['display_name']; ?>
                                            <?php $colors[] = $color; ?>
                                            <?php foreach ($images as $image): ?>
                                                <?php if ($color == $image['color'][0]): ?>
                                                    <?php $img = $image['data'][0]; break; ?>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                            <?php $options[] = '<option data-sku="'.$sku.'" data-name="'.$pname.'" data-weight="'.$product['data']['Shipping Weight']['data'][0].'" data-sku="'.$product['data']['SKU']['data'][0].'" data-price="'.$product['data']['Price']['data'][0].'" data-image="'.$img.'" data-color="'.$color.'" data-size="'.$size.'" value="'.$product['entry']['id'].'">'."$color [$size]".'</option>'; ?>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                                <?php array_multisort($colors, SORT_ASC, $options); ?>
                                <?php foreach ($options as $option) echo $option; ?>
                            <?php endif; ?>
                            </optgroup>
                        <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td class="border" colspan="7">Subtotal:</td>
                    <td class="border order-subtotal">
                        <input disabled="disabled" type="text" name="return[order][subtotal]" value="<?php echo number_format($subtotal * -1, 2, '.', ''); ?>" />
                    </td>
                </tr>
                <tr>
                    <td colspan="7">Discount:</td>
                    <td class="order-discount">
                        <input type="text" name="return[order][discount]" value="<?php echo number_format($eo->discount, 2, '.', ''); ?>" />
                    </td>
                </tr>
                <tr>
                    <td colspan="7">Gift Card Discount:</td>
                    <td class="order-gc-discount">
                        <input disabled="disabled" type="text" name="return[order][gift_card_discount]" value="<?php echo number_format($eo->gift_card_discount, 2, '.', ''); ?>" />
                    </td>
                </tr>
                <tr>
                    <td colspan="7">Discounted Subtotal:</td>
                    <td class="discounted-subtotal">$<?php echo number_format($subtotal * -1 + ($eo->discount + $eo->gift_card_discount), 2, '.', ''); ?></td>
                </tr>
                <tr>
                    <td colspan="7">Tax:</td>
                    <td class="order-tax">
                        <?php if ($eo->tax > $tax) $tax = $eo->tax; ?>
                        <input disabled="disabled" type="text" name="return[order][tax]" value="<?php echo number_format($tax * -1, 2, '.', ''); ?>" />
                    </td>
                </tr>
                <tr>
                    <td colspan="7">Shipping:</td>
                    <td class="order-shipping">
                        <input disabled="disabled" type="text" name="return[order][shipping]" value="0.00" />
                    </td>
                </tr>
                <tr>
                    <td class="border" colspan="7">Total:</td>
                    <td class="border order-total">
                        <input disabled="disabled" type="text" name="return[order][total]" value="<?php echo number_format(($subtotal + $tax) * -1 + ($eo->discount + $eo->gift_card_discount) , 2, '.', ''); ?>" />
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
    <input class="order-weight" type="hidden" name="return[order][weight]" value="0" />
    <div>
        Order Status: 
        <select name="return[data][order_status_id]">
        <?php foreach (Ecommerce::get_status_options() as $group => $options): ?>
            <optgroup label="<?php echo $group; ?>">
            <?php foreach ($options as $k => $v): ?>
                <option value="<?php echo $k; ?>"><?php echo $v; ?></option>
            <?php endforeach; ?>
            </optgroup>
        <?php endforeach; ?>
        </select>
    </div>
    <div>
        Return Status: 
        <select name="return[data][return_status_id]">
        <?php foreach (Ecommerce::get_status_options() as $group => $options): ?>
            <optgroup label="<?php echo $group; ?>">
            <?php foreach ($options as $k => $v): ?>
                <option value="<?php echo $k; ?>"><?php echo $v; ?></option>
            <?php endforeach; ?>
            </optgroup>
        <?php endforeach; ?>
        </select>
    </div>
    <div style="margin-top:10px;">
        <?php if ($index > 0): ?>
        <button style="margin-right:10px;" type="submit">Process Return</button>
        <?php endif; ?>
        <a href="/admin/module/Ecommerce/view_order/<?php echo $eo->id; ?>/">Cancel</a>
    </div>
</div>
</form>
