<?php
/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */
/*
 * common module
 *
 * @copyright       XOOPS Project https://xoops.org/
 * @license         GNU GPL 2 (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
 * @package         common
 * @since           1.00
 * @author          luciorota
 * @version         svn:$Id$
 */
namespace XoopsModules\Common;
use Xmf\Module\Helper;

defined('XOOPS_ROOT_PATH') or die('XOOPS root path not defined');

xoops_loadLanguage('formgooglemap', 'common');
xoops_load('XoopsFormLoader');

class FormGoogleMap extends \XoopsFormElementTray
{
    /**
     * @var commonHelper
     * @access private
     */
    private $commonHelper = null;

    /**
     * @var api_key
     * @access private
     */
    private $_api_key = '';

    public $config = array();
    public $setting = array();
    public $lat, $lng, $elevatin, $maptypeid, $zoom, $search;

    /**
     * constructor
     */
    public  $_caption = '';
    public  $_name    = '';
    private $_value   = array();

    /**
     * element id, it will different from element name for javascript issues
     *
     * @var string
     * @access private
     */
    private $_id;

    /**
     * FormGoogleMap::FormGoogleMap()
     *
     * @param mixed $caption
     * @param mixed $name
     * @param array('lat' => , 'lng' => , 'elevation' => , 'maptypeid' => , 'zoom' =>) $value
     * @param array('option name'=>'option_value') $configs
     */
    public function __construct($caption, $name, $value = array(), $api_key = '', $config = array())
    {
		$this->commonHelper = \Xmf\Module\Helper::getHelper('common');
		//
        $this->setCaption($caption);
        $this->setName($name);
		if (empty($api_key)) {
			$api_key = $this->commonHelper->getConfig('GoogleMapsAPIKey');
		}
        $this->setApi_key($api_key);
        $this->setId($name);
        $this->_value = $value;
        //
        $this->config['style'] = 'width:100%;height:400px;';
        $this->config['readonly'] = false;
        $this->config = array_merge($this->config, $config);
        //
        $this->lat = (is_array($value) && isset($value['lat'])) ? $value['lat'] : '0'; // default lat
        $this->lng = (is_array($value) && isset($value['lng'])) ? $value['lng'] : '0'; // default lng
        $this->elevation = (is_array($value) && isset($value['elevation'])) ? $value['elevation'] : ''; // default elevation
        $this->maptypeid = (is_array($value) && isset($value['maptypeid'])) ? $value['maptypeid'] : 'roadmap'; // default maptype
        $this->zoom = (is_array($value) && isset($value['zoom'])) ? $value['zoom'] : 8; // default zoom level
        $this->search = '';
    }

    /**
     * set the "api_key" attribute for the element
     *
     * @param string $api_key "api_key" attribute for the element
     */
    public function setApi_key($api_key)
    {
        $this->_api_key = $api_key;
    }

    /**
     * get the "api_key" attribute for the element
     *
     * @param bool $encode
     *
     * @return string "api_key" attribute
     */
    public function getApi_key($encode = true)
    {
        if (false != $encode) {
            return str_replace('&amp;', '&', htmlspecialchars($this->_api_key, ENT_QUOTES));
        }

        return $this->_api_key;
    }

    /**
     * set the "id" attribute for the element
     *
     * @param string $name "name" attribute for the element
     */
    public function setId($name)
    {
        $this->_id = md5(uniqid(mt_rand(), true));
    }

    /**
     * get the "id" attribute for the element
     *
     * @param bool $encode
     *
     * @return string "name" attribute
     */
    public function getId($encode = true)
    {
        if (false != $encode) {
            return str_replace('&amp;', '&', htmlspecialchars($this->_id, ENT_QUOTES));
        }

        return $this->_id;
    }

    /**
     * FormGoogleMap::setConfig()
     *
     * @return bool
     */
    public function setConfig()
    {
        $args = func_get_args();
        // For backward compatibility
        if (!is_array($args[0])) {
            if (count($args) >= 2) {
                $this->config = array_merge($this->config, array($args[0] => $args[1]));
                return true;
            } else {
                return false;
            }
        } else {
            $this->config = array_merge($this->config, $args[0]);
            return true;
        }
        return false;
    }

    /**
     * prepare HTML for output
     *
     * @return string HTML
     */
    public function render() {
        static $isCommonFormGoogleMapIncluded = false;
        $commonJs = '';
        $js = '';
        $html = '';
        $ret = '';

        $commonJs = "
function xoopsFormGoogleMap(name, id, initLat, initLng, initZoom, draggable) {
	// init vars
	var name = name;
	var initLatlng = new google.maps.LatLng(initLat, initLng);
	var currentLatlng = initLatlng;
	var currentZoom = initZoom;
	var map = null;
	var marker = null;
	var geocoderService = new google.maps.Geocoder(); // Create a Geocoder service
	var elevatorService = new google.maps.ElevationService(); // Create an ElevationService service
	// Build list of map types.
	var mapTypeIds = [];
	for(var type in google.maps.MapTypeId) {
		mapTypeIds.push(google.maps.MapTypeId[type]);
	}
	mapTypeIds.push('OSM');
	var initMapOptions = {
		center: initLatlng,
		zoom: initZoom,
		mapTypeId: '{$this->maptypeid}',
			mapTypeControlOptions: {
			mapTypeIds: mapTypeIds
		}
	};
	// create new map
	map = new google.maps.Map(document.getElementById(id), initMapOptions);

	map.mapTypes.set('OSM', new google.maps.ImageMapType({
		getTileUrl: function(coord, zoom) {
			return 'http://tile.openstreetmap.org/' + zoom + '/' + coord.x + '/' + coord.y + '.png';
		},
		tileSize: new google.maps.Size(256, 256),
		name: '" . _FORMGOOGLEMAP_TYPE_OPENSTREETMAP . "',
		maxZoom: 18
	}));

	// Create the DIV to hold the control and call the ResetControl() constructor passing in this DIV.
	var resetControlDiv = document.createElement('div');
	var resetControl = new ResetControl(resetControlDiv, map);

	resetControlDiv.index = 1;
	map.controls[google.maps.ControlPosition.TOP_RIGHT].push(resetControlDiv);

	if (draggable) {
		// Place a draggable marker on the map
		marker = new google.maps.Marker({
			position: initLatlng,
			map: map,
			title: '" . _FORMGOOGLEMAP_MARKER_CLICKTOUPDATEPOSITION . "',
			draggable: true
		});
		google.maps.event.addListener(marker, 'click', function() {
			//map.setZoom(8);
			currentLatlng = marker.getPosition();
			map.setCenter(currentLatlng);
			getElevation(currentLatlng);
			updateFields();
		});
	} else {
		// Place a not draggable marker on the map
		marker = new google.maps.Marker({
			position: initLatlng,
			map: map,
			draggable: false
		});
	}

	google.maps.event.addListener(map, 'zoom_changed', function() {
		currentZoom = map.getZoom();
	});

	google.maps.event.addListener(map, 'center_changed', function() {
		// 6 seconds after the center of the map has changed, pan back to the marker.
		window.setTimeout(function() {
			map.panTo(marker.getPosition());
		}, 6000);
	});



	/**
	 * The ResetControl adds a control to the map that simply returns the user to initLatlng.
	 * This constructor takes the control DIV as an argument.
	 */
	function ResetControl(controlDiv, map) {
		// Set CSS styles for the DIV containing the control
		// Setting padding to 5 px will offset the control from the edge of the map
		controlDiv.style.padding = '5px';
		// Set CSS for the control border
		var controlUI = document.createElement('div');
		controlUI.style.backgroundColor = 'white';
		controlUI.style.borderStyle = 'solid';
		controlUI.style.borderWidth = '1px';
		controlUI.style.bordercolor = 'red';
		controlUI.style.cursor = 'pointer';
		controlUI.style.textAlign = 'center';
		controlUI.title = '" . _FORMGOOGLEMAP_GO_INIT_DESC . "';
		controlDiv.appendChild(controlUI);
		// Set CSS for the control interior
		var controlText = document.createElement('div');
		controlText.style.fontFamily = 'Arial,sans-serif';
		controlText.style.fontSize = '12px';
		controlText.style.color = 'red';
		controlText.style.paddingLeft = '4px';
		controlText.style.paddingRight = '4px';
		controlText.innerHTML = '<b>" . _FORMGOOGLEMAP_GO_INIT . "</b>';
		controlUI.appendChild(controlText);
		// Setup the click event listeners: simply set the map init values
		google.maps.event.addDomListener(controlUI, 'click', function() {
			marker.setPosition(initLatlng);
			map.setCenter(initLatlng);
			map.setZoom(initZoom);
			updateFields();
		});
	}



	this.updatePosition = function() {
		var lat = parseFloat(document.getElementById('' + name + '[lat]').value);
		var lng = parseFloat(document.getElementById('' + name + '[lng]').value);
		currentLatlng = new google.maps.LatLng(lat, lng);
		map.setCenter(currentLatlng);
		marker.setPosition(currentLatlng);
		getElevation(currentLatlng);
	}

	this.updateZoom = function() {
		currentZoom = parseInt(document.getElementById('' + name + '[zoom]').value);
		map.setZoom(currentZoom);
	}

	this.codeAddress = function() {
		var address = document.getElementById('' + name + '[search]').value;
		geocoderService.geocode( {'address': address}, function(results, status) {
			if (status == google.maps.GeocoderStatus.OK) {
				map.setCenter(results[0].geometry.location);
				marker.setPosition(results[0].geometry.location);
			} else {
				alert('" . _FORMGOOGLEMAP_ERROR_GEOCODER . "' + status);
			}
		});
	}

	function updateFields() {
		document.getElementById('' + name + '[lat]').value = currentLatlng.lat();
		document.getElementById('' + name + '[lng]').value = currentLatlng.lng();
		document.getElementById('' + name + '[zoom]').value = currentZoom;
		document.getElementById('' + name + '[maptypeid]').value = map.getMapTypeId();
	}

	function getElevation(latLng) {
		var locations = [];
		locations.push(latLng);
		// Create a LocationElevationRequest object using the array's one value
		var positionalRequest = {
			'locations': locations
		}
		// Initiate the location request
		elevatorService.getElevationForLocations(positionalRequest, function(results, status) {
			if (status == google.maps.ElevationStatus.OK) {
				// Retrieve the first result
				if (results[0]) {
					document.getElementById('' + name + '[elevation]').value = results[0].elevation;
				} else {
					alert('" . _FORMGOOGLEMAP_WARNING_ELEVATOR_NOTFOUND . "');
				}
			} else {
				alert('" . _FORMGOOGLEMAP_ERROR_ELEVATOR . "' + status);
			}
		});
	}
}
        ";
        if (is_object($GLOBALS['xoTheme'])) {
            if ( !$isCommonFormGoogleMapIncluded ) {
                // CodeMirror stuff
                $GLOBALS['xoTheme']->addScript('https://maps.googleapis.com/maps/api/js?key=' . $this->getApi_key() . '');
                $GLOBALS['xoTheme']->addScript('', array(), $commonJs);
                $isCommonFormGoogleMapIncluded = true;
            }
        } else {
            if (!$isCommonFormGoogleMapIncluded) {
                $ret .= "<script src='https://maps.googleapis.com/maps/api/js?key=" . $this->getApi_key() . "' type='text/javascript'></script>\n";
                $ret .= "<script type='text/javascript'>\n";
                $ret .= $commonJs . "\n";
                $ret .= "</script>\n";
                $isCommonFormGoogleMapIncluded = true;
            }
        }

        $js .= "<script type='text/javascript'>\n";
        $js .= "var xoopsFormGoogleMap_{$this->_name} = null;\n";
        if ($this->config['readonly'] == false) {
            $js .= "xoopsOnloadEvent(
                    function() {
                        xoopsFormGoogleMap_{$this->_name} = new xoopsFormGoogleMap('{$this->_name}', '{$this->_name}_GoogleMap', {$this->lat}, {$this->lng}, {$this->zoom}, true);
                    });";
        } else {
            $js .= "xoopsOnloadEvent(
                    function() {
                        xoopsFormGoogleMap_{$this->_name} = new xoopsFormGoogleMap('{$this->_name}', '{$this->_name}_GoogleMap', {$this->lat}, {$this->lng}, {$this->zoom}, false);
                    });";
        }
        $js .= "</script>\n";

        $ret .= $js . "\n";

        $html .= '<fieldset>';
        if (empty($this->getApi_key())) {
            $html .= "<div style='{$this->config['style']}'>" . _FORMGOOGLEMAP_ERROR_NO_APYKEY . '</div>';
        } else {
            $html .= "<div id='{$this->_name}_GoogleMap' style='{$this->config['style']}'>";
            $html .= _FORMGOOGLEMAP_GOOGLEMAPHERE;
            $html .= '<br />';
            $html .= _FORMGOOGLEMAP_GOOGLEMAPHERE_DESC;
            $html .= '</div>';
        }
        $html .= '</fieldset>';
        $ret .= $html . "\n";

        if ($this->config['readonly'] == false) {
            $ret .= '<div>';
            $ret .= '<b>' . _FORMGOOGLEMAP_LATLNGZOOM . '</b>';
            $ret .= '<br />';
            $ret .= _FORMGOOGLEMAP_LATLNGZOOM_DESC1;
            $ret .= '<br />';
            $ret .= '<br />';
            $ret .= _FORMGOOGLEMAP_LAT;
            $ret .= '&nbsp;';
            $ret .= "<input type='text' id='{$this->_name}[lat]' name='{$this->_name}[lat]' value='{$this->lat}' maxlength='255' size='18' title='Latitude' onchange='xoopsFormGoogleMap_{$this->_name}.updatePosition();'>";
            $ret .= '&nbsp;';
            $ret .= _FORMGOOGLEMAP_LNG;
            $ret .= '&nbsp;';
            $ret .= "<input type='text' id='{$this->_name}[lng]' name='{$this->_name}[lng]' value='{$this->lng}' maxlength='255' size='18' title='Longitude' onchange='xoopsFormGoogleMap_{$this->_name}.updatePosition();'>";
            $ret .= '<br />';
            $ret .= '<small>' . _FORMGOOGLEMAP_LATLNGZOOM_DESC2 . '</small>';
            $ret .= '<br />';
            $ret .= '<br />';
            $ret .= _FORMGOOGLEMAP_ELEVATION;
            $ret .= '&nbsp;';
            $ret .= "<input readonly type='text' id='{$this->_name}[elevation]' name='{$this->_name}[elevation]' value='{$this->elevation}' maxlength='255' size='18' title='" . _FORMGOOGLEMAP_ELEVATION . "'>";
            $ret .= '&nbsp;';
            $ret .= "<input type='hidden' id='{$this->_name}[maptypeid]' name='{$this->_name}[maptypeid]' value='{$this->maptypeid}'>";
            $ret .= _FORMGOOGLEMAP_ZOOM;
            $ret .= '&nbsp;';
            $ret .= "<input type='text' id='{$this->_name}[zoom]' name='{$this->_name}[zoom]' value='{$this->zoom}' maxlength='255' size='2' title='Zoom level' onchange='xoopsFormGoogleMap_{$this->_name}.updateZoom();'>";
            $ret .= '</div>';
            $ret .= '<div>';
            $ret .= '<b>' . _FORMGOOGLEMAP_SEARCH . '</b>';
            $ret .= '<br />';
            $ret .= "<input type='text' id='{$this->_name}[search]' name='{$this->_name}[search]' value='' maxlength='255' size='80' title='Search location' >";
            $ret .= "<input type='button' id='{$this->_name}button' name='{$this->_name}button' value='Search' class='formButton' onclick='xoopsFormGoogleMap_{$this->_name}.codeAddress();' title='" . _FORMGOOGLEMAP_SEARCH . "'>";
            $ret .= '<br />';
            $ret .= _FORMGOOGLEMAP_SEARCH_DESC;
            $ret .= '</div>';
        } else {
            $ret .= _FORMGOOGLEMAP_LAT;
            $ret .= '&nbsp;';
            $ret .= "<span id='{$this->name}[lat]'>{$this->lat}</span>";
            $ret .= '&nbsp;';
            $ret .= _FORMGOOGLEMAP_LNG;
            $ret .= '&nbsp;';
            $ret .= "<span id='{$this->name}[lng]'>{$this->lng}</span>";
            $ret .= '&nbsp;';
            $ret .= _FORMGOOGLEMAP_ELEVATION;
            $ret .= '&nbsp;';
            $ret .= "<span id='{$this->name}[elevation]'>{$this->elevation}</span>";
            $ret .= '&nbsp;';
            $ret .= '<br />';
            $ret .= _FORMGOOGLEMAP_ZOOM;
            $ret .= '&nbsp;';
            $ret .= "<span id='{$this->name}[zoom]'>{$this->zoom}</span>";
        }
        return $ret;
    }
}
