<?php
/*
Misc Functions
*/
function mediawhale_admin_menu() {
        add_menu_page(
            __( '', 'mediawhale' ),
            __( 'Media Whale', 'mediawhale' ), 'manage_options',
            'mediawhale', 'mediawhale_admin_page_contents',
            MEDIAWHALE_URL."/admin/img/mediawhaleicon.png",
            3
        );
    }
add_action( 'admin_menu', 'mediawhale_admin_menu' );

function mediawhale_admin_page_contents() {
      require_once(MEDIAWHALE_DIR.'/admin/admin.php');
 }

 function mediawhale_article_usage($user_id){
    if(!empty($user_id)){
    ?>
    <div class="article-usage-temp">
        <?php
        $response_data = wp_remote_get('https://app.mediawhale.com/wp-admin/admin-ajax.php?action=get_article_count_ajax_request&user_id='.$user_id);
        echo esc_html($response_data['body']);
        ?>
    </div>
    <?php 
    }
 }

 function mediawhale_connected_account($user_id){
    if(!empty($user_id)){
    ?>
    <div class="article-usage-temp">
        <?php
        $response_data = wp_remote_get('https://app.mediawhale.com/wp-admin/admin-ajax.php?action=get_connected_account_request&user_id='.$user_id);
        echo esc_html($response_data['body']);
        ?>
    </div>
    <?php 
    }
 }

function mediawhale_player($atts){
    
    $post_id = 0;
    if(!empty($atts['post_id'])){
        $post_id = $atts['post_id'];
    }
    else{
        $post_id = $GLOBALS['post']->ID;
    }
    
    $converted_to_audio  = get_post_meta($post_id,'converted_audio', true);
    if(!empty($converted_to_audio)){
        $embed_code = '<div class="mediawhale-embed-code"><p><iframe class="mediawhale-iframe" src="https://app.mediawhale.com/api/audio-embed/?id='.$converted_to_audio.'&token='.rand().'" width="100%" height="125" frameborder="0" scrolling="no" border="no" allowtransparency="true"></iframe></p></div>';
        return $embed_code;
    }
}
add_shortcode('mediawhale_player','mediawhale_player');


function mediawhale_add_to_content( $content ) {

    if( is_single() && ! empty( $GLOBALS['post'] ) ) {
        $mediawhale_settings = get_option( 'mediawhale_settings' ); // Array of All Options
        $display_settings = $mediawhale_settings['display_player'];
        $convert_check = get_post_meta($GLOBALS['post']->ID,'mediawhaleconvert-to-audio',true);
        if ( $GLOBALS['post']->ID == get_the_ID() &&   $convert_check == 'yes') {

            $embed_code_id = get_post_meta($GLOBALS['post']->ID,'converted_audio', true);
            if(!empty($embed_code_id)){
                $embed_code = '<div class="mediawhale-embed-code"><p><iframe class="mediawhale-iframe" src="https://app.mediawhale.com/api/audio-embed/?id='.$embed_code_id.'&token='.rand().'" width="100%" height="125" frameborder="0" scrolling="no" border="no" allowtransparency="true"></iframe></p></div>';
                if($display_settings == 'before_content'){
                    $content = $embed_code.$content;
                }
                if($display_settings == 'after_content'){
                     $content .= $embed_code;
                }            
            }
        }
    }

    return $content;
}
add_filter('the_content', 'mediawhale_add_to_content');


/*
Comment For Bright Future
function mediawhale_title_content( $title, $id = null ) {
 
  if( is_single() && ! empty( $GLOBALS['post'] ) ) {
     $mediawhale_settings = get_option( 'mediawhale_settings' ); // Array of All Options
     $display_settings = $mediawhale_settings['display_player'];
     $embed_code = '<div class="mediawhale-embed-code"><p><iframe class="mediawhale-iframe" src="https://app.mediawhale.com/api/audio-embed/?id='.get_post_meta($GLOBALS['post']->ID,'converted_audio', true).'" width="100%" height="80" frameborder="0" scrolling="no" border="no" allowtransparency="true"></iframe></p></div>';

     if($display_settings == 'before_title'){
        $title = $embed_code.$title;
     }
     if($display_settings == 'after_title'){
         $title .= $embed_code;
     } 
  }
 
  return $title;
}
add_filter( 'the_title', 'mediawhale_title_content', 10, 2 );
*/

function mediawhale_convert_function($data){
   // print_r($data);
    $mediawhale_connection = get_option('mediawhale_connection');
    $mediawhale_connection = explode('_MDWHALE_',$mediawhale_connection);
    $url = 'https://app.mediawhale.com/wp-admin/admin-ajax.php?action=add_article_ajax_request_via_plugin';
    $response = wp_remote_post( $url, array(
        'method'      => 'POST',
        'timeout'     => 45,
        'redirection' => 5,
        'httpversion' => '1.0',
        'blocking'    => true,
        'headers'     => array(),
        'body'        => array(
            'nonce' => wp_create_nonce('dash_content_form'),
            'title' => sanitize_text_field($data['post_title']),
            'textarea' => apply_filters('the_content', sanitize_text_field($data['post_content'])),
            'post_id' => sanitize_text_field($data['post_id']),
            'voice_name' => get_option('mediawhale_settings')['voice_name_1'],
            'site' => site_url(),
            'language_name' => get_option('mediawhale_settings')['language_name_0'],
            'user_id_api' => $mediawhale_connection[1]
        ),
        )
    );
    return $response;
}

add_filter( 'updated_post_meta' , 'mediawhale_throw_to_server_post_data' , 10, 4);
function mediawhale_throw_to_server_post_data($meta_id, $post_id, $meta_key='', $meta_value='') {
    $data_new = array();
    $data_new['post_title'] = get_the_title($post_id);
    $data_new['post_content'] = get_post_field('post_content', $post_id);
    $data_new['post_id'] = $post_id;
    $converted_to_audio = get_post_meta($post_id,'converted_audio', true);  
    $convert_check = get_post_meta($post_id,'mediawhaleconvert-to-audio',true);
    if($convert_check == 'yes'){
        if(!empty($data_new['post_title']) && !empty($data_new['post_content'])){
                $response = mediawhale_convert_function($data_new);
                if ( is_wp_error( $response ) ) {
                    $error_message = $response->get_error_message();
                    _e("Something went wrong: $error_message","mediawhale");
                } else {
                   // print_r(wp_remote_retrieve_body($response));
                    $body_content = json_decode(wp_remote_retrieve_body($response));
                    if($body_content->type == '200'){
                        update_post_meta($post_id,'converted_audio',sanitize_text_field($body_content->postid));
                        update_post_meta($post_id,'test_log',sanitize_text_field($body_content->check_post_c));
                    }
                }
        }
    }
}
