<form method="post" action="<?php echo URI_PATH; ?>">
    <div class="row">
        <div class="field">
            <input type="text" class="text date" name="filters[start_date]" placeholder="Start Date" value="<?php echo $filters['start_date']; ?>" />
        </div>
    </div>
    <div class="row">
        <div class="field">
            <input type="text" class="text date" name="filters[end_date]" placeholder="End Date" value="<?php echo $filters['end_date']; ?>" />
        </div>
    </div>
    <div class="row">
        <div class="field">
        <?php foreach ($radios as $radio => $label): ?>
            <?php if ($radio === $filters['type']): ?>
            <input checked="checked" type="radio" class="radio" name="filters[type]" value="<?php echo $radio; ?>" />
            <?php echo $label; ?>
            <?php else: ?>
            <input type="radio" class="radio" name="filters[type]" value="<?php echo $radio; ?>" />
            <?php echo $label; ?>
            <?php endif; ?>
        <?php endforeach; ?>
        </div>
    </div>
    <div class="row">
        <div class="field">
        <?php foreach ($checkboxes as $value => $label): ?>
            <?php if (in_array($value, $filters['product_type'])): ?>
            <input checked="checked" type="checkbox" class="checkbox" name="filters[product_type][]" value="<?php echo $value; ?>" />
            <?php echo $label; ?>
            <?php else: ?>
            <input type="checkbox" class="checkbox" name="filters[product_type][]" value="<?php echo $value; ?>" />
            <?php echo $label; ?>
            <?php endif; ?>
        <?php endforeach; ?>
        </div>
    </div>
    <div class="row">
        <div class="field">
            <button type="submit">Submit</button> Export: <a href="<?php echo URI_PATH.'?export'; ?>">CSV</a> | <a href="<?php echo URI_PATH.'?export&type=pdf'; ?>">PDF</a>
        </div>
    </div>
</form>

<div class="reports">
    <div>
        <table>
            <colgroup class="color"></colgroup>
            <colgroup class="size"></colgroup>
            <colgroup class="qty"></colgroup>
            <tbody>
            <?php ksort($items); ?>
            <?php $_SESSION['Aether']['products']['export'] = array(); ?>
            <?php $total_qty = $total_price = 0; ?>
            <?php foreach ($items as $name => $skus): foreach ($skus as $sku => $prices): foreach ($prices as $price => $colors): ?>
                <?php $qty_total = $totals[$name][$sku][$price]; ?>
                <?php $total_qty += $qty_total; ?>
                <?php $total_price += $price * $qty_total; ?>
                <?php $col1 = $name.' - '.$sku.' ('.$qty_total.' sold - $'.number_format($price * $qty_total, 2).')'; ?>
                <?php $_SESSION['Aether']['products']['export'][] = array($col1, '', ''); ?>
                <?php $_SESSION['Aether']['products']['export'][] = array('Color', 'Size', 'Qty Sold'); ?>
                <tr style="background-color:#EEE;">
                    <td><strong><?php echo $col1; ?></strong></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td style="border-bottom:1px solid #000;">Color</td>
                    <td style="border-bottom:1px solid #000;padding:10px 10px 0px 0px;text-align:right;">Size</td>
                    <td style="border-bottom:1px solid #000;text-align:right;">Qty Sold</td>
                </tr>
                <?php ksort($colors); ?>
                <?php foreach($colors as $color => $sizes): ?>
                <?php $col1 = $color.': '.$color_totals[$name][$sku][$price][$color].' ($'.number_format($price, 2).')'; ?>
                <?php $_SESSION['Aether']['products']['export'][] = array($col1, '', ''); ?>
                <tr>
                    <td style="padding-top:10px;"><strong><?php echo $color; ?></strong>: <?php echo $color_totals[$name][$sku][$price][$color]; ?> ($<?php echo number_format($price, 2); ?>)</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <?php ksort($sizes); ?>
                <?php foreach ($sizes as $size => $qty): ?>
                <?php $_SESSION['Aether']['products']['export'][] = array('', $size, $qty); ?>
                <tr>
                    <td>&nbsp;</td>
                    <td style="padding-right:10px;text-align:right;"><?php echo $size; ?></td>
                    <td style="text-align:right;"><?php echo $qty; ?></td>
                </tr>
                <?php endforeach; ?>
                <?php $_SESSION['Aether']['products']['export'][] = array('', '', ''); ?>
            <?php endforeach; ?>
<?php endforeach; endforeach; endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php $col1 = 'Grand Total - ('.$total_qty.' items sold $'.number_format($total_price, 2).')'; ?>
    <?php $_SESSION['Aether']['products']['export'][] = array($col1, '', ''); ?>
    <div style="background-color:#EEE;font-weight:bold;"><?php echo $col1; ?></div>
</div>
