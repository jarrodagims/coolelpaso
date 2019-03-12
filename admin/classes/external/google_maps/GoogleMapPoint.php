<?php
class GoogleMapPoint
{
	protected $_name;
	protected $_latitude;
	protected $_longitude;
	protected $_address;
	protected $_city;
	protected $_state;
	protected $_zip;
	protected $_title;
	protected $_description;
	protected $_link;
	protected $_icon;
	protected $_zoomOnMouseover;
	protected $_index;
	public static $total = 0;
	
	public function __construct($latitude = '',$longitude = '',$description = '',$link = '')
	{
		GoogleMapPoint::$total++;
		$this->_index = GoogleMapPoint::$total;
		$this->setName();
		$this->setLatitude($latitude);
		$this->setLongitude($longitude);
		$this->setDescription($description);
		$this->setLink($link);
	}
	public function setName($value = ''){
		if(empty($value)){
			$value = 'google_map_point_'.$this->_index;
		}
		$this->_name = $value;
	}
	public function getName(){ return $this->_name;}
	public function setLatitude($value){ $this->_latitude = $value; return $this; }
	public function getLatitude(){ return $this->_latitude; }
	public function setLongitude($value){ $this->_longitude = $value; return $this; }
	public function getLongitude(){ return $this->_longitude; }
	public function setAddress($value){ $this->_address = $value; return $this; }
	public function getAddress(){ return $this->_address; }
	public function setCity($value){ $this->_city = $value; return $this; }
	public function getCity(){ return $this->_city; }
	public function setState($value){ $this->_state = $value; return $this; }
	public function getState(){ return $this->_state; }
	public function setZip($value){ $this->_zip = $value; return $this; }
	public function getZip(){ return $this->_zip; }
	public function setTitle($value){ $this->_title = $value; return $this; }
	public function getTitle(){ return $this->_title; }
	public function setDescription($value){ $this->_description = $value; return $this; }
	public function getDescription(){ return $this->_description; }
	public function setLink($value){ $this->_link = $value; return $this; }
	public function getLink(){ return $this->_link; }
	public function setIcon($value){ $this->_icon = $value; return $this; }
	public function getIcon(){ return $this->_icon; }
	public function setZoomOnMouseover($value){ $this->_zoomOnMouseOver = $value; return $this; }
	public function getZoomOnMouseOver(){ return $this->_zoomOnMouseOver; }
	public function getFullAddress(){
		return $this->getAddress().' '.$this->getCity().', '.$this->getState().' '.$this->getZip();
	}
	public function toJavascript($mapName)
	{
		if(!strlen(trim($this->getLatitude())) || !strlen(trim($this->getLongitude()))){
			return;
		}
		$container = GoogleMapContainer::getInstance();
		if(!strlen(trim($this->getIcon()))){
			$this->setIcon($container->getPath().$container->getDefaultIcon());	
		}
		ob_start();
		?>
        var image_<?php echo $this->getName(); ?> = '<?php echo $container->getPath().$this->getIcon(); ?>';
       	var marker_<?php echo $this->getName(); ?> = new google.maps.Marker({
            position: new google.maps.LatLng(<?php echo $this->getLatitude(); ?>,<?php echo $this->getLongitude(); ?>),
            map: map_<?php echo $mapName; ?>,
            title: '<?php echo strip_tags($this->getTitle()); ?>',
            icon: image_<?php echo $this->getName(); ?> 
        });
        
        var contentString_<?php echo $this->getName(); ?> = '<?php echo addslashes($this->getDescription()); ?>';
        
      	var infowindow_<?php echo $this->getName(); ?> = new google.maps.InfoWindow({
          	content: contentString_<?php echo $this->getName(); ?> 
      	});
        
        google.maps.event.addListener(marker_<?php echo $this->getName(); ?>, 'click', function() {
            infowindow_<?php echo $this->getName(); ?>.open(map_<?php echo $mapName; ?>,marker_<?php echo $this->getName(); ?>);
        }); 
        <?php
		return ob_get_clean();
	}
}
?>