<?php 
$cols = array(
    'Name', 'SKU', 'Color', 'Size', 'Price', 'Qty',
);
$last_name = $last_color = $last_cat = '';
$total_qty = $name_qty = $color_qty = 0;
$_SESSION['Aether']['inventory']['export'] = array();
?>
<script type="text/javascript">
$(function(){
    var color_id = $('#color-id'),
        size_id = $('#size-id'),
        product_id = $('#product-id'),
        qty = $('#qty'),
        overlay = $('.overlay');
    $('.background', overlay).fadeTo(0.1, 0.7);
    $('.adjust')
        .click(function(){
            var el = $(this);
            qty.val('');
            $('.name').text(el.data('name'));
            $('.color').text('Color: ' + el.data('colorname'));
            $('.size').text('Size: ' + el.data('sizename'));
            color_id.val(el.data('color'));
            size_id.val(el.data('size'));
            product_id.val(el.data('product'));
            $('.message', overlay)
                .text('');
            overlay
                .data('caller', el)
                .show();
        });
    $('a', overlay)
        .click(function(){
            overlay.hide();
        });
    $('button', overlay)
        .click(function(){
            var values = {
                    type: 'add',
                    id: product_id.val(),
                    option_x: size_id.val(),
                    option_y: color_id.val(),
                    quantity: qty.val()
                },
                caller = overlay.data('caller'),
                prod_qty = 0;
            qty.val('');
            $.post('/admin/rpc/Aether/inventory/', values, function(data) {
                if (data.success)
                {
                    prod_qty = parseInt(caller.text());
                    prod_qty += parseInt(values.quantity);
                    caller.text(prod_qty);
                    overlay.hide();
                }
                else
                {
                    $('.message', overlay)
                        .text('Error');
                }
            }, 'json');
        });
});
</script>
<style>
.overlay { display: none; height: 100%; left: 0px; position: fixed; top: 0px; width: 100%; }
.overlay .background { background-color: #FFF; height: 100%; position: absolute; width: 100%; }
.overlay .form { background-color: #FFF; border: 1px solid #000; height: 200px; left: 50%; margin: -100px 0px 0px -150px; padding: 20px; position: absolute; top: 50%; width: 300px; }
.overlay .viewer { height: 100%; position: relative; width: 100%; }
</style>
<div class="reports inventory-reports">
    <div class="overlay">
        <div class="viwer">
            <div class="background">&nbsp;</div>
            <div class="form">
                <p>
                    <span class="name">&nbsp;</span><br />
                    <span class="color">&nbsp;</span><br />
                    <span class="size">&nbsp;</span><br />
                </p>
                <p>Use positive numbers to add and negative numbers to subtract</p>
                <input id="color-id" type="hidden" name="product[color_id]" />
                <input id="size-id" type="hidden" name="product[size_id]" />
                <input id="product-id" type="hidden" name="product[id]" />
                <input id="qty" type="text" name="product[qty]" />
                <button type="submit">Add Quantity</button>
                <a href="javascript:;">Close</a>
                <p class="message"></p>
            </div>
        </div>
    </div>
    <p><a target="_blank" href="<?php echo URI_PATH; ?>?export">Export</a></p>
    <div>
        <table>
            <colgroup class="name"></colgroup>
            <colgroup class="sku"></colgroup>
            <colgroup class="color"></colgroup>
            <colgroup class="size"></colgroup>
            <colgroup class="price"></colgroup>
            <colgroup class="qty"></colgroup>
            <tbody>
            <?php foreach ($items as $k => $item): ?>
                <?php
                    $name_changed = $color_changed = FALSE;
                    if ($k === 0)
                    {
                        $last_color = $item['color'];
                        $last_name = $item['name'];
                    }
                    if ($last_name != $item['name'])
                    {
                        $name_changed = TRUE;
                        $last_name = $item['name'];
                    }
                    if ($last_color != $item['color'])
                    {
                        $color_changed = TRUE;
                        $last_color = $item['color'];
                    }
                    $row = array(
                        $item['name'], $item['sku'], $item['color'], $item['size'], '$'.number_format($item['price'], 2), $item['qty'],
                    );
                ?>
                <?php if ($color_changed): ?>
                    <tr style="background-color:#EEE;">
                        <td colspan="5" style="padding:5px 0px 5px 3px;">Color Subtotal:</td>
                        <td><?php echo $color_qty; ?></td>
                    </tr>
                    <?php $_SESSION['Aether']['inventory']['export'][] = array(
                        'Colot Subtotal:', '', '', '', '', $color_qty,
                    ); ?>
                    <?php $color_qty = 0; ?>
                <?php endif; ?>
                <?php if ($name_changed): ?>
                    <tr style="background-color:#CCC;">
                        <td colspan="5" style="padding:5px 0px 5px 3px;">Product Total:</td>
                        <td><?php echo $name_qty; ?></td>
                    </tr>
                    <?php $_SESSION['Aether']['inventory']['export'][] = array(
                        'Product Total:', '', '', '', '', $name_qty,
                    ); ?>
                    <?php $name_qty = 0; ?>
                <?php endif; ?>
                <?php if ($last_cat != $item['category']): ?>
                    <tr>
                        <td colspan="6">&nbsp;</td>
                    </tr>
                    <tr style="background-color:#000;">
                        <td colspan="6" style="color:#FFF;padding:5px 0 5px 3px;font-weight:bold;"><?php echo $item['category']; ?></td>
                    </tr>
                    <tr>
                        <td style="font-weight:bold;">Name</td>
                        <td style="font-weight:bold;">SKU</td>
                        <td style="font-weight:bold;">Color</td>
                        <td style="font-weight:bold;">Size</td>
                        <td style="font-weight:bold;">Price</td>
                        <td style="font-weight:bold;">Qty</td>
                    </tr>
                    <?php $last_cat = $item['category']; ?>
                    <?php $cat_qty = 0; ?>
                    <?php $_SESSION['Aether']['inventory']['export'][] = $cols; ?>
                <?php endif; ?>
                <?php $color_qty += $item['qty']; ?>
                <?php $name_qty += $item['qty']; ?>
                <?php $total_qty += $item['qty']; ?>
                <tr>
                    <td><?php echo $item['name']; ?></td>
                    <td><?php echo $item['sku']; ?></td>
                    <td><?php echo $item['color']; ?></td>
                    <td><?php echo $item['size']; ?></td>
                    <td><?php echo '$'.number_format($item['price'], 2); ?></td>
                    <td><a class="adjust" href="javascript:;" data-name="<?php echo $item['name'].' - '.$item['sku']; ?>" data-colorname="<?php echo $item['color']; ?>" data-sizename="<?php echo $item['size']; ?>" data-color="<?php echo $item['color_id']; ?>" data-size="<?php echo $item['size_id']; ?>" data-product="<?php echo $item['id']; ?>"><?php echo $item['qty']; ?></a></td>
                </tr>
                <?php $_SESSION['Aether']['inventory']['export'][] = $row; ?>
            <?php endforeach; ?>
                <tr style="background-color:#CCC;">
                    <td colspan="5" style="padding:5px 0px 5px 3px;">Grand Total:</td>
                    <td><?php echo $total_qty; ?></td>
                </tr>
                <?php $_SESSION['Aether']['inventory']['export'][] = array(
                    'Grand Total:', '', '', '', '', $total_qty,
                ); ?>
            </tbody>
        </table>
    </div>
</div>
