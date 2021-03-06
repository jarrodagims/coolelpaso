<?php
class CmsModule extends Model{
	// Module Configuration
	public $_moduleName  = 'CMS Modules';
	public $_moduleDir   = 'modules';
	public $_moduleTable = 'modules';
	public $_moduleClassName = 'CmsModule';
	public $_moduleDescription = '';
	public $_moduleIcon = '';
	public $_moduleCategoryLevelDepth = 1;
	
	// Static Variables
	protected static $_addLabel = 'Add Module';
	protected static $_editLabel = 'Edit Module';

	// Inherited Variables
	protected $_dbTable = 'modules';
	//protected $_permalinkField = 'permalink';
	protected $_action = 'index';
	
	// Table Variables
	protected $_id;
	protected $_name;
	protected $_dir;
	protected $_modTable;
	protected $_class;
	protected $_description;
	protected $_icon;
	protected $_enabled = '0';
	protected $_super = '0';
	protected $_sortOrder = 0;
	
	// Instance Variables
	protected $_requiredFields = array(
										'name',
										'dir',
										'mod_table',
										'class',
										);
	protected $_saveFields = array(
										'id',
										'name',
										'dir',
										'mod_table',
										'class',
										'description',
										'icon',
										'enabled',
										'super',
										'sort_order'
										);

	
	// Constructor
	public function __construct($id = 0)
	{
		parent::__construct($id);
	}
	
	// Static Methods
	public static function setAddLabel($value){self::$_addLabel = (string) $value;}
	public static function getAddLabel(){ return self::$_addLabel;}
	public static function setEditLabel($value){self::$_editLabel = (string) $value;}
	public static function getEditLabel(){ return self::$_editLabel;}

	// Accessor Methods
	public function setId($value){$this->_id = $value; return $this;}
	public function getId(){return $this->_id;}
	public function setName($value){$this->_name = $value; return $this;}
	public function getName(){return $this->_name;}
	public function setDir($value){$this->_dir = $value; return $this;}
	public function getDir(){return $this->_dir;}
	public function setModTable($value){$this->_modTable = $value; return $this;}
	public function getModTable(){return $this->_modTable;}
	public function setClass($value){$this->_class = $value; return $this;}
	public function getClass(){return $this->_class;}
	public function setDescription($value){$this->_description = $value; return $this;}
	public function getDescription(){return $this->_description;}
	public function setIcon($value){$this->_icon = $value; return $this;}
	public function getIcon(){return $this->_icon;}
	public function setEnabled($value){$this->_enabled = $value; return $this;}
	public function getEnabled(){return $this->_enabled;}
	public function setSuper($value){ $this->_super = (string) $value; return $this;}
	public function getSuper(){return $this->_super;}
	public function setSortOrder($value){$this->_sortOrder = $value; return $this;}
	public function getSortOrder(){return $this->_sortOrder;}

	// Instance Methods	
	public function install($username,$password){
		return $this;
	}
	
	public function validate(){
		if(!$this->checkRequired()){
			$this->addMessage('general',array('type'=>'failure','text'=>'Please complete all <b>required</b> fields'));
		}
		if($this->hasMessages() && !$this->hasMessage('general')){
			$this->addMessage('general',array('type'=>'failure','text'=>'Your submission contains errors<br />Please correct them and try again'));
		}
	}

	// Action Methods
	public function moduleIndexAction(){
		# Get the list of records
		$moduleClasses = new $this->_moduleClassName();
		$moduleClasses = $moduleClasses->fetchAll("","ORDER BY `sort_order` ASC");
		
		ob_start(); ?>
		
		<div class="index-wrapper">
			<ol class="sortable ui-sortable">
				<?php 
				if(count($moduleClasses)){
					foreach($moduleClasses as $module){
						echo $module->toHtml('default-list');
					}
				}
				?>
			</ol>
			<?php 
			if(!count($moduleClasses)){?>
				<div class="no_records"><i class="fa fa-times-circle"></i>No records available.</div>
			<?php
			}
			?>
			<?php echo $this->buildAdminListJavascript(); ?>
		</div>
		
		<?php
		return ob_get_clean();
	}
	
	public function defaultListAction(){
		ob_start(); ?>
		
		<li id="menuItem_<?php echo $this->getId(); ?>" class="lvl_1 <?php echo $this->_moduleClassName; ?> mjs-nestedSortable-leaf">
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
					<?php if($this->getSuper() != 1){echo $this->buildToggleButton('enabled');} ?>
				</div>
			</div>
		</li>
		
		<?php
		return ob_get_clean();
	}
	
	public function menuAction(){
		ob_start(); ?>
        <ul class="actions">
        	<li><a href="<?php echo $this->buildModalUrl('edit'); ?>" data-toggle="modal" data-target="#moduleModal"><i class="fa fa-pencil"></i>Edit</a></li>
		</ul>
        <?php
		$html = ob_get_clean();
		return $html;	
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
					<!-- Name -->
					<div class="form-group">
						<label for="name" class="required">Name</label>
						<input type="text" name="name" class="form-control required" value="<?php echo process($this->getName()); ?>" placeholder="Name" />
						<div class="error name"></div>
					</div>
					
				</div>
				
			</div>	
			<div class="row">
				<div class="col-sm-12">
					
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
					maxLevels: 1,
					isTree: true,
					expandOnHover: 700,
					protectRoot: false,
					startCollapsed: true,
					<?php //if($this->_moduleCategoryLevelDepth > 1){?>
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
					<?php //} ?>
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
}
?>