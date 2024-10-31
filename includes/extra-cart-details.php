<?php 
if (!defined('ABSPATH')) {
    exit;
}
	if(!class_exists('MA_PU_Extra_Cart_Details'))
	{
		class MA_PU_Extra_Cart_Details
		{
			public function __construct()
			{
				$settings 					 = get_option( 'woocommerce_'.MA_PU_ID.'_settings', null );
				$est_del_date = false;
				if(!empty($settings) && isset($settings))
				{
					$del_bool         =  isset($settings['est_del_date']) ? $settings['est_del_date'] : 'no' ;
					$est_del_date   = ($del_bool == 'yes') ? true : false;

				}

				if($est_del_date) {
				            add_filter( 'woocommerce_cart_shipping_method_full_label', array($this, 'ma_add_est_del_date'), 10, 2 );
				}
			  
			}
			
		public function ma_add_est_del_date( $label, $method ) {
	        if( !is_object($method) ){
	            return $label;
	        }
	        $est_delivery = $method->get_meta_data();
	        if( isset($est_delivery['Delivery']) && !empty($est_delivery['Delivery']) ){
	            $est_delivery_html = "<br /><small>".__('Est delivery: ', 'ma_pu_ship'). $est_delivery['Delivery'].'</small>';
	            $est_delivery_html = apply_filters( 'ma_purolator_estimated_delivery', $est_delivery_html, $est_delivery );
	            $label .= $est_delivery_html;
	        }
	        return $label;
	    }

		}
	}
	new MA_PU_Extra_Cart_Details();