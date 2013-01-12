<?php
class upsRate {
    var $AccessLicenseNumber;  
    var $UserId;  
    var $Password;
    var $ShipperNumber;
    var $credentials;

    function setCredentials($access,$user,$pass,$shipper) {
        $this->AccessLicenseNumber = $access;
        $this->UserID = $user;
        $this->Password = $pass;	
        $this->ShipperNumber = $shipper;
        $this->credentials = 1;
    }

    // Define the function getRate() - no parameters
    function getRate($shipper,$ship_from,$ship_to,$service,$dimensions)
    {
        if ($this->credentials != 1) {
            print 'Please set your credentials with the setCredentials function';
            die();
        }

        // REQUEST
        $request_string = <<<XML
        <AccessRequest xml:lang="en-US">  
            <AccessLicenseNumber>$this->AccessLicenseNumber</AccessLicenseNumber>  
            <UserId>$this->UserID</UserId>  
            <Password>$this->Password</Password>  
        </AccessRequest>
XML;
        $request_xml = new SimpleXMLElement($request_string);

        // RATE
        $rate_string = <<<XML
            <RatingServiceSelectionRequest xml:lang="en-US">  
                <Request>  
                    <TransactionReference>  
                        <CustomerContext>Rate Request</CustomerContext>  
                        <XpciVersion>1.000</XpciVersion>  
                    </TransactionReference>  
                    <RequestAction>Rate</RequestAction>  
                    <RequestOption>Shop</RequestOption>  
                </Request>
                <CustomerClassification>
                    <Code>00</Code>
                </CustomerClassification>
            </RatingServiceSelectionRequest>
XML;
        $rate_xml = new SimpleXMLElement($rate_string);
        $rate_xml->addChild('PickupType');
        $rate_xml->PickupType->addChild('Code', '01');

        // SHIPMENT
        $shipment_xml = &$rate_xml->Shipment;

        // RATE INFO
        /*
        $shipment_xml->addChild('RateInformation');
        $shipment_xml->RateInformation->addChild('NegotiatedRatesIndicator');
        */

        // SHIPPER
        $shipper_xml = &$shipment_xml->Shipper;
        $shipper_xml->addChild('ShipperNumber', $this->ShipperNumber);
        $shipper_xml->addChild('Address');
        foreach ($shipper as $k => $v)
        {
            $shipper_xml->Address->addChild($k, $v);
        }

        // SHIP TO
        $ship_to_xml = &$shipment_xml->ShipTo;
        $ship_to_xml->addChild('Address');
        foreach ($ship_to as $k => $v)
        {
            $ship_to_xml->Address->addChild($k, $v);
        }

        // SHIP FROM
        $ship_from_xml = &$shipment_xml->ShipFrom;
        $ship_from_xml->addChild('Address');
        foreach ($ship_from as $k => $v)
        {
            $ship_from_xml->Address->addChild($k, $v);
        }

        // PACKAGE
        $package_xml = &$shipment_xml->Package;

        // TYPE
        $package_xml->addChild('PackagingType');
        $package_xml->PackagingType->addChild('Code', '02');

        // WEIGHT
        $package_xml->addChild('PackageWeight');
        $package_xml->PackageWeight->addChild('UnitOfMeasurement');
        $package_xml->PackageWeight->UnitOfMeasurement->addChild('Code', 'LBS');
        $package_xml->PackageWeight->addChild('Weight', $dimensions['Weight']);
        unset($dimensions['Weight']);

        // DIMENSIONS
        $package_xml->addChild('Dimensions');
        $package_xml->Dimensions->addChild('UnitOfMeasurement');
        $package_xml->Dimensions->UnitOfMeasurement->addChild('Code', 'IN');
        foreach ($dimensions as $k => $v)
        {
            $package_xml->Dimensions->addChild($k, $v);
        }

        $data = $request_xml->asXML() . $rate_xml->asXML();

		$ch = curl_init("https://www.ups.com/ups.app/xml/Rate");  
		curl_setopt($ch, CURLOPT_HEADER, 1);  
		curl_setopt($ch,CURLOPT_POST,1);  
		curl_setopt($ch,CURLOPT_TIMEOUT, 60);  
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);  
		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);  
		curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);  
		curl_setopt($ch,CURLOPT_POSTFIELDS,$data);  
		$result=curl_exec ($ch);  
		curl_close($ch);  
	    // echo '<!-- '. $result. ' -->'; // THIS LINE IS FOR DEBUG PURPOSES ONLY-IT WILL SHOW IN HTML COMMENTS  
        $xml = simplexml_load_string(strstr($result, '<?'));
        $data = array();
        if ($xml->Response->ResponseStatusCode == 1)
        {
            foreach ($xml->RatedShipment as $rate)
            {
                $data[(string)$rate->Service->Code] = (string)$rate->TotalCharges->MonetaryValue;
            }
        }
        else
        {
            // var_dump($xml, $rate_xml, $request_xml);
        }
        return $data;
        /*
		$data = strstr($result, '<?');  
		$xml_parser = xml_parser_create();  
		xml_parse_into_struct($xml_parser, $data, $vals, $index);  
		xml_parser_free($xml_parser);  
		$params = array();  
		$level = array();  
		foreach ($vals as $xml_elem) {  
		 if ($xml_elem['type'] == 'open') {  
		if (array_key_exists('attributes',$xml_elem)) {  
		     list($level[$xml_elem['level']],$extra) = array_values($xml_elem['attributes']);  
		} else {  
		     $level[$xml_elem['level']] = $xml_elem['tag'];  
		}  
		 }  
		 if ($xml_elem['type'] == 'complete') {  
		$start_level = 1;  
		$php_stmt = '$params';  
		while($start_level < $xml_elem['level']) {  
		     $php_stmt .= '[$level['.$start_level.']]';  
		     $start_level++;  
		}  
		$php_stmt .= '[$xml_elem[\'tag\']] = $xml_elem[\'value\'];';  
        echo $php_stmt;
		eval($php_stmt);  
		 }  
		}  
        */
        // return $params['RATINGSERVICESELECTIONRESPONSE']['RATEDSHIPMENT']['TOTALCHARGES']['MONETARYVALUE'];  
    }
}
?>
