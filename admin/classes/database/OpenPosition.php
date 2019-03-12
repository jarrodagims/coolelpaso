<?php
class OpenPosition extends Model{
	// Module Configuration
	public $_moduleName  = 'Open Positions';
	public $_moduleDir   = 'open_positions';
	public $_moduleTable = 'open_positions';
	public $_moduleClassName = 'OpenPosition';
	public $_moduleDescription = 'This section allows administrators to create, modify, and delete Open Job Positions within the site.';
	public $_moduleIcon = 'fa-black-tie';
	public $_moduleCategoryLevelDepth = 1;
	
	// Static Variables
	protected static $_addLabel = 'Add Open Position';
	protected static $_editLabel = 'Edit Position';

	// Inherited Variables
	protected $_filePath = 'files/positions/';
	protected $_dbTable = 'open_positions';
	//protected $_permalinkField = 'permalink';
	protected $_action = 'index';
	
	// Table Variables
	protected $_id;
	protected $_title;
	protected $_department;
	protected $_additionalInformation;
	protected $_postDate;
	protected $_removeDate;
	protected $_pdf = '0';
	protected $_sortOrder = 0;
	
	// Instance Variables
	protected $_requiredFields = array(
										'title',
										'post_date',
										);
	protected $_saveFields = array(
									'id',
									'title',
									'department',
									'additional_information',
									'post_date',
									'remove_date',
									'pdf',
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
	public function setTitle($value){$this->_title = $value; return $this;}
	public function getTitle(){return $this->_title;}
	public function setDepartment($value){$this->_department = $value; return $this;}
	public function getDepartment(){return $this->_department;}
	public function setAdditionalInformation($value){$this->_additionalInformation = $value; return $this;}
	public function getAdditionalInformation(){return $this->_additionalInformation;}
	public function setPostDate($value){$this->_postDate = $this->formatDateTime((string) $value); return $this;}
	public function getPostDate(){return $this->_postDate;}
	public function setRemoveDate($value){$this->_removeDate = $this->formatDateTime((string) $value); return $this;}
	public function getRemoveDate(){return $this->_removeDate;}
	public function setPdf($value){$this->_pdf = (string) $value; return $this;}
	public function getPdf(){return $this->_pdf;}
	public function setSortOrder($value){$this->_sortOrder = $value; return $this;}
	public function getSortOrder(){return $this->_sortOrder;}

	// Instance Methods	
	public function install($username,$password){
		# Register module
		$this->register();
		
		# Create table
		if(!$this->isInstalled()){
			$query = "
			CREATE TABLE `".$this->_dbTable."` (
			 `id` int(11) NOT NULL AUTO_INCREMENT,
			 `title` varchar(255) NOT NULL,
			 `department` varchar(255) NOT NULL,
			 `additional_information` text NOT NULL,
			 `post_date` date NOT NULL,
			 `remove_date` date NOT NULL,
			 `pdf` enum('0','1') NOT NULL,
			 `sort_order` int(11) NOT NULL,
			 PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8
			";
			$result = $this->create($query);
		}

		# Create Directory for files
		if (!file_exists($GLOBALS['path'].$this->getFilePath())) {
			@mkdir($GLOBALS['path'].$this->getFilePath());
		}
		
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
			<div class="row">
				<div class="col-xs-12">
					<a class="btn btn-green pull-left" href="<?php echo $this->buildModalUrl('add'); ?>" data-toggle="modal" data-target="#moduleModal"><i class="fa fa-plus"></i><?php echo $this->_addLabel; ?></a>
				</div>
			</div>
			<ol class="sortable ui-sortable">
				<?php 
				if(count($moduleClasses)){
					foreach($moduleClasses as $testimony){
						echo $testimony->toHtml('default-list');
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
					<span data-id="<?php echo $this->getId(); ?>" class="itemTitle"><?php echo process($this->getTitle()); ?></span>
					<span data-id="<?php echo $this->getId(); ?>" class="action_menu">
						<a tabindex="0" class="pull-right" role="button" data-toggle="popover" data-trigger="focus" data-placement="bottom" data-content='<?php echo $this->toHtml('menu'); ?>'><i class="fa fa-gear"></i></a>
					</span>
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
        	<li><a href="<?php echo $this->buildModalUrl('confirm','delete_confirm'); ?>" class="delete" data-toggle="modal" data-target="#confirmModal"><i class="fa fa-trash-o"></i>Delete</a></li>
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
					<!-- Title -->
					<div class="form-group">
						<label for="title" class="">Title</label>
						<input type="text" name="title" class="form-control" value="<?php echo process($this->getTitle()); ?>" placeholder="Title" />
						<div class="error title"></div>
					</div>
				</div>
				<div class="col-sm-6">
					<!-- Department -->
					<div class="form-group">
						<label for="department" class="">Department</label>
						<input type="text" name="department" class="form-control" value="<?php echo process($this->getDepartment()); ?>" placeholder="Department" />
						<div class="error department"></div>
					</div>
				</div>
			</div>	
			<div class="row">
				<div class="col-sm-6">					
					<!-- Post Date -->
					<div class="form-group">
						<label for="post_date" class="required">Post Date</label>
						<?php echo FormComponent::dateTimePicker(array ('format'=>'MM/DD/YYYY', 'name'=>'post_date', 'defaultDate'=>$this->getPostDate())); ?>
						<div class="instruction">
							<ul>
								<li>Will be posted beginning on the day selected</li>
							</ul>
						</div>
					</div>
				</div>
				<div class="col-sm-6">					
					<!-- Remove Date -->
					<div class="form-group">
						<label for="remove_date" class="">Remove Date</label>
						<?php echo FormComponent::dateTimePicker(array ('format'=>'MM/DD/YYYY', 'name'=>'remove_date', 'defaultDate'=>$this->getRemoveDate())); ?>
						<div class="instruction">
							<ul>
								<li>Will be removed after the day selected</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12">
					<!-- Testimonial -->
					<div class="form-group">
						<label for="additional_information" class="">Additional Information</label>
						<div class="error additional_information"></div>
						<?php echo FormComponent::textareaSmall('additional_information', process($this->getAdditionalInformation()), 'additional_information', 'form-control'); ?>
					</div>
				</div>				
			</div>
			<div class="row">
				<div class="col-sm-6">
					<!-- PDF Document -->
					<?php $file = new File(); echo $file->manageFile(get_class($this),$this->getId(),2,$this->getPdf(),array('pdf'),'pdf','PDF Document'); ?>
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