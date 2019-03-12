<?php
class Staff extends Model{
	// Module Config
	public $_moduleName	= 'Staff';
	public $_moduleDir = 'staff';
	public $_moduleTable = 'staff';
	public $_moduleTableCategories = 'staff_categories';
	public $_moduleClassName = 'Staff';
	public $_moduleCategoryClassName = 'StaffCategory';
	public $_moduleDescription = 'This section allows administrators to create, modify, and delete Staff Members';
	public $_moduleIcon = 'fa-users';
	public $_moduleImageCanvasX = 830;
	public $_moduleImageCanvasY = 738;
	public $_moduleThumbCanvasX = 225;
	public $_moduleThumbCanvasY = 200;
	public $_moduleCategoryLevelDepth = 2;
	
	// Static Variables
	protected static $_addLabel = 'Add Staff Member';
	protected static $_editLabel = 'Edit Staff Member';
	protected static $_imageTypes = array(
									'png'
									);
	// Inherited Variables
	protected $_dbTable	= 'staff';
	protected $_permalinkField = 'permalink';
	protected $_action = 'index';
	protected $_rootUrl = '/staff';
	
	// Table Variables
	protected $_id;
	protected $_category;
	protected $_name;
	protected $_title;
	protected $_bio;
	protected $_permalink;
	protected $_image = '0';
	protected $_active = '0';
	protected $_sortOrder = 0;

	// Instance Variables
	protected $_categoryObject = NULL;
	protected $_messagesArray;
	protected $_hrefPrefix = '';
	protected $_requiredFields = array(
									'category',
									'name'
									);
	protected $_saveFields = array(
								'category',
								'name',
								'title',
								'bio',
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
	public function setName($value){$this->_name = (string) $value; return $this;}
	public function getName(){return $this->_name;}
	public function setTitle($value){$this->_title = (string) $value; return $this;}
	public function getTitle(){return $this->_title;}
	public function setBio($value){$this->_bio = (string) $value; return $this;}
	public function getBio(){return $this->_bio;}
	public function setPermalink($value){ $this->_permalink = (string) $value; return $this;}
	public function getPermalink(){return $this->_permalink;}
	public function setImage($value){$this->_image = (string) $value; return $this;}
	public function getImage(){return $this->_image;}
	public function setActive($value){$this->_active = (string) $value; return $this;}
	public function getActive(){return $this->_active;}
	public function setSortOrder($value){$this->_sortOrder = (int) $value; return $this;}
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
			 `title` varchar(255) NOT NULL DEFAULT '',
			 `bio` text NOT NULL,
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
		$this->checkRequired();
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
					<!-- Title -->
					<div class="form-group">
						<label for="title" class="">Title</label>
						<input type="text" name="title" class="form-control" value="<?php echo process($this->getTitle()); ?>" placeholder="Title" />
						<div class="error title"></div>
					</div>
				</div>
				<div class="col-sm-6">
					<!-- Image -->
					<?php $image = new Image(); echo $image->manageImage($this->getDbTable(),$this->getId(),1,$this->getImage(),$this->_moduleImageCanvasX,$this->_moduleImageCanvasY); ?>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12">
					<!-- Bio -->
					<div class="form-group">
						<label for="bio" class="">Bio</label>
						<div class="error bio"></div>
						<?php echo FormComponent::textareaFull('bio', process($this->getBio()), 'bio', 'form-control'); ?>
					</div>
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
			
			$(document).ready(function(e) {
				initSortable();
			});
		</script>
			<?php return ob_get_clean();
	}
}

?>