<?php
/**
 * Plugin Name:       ezMegaMenu
 * Plugin URI:        https://github.com/example/ezMegaMenu
 * Description:       A flexible and user-friendly mega menu plugin.
 * Version:           0.1.0
 * Author:            ezMegaMenu Team
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       ezmegamenu
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Plugin constants.
define( 'EZMM_PLUGIN_FILE', __FILE__ );
define( 'EZMM_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'EZMM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

if ( ! class_exists( 'EzMegaMenu' ) ) {
    class EzMegaMenu {
        /**
         * Constructor.
         */
        public function __construct() {
            // Load dependencies.
            $this->includes();

            if ( is_admin() ) {
                new EzMegaMenu_Admin();
            } else {
                new EzMegaMenu_Frontend();
            }
        }

        /**
         * Include required files.
         */
        private function includes() {
            require_once EZMM_PLUGIN_DIR . 'inc/admin/class-ezmegamenu-admin.php';
            require_once EZMM_PLUGIN_DIR . 'inc/frontend/class-ezmegamenu-frontend.php';
        }
    }
}

/**
 * Initialize the plugin.
 */
function ezmegamenu_init() {
    // Instantiate the main plugin class.
    new EzMegaMenu();
}
add_action( 'plugins_loaded', 'ezmegamenu_init' );
