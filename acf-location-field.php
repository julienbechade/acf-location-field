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

class acf_location_plugin
{
	var $settings;
	
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
	*  Constructor
	*
	*  @description: 
	*  @since 1.0.0
	*  @created: 23/06/12
	*/
	
	function __construct()
	{
		// vars
		$settings = array(
			'version' => '1.0.0',
			'basename' => plugin_basename(__FILE__),
		);
		
		$this->lang_dir = rtrim( dirname( realpath( __FILE__ ) ), '/' ) . '/lang';

		// actions
		add_action( 'init', array( &$this, 'load_textdomain' ), 2, 0 );
		add_action('acf/register_fields', array($this, 'register_fields'));
	}
	
	
	/*
	*  register_fields
	*
	*  @description: 
	*  @since: 3.6
	*  @created: 31/01/13
	*/
	
	function register_fields()
	{
		include_once('location-field.php');
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

new acf_location_plugin();

?>
