<p>Code: <?php echo $_REQUEST['code']; ?></p>

<p>Orders the coupon was used in:</p>
<ul>
<?php foreach ($orders as &$order): ?>
    <li><a href="/admin/module/Ecommerce/view_order/<?php echo $order['id']; ?>/"><?php echo $order['order_name']; ?></a></li>
<?php endforeach; ?>
</ul>
