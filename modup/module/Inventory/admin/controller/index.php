<?php

Admin::set('title', 'Inventory Product Groups and Option Groups');
Admin::set('header', 'Inventory Product Groups and Option Groups');

// get all product groups and product options
$product_groups = Inventory::get_product_groups();
$option_groups = Inventory::get_option_groups();

?>
