<?php
function mediawhale_update_site_status_on_interval() {
 
    $nonce = $_POST['nonce'];

    if ( ! wp_verify_nonce( $nonce, 'mediawhalesiteconnection' ) ) {
        die( 'Nonce value cannot be verified.' );
    }
 
    // The $_REQUEST contains all the data sent via ajax
    if ( isset($_REQUEST) ) {
     
        update_option('mediawhale_connection',sanitize_text_field($_POST['connection']));
     
    }
     
    // Always die in functions echoing ajax content
   die();
}
 
add_action( 'wp_ajax_mediawhale_update_site_status_on_interval', 'mediawhale_update_site_status_on_interval' );
 
// If you wanted to also use the function for non-logged in users (in a theme for example)
add_action( 'wp_ajax_nopriv_mediawhale_update_site_status_on_interval', 'mediawhale_update_site_status_on_interval' );