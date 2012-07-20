jQuery(document).ready(
			// One unique function per map
			function location_<?php echo $uid; ?>() {
				// ------------------------------------------------------------
				// ADD A MARKER
				// ------------------------------------------------------------
				function addMarker(position, address) 
				{
					// If a marker already exists
					if (marker)
					{
						// Delete it
						marker.setMap(null);
					}
					// Add the marker
					marker = new google.maps.Marker(
					{
						map: map,
						position: position,
						title: address,
						draggable: true
					});
					// Update the map
					map.setCenter(position);
					// Drag & drop it
					dragdropMarker();
				}
				
				// ------------------------------------------------------------
				// DRAG & DROP A MARKER
				// ------------------------------------------------------------
				function dragdropMarker() 
				{
					// Listen for the end of the drag and drop event
					google.maps.event.addListener(marker, 'dragend', function (mapEvent) 
					{
						// Update the address
						coordinates = mapEvent.latLng.lat() + ',' + mapEvent.latLng.lng();
						locateByCoordinates(coordinates);
					});
				}
				
				// ------------------------------------------------------------
				// ADD A MARKER WITH AN ADDRESS
				// ------------------------------------------------------------
				function locateByAddress(address) 
				{
					// Send over the address to get coordinates
					geocoder.geocode({'address': address}, function(results, status) 
					{
						// If Google has a result
						if (status == google.maps.GeocoderStatus.OK) 
						{
							// Add the marker
							addMarker(results[0].geometry.location, address);
							// Update the coordinates
							coordinates = results[0].geometry.location.lat() + ',' + results[0].geometry.location.lng();
							// Update the value
							coordinatesAddressInput.value =  address + '|' + coordinates;
							// Update the definition list
							ddAddress.innerHTML = address;
							ddCoordinates.innerHTML = coordinates;
						}
						else 
						{
							alert("<?php _e("This address couldn't be found: ",'acf-location-field'); ?>" + status);
						}
					});
				}
				
				// ------------------------------------------------------------
				// ADD A MARKER WITH COORDINATES
				// ------------------------------------------------------------
				function locateByCoordinates(coordinates) 
				{
					latlngTemp = coordinates.split(',',2);
					lat = parseFloat(latlngTemp[0]);
					lng = parseFloat(latlngTemp[1]);
					latlng = new google.maps.LatLng(lat, lng);
					
					// Send over the coordinates to get an address
					geocoder.geocode({'latLng': latlng}, function(results, status) 
					{
						// If Google has a result
						if (status == google.maps.GeocoderStatus.OK) 
						{
							// Update the address
							address = results[0].formatted_address;
							// Add the marker
							addMarker(latlng, address);
							// Update the value
							coordinatesAddressInput.value =  address + '|' + coordinates;
							// Update the definition list
							ddAddress.innerHTML = address;
							ddCoordinates.innerHTML = coordinates;
						} 
						else 
						{
							alert("<?php _e("This place couldn't be found: ",'acf-location-field'); ?>" + status);
						}
					});
				}
				
				// ------------------------------------------------------------
				// VARIABLES
				// ------------------------------------------------------------
				var map, lat, lng, latlng, marker, coordinates, address, val;
				// https://developers.google.com/maps/documentation/javascript/geocoding
				var geocoder = new google.maps.Geocoder();
				
				// Set the variables for the definition list
				var ddAddress = document.getElementById('location_dd-address_<?php echo $uid; ?>');
				var dtAddress = document.getElementById('location_dt-address_<?php echo $uid; ?>');
				var ddCoordinates = document.getElementById('location_dd-coordinates_<?php echo $uid; ?>');
				
				// Get the location
				var locationInput = document.getElementById('location_input_<?php echo $uid; ?>');
				var location = locationInput.value;
				
				// Get the coordinates and address
				var coordinatesAddressInput = document.getElementById('location_coordinates-address_<?php echo $uid; ?>');
				var coordinatesAddress = coordinatesAddressInput.value;
				// If not empty
				if (coordinatesAddress) 
				{
					// Split the value
					var coordinatesAddressTemp = coordinatesAddress.split('|', 2);
					// Get the coordinates
					coordinates = coordinatesAddressTemp[1];
					// Get the address
					address = coordinatesAddressTemp[0];
				}
				
				// ------------------------------------------------------------
				// CONSTRUCT THE MAP
				// ------------------------------------------------------------
				if (address) 
				{
					// Display the address
					ddAddress.innerHTML = address;
				}
				if (coordinates) 
				{
					// Display the coordinates
					ddCoordinates.innerHTML = coordinates;
					// Split the coordinates into 'lat' and 'lng'
					var latlngTemp = coordinates.split(',', 2);
					lat = parseFloat(latlngTemp[0]);
					lng = parseFloat(latlngTemp[1]);
				}
				else 
				{
					// Retrieve values set in the field group
					lat = <?php echo $center[0]; ?>;
					lng = <?php echo $center[1]; ?>;
				}
				// Set up a map with the coordinates
				latlng = new google.maps.LatLng(lat, lng);
				// Set all options needed
				var mapOptions = {
					zoom: <?php echo $zoom; ?>,
					center: latlng,
					mapTypeId: google.maps.MapTypeId.ROADMAP
				};
				// Draw the map
				map = new google.maps.Map(document.getElementById('location_map_<?php echo $uid; ?>'), mapOptions);
				// Then add a marker
				if (coordinates) 
				{
					addMarker(map.getCenter());
				}
				
				// ------------------------------------------------------------
				// WHEN THE MAP IS CLICKED
				// ------------------------------------------------------------
				google.maps.event.addListener(map, 'click', function(point) 
				{
					locateByCoordinates(point.latLng.lat() + ',' + point.latLng.lng());
				});
				
				// ------------------------------------------------------------
				// WHEN A KEY IS PRESSED IN THE INPUT FIELD
				// ------------------------------------------------------------
				locationInput.addEventListener('keypress', function(event) 
				{
					// If the 'enter' key is pressed
					if (event.keyCode == 13) 
					{
						// Retrieve the value
						location = locationInput.value;
						// Regular expression to match coordinates
						var regexp = new RegExp('^\-?[0-9]{1,3}\.[0-9]{6,},\-?[0-9]{1,3}\.[0-9]{6,}$');
						// If not empty
						if (location) 
						{
							// Test if the value match coordinates
							if (regexp.test(location)) 
							{
								locateByCoordinates(location);
							}
							else 
							{
								locateByAddress(location);
							}
						}
						// Prevent the post to be updated
						event.stopPropagation();
						event.preventDefault();
						return false;
					}
					
				}, false);
				
				// ------------------------------------------------------------
				// WHEN THE BUTTON IS CLICKED
				// ------------------------------------------------------------
				dtAddress.addEventListener('click', function() 
				{
					if (coordinates) {
						locateByCoordinates(coordinates);
					}
				}, false);
			}
		);