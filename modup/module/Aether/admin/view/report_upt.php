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
            <button type="submit">Submit</button>
            <a href="<?php echo URI_PATH.'?export'; ?>">Export</a>
        </div>
    </div>
</form>

<div class="reports">
<?php $qty_total = 0; ?>
<?php $_SESSION['Aether']['upt']['export'] = array(); ?>
<?php foreach ($orders as $name => $qty): ?>
    <?php $qty_total += $qty; ?>
    <?php $data = "Order: $name - Products: $qty"; ?>
    <?php $_SESSION['Aether']['upt']['export'][] = array($data); ?>
    <div style="background-color:#EEE;font-weight:bold;"><?php echo $data; ?></div>
<?php endforeach; ?>
    <?php $_SESSION['Aether']['upt']['export'][] = array("Total Orders: ".count($orders)); ?>
    <?php $_SESSION['Aether']['upt']['export'][] = array("Total Products: $qty_total"); ?>
    <div style="background-color:#EEE;font-weight:bold;margin-top:20px;">Total Orders: <?php echo count($orders); ?></div>
    <div style="background-color:#EEE;font-weight:bold;">Total Products: <?php echo $qty_total; ?></div>
</div>
