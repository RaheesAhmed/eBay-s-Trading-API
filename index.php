<?php
/**
 * Plugin Name: eBay API Plugin
 * Plugin URI: https://github.com/RaheesAhmed/eBay-Trading-API-Plugin
 * Description: A plugin that retrieves data from eBay using the Trading API.
 * Version: 1.0
 * Author: Rahees Ahmed
 * Author URI: https://github.com/RaheesAhmed/
 * License: GPL2+
 * License URI: https://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: ebay-api-plugin
 */


// Add the plugin menu to the WordPress admin dashboard
add_action( 'admin_menu', 'ebay_metadata_menu' );

function ebay_metadata_menu() {
    add_menu_page( 'eBay Metadata', 'eBay Metadata', 'manage_options', 'ebay-metadata', 'ebay_metadata_options' );
}

// Render the plugin options page
function ebay_metadata_options() {
    ?>
    <div class="wrap">
        <h2>eBay Metadata</h2>
        <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
            <?php wp_nonce_field( 'ebay_metadata_options' ); ?>
            <table class="form-table">
                <tr>
                    <th><label for="app_id">App ID</label></th>
                    <td><input type="text" name="app_id" id="app_id" value="<?php echo esc_attr( get_option( 'ebay_metadata_app_id' ) ); ?>" /></td>
                </tr>
                <tr>
                    <th><label for="dev_id">Dev ID</label></th>
                    <td><input type="text" name="dev_id" id="dev_id" value="<?php echo esc_attr( get_option( 'ebay_metadata_dev_id' ) ); ?>" /></td>
                </tr>
                <tr>
                    <th><label for="cert_id">Cert ID</label></th>
                    <td><input type="text" name="cert_id" id="cert_id" value="<?php echo esc_attr( get_option( 'ebay_metadata_cert_id' ) ); ?>" /></td>
                </tr>
                <tr>
                    <th><label for="auth_token">Auth Token</label></th>
                    <td><input type="text" name="auth_token" id="auth_token" value="<?php echo esc_attr( get_option( 'ebay_metadata_auth_token' ) ); ?>" /></td>
                </tr>
                <tr>
                    <th><label for="item_id">Item ID</label></th>
                    <td><input type="text" name="item_id" id="item_id" value="<?php echo esc_attr( get_option( 'ebay_metadata_item_id' ) ); ?>" /></td>
                </tr>
            </table>
            <p><input type="submit" class="button-primary" value="Retrieve Metadata" /></p>
            <input type="hidden" name="action" value="ebay_metadata_retrieve" />
        </form>
    </div>
    <?php
}

// Handle the eBay metadata retrieval request
add_action( 'admin_post_ebay_metadata_retrieve', 'ebay_metadata_retrieve' );

function ebay_metadata_retrieve() {
    // Verify that the user has permission to perform this action
    if ( ! current_user_can( 'manage_options' ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'ebay_metadata_options' ) ) {
        wp_die( 'You do not have permission to perform this action.' );
    }

    // Retrieve the plugin options
    $app_id = sanitize_text_field( $_POST['app_id'] );
    $dev_id = sanitize_text_field( $_POST['dev_id'] );
    $cert_id = sanitize_text_field( $_POST['cert_id'] );
    $auth_token = sanitize_text_field( $_POST['auth_token'] );
    $item_id = sanitize_text_field( $_POST['item_id'] );
    
    // Make the Trading API request to retrieve metadata for the specified eBay item
    $api_endpoint = 'https://api.ebay.com/ws/api.dll';
    $xml_request = <<<EOT
<?xml version="1.0" encoding="utf-8"?>
<GetItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">
  <RequesterCredentials>
    <eBayAuthToken>{$auth_token}</eBayAuthToken>
  </RequesterCredentials>
  <ItemID>{$item_id}</ItemID>
</GetItemRequest>
EOT;

    $ch = curl_init();
curl_setopt( $ch, CURLOPT_URL, $api_endpoint );
curl_setopt( $ch, CURLOPT_POSTFIELDS, $xml_request );
curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
curl_setopt( $ch, CURLOPT_POST, 1 );
curl_setopt( $ch, CURLOPT_HTTPHEADER, array(
    'X-EBAY-API-CALL-NAME: GetItem',
    'X-EBAY-API-APP-ID: ' . $app_id,
    'X-EBAY-API-DEV-NAME: ' . $dev_id,
    'X-EBAY-API-CERT-NAME: ' . $cert_id,
    'X-EBAY-API-SITEID: 0',
    'Content-Type: text/xml;charset=utf-8'
) );

$api_response = curl_exec( $ch );
curl_close( $ch );

// Parse the Trading API response and extract the metadata
$xml_response = simplexml_load_string( $api_response );
$xml_response->registerXPathNamespace( 'e', 'urn:ebay:apis:eBLBaseComponents' );
$item = $xml_response->xpath( '//e:GetItemResponse/e:Item' )[0];

// Display the retrieved metadata
echo '<div class="wrap">';
echo '<h2>eBay Item Metadata</h2>';
echo '<table class="widefat">';
echo '<thead><tr><th>Property</th><th>Value</th></tr></thead>';
echo '<tbody>';
echo '<tr><td>Item ID</td><td>' . $item->ItemID . '</td></tr>';
echo '<tr><td>Title</td><td>' . $item->Title . '</td></tr>';
echo '<tr><td>Price</td><td>' . $item->SellingStatus->CurrentPrice . ' ' . $item->SellingStatus->CurrentPrice->attributes()->currencyID . '</td></tr>';
echo '<tr><td>Condition</td><td>' . $item->ConditionDisplayName . '</td></tr>';
echo '<tr><td>Start Time</td><td>' . $item->StartTime . '</td></tr>';
echo '<tr><td>End Time</td><td>' . $item->EndTime . '</td></tr>';
echo '</tbody></table>';
echo '</div>';
}
    




