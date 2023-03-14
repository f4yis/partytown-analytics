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
    <script type="text/partytown">
        console.log('from party town')
    </script>
<?php
}

add_action( 'wp_head', 'setup_partytown', 1 );

?>