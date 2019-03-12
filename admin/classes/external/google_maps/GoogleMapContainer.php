<?php
class GoogleMapContainer
{
	private static $mapInstance;
	protected $_liveApiKey = 'AIzaSyAGT29IgFotIjeX1MKVuTxf6mHeeFB3RFQ';
	protected $_testApiKey = '';
	protected $_version = 3;
	protected $_mode = 'live';
	protected $_path;
	protected $_defaultIcon = 'default_icon.png';
	protected $_maps = array();
	protected $_icons = array();
	
	public function __construct()
	{
		$this->setPath('http://'.$_SERVER['HTTP_HOST'].'/includes/classes/v3/');
	}
	public static function getInstance()
    {
        if (!self::$mapInstance)
        {
            self::$mapInstance = new GoogleMapContainer();
        }

        return self::$mapInstance;
    } 
	public function setLiveApiKey($value){ $this->_liveApiKey = $value; return $this; }
	public function getLiveApiKey(){ return $this->_liveApiKey; }
	public function setTestApiKey($value){ $this->_testApiKey = $value; return $this; }
	public function getTestApiKey(){ return $this->_testApiKey; }
	public function setVersion($value){ $this->_version = $value; return $this; }
	public function getVersion(){ return $this->_version; }
	public function setMode($value){ $this->_mode = $value; return $this; }
	public function getMode(){ return $this->_mode; }
	public function setPath($value){ $this->_path = $value; return $this; }
	public function getPath(){ return $this->_path; }
	public function setDefaultIcon($value){ $this->_defaultIcon = $value; return $this; }
	public function getDefaultIcon(){ return $this->_defaultIcon; }
	public function addMap(GoogleMap $map)
	{
		$this->_maps[] = $map;
		return $this;
	}
	public function getMaps(){ return $this->_maps; }
	public function addIcon($name,$image){
		$this->_icons[$name] = $image;
		return $this;
	}
	public function getIcons(){ return $this->_icons; }
	public function getAPIKey(){
		if($this->getMode() == 'live'){
			return $this->getLiveAPIKey();
		}elseif($this->getMode() == 'test'){
			return $this->getTestAPIKey();
		}	
	}
	//
	public function toCSS(){
		foreach($this->getMaps() as $map){
			$return .= $map->toCSS();
		}
		return $return;
	}
	public static function toJavascript(){
		$container = GoogleMapContainer::getInstance();
		ob_start();
		?>
		<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $container->getAPIKey(); ?>&sensor=false"></script>
		<script type="text/javascript">
		function initialize() {
			<?php
			foreach($container->getMaps() as $map){
				echo $map->toJavascriptInitialize();
			}
			?>	
		}
		google.maps.event.addDomListener(window, 'load', initialize);
		</script>
        <?php
		foreach($container->getMaps() as $map){
			echo $map->toJavascript();
		}
		?>
		<?php
		$str = ob_get_clean();
		return $str;
	}
	public static function getCoordinates($address="",$city="",$state="",$zip=""){
		$container = GoogleMapContainer::getInstance();
		//
		$address=urlencode($address);
		$city = urlencode($city);
		$state = urlencode($state);
		$zip = urlencode($zip);
		//
		$url = 'http://maps.googleapis.com/maps/api/geocode/json?address='.$address.','.$city.',+'.$state.'+'.$zip.'&sensor=false';
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		$results = curl_exec($ch);
		curl_close($ch);
		//
		$results = json_decode($results,true);
		$results = $results['results'][0]['geometry'];
		$coordinates = $results['location'];
		//
		return array('latitude'=>$coordinates['lat'],'longitude'=>$coordinates['lng']);
	}
	
	public function zipRadiusQuery($zip,$selected_mile_radius,$table, $filter = ''){
		extract($this->getCoordinates('','','',$zip));
		/* SCRIPT ACTION:
		 * Query the database for all the retailers that are within $selected_mile_radius of the returned latitude and longitude ordering from closest to farthest
		 *
		 * Distance function found here: www.torontohealthprofiles.ca/documents/resources/Methods.pdf
		 * or here: http://209.85.173.132/search?q=cache:Zbx_pCt8JwEJ:www.torontohealthprofiles.ca/documents/resources/Methods.pdf+calculate+distance+between+two+%22decimal+coordinates%22&cd=1&hl=en&ct=clnk&gl=us
		 *
		 * Radius of the Earth
		 * radius(in km) = 6378 - 21 * sin(lat)
		 * radius(in mi) = 3963 - 13 * sin(lat)
		 * where lat is a latitude of the area. 
		 *
		 * Law of Cosines for Spherical Trigonometry
		 * a = sin(lat1) * sin(lat2)
		 * b = cos(lat1) * cos(lat2) * cos(lon2 - lon1)
		 * c = arccos(a + b)
		 * d = R * c
		 * where R is the radius of the earth. 
		 */
	
		# Prepare SQL distance formula
		$lat1 = "(".deg2rad($latitude).")";
		$lon1 = "(".deg2rad($longitude).")";
		$lat2 = "(radians(latitude))";
		$lon2 = "(radians(longitude))";
	
		# Radius of the Earth
		# R(in mi) = 3963 - 13 * sin(lat)
		$R = (3963 - 13 * sin(deg2rad($latitude)));
	
		# Law of Cosines for Spherical Trigonometry
		# d = R * c
		$a = "sin($lat1) * sin($lat2)";
		$b = "cos($lat1) * cos($lat2) * cos($lon2 - $lon1)";
		$c = "acos($a + $b)";
		$d = "$R * $c";
		$query = "SELECT *, ($d) as distance FROM $table HAVING (distance<=$selected_mile_radius) OR zip='$zip' $filter ORDER BY distance";
		return $query;
	}
}

?>