<?php 
if (!defined('ABSPATH')) {
    exit;
}
$this->init_settings(); 
global $woocommerce;
$wc_main_settings = array();
$packing_type = array('CustomerPackaging' => 'Customer Packaging','ExpressBox' => 'Express Box','ExpressPack' => 'Express Pack','ExpressEnvelope' => 'Express Envelope');
if(isset($_POST['ma_save_changs']))
{
	$wc_main_settings = get_option('woocommerce_'.MA_PU_ID.'_settings');	
	$my_account_number = (isset($wc_main_settings['account_number'])) ? $wc_main_settings['account_number'] : '';
	$my_site_id = (isset($wc_main_settings['site_id'])) ? $wc_main_settings['site_id'] : '';
	$my_site_pwd = (isset($wc_main_settings['site_password'])) ? $wc_main_settings['site_password'] : '';

	$wc_main_settings['production'] = (isset($_POST['ma_purolator_shipping_production']) && $_POST['ma_purolator_shipping_production'] == 'yes') ? 'yes' : 'no';

	$wc_main_settings['account_number'] = (isset($_POST['ma_purolator_shipping_ac_num'])) ? sanitize_text_field($_POST['ma_purolator_shipping_ac_num']) : $my_account_number;
	$wc_main_settings['activation_key'] = (isset($_POST['ma_purolator_shipping_activation_key'])) ? sanitize_text_field($_POST['ma_purolator_shipping_activation_key']) : '';
	$wc_main_settings['site_id'] = (isset($_POST['ma_purolator_shipping_site_id'])) ? sanitize_text_field($_POST['ma_purolator_shipping_site_id']) : $my_site_id;
	$wc_main_settings['site_password'] = (isset($_POST['ma_purolator_shipping_site_pwd'])) ? sanitize_text_field($_POST['ma_purolator_shipping_site_pwd']) : $my_site_pwd;
	$wc_main_settings['enabled'] = (isset($_POST['ma_purolator_shipping_rates'])) ? 'yes' : 'no';
	$wc_main_settings['enabled_label'] = (isset($_POST['ma_purolator_shipping_enabled_label'])) ? 'yes' : '';
	$wc_main_settings['title_enable'] = (isset($_POST['ma_purolator_shipping_title_enable'])) ? 'yes' : '';
	$wc_main_settings['offer_rates'] = (isset($_POST['ma_purolator_shipping_offer_rates'])) ? 'cheapest' : 'all';
	$wc_main_settings['est_del_date'] = (isset($_POST['ma_purolator_shipping_est_del_date'])) ? 'yes' : '';
	$wc_main_settings['debug'] = (isset($_POST['ma_purolator_shipping_debug'])) ? 'yes' : '';
	$wc_main_settings['shipper_person_name'] = (isset($_POST['ma_purolator_shipping_shipper_person_name'])) ? sanitize_text_field($_POST['ma_purolator_shipping_shipper_person_name']) : '';
	$wc_main_settings['title'] = (isset($_POST['ma_purolator_shipping_title'])) ? sanitize_text_field($_POST['ma_purolator_shipping_title']) : '';
	$wc_main_settings['shipper_company_name'] = (isset($_POST['ma_purolator_shipping_shipper_company_name'])) ? sanitize_text_field($_POST['ma_purolator_shipping_shipper_company_name']) : '';
	$wc_main_settings['shipper_phone_number'] = (isset($_POST['ma_purolator_shipping_shipper_phone_number'])) ? sanitize_text_field($_POST['ma_purolator_shipping_shipper_phone_number']) : '';
	$wc_main_settings['shipper_email'] = (isset($_POST['ma_purolator_shipping_shipper_email'])) ? sanitize_text_field($_POST['ma_purolator_shipping_shipper_email']) : '';
	$wc_main_settings['freight_shipper_street'] = (isset($_POST['ma_purolator_shipping_freight_shipper_street'])) ? sanitize_text_field($_POST['ma_purolator_shipping_freight_shipper_street']) : '';
	$wc_main_settings['shipper_street_2'] = (isset($_POST['ma_purolator_shipping_shipper_street_2'])) ? sanitize_text_field($_POST['ma_purolator_shipping_shipper_street_2']) : '';
	$wc_main_settings['freight_shipper_city'] = (isset($_POST['ma_purolator_shipping_freight_shipper_city'])) ? sanitize_text_field($_POST['ma_purolator_shipping_freight_shipper_city']) : '';
	$wc_main_settings['freight_shipper_state'] = (isset($_POST['ma_purolator_shipping_freight_shipper_state'])) ? sanitize_text_field($_POST['ma_purolator_shipping_freight_shipper_state']) : '';
	$wc_main_settings['pack_pu_type'] = (isset($_POST['ma_purolator_shipping_pack_pu_type']) && !empty($_POST['ma_purolator_shipping_pack_pu_type'])) ? sanitize_text_field($_POST['ma_purolator_shipping_pack_pu_type']) : 'CustomerPackaging';
	$wc_main_settings['origin'] = (isset($_POST['ma_purolator_shipping_origin'])) ? ($_POST['ma_purolator_shipping_origin']) : '';
	$wc_main_settings['base_country'] = $_POST['ma_purolator_shipping_base_country'];
	$wc_main_settings['conversion_rate'] = isset($_POST['ma_purolator_shipping_conversion_rate']) ? sanitize_text_field($_POST['ma_purolator_shipping_conversion_rate']) : '';
	$wc_main_settings['packing_method'] = isset($_POST['ma_purolator_shipping_packing_method']) ? $_POST['ma_purolator_shipping_packing_method'] : 'per_item';
	$wc_main_settings['box_max_weight'] = isset($_POST['ma_purolator_shipping_box_max_weight']) ? $_POST['ma_purolator_shipping_box_max_weight'] : '';
	$wc_main_settings['weight_unit'] = isset($_POST['ma_purolator_shipping_weight_unit']) ? $_POST['ma_purolator_shipping_weight_unit'] : 'kg';
	$wc_main_settings['services'] = $_POST['pu_services'];

	update_option('woocommerce_'.MA_PU_ID.'_settings',$wc_main_settings);
}

$general_settings = get_option('woocommerce_'.MA_PU_ID.'_settings');
$general_settings = empty($general_settings) ? array() : $general_settings;
$this->custom_services = isset($general_settings['services']) ? $general_settings['services'] : array();
?>

<table>
<?php
if(!class_exists('SOAPClient'))
{
	echo "<tr><td colspan='2'>";
	echo( '<div style="background: #fff;box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);font-size: 20px;line-height: 1em;  outline: 0;display: block;height: auto;    min-height: 100%;width: 100%;position: relative;-webkit-font-smoothing: subpixel-antialiased;border-left: 4px solid #dc3232;"><p style="padding:10px;"><b>Missing:</b> SOAPClient. Enbale SOAPClient to Send and Receive Rate Requests.</p></div>' );
	echo "</td></tr>";
}

?>
	<tr valign="top">
		<td style="width:40%;font-weight:800;">
			<label for="ma_purolator_shipping_production"><?php _e('Account Info','ma_pu_ship') ?> </label> 
		</td>
		<td scope="row" class="titledesc" style="display: block;margin-bottom: 20px;margin-top: 3px;">
			<fieldset style="padding:3px;">
				<?php if(isset($general_settings['production']) && $general_settings['production'] == 'yes')
				{ ?>
				<input class="input-text regular-input " type="radio" name="ma_purolator_shipping_production"  id="ma_purolator_shipping_production"  value="no" placeholder=""> <?php _e('Test Mode','ma_pu_ship') ?>
				<input class="input-text regular-input " type="radio"  name="ma_purolator_shipping_production" checked="true" id="ma_purolator_shipping_production"  value="yes" placeholder=""> <?php _e('Live Mode','ma_pu_ship') ?>
				<?php }else{ ?>
				<input class="input-text regular-input " type="radio" name="ma_purolator_shipping_production" checked="true" id="ma_purolator_shipping_production"  value="no" placeholder=""> <?php _e('Test Mode','ma_pu_ship') ?>
				<input class="input-text regular-input " type="radio" name="ma_purolator_shipping_production" id="ma_purolator_shipping_production"  value="yes" placeholder=""> <?php _e('Live Mode','ma_pu_ship') ?>
				<?php } ?>
				<br>
			</fieldset>
			<fieldset style="padding:3px;">
				<input class="input-text regular-input " type="text" name="ma_purolator_shipping_ac_num" id="ma_purolator_shipping_ac_num"   value="<?php echo (isset($general_settings['account_number'])) ? $general_settings['account_number'] : '130000279'; ?>" placeholder="130000279"> <label for="ma_purolator_shipping_ac_num"><?php _e('Account Number','ma_pu_ship') ?></label> <span class="woocommerce-help-tip" data-tip="<?php _e('Enter your DHL online account number as obtained from DHL. You can contact your DHL sales representative for this.','ma_pu_ship') ?>"></span>

			</fieldset>
			
			<fieldset style="padding:3px;">
				<input class="input-text regular-input " size="40" required type="text" name="ma_purolator_shipping_site_id" id="ma_purolator_shipping_site_id"  value="<?php echo (isset($general_settings['site_id'])) ? $general_settings['site_id'] : 'CIMGBTest'; ?>" placeholder="CIMGBTest"> 
				<label for="ma_purolator_shipping_"><?php _e('Key','ma_pu_ship') ?><font style="color:red;">*</font></label> <span class="woocommerce-help-tip" data-tip="<?php _e('You can get the SITE ID from the DHL integration team.','ma_pu_ship') ?>"></span>	
			</fieldset>
			<fieldset style="padding:3px;">
				<input class="input-text regular-input " size="40" required type="Password" name="ma_purolator_shipping_site_pwd" id="ma_purolator_shipping_site_pwd"  value="<?php echo (isset($general_settings['site_password'])) ? $general_settings['site_password'] : 'DLUntOcJma'; ?>" placeholder="**************"> 
				<label for="ma_purolator_shipping_site_pwd"><?php _e('Password','ma_pu_ship') ?><font style="color:red;">*</font></label>
				<span class="woocommerce-help-tip" data-tip="<?php _e('You can get the PASSWORD from the Purolator integration team.','ma_pu_ship') ?>"></span>				
			</fieldset>
			
			<fieldset style="padding:3px;">
				<input class="input-text regular-input" size="40" type="text" name="ma_purolator_shipping_activation_key" id="ma_purolator_shipping_activation_key"  value="<?php echo (isset($general_settings['activation_key'])) ? $general_settings['activation_key'] : ''; ?>" placeholder=""> <label for="ma_purolator_shipping_activation_key"><?php _e('User Token / Activation Key ','ma_pu_ship') ?></label> <span class="woocommerce-help-tip" data-tip="<?php _e('Enter your DHL online account number as obtained from DHL. You can contact your DHL sales representative for this.','ma_pu_ship') ?>"></span>

			</fieldset>
		</td>
	</tr>
	<tr valign="top">
		<td style="width:40%;font-weight:800;">
			<label for="ma_purolator_shipping_rates"><?php _e('Enable/Disable','ma_pu_ship') ?></label>
		</td>
		<td scope="row" class="titledesc" style="display: block;margin-bottom: 20px;margin-top: 3px;">
			<b><u>Real Time Rates</u></b>
			<fieldset style="padding:3px;">

				<input class="input-text regular-input " type="checkbox" name="ma_purolator_shipping_rates" id="ma_purolator_shipping_rates" style="" value="yes" <?php echo (!isset($general_settings['enabled']) || isset($general_settings['enabled']) && $general_settings['enabled'] ==='yes') ? 'checked' : ''; ?> placeholder=""> <?php _e('Enable Real time Rates','ma_pu_ship') ?> <span class="woocommerce-help-tip" data-tip="<?php _e('Enable this to fetch the rates from DHL in cart/checkout page.','ma_pu_ship') ?>"></span>
			</fieldset>
			<fieldset style="padding:3px;">

				<input class="input-text regular-input " type="checkbox" name="ma_purolator_shipping_title_enable" id="ma_purolator_shipping_title_enable" style="" value="yes" <?php echo (!isset($general_settings['title_enable']) || isset($general_settings['title_enable']) && $general_settings['enabled_label'] ==='yes') ? 'checked' : ''; ?> placeholder=""> <?php _e('Enable Method Title','ma_pu_ship') ?> <span class="woocommerce-help-tip" data-tip="<?php _e('Enable this to fetch the rates from DHL in cart/checkout page.','ma_pu_ship') ?>"></span>
			</fieldset>
			<fieldset style="padding:3px;" id="ma_en_method_title">
				<label for="ma_purolator_shipping_title"><?php _e('Method Title','ma_pu_ship') ?></label> <span class="woocommerce-help-tip" data-tip="<?php _e('Provide the Method title which will be reflected as the service name if "Show cheapest rates only" is enabled.','ma_pu_ship') ?>"></span>
				<br/><input class="input-text regular-input " type="text" name="ma_purolator_shipping_title" id="ma_purolator_shipping_title" style="" value="<?php echo (isset($general_settings['title'])) ? $general_settings['title'] : __( 'Purolator', 'ma_pu_ship' ); ?>" placeholder=""> 
			</fieldset>
			
			<fieldset style="padding:3px;">
				<input class="input-text regular-input " type="checkbox" name="ma_purolator_shipping_offer_rates" id="ma_purolator_shipping_offer_rates" style="" value="yes" <?php echo (isset($general_settings['offer_rates']) && $general_settings['offer_rates'] ==='cheapest') ? 'checked' : ''; ?> placeholder="">  <?php _e('Show Cheapest Rates Only','ma_pu_ship') ?> <span class="woocommerce-help-tip" data-tip="<?php _e('On enabling this, the cheapest rate will be shown in the cart/checkout page.','ma_pu_ship') ?>"></span>
			</fieldset>
			
			<fieldset style="padding:3px;">
				<input class="input-text regular-input " type="checkbox" name="ma_purolator_shipping_est_del_date" id="ma_purolator_shipping_est_del_date" style="" value="yes" <?php echo (isset($general_settings['est_del_date']) && $general_settings['est_del_date'] ==='yes') ? 'checked' : ''; ?> placeholder="">  <?php _e('Show Estimated Delivery Date','ma_pu_ship') ?> <span class="woocommerce-help-tip" data-tip="<?php _e('On enabling this, the cheapest rate will be shown in the cart/checkout page.','ma_pu_ship') ?>"></span>
			</fieldset>
			<?php $default_currency = get_woocommerce_currency(); ?>
			<b><u>Development (Requst/Response)</u></b>
			<fieldset style="padding:3px;">
				<input class="input-text regular-input " type="checkbox" name="ma_purolator_shipping_debug" id="ma_purolator_shipping_debug" style="" value="yes" <?php echo (isset($general_settings['debug']) && $general_settings['debug'] ==='yes') ? 'checked' : ''; ?> placeholder=""> <?php _e('Enable Developer Mode','ma_pu_ship') ?> <span class="woocommerce-help-tip" data-tip="<?php _e('Enable this option to troubleshoot the plugin. On enabling this, request and response information will be shown in the cart/checkout page.','ma_pu_ship') ?>"></span>
			</fieldset>
			
		</td>
	</tr>
	<tr valign="top">
		<td style="width:40%;font-weight:800;">
			<label for="ma_purolator_shipping_"><?php _e('Default Currency','ma_pu_ship') ?></label>
		</td>
		<td scope="row" class="titledesc" style="display: block;margin-bottom: 20px;margin-top: 3px;">
			<fieldset style="padding:3px;">
				<?php $selected_currency = 'CAD'?>
				<label for="ma_purolator_shipping_"><?php echo '<b>'.$selected_currency.' ('. get_woocommerce_currency_symbol($selected_currency).')</b>'; ?></label> <span class="woocommerce-help-tip" data-tip="<?php _e('This field picks the default currency of the country provided in Shipper Address Section.','ma_pu_ship') ?>"></span><br/>
			</fieldset>
			<?php 
			if($selected_currency != $default_currency)
			{
				?>
				<fieldset style="padding:3px;">
				<label for="ma_purolator_shipping_conversion_rate"><?php _e('Converstion Rate','ma_pu_ship') ?></label> <span class="woocommerce-help-tip" data-tip="Use this field to set the conversion rate of the  DHL currency <?php echo $selected_currency; ?> to the Store’s currency <?php echo $default_currency; ?>. "></span> <br/>	 
				<input class="input-text regular-input " type="number" min="0" step="0.00001" name="ma_purolator_shipping_conversion_rate" id="ma_purolator_shipping_conversion_rate" style="" value="<?php echo (isset($general_settings['conversion_rate'])) ? $general_settings['conversion_rate'] : ''; ?>" placeholder=""><b> <?php echo $default_currency; ?></b>
				</fieldset>
				<?php
			}
			 ?>
		</td>
	</tr>
		<tr valign="top" >
		<td style="width:30%;font-weight:800;">
			<label for="ma_purolator_shipping_"><?php _e('Service Availability','ma_pu_ship') ?></label>
		</td>
		<td scope="row" class="titledesc" style="display: block;margin-bottom: 20px;margin-top: 3px;">
			<fieldset style="padding:3px;">
				<?php if(isset($general_settings['availability']) && $general_settings['availability'] ==='specific')
				{ ?>
				<input class="input-text regular-input " type="radio" name="ma_purolator_shipping_availability"  id="ma_purolator_shipping_availability1" value="all" placeholder=""> <?php _e('Supports All Countries','ma_pu_ship') ?>
				<input class="input-text regular-input " type="radio"  name="ma_purolator_shipping_availability" checked="true" id="ma_purolator_shipping_availability2"  value="specific" placeholder=""> Supports <?php _e('Specific Countries','ma_pu_ship') ?>
				<?php }else{ ?>
				<input class="input-text regular-input " type="radio" name="ma_purolator_shipping_availability" checked="true" id="ma_purolator_shipping_availability1"  value="all" placeholder=""> <?php _e('Supports All Countries','ma_pu_ship') ?>
				<input class="input-text regular-input " type="radio" name="ma_purolator_shipping_availability" id="ma_purolator_shipping_availability2"  value="specific" placeholder=""> <?php _e('Supports Specific Countries','ma_pu_ship') ?>
				<?php } ?>
			</fieldset>
			<fieldset style="padding:3px;" id="pu_spacific">
				<label for="ma_purolator_shipping_countries"><?php _e('Specific Countries','ma_pu_ship') ?></label> <span class="woocommerce-help-tip" data-tip="<?php _e('You can select the shipping method to be available for all countries or selective countries.','ma_pu_ship') ?>"></span><br/>

				<select class="chosen_select" multiple="true" name="ma_purolator_shipping_countries[]" >
					<?php 
					$woocommerce_countys = $woocommerce->countries->get_countries();
					$selected_country =  (isset($general_settings['countries']) && !empty($general_settings['countries']) ) ? $general_settings['countries'] : array($woocommerce->countries->get_base_country());
	
					foreach ($woocommerce_countys as $key => $value) {
						if(in_array($key, $selected_country))
						{
							echo '<option value="'.$key.'" selected>'.$value.'</option>';
						}
						echo '<option value="'.$key.'">'.$value.'</option>';
					}
					?>
				</td>
			</fieldset>
	</tr>
	</tr>
		<tr valign="top" >
		<td style="width:30%;font-weight:800;">
			<label for="ma_purolator_shipping_"><?php _e('Packing Options','ma_pu_ship') ?></label>
		</td>
		<td scope="row" class="titledesc" style="display: block;margin-bottom: 20px;margin-top: 3px;">
			<fieldset style="padding:3px;">
			<select name="ma_purolator_shipping_pack_pu_type">
			<?php 
				$selected_type = isset($general_settings['pack_pu_type']) ? $general_settings['pack_pu_type'] : 'CustomerPackaging';
				foreach($packing_type as $key => $value)
				{
					if($selected_type == $key)
					{
						echo '<option value="'.$key.'" selected="true"> '.$value.' </option>';
					}else
					{
						echo '<option value="'.$key.'"> '.$value.' </option>';
					}
					
				}
			?>
			</select>
			</fieldset>
			<fieldset style="padding:3px;">
				<?php if(isset($general_settings['packing_method']) && $general_settings['packing_method'] == 'weight_based')
				{ ?>
				<input class="input-text regular-input " type="radio" name="ma_purolator_shipping_packing_method"  id="ma_purolator_shipping_packing1" value="per_item" placeholder=""> <?php _e('One By One Packing','ma_pu_ship') ?>
				<input class="input-text regular-input " type="radio"  name="ma_purolator_shipping_packing_method" checked="true" id="ma_purolator_shipping_packing2"  value="weight_based" placeholder=""> <?php _e('Weight Based Packing','ma_pu_ship') ?>
				<?php }else{ ?>
				<input class="input-text regular-input " type="radio" name="ma_purolator_shipping_packing_method" checked="true" id="ma_purolator_shipping_packing1"  value="per_item" placeholder=""> <?php _e('One By One Packing','ma_pu_ship') ?>
				<input class="input-text regular-input " type="radio" name="ma_purolator_shipping_packing_method" id="ma_purolator_shipping_packing2"  value="weight_based" placeholder=""> <?php _e('Weight Based packing','ma_pu_ship') ?>
				<?php } ?>
			</fieldset>
			<fieldset style="padding:3px;" id="pu_max_weight">
				 <label for="ma_purolator_shipping_box_max_weight"><?php _e('Maximum Weight / Packing','ma_pu_ship') ?></label> <span class="woocommerce-help-tip" data-tip="<?php _e('This option will allow each box to hold the maximum value provided in the field. 
Box Sizes - This section allows you to create your own box size(dimensions) and provide the box weight.','ma_pu_ship') ?>" ></span><br>
				 <input class="input-text regular-input " type="text" name="ma_purolator_shipping_box_max_weight" id="ma_purolator_shipping_box_max_weight" style="" value="<?php echo (isset($general_settings['box_max_weight'])) ? $general_settings['box_max_weight'] : ''; ?>" placeholder="">
			</fieldset>
			<fieldset style="padding:3px;">
				<?php if(isset($general_settings['weight_unit']) && $general_settings['weight_unit'] == 'kg')
				{ ?>
				<input class="input-text regular-input " type="radio" name="ma_purolator_shipping_weight_unit"  id="ma_purolator_shipping_weight_unit1" value="lb" placeholder=""> <?php _e('LB','ma_pu_ship') ?>
				<input class="input-text regular-input " type="radio"  name="ma_purolator_shipping_weight_unit" checked="true" id="ma_purolator_shipping_weight_unit2"  value="kg" placeholder=""> <?php _e('KG','ma_pu_ship') ?>
				<?php }else{ ?>
				<input class="input-text regular-input " type="radio" name="ma_purolator_shipping_weight_unit" checked="true" id="ma_purolator_shipping_weight_unit1"  value="lb" placeholder=""> <?php _e('LB','ma_pu_ship') ?>
				<input class="input-text regular-input " type="radio" name="ma_purolator_shipping_weight_unit" id="ma_purolator_shipping_weight_unit2"  value="kg" placeholder=""> <?php _e('KG','ma_pu_ship') ?>
				<?php } ?>
			</fieldset>
		
	</tr>

	<tr valign="top">
		<td style="width:40%;font-weight:800;">
			<label for="ma_purolator_shipping_"><?php _e('Shipper Address','ma_pu_ship') ?></label>
		</td>
		<td scope="row" class="titledesc" style="display: block;margin-bottom: 20px;margin-top: 3px;">

			<table>
				<tr>
					<td>
						<fieldset style="padding-left:3px;">
							<label for="ma_purolator_shipping_"><?php _e('Shipper Name','ma_pu_ship') ?><font style="color:red;">*</font></label> <span class="woocommerce-help-tip" data-tip="<?php _e('Name of the person responsible for shipping.','ma_pu_ship') ?>"></span>	<br/>
							<input class="input-text regular-input " type="text" name="ma_purolator_shipping_shipper_person_name" id="ma_purolator_shipping_shipper_person_name" style="" value="<?php echo (isset($general_settings['shipper_person_name'])) ? $general_settings['shipper_person_name'] : ''; ?>" placeholder=""> 	
						</fieldset>
					</td>
					<td>
						<fieldset style="padding-left:3px;">
							<label for="ma_purolator_shipping_"><?php _e('Company Name','ma_pu_ship') ?><font style="color:red;">*</font></label> <span class="woocommerce-help-tip" data-tip="<?php _e('Company name of the shipper.','ma_pu_ship') ?>"></span>	 <br/>
							<input class="input-text regular-input " type="text" name="ma_purolator_shipping_shipper_company_name" id="ma_purolator_shipping_shipper_company_name" style="" value="<?php echo (isset($general_settings['shipper_company_name'])) ? $general_settings['shipper_company_name'] : ''; ?>" placeholder=""> 	
						</fieldset>

					</td>
				</tr>
				<tr>
					<td>

						<fieldset style="padding-left:3px;">
							<label for="ma_purolator_shipping_"><?php _e('Phone Number','ma_pu_ship') ?><font style="color:red;">*</font></label> <span class="woocommerce-help-tip" data-tip="<?php _e('Phone number of the shipper.','ma_pu_ship') ?>"></span>	<br/>
							<input class="input-text regular-input " type="text" name="ma_purolator_shipping_shipper_phone_number" id="ma_purolator_shipping_shipper_phone_number" style="" value="<?php echo (isset($general_settings['shipper_phone_number'])) ? $general_settings['shipper_phone_number'] : ''; ?>" placeholder=""> 	
						</fieldset>
					</td>
					<td>

						<fieldset style="padding-left:3px;">
							<label for="ma_purolator_shipping_"><?php _e('Email Address','ma_pu_ship') ?></label> <span class="woocommerce-help-tip" data-tip="<?php _e('Email address of the shipper.','ma_pu_ship') ?>"></span>	<br/>
							<input class="input-text regular-input " type="text" name="ma_purolator_shipping_shipper_email" id="ma_purolator_shipping_shipper_email" style="" value="<?php echo (isset($general_settings['shipper_email'])) ? $general_settings['shipper_email'] : ''; ?>" placeholder=""> 	
						</fieldset>

					</td>
				</tr>
				<tr>
					<td>

						<fieldset style="padding-left:3px;">
							<label for="ma_purolator_shipping_"><?php _e('Street Number','ma_pu_ship') ?><font style="color:red;">*</font></label> <span class="woocommerce-help-tip" data-tip="<?php _e('Official address line 1 of the shipper.','ma_pu_ship') ?>"></span>	<br> 
							<input class="input-text regular-input " type="text" name="ma_purolator_shipping_freight_shipper_street" id="ma_purolator_shipping_freight_shipper_street" style="" value="<?php echo (isset($general_settings['freight_shipper_street'])) ? $general_settings['freight_shipper_street'] : ''; ?>" placeholder=""> 	
						</fieldset>

					</td>
					<td>

						<fieldset style="padding-left:3px;">
							<label for="ma_purolator_shipping_"><?php _e('Street Name','ma_pu_ship') ?></label> <font style="color:red;">*</font><span class="woocommerce-help-tip" data-tip="<?php _e('Official address line 2 of the shipper.','ma_pu_ship') ?>"></span>	<br/> 
							<input class="input-text regular-input " type="text" name="ma_purolator_shipping_shipper_street_2" id="ma_purolator_shipping_shipper_street_2" style="" value="<?php echo (isset($general_settings['shipper_street_2'])) ? $general_settings['shipper_street_2'] : ''; ?>" placeholder=""> 	
						</fieldset>

					</td>
				</tr>
				<tr>
					<td>
						<fieldset style="padding-left:3px;">
							<label for="ma_purolator_shipping_freight_shipper_city"><?php _e('City','ma_pu_ship') ?><font style="color:red;">*</font></label> <span class="woocommerce-help-tip" data-tip="<?php _e('City of the shipper.','ma_pu_ship') ?>"></span>	 <br/>

							<input class="input-text regular-input " type="text" name="ma_purolator_shipping_freight_shipper_city" id="ma_purolator_shipping_freight_shipper_city" style="" value="<?php echo (isset($general_settings['freight_shipper_city'])) ? $general_settings['freight_shipper_city'] : ''; ?>" placeholder="">
						</fieldset>
					</td>
					<td>
						<fieldset style="padding-left:3px;">

							<label for="ma_purolator_shipping_freight_shipper_state"><?php _e('State','ma_pu_ship') ?><font style="color:red;">*</font></label> <span class="woocommerce-help-tip" data-tip="<?php _e('State of the shipper.','ma_pu_ship') ?>"></span>	<br/>
							<input class="input-text regular-input " type="text" name="ma_purolator_shipping_freight_shipper_state" id="ma_purolator_shipping_freight_shipper_state" style="" value="<?php echo (isset($general_settings['freight_shipper_state'])) ? $general_settings['freight_shipper_state'] : ''; ?>" placeholder="">
						</fieldset>
					</td>
				</tr>
				<tr>
					<td>
					<fieldset style="padding-left:3px;">

					<label for="ma_purolator_shipping_base_country"><?php _e('Country','ma_pu_ship') ?><font style="color:red;">*</font></label> <span class="woocommerce-help-tip" data-tip="<?php _e('Country of the shipper(Used for fetching rates and label generation).','ma_pu_ship') ?>"></span><br/>

						<select style="width:75%;" name="ma_purolator_shipping_base_country" >
							<?php 
							$woocommerce_countys = $woocommerce->countries->get_countries();
							$selected_country =  (isset($general_settings['base_country']) && $general_settings['base_country'] !='') ? $general_settings['base_country'] : $woocommerce->countries->get_base_country();

							foreach ($woocommerce_countys as $key => $value) {
								if($key === $selected_country)
								{
									echo '<option value="'.$key.'" selected>'.$value.'</option>';
								}
								echo '<option value="'.$key.'">'.$value.'</option>';
							}
							?>
						</select>

					</fieldset>
				</td>
				<td>	
					<fieldset style="padding-left:3px;">

						<label for="ma_purolator_shipping_origin"><?php _e('Postal Code','ma_pu_ship') ?><font style="color:red;">*</font></label> <span class="woocommerce-help-tip" data-tip="<?php _e('Postal code of the shipper(Used for fetching rates and label generation).','ma_pu_ship') ?>"></span><br/>
						<input class="input-text regular-input " type="text" name="ma_purolator_shipping_origin" id="ma_purolator_shipping_origin" style="" value="<?php echo (isset($general_settings['origin'])) ? $general_settings['origin'] : ''; ?>" placeholder="">
					</fieldset>
				</td>
			</tr>
		</table>

	</td>
</tr>
<tr valign="top" ">
	<td colspan="2">
		<?php
		include( MA_PU_ROOT_PATH.'/includes/helper/service-table.php' );
		?>
	</td>
</tr>
<tr>
	<td colspan="2" style="text-align:right;">

		<button type="submit" class="button button-primary" name="ma_save_changs"> <?php _e('Save Changes','ma_pu_ship') ?> </button>
		
	</td>
</tr>
</table>

<script type="text/javascript">

		
		jQuery(window).load(function(){
			
			jQuery('#ma_purolator_shipping_title_enable').change(function(){
				if(jQuery('#ma_purolator_shipping_title_enable').is(':checked')) {
					jQuery('#ma_en_method_title').show();
				}else
				{
					jQuery('#ma_en_method_title').hide();
				}
			}).change();
			jQuery('#ma_purolator_shipping_availability2').change(function(){
				if(jQuery('#ma_purolator_shipping_availability2').is(':checked')) {
					jQuery('#pu_spacific').show();
				}else
				{
					jQuery('#pu_spacific').hide();
				}
			}).change();
			
			jQuery('#ma_purolator_shipping_packing2').change(function(){
				if(jQuery('#ma_purolator_shipping_packing2').is(':checked')) {
					jQuery('#pu_max_weight').show();
				}else
				{
					jQuery('#pu_max_weight').hide();
				}
			}).change();
			
		});

	</script>
