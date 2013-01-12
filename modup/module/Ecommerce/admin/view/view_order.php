<div id="ecommerce_view_order">
    <div class="info">
        <div class="data cleared">
            <div class="col1">
                <p>Order Name: <?php echo $eo->order_name; ?></p>
                <p>Order Date: <?php echo date('Y-m-d', $eo->created_date); ?></p>
                <p>Status: <?php echo $eo->Status->name; ?></p>
                <p>Customer Email: <a href="mailto:<?php echo $eo->customer_email; ?>"><?php echo $eo->customer_email; ?></a></p>
            </div>
            <div class="col2">
                <?php if ($eor !== FALSE): ?>
                <p><a href="/admin/module/Ecommerce/view_order/<?php echo $eor->id; ?>/">View Return</a></p>
                <?php else: ?>
                <p><a href="/admin/module/Ecommerce/return_order/<?php echo $eo->id; ?>/">Create Return</a></p>
                <?php endif; ?>
                <p><a href="/admin/module/Ecommerce/edit_order/<?php echo $eo->id; ?>/">Edit Order</a></p>
                <p><a target="_blank" href="/admin/mod/Ecommerce/view_invoice/<?php echo $eo->id; ?>/">View Invoice</a></p>
                <?php if (strlen($eo->tracking_number)): ?>
                <p><a target="_blank" href="/admin/mod/Ecommerce/view_shipping/<?php echo $eo->id; ?>/">View Shipping Confirmation</a></p>
                <?php endif; ?>
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
                    <li><?php echo $eo->ShippingAddress->state; ?></li>
                    <li><?php echo $eo->ShippingAddress->country; ?></li>
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
            <?php foreach ($eo->Products as $product): ?>
                <tr>
                    <td class="product-name">
                        <p><?php echo $product->name; ?><br />
                        SKU: <?php echo $product->sku; ?><br />
                        <?php foreach ($product->Options as $option) if ($option->name !== 'image') echo $option->name.': '.$option->data.' '; ?>
                        </p>
                    </td>
                    <td><?php echo $product->weight; ?></td>
                    <td>$<?php echo $product->price; ?></td>
                    <td><?php echo $product->quantity; ?></td>
                    <td>$<?php echo $product->discount * -1; ?></td>
                    <td>$<?php echo $product->tax; ?></td>
                    <td>$<?php echo $product->shipping; ?></td>
                    <td>$<?php echo $product->total; ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td class="border" colspan="7">Subtotal:</td>
                    <td class="border">$<?php echo $eo->subtotal; ?></td>
                </tr>
                <tr>
                    <td colspan="7">Discount:</td>
                    <td>$<?php echo $eo->discount * -1; ?></td>
                </tr>
                <tr>
                    <td colspan="7">Gift Card Discount:</td>
                    <td>$<?php echo $eo->gift_card_discount * -1; ?></td>
                </tr>
                <tr>
                    <td colspan="7">Discounted Subtotal:</td>
                    <td>$<?php echo number_format($eo->subtotal - ($eo->discount + $eo->gift_card_discount), 2); ?></td>
                </tr>
                <tr>
                    <td colspan="7">Tax:</td>
                    <td>$<?php echo $eo->tax; ?></td>
                </tr>
                <tr>
                    <td colspan="7">Shipping:</td>
                    <td>$<?php echo $eo->shipping; ?></td>
                </tr>
                <tr>
                    <td class="border" colspan="7">Total:</td>
                    <td class="border">$<?php echo $eo->total; ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
    <div class="meta cleared">
        <div class="col1">
            <h2>Meta</h2>
            <ul>
                <?php if (count($eo->Options)) foreach ($eo->Options as $meta): ?>
                    <li><?php echo ucwords($meta->name); ?> - <?php echo $meta->data; ?></li>
                <?php endforeach; ?>
                <?php $coupons = array(); if (count($eo->Coupons)) foreach ($eo->Coupons as $coupon) $coupons[] = $coupon['code']; ?>
                <li>Coupons: <?php echo implode(', ', $coupons); ?></li>
                <li>User Comments: <?php echo $eo->user_comments; ?></li>
                <li>Admin Comments: <?php echo $eo->admin_comments; ?></li>
                <li>Weight (lbs): <?php echo $eo->weight; ?></li>
                <li>PayPal Authorization ID: <?php echo $eo->pp_authorization_id; ?></li>
                <li>PayPal Transaction ID: <?php echo $eo->pp_transaction_id; ?></li>
                <?php if (is_string($eo->returned_order_name) && strlen($eo->returned_order_name)): ?>
                    <li>PayPal Return Transaction ID: <?php echo $eo->pp_return_transaction_id; ?></li>
                <?php endif; ?>
                <li>Tracking Number: <?php echo $eo->tracking_number; ?></li>
            </ul>
            <?php if ($eoo != FALSE && is_string($eo->returned_order_name) && strlen($eo->returned_order_name)): ?>
                <?php if ((is_null($eo->pp_return_transaction_id) || !strlen($eo->pp_return_transaction_id)) && $eo->total < 0): ?>
                    <h2>Refund</h2>
                    <?php if ((time() - $eoo->created_date) >= (60 * 60 * 24 * 60)): ?>
                    <p>Refund not available. The original order is older than the 60 days.</p>
                    <?php else: ?>
                    <form method="post" action="/admin/module/Ecommerce/refund_order/<?php echo $eo->id; ?>/">
                        <input type="hidden" name="type" value="paypal" />
                        <input type="text" name="paypal[amount]" value="<?php echo abs($eo->total); ?>" />
                        <button type="submit">Refund</button>
                    </form>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        <div class="col2">
            <form action="<?php echo URI_PATH; ?>" method="post">
                <div class="row">
                    <div class="field">
                        Type: <select class="select" name="email[tpl]">
                        <?php foreach(Ecommerce::get_available_templates() as $tpl => $name): ?>
                            <option value="<?php echo $tpl; ?>"><?php echo $name; ?></option>
                        <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="field">
                        Email Account: <select class="select account" name="email[account]">
                        <?php foreach(Ecommerce::get_email_accounts() as $name => $config): ?>
                            <option value="<?php echo $name; ?>"><?php echo $name; ?></option>
                        <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="field">
                        From Name: <input class="text" type="text" name="email[from_name]" value="<?php echo User::i('nice_name'); ?>" />
                    </div>
                    <div class="field">
                        From Email: <input class="text" type="text" name="email[from]" value="<?php echo User::i('email'); ?>" />
                    </div>
                </div>
                <div class="row">
                    <div class="field">
                        To Name: <input class="text" type="text" name="email[to_name]" value="<?php echo $eo->BillingAddress->name; ?>" />
                    </div>
                    <div class="field">
                        To Email: <input class="text" type="text" name="email[to]" value="<?php echo $eo->customer_email; ?>" />
                    </div>
                </div>
                <div class="row">
                    <div class="field">
                        Subject: <input class="text" type="text" name="email[subject]" value="Your Order" />
                    </div>
                </div>
                <div class="row">
                    <div class="field">
                        Title: <input class="text" type="text" name="email[title]" />
                    </div>
                </div>
                <div class="row">
                    <div class="field">
                        <button class="button" type="submit">Send Email</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
