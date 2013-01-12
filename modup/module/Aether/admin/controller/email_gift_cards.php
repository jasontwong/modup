<?php
Admin::set('title', 'Gift Card Purchases');
Admin::set('header', 'Gift Card Purchases');

if (ake('id', $_REQUEST)){
    $gct = Doctrine::getTable('AetherGiftCard');
    $gc = $gct->find($_REQUEST['id']);
}

if (ake('coupon_code', $_POST)){
	$temp = array($gc['to_email'] => $gc['to_name']);
	if(ake('KRATEDEV', $_SERVER)){
        $temp = array(
            "jason@kratedesign.com" => $gc['to_name'],
            "jamie@kratedesign.com" => $gc['to_name'],
            "edwin@kratedesign.com" => $gc['to_name'],
        );
	}
	ob_start();
	include DIR_MODULE."/Aether/templates/gift_card_embedded.php";
	$content = ob_get_clean();
    $mailer = new Mailer(Ecommerce::get_email_accounts("customerservice"));
    $mailer->setSubject("Your Aether Apparel Gift Card")
        ->setFrom(array("customerservice@aetherapparel.com" => "Aether Apparel"))
        ->setBCC(array('customerservice@aetherapparel.com'))
        ->setBody($content, 'text/html')
        ->setTo($temp);
	$mailer->send();
    $gc->is_sent=1;
	$gc->save();
	header('Location: /admin/module/Aether/gift_cards/');
    exit;
}
?>
