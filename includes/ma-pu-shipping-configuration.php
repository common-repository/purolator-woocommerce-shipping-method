<?php
if (!defined('ABSPATH')) {
    exit;
}
class MA_purolator_Shipping extends WC_Shipping_Method {
	
    private $found_rates;
    private $services;
	public function __construct() {
        $this->id = MA_PU_ID;
        $this->method_title = __('Purolator', 'ma_pu_ship');
        $this->method_description = '<img style="float:right;" src="'.MA_PU_URL_PATH.'/resource/images/logo.png" width="200" />';
		$this->services = include( 'helper/list-of-services.php' );
		$this->init();  
    }
	
	private function init() {
       
        include_once('helper/default-values.php');
        // Load the settings.
        $this->init_form_fields();
        $this->init_settings();

        // Define user set variables
        $this->enabled         = isset( $this->settings['enabled'] ) ? $this->settings['enabled'] : 'no';
        $this->title           = $this->get_option('title', $this->method_title);
        $this->Purolator           = $this->get_option('title_enable', 'yes');
        $this->availability    = isset( $this->settings['availability'] ) ? $this->settings['availability'] : 'all';
        $this->countries       = isset( $this->settings['countries'] ) ? $this->settings['countries'] : array();
        $this->origin          = apply_filters('woocommerce_pu_origin_postal_code', str_replace(' ', '', strtoupper($this->get_option('origin'))));
        $selected_country      = isset($this->settings['base_country']) ? $this->settings['base_country'] : WC()->countries->get_base_country();
        $this->origin_country  = apply_filters('woocommerce_pu_origin_country_code', $selected_country);
        $this->account_number  = $this->get_option('account_number');
        $this->site_id         = $this->get_option('site_id');
        $this->site_password   = $this->get_option('site_password');
        $this->show_pu_extra_charges = $this->get_option('show_pu_extra_charges');
        $this->freight_shipper_city = $this->get_option('freight_shipper_city');
        $del_bool         =  $this->get_option( 'delivery_time' );
        $this->delivery_time   = ($del_bool == 'yes') ? true : false;
		$live_location = 'https://webservices.purolator.com/PWS/V1/Estimating/EstimatingService.asmx';
		$test_location = 'https://devwebservices.purolator.com/PWS/V1/Estimating/EstimatingService.asmx';
		$this->usr_token = $this->get_option('activation_key');
        $this->production      = (!empty($this->settings['production']) && $this->settings['production']  == 'yes') ? true : false;
        $this->location     = ($this->production == true) ? $live_location : $test_location;

        $debug_bool             = $this->get_option('debug');
        $this->debug            = ($debug_bool  == 'yes') ? true : false;
        
        $this->packing_method  = $this->get_option('packing_method', 'per_item');
        $this->custom_services = $this->get_option('services', array());
		$this->offer_rates     = $this->get_option('offer_rates', 'all');
		$this->pack_pu_type     = $this->get_option('pack_pu_type', 'CustomerPackaging');

        
        $this->weight_unit     = $this->get_option('weight_unit') == 'lb' ? 'LBS' : 'KG';

        $this->quoteapi_weight_unit = $this->weight_unit == 'LBS' ? 'lb' : 'kg';
		$this->dimension_unit = $this->weight_unit == 'LBS' ? 'in' : 'cm';
        
        $this->conversion_rate = !empty($this->settings['conversion_rate']) ? $this->settings['conversion_rate'] : '';
		$this->ship_from_address	=   'origin_address';
		$this->box_max_weight         = !empty($this->settings['box_max_weight']) ? $this->settings['box_max_weight'] : '';
		
        add_action('woocommerce_update_options_shipping_' . $this->id, array($this, 'process_admin_options'));
    }

    /**
     * is_available function.
     *
     * @param array $package
     * @return bool
     */
    public function is_available( $package ) {
        if ( "no" === $this->enabled || empty($this->enabled ) ) {
            return false;
        }

        if ( 'specific' === $this->availability ) {
            if ( is_array( $this->countries ) && ! in_array( $package['destination']['country'], $this->countries ) ) {
                return false;
            }
        } elseif ( 'excluding' === $this->availability ) {
            if ( is_array( $this->countries ) && ( in_array( $package['destination']['country'], $this->countries ) || ! $package['destination']['country'] ) ) {
                return false;
            }
        }
		if(!class_exists('SOAPClient'))
		{
				if($this->debug)
				{
					echo "<tr><td colspan='2'>";
					echo( '<div style="background: #fff;box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);font-size: 20px;line-height: 1em;  outline: 0;display: block;height: auto;    min-height: 100%;width: 100%;position: relative;-webkit-font-smoothing: subpixel-antialiased;border-left: 4px solid #dc3232;"><p style="padding:10px;"><b>Purolator Shipping Notification (Missing Class):</b> SOAPClient. Enbale SOAPClient to Send and Receive Rate Requests.</p></div>' );
					echo "</td></tr>";
				}
				return false;
			}
        return apply_filters( 'woocommerce_shipping_' . $this->id . '_is_available', true, $package );
    }

    public function debug($message, $type = 'notice') {
        if ($this->debug) {
            wc_add_notice($message, $type);
        }
    }

    public function admin_options() {
       
        // Show settings
        parent::admin_options();
    }

    public function init_form_fields() {
		if(isset($_GET['page']) && $_GET['page'] === 'wc-settings')
		{
			$this->form_fields = array(
										'ma_pu_tab_settings' => array(
											'type' 			=> 'ma_pu_tab'
											),
									 
									);
		}
    }

    public function generate_ma_pu_tab_html() {

        $tab = (!empty($_GET['subtab'])) ? esc_attr($_GET['subtab']) : 'general';

                echo '
                <div class="wrap">
                    <style>
                        .woocommerce-help-tip{color:darkgray !important;}
                        .woocommerce-save-button{display:none !important;}
                        <style>
                        .woocommerce-help-tip {
                            position: relative;
                            display: inline-block;
                            border-bottom: 1px dotted black;
                        }

                        .woocommerce-help-tip .tooltiptext {
                            visibility: hidden;
                            width: 120px;
                            background-color: black;
                            color: #fff;
                            text-align: center;
                            border-radius: 6px;
                            padding: 5px 0;

                            /* Position the tooltip */
                            position: absolute;
                            z-index: 1;
                        }

                        .woocommerce-help-tip:hover .tooltiptext {
                            visibility: visible;
                        }
                        </style>
                    </style>
                    <hr class="wp-header-end">';
                $this->ma_get_tabs_name_url($tab);
                switch ($tab) {
                    case "general":
                        echo '<div class="table-box table-box-main" id="general_section" style="margin-top: 0px;
    border: 1px solid #ccc;border-top: unset !important;padding: 5px;">';
                        require_once('settings/general_settings.php');
                        echo '</div>';
                        break;
                }
                echo '
                </div>';


       }
       public function ma_get_tabs_name_url($current = 'general')
       {
        $tabs = array(
                    'general' => __("General", 'ma-shipping-pu'),
               //     'licence' => __("License", 'ma-shipping-pu'),
                //    'more' => __("Our More Plugins", 'ma-shipping-pu')
                );
                $html = '<h2 class="nav-tab-wrapper">';
                foreach ($tabs as $tab => $name) {
                    $class = ($tab == $current) ? 'nav-tab-active' : '';
                    $style = ($tab == $current) ? 'border-bottom: 1px solid transparent !important;' : '';
                    $html .= '<a style="text-decoration:none !important;' . $style . '" class="nav-tab ' . $class . '" href="'. wp_nonce_url('?page='.ma_pu_get_deprecated_url().'&tab=shipping&section=ma_purolator_shipping&subtab=' . $tab) . '">' . $name . '</a>';
                }
                $html .= '</h2>';
                echo $html;
            }
    
    public function validate_services_field($key) {
        $services = array();
        $posted_services = $_POST['pu_services'];

        foreach ($posted_services as $code => $settings) {
            $services[$code] = array(
                'name' => wc_clean($settings['name']),
                'order' => wc_clean($settings['order']),
                'enabled' => isset($settings['enabled']) ? true : false,
                'adjustment' => wc_clean($settings['adjustment']),
                'adjustment_percent' => str_replace('%', '', wc_clean($settings['adjustment_percent']))
            );
        }

        return $services;
    }

    public function get_pu_single_array($package) {
        switch ($this->packing_method) {
            case 'weight_based' :
                return $this->weight_based_shipping($package);
                break;
			case 'per_item' :
            default :
                return $this->per_item_shipping($package);
                break;
        }
    }

    /**
     * weight_based_shipping function.
     *
     * @access private
     * @param mixed $package
     * @return void
    **/
    private function weight_based_shipping($package) {
        global $woocommerce;
        if ( ! class_exists( 'WeightPack' ) ) {
            include_once 'weight_pack/class-ma-weight-packing.php';
        }
        $weight_pack = new WeightPack('pack_descending');
        $weight_pack->set_max_weight($this->box_max_weight);
        
        $package_total_weight = 0;
        $insured_value = 0;
        
        
        $ctr = 0;
        foreach ($package['contents'] as $item_id => $values) {
            $ctr++;
            
            $skip_product = apply_filters('ma_shipping_skip_product_from_pu_label',false, $values, $package['contents']);
            if($skip_product){
                continue;
            }
            
            if (!($values['quantity'] > 0 && $values['data']->needs_shipping())) {
                $this->debug(sprintf(__('Product #%d is virtual. Skipping.', 'ma-shipping-pu'), $ctr));
                continue;
            }

            if (!$values['data']->get_weight()) {
                $this->debug(sprintf(__('Product #%d is missing weight.', 'ma-shipping-pu'), $ctr), 'error');
                return;
            }
            $weight_pack->add_item(wc_get_weight( $values['data']->get_weight(), $this->weight_unit ), $values['data'], $values['quantity']);
        }
        
        $pack   =   $weight_pack->pack_items();  
        $errors =   $pack->get_errors();
        if( !empty($errors) ){
            //do nothing
            return;
        } else {
            $boxes    =   $pack->get_packed_boxes();
            $unpacked_items =   $pack->get_unpacked_items();
            
            $insured_value        =   0;
            
            $packages      =   array_merge( $boxes, $unpacked_items ); // merge items if unpacked are allowed
            $package_count  =   sizeof($packages);
            // get all items to pass if item info in box is not distinguished
            $packable_items =   $weight_pack->get_packable_items();
            $all_items    =   array();
            if(is_array($packable_items)){
                foreach($packable_items as $packable_item){
                    $all_items[]    =   $packable_item['data'];
                }
            }
            //pre($packable_items);
            $order_total = '';
            if(isset($this->order)){
                $order_total    =   $this->order->get_total();
            }
            
            $to_ship  = array();
            $group_id = 1;
            foreach($packages as $package){//pre($package);
            
                $packed_products = array();
                if(($package_count  ==  1) && isset($order_total)){
                    $insured_value  =   $order_total;
                }else{
                    $insured_value  =   0;
                    if(!empty($package['items'])){
                        foreach($package['items'] as $item){                        
                            $insured_value        =   $insured_value+$item->get_price();
                            
                        }
                    }else{
                        if( isset($order_total) && $package_count){
                            $insured_value  =   $order_total/$package_count;
                        }
                    }
                }
                $packed_products    =   isset($package['items']) ? $package['items'] : $all_items;
                // Creating package request
                $package_total_weight   =   $package['weight'];
                
                $insurance_array = array(
                    'Amount' => round($values['data']->get_price()),
                    'Currency' => get_woocommerce_currency()
                );
                $group = array(
                    'GroupNumber' => $group_id,
                    'GroupPackageCount' => 1,
                    'Weight' => array(
                        'Value' => round($package_total_weight, 3),
                        'Units' => $this->weight_unit
                    ),
                    'packed_products' => $packed_products,
                );
                $group['InsuredValue'] = $insurance_array;
                $group['packtype'] = isset($this->settings['shp_pack_type'])?$this->settings['shp_pack_type'] : 'OD';
                
                $to_ship[] = $group;
                $group_id++;
            }
        }
        return $to_ship;
    }


    private function per_item_shipping($package) {
        $to_ship = array();
        $group_id = 1;

        // Get weight of order
        foreach ($package['contents'] as $item_id => $values) {

            if (!$values['data']->needs_shipping()) {
                $this->debug(sprintf(__('Product # is virtual. Skipping.', 'ma-shipping-pu'), $item_id), 'error');
                continue;
            }

            $skip_product = apply_filters('ma_shipping_skip_product_from_pu_rate',false, $values, $package['contents']);
            if($skip_product){
                continue;
            }

            if (!$values['data']->get_weight()) {
                $this->debug(sprintf(__('Product # is missing weight. Aborting.', 'ma-shipping-pu'), $item_id), 'error');
                return;
            }

            $group = array();
            $insurance_array = array(
                'Amount' => round($values['data']->get_price()),
                'Currency' => get_woocommerce_currency()
            );
            //if ($this->settings['insure_contents'] == 'yes' && !empty($this->conversion_rate)) {
                  //  $crate = 1 / $this->conversion_rate;
                    //$insurance_array['Amount']      = round($values['data']->get_price() * $crate, 2);
                   // $insurance_array['Currency']    = $this->settings['pu_currency_type'];
                //}
            $group = array(
                'GroupNumber' => $group_id,
                'GroupPackageCount' => 1,
                'Weight' => array(
                    'Value' => round(wc_get_weight($values['data']->get_weight(), $this->weight_unit), 3),
                    'Units' => $this->weight_unit
                ),
                'packed_products' => array($values['data'])
            );

            if ( ma_pu_get_product_length( $values['data'] ) && ma_pu_get_product_height( $values['data'] ) && ma_pu_get_product_width( $values['data'] )) {

                $dimensions = array( ma_pu_get_product_length( $values['data'] ), ma_pu_get_product_width( $values['data'] ), ma_pu_get_product_height( $values['data'] ));

                sort($dimensions);

                $group['Dimensions'] = array(
                    'Length' => max(1, round(wc_get_dimension($dimensions[2], $this->dimension_unit), 0)),
                    'Width' => max(1, round(wc_get_dimension($dimensions[1], $this->dimension_unit), 0)),
                    'Height' => max(1, round(wc_get_dimension($dimensions[0], $this->dimension_unit), 0)),
                    'Units' => $this->dimension_unit
                );
            }
            $group['packtype'] = isset($this->settings['shp_pack_type'])?$this->settings['shp_pack_type'] : 'BOX';
            $group['InsuredValue'] = $insurance_array;

            for ($i = 0; $i < $values['quantity']; $i++)
                $to_ship[] = $group;

            $group_id++;
        }

        return $to_ship;
    }

    private function get_pu_request($pu_pakages, $package) {
        // Time is modified to avoid date diff with server.
		$destination_postcode = strtoupper($package['destination']['postcode']);
        $destination_state = strtoupper($package['destination']['state']);
		$total_weight = $this->ma_get_package_piece($pu_pakages);
		$destination_city = strtoupper($package['destination']['city']);
	
		$request_array = array(
						'BillingAccountNumber' => $this->account_number,
						'SenderPostalCode' => $this->origin,
						'ReceiverAddress' => array(
									'City' => $destination_city,
									'Province' => $destination_state,
									'Country' => $package['destination']['country'],
									'PostalCode' => $destination_postcode,
								),
						'PackageType' => $this->pack_pu_type,
						'TotalWeight' => array(
										'Value' => $total_weight,
										'WeightUnit' => $this->quoteapi_weight_unit,
						),
		);

        $request_array = apply_filters('ma_purolater_rate_request', $request_array, $package);
        return $request_array;
    }

    private function ma_get_package_piece($pu_pakages) {
        $total_weight = 0;
        if ($pu_pakages) {
            foreach ($pu_pakages as $key => $parcel) {
                $total_weight = $total_weight + $parcel['Weight']['Value'];
            }
        }
        return $total_weight;
    }

    private function ma_get_postcode_city($country, $city, $postcode) {
        $no_postcode_country = array('AE', 'AF', 'AG', 'AI', 'AL', 'AN', 'AO', 'AW', 'BB', 'BF', 'BH', 'BI', 'BJ', 'BM', 'BO', 'BS', 'BT', 'BW', 'BZ', 'CD', 'CF', 'CG', 'CI', 'CK',
            'CL', 'CM', 'CO', 'CR', 'CV', 'DJ', 'DM', 'DO', 'EC', 'EG', 'ER', 'ET', 'FJ', 'FK', 'GA', 'GD', 'GH', 'GI', 'GM', 'GN', 'GQ', 'GT', 'GW', 'GY', 'HK', 'HN', 'HT', 'IE', 'IQ', 'IR',
            'JM', 'JO', 'KE', 'KH', 'KI', 'KM', 'KN', 'KP', 'KW', 'KY', 'LA', 'LB', 'LC', 'LK', 'LR', 'LS', 'LY', 'ML', 'MM', 'MO', 'MR', 'MS', 'MT', 'MU', 'MW', 'MZ', 'NA', 'NE', 'NG', 'NI',
            'NP', 'NR', 'NU', 'OM', 'PA', 'PE', 'PF', 'PY', 'QA', 'RW', 'SA', 'SB', 'SC', 'SD', 'SL', 'SN', 'SO', 'SR', 'SS', 'ST', 'SV', 'SY', 'TC', 'TD', 'TG', 'TL', 'TO', 'TT', 'TV', 'TZ',
            'UG', 'UY', 'VC', 'VE', 'VG', 'VN', 'VU', 'WS', 'XA', 'XB', 'XC', 'XE', 'XL', 'XM', 'XN', 'XS', 'YE', 'ZM', 'ZW');

        $postcode_city = !in_array( $country, $no_postcode_country ) ? $postcode_city = "<Postalcode>{$postcode}</Postalcode>" : '';
        if( !empty($city) ){
            $postcode_city .= "<City>{$city}</City>";
        }
        return $postcode_city;
    }

	
	function createPWSSOAPClient()
	{
	  $client = new SoapClient( MA_PU_ROOT_PATH."/api/production/EstimatingService.wsdl", 
								array	(
										'trace'			=>	true,
										'location'	=>	$this->location,
										'uri'				=>	"http://purolator.com/pws/datatypes/v1",
										'login'			=>	$this->site_id,
										'password'	=>	$this->site_password,
										'exceptions' => 0,
										
									  )
							  );
		$headers[] = new SoapHeader ( 'http://purolator.com/pws/datatypes/v1', 
									'RequestContext', 
									array (
											'Version'           =>  '1.4',
											'Language'          =>  'en',
											'GroupID'           =>  'xxx',
											'RequestReference'  =>  'MoreAddons',
											'UserToken'         =>  $this->usr_token,
										  )
								  ); 
	  //Apply the SOAP Header to your client                            
	  $client->__setSoapHeaders($headers);
	  return $client;
	}
     public function calculate_shipping( $package=array() ) {
        // Clear rates
        $this->found_rates = array();

        // Debugging
        $this->debug(__('pu debug mode is on - to hide these messages, turn debug mode off in the settings.', 'ma-shipping-pu'));
		// Packages returned ahould be an array regardless of filter added or not 
		$packages = apply_filters('ma_filter_package_address', array($package) , $this->ship_from_address);
		
		$pu_requests	=	array();
			foreach($packages as $package){
				$pu_single_pack		=	$this->get_pu_single_array( $package );
				$pu_pu_req		=	$this->get_pu_request( $pu_single_pack, $package );
				$pu_requests[]	=	$pu_pu_req;
				
		}
		$client = $this->createPWSSOAPClient();
		if ($pu_requests) {
			try {
			foreach ( $pu_requests as $key => $request ) {
				$response = $client->__SoapCall('GetQuickEstimate',array($request));
				if($this->debug)
				{
					$this->debug("<pre>Request : ".htmlspecialchars($client->__getLastRequest().'</pre>'));
					$this->debug("<pre>Response: ".htmlspecialchars($client->__getLastResponse().'</pre>'));
					
				}
				$this->process_result($response, $request);
			
			}            
			} catch (Exception $e) {
				$this->debug(print_r($e, true), 'error');
				return false;
			}
		
        }

        // Ensure rates were found for all packages
        $packages_to_quote_count = sizeof($pu_requests);
        
        if ($this->found_rates) {
            foreach ($this->found_rates as $key => $value) {
                if ($value['packages'] < $packages_to_quote_count) {
                    unset($this->found_rates[$key]);
                }
            }
        }
        // Rate conversion
        if ($this->conversion_rate) {
            foreach ($this->found_rates as $key => $rate) {
                $this->found_rates[$key]['cost'] = $rate['cost'] * $this->conversion_rate;
            }
        }
  
		$this->add_found_rates();
       
    }


    private function ma_get_cost_based_on_currency($qtdsinadcur, $default_charge) {
        
		if (!empty($qtdsinadcur)) {
            foreach ($qtdsinadcur as $multiple_currencies) {
                if ((string) $multiple_currencies->CurrencyCode == get_woocommerce_currency() && !empty($multiple_currencies->TotalAmount))
                    return $multiple_currencies->TotalAmount;
            }
        }
        return $default_charge;
    }

    private function process_result($result = '',$defined_req = array()) {
        
		if ( is_wp_error( $result ) ) {
            $error_message = $result->get_error_message();
            $this->debug('Purolator WP ERROR: <a href="#" class="debug_reveal">Reveal</a><pre class="debug_info" style="background:#EEE;border:1px solid #DDD;padding:5px;">' . print_r(htmlspecialchars($error_message), true) . '</pre>');
        }
		else
		{
			libxml_use_internal_errors(true);
		}
		if(!empty($result))
        {
           
		   if(isset($result->ShipmentEstimates))
		   {
			   foreach($result->ShipmentEstimates as $reutn_service)
			   {
				   foreach($reutn_service as $service)
				   {
					   
					$ServiceID = isset($service->ServiceID) ? strval((string) $service->ServiceID) : '';
					$ExpectedDeliveryDate = isset($service->ExpectedDeliveryDate) ? strval((string) $service->ExpectedDeliveryDate) : '';
					$EstimatedTransitDays = isset($service->EstimatedTransitDays) ? strval((string) $service->EstimatedTransitDays) : '';
					$TotalPrice = isset($service->TotalPrice) ? strval((string) $service->TotalPrice) : 0;
			        $rate_id = $this->id . ':' . $ServiceID;
                    
					if($TotalPrice > 0) $this->prepare_rate($rate_id,$ServiceID,$TotalPrice,$ExpectedDeliveryDate,$EstimatedTransitDays);
               
				   }
					
                
			   }
		   }
		}
		
		
    }

    private function prepare_rate($rate_id,$ServiceID,$TotalPrice,$ExpectedDeliveryDate,$EstimatedTransitDays) {

        // Name adjustment
		if (!empty($this->custom_services[$ServiceID]['name'])) {
            $rate_name = $this->custom_services[$ServiceID]['name'];
        }
		else
		{
			$rate_name = $ServiceID;
		}

        // Cost adjustment %
        if (!empty($this->custom_services[$ServiceID]['adjustment_percent'])) {
            $TotalPrice = $TotalPrice + ( $TotalPrice * ( floatval($this->custom_services[$ServiceID]['adjustment_percent']) / 100 ) );
        }
        // Cost adjustment
        if (!empty($this->custom_services[$ServiceID]['adjustment'])) {
            $TotalPrice = $TotalPrice + floatval($this->custom_services[$ServiceID]['adjustment']);
        }

        // Enabled check
        if (isset($this->custom_services[$ServiceID]) && empty($this->custom_services[$ServiceID]['enabled'])) {
            return;
        }

        // Merging
        if (isset($this->found_rates[$rate_id])) {
            $TotalPrice = $TotalPrice + $this->found_rates[$rate_id]['cost'];
            $packages = 1 + $this->found_rates[$rate_id]['packages'];
        } else {
            $packages = 1;
        }

        $this->found_rates[$rate_id] = array(
            'id' => $rate_id,
            'label' => $rate_name,
            'cost' => $TotalPrice,
			'packages' => $packages,
            'meta_data' => array('Delivery'=>$ExpectedDeliveryDate,'Transit'=>$EstimatedTransitDays)
        );
    }

    public function add_found_rates() {
        if ($this->found_rates) {
			//foreach ($this->found_rates as $key => $rate) {
				//$this->add_rate($rate);
			//}
          
		  if ($this->offer_rates == 'all') {

                uasort($this->found_rates, array($this, 'sort_rates'));

                foreach ($this->found_rates as $key => $rate) {
                    $this->add_rate($rate);
                }
            } else {
                $cheapest_rate = '';

                foreach ($this->found_rates as $key => $rate) {
                    if (!$cheapest_rate || $cheapest_rate['cost'] > $rate['cost']) {
                        $cheapest_rate = $rate;
                    }
                }
				if(isset($this->title_enable) && $this->title_enable == 'yes')
				{
					$cheapest_rate['label'] = $this->title;
				}
                $this->add_rate($cheapest_rate);
            }
	    }
    }

    public function sort_rates($a, $b) {
            return 0;
    }
	
}