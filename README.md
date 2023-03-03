# eBay-s-Trading-API
eBay's Trading API and retrieve metadata for a specific item like:

```  // Extract the metadata for the item
$item_id = (string) $xml->Item->ItemID;
$item_title = (string) $xml->Item->Title;
$item_price = (float) $xml->Item->SellingStatus->CurrentPrice;
$item_currency = (string) $xml->Item->SellingStatus->CurrentPrice['currencyID'];
$item_quantity = (int) $xml->Item->Quantity;
$item_start_time = (string) $xml->Item->ListingDetails->StartTime;
$item_end_time = (string) $xml->Item->ListingDetails->EndTime;   ```




In the above code, replace your_item_id_here with the actual item ID you want to retrieve metadata for, and replace your_access_token_here, your_dev_name_here, your_app_name_here, and your_cert_name_here with your actual eBay developer credentials. The curl_exec function will return the API response in XML format, which you can parse and extract the metadata for the item.
