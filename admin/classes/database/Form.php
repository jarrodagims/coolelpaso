<?php
class Form extends Model{
	// Module Config
	public $_moduleName	= 'Forms';
	public $_moduleDir = 'forms';
	public $_moduleTable = 'forms';
	public $_moduleTableCategories = 'form_categories';
	public $_moduleClassName = 'Form';
	public $_moduleCategoryClassName = 'FormCategory';
	public $_moduleDescription = 'This section allows administrators to create, modify, and delete Forms';
	public $_moduleIcon = 'fa-list-ul';
	public $_moduleImageCanvasX = 400;
	public $_moduleImageCanvasY = 600;
	public $_moduleThumbCanvasX = 150;
	public $_moduleThumbCanvasY = 225;
	public $_moduleCategoryLevelDepth = 2;
	
	// Static Variables
	protected static $_addLabel = 'Add Form';
	protected static $_editLabel = 'Edit Form';
	protected static $_imageTypes = array(
									'png'
									);
	// Inherited Variables
	protected $_dbTable	= 'forms';
	protected $_permalinkField = 'permalink';
	protected $_action = 'index';
	protected $_rootUrl = '/form';
	
	// Table Variables
	protected $_id;
	protected $_category;
	protected $_name;
	protected $_recipients;
	protected $_subject;
	protected $_successMsg;
	protected $_formStructure = '';
	protected $_permalink;
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
								'recipients',
								'subject',
								'success_msg',
								'form_structure',
								'permalink',
								'sort_order'
								);
	
	// Constructor
	public function __construct($id = 0){
		parent::__construct($id);
	}

	// Static Methods
	public static function setAddLabel($v){self::$_addLabel = (string)$v;}
	public static function getAddLabel(){return self::$_addLabel;}
	public static function setEditLabel($v){self::$_editLabel = (string)$v;}
	public static function getEditLabel(){return self::$_editLabel;}
	public static function getFileTypes(){return self::$_fileTypes;}
	
	// Accessor Methods
	public function setId($v){$this->_id = (int)$v; return $this;}
	public function getId(){return $this->_id;}
	public function setCategory($v){$this->_category = (int)$v; return $this;}
	public function getCategory(){return $this->_category;}
	public function setName($v){$this->_name = (string)$v; return $this;}
	public function getName(){return $this->_name;}
	public function setRecipients($v){$this->_recipients = (string)$v; return $this;}
	public function getRecipients(){return $this->_recipients;}
	public function setSubject($v){$this->_subject = (string)$v; return $this;}
	public function getSubject(){return $this->_subject;}	
	public function setSuccessMsg($v){$this->_successMsg = (string)$v; return $this;}
	public function getSuccessMsg(){return $this->_successMsg;}	
	public function setFormStructure($v){$this->_formStructure = (string)$v; return $this;}
	public function getFormStructure(){return $this->_formStructure;}
	public function setPermalink($v){$this->_permalink = (string)$v; return $this;}
	public function getPermalink(){return $this->_permalink;}
	public function setSortOrder($v){$this->_sortOrder = (int)$v; return $this;}
	public function getSortOrder(){return $this->_sortOrder;}
	
	// Instance Methods
	public function setHrefPrefix($v){$this->_hrefPrefix = (string)$v; return $this;}
	public function getHrefPrefix(){return $this->mysqlPrep($this->_hrefPrefix);}
	public function setSaveFields($v){$this->_saveFields = $v; return $this;}
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
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 
			";
			$result = $this->create($query);
			
			$query = "
			CREATE TABLE `".$this->_dbTable."` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `category` int(10) unsigned NOT NULL DEFAULT '0',
			  `name` varchar(255) NOT NULL DEFAULT '',
			  `recipients` text NOT NULL,
			  `subject` varchar(255) NOT NULL,
			  `success_msg` varchar(255) NOT NULL,
			  `form_structure` text NOT NULL,
			  `permalink` varchar(255) NOT NULL DEFAULT '',
			  `sort_order` int(10) unsigned NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id`),
			  KEY `category` (`category`) USING BTREE,
			  KEY `permalink` (`permalink`) USING BTREE
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 
			";
			$result = $this->create($query);
			
			$query = "
			CREATE TABLE `form_submissions` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `form_id` int(10) NOT NULL,
			  `form_data` text NOT NULL,
			  `submission_data` text NOT NULL,
			  `archived` enum('0','1') NOT NULL DEFAULT '0',
			  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8  
			";
			$result = $this->create($query);
		}
		
		return $this;
	}

	public function buildHref(){
		return $_SERVER['REQUEST_URI'].'/'.$this->getPermalink();	
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
	
	public function buildListImage(){
		return '<img src="/'.$this->getFilePath().'/'.$this->getDbTable().$this->getId().'.'.$this->getImage().'" class="img-responsive img-staff" alt="'.process($this->getName()).'" />';
	}	
	
	public function buildDetailImage(){
		return '<img align="left" valign="middle" src="/'.$this->getFilePath().'/'.$this->getDbTable().$this->getId().'.'.$this->getImage().'" class="img-responsive" alt="'.process($this->getName()).'" />';
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
					<?php //echo $this->buildToggleButton(); ?>
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
		ob_start();?>
		<script src="/js/form-render.min.js"></script>
		<script>
			jQuery(function($) {			  
				$('.fb-render').formRender({
					dataType: 'json',
					formData: <?php echo json_decode($this->getFormStructure()); ?>
				});
				$('#formElem').submit(function(e){
					e.preventDefault();
					var inputs = new Object();
					var required = new Array();
					var multibox = new Object();
					$(this).find('input, textarea, select, input:checkbox, input:radio').not(':input[type=button], :input[type=submit], :input[type=reset]').each(function(){
						var $this = $(this);
						// get input name and value
						if($this.is(':checkbox')){
							var cleanName = $this.attr('name').replace('[]', '');

							$('[name="'+$this.attr('name')+'"]').each(function(){
								if($this.is(':checked')){
									multibox[$this.attr('name')] += '|'+$this.val();
								}
							});
							if(multibox.length === 0){
								inputs[cleanName] = '';
							}else{
								inputs[cleanName] = [multibox[$this.attr('name')],$this.attr('type')];
							}


						}else if($this.is(':radio')){
							if($('[name="'+$this.attr('name')+'"]').is(':checked')){
								inputs[$this.attr('name')] = [$('[name="'+$this.attr('name')+'"]:checked').val(),$this.attr('type')];
							}else{
								inputs[$this.attr('name')] = '';
							}
						}else{
							inputs[$this.attr('name')] = [$this.val(),$this.attr('type')];
						}
						// get required
						if($this.prop('required')){
							required.push($this.attr('name'));
						}
					});
					required = jQuery.unique(required);
					$.ajax({
					  async: false,
					  url:'/form-action.php',
					  method:'POST',
					  data:{'form_action':'submit', 'form_id':'<?php echo $this->getId(); ?>',form_inputs:inputs,form_required:required},
					  success:function(data){
						if(data){
							console.log(data);
							var response = jQuery.parseJSON(data);
							var formOutcome = response.formOutcome;
							var messages = response.messages;
							for(var messageName in messages){
								var message = messages[messageName];
								var errorContainer = $(".field-"+messageName);
								if(message.type == 'failure'){
									// Sroll to top of form
									$("html, body").animate({ scrollTop: $('#formElem').offset().top+"px" });
									$('.error-'+messageName).slideUp().remove();
									errorContainer.after("<span class='label label-danger error-"+messageName+"'>"+message.text+"</span>");
									$('.error-'+messageName).show()

								}else{
									$('.error-'+messageName).slideUp().remove();
								}
								if(formOutcome.status == "success"){
									// Google Analytics Code
									$('.form-success').slideDown();
									$('.form-failure').hide();
									$('#formElem').hide();
									window.dataLayer = window.dataLayer || []
									  window.dataLayer.push({
									   'event': 'formSubmissions',
									   'formType': 'Contact us',
									});
									//ga('send', 'pageview', '/contact-us/success');
								}else{
									$('.form-failure').slideDown();
								}
							}
						  }
					  }
					});
				});
			});
		</script>
		<?php $GLOBALS['JAVASCRIPT'] = ob_get_clean();
		ob_start();
		?>
		<div class="form-success none"><?php echo $this->getSuccessMsg(); ?></div>
		<div class="form-failure none">Your submission contains errors, please correct them and try again.</div>
		<form id="formElem" name="formElem" action="" method="post" class="form-container" novalidate>
		<div class=" fb-render"></div>
		<button type="submit" class="submit-button">Submit</button>
		</form>
        <?php
		return ob_get_clean();
	}

	
	public function detailedAction(){
		ob_start();
		?>
			<div class="staff-bios-detail">
			<!--<a href="" class="animated-hover">Return to officers</a>-->
				<div class="h3"><?php echo $this->getTitle(); ?></div><hr><br />
       			<?php if($this->getImage()){ echo $this->buildDetailImage();} ?>
				<?php echo $this->getFormStructure(); ?>
			</div>
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
					<!-- Name -->
					<div class="form-group">
						<label for="nameholder" class="required">Name</label>
						<input type="text" name="name_holder" id="name_holder" class="form-control required" value="<?php echo process($this->getName()); ?>" placeholder="Name" />
						<div class="error name"></div>
					</div>
					<!-- Category -->
					<div class="form-group">
						<label for="category" class="required">Category</label><br>
						<?php echo $this->listCategories($this->getCategory()); ?>
						<div class="error category"></div>
					</div>
					<!-- Subject -->
					<div class="form-group">
						<label for="subject" class="required">Subject</label>
						<input type="text" name="subject" id="subject" class="form-control" value="<?php echo process($this->getSubject()); ?>" placeholder="Subject" />
						<div class="error subject"></div>
					</div>
					<!-- Success Message -->
					<div class="form-group">
						<label for="success_msg" class="required">Success Message</label>
						<input type="text" name="success_msg" id="success_msg" class="form-control" value="<?php echo process($this->getSuccessMsg()); ?>" placeholder="Success Message" />
						<div class="error success_msg"></div>
					</div>
				</div>
				<div class="col-sm-6">
					<!-- Recipients -->
					<div class="form-group">
						<label for="recipients" class="required">Recipients</label>
						<div class="instruction">
							<ul>
								<li>One email address per line.</li>
							</ul>
						</div>
						<textarea name="recipients" id="recipients" rows="9" class="form-control" placeholder="Recipients"><?php echo process($this->getRecipients()); ?></textarea>
						<div class="error recipients"></div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12">
					<!-- Bio -->
					<div class="form-group">
						<label for="form_structure" class="">Create Form</label>
						<input type="hidden" id="form_structure" name="form_structure" value="" />
						<link rel="stylesheet" type="text/css" href="form-builder/css/demo.css">
						<div id="build-wrap"></div>
						<script src="form-builder/js/form-builder.min.js"></script>
						<script>
							<?php if($this->getFormStructure()){$formData = $this->getFormStructure();}else{$formData = '"{}"';} ?>
							jQuery(function($){
								$("#name_holder").change(function(){
									$("#name").val($("#name_holder").val());
								});
								var options = {
									 disabledActionButtons:['data','clear','save'],
									 disableFields:['autocomplete','button','number','file'],
									 defaultFields:<?php echo json_decode($formData); ?>
									};
								  var fbTemplate = document.getElementById('build-wrap');
								  var formBuilder = $(fbTemplate).formBuilder(options);
								  $("#build-wrap").mouseout(function(){
									var jsonData = JSON.stringify(formBuilder.actions.getData('json'));
									$("#form_structure").val(jsonData);
								  });
								});
						</script>
						<div class="error bio"></div>
					</div>
				</div>
			</div>	
			<?php if($action == 'edit'): ?> 
			<input type="hidden" name="id" value="<?php echo $this->getId(); ?>" />
			<?php endif; ?> 
			<input type="hidden" name="action" value="<?php echo $action; ?>" />
			<input type="hidden" id="name" name="name" value="<?php echo $this->getName(); ?>" />
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