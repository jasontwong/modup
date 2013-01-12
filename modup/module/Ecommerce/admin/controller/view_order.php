<?php

if (!User::has_perm('view ecommerce orders'))
{
    throw new Exception('You do not have access to this page');
}

Admin::set('title', 'View Order');
Admin::set('header', 'View Order');

if (!defined('URI_PART_4') || !is_numeric(URI_PART_4))
{
    throw new Exception('No order id');
}

$eot = Doctrine::getTable('EcommerceOrder');
$eo = $eot->find(URI_PART_4);

if ($eo === FALSE)
{
    throw new Exception('Order does not exist');
}

$eor = $eot->findOneByReturnedOrderName($eo->order_name);
$eoo = FALSE;
if (is_string($eo->returned_order_name) && strlen($eo->returned_order_name))
{
    $eoo = $eot->findOneByOrderName($eo->returned_order_name);
}

if (ake('email', $_POST))
{
    $email = $_POST['email'];
    if (ake('title', $email) && strlen($email['title']) < 1)
    {
        unset($email['title']);
    }
    $order = EcommerceAPI::get_order_details_by_id(URI_PART_4);
    ob_start();
    include Ecommerce::$templates_dir.'/'.$email['tpl'];
    $content = ob_get_clean();
    $mailer = new Mailer(Ecommerce::get_email_accounts($email['account']));
    $mailer->setSubject($email['subject'])
        ->setFrom(array($email['from'] => $email['from_name']))
        ->setBCC(array('customerservice@aetherapparel.com'))
        ->setBody($content, 'text/html')
        ->setTo(array($email['to'] => $email['to_name']));
    $mailer->send();
    header('Location: '.URI_PATH);
    exit;
}

?>
