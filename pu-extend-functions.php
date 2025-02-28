<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('ma_pu_get_settings_url')){
	function ma_pu_get_settings_url(){
		return version_compare(WC()->version, '2.1', '>=') ? "wc-settings" : "woocommerce_settings";
	}
}

if (!function_exists('ma_pu_plugin_override')){
	add_action( 'plugins_loaded', 'ma_pu_plugin_override' );
	function ma_pu_plugin_override() {
		if (!function_exists('WC')){
			function WC(){
				return $GLOBALS['woocommerce'];
			}
		}
	}
}

if (!function_exists('ma_pu_get_shipping_countries')){
	function ma_pu_get_shipping_countries(){
		$woocommerce = WC();
		$shipping_countries = method_exists($woocommerce->countries, 'get_shipping_countries')
		? $woocommerce->countries->get_shipping_countries()
		: $woocommerce->countries->countries;
		return $shipping_countries;
	}
}

//Order Start
if(!function_exists('ma_pu_get_order_id'))
{
function ma_pu_get_order_id( $order ){
	global $woocommerce;
	return ( WC()->version < '2.7.0' ) ? $order->id : $order->get_id();
}
}
if(!function_exists('ma_pu_get_order_currency'))
{
function ma_pu_get_order_currency($order){
	global $woocommerce;
	return ( WC()->version < '2.7.0' ) ? $order->get_order_currency() : $order->get_currency();
}
}
if(!function_exists('ma_pu_get_order_shipping_country'))
{
function ma_pu_get_order_shipping_country( $order ){
	global $woocommerce;
	return ( WC()->version < '2.7.0' ) ? $order->shipping_country : $order->get_shipping_country();
}
}

if(!function_exists('ma_pu_get_order_shipping_first_name'))
{
function ma_pu_get_order_shipping_first_name( $order ){
	global $woocommerce;
	return ( WC()->version < '2.7.0' ) ? $order->shipping_first_name : $order->get_shipping_first_name();
}
}

if(!function_exists('ma_pu_get_order_shipping_last_name'))
{
function ma_pu_get_order_shipping_last_name( $order ){
	global $woocommerce;
	return ( WC()->version < '2.7.0' ) ? $order->shipping_last_name : $order->get_shipping_last_name();
}
}
if(!function_exists('ma_pu_get_order_shipping_company'))
{
function ma_pu_get_order_shipping_company( $order ){
	global $woocommerce;
	return ( WC()->version < '2.7.0' ) ? $order->shipping_company : $order->get_shipping_company();
}
}

if(!function_exists('ma_pu_get_order_shipping_address_1'))
{
function ma_pu_get_order_shipping_address_1( $order ){
	global $woocommerce;
	return ( WC()->version < '2.7.0' ) ? $order->shipping_address_1 : $order->get_shipping_address_1();
}
}
if(!function_exists('ma_pu_get_order_shipping_address_2'))
{
function ma_pu_get_order_shipping_address_2( $order ){
	global $woocommerce;
	return ( WC()->version < '2.7.0' ) ? $order->shipping_address_2 : $order->get_shipping_address_2();
}
}
if(!function_exists('ma_pu_get_order_shipping_city'))
{
function ma_pu_get_order_shipping_city( $order ){
	global $woocommerce;
	return ( WC()->version < '2.7.0' ) ? $order->shipping_city : $order->get_shipping_city();
}
}
if(!function_exists('ma_pu_get_order_shipping_state'))
{
function ma_pu_get_order_shipping_state( $order ){
	global $woocommerce;
	return ( WC()->version < '2.7.0' ) ? $order->shipping_state : $order->get_shipping_state();
}
}

if(!function_exists('ma_pu_get_order_shipping_postcode'))
{
function ma_pu_get_order_shipping_postcode( $order ){
	global $woocommerce;
	return ( WC()->version < '2.7.0' ) ? $order->shipping_postcode : $order->get_shipping_postcode();
}
}

if(!function_exists('ma_pu_get_order_billing_email'))
{
function ma_pu_get_order_billing_email( $order ){
	global $woocommerce;
	return ( WC()->version < '2.7.0' ) ? $order->billing_email : $order->get_billing_email();
}
}

if(!function_exists('ma_pu_get_order_billing_phone'))
{
function ma_pu_get_order_billing_phone( $order ){
	global $woocommerce;
	return ( WC()->version < '2.7.0' ) ? $order->billing_phone : $order->get_billing_phone();
}
}
//order ends

// Product Starts
if(!function_exists('ma_pu_get_product_id'))
{
function ma_pu_get_product_id( $item ){
	global $woocommerce;
	return ( WC()->version < '2.7.0' ) ? $item->id : $item->get_id();
}
}
if(!function_exists('ma_pu_get_product_length'))
{
function ma_pu_get_product_length( $item ){
	global $woocommerce;
	return ( WC()->version < '2.7.0' ) ? $item->length : $item->get_length();
}
}

if(!function_exists('ma_pu_get_product_width'))
{
function ma_pu_get_product_width( $item ){
	global $woocommerce;
	return ( WC()->version < '2.7.0' ) ? $item->width : $item->get_width();
}
}

if(!function_exists('ma_pu_get_product_height'))
{
function ma_pu_get_product_height( $item ){
	global $woocommerce;
	return ( WC()->version < '2.7.0' ) ? $item->height : $item->get_height();
}
}
if(!function_exists('ma_pu_get_product_weight'))
{
function ma_pu_get_product_weight( $item ){
	global $woocommerce;
	return ( WC()->version < '2.7.0' ) ? $item->weight : $item->get_weight();
}
}
//product ends