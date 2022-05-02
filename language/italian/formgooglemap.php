<?php
//class/formgooglemap.php
define('_FORMGOOGLEMAP_ERROR_GEOCODER', 'Geocode was not successful for the following reason: ');
define('_FORMGOOGLEMAP_ERROR_ELEVATOR', 'Elevation service failed due to: ');
define('_FORMGOOGLEMAP_ERROR_ELEVATOR_NOTFOUND', 'No results found');
define('_FORMGOOGLEMAP_ERROR_NO_APYKEY', 'Please set a valid Google Maps API Key');


define('_FORMGOOGLEMAP_GO_INIT', 'Reset');
define('_FORMGOOGLEMAP_GO_INIT_DESC', 'Click to set the map to Initial position');
define('_FORMGOOGLEMAP_TYPE_OPENSTREETMAP', 'OpenStreetMap');

define('_FORMGOOGLEMAP_MARKER_CLICKTOUPDATEPOSITION', 'Click to set the coordinates or drag');
define('_FORMGOOGLEMAP_GOOGLEMAPHERE', '<b>GOOGLE MAP HERE</b>');
define('_FORMGOOGLEMAP_GOOGLEMAPHERE_DESC', 'In this area will appear Google Maps map if an internet connection is available');
define('_FORMGOOGLEMAP_LAT', 'Latitude (Dec. Deg.)');
define('_FORMGOOGLEMAP_LNG', 'Longitude (Dec. Deg.)');
define('_FORMGOOGLEMAP_ELEVATION', 'Elevation (meters)');
define('_FORMGOOGLEMAP_ZOOM', 'Zoom level');
define('_FORMGOOGLEMAP_LATLNGZOOM', 'Position');
define(
    '_FORMGOOGLEMAP_LATLNGZOOM_DESC1',
    'Drag the Marker and click on it to set the coordinates or input the coordinates to automatically move the marker'
);
define(
    '_FORMGOOGLEMAP_LATLNGZOOM_DESC2',
    'Use: deg-min-sec suffixed with N/S/E/W (e.g. 40&deg;44&#39;55&quot;N, 73 59 11W), <br />or signed decimal degrees without compass direction, where negative indicates west/south (e.g. +40.689060  -74.044636)'
);
define('_FORMGOOGLEMAP_SEARCH', 'Search location');
define('_FORMGOOGLEMAP_SEARCH_DESC', 'Use &quot;Search Location&quot; to use Google searching engine');
define('_FORMGOOGLEMAP_SEARCHBUTTON', 'Search');
define('_FORMGOOGLEMAP_SEARCHERROR', 'Geocode was not successful for the following reason: ');
