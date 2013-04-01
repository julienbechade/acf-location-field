<?php
/*
* Plugin Name: Advanced Custom Fields - Location Field add-on
* Plugin URI:  https://github.com/julienbechade/acf-location-field
* Description: This plugin is an add-on for Advanced Custom Fields. It allows you to find coordinates and/or address of a location with Google Maps.
* Author:      Julien Bechade
* Author URI:  http://julienbechade.com/
* Version:     1.0
* Text Domain: acf-location-field
* Domain Path: /lang/
*/

add_action( 'acf/register_fields', 'register_acf_location_field' );
function register_acf_location_field()
{
  require dirname( __FILE__ ) .'/location-field.php';
  ACF_Location_Field_Helper::singleton();
}