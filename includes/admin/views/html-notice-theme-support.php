<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<div id="message" class="updated woocommerce-message wc-connect">
	<p><?php _e( sprintf( '<strong>Your theme does not declare %s support</strong> &#8211; if you encounter layout issues please read the integration guide or choose a theme that is compatible with %s.', WC_Charter()->name, WC_Charter()->name ), 'wc_charter' ); ?></p>
	<p class="submit"><a href="<?php echo esc_url( apply_filters( 'wc_charter_theme_docs_url', WC_Charter()->doc_url . 'theme-compatibility-intergration/', 'theme-compatibility' ) ); ?>" class="button-primary"><?php _e( 'Theme Integration Guide', 'wc_charter' ); ?></a> <a class="skip button-primary" href="<?php echo esc_url( add_query_arg( 'hide_wc_charter_theme_support_check', 'true' ) ); ?>"><?php _e( 'Hide this notice', 'wc_charter' ); ?></a></p>
</div>
