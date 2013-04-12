<?php

if( !class_exists( 'ACF_Location_Field' ) && class_exists( 'acf_field' ) ) :

/*
 * Advanced Custom Fields - Location Field add-on
 *
 * @author Julien Bechade <julien.bechade@gmail.com>
 * @contributor Brian Zoetewey <brian.zoetewey@ccci.org>
 * @version 1.0
 *
 */

class ACF_Location_Field extends acf_field
{
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
		$this->l10n_domain = acf_location_plugin::L10N_DOMAIN;

    	// set name / title
    	$this->name = 'location-field'; // variable name (no spaces / special characters / etc)
		$this->label = __( 'Location', $this->l10n_domain ); // field label (Displayed in edit screens)

    	parent::__construct();
   	}


	/*--------------------------------------------------------------------------------------
	*
	*	admin_head
	*	- this function is called in the admin_head of the edit screen where your field
	*	is created. Use this function to create css and javascript to assist your
	*	create_field() function.
	*
	*	@author Elliot Condon
	*	@since 2.2.0
	*
	*-------------------------------------------------------------------------------------*/

	public function input_admin_head()
	{
		echo '<script src="https://maps.googleapis.com/maps/api/js?sensor=false" type="text/javascript"></script>';
	}

	public function input_admin_enqueue_scripts()
	{
		global $pagenow;
		wp_register_style( 'acf-location-field', plugins_url( 'style.css', __FILE__ ) );

		if( in_array( $pagenow, array( 'post.php', 'post-new.php', 'admin.php' ) ) )
		{
			wp_enqueue_style( 'acf-location-field' );
		}
	}


	/*--------------------------------------------------------------------------------------
	*
	*	admin_print_scripts / admin_print_styles
	*	- this function is called in the admin_print_scripts / admin_print_styles where
	*	your field is created. Use this function to register css and javascript to assist
	*	your create_field() function.
	*
	*	@author Elliot Condon
	*	@since 3.0.0
	*
	*-------------------------------------------------------------------------------------*/

	public function field_group_admin_enqueue_scripts()
	{
		global $pagenow;
		wp_register_style( 'acf-location-field', plugins_url( 'style.css', __FILE__ ) );

		if( in_array( $pagenow, array( 'post.php', 'post-new.php', 'admin.php' ) ) )
		{
			wp_enqueue_style( 'acf-location-field' );
		}
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
		$field['scrollwheel'] = isset($field['scrollwheel']) ? $field['scrollwheel'] : '1';
	}


	/*--------------------------------------------------------------------------------------
	*
	*	create_options
	*	- this function is called from core/field_meta_box.php to create extra options
	*	for your field
	*
	*	@params
	*	- $field (array) - the field object
	*
	*	@author Elliot Condon
	*	@since 2.2.0
	*
	*-------------------------------------------------------------------------------------*/

	public function create_options($field)
	{
		$this->set_field_defaults($field);
		$key = $field['name'];
		?>
		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label><?php _e('Map address','acf-location-field'); ?></label>
				<p class="description"><?php _e('Return the address along with the coordinates.','acf-location-field'); ?></p>
			</td>
			<td>
				<?php
		do_action('acf/create_field', array(
					'type' => 'radio',
					'name' => 'fields['.$key.'][val]',
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
		do_action('acf/create_field', array(
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
				    do_action('acf/create_field', array(
					    'type'	=>	'text',
					    'name'	=>	'fields['.$key.'][zoom]',
					    'value'	=>	$field['zoom']
					));
				?>
			</td>
		</tr>
		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label><?php _e('Map Scrollwheel','acf-location-field'); ?></label>
				<p class="description"><?php _e('Allows scrollwheel zooming on the map field','acf-location-field'); ?></p>
			</td>
			<td>
				<?php
				    do_action('acf/create_field', array(
					'type' => 'radio',
					'name' => 'fields['.$key.'][scrollwheel]',
					'value' => $field['scrollwheel'],
					'layout' => 'horizontal',
					'choices' => array(
						'1' => __('Yes', 'acf-location-field'),
						'0' => __('No', 'acf-location-field')
					)
					  ));
				?>
			</td>
		</tr>
		<?php
	}



	/*--------------------------------------------------------------------------------------
	*
	*	create_field
	*	- this function is called on edit screens to produce the html for this field
	*
	*	@author Elliot Condon
	*	@since 2.2.0
	*
	*-------------------------------------------------------------------------------------*/

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
		$scrollwheel = $field['scrollwheel'];

	?>
	<script type="text/javascript">
		function location_init(uid){
			function addMarker(position,address){
				if(marker){marker.setMap(null)}
				marker = new google.maps.Marker({map:map,position:position,title:address,draggable:true});
				map.setCenter(position);
				dragdropMarker()
			}
			function dragdropMarker(){
				google.maps.event.addListener(marker,'dragend',function(mapEvent){
					coordinates = mapEvent.latLng.lat()+','+mapEvent.latLng.lng();locateByCoordinates(coordinates)})
			}
			function locateByAddress(address){
				geocoder.geocode({'address':address},function(results,status){
					if(status == google.maps.GeocoderStatus.OK){
						addMarker(results[0].geometry.location,address);
						coordinates = results[0].geometry.location.lat()+','+results[0].geometry.location.lng();
						coordinatesAddressInput.value = address+'|'+coordinates;ddAddress.innerHTML=address;
						ddCoordinates.innerHTML = coordinates
					}
					else{
						alert("<?php _e("This address could not be found: ",'acf-location-field');?>"+status)
					}
				})
			}
			function locateByCoordinates(coordinates){
				latlngTemp = coordinates.split(',',2);
				lat = parseFloat(latlngTemp[0]);
				lng = parseFloat(latlngTemp[1]);
				latlng = new google.maps.LatLng(lat,lng);
				geocoder.geocode({'latLng':latlng},function(results,status){
					if(status == google.maps.GeocoderStatus.OK){
						address = results[0].formatted_address;addMarker(latlng,address);
						coordinatesAddressInput.value = address+'|'+coordinates;ddAddress.innerHTML=address;ddCoordinates.innerHTML=coordinates
					}
					else{
						alert("<?php _e("This place could not be found: ",'acf-location-field');?>"+status)
					}
				})
			}
			var map,lat,lng,latlng,marker,coordinates,address,val;
			var geocoder = new google.maps.Geocoder();
			var ddAddress = document.getElementById('location_dd-address_'+uid);
			var dtAddress = document.getElementById('location_dt-address_'+uid);
			var ddCoordinates = document.getElementById('location_dd-coordinates_'+uid);
			var locationInput = document.getElementById('location_input_'+uid);
			var location = locationInput.value;
			var coordinatesAddressInput = document.getElementById('location_coordinates-address_'+uid);
			var coordinatesAddress = coordinatesAddressInput.value;
			if(coordinatesAddress){
				var coordinatesAddressTemp = coordinatesAddress.split('|',2);
				coordinates = coordinatesAddressTemp[1];
				address = coordinatesAddressTemp[0]
			}if(address){
				ddAddress.innerHTML = address
			}
			if(coordinates){
				ddCoordinates.innerHTML = coordinates;
				var latlngTemp = coordinates.split(',',2);
				lat = parseFloat(latlngTemp[0]);
				lng = parseFloat(latlngTemp[1])
			}else{
				lat = <?php echo $center[0];?>;
				lng = <?php echo $center[1];?>
			}
			latlng = new google.maps.LatLng(lat,lng);
			var mapOptions = {
				zoom:<?php echo $zoom;?>,
				center:latlng,
				mapTypeId:google.maps.MapTypeId.ROADMAP,scrollwheel: <?php echo $scrollwheel; ?>
			};
			map = new google.maps.Map(document.getElementById('location_map_'+uid),mapOptions);
			if(coordinates){
				addMarker(map.getCenter())
			}
			google.maps.event.addListener(map,'click',function(point){
				locateByCoordinates(point.latLng.lat()+','+point.latLng.lng())
			});
			locationInput.addEventListener('keypress',function(event){
				if(event.keyCode == 13){
					location=locationInput.value;
					var regexp = new RegExp('^\-?[0-9]{1,3}\.[0-9]{6,},\-?[0-9]{1,3}\.[0-9]{6,}$');
					if(location){
						if(regexp.test(location)){
							locateByCoordinates(location)
						}
						else{
							locateByAddress(location)}
						}
						event.stopPropagation();
						event.preventDefault();
						return false
					}
				},false);
			dtAddress.addEventListener('click',function(){
				if(coordinates){
					locateByCoordinates(coordinates)
				}
			},false)
		};

		jQuery(document).ready(function(){
			location_init("<?php echo $uid;?>");
		});
		var mapids = Array();
		jQuery(document).on('acf/setup_fields',function(e){
			var new_uid = jQuery(".repeater .row input[id*=location_coordinates]").not(".exsist").last().attr("id");

			if(new_uid) {
				new_uid = new_uid.replace("location_coordinates-address_","");
				location_init(new_uid);
				jQuery(".repeater .row input[id*=location_coordinates]").addClass("exsist");
			}
		});
	</script>
	<input type="hidden" value="<?php echo $field['value']; ?>" id="location_coordinates-address_<?php echo $uid; ?>" name="<?php echo $field['name']; ?>"/>
	<input type="text" id="location_input_<?php echo $uid; ?>" placeholder="<?php _e('Search for a location','acf-location-field'); ?>" />
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



	/*--------------------------------------------------------------------------------------
	*
	*	get_value_for_api
	*	- called from your template file when using the API functions (get_field, etc).
	*	This function is useful if your field needs to format the returned value
	*
	*	@params
	*	- $post_id (int) - the post ID which your value is attached to
	*	- $field (array) - the field object.
	*
	*	@author Elliot Condon
	*	@since 3.0.0
	*
	*-------------------------------------------------------------------------------------*/

	public function get_value_for_api($post_id, $field)
	{
		// get value
		$value = $this->get_value($post_id, $field);

		// format value
		$value = explode('|', $value);
		if ($field['val'] == 'address')
		{
			$value = array( 'coordinates' => $value[1], 'address' => $value[0] );
		}
		else {
			$value = $value[1];
		}

		// return value
		return $value;

	}

}
endif; //class_exists 'ACF_Location_Field'


new ACF_Location_Field();

?>
