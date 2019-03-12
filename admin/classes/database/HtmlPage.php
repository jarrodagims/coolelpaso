<?php
class HtmlPage extends Model{
	// Module Configuration
	public $_moduleName  = 'Pages';
	public $_moduleDir   = 'html_pages';
	public $_moduleTable = 'html_pages';
	public $_moduleClassName = 'HtmlPage';
	public $_moduleDescription = 'This section allows administrators to create, modify, and delete pages within the site.';
	public $_moduleIcon = 'fa-pencil';
	public $_moduleDepthLimit = 3;
	public $_moduleThumbCanvasX = 450;
	public $_moduleThumbCanvasY = 75;
	public $_moduleImageCanvasX = 1589;
	public $_moduleImageCanvasY = 800;
	public $_moduleImageProductCanvasX = 400;
	public $_moduleImageProductCanvasY = 400;
	public $_moduleDepthFunctionNames = array('primary','secondary','tertiary');
	
	// Static Variables
	protected static $_addLabel = 'Add HTML Page';
	protected static $_editLabel = 'Edit HTML Page';

	// Inherited Variables
	protected $_dbTable = 'html_pages';
	protected $_permalinkField = 'permalink_full';
	protected $_action = 'index';

	// Table Variables
	protected $_id;
	protected $_parentId = 0;
	protected $_name;
	protected $_html;
	protected $_url;
	protected $_image = '';
	protected $_imageTwo = '';
	protected $_imageThree = '';
	protected $_bannerTitle;
	protected $_bannerText;
	protected $_bannerUrl;
	protected $_permalink;
	protected $_permalinkFull;
	protected $_customPage = '';
	protected $_useForm;
	protected $_depth;
	protected $_locked = '0';
	protected $_active = '0';
	protected $_superAdmin = '0';
	protected $_searchable = '1';
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
									'image_two',
									'image_three',
									'banner_title',
									'banner_text',
									'banner_url',
									'permalink',
									'permalink_full',
									'custom_page',
									'use_form',
									'depth',
									'locked',
									'active',
									'super_admin',
									'searchable',
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
	public function setImageTwo($value){$this->_imageTwo = (string) $value; return $this;}
	public function getImageTwo(){return $this->_imageTwo;}
	public function setImageThree($value){$this->_imageThree = (string) $value; return $this;}
	public function getImageThree(){return $this->_imageThree;}
	public function setBannerTitle($value){$this->_bannerTitle = (string) $value; return $this;}
	public function getBannerTitle(){return $this->_bannerTitle;}
	public function setBannerText($value){$this->_bannerText = (string) $value; return $this;}
	public function getBannerText(){return $this->_bannerText;}
	public function setBannerUrl($value){$this->_bannerUrl = (string) $value; return $this;}
	public function getBannerUrl(){return $this->_bannerUrl;}
	public function setPermalink($value){$this->_permalink = (string) $value; return $this;}
	public function getPermalink(){return $this->_permalink;}
	public function setPermalinkFull($value){$this->_permalinkFull = (string) $value; return $this;}
	public function getPermalinkFull(){return $this->_permalinkFull;}
	public function setCustomPage($value){$this->_customPage = (string) $value; return $this;}
	public function getCustomPage(){return $this->_customPage;}
	public function setUseForm($value){$this->_useForm = (string) $value; return $this;}
	public function getUseForm(){return $this->_useForm;}
	public function setDepth($value){$this->_depth = (int) $value; return $this;}
	public function getDepth(){return $this->_depth;}
	public function setLocked($value){$this->_locked = (string) $value; return $this;}
	public function getLocked(){return $this->_locked;}
	public function setActive($value){$this->_active = (string) $value; return $this;}
	public function getActive(){return $this->_active;}
	public function setSuperAdmin($value){$this->_superAdmin = (string) $value; return $this;}
	public function getSuperAdmin(){return $this->_superAdmin;}
	public function setSearchable($value){$this->_searchable = (string) $value; return $this;}
	public function getSearchable(){return $this->_searchable;}
	public function setSortOrder($value){$this->_sortOrder = (int) $value; return $this;}
	public function getSortOrder(){return $this->_sortOrder;}

	// Instance Methods
	public function install(){
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
			 `permalink` varchar(255) NOT NULL DEFAULT '',
			 `permalink_full` varchar(255) NOT NULL,
			 `custom_page` varchar(255) NOT NULL,
			 `use_form` varchar(3) NOT NULL,
			 `depth` tinyint(4) NOT NULL,
			 `locked` enum('0','1') NOT NULL DEFAULT '0',
			 `active` enum('0','1') NOT NULL DEFAULT '0',
  			 `super_admin` enum('0','1') NOT NULL DEFAULT '0',
  			 `searchable` enum('1','2') NOT NULL DEFAULT '1',
			 `sort_order` int(10) unsigned NOT NULL DEFAULT '0',
			 PRIMARY KEY (`id`),
			 KEY `parent_id` (`parent_id`) USING BTREE,
			 KEY `permalink` (`permalink`) USING BTREE
			) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8
			";
			$result = $this->create($query);
		}
		
		return $this;
	}

	public function validate(){
		if(!$this->checkRequired()){
			$this->addMessage('general',array('type'=>'failure','text'=>'Please complete all required fields.'));
		}
		return $this;
	}
	
	public function getFileList(){
		$files = array();
		$files[] = '';
		foreach (glob("../../*.php") as $file) {
		  $files[] = str_replace("../../", "", $file);
		}
		return $files;
	}

	public function setParent($value = ''){
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

	public function getParent(){
		if($this->_parent === NULL){
			$this->setParent();
		}
		return $this->_parent;
	}

	public function setChildren($value = '',$activeOnly = ''){
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
	
    public function getChildren($activeOnly = false){
        if($this->_children === NULL){
            $this->setChildren('',$activeOnly);
        }
        return $this->_children;
    }

	public function getRoot(){
		$htmlPage = $this;
		$parentId = $htmlPage->getParentId();
		while($parentId != 0){
			$htmlPage = new $this->_moduleClassName($parentId);
			$parentId = $htmlPage->getParentId();
		}
		return $htmlPage;
	}
	
	public function hasParent($permalink){
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

	public function calculateDepth(){
		$depth = 1;
		$object = $this;
		while($object->getParentId() != 0){
			$depth++;
			$object = new $this->_moduleClassName($object->getParentId());
		}
		return $depth;
	}

	public function getTreeIds(){
		$object = $this;
		$treeIds[] = $object->getId();
		while($object->getParentId() != 0){
			$object = new $this->_moduleClassName($object->getParentId());
			$treeIds[] = $object->getId();
		}
		return $treeIds;
	}

	public function isLeaf(){
		$children = new $this->_moduleClassName();
		$children = $children->fetchCount("WHERE `parent_id` = '".$this->getId()."'");
		if($children){return false;}
		return true;
	}
	
	public function buildHref($traversing = false){
		if(strlen(trim($this->getUrl())) && !$traversing){
			return $this->getUrl();
		}else{
			return '/'.$this->getPermalinkFull();
		}
	}

	public function buildPermalinkFull(){
		if($this->getParentId() == 0){
			return $this->getPermalink();
		}else{
			$parent = new $this->_moduleClassName($this->getParentId());
			return $parent->buildPermalinkFull().'/'.$this->getPermalink();
		}
	}

	public function updateChildPermalinks(){
		$children = $this->getChildren();
		foreach($children as $child){
			$child->setPermalinkFull($child->buildPermalinkFull());
			$child->save();
			$child->updateChildPermalinks();
		}
	}

	public function buildLink($mobile = false){
		
		if($mobile){
			$mobileClass = ' class="nav-link"';
		}else{
			$mobileClass = '';
		}
		
		if(stristr($this->getUrl(),'http') || stristr($this->getUrl(),'https')){
			$class = ' class="external"';
		}else{
			$class = '';
		}
		return '<a'.$mobileClass.' href="'.$this->buildHref().'"'.$class.' title="'.$this->getName().'">'.$this->getName().'</a>';
	}
	
	public function buildBreadCrumbLink($mobile = false){
		
		if($mobile){
			$mobileClass = ' class="nav-link"';
		}else{
			$mobileClass = '';
		}
		
		if(stristr($this->getUrl(),'http') || stristr($this->getUrl(),'https')){
			$class = ' class="external"';
		}else{
			$class = '';
		}
		return '<a'.$mobileClass.' href="'.$this->buildHref().'"'.$class.' title="'.$this->getName().'">'.generateBlurb($this->getName(),30).'</a>';
	}

	public function buildImage(){
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

	public function buildImageSrc(){
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


	public function buildBreadcrumb($showHome = true, $showSelf = true, $extendedCrumbs = null){
        $treeIds = array_reverse($this->getTreeIds());
        // If only one item don't show the breadcrumb
        if(sizeof($treeIds) <= 1){
            return;
        }
        if(sizeof($treeIds)){
			ob_start();
            ?>
            <ul>
            <?php
            
            if($showHome){
                ?>
                <li><a href="/"><em class="fa fa-home"></em></a></li>
                <?php
            }
            foreach($treeIds as $id){
                $object = new $this->_moduleClassName($id);
                if($object->getId() == $this->getId() && !$extendedCrumbs){
                    $currentClass = 'class="active"';
                    $liHtml = $object->buildBreadCrumbLink();
                }else{
                    $currentClass = '';
                    $liHtml = $object->buildBreadCrumbLink();
                }
                if(!$showSelf && $object->getId() == $this->getId()){
                }else{
                    ?>
                    <li <?php echo $currentClass; ?>><?php echo $liHtml; ?></li>
                    <?php
                }
            }
			if($extendedCrumbs){
				end($extendedCrumbs);
				$end = key($extendedCrumbs);
				foreach($extendedCrumbs as $permalink => $name){ ?>
				<li <?php echo ($permalink == $end ? 'class="active"' : '') ?>><a href="/<?php echo $object->getpermalinkFull().'/'.$permalink; ?>"><?php echo $name; ?></a></li>
			<?php }
			} ?>
            </ul>
            <?php
            return ob_get_clean();
        }
    }

	
	public function sideNavAction($extendedNav = null){
		
		# Root Page
		$root = $this->getRoot();

		# Get Tree Ids for active page
		$treeIds = $this->getTreeIds();

		# Build Nested List
		$list = $root->buildNestedList($this->getId(), $treeIds, true, $extendedNav);
		ob_start();
		if(strlen(trim($list))){?>
			<div class="side-nav bg-blue white paralucent">
				<?php echo $list;?>
			</div>
		<?php
		}

		return ob_get_clean();
	}

	public function buildNestedList($activeId = 0, $treeIds = array(), $pathOnly = false, $extendedNav = null){

		# Get Children
		$children = $this->getChildren(true);

		# Start Buffer
		ob_start();
		if(sizeof($children)){ ?>
		
		<ul class="ul-first">
			<?php
			foreach($children as $child){

				# Get active class
				$class= "";
				if($child->getId() == $activeId && ltrim($_SERVER['REQUEST_URI'], '/') == $child->getpermalinkFull()){
					$class = "active ";
				}

				if(preg_match("/http[s]{0,1}:\/\//",$child->getUrl())){
					$class = "external ";
				}

				$class = "class='".$class."'";

				# Build Link
				if($child->getActive()){
				?>
					<li>
						<a href="<?php echo $child->buildHref(); ?>" title="<?php echo $child->getName(); ?>" <?php echo $class; ?> >
							<span><?php echo $child->getName(); ?></span>
						</a>
						
						<?php

						# If the page is part of the tree - build out the children
						if($pathOnly){
							if(in_array($child->getId(), $treeIds)){
								echo $child->buildNestedList($activeId, $treeIds, true, $extendedNav);
								if(!sizeof($child->getChildren(true)) && $extendedNav){?>
									<ul>
									<?php foreach($extendedNav as $permalink => $name){  ?>
										<li>
											<a href="<?php echo '/'.$child->getpermalinkFull().'/'.$permalink; ?>" title="<?php echo $name; ?>" <?php echo (strstr($_SERVER['REQUEST_URI'], $permalink)  ? 'class="active"' : ''); ?>><span><?php echo $name; ?></span></a>
										</li>
									<?php } ?>
									</ul>
								<?php } 
							}
						}
						else{
							echo $child->buildNestedList();
							
						}
						?>
					</li>
				<?php
				}
			}
			?>
		</ul>

		<?php
		}else{
			if(sizeof($extendedNav)){?>
				<ul>
				<?php foreach($extendedNav as $permalink => $name){  ?>
					<li>
						<a href="<?php echo '/'.$this->getpermalinkFull().'/'.$permalink; ?>" title="<?php echo $name; ?>" <?php echo (strstr($_SERVER['REQUEST_URI'], $permalink)  ? 'class="active"' : ''); ?>><span><?php echo $name; ?></span></a>
					</li>
				<?php } ?>
				</ul>
			<?php }
		}

		# Return Buffer
		return ob_get_clean();
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
			
			/*
			echo "<br /><br /><br /><br /><hr />";
			echo $this->buildSortingStructure();
		
			echo "<br /><br /><br /><br /><hr />";
			echo $this->buildSortingStructure('all', 119);
		
			echo "<br /><br /><br /><br /><hr />";
			echo $this->buildSortingStructure(2, 119);
		
			echo "<br /><br /><br /><br /><hr />";
			echo $this->buildSortingStructure('all', 119, 1, 1, 1);
			*/
				
			echo $this->buildAdminListJavascript();
		
			?>
		</div>
		
		<?php
		return ob_get_clean();
	}
	
	public function indexAction(){
		ob_start();
		if($this->getImageTwo() || $this->getImageThree()){?>
		<div class="row">
			<div class="col-md-8">
				<?php echo $this->getHtml(); ?>
			</div>
			<div class="col-md-4">
				<?php if($this->getImageTwo()){ ?><img class="img-fluid" src="/files/<?php echo $this->getDbTable().$this->getId().'_two.'.$this->getImageTwo(); ?>" alt="<?php echo $this->getName(); ?>" /><br /><br /><?php } ?>
				<?php if($this->getImageThree()){ ?><img class="img-fluid"  src="/files/<?php echo $this->getDbTable().$this->getId().'_three.'.$this->getImageThree(); ?>" alt="<?php echo $this->getName(); ?>" /><?php } ?>
			</div>
		</div>
		<?php }else{ ?>
		<?php echo $this->getHtml(); ?>
		<?php } ?>
		<?php
		return ob_get_clean();
	}

    public function mainSubNavigationAction(){
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
	
	
    public function navMenuAction($excludePages = array(),$mobile = false){
        $topLevelItems = $this->fetchAll("WHERE `parent_id` = '0' AND `active` = '1'", "ORDER BY `sort_order`");
		ob_start();
		foreach($topLevelItems as $topLevelItem){ 
			if(!in_array($topLevelItem->getPermalink(), $excludePages)){
				$subLevelItems = $topLevelItem->getChildren(true);
				if(sizeof($subLevelItems)){ ?>
					<li class="dropdown" ><?php echo $topLevelItem->buildLink(); ?><span><a class="mobile-toggle" href="#"><em class="fa fa-chevron-down" aria-hidden="true"></em></a></span>
						<ul class="sub-menu">
							<?php foreach($subLevelItems as $subLevelItem){?>
								<li class="newsubmenu">
									<?php
									//added for extra level pages on main nav
									echo $subLevelItem->buildLink(); 
									$extraLevel = $subLevelItem->getChildren(true);
									//fix for mobile navigation sets a carret on the pages that actually have subpages
									if(sizeof($extraLevel)!=0){?>
									<span><a class="mobile-toggle" href="#"><em class="fa fa-chevron-down" aria-hidden="true"></em></a></span>
									<?php }  		
										echo '<ul class="submenu-level2">';
										foreach ($extraLevel as $level){
											echo '<li>'.$level->buildLink().'</li>';
										} 
										echo '</ul>';?>
								</li>
							<?php } ?>
						</ul>
					</li>
			<?php }else{ ?>
					<li<?php echo ($mobile ? ' class="nav-item"' : ''); ?>><?php echo $topLevelItem->buildLink($mobile); ?></li>
			<?php }
			}
		} 
		return  ob_get_clean();
    }
	
    public function navMenuMobileAction($excludePages = array()){
        $topLevelItems = $this->fetchAll("WHERE `parent_id` = '0' AND `active` = '1'", "ORDER BY `sort_order`");
		ob_start();
		foreach($topLevelItems as $topLevelItem){ 
			if(!in_array($topLevelItem->getPermalink(), $excludePages)){
				$subLevelItems = $topLevelItem->getChildren(true);
				if(sizeof($subLevelItems)){ ?>
					<li class="nav-item"><?php echo $topLevelItem->buildLink(); ?></li>
			<?php }else{ ?>
					<li class="nav-item"><?php echo $topLevelItem->buildLink(); ?></li>
			<?php }
			}
		} 
		return  ob_get_clean();
    }

    public function subNavigationHelper($selected,$selectedDepth,$selectedTreeIds){
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
			$externalClass = '';
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

    public function subNavigationAction(){
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
	

	public function searchResultAction(){
		ob_start(); ?>

		<a href="<?php echo $this->buildHref(); ?>" class="col-sm-12 no-underline">
			<div class="h3 blue no-underline"><?php echo $this->getName(); ?></div>
			<div class="search-description purple">
				<?php echo substr(strip_tags($this->getHtml()), 0 , 160)."..."; ?>
			</div>
		</a>

		<?php
		return ob_get_clean();
	}
	


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
					<?php echo $this->getName(); ?>
				</span>
				<span data-id="<?php echo $this->getId(); ?>" class="action_menu">
					<a tabindex="0" class="pull-right" role="button" data-toggle="popover" data-trigger="focus" data-placement="bottom" data-content='<?php echo $this->toHtml('menu'); ?>'><i class="fa fa-gear"></i></a>
				</span>
				<?php echo $this->buildToggleButton(); ?>
				<?php if($this->getLocked() == 1){?><span class="label label-danger pull-right toggle-label"><i class="fa fa-lock"></i>Locked</span><?php }?>
				<?php if($this->getSuperAdmin() == 1){?><span class="label label-default pull-right toggle-label"><i class="fa fa-eye-slash"></i>Super Admin Only</span><?php }?>
			</div>
	   </div>
        <?php
		return ob_get_clean();
	}
	
	public function menuAction(){			
		# Popover Menu
		ob_start(); ?>
        <ul class="actions">
			<li><a href="<?php echo $this->buildModalUrl('edit'); ?>" data-target="#moduleModal" data-toggle="modal" ><i class="fa fa-pencil"></i>Edit</a></li>
			<li><a href="<?php echo $this->buildModalUrl('add', $this->getId()); ?>" data-target="#moduleModal" data-toggle="modal" ><i class="fa fa-plus"></i>Add Sub Page</a></li>
			
			<?php if($_SESSION['session_fullname'] == $GLOBALS['sstg_admin']){?><li><?php echo $this->buildToggle("locked"); ?></li><li><?php echo $this->buildToggle("super_admin"); ?></li><?php }?>
			<li><a href="/<?php echo $this->getPermalinkFull(); ?>" target="_blank"><i class="fa fa-search"></i>View Page</a></li>
			<?php if($this->getLocked() != 1){?><li><a href="<?php echo $this->buildModalUrl('confirm','delete_confirm'); ?>" class="delete" data-toggle="modal" data-target="#confirmModal"><i class="fa fa-trash-o"></i>Delete Page</a></li><?php }?>
		</ul>
        <?php
		return ob_get_clean();
	}

	public function adminNewRecord($parent_id){
		$this->setParentId($parent_id);
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
					<!-- Custom Page -->
					<?php if($_SESSION['session_fullname'] == $GLOBALS['sstg_admin']){ ?>
					<div class="form-group">
						<label for="custom_page">Custom Page</label>
						<?php echo FormComponent::selectList('custom_page',$this->getFileList(),$this->getFileList(),$this->getCustomPage(),'custom_page'); ?>
					</div>
					<!-- Use Form -->
					<div class="form-group">
						<label for="custom_page">Use Form</label>
						<?php echo FormComponent::linkedModule('Form', 'use_form', $this->getUseForm()); ?>
					</div>
					<!-- Show in search results -->
					<div class="form-group">
						<label for="">This page will apear in the search results.</label>
						<?php echo FormComponent::radioButtonList('searchable',array(1, 2),array('Yes', 'No'),$this->getSearchable(),'searchable'); ?>
						<div class="error url_override"></div>
					</div>
					<? } ?>
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
				</div>
				<div class="col-sm-6">
					<div class="panel panel-custom">
						<div class="panel-heading">Banner</div>
						<div class="panel-body">
							<!-- Banner Image -->
							<?php $image = new Image(); $image->setDirectory('/files/'); echo $image->manageImage($this->getDbTable(),$this->getId(),1,$this->getImage(),$this->_moduleImageCanvasX,$this->_moduleImageCanvasY); ?>
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
			<?php if(strstr($this->getPermalinkFull(), 'products') && $this->getPermalink() != 'products'){ ?>
			<div class="row">
				<div class="col-sm-6">
					<!-- Image -->
					<?php $image = new Image(); echo $image->manageImage($this->getDbTable(),$this->getId(),2,$this->getImageTwo(),$this->_moduleImageProductCanvasX,$this->_moduleImageProductCanvasY,"img-responsive",'image_two','Primary Product Image'); ?>
				</div>
				<div class="col-sm-6">
					<!-- Image -->
					<?php $image = new Image(); echo $image->manageImage($this->getDbTable(),$this->getId(),3,$this->getImageThree(),$this->_moduleImageProductCanvasX,$this->_moduleImageProductCanvasY,"img-responsive",'image_three','Secondary Product Image'); ?>
				</div>
			</div>
			<?php } ?>
			

			
			
			
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
	
	public function buildParentPageSelect($selected_parent = '0'){		
		$select = "<select class='selectpicker' name='parent_id' data-none-selected-text=''>";
		
		// Get Parent Pages
		$parents = new HtmlPage();
		if($_SESSION['session_fullname'] == $GLOBALS['sstg_admin']){
			$superAdminVisiblityFilter = "";
		}else{
			$superAdminVisiblityFilter = "AND super_admin = '0'";
		}
		$parents = $parents->fetchAll("WHERE `parent_id` = '0' ".$superAdminVisiblityFilter, "ORDER BY `sort_order` ASC");
		
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
	
	public function buildOptions($selected_parent = '0'){
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
		
		ob_start();
		
		# If single == 1 - print one leaf from given Root Parent Id
		if($single){ 
			$leaf = new $this->_moduleClassName($root_parent_id);
			?>
			<li id="menuItem_<?php echo $leaf->getId(); ?>" class="lvl_<?php echo $current_level; ?>" >
				<?php echo $leaf->toHtml('default_list'); ?>
			</li>
		<?php
		}
		
		# Otherwise - print Hierarchy
		else{
			# If Current Level == 1 - start list
			if($current_level == 1){ ?>
			<ol class='sortable'>
			<?php
			}

			# If Root Parent Id does not equal 0 - print parent
			if($root_parent_id != 0){ 
				$root = new $this->_moduleClassName($root_parent_id);
				
				if($root->getParentId() == 0){
					$prev_level = $current_level;
					$current_level = 1;
				}
				
				?>
				<li id="menuItem_<?php echo $root->getId(); ?>" class="lvl_<?php echo $current_level; ?>" >
					<?php echo $root->toHtml('default_list'); ?>
			<?php
			}

			# Get branches of root
			$branches = new $this->_moduleClassName();
			if($_SESSION['session_fullname'] == $GLOBALS['sstg_admin']){
				$superAdminVisiblityFilter = "";
			}else{
				$superAdminVisiblityFilter = "AND super_admin = '0'";
			}
			$branches = $branches->fetchAll("WHERE `parent_id` = '".$root_parent_id."' ".$superAdminVisiblityFilter, "ORDER BY `sort_order`");

			# If there are branches - start a nested list
			if(count($branches)){

				# If the Root Parent Id does not equal 0 - start nested list
				if($root_parent_id != 0){ ?>
				<ol>
				<?php
				}

				# Iterate through branches
				foreach($branches as $branch){

					# If branch is a leaf - print
					if($branch->isLeaf()){ ?>
						<li id="menuItem_<?php echo $branch->getId(); ?>" class="lvl_<?php echo ($root_parent_id != 0)? $current_level+1 : $current_level; ?>" >
							<?php echo $branch->toHtml('default_list'); ?>
							<ol></ol>
						</li>		
					<?php
					}

					# Else - recurrsive call 
					else{
						# If there is a set number of levels specified
						if($max_levels != 'all'){
							
							# Subtract one level
							$max_levels = intval($max_levels); 
							$max_levels = $max_levels - 1;
							
							# If the level is greater than 0 - make recurrsive call
							if($max_levels > 1){
								echo $this->buildSortingStructure($max_levels, $branch->getId(), 1, $current_level+1);
							}
							else if($max_levels == 1){
								echo $this->buildSortingStructure($max_levels, $branch->getId(), $sorting_enabled, $current_level+1, 1);
							}
							
						}
						else{
							echo $this->buildSortingStructure($max_levels, $branch->getId(), 1, $current_level+1);
						}
					}

				}

				# If the Root Parent Id does not equal 0 - end nested list
				if($root_parent_id != 0){ ?>
				</ol>
				<?php
				}
			}

			# If Root Parent Id does not equal 0 - close first li tag
			if($root_parent_id != 0){ ?>
				</li>
			<?php
				if($root->getParentId() == 0){
					$current_level = $prev_level;
				}
			} 

			# If Current Level = 1 - end list
			if($current_level == 1){ ?>
			</ol>
			<?php
			}
			
			# If there were no branches - add No items message
			if(!count($branches) && $root_parent_id == 0){ ?>
			<div class="no_records"><i class="fa fa-times-circle"></i>No records available.</div>
			<?php
			}
		}
		
		return ob_get_clean();
		
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
