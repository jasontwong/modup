<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" style="margin-left: auto; margin-top: 0; margin-bottom: 0; margin-right: auto; background-color: #000;">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Redeem your Giftcard now!</title>

</head>
<body style="margin-left: auto; margin-top: 0; margin-bottom: 0; margin-right: auto; background-color: #000;" bgcolor="#000">
<table cellpadding="0" cellspacing="0" border="0" style="width: 764px; height: 619px; margin-top: 0; margin-right: auto; margin-bottom: 0; margin-left: auto; padding-top: 10px; padding-right: 20px; padding-bottom: 0px; padding-left: 20px; background-color: #000;" bgcolor="#000">
<tr style="margin-left: auto; margin-top: 0; margin-bottom: 0; margin-right: auto; background-color: #000;" bgcolor="#000">
	<td style="width: 20px; margin-top: 0; margin-right: auto; margin-bottom: 0; margin-left: auto; background-color: #000;" bgcolor="#000"></td>
	<td style="height: 332px; width: 744px; margin-top: 0; margin-right: auto; margin-bottom: 0; margin-left: auto; background-color: #000;" bgcolor="#000">
		<img src="http://<?php echo $_SERVER['HTTP_HOST'];?>/images/email/Aether-Gift-Card-02_03.jpg" style="border-top-style: none; border-right-style: none; border-bottom-style: none; border-left-style: none;" />
	</td>
	<td style="width: 20px; margin-top: 0; margin-right: auto; margin-bottom: 0; margin-left: auto; background-color: #000;" bgcolor="#000"></td>
</tr>
<tr style="margin-left: auto; margin-top: 0; margin-bottom: 0; margin-right: auto; background-color: #000;" bgcolor="#000">
	<td style="width: 20px; margin-top: 0; margin-right: auto; margin-bottom: 0; margin-left: auto; background-color: #000;" bgcolor="#000"></td>
    <td style="margin-left: auto; margin-top: 0; margin-bottom: 0; margin-right: auto; background-color: #000;" bgcolor="#000">
    	<table cellpadding="0" cellspacing="0" border="0" style="width: 744px; margin-top: 0; margin-right: auto; margin-bottom: 0; margin-left: auto; background-color: #000;" bgcolor="#000">
		<tr style="margin-left: auto; margin-top: 0; margin-bottom: 0; margin-right: auto; background-color: #000;" bgcolor="#000">
			<td style="width: 196px; height: 132px; margin-top: 0; margin-right: auto; margin-bottom: 0; margin-left: auto; background-color: #000;" bgcolor="#000">
				<a href="" target="_blank" style="text-decoration: none;">
					<img src="http://<?php echo $_SERVER['HTTP_HOST'];?>/images/email/Aether-Gift-Card-02_05.jpg" style="border-top-style: none; border-right-style: none; border-bottom-style: none; border-left-style: none;" />
				</a>
        </td>
    		<td style="width: 548; height: 132px; vertical-align: text-top; font-family: Arial; font-size: 13px; color: #FFF; line-height: 1.5em; text-align: left; margin-top: 0; margin-right: auto; margin-bottom: 0; margin-left: auto; padding-top: 12px; padding-right: 0; padding-bottom: 0; padding-left: 0; background-color: #000;" align="left" bgcolor="#000" valign="text-top">
    			<img src="http://<?php echo $_SERVER['HTTP_HOST'];?>/images/email/Aether-Gift-Card-02_06.jpg" style="border-top-style: none; border-right-style: none; border-bottom-style: none; border-left-style: none;" />
    		</td>
    	</tr>
    	<tr style="margin-left: auto; margin-top: 0; margin-bottom: 0; margin-right: auto; background-color: #000;" bgcolor="#000">
    		<td style="width: 196px; height: 132px; margin-top: 0; margin-right: auto; margin-bottom: 0; margin-left: auto; background-color: #000;" bgcolor="#000">
    			<a href="" target="_blank" style="text-decoration: none;">
    				<img src="http://<?php echo $_SERVER['HTTP_HOST'];?>/images/email/Aether-Gift-Card-02_07.jpg" style="border-top-style: none; border-right-style: none; border-bottom-style: none; border-left-style: none;" />
    			</a>
    		</td>
	        <td style="width: 548; height: 132px; vertical-align: text-top; font-family: Arial; font-size: 13px; color: #FFF; line-height: 1.5em; text-align: left; margin-top: 0; margin-right: auto; margin-bottom: 0; margin-left: auto; padding-top: 12px; padding-right: 0; padding-bottom: 0; padding-left: 0; background-color: #000;" align="left" bgcolor="#000" valign="text-top">
            CODE: <b><?php echo $_POST['coupon_code'];?></b> &emsp;&emsp; AMOUNT: <b>$<?php echo $gc['amount'];?></b><br /><br /><b>
			<?php if(strlen($gc['message'])):?>"<?php echo $gc['message'];?>"<br /><br /><?php endif;?>
			From: <?php echo $gc['from_name'];?></b>
			</td>
	    </tr>
		</table>
	</td>
    <td style="width: 20px; margin-top: 0; margin-right: auto; margin-bottom: 0; margin-left: auto; background-color: #000;" bgcolor="#000"></td>
</tr>
</table>

<style type="text/css">
body { margin: 0 auto !important; background-color: #000 !important; }
img { border: none !important; }
</style>
</body>
</html>