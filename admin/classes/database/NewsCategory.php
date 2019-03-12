<?php
class NewsCategory extends Category{
	// Module Configuration
	public $_moduleClassName = 'News';
	public $_moduleCategoryName = 'NewsCategory';
	// Static Variables
	protected static $_addLabel = 'Add News Article Category';
	protected static $_editLabel = 'Edit News Article Category';

	// Inherited Variables - Model
	protected $_dbTable	= 'news_categories';
	protected $_permalinkField = 'permalink';
	protected $_action = 'index';
	
	// Inherited Variables - Category
	protected $_childClass = 'News';
	protected $_sortBy = 'sort_order';
	
	// Table Variables
	protected $_id;
	protected $_name;
	protected $_permalink;
	protected $_sortOrder = 0;
	protected $_locked = '0';
	
	// Instance Variables
	protected $_messagesArray;
	protected $_news = NULL;
	protected $_hrefPrefix = '';
	protected $_requiredFields = array(
									'name'
									);
	protected $_saveFields = array(
								'name',
								'permalink',
								'sort_order',
								'locked'
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
	public function setName($value){$this->_name = (string) $value; return $this;}
	public function getName(){return $this->_name;}
	public function setPermalink($value){$this->_permalink = (string) $value; return $this;}
	public function getPermalink(){return $this->_permalink;}
	public function setSortOrder($value){$this->_sortOrder = (int) $value; return $this;}
	public function getSortOrder(){return $this->_sortOrder;}
	public function setLocked($value){$this->_locked = (string) $value; return $this;}
	public function getLocked(){return $this->_locked;}

	// Instance Methods
	public function setHrefPrefix($value){$this->_hrefPrefix = (string) $value; return $this;}
	public function getHrefPrefix(){return $this->mysqlPrep($this->_hrefPrefix);}
	public function setSaveFields($value){$this->_saveFields = $value; return $this;}
	public function getSaveFields(){return $this->_saveFields;}
	
	public function setRecords($records = ''){
		if(is_array($records)){
			$this->_news = $records;
		}elseif($this->getId()){
			$records = new $this->_moduleClassName();
			$this->_news = $records->fetchAll("WHERE `category` = ".$this->getId(), "ORDER BY `sort_order`");
		}else{
			$this->_news = array();
		}
		return $this;
	}

	public function getRecords(){
		if($this->_news === NULL){
			$this->setRecords();
		}
		return $this->_news;
	}

	public function setNews($news = ''){
		if(is_array($news)){
			$this->_news = $news;
		}elseif($this->getId()){
			$news = new $this->_moduleClassName();
			if($this->getFeaturedMode()){
				$featuredFilter = " AND `featured` = '1' ";
			}else{
				$featuredFilter = " AND `do_not_show_on_news`='0' ";
			}
			$this->_news = $news->fetchAll("WHERE (`category` = ".$this->getId()." ".$featuredFilter.") AND `post_date` <= NOW() AND (`remove_date` >= CURDATE() OR `remove_date` = '0000-00-00')","ORDER BY `date` DESC");
		}else{
			$this->_news = array();
		}
		return $this;
	}

	public function getNews(){
		if($this->_news === NULL){
			$this->setNews();
		}
		return $this->_news;
	}
	
	public function validate(){
		$this->checkRequired();
		return $this;
	}
	
	// Action Methods
	public function defaultListAction(){		
		# List Structure
		ob_start(); ?>
		<div class="menuDiv input-group">
			<span title="Drag item to change sort order" class="input-group-addon draghandle">
				<i class="fa fa-arrows" aria-hidden="true"></i>
			</span>
			<div class="branch_content">
				<span title="Click to show/hide sub-items" class="disclose hidden">
					<i class="fa fa-chevron-circle-down" aria-hidden="true"></i>
				</span>
				<span data-id="<?php echo $this->getId(); ?>" class="itemTitle">
					<?php echo ucwords($this->getName()); ?>
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
		$child_item = new $this->_childClass();
		
		ob_start(); ?>
        <ul class="actions">
			<li><a href="<?php echo $child_item->buildModalUrl('add', $this->getId()); ?>" data-toggle="modal" data-target="#moduleModal"><i class="fa fa-plus"></i><?php echo $child_item->_addLabel;?></a></li>
			<li><a data-toggle="modal" href="<?php echo $this->buildModalUrl('edit'); ?>"  data-target="#moduleModal"><i class="fa fa-pencil"></i>Edit</a></li>
        	<li><a href="<?php echo $this->buildModalUrl('confirm','delete_confirm'); ?>" class="delete" data-toggle="modal" data-target="#confirmModal"><i class="fa fa-trash-o"></i>Delete</a></li>
		</ul>
        <?php
		$html = ob_get_clean();
		return $html;	
	}
	
	public function batchCommandsAction(){
		ob_start(); ?>
		<div class="row batch-container">
			<ul class="nav nav-pills pull-right batch-actions">
				<li role="presentation" class="dropdown">
				<a class="dropdown-toggle btn btn-custom" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false"> Batch Actions <span class="caret"></span></a>
					<ul class="dropdown-menu batch-dropdown">
						<li class="batch-option"><a class="batch-archive-btn" href="#"><i class="fa fa-file-archive-o" aria-hidden="true"></i>Archive</a></li>
						<li class="batch-option"><a class="batch-active-btn" href="#"><i class="fa fa-power-off" aria-hidden="true"></i>Toggle Active</a></li>
						<li class="batch-option"><a class="batch-feature-btn" href="#"><i class="fa fa-star" aria-hidden="true"></i>Feature</a></li>
						<li class="batch-option"><a class="batch-download-btn" href="#"><i class="fa fa-download" aria-hidden="true"></i>Download</a></li>
						<li role="separator" class="divider"></li>
						<li class="batch-option"><a class="batch-delete-btn" href="#"><i class="fa fa-trash-o"></i>Delete</a></li>
					</ul>
				</li>
			</ul>
			<div class="pull-right current-batch-action-options">
				<span><i class="fa fa-trash-o"></i>Delete</span>
				<button type="button" class="btn btn-custom batch-select-all"><i class="fa fa-check-square-o" aria-hidden="true"></i>Select All</button>
				<button type="button" class="btn btn-custom batch-apply">Apply</button>
				<button type="button" class="btn btn-custom batch-cancel">Cancel</button>
			</div>
		</div>
        <?php
		return ob_get_clean();	
	}
	
	public function indexAction(){
		$this->setNews();
		if(sizeof($this->getNews())){
			ob_start();
			foreach($this->getNews() as $news){
				$news->setHrefPrefix($this->getHrefPrefix());
				echo $news->toHtml('listing');
			}
			return ob_get_clean();
		}else{
			return '<p>No News Articles at this time. Please check back again later.</p>';
		}
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
		<form id="form" action="action_categories.php" method="post" enctype="multipart/form-data">
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
						<label for="name" class="required">Name</label>
						<input type="text" name="name" class="form-control required" value="<?php echo process($this->getName()); ?>" placeholder="Name" />
						<div class="error name"></div>
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
}
?>