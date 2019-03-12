<?php
class GoogleMap
{
	# Google Map Instance Variables
	protected $_name;
	protected $_centerLatitude = '37.686028'; // Wichita, Kansas (center of U.S.)
	protected $_centerLongitude = '-97.336445'; // Wichita, Kansas (center of U.S.)
	protected $_zoom = '14';
	protected $_size = 'normal'; //normal, small
	protected $_type = 'ROADMAP'; //HYBRID, ROADMAP, SATELLITE, TERRAIN
	protected $_travelMode = 'DRIVING'; //DRIVING,
	protected $_to;
	protected $_from;
	protected $_points = array();
	protected $_directions = array();
	protected $_index;
	protected $_displayType = '';

	public static $total = 0;
	
	# XHTML/CSS Instance Variables
	protected $_css = array(
	'width'=>'200px',
	'height'=>'200px',
	'border'=>'1px solid #999999',
	'display'=>'block',
	'overflow'=>'hidden'
	);
	//
	public function __construct($name = ''){
		GoogleMap::$total++;
		$this->_index = GoogleMap::$total;
		$this->setName($name);
		GoogleMapContainer::getInstance()->addMap($this);
	}
	public function setName($value = ''){
		if(empty($value)){
			$value = 'google_map_'.$this->_index;
		}
		$this->_name = $value;
	}
	public function getName(){ return $this->_name;}
	public function setCenterLatitude($value){ $this->_centerLatitude = $value; return $this; }
	public function getCenterLatitude(){ return $this->_centerLatitude; }
	public function setCenterLongitude($value){ $this->_centerLongitude = $value; return $this; }
	public function getCenterLongitude(){ return $this->_centerLongitude; }
	public function setZoom($value){ $this->_zoom = $value; return $this;}
	public function getZoom(){ return $this->_zoom; }
	public function setSize($value){ $this->_size = $value; return $this;}
	public function getSize(){ return $this->_size; }
	public function setType($value){ $this->_type = $value; return $this; }
	public function getType(){ return $this->_type; }
	public function setTravelMode($value){ $this->_travelMode = $value; return $this; }
	public function getTravelMode(){ return $this->_travelMode; }
	public function setTo($value){ $this->_to = $value; return $this; }
	public function getTo(){ return $this->_to; }
	public function setFrom($value){ $this->_from = $value; return $this; }
	public function getFrom(){ return $this->_from; }
	//
	public function addPoint(GoogleMapPoint $point){ $this->_points[] = $point; }
	public function getPoints(){ return $this->_points; }
	public function setDirections($from,$to,$useAddress = true){
		if(get_class($from) == 'GoogleMapPoint'){
			if($useAddress){
				$from = $from->getFullAddress();
			}else{
				$from = $from->getLatitude().','.$from->getLongitude();
			}
		}
		if(get_class($to) == 'GoogleMapPoint'){
			if($useAddress){
				$to = $to->getFullAddress();
			}else{
				$to = $to->getLatitude().','.$to->getLongitude();
			}
		}
		if($useAddress){
			$this->_directions = array('from'=>$from,'to'=>$to,'use_address'=>$useAddress);
		}else{
			$this->_directions = array('from'=>$from,'to'=>$to,'use_address'=>$useAddress);
		}
	}
	public function getDirections($key = ''){
		if(strlen(trim($key))){
			return $this->_directions[$key];
		}else{
			return $this->_directions;
		}
	}
	//
	public function removeDirections(){
		$this->_directions = array();
	}
	public function setCSSProperty($property,$value){
		$this->_css[$property] = $value;
	}
	public function getCSSProperty($property){
		return $this->_css[$property];
	}
	public function removeCSSProperty($property){
		unset($this->_css[$property]);
	}
	public function toHTML(){
		ob_start();
		?>
		<div id="<?php echo $this->getName(); ?>"></div>
        <?php
		return ob_get_clean();
	}
	public function toHTMLDirections()
	{
		ob_start();
		?>
		<div id="directions_<?php echo $this->getName(); ?>"></div>
		<?php
		return ob_get_clean();
	}
	public function toCSS()
	{
		ob_start();
		?>
		#<?php echo $this->getName(); ?>{
        <?php
		foreach($this->_css as $property=>$value){
			echo $property.':'.$value.';
			';
		}
		?>
		}
        <?php
		return ob_get_clean();
	}
	
	public function toJavascriptInitialize(){
		ob_start();
		?>
        initialize_<?php echo $this->getName(); ?>();
        <?php
		return ob_get_clean();	
	}
	
	public function toJavascript(){
		ob_start();
		?>
        <script type="text/javascript">
		// Map_<?php echo $this->getName(); ?> 
		function initialize_<?php echo $this->getName(); ?>(){
			directionsDisplay_<?php echo $this->getName(); ?> = new google.maps.DirectionsRenderer();
			directionsService_<?php echo $this->getName(); ?> = new google.maps.DirectionsService();
			load_<?php echo $this->getName(); ?>();
		}
        // End Map_<?php echo $this->getName(); ?>
		
		// Load map
		function load_<?php echo $this->getName(); ?>(){
			var mapOptions_<?php echo $this->getName(); ?> = {
				mapTypeId: google.maps.MapTypeId.<?php echo $this->getType(); ?>,
				center: new google.maps.LatLng(<?php echo $this->getCenterLatitude(); ?>,<?php echo $this->getCenterLongitude(); ?>),
				zoom: <?php echo $this->getZoom(); ?>
			};
			var map_<?php echo $this->getName(); ?> = new google.maps.Map($("#<?php echo $this->getName(); ?>").get(0),mapOptions_<?php echo $this->getName(); ?>);
			<?php echo $this->toJavascriptPoints(); ?>
			directionsDisplay_<?php echo $this->getName(); ?>.setMap(map_<?php echo $this->getName(); ?>);
			directionsDisplay_<?php echo $this->getName(); ?>.setPanel(document.getElementById('directions_<?php echo $this->getName(); ?>'));
		}
		//
		
        // Bind directions
        function directions_<?php echo $this->getName(); ?>(){
            var start = $("#from_<?php echo $this->getName(); ?>").val();
            var end = $("#to_<?php echo $this->getName(); ?>").val();
            var request = {
                  origin:start,
                  destination:end,
                  travelMode: google.maps.TravelMode.<?php echo $this->getTravelMode(); ?>
              };
              directionsService_<?php echo $this->getName(); ?>.route(request, function(response, status) {
                if (status == google.maps.DirectionsStatus.OK) {
                  directionsDisplay_<?php echo $this->getName(); ?>.setDirections(response);
                }
              });
			  return false;
        }
		// End directions
		
		// Clear directions
		function clear_<?php echo $this->getName(); ?>(){
			$("#directions_<?php echo $this->getName(); ?>").html('');
			initialize_<?php echo $this->getName(); ?>();
			return false;	
		}
		// End directions
		
		</script>
		<?php
		return ob_get_clean();
	}
	protected function toJavascriptPoints(){
		foreach($this->getPoints() as $point){
			$return .= ''.
			$point->toJavascript($this->getName()).'
			';
		}
		return $return;
	}
	public function calculateCenter(){
		foreach($this->getPoints() as $point){
			$latitude += $point->getLatitude();
			$longitude += $point->getLongitude();
		}
		$this->setCenterLatitude($latitude/sizeof($this->getPoints()));
		$this->setCenterLongitude($longitude/sizeof($this->getPoints()));
		return $this;
	}
	public function getMinLon(){
		$min = '';
		foreach($this->getPoints() as $point){
			if($min == ''){
				$min = $point->getLongitude();	
			}elseif($point->getLongitude() < $min){
				$min = $point->getLongitude();	 
			}
		}	
		return $min;
	}
	public function getMinLat(){
		$min = '';
		foreach($this->getPoints() as $point){
			if($min == ''){
				$min = $point->getLatitude();	
			}elseif($point->getLatitude() < $min){
				$min = $point->getLatitude();	 
			}
		}		
		return $min;
	}
	public function getMaxLon(){
		$max = '';
		foreach($this->getPoints() as $point){
			if($max == ''){
				$max = $point->getLongitude();	
			}elseif($point->getLongitude() > $max){
				$max = $point->getLongitude();	 
			}
		}		
		return $max;
	}
	public function getMaxLat(){
		$max = '';
		foreach($this->getPoints() as $point){
			if($max == ''){
				$max = $point->getLatitude();	
			}elseif($point->getLatitude() > $max){
				$max = $point->getLatitude();	 
			}
		}	
	}
	
	public function autoZoom(){
		$min_lat = $this->getMinLat();
		$max_lat = $this->getMaxLat();
		$min_lon = $this->getMinLon();
		$max_lon = $this->getMaxLon();
		$miles = intval((3958.75 * acos(sin($min_lat / 57.2958) * sin($max_lat / 57.2958) + cos($min_lat / 57.2958) * cos($max_lat / 57.2958) * cos($max_lon / 57.2958 - $min_lon / 57.2958)))*100);
		switch($miles){
			case 0:
				$this->_zoom = 15;
				break;
			case ($miles < 20):
				$this->_zoom = 17;
				break;
			case ($miles < 50):
				$this->_zoom = 16;
				break;
			case ($miles < 100):
				$this->_zoom = 15;
				break;
			case ($miles < 250):
				$this->_zoom = 14;
				break;
			case ($miles < 700):
				$this->_zoom = 13;
				break;
			case ($miles < 1100):
				$this->_zoom = 12;
				break;
			case ($miles < 1500):
				$this->_zoom = 11;
				break;
			case ($miles >= 1500):
				$this->_zoom = 11;
				break;
		}
		//$zoom = round(pow($miles,2)-(33.1*$miles)+247.02);
		return;
	}
	
	function setDisplayType($value) { $this->_displayType = $value; return $this; }
	function getDisplayType() { return $this->_displayType; }	
}
?>