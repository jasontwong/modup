<script type="text/javascript">
(function($){
    $(function(){
        $('#rates td a').click(function(){
            $(this).closest('tr').remove();
        });
    });
})(jQuery);
</script>
<style>
form { font-size: 1.2em; }
</style>
<form method="post" action="<?php echo URI_PATH; ?>">
<table id="rates">
    <thead>
        <tr>
            <th>SKU</th>
            <th>Price</th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($data as $sku => $rate): ?>
        <tr>
            <td style="padding:0px 5px 5px 0px;"><input class="text" placeholder="SKU" type="text" name="skus[]" value="<?php echo $sku; ?>" /></td>
            <td><input class="text" placeholder="0.00" type="text" name="rates[]" value="<?php echo $rate; ?>" /></td>
            <td><a href="javascript:;">Delete</a></td>
        </tr>
    <?php endforeach; ?>
        <tr>
            <td style="padding:0px 5px 5px 0px;"><input class="text" placeholder="SKU" type="text" name="skus[]" value="" /></td>
            <td><input class="text" placeholder="0.00" type="text" name="rates[]" value="" /></td>
            <td>&nbsp;</td>
        </tr>
    </tbody>
</table>
<div style="padding-top:15px;"><button type="submit">Update</button></div>
</form>
