<?php

if (!defined('URI_PART_4'))
{
    throw new Exception("How'd you get here?");
}

$order = EcommerceAPI::get_order_details_by_id(URI_PART_4);

include Ecommerce::$templates_dir.'/shipping.tpl.php';

?>
