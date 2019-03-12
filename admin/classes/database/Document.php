<?php
class Document extends Model{
	// Module Configuration
	public $_moduleName  = 'Documents';
	public $_moduleDir   = 'documents';
	public $_moduleTable = 'documents';
	public $_moduleTableCategories = 'documents_categories';
	public $_moduleClassName = 'Document';
	public $_moduleCategoryClassName = 'DocumentCategory';
	public $_moduleDescription = 'This section allows administrators to create, modify, and delete Photo Galleries.';
	public $_moduleIcon = 'fa-file-text-o';
	public $_moduleCategoryLevelDepth = 2;
	public $_moduleImageCanvasX = 830;
	public $_moduleImageCanvasY = 738;
	public $_moduleThumbCanvasX = 160;
	public $_moduleThumbCanvasY = 200;
	public $_moduleFeaturedLimit = 3;
	
	// Static Variables
	protected static $_addLabel = 'Add Document';
	protected static $_editLabel = 'Edit Document';
	protected static $_fileTypes = array(
									'doc',
									'docx',
									'xlsx',
									'pdf',
									'txt',
									'rtf',
									'pptx',
									'zip'
									);
	// Inherited Variables
	protected $_dbTable = 'documents';
	protected $_permalinkField = 'permalink';
	protected $_action = 'index';
	
	// Table Variables
	protected $_id;
	protected $_category;
	protected $_title = '';
	protected $_description = '';
	protected $_url = '';
	protected $_date = '0000-00-00';
	protected $_name = '';
	protected $_type = '';
	protected $_previewContent = '';
	protected $_extension = '';
	protected $_extensionUrl = '';
	protected $_file = '0';
	protected $_permalink = '';
	protected $_active = '0';
	protected $_featured = '0';
	protected $_sortOrder = '0';
	
	// Instance Variables
	protected $_categoryObject = NULL;
	protected $_requiredFields = array(
									'title'
									);
	protected $_saveFields = array(
									'category',
									'title',
									'description',
									'url',
									'date',
									'name',
									'type',
									'preview_content',
									'extension',
									'extension_url',
									'file',
									'permalink',
									'active',
									'featured',
									'sort_order'
									);
	
	// Constructor
	public function __construct($id = 0)
	{
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
	public function setTitle($value){$this->_title = (string) $value; return $this;}
	public function getTitle(){return $this->_title;}
	public function setDescription($value){$this->_description = (string) $value; return $this;}
	public function getDescription(){return $this->_description;}
	public function setUrl($value){$this->_url = (string) $value; return $this;}
	public function getUrl(){return $this->_url;}
	public function setDate($value){$this->_date = (string) $value; return $this;}
	public function getDate(){return $this->_date;}
	public function setName($value){$this->_name = (string) $value; return $this;}
	public function getName(){return $this->_name;}
	public function setType($value){$this->_type = (string) $value; return $this;}
	public function getType(){return $this->_type;}
	public function setPreviewContent($value){$this->_previewContent = (string) $value; return $this;}
	public function getPreviewContent(){return $this->_previewContent;}
	public function setExtension($value){$this->_extension = (string) $value; return $this;}
	public function getExtension(){return $this->_extension;}
	public function setExtensionUrl($value){$this->_extensionUrl = (string) $value; return $this;}
	public function getExtensionUrl(){return $this->_extensionUrl;}
	public function setFile($value){$this->_file = (string) $value; return $this;}
	public function getFile(){return $this->_file;}
	public function setPermalink($value){$this->_permalink = (string) $value; return $this;}
	public function getPermalink(){return $this->_permalink;}
	public function setActive($value){$this->_active = (string) $value; return $this;}
	public function getActive(){return $this->_active;}
	public function setFeatured($value){$this->_featured = (string) $value; return $this;}
	public function getFeatured(){return $this->_featured;}
	public function setSortOrder($value){$this->_sortOrder = (int) $value; return $this;}
	public function getSortOrder(){return $this->_sortOrder;}
	
	// Instance Methods
	public function setSaveFields($value){$this->_saveFields = $value; return $this;}
	public function getSaveFields(){return $this->_saveFields;}

	public function install(){
		# Register module
		$this->register($this->_moduleCategoryClassName);
		
		# Create tables
		if(!$this->isInstalled()){
			$query = "
			CREATE TABLE `".$this->_moduleTableCategories."` (
			 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			 `name` varchar(255) NOT NULL DEFAULT '',
			 `permalink` varchar(255) NOT NULL DEFAULT '',
			 `locked` enum('0','1') NOT NULL DEFAULT '0',
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
			 `title` varchar(255) NOT NULL DEFAULT '',
			 `description` text NOT NULL,
			 `url` varchar(255) NOT NULL DEFAULT '',
			 `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			 `name` varchar(255) NOT NULL DEFAULT '',
			 `type` varchar(255) NOT NULL DEFAULT '',
			 `preview_content` text NOT NULL,
			 `extension` varchar(255) NOT NULL DEFAULT '',
			 `extension_url` varchar(3) NOT NULL,
			 `file` enum('0','1') NOT NULL DEFAULT '0',
			 `permalink` varchar(255) NOT NULL DEFAULT '',
			 `active` enum('0','1') NOT NULL DEFAULT '0',
			 `featured` enum('0','1') NOT NULL DEFAULT '0',
			 `sort_order` int(10) unsigned NOT NULL DEFAULT '0',
			 PRIMARY KEY (`id`),
			 KEY `permalink` (`permalink`) USING BTREE,
			 KEY `category` (`category`) USING BTREE
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
	
	
	public function buildHref(){
		if(strlen(trim($this->getUrl()))){
			return $this->getUrl();
		}else{
			return '/documents/'.$this->getPermalink();
		}
	}
	
	public function buildLink(){
		if(preg_match("/http[s]{0,1}:\/\//",$this->getUrl())){
			return '<a href="'.$this->getUrl().'" class="external" onClick="javascript: pageTracker._trackPageview(\'/outbound_link/'.abstractDomain($this->getUrl()).'\');">'.$this->getTitle().'</a>';
		}elseif(strlen($this->getUrl())){
			return '<a href="'.$this->buildHref().'">'.$this->getTitle().'</a>';
		}else{
			return '<a href="'.$this->buildHref().'" onClick="javascript: pageTracker._trackPageview(\'/downloads/'.$this->getPermalink().'\');">'.$this->getTitle().'</a>';
		}
	}
	
	// Action Methods
	public function indexAction(){
		ob_start();
		?>
         <img src="/files/portfolio_photos<?php echo $this->getId().$this->getExtension(); ?>" class="portfolio_detail_image" alt="<?php echo $this->getCaption(); ?>" />
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
					<div class="form-group">
						<label for="title" class="required">Title</label>
						<input type="text" id="title" name="title" size="40" class="form-control" placeholder="Title" value="<?php echo process($this->getTitle()); ?>" />
						<div class="error name"></div>
					</div>
					<div class="form-group">
						<label for="category" class="block required">Category</label>
						<?php echo $this->listCategories($this->getCategory()); ?> 
					</div>

				</div>
				
				
				<div class="col-sm-6">
					<div class="form-group">
						<label for="url">URL</label>
						<input id="url" name="url" type="text" size="40" class="form-control" placeholder="URL" value="<?php echo process($this->getUrl()); ?>" /><p class="instruction">Examples: "/privacy-policy" or "http://www.stantonstreet.com"<br />Specifying a URL will override any linked document.</p>
						<div class="error url"></div>
					</div>
					<?php $file = new File(); echo $file->manageFile(get_class($this),$this->getId(),1,$this->getFile(),$this->getFileTypes()); ?> 
				</div>
				
				<div class="col-sm-12">
					<div class="form-group">
						<label for="description">Description</label>
						<textarea id="description" name="description" class="form-control tiny-mce tiny-mce-small"><?php echo process($this->getDescription()); ?></textarea> 
						<div class="error description"></div>
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
		.file-zoom-dialog .btn-navigate{color:#999;}
		.embeded-pdf{border: 1px solid #ddd;}
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
								//console.log(data);
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
		$this->setCategory($category);
	}
	
	public function defaultListAction(){
		ob_start(); ?>

        <?php
		return ob_get_clean();	
	}	
}
?>