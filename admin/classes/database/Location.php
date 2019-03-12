<?php
class Location extends Model{
	// Module Config
	public $_moduleName	= 'Locations';
	public $_moduleDir = 'locations';
	public $_moduleTable = 'locations';
	public $_moduleTableCategories = 'location_categories';
	public $_moduleClassName = 'Location';
	public $_moduleCategoryClassName = 'LocationCategory';
	public $_moduleDescription = 'This section allows administrators to create, modify, and delete Locations';
	public $_moduleIcon = 'fa-map-pin';
	public $_moduleImageCanvasX = 830;
	public $_moduleImageCanvasY = 738;
	public $_moduleThumbCanvasX = 225;
	public $_moduleThumbCanvasY = 200;
	public $_moduleCategoryLevelDepth = 2;

	// Static Variables
	protected static $_addLabel = 'Add Location';
	protected static $_editLabel = 'Edit Location';
	protected static $_imageTypes = array(
									'png',
									'jpg'
									);
	// Inherited Variables
	protected $_dbTable	= 'locations';
	protected $_permalinkField = 'permalink';
	protected $_action = 'index';
	protected $_rootUrl = '/location';

	// Table Variables
	protected $_id;
	protected $_name;
	protected $_address;
	protected $_streetNumber;
	protected $_streetName;
	protected $_city;
	protected $_state;
	protected $_zipCode;
	protected $_latitude = '';
	protected $_longitude = '';
	protected $_phoneNumber;
	protected $_permalink;
	protected $_image = '';
	protected $_active = '0';
	protected $_sortOrder = '0';

	// Instance Variables
	protected $_requiredFields = array(
										'category',
										'name',
										'address',
										);
	protected $_saveFields = array(
									'id',
									'category',
									'name',
									'address',
									'street_number',
									'street_name',
									'city',
									'state',
									'zip_code',
									'latitude',
									'longitude',
									'phone_number',
									'permalink',
									'image',
									'active',
									'sort_order'
									);

	// Constructor
	public function __construct($id = 0){
		parent::__construct($id);
	}

	// Static Methods
	public static function setAddLabel($value){self::$_addLabel = (string) $value;}
	public static function getAddLabel(){return self::$_addLabel;}
	public static function setEditLabel($value){self::$_editLabel = (string) $value;}
	public static function getEditLabel(){return self::$_editLabel;}
	public static function getFileTypes(){return self::$_fileTypes;}

	// Accessor Methods
	public function setId($value){$this->_id = (int) $value; return $this;}
	public function getId(){return $this->_id;}
	public function setCategory($value){$this->_category = (int) $value; return $this;}
	public function getCategory(){return $this->_category;}
	public function setName($value){$this->_name = $value; return $this;}
	public function getName(){return $this->_name;}
	public function setAddress($value){$this->_address = $value; return $this;}
	public function getAddress(){return $this->_address;}
	public function setStreetNumber($value){$this->_streetNumber = $value; return $this;}
	public function getStreetNumber(){return $this->_streetNumber;}
	public function setStreetName($value){$this->_streetName = $value; return $this;}
	public function getStreetName(){return $this->_streetName;}
	public function setCity($value){$this->_city = $value; return $this;}
	public function getCity(){return $this->_city;}
	public function setState($value){$this->_state = $value; return $this;}
	public function getState(){return $this->_state;}
	public function setZipCode($value){$this->_zipCode = $value; return $this;}
	public function getZipCode(){return $this->_zipCode;}
	public function setLatitude($value){$this->_latitude = $value; return $this;}
	public function getLatitude(){return $this->_latitude;}
	public function setLongitude($value){$this->_longitude = $value; return $this;}
	public function getLongitude(){return $this->_longitude;}
	public function setPhoneNumber($value){$this->_phoneNumber = $value; return $this;}
	public function getPhoneNumber(){return $this->_phoneNumber;}
	public function setPermalink($value){$this->_permalink = $value; return $this;}
	public function getPermalink(){return $this->_permalink;}
	public function setImage($value){$this->_image = $value; return $this;}
	public function getImage(){return $this->_image;}
	public function setActive($value){$this->_active = $value; return $this;}
	public function getActive(){return $this->_active;}
	public function setSortOrder($value){$this->_sortOrder = $value; return $this;}
	public function getSortOrder(){return $this->_sortOrder;}

	// Instance Methods
	public function setHrefPrefix($value){$this->_hrefPrefix = (string) $value; return $this;}
	public function getHrefPrefix(){return $this->mysqlPrep($this->_hrefPrefix);}
	public function setSaveFields($value){$this->_saveFields = $value; return $this;}
	public function getSaveFields(){return $this->_saveFields;}

	public function setCategoryObject($categoryObject = ''){
		if(is_object($categoryObject) && get_class($categoryObject) == $this->_moduleCategoryClassName){
			$this->_categoryObject = $categoryObject;
		}elseif(strlen(trim($this->getCategory()))){
			$this->_categoryObject = new $this->_moduleCategoryClassName($this->getCategory());
		}
		return $this;
	}

	public function getCategoryObject(){
		if($this->_categoryObject === NULL && strlen(trim($this->getCategory()))){
			$this->setCategoryObject();
		}
		return $this->_categoryObject;
	}

	public function install(){
		# Register module
		$this->register();

		# Create tables
		if(!$this->isInstalled()){
			$query = "
			CREATE TABLE `".$this->_moduleTableCategories."` (
			 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			 `name` varchar(255) NOT NULL DEFAULT '',
			 `permalink` varchar(255) NOT NULL DEFAULT '',
			 `sort_order` int(10) unsigned NOT NULL DEFAULT '0',
			 PRIMARY KEY (`id`),
			 KEY `permalink` (`permalink`) USING BTREE
			) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8
			";
			$result = $this->create($query);

			$query = "
			CREATE TABLE `".$this->_dbTable."` (
			 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			 `category` int(10) unsigned NOT NULL DEFAULT '0',
			 `name` varchar(255) NOT NULL DEFAULT '',
			 `address` varchar(255) NOT NULL DEFAULT '',
			 `street_number` varchar(255) NOT NULL DEFAULT '',
			 `street_name` varchar(255) NOT NULL DEFAULT '',
			 `city` varchar(255) NOT NULL DEFAULT '',
			 `state` varchar(255) NOT NULL DEFAULT '',
			 `zip_code` varchar(255) NOT NULL DEFAULT '',
			 `latitude` varchar(255) NOT NULL DEFAULT '',
			 `longitude` varchar(255) NOT NULL DEFAULT '',
			 `phone_number` varchar(255) NOT NULL DEFAULT '',
			 `permalink` varchar(255) NOT NULL DEFAULT '',
			 `image` varchar(255) NOT NULL,
			 `active` enum('0','1') NOT NULL DEFAULT '0',
			 `sort_order` int(10) unsigned NOT NULL DEFAULT '0',
			 PRIMARY KEY (`id`),
			 KEY `category` (`category`) USING BTREE,
			 KEY `permalink` (`permalink`) USING BTREE,
			 KEY `featured` (`active`) USING BTREE
			) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8
			";
			$result = $this->create($query);
		}

		return $this;
	}

	public function buildHref(){
		return $this->_rootUrl.'/'.$this->getPermalink();
	}

	public function buildLink(){
		if(preg_match('#^http[s]?://#',$this->buildHref())){
			$external = 'class="external"';
		}else{
			$external = '';
		}
		return '<a href="'.$this->buildHref().'" '.$external.'>'.process($this->getName()).'</a>';
	}

	public function buildImage(){
		return '<img src="/'.$this->getFilePath().'/'.$this->getDbTable().$this->getId().'.'.$this->getImage().'" class="image" alt="'.process($this->getName()).'" />';
	}

	public function buildImageThumbnail(){
		return '<img src="/'.$this->getFilePath().'/'.$this->getDbTable().$this->getId().'_t.'.$this->getImage().'" class="thumbnail_image" alt="'.process($this->getName()).'" />';
	}

	public function validate(){
		if(!$this->checkRequired()){
			$this->addMessage('general',array('type'=>'failure','text'=>'Please complete all <b>required</b> fields'));
		}
		if(!strlen(trim($this->getLatitude())) || !strlen(trim($this->getLongitude()))){
			$this->addMessage('address', array('type'=>'failure', 'text'=>'Invalid Address - Latitude and Longitude could not be calculated'));
		}
		if($this->hasMessages() && !$this->hasMessage('general')){
			$this->addMessage('general',array('type'=>'failure','text'=>'Your submission contains errors<br />Please correct them and try again'));
		}
	}

	public function listCategories($selected){
		$values = array();
		$labels = array();
		$categoryClass = new $this->_moduleCategoryClassName();
		$categories = $categoryClass->fetchAll("","ORDER BY `sort_order`");
		if(!sizeof($categories)){
		}else{
			foreach($categories as $category){
				$values[] = $category->getId();
				$labels[] = $category->getName();
			}
		}
		$name = "category";
		return FormComponent::selectList($name,$values,$labels,$selected,$name);
	}

	// Action Methods
	public function moduleIndexAction(){

		$moduleCategoryClass = new $this->_moduleCategoryClassName();

		ob_start();?>

		<div class="index-wrapper">
			<div class="row">
				<div class="col-xs-12">
					<a class="btn btn-green pull-left" href="<?php echo $moduleCategoryClass->buildModalUrl('add'); ?>" data-toggle="modal" data-target="#moduleModal"><i class="fa fa-plus"></i><?php echo $moduleCategoryClass->_addLabel; ?></a>
				</div>
			</div>
			<?php
			echo $moduleCategoryClass->buildSortingStructure();
			echo $this->buildAdminListJavascript();
			?>
		</div>

		<?php
		return ob_get_clean();
	}

	public function defaultListAction(){
		ob_start(); ?>
			<div id="<?php echo $this->_moduleClassName; ?>_<?php echo $this->getId(); ?>" class="menuDiv input-group">
				<span title="Drag item to change sort order" class="input-group-addon draghandle">
					<i class="fa fa-arrows" aria-hidden="true"></i>
				</span>
				<div class="branch_content">
					<span title="Click to show/hide sub-items" class="disclose hidden">
						<i class="fa fa-chevron-circle-down" aria-hidden="true"></i>
					</span>
					<span data-id="<?php echo $this->getId(); ?>" class="itemTitle"><?php echo process($this->getName()); ?></span>
					<span data-id="<?php echo $this->getId(); ?>" class="action_menu">
						<a tabindex="0" class="pull-right" role="button" data-toggle="popover" data-trigger="focus" data-placement="bottom" data-content='<?php echo $this->toHtml('menu'); ?>'><i class="fa fa-gear"></i></a>
					</span>
					<?php echo $this->buildToggleButton(); ?>
				</div>
			</div>
        <?php
		return ob_get_clean();
	}

	public function menuAction(){
		ob_start(); ?>
        <ul class="actions">
        	<li><a href="<?php echo $this->buildModalUrl('edit'); ?>" data-toggle="modal" data-target="#moduleModal"><i class="fa fa-pencil"></i>Edit</a></li>
        	<li><a href="<?php echo $this->buildModalUrl('confirm','delete_confirm'); ?>" class="delete" data-toggle="modal" data-target="#confirmModal"><i class="fa fa-trash-o"></i>Delete</a></li>
		</ul>
        <?php
		$html = ob_get_clean();
		return $html;
	}

	public function indexAction(){
		ob_start();
		?>

        <?php
		return ob_get_clean();
	}

	public function listingAction(){
		ob_start();
		?>

        <?php
		return ob_get_clean();
	}

	public function featuredAction(){
		ob_start();
		?>

        <?php
		return ob_get_clean();
	}

	public function adminNewRecord($category){
		$this->setCategory($category);
	}

	public function adminAddAction(){
		return $this->buildAdminAddEditHtml('add');
	}

	public function adminEditAction(){
		return $this->buildAdminAddEditHtml('edit');
	}

	protected function buildAdminAddEditHtml($action){
		if(!in_array($action,array('add','edit'))){
			return '';
		}
		$actionLabel = $this->{'get'.ucfirst($action).'Label'}();
		//
		$messages = $this->prepareMessages();
		$this->clearMessages();
		//
		ob_start();
		?>
       	<div class="error general"></div>
		<form id="form" action="action.php" method="post" enctype="multipart/form-data">
			<div class="row">
				<div class="col-sm-12">
					<div class="denote-required">
						<i class="fa fa-asterisk"></i>
						Denotes a required field
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-6">
					<!-- Category -->
					<div class="form-group">
						<label for="category" class="required">Category</label><br>
						<?php echo $this->listCategories($this->getCategory()); ?>
						<div class="error category"></div>
					</div>
					<!-- Name -->
					<div class="form-group">
						<label for="name" class="required">Name</label>
						<input type="text" name="name" class="form-control required" value="<?php echo process($this->getName()); ?>" placeholder="Name" />
						<div class="error name"></div>
					</div>
					<!-- Address -->
					<div class="form-group">
						<label for="address" class="required">Address</label>
						<input type="text" name="address" id="address" class="form-control required" value="<?php echo process($this->getAddress()); ?>" placeholder="Address" onFocus="geolocate()" />
					</div>
					<div class="panel panel-custom">
						<div class="panel-heading"><i class="fa fa-map-pin"></i>Address Information</div>
						<div class="panel-body">
							<small class="instructions">These are calulated based on the location address.</small>
							<div class="col-sm-5">
								<!-- Street Number -->
								<div class="form-group">
									<label for="street_number">Street Number</label>
									<input type="text" name="street_number" id="street_number" class="form-control" value="<?php echo process($this->getStreetNumber()); ?>" placeholder="Street Number" readonly />
								</div>
							</div>
							<div class="col-sm-7">
								<!-- Street Name -->
								<div class="form-group">
									<label for="longitude">Street Name</label>
									<input type="text" name="street_name" id="route" class="form-control" value="<?php echo process($this->getStreetName()); ?>" placeholder="Street Name" readonly />
								</div>
							</div>
							<div class="col-sm-12">
								<!-- City -->
								<div class="form-group">
									<label for="city">City</label>
									<input type="text" name="city" id="locality" class="form-control" value="<?php echo process($this->getCity()); ?>" placeholder="City" readonly />
								</div>
							</div>
							<div class="col-sm-3">
								<!-- State -->
								<div class="form-group">
									<label for="state">State</label>
									<input type="text" name="state" id="administrative_area_level_1" class="form-control" value="<?php echo process($this->getState()); ?>" placeholder="State" readonly />
								</div>
							</div>
							<div class="col-sm-9">
								<!-- Zip Code-->
								<div class="form-group">
									<label for="zip_code">Zip Code</label>
									<input type="text" name="zip_code" id="postal_code" class="form-control" value="<?php echo process($this->getZipCode()); ?>" placeholder="Zip Code" readonly />
								</div>
							</div>
							<div class="col-sm-6">
								<!-- Latitude -->
								<div class="form-group">
									<label for="latitude">Latitude</label>
									<input type="text" name="latitude" id="latitude" class="form-control" value="<?php echo process($this->getLatitude()); ?>" placeholder="Latitude" readonly />
								</div>
							</div>
							<div class="col-sm-6">
								<!-- Longitude -->
								<div class="form-group">
									<label for="longitude">Longitude</label>
									<input type="text" name="longitude" id="longitude" class="form-control" value="<?php echo process($this->getLongitude()); ?>" placeholder="Longitude" readonly />
								</div>
							</div>
						</div>
					</div>
					<!-- Phone Number -->
					<div class="form-group">
						<label for="phone_number" class="">Phone Number</label>
						<input type="text" name="phone_number" class="form-control" value="<?php echo process($this->getPhoneNumber()); ?>" placeholder="Phone Number" />
						<div class="error phone_number"></div>
					</div>
				</div>
				<div class="col-sm-6">
					<!-- Image -->
					<?php $image = new Image(); echo $image->manageImage($this->getDbTable(),$this->getId(),1,$this->getImage(),$this->_moduleImageCanvasX,$this->_moduleImageCanvasY); ?>
				</div>
			</div>
			<?php if($action == 'edit'): ?>
			<input type="hidden" name="id" value="<?php echo $this->getId(); ?>" />
			<?php endif; ?>
			<input type="hidden" name="action" value="<?php echo $action; ?>" />
		</form>
		<?php

		$form = ob_get_clean();

		$modal = new Module();
		return $modal->buildInnerModal($actionLabel, $form);
	}

	public function buildAdminListJavascript(){
		ob_start();?>
		<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAj9EHc0cGbca7tInwY7KmAN7R-nqnt3yM&libraries=places"
        async defer></script>
		<script>
			function moduleSpecificRefresh(content ='',action){
				if(action === 'add'){
					initSortable();
					initPopOver();
					toggleVisible();
				}
				if(action === 'edit'){
					initSortable();
					initPopOver();
				}
				else if(action === 'delete'){
					toggleVisible();
				}
			}
			function initSortable() {
				var ns = $('ol.sortable').nestedSortable({
					forcePlaceholderSize: true,
					handle: '.draghandle',
					helper:	'clone',
					items: 'li',
					opacity: .6,
					placeholder: 'placeholder',
					revert: 250,
					tabSize: 25,
					tolerance: 'pointer',
					toleranceElement: '> div',
					maxLevels: 3,
					isTree: true,
					expandOnHover: 700,
					protectRoot: <?php echo ($this->_moduleCategoryLevelDepth > 1 ? 'true' : 'false'); ?>,
					startCollapsed: true,
					<?php if($this->_moduleCategoryLevelDepth > 1){?>
					isAllowed: function(placeholder, placeholderParent, currentItem){
					window.currentId = currentItem[0].id.split("_")[1];
					window.module = currentItem[0].className.split(" ")[1];
					window.lvl = currentItem[0].className.split(" ")[0].replace("lvl_", "");
						if (placeholderParent == null){
							return true;
						}else{
							var parentLevel=placeholderParent[0].className.split(" ")[0];
							parentLevel=parseInt(parentLevel.split("_")[1])+1;
							var movingLevel=currentItem[0].className.split(" ")[0];
							movingLevel=parseInt(movingLevel.split("_")[1]);
							if(parentLevel == movingLevel){return true;}else{return false;}
						}
					},
					<?php } ?>
					relocate: function(){
						toggleVisible();
						$.ajax({
							type: 'POST',
							url:"../../includes/sort_list.php",
							data: $('ol.sortable').nestedSortable('serialize')+"&id="+currentId+"&module="+module+"&lvl="+lvl,
							success:function(data) {
								console.log(data);
							}
						});
					}
				});
			}

			function initAutocomplete() {
				// Create the autocomplete object, restricting the search to geographical
				// location types.
				autocomplete = new google.maps.places.Autocomplete(
				/** @type {!HTMLInputElement} */(document.getElementById('address')),
				{types: ['geocode']});

				// When the user selects an address from the dropdown, populate the address
				// fields in the form.
        		autocomplete.addListener('place_changed', fillInAddress);
			}

			function fillInAddress() {
				// Get the place details from the autocomplete object.
				var place = autocomplete.getPlace();
				// Get Latitude
				var latitude = place.geometry.location.lat();
				// Get Longitude
				var longitude = place.geometry.location.lng();
				// Fill values
				$("#latitude").val(latitude);
				$("#longitude").val(longitude);
				// Get each component of the address from the place details
				// and fill the corresponding field on the form.
				for (var i = 0; i < place.address_components.length; i++) {
					var addressType = place.address_components[i].types[0];
					$("#"+addressType).val(place.address_components[i].short_name);
				}
			}

			// Bias the autocomplete object to the user's geographical location,
			// as supplied by the browser's 'navigator.geolocation' object.
			function geolocate() {
				if (navigator.geolocation) {
					navigator.geolocation.getCurrentPosition(function(position) {
						var geolocation = {
							lat: position.coords.latitude,
							lng: position.coords.longitude
						};
						var circle = new google.maps.Circle({
							center: geolocation,
							radius: position.coords.accuracy
						});
						autocomplete.setBounds(circle.getBounds());
					});
				}
			}

			$(function(){
				$('#moduleModal').on('shown.bs.modal', function () {
					initAutocomplete();
				});
			});

			$(document).ready(function(e) {
				initSortable();
			});
		</script>
			<?php return ob_get_clean();
	}
}

?>
