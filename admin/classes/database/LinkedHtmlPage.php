<?php
class LinkedHtmlPage extends Model{
	// Module Configuration
	public $_moduleName  = 'Linked HTML Pages';
	public $_moduleDir   = 'linked_html_pages';
	public $_moduleTable = 'linked_html_pages';
	public $_moduleClassName = 'LinkedHtmlPage';
	public $_moduleDescription = 'This section allows administrators to create, modify, and delete HTML pages, with a linked photo gallery, within the site.';
	public $_moduleIcon = 'fa-pencil-square';
	public $_moduleDepthLimit = 3;
	public $_moduleThumbCanvasX = 450;
	public $_moduleThumbCanvasY = 75;
	public $_moduleImageCanvasX = 1200;
	public $_moduleImageCanvasY = 200;
	public $_moduleDepthFunctionNames = array('primary','secondary','tertiary');
	
	// Static Variables
	protected static $_addLabel = 'Add HTML Page';
	protected static $_editLabel = 'Edit HTML Page';

	// Inherited Variables
	protected $_dbTable = 'linked_html_pages';
	protected $_permalinkField = 'permalink_full';
	protected $_action = 'index';

	// Table Variables
	protected $_id;
	protected $_parentId = 0;
	protected $_name;
	protected $_html;
	protected $_url;
	protected $_image = '';
	protected $_bannerTitle;
	protected $_bannerText;
	protected $_bannerUrl;
	protected $_associatedPhotoGallery;
	protected $_associatedPhotos;
	protected $_permalink;
	protected $_permalinkFull;
	protected $_depth;
	protected $_locked = '0';
	protected $_active = '0';
	protected $_sortOrder = 0;

	// Instance Variables
	protected $_parent = NULL;
	protected $_children = NULL;

	protected $_requiredFields = array(
									'name'
									);
	protected $_saveFields = array(
									'parent_id',
									'name',
									'html',
									'url',
									'image',
									'banner_title',
									'banner_text',
									'banner_url',
									'associated_photo_gallery',
									'associated_photos',
									'permalink',
									'permalink_full',
									'depth',
									'locked',
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
	public static function getAddLabel(){return self::$_addLabel;}
	public static function setEditLabel($value){self::$_editLabel = (string) $value;}
	public static function getEditLabel(){return self::$_editLabel;}

	// Accessor Methods
	public function setId($value){$this->_id = (int) $value; return $this;}
	public function getId(){return $this->_id;}
	public function setParentId($value){$this->_parentId = (int) $value; return $this;}
	public function getParentId(){return $this->_parentId;}
	public function setName($value){$this->_name = (string) $value; return $this;}
	public function getName(){return $this->_name;}
	public function setHtml($value){$this->_html = (string) $value; return $this;}
	public function getHtml(){return $this->_html;}
	public function setUrl($value){$this->_url = (string) $value; return $this;}
	public function getUrl(){return $this->_url;}
	public function setImage($value){$this->_image = (string) $value; return $this;}
	public function getImage(){return $this->_image;}
	public function setBannerTitle($value){$this->_bannerTitle = (string) $value; return $this;}
	public function getBannerTitle(){return $this->_bannerTitle;}
	public function setBannerText($value){$this->_bannerText = (string) $value; return $this;}
	public function getBannerText(){return $this->_bannerText;}
	public function setBannerUrl($value){$this->_bannerUrl = (string) $value; return $this;}
	public function getBannerUrl(){return $this->_bannerUrl;}
	public function setAssociatedPhotoGallery($value){$this->_associatedPhotoGallery = $value; return $this;}
	public function getAssociatedPhotoGallery(){return $this->_associatedPhotoGallery;}
	public function setAssociatedPhotos($value){$this->_associatedPhotos = $value; return $this;}
	public function getAssociatedPhotos(){return $this->_associatedPhotos;}
	public function setPermalink($value){$this->_permalink = (string) $value; return $this;}
	public function getPermalink(){return $this->_permalink;}
	public function setPermalinkFull($value){$this->_permalinkFull = (string) $value; return $this;}
	public function getPermalinkFull(){return $this->_permalinkFull;}
	public function setDepth($value){$this->_depth = (int) $value; return $this;}
	public function getDepth(){return $this->_depth;}
	public function setLocked($value){$this->_locked = (string) $value; return $this;}
	public function getLocked(){return $this->_locked;}
	public function setActive($value){$this->_active = (string) $value; return $this;}
	public function getActive(){return $this->_active;}
	public function setSortOrder($value){$this->_sortOrder = (int) $value; return $this;}
	public function getSortOrder(){return $this->_sortOrder;}
	
	// Instance Methods
	public function install()
	{
		# Register module
		$this->register();
		
		# Create table
		if(!$this->isInstalled()){
			$query = "
			CREATE TABLE `".$this->_dbTable."` (
			 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			 `parent_id` int(10) unsigned NOT NULL DEFAULT '0',
			 `name` varchar(255) NOT NULL DEFAULT '',
			 `html` text NOT NULL,
			 `url` varchar(255) NOT NULL DEFAULT '',
			 `image` varchar(255) NOT NULL,
			 `banner_title` varchar(255) NOT NULL DEFAULT '',
			 `banner_text` varchar(255) NOT NULL DEFAULT '',
			 `banner_url` varchar(255) NOT NULL DEFAULT '',
			 `associated_photo_gallery` int(11) NOT NULL,
			 `associated_photo` int(11) NOT NULL,
			 `permalink` varchar(255) NOT NULL DEFAULT '',
			 `permalink_full` varchar(255) NOT NULL,
			 `depth` tinyint(4) NOT NULL,
			 `locked` enum('0','1') NOT NULL DEFAULT '0',
			 `active` enum('0','1') NOT NULL DEFAULT '0',
			 `sort_order` int(10) unsigned NOT NULL DEFAULT '0',
			 PRIMARY KEY (`id`),
			 KEY `parent_id` (`parent_id`) USING BTREE,
			 KEY `permalink` (`permalink`) USING BTREE
			) ENGINE=MyISAM AUTO_INCREMENT=124 DEFAULT CHARSET=utf8
			";
			$result = $this->create($query);
		}
		
		return $this;
	}

	public function validate()
	{
		if(!$this->checkRequired()){
			$this->addMessage('general',array('type'=>'failure','text'=>'Please complete all required fields.'));
		}
		return $this;
	}

	public function setParent($value = '')
	{
		if(empty($value)){
			if($this->getId()){
				$this->_parent = new self($this->getParentId());
			}else{
				$this->_parent = new self();
			}
		}else{
			$this->_parent = $value;
		}
		return $this;
	}

	public function getParent()
	{
		if($this->_parent === NULL){
			$this->setParent();
		}
		return $this->_parent;
	}

	public function setChildren($value = '',$activeOnly = '')
    {
        if(empty($value)){
            if(strlen(trim($activeOnly))){
                $activeFilter = " AND `active` = '1'";
            }else{
                $activeFilter = '';
            }
            $children = $this->fetchAll("WHERE `parent_id` = '".$this->getId()."'".$activeFilter,"ORDER BY `sort_order`");
            $this->_children = $children;
        }else{
            $this->_children = $value;
        }
        return $this;
    }
    public function getChildren($activeOnly = false)
    {
        if($this->_children === NULL){
            $this->setChildren('',$activeOnly);
        }
        return $this->_children;
    }

	public function getRoot()
	{
		$htmlPage = $this;
		$parentId = $htmlPage->getParentId();
		while($parentId != 0){
			$htmlPage = new $this->_moduleClassName($parentId);
			$parentId = $htmlPage->getParentId();
		}
		return $htmlPage;
	}
	public function hasParent($permalink)
	{
		$htmlPage = $this;
		$parentId = $htmlPage->getParentId();
		while($parentId != 0){
			$htmlPage = new $this->_moduleClassName($parentId);
			if($htmlPage->getPermalink() == $permalink){
				return true;
			}
			$parentId = $htmlPage->getParentId();
		}
		return false;
	}

	public function calculateDepth()
	{
		$depth = 1;
		$object = $this;
		while($object->getParentId() != 0){
			$depth++;
			$object = new $this->_moduleClassName($object->getParentId());
		}
		return $depth;
	}

	public function getTreeIds()
	{
		$object = $this;
		$treeIds[] = $object->getId();
		while($object->getParentId() != 0){
			$object = new $this->_moduleClassName($object->getParentId());
			$treeIds[] = $object->getId();
		}
		return $treeIds;
	}

	public function getChildrenTreeIds()
	{
		$object = $this;
		$treeIds = array();
		if(sizeof($this->getChildren())){

		}
	}
	
	public function buildHref($traversing = false)
	{
		if(strlen(trim($this->getUrl())) && !$traversing){
			return $this->getUrl();
		}else{
			return '/'.$this->getPermalinkFull();
		}
	}

	public function buildPermalinkFull()
	{
		if($this->getParentId() == 0){
			return $this->getPermalink();
		}else{
			$parent = new $this->_moduleClassName($this->getParentId());
			return $parent->buildPermalinkFull().'/'.$this->getPermalink();
		}
	}

	public function updateChildPermalinks()
	{
		$children = $this->getChildren();
		foreach($children as $child){
			$child->setPermalinkFull($child->buildPermalinkFull());
			$child->save();
			$child->updateChildPermalinks();
		}
	}

	public function buildLink()
	{
		if(stristr($this->getUrl(),'http') || stristr($this->getUrl(),'https')){
			$class = 'class="external"';
		}else{
			$class = '';
		}
		return '<a href="'.$this->buildHref().'" '.$class.'>'.$this->getName().'</a>';
	}

	public function buildImage()
	{
		if($this->getImage()){
			return '<img src="/files/'.$this->getDbTable().$this->getId().$this->getImage().'" alt="'.$this->getName().'" />';
		}else{
			if(!$this->getParentId()){
			}else{
				$parent = new $this->_moduleClassName($this->getParentId());
				return $parent->buildImage();
				//$root = $this->getRoot();
				//return $root->buildImage();
			}
		}
		return '<img src="/images/banner-default.jpg" alt="'.$this->getName().'" />';
	}

	public function buildImageSrc()
	{
		if($this->getImage()){
			return '/files/'.$this->getDbTable().$this->getId().'.'.$this->getImage();
		}else{
			if(!$this->getParentId()){
			}else{
				$parent = new $this->_moduleClassName($this->getParentId());
				return $parent->buildImageSrc();
				//$root = $this->getRoot();
				//return $root->buildImage();
			}
		}
		return '/images/banner-default.jpg';
	}

	public function buildBreadcrumb($showHome = true, $showSelf = true)
    {
        $treeIds = array_reverse($this->getTreeIds());
        // If only one item don't show the breadcrumb
        if(sizeof($treeIds) <= 1){
            return;
        }
        if(sizeof($treeIds)){
            ?>
            <div class="twelve columns breadcrumb">
            <ul class="breadcrumb-list">
            <?php
            ob_start();
            if($showHome){
                ?>
                <li><a href="/">Home</a></li>
                <?php
            }
            foreach($treeIds as $id){
                $object = new $this->_moduleClassName($id);
                if($object->getId() == $this->getId()){
                    $currentClass = 'current_page';
                    $liHtml = $object->getName();
                }else{
                    $currentClass = '';
                    $liHtml = $object->buildLink();
                }
                if(!$showSelf && $object->getId() == $this->getId()){
                }else{
                    ?>
                    <li class="<?php echo $currentClass; ?>"><?php echo $liHtml; ?></li>
                    <?php
                }
            }
            ?>
            </ul>
            </div>
            <?php
            return ob_get_clean();
        }
    }

	// Action Methods
	public function moduleIndexAction(){
		
		ob_start();
		?>
		
		<div class="index-wrapper">
			<div class="row">
				<div class="col-xs-12">
					<a class="btn btn-green pull-left" href="<?php echo $this->buildModalUrl('add', 0); ?>" data-toggle="modal" data-target="#moduleModal"><i class="fa fa-plus"></i>Create New HTML Page</a>
				</div>
			</div>
			<?php 
			echo $this->buildSortingStructure();
			echo $this->buildAdminListJavascript();
			?>
		</div>
		
		<?php
		return ob_get_clean();
	}
	public function indexAction()
	{
		ob_start();
		?>
		<h2><?php echo process($this->getName()); ?></h2>
		<?php echo $this->getHtml(); ?>
		<?php
		return ob_get_clean();
	}

    public function mainSubNavigationAction()
    {
        $children = $this->getChildren(true);
        if(sizeof($children)){
            ob_start();
            ?>
            <ul class="main_sub_navigation">
                <?php
                foreach($children as $child){
                    ?><li><?php echo $child->buildLink(); ?></li><?php
                }
                ?>
            </ul>
            <?php
            return ob_get_clean();
        }
    }

    public function subNavigationHelper($selected,$selectedDepth,$selectedTreeIds)
    {
        // Current depth specifics can be defined here
        $currentDepth = $this->getDepth();
        switch($currentDepth){
            case '1':
                $ulClass = 'content_subnavigation';
            break;

            default:
                $ulClass = '';
            break;

        }

        // Get the children
        $children = $this->getChildren(true);

        // Base case, no children
        if(!sizeof($children)){
            return;
        }

        // Start a new ul block, recursively cycle through children
        ob_start();
        ?>
        <ul class="<?php echo $ulClass; ?>">
        <?php
        foreach($children as $child){
            if($child->getPermalink() == $selected){
                $selectedClass = 'selected';
            }else{
                $selectedClass = '';
            }
            if(strlen(trim($child->getUrl()))){
                if(preg_match('#^http[s]?://#',$child->getUrl())){
                    $externalClass = 'external';
                }else{
                    $externalClass = '';
                }
            }
            ?>
            <li>
            <a href="<?php echo $child->buildHref(); ?>" class="<?php echo $selectedClass; ?> <?php echo $externalClass; ?>"><?php echo $child->getName(); ?></a>
            <?php //echo $child->buildLink(); ?>
            <?php
            if(in_array($child->getId(),$selectedTreeIds)){
                // Child is a parent in the tree, try to show it's children
                echo $child->subNavigationHelper($selected,$selectedDepth,$selectedTreeIds);
            }
            ?>
            </li>
            <?php
        }
        ?>
        </ul>
        <?php
        return ob_get_clean();
    }

    public function subNavigationAction()
    {
        // Initialize some defaults
        $root = $this->getRoot();
        $selected = $this->getPermalink();
        $selectedDepth = $this->getDepth();
        $selectedTreeIds = $this->getTreeIds();

        // Get the root children to begin the sub navigation
        $rootChildren = $root->getChildren(true);
        ob_start();
        if(sizeof($rootChildren)){
            ?>
            <div class="content_subnavigation_container">
            <?php
            echo $root->subNavigationHelper($selected,$selectedDepth,$selectedTreeIds);
            ?>
            </div>
            <?php
        }
        return ob_get_clean();
    }


	public function defaultListAction()
	{			
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
				<?php echo $this->buildToggleButton(); ?>
				<?php if($this->getLocked() == 1){?><span class="label label-danger pull-right toggle-label"><i class="fa fa-lock"></i>Locked</span><?php }?>
			</div>
	   </div>
        <?php
		return ob_get_clean();
	}
	
	public function menuAction()
	{			
		# Popover Menu
		ob_start(); ?>
        <ul class="actions">
			<li><a href="<?php echo $this->buildModalUrl('edit'); ?>" data-target="#moduleModal" data-toggle="modal" ><i class="fa fa-pencil"></i>Edit</a></li>
			<li><a href="<?php echo $this->buildModalUrl('add', $this->getId()); ?>" data-target="#moduleModal" data-toggle="modal" ><i class="fa fa-plus"></i>Add Sub Page</a></li>
			
			<?php if($_SESSION['session_fullname'] == $GLOBALS['sstg_admin']){?><li><?php echo $this->buildToggle("locked"); ?></li><?php }?>
			<li><a href="/<?php echo $this->getPermalinkFull(); ?>" target="_blank"><i class="fa fa-search"></i>View Page</a></li>
			<?php if($this->getLocked() != 1){?><li><a href="<?php echo $this->buildModalUrl('confirm','delete_confirm'); ?>" class="delete" data-toggle="modal" data-target="#confirmModal"><i class="fa fa-trash-o"></i>Delete Page</a></li><?php }?>
		</ul>
        <?php
		return ob_get_clean();
	}

	public function adminNewRecord($parent_id)
	{
		$this->setParentId($parent_id);
	}
	
	public function adminAddAction()
	{
		return $this->buildAdminAddEditHtml('add');
	}

	public function adminEditAction()
	{
		return $this->buildAdminAddEditHtml('edit');
	}

	protected function buildAdminAddEditHtml($action)
	{
		if(!in_array($action,array('add','edit'))){
			return '';
		}
		$actionLabel = $this->{'get'.ucfirst($action).'Label'}();
		
		# Messages
		$messages = $this->prepareMessages();
		$this->clearMessages();
		 
		# Form
		ob_start();
		?>
        <div class="error general"></div>
		<form action="action.php" id="form" method="post" enctype="multipart/form-data">
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
					<!-- Parent Page -->
					<div class="form-group">
						<label for="parent_id" class="required">Parent Page</label>
						<?php echo $this->buildParentPageSelect($this->getParentId()); ?>
					</div>
					<!-- Name -->
					<div class="form-group">
						<label for="name" class="required">Name</label><br>
						<?php 
						# If the page is locked
						if($this->getLocked()){ ?>
							<input type="text" name="name" value="<?php echo process($this->getName()); ?>" class="form-control" disabled />
							<h5><span class="label label-info"><i class="fa fa-lock"></i>This field has been locked from editing</span></h5>
						<?php 
						}
						# Otherwise - if the page is not locked
						else{?>
							<input type="text" name="name" class="form-control required" value="<?php echo process($this->getName()); ?>" placeholder="Name" />
							<div class="error name"></div>
						<?} ?>
					</div>
					<!-- URL -->
					<div class="form-group">
						<label for="url">URL</label>
						<input type="text" name="url" class="form-control" value="<?php echo process($this->getUrl()); ?>" placeholder="URL" />
						<div class="instruction">
							<ul>
								<li>Examples: "/privacy-policy" or "http://www.stantonstreet.com"</li>
								<li>Specifying a URL will override this page's default link</li>
							</ul>
						</div>
                   		<div class="error url"></div>
                    </div>
                    <!-- Associated Photos -->
                    <div class="form-group">
                    	<label for="associated_photo">Associated Photos</label>
                    	<?php echo FormComponent::linkedModule('Photo', 'associated_photos', unserialize($this->getAssociatedPhotos()), 1); ?>
                    </div>
                    <!-- Associated Photo Gallery -->
                    <div class="form-group">
                    	<label for="associated_photo">Associated Photo Gallery</label>
                    	<?php echo FormComponent::linkedModule('PhotoCategory', 'associated_photo_gallery', $this->getAssociatedPhotoGallery()); ?>
                    </div>
				</div>
				<div class="col-sm-6">
					<div class="panel panel-custom">
						<div class="panel-heading">Banner</div>
						<div class="panel-body">
							<!-- Banner Image -->
							<?php $image = new Image(); echo $image->manageImage($this->getDbTable(),$this->getId(),1,$this->getImage(),$this->_moduleImageCanvasX,$this->_moduleImageCanvasY); ?>
							<!-- Banner Title -->
							<div class="form-group">
								<label for="banner_title">Banner Title</label>
								<input type="text" name="banner_title" class="form-control" value="<?php echo process($this->getBannerTitle()); ?>" placeholder="Banner Title" />
								<div class="error banner_title"></div>
							</div>
							<!-- Banner Text -->
							<div class="form-group">
								<label for="banner_text">Banner Text</label>
								<input type="text" name="banner_text" class="form-control" value="<?php echo process($this->getBannerText()); ?>" placeholder="Banner Text" />
								<div class="error banner_text"></div>
                    		</div>
                    		<!-- Baner URL -->
                    		<div class="form-group">
								<label for="banner_url">Banner URL</label>
								<input type="text" name="banner_url" class="form-control" value="<?php echo process($this->getBannerUrl()); ?>" placeholder="Banner URL" />
								<div class="instruction">
									<ul>
										<li>Examples: "/privacy-policy" or "http://www.stantonstreet.com"</li>
									</ul>
								</div>
								<div class="error banner_url"></div>
                    		</div>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12">
					<!-- HTML -->
					<div class="form-group">
						<label for="html">HTML Content</label>
						<div class="error html"></div>
						<?php echo FormComponent::textareaFull('html', process($this->getHtml()), 'html', 'form-control required'); ?>
					</div>
				</div>
			</div>
			<?php if($action == 'edit'): ?> 
			<input type="hidden" name="id" value="<?php echo $this->getId(); ?>" />
			<?php endif; ?> 
			<input type="hidden" name="locked" value="<?php echo $this->getLocked();?>" />
			<input type="hidden" name="action" value="<?php echo $action; ?>" />
		</form>
		<?php
		
		$form = ob_get_clean();
		
		$modal = new Module();
		return $modal->buildInnerModal($actionLabel, $form);
	}
	
	public function buildParentPageSelect($selected_parent = '0')
	{		
		$select = "<select class='selectpicker' name='parent_id' data-none-selected-text=''>";
		
		// Get Parent Pages
		$parents = new HtmlPage();
		$parents = $parents->fetchAll("WHERE `parent_id` = 0", "ORDER BY `sort_order` ASC");
		
		// Empty Option
		if($selected_parent == '0'){
			$select .= "<option value='0' selected='selected'>HTML Pages</option>";
		}
		else{
			$select .= "<option value='0'>HTML Pages</option>";
		}

		// Divider 
		$select .= "<option data-divider='true'></option>";
		
		foreach($parents as $parent){
			$select .= $parent->buildOptions($selected_parent);
		
			// Divider 
			$select .= "<option data-divider='true'></option>";
		}
				
		$select .= "</select>";
		return $select;
	}
	
	public function buildOptions($selected_parent = '0')
	{
		$paddingBase = 10;
		$paddingIncrement = 15;
		$depth = $this->calculateDepth()+1;
		$padding = ($paddingIncrement*$depth)+$paddingBase;
		
		if($selected_parent == $this->getId()){
			$select = "<option style='padding-left: ".$padding."px' value='".$this->getId()."' selected='selected' >".$this->getName()."</option>";			
		}
		else{
			$select = "<option style='padding-left: ".$padding."px' value='".$this->getId()."' >".$this->getName()."</option>";
		}
		
		$subpages = $this->getChildren();
	
		foreach($subpages as $subpage){
			$select .= $subpage->buildOptions($selected_parent);
		}
		
		return $select;
	}
	
	/* Builds Nested list for sorting */
	public function buildSortingStructure($max_levels = 'all', $root_parent_id = 0, $sorting_enabled = 1,$current_level = 1, $single = 0){
		if($max_levels != 'all'){
			$max_levels	= intval($max_levels);
		}
		$className = get_class($this);
		$branches = new $className();
		$branches = $branches->fetchAll("WHERE `parent_id` = " . $root_parent_id, "ORDER BY `sort_order`");
		$list_items = '';
		$tree = '';

		if(count($branches)){
			ob_start();
			if(!$single){
				echo '<ol class="'.($current_level==1 ? 'sortable ' : '').'">';
			}
			//foreach child belonging to the specified parent id
			foreach($branches as $branch){
				if($branch->getLocked() == 1){
					echo '<li id="menuItem_'.$branch->getId().'" class="lvl_'.$current_level.' not_sortable">';
				}
				else{
					echo '<li id="menuItem_'.$branch->getId().'" class="lvl_'.$current_level.'">';
				}
				//if(method_exists($branch,$branch->_moduleDepthFunctionNames[$current_level-1].'Action')){
					//echo $branch->toHtml($branch->_moduleDepthFunctionNames[$current_level-1]);
				//}else{
					echo $branch->toHtml('default_list');
				//}
				if($max_levels == 'all'){
					echo $this->buildSortingStructure('all', $branch->getId(), 1, $current_level+1);
				}
				else if($max_levels > 1){
					echo $this->buildSortingStructure($max_levels-1, $branch->getId(), 1, $current_level+1);
				}
				echo '</li>';
			}
			if(!$single){
				echo '</ol>';
			}
			$list_items = ob_get_clean();

			if($root_parent_id == 0){
				ob_start();
				echo $list_items;
                $tree = ob_get_clean();
			}
			else{
				$tree = $list_items;
			}
		}
		else if(!count($branches) && $single){
			$leaf = new HtmlPage($root_parent_id);
			ob_start();?>
			<li id="menuItem_<?php echo $leaf->getId(); ?>" class="lvl_'.$current_level.'">
				<?php echo $leaf->toHtml('default_list'); ?>
			</li>
			<?php
			$tree = ob_get_clean();
		}
		else if(!count($branches) && $root_parent_id == 0){
			ob_start();
			?>
			<div class="no_records"><i class="fa fa-times-circle"></i>No records available.</div>
			<ol class="<?php echo ($currentLevel==1 ? 'sortable ' : ''); ?>"></ol>
			<?php
			$tree = ob_get_clean();
		}
		else{
			$tree = "<ol></ol>";
		}
		
		return $tree;
	}
	
	public function buildAdminListJavascript(){	
		$maxLevels = sizeof($this->_moduleDepthFunctionNames);
		ob_start();?>
		<script>
			function moduleSpecificRefresh(content,action){
				if(action === 'add' || action === 'replace'){
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
					maxLevels: <?php echo $maxLevels; ?>,
					isTree: true,
					expandOnHover: 700,
					protectRoot: false,
					startCollapsed: true,
					<?php if($maxLevels > 1){?>
					isAllowed: function(placeholder, placeholderParent, currentItem){
						if(currentItem.hasClass('not_sortable')){
							return false;
						}
						window.currentId = currentItem[0].id.split("_")[1];
						return true;
						/*
						if (placeholderParent == null){
							return true;
						}else{
							var parentLevel=placeholderParent[0].className.split(" ")[0];
							parentLevel=parseInt(parentLevel.split("_")[1])+1;
							var movingLevel=currentItem[0].className.split(" ")[0];
							movingLevel=parseInt(movingLevel.split("_")[1]);

							if(parentLevel == movingLevel){return true;}else{return false;}
						}*/
					},
					<?php } ?>
					relocate: function(){
						toggleVisible();
						//console.log(currentId);
						$.ajax({
							type: 'POST',
							url:"../../includes/sort_list.php",
							data: $('ol.sortable').nestedSortable('serialize')+"&id="+currentId+"&module=<?php echo $this->_moduleClassName; ?>",
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
	
	public function BatchCommandsAction(){
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
	
}
?>
