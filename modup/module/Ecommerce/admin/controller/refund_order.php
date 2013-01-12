<?php

if (ake('type', $_POST) && defined('URI_PART_4'))
{
    $eot = Doctrine::getTable('EcommerceOrder');
    $eo = $eot->find(URI_PART_4);
    if ($eo !== FALSE)
    {
        switch ($_POST['type'])
        {
            case 'paypal':
                $paypal['AMT'] = number_format(abs($_POST['paypal']['amount']), 2, '.', '');
                $paypal['TRANSACTIONID'] = $eo->pp_transaction_id;
                $paypal['REFUNDTYPE'] = 'partial';
                $paypal['CURRENCYCODE'] = 'USD';
                $response = Ecommerce::paypal_request('RefundTransaction', '&' . http_build_query($paypal), PP_ENVIRONMENT);
                if ("SUCCESS" == strtoupper($response["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($response["ACK"]))
                {
                    $eo->pp_return_transaction_id = urldecode($response['REFUNDTRANSACTIONID']);
                    if ($eo->isValid())
                    {
                        $eo->save();
                        header('Location: /admin/module/Ecommerce/view_order/'.URI_PART_4);
                        exit;
                    }
                }
                else
                {
                    var_dump($response);
                }
            break;
        }
    }
}

throw new Exception("You don't belong here");

?>
