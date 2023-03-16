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

    $option_name = 'partytown_analytics_inputs';
    $options = get_option( $option_name , array());
    $GA_ID = $options['ga_tracking'];
    $enabled = false;
    if($GA_ID) {
        $enabled = true;
    }
    if($enabled == true): 
?>
    <script>
        window.partytown = <?php echo wp_json_encode( $config ); ?>;
    </script>
    <script src="<?php echo $partytown_js;?>"></script>
    <?php
        if($GA_ID):
    ?>
    <script id="ga_script" type="text/partytown" src="<?php echo $plugin_dir ?>src/ga/analytics.js?id=<?php echo $GA_ID;?>"></script>
    <script type="text/partytown">
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', '<?php echo $GA_ID;?>');
    </script>
<?php
    endif;
    endif;
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

    if ( isset( $_POST['google_auth'] ) ) {
        ?>
        <div class="pta-popup-overlay">
            <form class="pta-popup" method="post" action="">
                <?php wp_nonce_field( 'partytown_analytics_save_settings', 'partytown_analytics_settings_nonce' ); ?>
                <div class="pta-section-heading">GA Properties<span onclick="window.location.href = window.location.href" style="margin-left: auto;"><svg width="12" height="12" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="121.31px" height="122.876px" viewBox="0 0 121.31 122.876" enable-background="new 0 0 121.31 122.876" xml:space="preserve"><g><path fill-rule="evenodd" clip-rule="evenodd" d="M90.914,5.296c6.927-7.034,18.188-7.065,25.154-0.068 c6.961,6.995,6.991,18.369,0.068,25.397L85.743,61.452l30.425,30.855c6.866,6.978,6.773,18.28-0.208,25.247 c-6.983,6.964-18.21,6.946-25.074-0.031L60.669,86.881L30.395,117.58c-6.927,7.034-18.188,7.065-25.154,0.068 c-6.961-6.995-6.992-18.369-0.068-25.397l30.393-30.827L5.142,30.568c-6.867-6.978-6.773-18.28,0.208-25.247 c6.983-6.963,18.21-6.946,25.074,0.031l30.217,30.643L90.914,5.296L90.914,5.296z"/></g></svg></span></div>
                <div class="pta-row">
                    <div class="pta-popup-sec" style="">
                        <label class="pta-label">Select GA</label>
                        <select class="pta-select" name="<?php echo esc_attr( $option_name ); ?>[ga_tracking]">
                            <?php
                                $lines = explode("\n",$_POST['data']);
                                foreach($lines as $x => $val) {
                                    $list = explode("||||", $val);
                                    $last = end($list);
                                    $val = str_replace("||||"," -> ",$val);
                                    echo "<option value='$last'>$val</option>";
                                }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="pta-section-heading">
                    <input type="submit" name="partytown_analytics_save_settings" value="Save Settings" class="button-primary pta-btn" />
                </div>
            </form>
        </div>
    <?php
    }
    
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