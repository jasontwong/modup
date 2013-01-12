<div id="ecommerce_filters">
    <form action="<?php echo URI_PATH; ?>" method="get">
        <div class="group">
            <div class="row">
                <div class="label">Date</div>
                <div class="field">
                    <label>Start <input type="text" class="text date" name="filter[start_date]" value="<?php echo $filter['start_date']; ?>"></label>
                    <label>End <input type="text" class="text date" name="filter[end_date]" value="<?php echo $filter['end_date']; ?>"></label>
                </div>
            </div>
            <div class="row">
                <div class="label">State</div>
                <div class="field">
                    <input class="text" type="text" name="filter[state]" placeholder="2 letter code for US and CA" value="<?php echo $filter['state']; ?>">
                </div>
            </div>
            <div class="row">
                <div class="label">Product</div>
                <div class="field">
                    <input class="text" type="text" name="filter[name]" placeholder="Product Name" value="<?php echo $filter['name']; ?>">
                </div>
            </div>
            <div class="row">
                <div class="label">SKU</div>
                <div class="field">
                    <input class="text" type="text" name="filter[sku]" placeholder="Product SKU" value="<?php echo $filter['sku']; ?>">
                </div>
            </div>
            <div class="row">
                <div class="label">Sort</div>
                <div class="field">
                    <select name="filter[sort][type]">
                    <?php foreach ($types as $type => $name): ?>
                        <?php if ($type === $filter['sort']['type']): ?>
                        <option selected="selected" value="<?php echo $type; ?>"><?php echo $name; ?></option>
                        <?php else: ?>
                        <option value="<?php echo $type; ?>"><?php echo $name; ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    </select>
                    <select name="filter[sort][order]">
                        <option <?php if ($filter['sort']['order'] === 'ASC') echo 'selected="selected" '; ?>value="ASC">Ascending</option>
                        <option <?php if ($filter['sort']['order'] === 'DESC') echo 'selected="selected" '; ?>value="DESC">Descending</option>
                    </select>
                    <select name="filter[rows]">
                    <?php foreach ($rows as $row): ?>
                        <?php if ($filter['rows'] == $row): ?>
                        <option selected="selected" value="<?php echo $row; ?>"><?php echo $row; ?></option>
                        <?php else: ?>
                        <option value="<?php echo $row; ?>"><?php echo $row; ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    </select>
                    <button name="submit" type="submit">Submit</button>
                    <a href="/admin/module/Ecommerce/products/?<?php echo http_build_query(array('filter' => $filter)); ?>&export" target="_blank">Export</a>
                </div>
            </div>
        </div>
    </form>
</div>
<table id="ecommerce_products">
    <thead>
        <tr>
        <?php foreach ($columns as $col): ?>
            <th><?php echo $col; ?></th>
        <?php endforeach; ?>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($products as $product): ?>
        <tr>
        <?php foreach ($columns as $key => $col): ?>
            <td><?php echo deka('', $product, $key); ?></td>
        <?php endforeach; ?>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php if ($num_pages > 1): ?>
    <div id="products_pagination">
    <?php for ($i = 1; $i <= $num_pages; $i++): ?>
        <?php if ($i == $page): ?>
            <?php echo $i; ?>
        <?php else: ?>
            <a href="/admin/module/Ecommerce/products/<?php echo $i; ?>/?<?php echo http_build_query($_GET); ?>"><?php echo $i; ?></a>
        <?php endif; ?>
    <?php endfor; ?>
    </div>
<?php endif; ?>
