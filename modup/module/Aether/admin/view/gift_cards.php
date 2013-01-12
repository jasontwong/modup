<?php if ($gift_cards): ?>

    <table id='notification-registrations'>
        <thead>
            <tr>
                <th>Order Name</th>
                <th>Amount</th>
                <th>Type</th>
                <th>To Name</th>
                <th>To Email</th>
                <th>From Name</th>
                <th>From Email</th>
                <th>Message</th>
                <th>Date</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($gift_cards as &$gc): ?>
                <tr>
                    <td>
                        <a href="/admin/module/Ecommerce/view_order/<?php echo $gc['order_id']; ?>/"><?php echo hsc($gc['order_name']) ?></a>
                    </td>
                    <td>
                        $<?php echo hsc(number_format($gc['amount'], 2)) ?>
                    </td>
                    <td>
                        <?php echo hsc($gc['type']) ?>
                    </td>
                    <td>
                        <?php echo hsc($gc['to_name']) ?>
                    </td>
                    <td>
                        <?php echo hsc($gc['to_email']) ?>
                    </td>
                    <td>
                        <?php echo hsc($gc['from_name']) ?>
                    </td>
                    <td>
                        <?php echo hsc($gc['from_email']) ?>
                    </td>
                    <td>
                        <?php echo hsc($gc['message']) ?>
                    </td>
                    <td>
                        <?php echo hsc(date('Y-m-d', $gc['created_date'])) ?>
                    </td>
                    <td>
                        <a href="<?php echo URI_PATH; ?>?delete=1&id=<?php echo $gc['id']; ?>">Delete</a>
                    </td>
                    <td>
                    	<?php if($gc['type']==="email"): if ($gc['is_sent']):?>
                      	Sent
						<?php else:?> 
                        <a href="/admin/module/Aether/email_gift_cards/?id=<?php echo $gc['id'];?>">Email Now</a>
						<?php endif;endif;?>
                    </td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>

<?php else: ?>

    <p>There are no gift cards at this time.</p>

<?php endif ?>
