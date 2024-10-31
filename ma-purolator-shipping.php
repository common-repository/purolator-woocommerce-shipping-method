<?php
/*
	Plugin Name: Purolator Shipping Method for WooCommerce
	Plugin URI: #
	Description: Obtain Real time shipping rates, shipping labels via Purolator Shipping API.
	Version: 1.0.1
	Author: MoreAddons
	Author URI: https://www.moreaddons.com/plugins/
	Copyright: 2017-2018 MoreAddons.
	Text Domain: ma_pu_ship
*/
	if (!defined('ABSPATH')) {
		exit;
	}
	if( !defined('MA_PU_URL_PATH') ){
		define("MA_PU_URL_PATH", plugins_url('',__FILE__));
	}
	if( !defined('MA_PU_ROOT_PATH') ){
		define("MA_PU_ROOT_PATH", plugin_dir_path(__FILE__ ));
	}
	
	if( !defined('MA_PU_ID') ){
		define("MA_PU_ID", "ma_purolator_shipping");
	}
	if(!defined('MA_PU_NEXT_VERSION'))
	{
		define ('MA_PU_NEXT_VERSION','1.0.1');
	}
/**
 * Check if WooCommerce is active
 */
require_once(ABSPATH . "wp-admin/includes/plugin.php");
if (in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) )) {	
	
	include_once ( 'pu-extend-functions.php' );
	
	
if (!function_exists('ma_pu_get_deprecated_url')){
	function ma_pu_get_deprecated_url(){
		return version_compare(WC()->version, '2.1', '>=') ? "wc-settings" : "woocommerce_settings";
	}
}

if( !class_exists('MA_PU_Shipping_Class') ){
	class MA_PU_Shipping_Class {
		
		public function __construct() {
			add_action('init', array($this, 'ma_load_plugin_textdomain'));
            add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'ma_pu_plugin_action_links' ) );
			add_action( 'woocommerce_shipping_init', array( $this, 'ma_pu_shipping_init' ) );
			add_filter( 'woocommerce_shipping_methods', array( $this, 'ma_pu_shipping_method_add' ) );		
			include_once('includes/extra-cart-details.php');
			}
			public function ma_load_plugin_textdomain() {

                load_plugin_textdomain('ma_pu_ship', false, dirname(plugin_basename(__FILE__)) . '/wpml');
            }
			public function ma_pu_plugin_action_links( $links ) {
				$plugin_links = array(
					'<a href="' . wp_nonce_url(admin_url( 'admin.php?page=' . ma_pu_get_deprecated_url() . '&tab=shipping&section=ma_purolator_shipping' )) . '">' . __( 'Purolator Settings', 'ma_pu_ship' ) . '</a>',
					//'<a href="#" target="_blank">' . __('Documentation', 'ma_pu_ship') . '</a>',
					'<a href="https://wordpress.org/support/plugin/purolator-woocommerce-shipping-method" target="_blank">' . __('Support', 'ma_pu_ship') . '</a>'
					);
				if ( array_key_exists( 'deactivate', $links ) ) {
					$links['deactivate'] = str_replace( '<a', '<a class="purolator-woocommerce-shipping-method-deactivate-link"', $links['deactivate'] );
				}
				return array_merge( $plugin_links, $links );
			}	
		public function ma_pu_shipping_init() {
			include_once( 'includes/ma-pu-shipping-configuration.php' );
			
		}

			
		public function ma_pu_shipping_method_add( $methods ) {
			$methods[] = 'ma_purolator_shipping';
			return $methods;
		}
	}
	}
	new MA_PU_Shipping_Class();	
		if (!class_exists('MoreAddons_Uninstall_feedback_Listener')) {
			require_once ("includes/class-moreaddons-uninstall.php");
		}
		$qvar = array(
			'name' => 'Purolator Shipping Method for WooCommerce',
			'version' => MA_PU_NEXT_VERSION,
			'slug' => 'purolator-woocommerce-shipping-method',
			'lang' => 'ma_pu_ship'
		);
		new MoreAddons_Uninstall_feedback_Listener($qvar);
}
else {
    add_action('admin_notices','ma_pu_wc_admin_notices', 99);
    deactivate_plugins(plugin_basename(__FILE__));
    function ma_pu_wc_admin_notices()
    {
        is_admin() && add_filter('gettext', function($translated_text, $untranslated_text, $domain)
        {
            $old = array(
                "Plugin <strong>activated</strong>.",
                "Selected plugins <strong>activated</strong>."
            );
            $new = "<span style='color:red'>Purolator Shipping Method for WooCommerce - WooCommerce is not Installed</span>";
            if (in_array($untranslated_text, $old, true)) {
                $translated_text = $new;
            }
            return $translated_text;
        }, 99, 3);
    }
    return;
}