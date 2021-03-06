<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<div id="message" class="updated woocommerce-message wc-connect">
	<p><?php echo sprintf( __( '<strong>%s Data Update Required</strong> &#8211; We just need to update your install to the latest version', 'wc_charter' ), WC_Charter()->name ); ?></p>
	<p class="submit"><a href="<?php echo add_query_arg( 'do_update_wc_charter', 'true', admin_url('admin.php?page=wc-charter-settings') ); ?>" class="wc-charter-update-now button-primary"><?php _e( 'Run the updater', 'wc_charter' ); ?></a></p>
</div>
<script type="text/javascript">
	jQuery('.wc-charter-update-now').click('click', function(){
		var answer = confirm( '<?php _e( 'It is strongly recommended that you backup your database before proceeding. Are you sure you wish to run the updater now?', 'wc_charter' ); ?>' );
		return answer;
	});
</script>
