<?php
/*
 * Advanced Custom Fields - Location Field add-on
 * 
 * @author Julien Bechade <julien.bechade@gmail.com>
 * @contributor Brian Zoetewey <brian.zoetewey@ccci.org>
 * @contributor Mark Fabrizio <fabrizim@owlwatch.com>
 * @version 1.1
 *
 */
class ACF_Location_Field extends acf_field
{
	/*
	 * Base directory
	 * @var string
	 *
	 */
	private $base_dir;
	
	/*
	 * Relative Uri from the WordPress ABSPATH constant
	 * @var string
	 *
	 */
	private $base_uri_rel;
	
	/*
	 * Absolute Uri
	 * 
	 * This is used to create urls to CSS and JavaScript files.
	 * @var string
	 *
	 */
	private $base_uri_abs;
	
	/*
	 * WordPress Localization Text Domain
	 * 
	 * The textdomain for the field is controlled by the helper class.
	 * @var string
	 *
	 */
	private $l10n_domain;
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	Constructor
	*	- This function is called when the field class is initalized on each page.
	*	- Here you can add filters / actions and setup any other functionality for your field
	*
	*	@author Elliot Condon
	*	@since 2.2.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	public function __construct()
	{
		
		//Get the textdomain from the Helper class
		$this->l10n_domain = ACF_Location_Field_Helper::L10N_DOMAIN;
		
		//Base directory of this field
		$this->base_dir = rtrim( dirname( realpath( __FILE__ ) ), DIRECTORY_SEPARATOR );
		
		$this->name = 'location-field';
		$this->label = __( 'Location', $this->l10n_domain ); // field label (Displayed in edit screens);
		
		//Call parent constructor
    parent::__construct();
    
		
		//Build the base relative uri by searching backwards until we encounter the wordpress ABSPATH
		//This may not work if the $base_dir contains a symlink outside of the WordPress ABSPATH
		$root = array_pop( explode( DIRECTORY_SEPARATOR, rtrim( realpath( ABSPATH ), DIRECTORY_SEPARATOR ) ) );
		$path_parts = explode( DIRECTORY_SEPARATOR, $this->base_dir );
		$parts = array();
		
		while( $part = array_pop( $path_parts ) ) 
		{
			if( $part == $root )
				break;
			array_unshift( $parts, $part );
		}
		
		$this->base_uri_rel = '/' . implode( '/', $parts );
		$this->base_uri_abs = get_site_url( null, $this->base_uri_rel );
  }
	
	
	/*
	*  input_admin_enqueue_scripts()
	*
	*  This action is called in the admin_enqueue_scripts action on the edit screen where your field is created.
	*  Use this action to add css + javascript to assist your create_field() action.
	*
	*  $info	http://codex.wordpress.org/Plugin_API/Action_Reference/admin_enqueue_scripts
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/
	public function input_admin_enqueue_scripts()
	{
		wp_enqueue_style( 'acf-location-field', $this->base_uri_abs . '/style.css' );
	}
	
	
	/*
	*  input_admin_head()
	*
	*  This action is called in the admin_head action on the edit screen where your field is created.
	*  Use this action to add css and javascript to assist your create_field() action.
	*
	*  @info	http://codex.wordpress.org/Plugin_API/Action_Reference/admin_head
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/
	public function input_admin_head()
	{
	?>
		<script src="https://maps.googleapis.com/maps/api/js?sensor=false" type="text/javascript"></script>
	<?php
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	set_field_defaults
	*	- populates the fields array with defaults for this field type
	*
	*	@param array $field
	*	@return array
	*
	*-------------------------------------------------------------------------------------*/
	 
	private function set_field_defaults(&$field)
	{
		$field['center'] = isset($field['center']) ? $field['center'] : '48.856614,2.3522219000000177';
		$field['zoom'] = isset($field['zoom']) ? $field['zoom'] : '2';
		$field['val'] = isset($field['val']) ? $field['val'] : 'address';
	}
	
	
	/*
	*  create_options()
	*
	*  Create extra options for your field. This is rendered when editing a field.
	*  The value of $field['name'] can be used (like bellow) to save extra data to the $field
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field	- an array holding all the field's data
	*/
	public function create_options($field)
	{
		$this->set_field_defaults($field);
		$key = $field['key'];
		
		?>
		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label><?php _e('Map address','acf-location-field'); ?></label>
				<p class="description"><?php _e('Return the address along with the coordinates.','acf-location-field'); ?></p>
			</td>
			<td>
				<?php 
				create_field(array(
					'type' => 'radio',
					'name' => 'fields['.$field['name'].'][val]',
					'value' => $field['val'],
					'layout' => 'horizontal',
					'choices' => array(
						'address' => __('Yes', 'acf-location-field'),
						'coordinates' => __('No', 'acf-location-field')
					)
				));
				?>
			</td>
		</tr>
		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label><?php _e('Map center','acf-location-field'); ?></label>
				<p class="description"><?php _e('Latitude and longitude to center the initial map.','acf-location-field'); ?></p>
			</td>
			<td>
				<?php 
				create_field(array(
					'type'	=>	'text',
					'name'	=>	'fields['.$key.'][center]',
					'value'	=>	$field['center']
				));
				?>
			</td>
		</tr>
		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label><?php _e('Map zoom','acf-location-field'); ?></label>
				<p class="description"><?php _e('','acf-location-field'); ?></p>
			</td>
			<td>
				<?php 
				create_field(array(
					'type'	=>	'text',
					'name'	=>	'fields['.$key.'][zoom]',
					'value'	=>	$field['zoom']
				));
				?>
			</td>
		</tr>
		<?php
	}
	
	
	/*
	*  create_field()
	*
	*  Create the HTML interface for your field
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field - an array holding all the field's data
	*/
	public function create_field($field)
	{
		$this->set_field_defaults($field);
		
		// Build an unique id based on ACF's one.
		$pattern = array('/\[/', '/\]/');
		$replace = array('_', '');
		$uid = preg_replace($pattern, $replace, $field['name']);
		// Retrieve options value
		$zoom = $field['zoom'];
		$center = explode(',', $field['center']);
	
	?>
	<script type="text/javascript">
		jQuery(document).ready(function location_<?php echo $uid;?>(){function addMarker(position,address){if(marker){marker.setMap(null)}marker=new google.maps.Marker({map:map,position:position,title:address,draggable:true});map.setCenter(position);dragdropMarker()}function dragdropMarker(){google.maps.event.addListener(marker,'dragend',function(mapEvent){coordinates=mapEvent.latLng.lat()+','+mapEvent.latLng.lng();locateByCoordinates(coordinates)})}function locateByAddress(address){geocoder.geocode({'address':address},function(results,status){if(status==google.maps.GeocoderStatus.OK){addMarker(results[0].geometry.location,address);coordinates=results[0].geometry.location.lat()+','+results[0].geometry.location.lng();coordinatesAddressInput.value=address+'|'+coordinates;ddAddress.innerHTML=address;ddCoordinates.innerHTML=coordinates}else{alert("<?php _e("This address couldn't be found: ",'acf-location-field');?>"+status)}})}function locateByCoordinates(coordinates){latlngTemp=coordinates.split(',',2);lat=parseFloat(latlngTemp[0]);lng=parseFloat(latlngTemp[1]);latlng=new google.maps.LatLng(lat,lng);geocoder.geocode({'latLng':latlng},function(results,status){if(status==google.maps.GeocoderStatus.OK){address=results[0].formatted_address;addMarker(latlng,address);coordinatesAddressInput.value=address+'|'+coordinates;ddAddress.innerHTML=address;ddCoordinates.innerHTML=coordinates}else{alert("<?php _e("This place couldn't be found: ",'acf-location-field');?>"+status)}})}var map,lat,lng,latlng,marker,coordinates,address,val;var geocoder=new google.maps.Geocoder();var ddAddress=document.getElementById('location_dd-address_<?php echo $uid; ?>');var dtAddress=document.getElementById('location_dt-address_<?php echo $uid; ?>');var ddCoordinates=document.getElementById('location_dd-coordinates_<?php echo $uid; ?>');var locationInput=document.getElementById('location_input_<?php echo $uid; ?>');var location=locationInput.value;var coordinatesAddressInput=document.getElementById('location_coordinates-address_<?php echo $uid; ?>');var coordinatesAddress=coordinatesAddressInput.value;if(coordinatesAddress){var coordinatesAddressTemp=coordinatesAddress.split('|',2);coordinates=coordinatesAddressTemp[1];address=coordinatesAddressTemp[0]}if(address){ddAddress.innerHTML=address}if(coordinates){ddCoordinates.innerHTML=coordinates;var latlngTemp=coordinates.split(',',2);lat=parseFloat(latlngTemp[0]);lng=parseFloat(latlngTemp[1])}else{lat=<?php echo $center[0];?>;lng=<?php echo $center[1];?>}latlng=new google.maps.LatLng(lat,lng);var mapOptions={zoom:<?php echo $zoom;?>,center:latlng,mapTypeId:google.maps.MapTypeId.ROADMAP,scrollwheel:false};map=new google.maps.Map(document.getElementById('location_map_<?php echo $uid; ?>'),mapOptions);if(coordinates){addMarker(map.getCenter())}google.maps.event.addListener(map,'click',function(point){locateByCoordinates(point.latLng.lat()+','+point.latLng.lng())});locationInput.addEventListener('keypress',function(event){if(event.keyCode==13){location=locationInput.value;var regexp=new RegExp('^\-?[0-9]{1,3}\.[0-9]{6,},\-?[0-9]{1,3}\.[0-9]{6,}$');if(location){if(regexp.test(location)){locateByCoordinates(location)}else{locateByAddress(location)}}event.stopPropagation();event.preventDefault();return false}},false);dtAddress.addEventListener('click',function(){if(coordinates){locateByCoordinates(coordinates)}},false)});
	</script>
	<input type="text" id="location_input_<?php echo $uid; ?>" placeholder="Search for a location" />
	<input type="hidden"
				 value="<?php echo $field['value']; ?>"
				 id="location_coordinates-address_<?php echo $uid; ?>"
				 name="<?php echo $field['name']; ?>"
				 data-uid="<?php echo $uid; ?>"
				 data-center="<?php echo $field['center']; ?>"
				 data-zoom="<?php echo $zoom; ?>"
	/>
	<dl class="location_dl">
		<dt class="location_dt-address" id="location_dt-address_<?php echo $uid; ?>" role="button" title="<?php _e('Find the complete address','acf-location-field'); ?>"><?php _e('Address: ','acf-location-field'); ?></dt>
		<dd class="location_dd" id="location_dd-address_<?php echo $uid; ?>">&nbsp;</dd>
		<dt class="location_dt-coordinates"><?php _e('Coordinates: ','acf-location-field'); ?></dt>
		<dd class="location_dd" id="location_dd-coordinates_<?php echo $uid; ?>">&nbsp;</dd>
	</dl>
	<div class="location_map-container">
		<div class="location_map" id="location_map_<?php echo $uid; ?>"></div>
	</div>
	<?php
	}
	
	/*
	*  format_value_for_api()
	*
	*  This filter is appied to the $value after it is loaded from the db and before it is passed back to the api functions such as the_field
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value	- the value which was loaded from the database
	*  @param	$field	- the field array holding all the field options
	*
	*  @return	$value	- the modified value
	*/
	public function get_value_for_api($value, $field)
	{
		// format value
		$value = explode('|', $value);
		if ($field['val'] == 'address') 
		{
			$value = array( 'coordinates' => $value[1], 'address' => $value[0] );
		}
		else {
			$value = $value[0];
		}
		
		// return value
		return $value;
	}	
}

/*
 * Advanced Custom Fields - Location Field Helper
 * 
 * @author Brian Zoetewey <brian.zoetewey@ccci.org>
 *
 */
class ACF_Location_Field_Helper {
	/*
	 * Singleton instance
	 * @var ACF_Location_Field_Helper
	 *
	 */
	private static $instance;
	
	/*
	 * Returns the ACF_Location_Field_Helper singleton
	 * 
	 * <code>$obj = ACF_Location_Field_Helper::singleton();</code>
	 * @return ACF_Location_Field_Helper
	 *
	 */
	public static function singleton() 
	{
		if( !isset( self::$instance ) ) 
		{
			$class = __CLASS__;
			self::$instance = new $class();
		}
		return self::$instance;
	}
	
	/*
	 * Prevent cloning of the ACF_Location_Field_Helper object
	 * @internal
	 *
	 */
	private function __clone() 
	{
		
	}
	
	/*
	* WordPress Localization Text Domain
	*
	* Used in wordpress localization and translation methods.
	* @var string
	*
	*/
	const L10N_DOMAIN = 'acf-location-field';
	
	/*
	 * Language directory path
	 * 
	 * Used to build the path for WordPress localization files.
	 * @var string
	 *
	 */
	private $lang_dir;
	
	/*
	 * Constructor
	 *
	 */
	private function __construct() 
	{
		$this->lang_dir = rtrim( dirname( realpath( __FILE__ ) ), '/' ) . '/lang';
		
		$this->register_field();
		add_action( 'init', array( &$this, 'load_textdomain' ), 2, 0 );
	}
	
	/*
	 * Registers the Field with Advanced Custom Fields
	 *
	 */
	public function register_field() 
	{
		new ACF_Location_Field();
	}
	
	/*
	 * Loads the textdomain for the current locale if it exists
	 *
	 */
	public function load_textdomain() 
	{
		$locale = get_locale();
		$mofile = $this->lang_dir . '/' . self::L10N_DOMAIN . '-' . $locale . '.mo';
		load_textdomain( self::L10N_DOMAIN, $mofile );
	}
}
?>