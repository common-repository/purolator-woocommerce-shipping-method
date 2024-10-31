<?php
if (!defined('ABSPATH')) {
    exit;
}
	global $woocommerce;
    $general_settings = get_option('woocommerce_'.MA_PU_ID.'_settings');
    if(empty($general_settings))
    {
        
	$wc_main_settings 							= array();
	$wc_main_settings['production'] 			= '';
	$wc_main_settings['account_number'] 		= '';
	$wc_main_settings['site_id'] 				= '';
	$wc_main_settings['site_password'] 			= '';
	$wc_main_settings['enabled'] 				= 'yes';
	$wc_main_settings['enabled_label'] 			= 'yes';
	$wc_main_settings['insure_contents'] 		= '';
	$wc_main_settings['debug'] 					= '';
	$wc_main_settings['pu_currency_type'] 		= '';
	$wc_main_settings['delivery_time'] 			= '';
	$wc_main_settings['offer_rates'] 			= 'all';
	$wc_main_settings['title'] 					= __( 'Purolator', 'ma_pu_ship' );
	$wc_main_settings['availability'] 			= 'all';
	$wc_main_settings['base_country'] 			= $woocommerce->countries->get_base_country();
	$wc_main_settings['countries'] = array();

	$sort = 0;
	$ordered_services = array();
	foreach ( $this->services as $code => $name ) {
		if ( isset( $custom_services[ $code ]['order'] ) ) {
			$sort = $custom_services[ $code ]['order'];
			}
		while ( isset( $ordered_services[ $sort ] ) )
			$sort++;
			$ordered_services[ $sort ] = array( $code, $name );
			$sort++;
		}
		ksort( $ordered_services );
		$pu_service = array();
		foreach ( $ordered_services as $value ) {
			
			$code = $value[0];
			$name = $value[1];
			$pu_service[ $code]['order'] = isset( $custom_services[ $code ]['order'] ) ? $custom_services[ $code ]['order'] : '';
			$pu_service[ $code]['name'] = isset( $custom_services[ $code ]['name'] ) ? $custom_services[ $code ]['name'] : '';
			$pu_service[ $code]['enabled'] = true;
			$pu_service[ $code]['adjustment'] = isset( $custom_services[ $code ]['adjustment'] ) ? $custom_services[ $code ]['adjustment'] : '';
			$pu_service[ $code]['adjustment_percent'] = isset( $custom_services[ $code ]['adjustment_percent'] ) ? $custom_services[ $code ]['adjustment_percent'] : '';
		}
		$wc_main_settings['services'] = $pu_service;

		update_option('woocommerce_'.MA_PU_ID.'_settings',$wc_main_settings);
    }