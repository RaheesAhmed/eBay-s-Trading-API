<?php

$itemId = 'your_item_id_here';

$apiUrl = 'https://api.ebay.com/ws/api.dll';

$xmlRequest = '<?xml version="1.0" encoding="utf-8"?>
<GetItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">
    <RequesterCredentials>
        <eBayAuthToken>your_access_token_here</eBayAuthToken>
    </RequesterCredentials>
    <ItemID>' . $itemId . '</ItemID>
    <IncludeItemSpecifics>true</IncludeItemSpecifics>
</GetItemRequest>';

$headers = array(
    'X-EBAY-API-CALL-NAME: GetItem',
    'X-EBAY-API-SITEID: 0', // Site ID for US eBay site
    'X-EBAY-API-COMPATIBILITY-LEVEL: 1087',
    'X-EBAY-API-DEV-NAME: your_dev_name_here',
    'X-EBAY-API-APP-NAME: your_app_name_here',
    'X-EBAY-API-CERT-NAME: your_cert_name_here',
    'Content-Type: text/xml;charset=utf-8'
);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlRequest);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$response = curl_exec($ch);
curl_close($ch);

// Send API request and get the response in XML format
$response = file_get_contents($api_endpoint . $api_request);

// Parse the XML response
$xml = simplexml_load_string($response);

// Extract the metadata for the item
$item_id = (string) $xml->Item->ItemID;
$item_title = (string) $xml->Item->Title;
$item_price = (float) $xml->Item->SellingStatus->CurrentPrice;
$item_currency = (string) $xml->Item->SellingStatus->CurrentPrice['currencyID'];
$item_quantity = (int) $xml->Item->Quantity;
$item_start_time = (string) $xml->Item->ListingDetails->StartTime;
$item_end_time = (string) $xml->Item->ListingDetails->EndTime;

?>
