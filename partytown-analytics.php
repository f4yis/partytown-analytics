<?php
/**
 * Plugin Name: Partytown Analytics
 * Plugin URI: https://fayis.in
 * Description: Partytown Analytics for wordpress
 * Version: 1.0.0
 * Author: Mohamed Fayis
 * Author URI: https://fayis.in
 * License: Apache License, Version 2.0
 * License URI: https://www.apache.org/licenses/LICENSE-2.0.html
*/

function setup_partytown() {
    $site_url = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'];
    $plugin_dir = plugin_dir_url( __FILE__ );
    
    $partytown_dir = 'src/partytown/';

    $config = array(
        'lib' => str_replace($site_url, '', $plugin_dir . $partytown_dir),
    );
    $config = apply_filters( 'partytown_configuration', $config );

    $partytown_js = $plugin_dir . $partytown_dir . 'partytown.js';

?>
    <script>
        window.partytown = <?php echo wp_json_encode( $config ); ?>;
    </script>
    <script src="<?php echo $partytown_js;?>"></script>
    <script id="ga_script" type="text/partytown" src="<?php echo $plugin_dir ?>src/ga/analytics.js?id=G-Q93E91R2W9"></script>
    <script type="text/partytown">
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-Q93E91R2W9');
    </script>
<?php
}

add_action( 'wp_head', 'setup_partytown', 1 );

add_action( 'admin_menu', 'partytown_analytics_settings_menu' );

function partytown_analytics_settings_menu() {
    add_options_page(
        'Partytown Analytics Settings',
        'Partytown Analytics',
        'manage_options',
        'partytown-analytics-settings', // menu slug
        'partytown_analytics_settings_page' // callback function to display page content
    );
}

function partytown_analytics_settings_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    $option_name = 'partytown_analytics_inputs';

    if ( isset( $_POST['partytown_analytics_save_settings'] ) ) {
        $options = array();
        foreach ($_POST[$option_name] as $key => $value) {
            $options[$key] = sanitize_text_field( $value );
        }
        update_option( $option_name, $options );
    }
    $options = get_option( $option_name , array())
    
    ?>
    <div class="pta-wrap">
        <h1 class="pta-title"><?php echo esc_html( get_admin_page_title() ); ?></h1>
        <form class="pta-form" method="post" action="">
            <?php wp_nonce_field( 'partytown_analytics_save_settings', 'partytown_analytics_settings_nonce' ); ?>
            <div class="pta-section-heading">Integrations</div>
            <div class="pta-row">
                <div class="pta-labels">
                    <label class="pta-label">GA Tracking Code</label>
                    <p class="pta-desc">
                        Please enter the your google analytics tracking code.
                    </p>
                </div>
                <div class="pta-fields input-with-btn">
                    <input type="text" class="pta-input" placeholder="Tracking Code"
                    name="<?php echo esc_attr( $option_name ); ?>[ga_tracking]" value="<?php echo $options['ga_tracking'] ?? ''; ?>"
                    />
                    <button type="button" onclick="window.location.href = `http://localhost:3000/pta/auth?url=${window.location.href}`">LOGIN WITH GOOGLE</button>
                </div>
            </div>
            <div class="pta-hr"></div>
            <div class="pta-section-heading">
                <input type="submit" name="partytown_analytics_save_settings" value="Save Settings" class="button-primary pta-btn" />
            </div>
        </form>
    </div>
    <?php
}


function my_plugin_enqueue_styles() {
    $screen = get_current_screen();

    if ( $screen->id === 'settings_page_partytown-analytics-settings' ) {
        wp_enqueue_style( 'partytown-analytics-settings-styles', plugins_url( 'src/style.css', __FILE__ ) );
    }
}
add_action( 'admin_enqueue_scripts', 'my_plugin_enqueue_styles', 10, 1 );
?>