<?php
	define( 'LC_PARTNER_ID', '' );
	define( 'LC_UTM_CAMPAIGN', '' );
	define( 'PLUGIN_SLUG', 'wp-gumstack-for-wordpress' );
	define( 'PLUGIN_MAIN_FILE', PLUGIN_SLUG . '/gumstack.php' );
	define( 'OPTION_PREFIX', 'gumstack_' );
	define( 'MENU_SLUG', 'gumstack' );
	
	// Below has to be done this way because of PHP 5.6 limitations for using arrays in define.
	const DEPRECATED_OPTION_PREFIXES = array(
	        'wp-legacy'  => 'gumstack_',
	        'woo-legacy' => 'wc-lc_',
	        'woo-2.x'    => 'woo_gumstack_',
	);