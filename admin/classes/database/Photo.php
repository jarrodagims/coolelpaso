<?php
class Photo extends Model{
	// Module Configuration
	public $_moduleName  = 'Photo Gallery';
	public $_moduleDir   = 'photo_gallery';
	public $_moduleTable = 'photos';
	public $_moduleTableCategories = 'photos_categories';
	public $_moduleClassName = 'Photo';
	public $_moduleCategoryClassName = 'PhotoCategory';
	public $_moduleDescription = 'This section allows administrators to create, modify, and delete Photo Galleries.';
	public $_moduleIcon = 'fa-picture-o';
	public $_moduleImageCanvasX = 830;
	public $_moduleImageCanvasY = 738;
	public $_moduleThumbCanvasX = 225;
	public $_moduleThumbCanvasY = 200;
	public $_moduleFeaturedLimit = 3;
	public $_moduleCategoryLevelDepth = 2;
	
	// Static Variables
	protected static $_addLabel = 'Add Photo';
	protected static $_editLabel = 'Edit Photo';

	// Inherited Variables
	protected $_filePath = 'files/photo_gallery/';
	protected $_dbTable = 'photos';
	protected $_permalinkField = 'permalink';
	protected $_action = 'index';
	
	// Table Variables
	protected $_id;
	protected $_category;
	protected $_name = '';
	protected $_url = '';
	protected $_caption = '';
	protected $_photographer = '';
	protected $_date = '0000-00-00';
	protected $_permalink = '';
	protected $_extension = '';
	protected $_sortOrder = '0';
	
	// Instance Variables
	protected $_requiredFields = array(
									'name'
									);
	protected $_saveFields = array(
									'category',
									'name',
									'url',
									'caption',
									'photographer',
									'date',
									'permalink',
									'extension',
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
	
	// Accessor Methods
	public function setId($value){$this->_id = (int) $value; return $this;}
	public function getId(){return $this->_id;}
	public function setCategory($value){$this->_category = (int) $value; return $this;}
	public function getCategory(){return $this->_category;}
	public function setName($value){$this->_name = (string) $value; return $this;}
	public function getName(){return $this->_name;}
	public function setUrl($value){$this->_url = (string) $value; return $this;}
	public function getUrl(){return $this->_url;}
	public function setCaption($value){$this->_caption = (string) $value; return $this;}
	public function getCaption(){return $this->_caption;}
	public function setPhotographer($value){$this->_photographer = (string) $value; return $this;}
	public function getPhotographer(){return $this->_photographer;}
	public function setDate($value){$this->_date = $this->formatDate((string) $value); return $this;}
	public function getDate(){return $this->_date;}
	public function setPermalink($value){$this->_permalink = (string) $value; return $this;}
	public function getPermalink(){return $this->_permalink;}
	public function setExtension($value){$this->_extension = (string) $value; return $this;}
	public function getExtension(){return $this->_extension;}
	public function setSortOrder($value){$this->_sortOrder = (int) $value; return $this;}
	public function getSortOrder(){return $this->_sortOrder;}
	
	// Instance Methods
	public function setSaveFields($value){$this->_saveFields = $value; return $this; }
	public function getSaveFields(){return $this->_saveFields; }

	
	public function install(){
		# Register module
		$this->register($this->_moduleCategoryClassName);
		
		# Create tables
		if(!$this->isInstalled()){
			$query = "
			CREATE TABLE `".$this->_moduleTableCategories."` (
			 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			 `name` varchar(255) NOT NULL DEFAULT '',
			 `description` text NOT NULL,
			 `permalink` varchar(255) NOT NULL DEFAULT '',
			 `active` enum('0','1') NOT NULL DEFAULT '0',
			 `sort_order` int(10) unsigned NOT NULL DEFAULT '0',
			 PRIMARY KEY (`id`),
			 KEY `permalink` (`permalink`)
			) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8
			";
			$result = $this->create($query);
			
			$query = "
			CREATE TABLE `".$this->_dbTable."` (
			 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			 `category` int(10) unsigned NOT NULL DEFAULT '0',
			 `name` varchar(255) NOT NULL DEFAULT '',
			 `url` varchar(255) NOT NULL DEFAULT '',
			 `caption` varchar(255) NOT NULL DEFAULT '',
			 `photographer` varchar(255) NOT NULL DEFAULT '',
			 `date` date NOT NULL DEFAULT '0000-00-00',
			 `permalink` varchar(255) NOT NULL DEFAULT '',
			 `extension` varchar(255) NOT NULL,
			 `sort_order` int(10) unsigned NOT NULL DEFAULT '0',
			 PRIMARY KEY (`id`),
			 KEY `category` (`category`),
			 KEY `permalink` (`permalink`)
			) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8
			";
			$result = $this->create($query);
		}
		
		return $this;
	}
	
	public function validate(){
		$includeGeneral = 0;
		// Check required fields
		foreach($this->getRequiredFields() as $field){
			if(!strlen(trim($this->__get($field)))){
				$includeGeneral = 1;
				$this->addMessage($field,array('type'=>'failure','text'=>'Required'));
			}
		}
		if($includeGeneral){
			$this->addMessage('general',array('type'=>'failure','text'=>'Please complete all required fields'));
		}
		return $this;
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
		return FormComponent::selectList($name,$values,$labels,$selected,$name,false,'selectpicker');
	}
	
	// Action Methods
	public function indexAction(){
		ob_start();
		?>
         <img src="/files/photo_gallery<?php echo $this->getId().'.'.$this->getExtension(); ?>" class="portfolio_detail_image" alt="<?php echo $this->getCaption(); ?>" />
        <?php
		return ob_get_clean();
	}

	public function adminAddAction(){
		return $this->buildAdminAddEditHtml('add');
	}
	
	public function adminEditAction(){
		return $this->buildAdminAddEditHtml('edit');
	}
	
	protected function buildAdminAddEditHtml($action,$mode = ''){
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
					<!-- Title -->
					<div class="form-group">
						<label for="name" class="required">Title</label>
						<input type="text" id="name" name="name" size="40" class="form-control required" placeholder="Name" value="<?php echo process($this->getName()); ?>" />
						<div class="error name"></div>
					</div>
					<!-- Gallery -->
					<div class="form-group">
						<label for="category" class="required">Gallery</label>
						<?php echo $this->listCategories($this->getCategory()); ?> 
					</div>
					<!-- URL -->
					<div class="form-group">
						<label for="url">URL</label>
						<input id="url" name="url" type="text" size="40" class="form-control" placeholder="URL" value="<?php echo process($this->getUrl()); ?>" />
						<div class="error url"></div>
					</div>
					<!-- Caption -->
					<div class="form-group">
						<label for="caption">Caption</label>
						<input id="caption" name="caption" type="text" size="40" class="form-control" placeholder="Caption" value="<?php echo process($this->getCaption()); ?>" />
						<div class="error caption"></div>
					</div>
					<!-- Photographer -->
					<div class="form-group">
						<label for="photographer">Photographer</label>
						<input id="photographer" name="photographer" type="text" size="40" class="form-control" placeholder="Photographer" value="<?php echo process($this->getPhotographer()); ?>" />
						<div class="error photographer"></div>
					</div>
					<!-- Date -->
					<div class="form-group">
						<label for="date_holder">Date</label>
						<?php echo FormComponent::dateTimePicker(array ('format'=>'MM/DD/YYYY','defaultDate'=>$this->getDate())); ?> 
						<div class="error date"></div>
					</div>
				</div>
				<div class="col-sm-6">
					<!-- Image -->
					<?php 
						$image = new Image(); 
						$image->setDirectory(DIRECTORY_SEPARATOR.$this->getFilePath());
						echo $image->manageImage($this->getDbTable(),$this->getId(),0,$this->getExtension(),$this->_moduleImageCanvasX,$this->_moduleImageCanvasY); ?>
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

	public function adminAddJavascriptAction(){
		return $this->buildAdminAddEditJavascript('add');
	}

	public function adminEditJavascriptAction(){
		return $this->buildAdminAddEditJavascript('edit');
	}

	protected function buildAdminAddEditJavascript($action){
		if(!in_array($action,array('add','edit'))){
			return '';
		}
		
	}
	
	public function buildCss(){	
	ob_start();?><style>
		#import{display:none;}
		.kv-file-content{height:inherit;overflow:hidden;}
		.kv-file-content .kv-preview-data{width:<?php echo $this->_moduleThumbCanvasX;?>px !important; max-height:<?php echo $this->_moduleThumbCanvasY;?>px !important;}
		.lvl_2 .menuDiv:hover{background: #FCFCFC;}
	</style>
	<?php return ob_get_clean();
	}
	
	public function buildAdminListJavascript(){	
		ob_start();?>
		<script>
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
					maxLevels: <?php echo $this->_moduleCategoryLevelDepth; ?>,
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

			$(document).ready(function(e) {
				initSortable();
			});
		</script>
			<?php return ob_get_clean();
	}

	public function adminNewRecord($category){
		$this->setDate(date('Y-m-d H:i:s'));
	}
	
	public function defaultListAction(){
		ob_start(); ?>

        <?php
		return ob_get_clean();	
	}	
}
?>