<?php
class Testimonial extends Model{
	// Module Configuration
	public $_moduleName  = 'Testimonials';
	public $_moduleDir   = 'testimonials';
	public $_moduleTable = 'testimonials';
	public $_moduleClassName = 'Testimonial';
	public $_moduleDescription = 'This section allows administrators to create, modify, and delete Testimonials within the site.';
	public $_moduleIcon = 'fa-quote-left';
	public $_moduleImageCanvasX = 830;
	public $_moduleImageCanvasY = 738;
	public $_moduleThumbCanvasX = 225;
	public $_moduleThumbCanvasY = 200;
	public $_moduleCategoryLevelDepth = 1;
	
	// Static Variables
	protected static $_addLabel = 'Add Testimonial';
	protected static $_editLabel = 'Edit Testimonial';

	// Inherited Variables
	protected $_dbTable = 'testimonials';
	//protected $_permalinkField = 'permalink';
	protected $_action = 'index';
	
	// Table Variables
	protected $_id;
	protected $_name;
	protected $_age = "";
	protected $_title = "";
	protected $_rating = '5';
	protected $_testimonial;
	protected $_image = '0';
	protected $_active = '0';
	protected $_sortOrder = 0;
	
	// Instance Variables
	protected $_requiredFields = array(
										'name',
										'testimonial',
										);
	protected $_saveFields = array(
									'id',
									'name',
									'age',
									'title',
									'rating',
									'testimonial',
									'image',
									'active',
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
	public function setAge($value){$this->_age = $value; return $this;}
	public function getAge(){return $this->_age;}
	public function setTitle($value){$this->_title = $value; return $this;}
	public function getTitle(){return $this->_title;}
	public function setRating($value){$this->_rating = $value; return $this;}
	public function getRating(){return $this->_rating;}
	public function setTestimonial($value){$this->_testimonial = $value; return $this;}
	public function getTestimonial(){return $this->_testimonial;}
	public function setImage($value){$this->_image = $value; return $this;}
	public function getImage(){return $this->_image;}
	public function setActive($value){$this->_active = $value; return $this;}
	public function getActive(){return $this->_active;}
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
			 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			 `name` varchar(255) NOT NULL DEFAULT '',
			 `age` varchar(255) NOT NULL,
			 `title` varchar(255) NOT NULL DEFAULT '',
			 `testimonial` text NOT NULL,
 			 `image` varchar(255) NOT NULL,
			 `active` enum('0','1') NOT NULL DEFAULT '0',
			 `sort_order` int(10) unsigned NOT NULL DEFAULT '0',
			 PRIMARY KEY (`id`),
			 KEY `featured` (`active`) USING BTREE
			) ENGINE=MyISAM DEFAULT CHARSET=utf8
			";
			$result = $this->create($query);
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
					<span data-id="<?php echo $this->getId(); ?>" class="itemTitle"><?php echo process($this->getName()); ?></span>
					<span data-id="<?php echo $this->getId(); ?>" class="action_menu">
						<a tabindex="0" class="pull-right" role="button" data-toggle="popover" data-trigger="focus" data-placement="bottom" data-content='<?php echo $this->toHtml('menu'); ?>'><i class="fa fa-gear"></i></a>
					</span>
					<?php echo $this->buildToggleButton('active'); ?>
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
	
	public function indexAction(){
		ob_start();
		$testimonial = $this->fetchAll("WHERE `active` = '1'","ORDER BY RAND() LIMIT 1");
		?>
							<p class="homepage-testimonial-content" data-aos="fade">"<?php echo strip_tags($testimonial[0]->getTestimonial()); ?>"</p>
							<p class="homepage-testimonial-name">-<?php echo $testimonial[0]->getName(); ?></p>
        <?php
		return ob_get_clean();
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
					<!-- Age -->
					<!--<div class="form-group">
						<label for="age" class="">Age</label>
						<input type="text" name="age" class="form-control" value="<?php echo process($this->getAge()); ?>" placeholder="Age" />
						<div class="error age"></div>
					</div>-->
					<!-- Title -->
					<div class="form-group">
						<label for="rating">Star Rating</label>
						<?php $values = [1, 2, 3, 4, 5]; $labels = ['1 Star', '2 Stars', '3 Stars', '4 Stars', '5 Stars'];
							echo FormComponent::selectList('rating',$values,$labels,$this->getRating(),'rating','rating'); ?>
						<div class="error rating"></div>
					</div>
				</div>
				<div class="col-sm-6">
					<!-- Image -->
					<?php $image = new Image(); $image->setDirectory('/files/'); echo $image->manageImage($this->getDbTable(),$this->getId(),1,$this->getImage(),$this->_moduleImageCanvasX,$this->_moduleImageCanvasY); ?>
				</div>
			</div>	
			<div class="row">
				<div class="col-sm-12">
					<!-- Testimonial -->
					<div class="form-group">
						<label for="testimonial" class="required">Testimonial</label>
						<div class="error testimonial"></div>
						<?php echo FormComponent::textareaFull('testimonial', process($this->getTestimonial()), 'testimonial', 'form-control required'); ?>
						
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