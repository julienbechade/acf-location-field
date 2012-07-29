Advanced Custom Fields - Location Field add-on
==============================================

Adds a Location field to Advanced Custom Fields. This field allows you to find addresses and coordinates of a desired location.

Description
-----------

This is an add-on for the [Advanced Custom Fields](http://wordpress.org/extend/plugins/advanced-custom-fields/)
WordPress plugin and will not provide any functionality to WordPress unless Advanced Custom Fields is installed
and activated.

The Location field provides:

* a search field where you can type in some coordinates or an address and hit `Enter`. 
* a Google map which you can click at the desired location.

In both cases, Google will find the location and return the coordinates and the complete address, if you want it complete. A marker will be added at the desired location.

### Download on Wordpress.org
http://wordpress.org/extend/plugins/advanced-custom-fields-location-field-add-on/

### Source Repository on GitHub
https://github.com/julienbechade/acf-location-field

### Bugs, Questions or Suggestions
https://github.com/julienbechade/acf-location-field/issues

Usage
-----

Make sure you read the [Advanced Custom Fields](http://www.advancedcustomfields.com/docs/getting-started/)'s documentation first.

### Back-end

The Location field comes with 3 options:

1. The map address let you choose the value(s) to return on the front-end:
	* Coordinates and address (default)
	* Coordinates only
2. The map center let you set the coordinates used to center the initial blank map.
2. The map zoom.

### Front-end

Retrieving the value(s) on the front-end differs according to the Map address options.

* Coordinates and address (default)
``` php
<?php
	$location = get_field('location');
	
	echo $location['address'];
	echo $location['coordinates'];
?>
```
* Coordinates only
``` php
<?php the_field('location'); ?>
```

Installation
------------

The Location Field plugin can be used as a WordPress plugin or included in other plugins or themes.
There is no need to call the Advanced Custom Fields `register_field()` method for this field.

* WordPress plugin
	1. Download the plugin and extract it to `/wp-content/plugins/` directory.
	2. Activate the plugin through the `Plugins` menu in WordPress.
* Added to Theme or Plugin
	1. Download the plugin and extract it to your theme or plugin directory.
	2. Include the `location-field.php` file in you theme's `functions.php` or plugin file.  
	   `include_once( rtrim( dirname( __FILE__ ), '/' ) . '/acf-location-field/location-field.php' );`

Frequently Asked Questions
--------------------------

### I've activated the plugin, but nothing happens!

Make sure you have [Advanced Custom Fields](http://wordpress.org/extend/plugins/advanced-custom-fields/) installed and
activated. This is not a standalone plugin for WordPress, it only adds additional functionality to Advanced Custom Fields.

Changelog
---------

### 1.0

* Initial release