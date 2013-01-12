<?php
define('HOSTNAME', 'http://'.$_SERVER['HTTP_HOST']);
define('IMG_DIR', HOSTNAME.'/images/email');
?>
<html>

<head>
</head>
<body>

<center>

<table cellpadding='6' cellspacing='0' width='700'><tr><td style='background-color: #000000; font-family: Arial,sans-serif;'>

    <!-- header -->
    <table cellpadding='0' cellspacing='0' style='background-color: #000000; line-height: 0;'>
        <tr>
            <td><img height='17' width='24' src='<?php echo IMG_DIR ?>/b1.png' alt=''></td>
            <td><img height='17' width='200' src='<?php echo IMG_DIR ?>/b2.png' alt=''></td>
            <td><img height='17' width='464' src='<?php echo IMG_DIR ?>/b3.png' alt=''></td>
        </tr>
        <tr>
            <td><img height='40' width='24' src='<?php echo IMG_DIR ?>/b1.png' alt=''></td>
            <td><img height='40' width='200' src='<?php echo HOSTNAME ?>/images/aether-logo.jpg' alt='Aether'></td>
            <td><img height='40' width='464' src='<?php echo IMG_DIR ?>/b3.png' alt=''></td>
        </tr>
        <tr>
            <td><img height='20' width='24' src='<?php echo IMG_DIR ?>/b1.png' alt=''></td>
            <td><img height='20' width='200' src='<?php echo IMG_DIR ?>/b2.png' alt=''></td>
            <td><img height='20' width='464' src='<?php echo IMG_DIR ?>/b3.png' alt=''></td>
        </tr>
    </table>

    <!-- content -->
    <table cellpadding='0' cellspacing='0' style='background-color: #ffffff; text-align: left; font-family: Arial,sans-serif;'>
        <tr>
            <td><img width='62' src='<?php echo IMG_DIR ?>/w1.png' alt=''></td>
            <td>
                <table cellpadding='0' cellspacing='0' style='background-color: #ffffff;'>
                    <tr>
                        <td>

                            <img height='45' width='564' src='<?php echo IMG_DIR ?>/w2.png' alt=''>

                            <table cellpadding='0' cellspacing='0'>
                                <tr>
                                    <td style='width: 400px; font-size: 18px; font-weight: bold;'>
                                        <?php echo isset($email) && eka($email, 'title') ? $email['title'] : 'THANK YOU FOR YOUR ORDER'; ?>
                                    </td>
                                    <td style='font-size: 10px;'>
                                        ORDER #<?php echo $order['order_name'] ?> <br>
                                        <?php echo date('Y-m-d') ?>
                                    </td>
                                </tr>
                            </table>

                            <img height='40' width='564' src='<?php echo IMG_DIR ?>/w2.png' alt=''>
                            
                            <table cellpadding='0' cellspacing='0' style='font-family: Arial,sans-serif;'>
                                <tr>
                                    <td style='width: 200px; font-size: 10px;'>
                                        <div style='font-size: 12px; font-weight: bold;'>SHIPPING</div>
                                        <div style='color: #636466;'>
                                            <?php 
                                            echo $order['ShippingAddress']['name'].'<br>';
                                            if ($order['ShippingAddress']['company'])
                                            {
                                                echo $order['ShippingAddress']['company'].'<br>';
                                            }
                                            echo $order['ShippingAddress']['address1'];
                                            if ($order['ShippingAddress']['address2'])
                                            {
                                                echo '<br>'.$order['ShippingAddress']['address2'];
                                            }
                                            echo '<br>';
                                            echo $order['ShippingAddress']['city'];
                                            if ($order['ShippingAddress']['state'])
                                            {
                                                echo ', '.$order['ShippingAddress']['state'];
                                            }
                                            if ($order['ShippingAddress']['zipcode'])
                                            {
                                                echo ' '.$order['ShippingAddress']['zipcode'];
                                            }
                                            echo    '<br>'.
                                                    $order['ShippingAddress']['country'].'<br>'.
                                                    $order['ShippingAddress']['phone'].'<br>'.
                                                    $order['customer_email'].'<br>'.
                                                    '&nbsp;';
                                            ?>
                                        </div>
                                    </td>
                                    <td style='width: 200px; font-size: 10px;'>
                                        <div style='font-size: 12px; font-weight: bold;'>BILLING</div>
                                        <div style='color: #636466;'>
                                            <?php 
                                            echo $order['BillingAddress']['name'].'<br>';
                                            if ($order['BillingAddress']['company'])
                                            {
                                                echo $order['BillingAddress']['company'].'<br>';
                                            }
                                            echo $order['BillingAddress']['address1'];
                                            if ($order['BillingAddress']['address2'])
                                            {
                                                echo '<br>'.$order['BillingAddress']['address2'];
                                            }
                                            echo '<br>';
                                            echo $order['BillingAddress']['city'];
                                            if ($order['BillingAddress']['state'])
                                            {
                                                echo ', '.$order['BillingAddress']['state'];
                                            }
                                            if ($order['BillingAddress']['zipcode'])
                                            {
                                                echo ' '.$order['BillingAddress']['zipcode'];
                                            }
                                            echo    '<br>'.
                                                    $order['BillingAddress']['country'].'<br>'.
                                                    $order['BillingAddress']['phone'].'<br>'.
                                                    $order['customer_email'].'<br>'.
                                                    deka('', $order, 'options', 'how heard');
                                            ?>
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <div style='font-size: 10px; color: #939598; line-height: 16px;'>
                                <img height='27' width='564' src='<?php echo IMG_DIR ?>/w2.png' alt=''>
                                <div>
                                    THE FOLLOWING TRACKING NUMBER CAN BE USED TO TRACK YOUR ORDER: <?php echo $order['tracking_number']; ?>
                                </div>
                                <img height='14' width='564' src='<?php echo IMG_DIR ?>/w2.png' alt=''>
                                <div>
                                    <a href="<?php echo Ecommerce::get_tracking_link($order['tracking_number'], $order['shipping_carrier']); ?>">CLICK HERE TO VIEW TRACKING INFORMATION.</a>
                                </div>
                                <img height='22' width='564' src='<?php echo IMG_DIR ?>/w2.png' alt=''>
                            </div>

                            <?php foreach ($order['Products'] as $item): ?>
                                <div style='border-top: 1px solid #666666;'>
                                    <img height='3' width='564' src='<?php echo IMG_DIR ?>/w2.png' alt=''>
                                    <table cellpadding='0' cellspacing='0'>
                                        <tr>
                                            <td style='vertical-align: top; width: 151px;'>
                                                <?php 
                                                    echo strlen(deka('', $item, 'options', 'image'))
                                                        ? '<img width="113" height="113" src="'.str_replace(' ', '%20', HOSTNAME.hsc($item['options']['image'])).'" />'
                                                        : '<img width="113" height="113" src="'.IMG_DIR.'/no-image-available.png" />';
                                                ?>
                                            </td>
                                            <td style='vertical-align: top; width: 330px;'>
                                                <img height='15' width='330' src='<?php echo IMG_DIR ?>/iw2.png' alt=''>
                                                <div style='font-size: 14px; font-weight: bold;'>
                                                    <?php echo strtoupper(htmlspecialchars_decode($item['name'])) ?>
                                                </div>
                                                <div style='font-size: 12px; color: #636466;'>
                                                    <?php 
                                                    echo 'SKU NO: '.strtoupper($item['sku']);
                                                    if (deka(FALSE, $item, 'options', 'color'))
                                                    {
                                                        echo '<br>COLOR: '.strtoupper($item['options']['color']);
                                                    }
                                                    if (deka(FALSE, $item, 'options', 'size'))
                                                    {
                                                        echo '<br>SIZE: '.$item['options']['size'];
                                                    }
                                                    if (deka(FALSE, $item, 'quantity'))
                                                    {
                                                        echo '<br>QTY: '.$item['quantity'];
                                                    }
                                                    ?>
                                                </div>
                                            </td>
                                            <td style='vertical-align: top; width: 83px;'>
                                                <img height='15' width='83' src='<?php echo IMG_DIR ?>/iw3.png' alt=''>
                                                <div style='font-weight: bold; font-size: 12px;'>
                                                    $<?php echo number_format($item['price'], 2) ?>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            <?php endforeach ?>

                            <div style='border-top: 1px solid #666666;'>
                                <img height='3' width='564' src='<?php echo IMG_DIR ?>/w2.png' alt=''>
                                <table cellpadding='0' cellspacing='0' style='font-family: Arial,sans-serif; line-height: 18px;'>
                                    <tr>
                                        <td style='vertical-align: top; width: 151px;'>
                                            <img height='1' width='151' src='<?php echo IMG_DIR ?>/iw1.png' alt=''>
                                        </td>
                                        <td style='vertical-align: top; width: 330px; font-weight: bold; font-size: 12px;'>
                                            SUBTOTAL
                                        </td>
                                        <td style='vertical-align: top; font-weight: bold; font-size: 12px; width: 83px;'>
                                            $<?php echo number_format($order['subtotal'], 2) ?>
                                        </td>
                                    </tr>
                                    <?php if ($order['discount'] > 0): ?>
                                    <tr>
                                        <td style='width: 151px; vertical-align: top;'>
                                            <img height='1' width='151' src='<?php echo IMG_DIR ?>/iw1.png' alt=''>
                                        </td>
                                        <td style='width: 330px; vertical-align: top; font-weight: bold; font-size: 12px; color: #939598;'>
                                            COUPON DISCOUNT
                                        </td>
                                        <td style='vertical-align: top; font-weight: bold; font-size: 12px; color: #939598;'>
                                            $<?php echo number_format($order['discount'] * -1, 2) ?>
                                        </td>
                                    </tr>
                                    <?php endif; ?>
                                    <?php if ($order['gift_card_discount'] > 0): ?>
                                    <tr>
                                        <td style='width: 151px; vertical-align: top;'>
                                            <img height='1' width='151' src='<?php echo IMG_DIR ?>/iw1.png' alt=''>
                                        </td>
                                        <td style='width: 330px; vertical-align: top; font-weight: bold; font-size: 12px; color: #939598;'>
                                            GIFT CARD DISCOUNT
                                        </td>
                                        <td style='vertical-align: top; font-weight: bold; font-size: 12px; color: #939598;'>
                                            $<?php echo number_format($order['gift_card_discount'] * -1, 2) ?>
                                        </td>
                                    </tr>
                                    <?php endif; ?>
                                    <tr>
                                        <td style='width: 151px; vertical-align: top;'>
                                            <img height='1' width='151' src='<?php echo IMG_DIR ?>/iw1.png' alt=''>
                                        </td>
                                        <td style='width: 330px; vertical-align: top; font-weight: bold; font-size: 12px; color: #939598;'>
                                            SHIPPING &amp; HANDLING
                                        </td>
                                        <td style='vertical-align: top; font-weight: bold; font-size: 12px; color: #939598;'>
                                            $<?php echo number_format($order['shipping'], 2) ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style='width: 151px; vertical-align: top;'>
                                            <img height='1' width='151' src='<?php echo IMG_DIR ?>/iw1.png' alt=''>
                                        </td>
                                        <td style='width: 330px; vertical-align: top; font-weight: bold; font-size: 12px; color: #939598;'>
                                            TAX
                                        </td>
                                        <td style='vertical-align: top; font-weight: bold; font-size: 12px; color: #939598;'>
                                            $<?php echo number_format($order['tax'], 2) ?>
                                        </td>
                                    </tr>
                                </table>
                                <img height='3' width='564' src='<?php echo IMG_DIR ?>/w2.png' alt=''>
                            </div>
                            <div style='border-top: 1px solid #666666;'>
                                <img height='3' width='564' src='<?php echo IMG_DIR ?>/w2.png' alt=''>
                                <table cellpadding='0' cellspacing='0' style='font-family: Arial,sans-serif;'>
                                    <tr>
                                        <td style='width: 151px; vertical-align: top;'>
                                            <img height='1' width='151' src='<?php echo IMG_DIR ?>/iw1.png' alt=''>
                                        </td>
                                        <td style='width: 330px; vertical-align: top; font-weight: bold; font-size: 12px;'>
                                            ORDER TOTAL
                                        </td>
                                        <td style='vertical-align: top; font-weight: bold; font-size: 12px;'>
                                            $<?php echo number_format($order['total'], 2) ?>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <div style='font-size: 10px; color: #939598; line-height: 16px;'>
                                <img height='44' width='564' src='<?php echo IMG_DIR ?>/w2.png' alt=''>
                                <?php if ($order['user_comments']): ?>
                                    COMMENTS: <?php echo nl2br(strtoupper($order['user_comments'])) ?>
                                    <img height='14' width='564' src='<?php echo IMG_DIR ?>/w2.png' alt=''>
                                <?php endif ?>
                                QUESTIONS? CONTACT CUSTOMER SERVICE AT <a href='mailto:customerservice@aetherapparel.com'>CUSTOMERSERVICE@AETHERAPPAREL.COM</a><br>
                                OR GIVE US A CALL AT (323)-785-0701<br>
                                <a href='http://www.aetherapparel.com/'>WWW.AETHERAPPAREL.COM</a>
                                <img height='40' width='564' src='<?php echo IMG_DIR ?>/w2.png' alt=''>
                            </div>

                        </td>
                    </tr>

                </table>
            </td>
            <td><img width='62' src='<?php echo IMG_DIR ?>/w1.png' alt=''></td>
        </tr>
    </table>

</td></tr></table>

</center>

</body>

</html>
