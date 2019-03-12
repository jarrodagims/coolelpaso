<?php
class Faq extends Model{
	// Module Configuration
	public $_moduleName  = 'FAQs';
	public $_moduleDir   = 'faqs';
	public $_moduleTable = 'faqs';
	public $_moduleTableCategories = 'faqs_categories';
	public $_moduleClassName = 'Faq';
	public $_moduleCategoryClassName = 'FaqCategory';
	public $_moduleDescription = 'This section allows administrators to create, modify, and delete FAQs.';
	public $_moduleIcon = 'fa-question-circle';
	public $_moduleFeaturedLimit = 10;
	public $_moduleCategoryLevelDepth = 2;
	
	// Static Variables
	protected static $_addLabel = 'Add FAQ';
	protected static $_editLabel = 'Edit FAQ';
	
	// Inherited Variables
	protected $_dbTable = 'faqs';
	protected $_permalinkField = 'permalink';
	protected $_action = 'index';
	
	// Table Variables
	protected $_id;
	protected $_category = 0;
	protected $_question;
	protected $_answer;
	protected $_permalink;
	protected $_sortOrder = 0;

	// Instance Variables
	protected $_requiredFields = array(
									'question',
									'answer'
									);
	protected $_saveFields = array(
									'category',
									'question',
									'answer',
									'permalink',
									'sort_order'
									);

	// Static Methods
	public static function setAddLabel($value){self::$_addLabel = (string) $value;}
	public static function getAddLabel(){return self::$_addLabel;}
	public static function setEditLabel($value){self::$_editLabel = (string) $value;}
	public static function getEditLabel(){return self::$_editLabel;}

	// Constructor
	public function __construct($id = 0){
		parent::__construct($id);
	}
	
	// Accessor Methods
	public function setId($value){$this->_id = (int) $value; return $this;}
	public function getId(){return $this->_id;}
	public function setCategory($value){$this->_category = (int) $value; return $this;}
	public function getCategory(){return $this->_category;}
	public function setQuestion($value){$this->_question = (string) $value; return $this;}
	public function getQuestion(){return $this->_question;}
	public function setAnswer($value){$this->_answer = (string) $value; return $this;}
	public function getAnswer(){return $this->_answer;}
	public function setPermalink($value){$this->_permalink = (string) $value; return $this;}
	public function getPermalink(){return $this->_permalink;}
	public function setSortOrder($value){$this->_sortOrder = (int) $value; return $this;}
	public function getSortOrder(){return $this->_sortOrder;}

	// Instance Methods
	public function setSaveFields($value){$this->_saveFields = $value; return $this;}
	public function getSaveFields(){return $this->_saveFields;}

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
			 `locked` enum('0','1') DEFAULT '0',
			 PRIMARY KEY (`id`),
			 KEY `permalink` (`permalink`) USING BTREE
			) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8
			";
			$result = $this->create($query);
			
			$query = "
			CREATE TABLE `".$this->_dbTable."` (
			 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			 `category` int(10) unsigned NOT NULL DEFAULT '0',
			 `question` text NOT NULL,
			 `answer` text NOT NULL,
			 `permalink` varchar(255) NOT NULL DEFAULT '',
			 `sort_order` int(10) unsigned NOT NULL DEFAULT '0',
			 PRIMARY KEY (`id`),
			 KEY `category` (`category`) USING BTREE,
			 KEY `permalink` (`permalink`) USING BTREE
			) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8
			";
			$result = $this->create($query);
		}
		
		return $this;
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
					<a class="btn btn-green pull-left" href="<?php echo $moduleCategoryClass->buildModalUrl('add'); ?>" data-toggle="modal" data-target="#moduleModal"><i class="fa fa-plus"></i>Create New FAQs Category</a>
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
	
	public function indexAction(){
		ob_start();
		?>
		<div class="faq-question" data-aos="fade-down"><?php echo strip_tags($this->getQuestion()); ?></div>
		<div class="faq-answer" data-aos="fade-up"><?php echo $this->getAnswer(); ?></div>
		<?php
		return ob_get_clean();
	}
	
	public function defaultListAction(){		
		# List Structure
		ob_start(); ?>
		<div id="<?php echo $this->_moduleClassName; ?>_<?php echo $this->getId(); ?>" class="menuDiv input-group">
			<span title="Drag item to change sort order" class="input-group-addon draghandle">
				<i class="fa fa-arrows" aria-hidden="true"></i>
			</span>
			<div class="branch_content">
				<span title="Click to show/hide sub-items" class="disclose hidden">
					<i class="fa fa-chevron-circle-down" aria-hidden="true"></i>
				</span>
				<span data-id="<?php echo $this->getId(); ?>" class="itemTitle">
					<span class="bold">Q: </span><span class="question"><?php echo strip_tags($this->getQuestion()); ?></span>
				</span>
				<span data-id="<?php echo $this->getId(); ?>" class="action_menu">
					<a tabindex="0" class="pull-right" role="button" data-toggle="popover" data-trigger="focus" data-placement="bottom" data-content='<?php echo $this->toHtml('menu'); ?>'><i class="fa fa-gear"></i></a>
				</span>

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
	
	public function adminNewRecord($categoryId){
		return $this->setCategory($categoryId);
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
      			</div>
      		</div>
      		<div class="row">
      			<div class="col-sm-12">
      				<!-- Question -->
      				<label for="question" class="required">Question</label><br>
      				<?php echo FormComponent::textareaSmall('question', process($this->getQuestion()), 'question', 'form-control required'); ?>
      				<div class="error question"></div>
      				<br>
      			</div>
      			<div class="col-sm-12">
      				<!-- Answer -->
      				<label for="answer" class="required">Answer</label><br>
      				<?php echo FormComponent::textareaFull('answer', process($this->getAnswer()), 'answer', 'form-control required'); ?>
      				<div class="error answer"></div>
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
			function moduleSpecificRefresh(content,action){
				if(action === 'add'){
					initSortable();
					initPopOver();
					toggleVisible();
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