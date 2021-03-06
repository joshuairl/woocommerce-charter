<?php
/**
 * Installation related functions and actions.
 *
 * @author 		Joshua F. Rountree
 * @category 	Admin
 * @package 	WooCommerce Charter/Classes
 * @version 	0.0.1
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WC_Charter_Install' ) ) {

/**
 * WC_Charter_Install Class
 */
class WC_Charter_Install {

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		register_activation_hook( WC_CHARTER, array( &$this, 'install' ) );

		add_action( 'admin_init', array( &$this, 'install_actions' ) );
		add_action( 'admin_init', array( &$this, 'check_version' ), 5 );
		add_action( 'in_plugin_update_message-'.plugin_basename( WC_CHARTER_FILE ), array( &$this, 'in_plugin_update_message' ) );
	}

	/**
	 * check_version function.
	 *
	 * @access public
	 * @return void
	 */
	public function check_version() {
		if ( ! defined( 'IFRAME_REQUEST' ) && ( get_option( 'wc_charter_version' ) != WC_Charter()->version || get_option( 'wc_charter_db_version' ) != WC_Charter()->version ) )
			$this->install();
	}

	/**
	 * Install actions such as installing pages when a button is clicked.
	 */
	public function install_actions() {
		// Update button
		if ( ! empty( $_GET['do_update_wc_charter'] ) ) {

			$this->update();

			// Update complete
			delete_option( '_wc_charter_needs_update' );

		}
	}

	/**
	 * Install WooCommerce Charter
	 */
	public function install() {
		$this->create_options();

		// Queue upgrades
		$current_version = get_option( 'wc_charter_version', null );
		$current_db_version = get_option( 'wc_charter_db_version', null );

		// Remove the note tags ( '/*' and '*/' ) to enable updates.
		/*if ( version_compare( $current_db_version, '0.0.1', '<' ) && null !== $current_db_version ) {
			update_option( '_wc_charter_needs_update', 1 );
		}
		else {
			update_option( 'wc_charter_db_version', WC_Charter()->version );
		}*/

		// Update version
		update_option( 'wc_charter_version', WC_Charter()->version );
	}

	/**
	 * Handle updates
	 */
	public function update() {
		// Do updates
		$current_db_version = get_option( 'wc_charter_db_version' );

		// Remove the note tags ( '/*' and '*/' ) to enable updates.
		/*if ( version_compare( $current_db_version, '0.0.1', '<' ) || WC_CHARTER_VERSION == '0.0.1' ) {
			include( 'updates/woocommerce-extension-plugin-name-update-0.0.1.php' );
			update_option( 'wc_charter_db_version', '0.0.1' );
		}*/

		update_option( 'wc_charter_db_version', WC_Charter()->version );
	}

	/**
	 * Default options
	 *
	 * Sets up the default options used on the settings page
	 *
	 * @access public
	 */
	function create_options() {
		/** 
		 * This loads the settings for the plugin.
		 * First checks what version of WooCommerce is active,
		 * then loads the appropriate format.
		 */
		if( version_compare(WC_EXTEND_WOOVERSION, "1.6.6", '<=') || version_compare(WC_EXTEND_WOOVERSION, "2.0.20", '<=') ) {

			if(version_compare(WC_EXTEND_WOOVERSION, "1.6.6", '<=')){
				include_once( 'settings/v1.6.6/class-wc-charter-admin-settings.php' );
			}
			else if(version_compare(WC_EXTEND_WOOVERSION, "2.0.20", '<=')){
				include_once( 'settings/v2.0.20/class-wc-charter-admin-settings.php' );
			}

			$this->settings = new WC_Charter_Admin_Settings();
			$this->settings->get_settings();

			// Run through each settings to load the default settings.
			foreach ( $this->settings as $value ) {
				if ( isset( $value['default'] ) && isset( $value['id'] ) ) {
					$autoload = isset( $value['autoload'] ) ? (bool) $value['autoload'] : true;
					add_option( $value['id'], $value['default'], '', ( $autoload ? 'yes' : 'no' ) );
				}
			}
		}
		else{
			// Include settings so that we can run through defaults.
			include_once( 'class-wc-charter-admin-settings.php' );

			$settings = WC_Charter_Admin_Settings::get_settings_pages();

			// Run through each section and settings to load the default settings.
			foreach ( $settings as $section ) {
				$section = $section->get_settings();
				foreach ( $section as $value ) {
					if ( isset( $value['default'] ) && isset( $value['id'] ) ) {
						$autoload = isset( $value['autoload'] ) ? (bool) $value['autoload'] : true;
						add_option( $value['id'], $value['default'], '', ( $autoload ? 'yes' : 'no' ) );
					}
				}
			}
		}
	}

	/**
	 * Show details of plugin changes on Installed Plugin Screen.
	 *
	 * @return void
	 */
	function in_plugin_update_message() {
		$response = wp_remote_get( WC_CHARTER_README_FILE );

		if ( ! is_wp_error( $response ) && ! empty( $response['body'] ) ) {

			// Output Upgrade Notice
			$matches = null;
			$regexp = '~==\s*Upgrade Notice\s*==\s*=\s*[0-9.]+\s*=(.*)(=\s*' . preg_quote( WC_CHARTER_VERSION ) . '\s*=|$)~Uis';

			if ( preg_match( $regexp, $response['body'], $matches ) ) {
				$notices = (array) preg_split('~[\r\n]+~', trim( $matches[1] ) );

				echo '<div style="font-weight: normal; background: #147ACA; color: #fff !important; border: 1px solid #b76ca9; padding: 9px; margin: 9px 0;">';

				foreach ( $notices as $index => $line ) {
					echo '<p style="margin: 0; font-size: 1.1em; color: #fff; text-shadow: 0 1px 1px #B6D0DF;">' . preg_replace( '~\[([^\]]*)\]\(([^\)]*)\)~', '<a href="${2}">${1}</a>', $line ) . '</p>';
				}

				echo '</div>';
			}

			// Output Changelog
			$matches = null;
			$regexp = '~==\s*Changelog\s*==\s*=\s*[0-9.]+\s*-(.*)=(.*)(=\s*' . preg_quote( WC_CHARTER_VERSION ) . '\s*-(.*)=|$)~Uis';

			if ( preg_match( $regexp, $response['body'], $matches ) ) {
				$changelog = (array) preg_split('~[\r\n]+~', trim( $matches[2] ) );

				echo ' ' . __( 'What\'s new:', 'wc_charter' ) . '<div style="font-weight: normal;">';

				$ul = false;

				foreach ( $changelog as $index => $line ) {
					if ( preg_match('~^\s*\*\s*~', $line ) ) {
						if ( ! $ul ) {
							echo '<ul style="list-style: disc inside; margin: 9px 0 9px 20px; overflow:hidden; zoom: 1;">';
							$ul = true;
						}
						$line = preg_replace( '~^\s*\*\s*~', '', htmlspecialchars( $line ) );
						echo '<li style="width: 50%; margin: 0; float: left; ' . ( $index % 2 == 0 ? 'clear: left;' : '' ) . '">' . $line . '</li>';
					}
					else {
						if ( $ul ) {
							echo '</ul>';
							$ul = false;
						}
						echo '<p style="margin: 9px 0;">' . htmlspecialchars( $line ) . '</p>';
					}
				}

				if ($ul) {
					echo '</ul>';
				}

				echo '</div>';
			}
		}
	}
}

} // end if class exists.

return new WC_Charter_Install();

?>
