<?php

class Mediawhale {
    private $config = '{"title":"Media Whale","prefix":"mediawhale","domain":"mediawhale","class_name":"Mediawhale","post-type":["post"],"context":"side","priority":"default","fields":[{"type":"radio","label":"Convert to Audio","default":"no","options":"yes : Yes\r\nno : No","id":"mediawhaleconvert-to-audio"}]}';

    public function __construct() {
        $this->config = json_decode( $this->config, true );
        if(get_option('mediawhale_connection')){
            add_action( 'add_meta_boxes', [ $this, 'add_meta_boxes' ] );
        }
        add_action( 'save_post', [ $this, 'save_post' ] );
    }

    public function add_meta_boxes() {
        foreach ( $this->config['post-type'] as $screen ) {
            add_meta_box(
                sanitize_title( $this->config['title'] ),
                $this->config['title'],
                [ $this, 'add_meta_box_callback' ],
                $screen,
                $this->config['context'],
                $this->config['priority']
            );
        }
    }

    public function save_post( $post_id ) {
        foreach ( $this->config['fields'] as $field ) {
            switch ( $field['type'] ) {
                default:
                    if ( isset( $_POST[ $field['id'] ] ) ) {
                        $sanitized = sanitize_text_field( $_POST[ $field['id'] ] );
                        update_post_meta( $post_id, $field['id'], $sanitized );
                    }
            }
        }
    }

    public function add_meta_box_callback() {
        $this->fields_div();
    }

    private function fields_div() {
        $mediawhale_connection = get_option('mediawhale_connection');
        $mediawhale_connection = explode('_MDWHALE_',$mediawhale_connection);
        $check_status_account = wp_remote_get('https://app.mediawhale.com/wp-admin/admin-ajax.php?action=media_whale_check_if_allowed_via_rest&user_id='.$mediawhale_connection[1]);
        $check_status_account = $check_status_account['body'];
        $check_status_account = json_decode($check_status_account);
        if(!empty(get_option('mediawhale_settings')['voice_name_1']) && !empty(get_option('mediawhale_settings')['language_name_0'])){ 
        if($check_status_account->type == '400'){
            ?>
            <div class="mediawhale_upgrade_req">
                <?php _e('You need to upgrade your account to add more articles.','mediawhale'); ?>
                <a href="https://app.mediawhale.com/your-membership/" target="_blank">Upgrade Account</a>
            </div>
            <?php
        }
        else{
            foreach ( $this->config['fields'] as $field ) {
                ?><div class="components-base-control">
                    <div class="components-base-control__field"><?php
                        $this->label( $field );
                        $this->field( $field );
                    ?></div>
                </div><?php
            }
        }
        }
        else{
            ?>
                <div class="convert_box_empty_not_sl empty_post_meta">
                                <div class="convert_box_empty_s">
                                        <?php _e("You need to select the Voice and Langauge <a href='".get_admin_url()."admin.php?page=mediawhale-settings'>settings.</a>","mediawhale"); ?>
                                </div>
                            </div>
            <?php
        }
    }

    private function label( $field ) {
        switch ( $field['type'] ) {
            case 'radio':
                echo '<div class="components-base-control__label">' . esc_html($field['label']) . '</div>';
                break;
            default:
                printf(
                    '<label class="components-base-control__label" for="%s">%s</label>',
                    $field['id'], esc_html($field['label'])
                );
        }
    }

    private function field( $field ) {
        switch ( $field['type'] ) {
            case 'radio':
                $this->radio( $field );
                break;
            default:
                $this->input( $field );
        }
    }

    private function input( $field ) {
        printf(
            '<input class="components-text-control__input %s" id="%s" name="%s" %s type="%s" value="%s">',
            isset( $field['class'] ) ? $field['class'] : '',
            $field['id'], $field['id'],
            isset( $field['pattern'] ) ? "pattern='{$field['pattern']}'" : '',
            $field['type'],
            $this->value( $field )
        );
    }

    private function radio( $field ) {
        printf(
            '<fieldset><legend class="screen-reader-text">%s</legend>%s</fieldset>',
            esc_html($field['label']),
            $this->radio_options( $field )
        );
    }

    private function radio_checked( $field, $current ) {
        $value = $this->value( $field );
        if ( $value === $current ) {
            return 'checked';
        }
        return '';
    }

    private function radio_options( $field ) {
        $output = [];
        $options = explode( "\r\n", $field['options'] );
        $i = 0;
        foreach ( $options as $option ) {
            $pair = explode( ':', $option );
            $pair = array_map( 'trim', $pair );
            $output[] = sprintf(
                '<label><input %s id="%s-%d" name="%s" type="radio" value="%s"> %s</label>',
                $this->radio_checked( $field, $pair[0] ),
                $field['id'], $i, $field['id'],
                $pair[0], $pair[1]
            );
            $i++;
        }
        return implode( '<br>', $output );
    }

    private function value( $field ) {
        global $post;
        if ( metadata_exists( 'post', $post->ID, $field['id'] ) ) {
            $value = get_post_meta( $post->ID, $field['id'], true );
        } else if ( isset( $field['default'] ) ) {
            $value = $field['default'];
        } else {
            return '';
        }
        return str_replace( '\u0027', "'", $value );
    }

}
new Mediawhale;
 

class MediaWhale_Settings {
    private $settings_options;

    public function __construct() {
        add_action( 'admin_menu', array( $this, 'settings_add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'settings_page_init' ) );
    }

    public function settings_add_plugin_page() {
        if(!empty(get_option('mediawhale_connection'))) {  
            add_submenu_page(
                'mediawhale',
                'Settings', // page_title
                'Settings', // menu_title
                'manage_options', // capability
                'mediawhale-settings', // menu_slug
                array( $this, 'settings_create_admin_page' ) // function
            );
        }
    }

    public function settings_create_admin_page() {
        $this->settings_options = get_option( 'mediawhale_settings' ); ?>

        <div class="wrap">
            <h2><?php _e('Settings','mediawhale'); ?></h2>
            <p><?php _e('Default Settings For Media Whale','mediawhale'); ?></p>
            <?php settings_errors(); ?>

            <form method="post" action="options.php">
                <?php
                    settings_fields( 'settings_option_group' );
                    do_settings_sections( 'settings-admin' );
                ?>
                <div class="shortcode-container">
                    <span class="shortcode-title"><?php _e('Shortcode: ','mediawhale'); ?></span>
                     <code>[mediawhale_player]</code>
                </div>
                <?php
                    submit_button();
                ?>
            </form>
        </div>
    <?php }

    public function settings_page_init() {
        register_setting(
            'settings_option_group', // option_group
            'mediawhale_settings', // option_name
            array( $this, 'settings_sanitize' ) // sanitize_callback
        );

        add_settings_section(
            'settings_setting_section', // id
            '', // title
            array( $this, 'settings_section_info' ), // callback
            'settings-admin' // page
        );

        add_settings_field(
            'language_name_0', // id
            'Language Name', // title
            array( $this, 'language_name_0_callback' ), // callback
            'settings-admin', // page
            'settings_setting_section' // section
        );

        add_settings_field(
            'voice_name_1', // id
            'Voice Name', // title
            array( $this, 'voice_name_1_callback' ), // callback
            'settings-admin', // page
            'settings_setting_section' // section
        );

        add_settings_field(
            'display_player', // id
            'Display Player', // title
            array( $this, 'display_player_callback' ), // callback
            'settings-admin', // page
            'settings_setting_section' // section
        );

    }

    public function settings_sanitize($input) {
        $sanitary_values = array();
        if ( isset( $input['language_name_0'] ) ) {
            $sanitary_values['language_name_0'] = $input['language_name_0'];
        }

        if ( isset( $input['voice_name_1'] ) ) {
            $sanitary_values['voice_name_1'] = $input['voice_name_1'];
        }
        if ( isset( $input['display_player'] ) ) {
            $sanitary_values['display_player'] = $input['display_player'];
        }


        return $sanitary_values;
    }

    public function settings_section_info() {
        
    }

    public function language_name_0_callback() {
        ?> 
        <select name="mediawhale_settings[language_name_0]" id="language_name" data-selected="<?php if(!empty($this->settings_options['language_name_0'])) { echo $this->settings_options['language_name_0']; } ?>" class="language_name" >
            <option value="16">Arabic</option>
            <option value="36">Brazilian</option>
            <option value="23">chinese, mandarin</option>
            <option value="24">Danish</option>
            <option value="25">Dutch</option>
            <option value="17">English, Australian</option>
            <option value="18">English, British</option>
            <option value="19">English, Indian</option>
            <option value="15" selected="">English, US</option>
            <option value="21">English, Welsh</option>
            <option value="22">French</option>
            <option value="26">French , Canadian</option>
            <option value="27">German</option>
            <option value="28">Hindi</option>
            <option value="29">Icelandic</option>
            <option value="30">Italian</option>
            <option value="31">Japanese</option>
            <option value="32">Korean</option>
            <option value="33">Norwegian</option>
            <option value="34">Polish</option>
            <option value="35">Portuguese</option>
            <option value="37">Romanian</option>
            <option value="38">Russian</option>
            <option value="40">Spanish,Castilian</option>
            <option value="39">Spanish,Mexican</option>
            <option value="42">Spanish,US</option>
            <option value="43">Swedish</option>
            <option value="44">Turkish</option>
            <option value="45">Welsh</option>
        </select>

        <?php
    }

    public function voice_name_1_callback() {
        ?> <select name="mediawhale_settings[voice_name_1]" data-selected="<?php if(!empty($this->settings_options['voice_name_1'])) { echo $this->settings_options['voice_name_1']; } ?>" id="voice_name" >
            <?php $selected = (isset( $this->settings_options['voice_name_1'] ) && $this->settings_options['voice_name_1'] === 'voice') ? 'selected' : '' ; ?>
            <option value="voice" <?php echo esc_html($selected); ?>> Voice</option>
        </select> 

        <a href="javascript:void(0);" class="listen-voice-sample">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
            <span><?php _e('Connecting...','mediawhale'); ?></span>
        </a>
        <div id="preview_audio">

        </div>
        <?php
    }

    public function display_player_callback() {
        ?> <select name="mediawhale_settings[display_player]"  id="display_player_callback" >
            <?php $selected = (isset( $this->settings_options['display_player'] ) && $this->settings_options['display_player'] === 'after_content') ? 'selected' : '' ; ?>
            <option value="after_content" <?php echo esc_html($selected); ?>>After Content</option>
            <!--
            <option value="after_title" <?php echo $selected; ?>>After Title</option>
            -->
            <?php $selected = (isset( $this->settings_options['display_player'] ) && $this->settings_options['display_player'] === 'before_content') ? 'selected' : '' ; ?>
            <option value="before_content" <?php echo esc_html($selected); ?>>Before Content</option>


            <?php $selected = (isset( $this->settings_options['display_player'] ) && $this->settings_options['display_player'] === 'use_shortcode') ? 'selected' : '' ; ?>
            <option value="use_shortcode" <?php echo esc_html($selected); ?>>Use Shortcode</option>


             <!--
             <option value="before_title" <?php echo $selected; ?>>Before Title</option>
             -->
        </select> 
        <div class="mediawhale-warning-txt"><?php _e('This option could display player at multiple spots on post page. As it depends on theme, that\'s how it\'s written. '); ?></div>
        <?php
    }


}
if ( is_admin() )
    $settings = new MediaWhale_Settings();