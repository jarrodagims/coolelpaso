<?php
class Promo extends Model{
	// Module Config
	public $_moduleName	= 'Promotions';
	public $_moduleDir = 'promos';
	public $_moduleTable = 'promo';
	public $_moduleTableCategories = 'promo_categories';
	public $_moduleClassName = 'Promo';
	public $_moduleCategoryClassName = 'PromoCategory';
	public $_moduleDescription = 'This section allows administrators to create, modify, and delete Promotions and Coupons';
	public $_moduleIcon = 'fa-tag';
	public $_moduleImageCanvasX = 243;
	public $_moduleImageCanvasY = 173;
	public $_moduleThumbCanvasX = 225;
	public $_moduleThumbCanvasY = 200;
	public $_moduleFeaturedLimit = 1;
	public $_moduleCategoryLevelDepth = 2;

	// Static Variables
	protected static $_addLabel = 'Add Promotion';
	protected static $_editLabel = 'Edit Promotion';
	protected static $_newsUrl = '/promotions';
	protected static $_fileTypes = array(
									'pdf'
									);

	// Inherited Variables
	protected $_filePath = 'files/promos/';
	protected $_dbTable	= 'promo';
	protected $_permalinkField = 'permalink';
	protected $_action = 'index';

	// Table Variables
	protected $_id;
	protected $_category;
	protected $_headline;
	protected $_savings = '';
	protected $_date;
	protected $_postDate;
	protected $_removeDate;
	protected $_lead = '';
	protected $_body = '';
	protected $_url = '';
	protected $_urlLabel = '';
	protected $_urlOverride = '0';
	protected $_image = '0';
	protected $_pdf = '0';
	protected $_pdfLabel = '';
	protected $_doNotShowOnNews = '0';
	protected $_permalink;
	protected $_featured = '0';
	protected $_sortOrder = 0;

	// Instance Variables
	protected $_categoryObject = NULL;
	protected $_messagesArray;
	protected $_hrefPrefix = '';
	protected $_requiredFields = array(
									'category',
									'headline',
									'date',
									'post_date'
									);
	protected $_saveFields = array(
								'category',
								'headline',
								'savings',
								'date',
								'post_date',
								'remove_date',
								'lead',
								'body',
								'url',
								'url_label',
								'url_override',
								'image',
								'pdf',
								'pdf_label',
								'do_not_show_on_news',
								'permalink',
								'featured',
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
	public function setHeadline($value){$this->_headline = (string) $value; return $this;}
	public function getHeadline(){return $this->_headline;}
	public function setSavings($value){$this->_savings = (string) $value; return $this;}
	public function getSavings(){return $this->_savings;}
	public function setDate($value){$this->_date = $this->formatDate((string) $value); return $this;}
	public function getDate(){return $this->_date;}
	public function setPostDate($value){$this->_postDate = $this->formatDateTime((string) $value); return $this;}
	public function getPostDate(){return $this->_postDate;}
	public function setRemoveDate($value){$this->_removeDate = $this->formatDateTime((string) $value); return $this;}
	public function getRemoveDate(){return $this->_removeDate;}
	public function setLead($value){$this->_lead = (string) $value; return $this;}
	public function getLead(){return $this->_lead;}
	public function setBody($value){$this->_body = (string) $value; return $this;}
	public function getBody(){return $this->_body;}
	public function setUrl($value){$this->_url = (string) $value; return $this;}
	public function getUrl(){return $this->_url;}
	public function setUrlLabel($value){$this->_urlLabel = (string) $value; return $this;}
	public function getUrlLabel(){return $this->_urlLabel;}
	public function setUrlOverride($value){$this->_urlOverride = (string) $value; return $this;}
	public function getUrlOverride(){return $this->_urlOverride;}
	public function setImage($value){$this->_image = (string) $value; return $this;}
	public function getImage(){return $this->_image;}
	public function setPdf($value){$this->_pdf = (string) $value; return $this;}
	public function getPdf(){return $this->_pdf;}
	public function setPdfLabel($value){$this->_pdfLabel = (string) $value; return $this;}
	public function getPdfLabel(){return $this->_pdfLabel;}
	public function setDoNotShowOnNews($value){$this->_doNotShowOnNews = (string) $value; return $this;}
	public function getDoNotShowOnNews(){return $this->_doNotShowOnNews;}
	public function setPermalink($value){$this->_permalink = (string) $value; return $this;}
	public function getPermalink(){return $this->_permalink;}
	public function setFeatured($value){$this->_featured = (string) $value; return $this;}
	public function getFeatured(){return $this->_featured;}
	public function setSortOrder($value){$this->_sortOrder = (int) $value; return $this;}
	public function getSortOrder(){return $this->_sortOrder;}

	// Helper setter methods for processing admin forms
	public function setYear($value){return $this->setDate(modifyDateString($this->getDate(),$value,'year'));}
	public function setMonth($value){return $this->setDate(modifyDateString($this->getDate(),$value,'month'));}
	public function setDay($value){return $this->setDate(modifyDateString($this->getDate(),$value,'day'));}
	public function setPostYear($value){return $this->setPostDate(modifyDateTimeString($this->getPostDate(),$value,'year'));}
	public function setPostMonth($value){return $this->setPostDate(modifyDateTimeString($this->getPostDate(),$value,'month'));}
	public function setPostDay($value){return $this->setPostDate(modifyDateTimeString($this->getPostDate(),$value,'day'));}
	public function setPostHour($value){return $this->setPostDate(modifyDateTimeString($this->getPostDate(),$value,'hour'));}
	public function setPostMinute($value){return $this->setPostDate(modifyDateTimeString($this->getPostDate(),$value,'minute'));}
	public function setPostAmPm($value){return $this->setPostDate(modifyDateTimeString($this->getPostDate(),$value,'am_pm'));}
	public function setRemoveYear($value){return $this->setRemoveDate(modifyDateTimeString($this->getRemoveDate(),$value,'year'));}
	public function setRemoveMonth($value){return $this->setRemoveDate(modifyDateTimeString($this->getRemoveDate(),$value,'month'));}
	public function setRemoveDay($value){return $this->setRemoveDate(modifyDateTimeString($this->getRemoveDate(),$value,'day'));}
	public function setRemoveHour($value){return $this->setRemoveDate(modifyDateTimeString($this->getRemoveDate(),$value,'hour'));}
	public function setRemoveMinute($value){return $this->setRemoveDate(modifyDateTimeString($this->getRemoveDate(),$value,'minute'));}
	public function setRemoveAmPm($value){return $this->setRemoveDate(modifyDateTimeString($this->getRemoveDate(),$value,'am_pm'));}

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
			 `locked` enum('0','1') NOT NULL DEFAULT '0',
			 PRIMARY KEY (`id`),
			 KEY `permalink` (`permalink`) USING BTREE
			) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8
			";
			$result = $this->create($query);

			$query = "
			CREATE TABLE `".$this->_dbTable."` (
			 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			 `category` int(10) unsigned NOT NULL DEFAULT '0',
			 `headline` varchar(255) NOT NULL DEFAULT '',
			 `date` date NOT NULL DEFAULT '0000-00-00',
			 `post_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			 `remove_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			 `lead` text NOT NULL,
			 `body` text NOT NULL,
			 `url` varchar(255) NOT NULL DEFAULT '',
			 `url_label` varchar(255) NOT NULL DEFAULT '',
			 `url_override` enum('0','1') NOT NULL DEFAULT '0',
			 `permalink` varchar(255) NOT NULL DEFAULT '',
			 `image` varchar(255) NOT NULL,
			 `pdf` enum('0','1') NOT NULL DEFAULT '0',
			 `pdf_label` varchar(255) NOT NULL DEFAULT '',
			 `do_not_show_on_news` enum('0','1') NOT NULL DEFAULT '0',
			 `featured` enum('0','1') NOT NULL DEFAULT '0',
			 `sort_order` int(10) unsigned NOT NULL DEFAULT '0',
			 PRIMARY KEY (`id`),
			 KEY `category` (`category`) USING BTREE,
			 KEY `post_date` (`post_date`) USING BTREE,
			 KEY `remove_date` (`remove_date`) USING BTREE,
			 KEY `permalink` (`permalink`) USING BTREE,
			 KEY `featured` (`featured`) USING BTREE
			) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8
			";
			$result = $this->create($query);
		}

		# Create Directory for files
		if (!file_exists($GLOBALS['path'].$this->getFilePath())) {
			@mkdir($GLOBALS['path'].$this->getFilePath());
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
		return '<a href="'.$this->buildHref().'" '.$external.'>'.process($this->getHeadline()).'</a>';
	}

	public function buildImage(){
		return '<img src="/'.$this->getFilePath().'/'.$this->getDbTable().$this->getId().'.'.$this->getImage().'" class="image" alt="'.process($this->getHeadline()).'" />';
	}
	public function buildImageSrc(){
		return '/'.$this->getFilePath().'/'.$this->getDbTable().$this->getId().'.'.$this->getImage();
	}

	public function buildImageThumbnail(){
		return '<img src="/'.$this->getFilePath().'/'.$this->getDbTable().$this->getId().'_t.'.$this->getImage().'" class="thumbnail_image" alt="'.process($this->getHeadline()).'" />';
	}

	public function buildPdfLink(){
		return '<a href="/'.$this->getFilePath().'/'.$this->getDbTable().$this->getId().'.pdf" class="external">Download PDF</a>';
	}

	public function buildUrlLink(){
		if(preg_match('#^http[s]?://#',$this->getUrl())){
			$external = 'class="external"';
		}else{
			$external = '';
		}
		return '<a href="'.$this->getUrl().'" '.$external.'>Read More</a>';
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
		return FormComponent::dropDownList($name,$values,$labels,$selected,$name);
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
					<span data-id="<?php echo $this->getId(); ?>" class="itemTitle"><strong><?php echo process($this->getHeadline()); ?></strong> - <em><?php echo $this->formatDate($this->getDate(), "F j, Y"); ?></em></span>
					<span data-id="<?php echo $this->getId(); ?>" class="action_menu">
						<a tabindex="0" class="pull-right" role="button" data-toggle="popover" data-trigger="focus" data-placement="bottom" data-content='<?php echo $this->toHtml('menu'); ?>'><i class="fa fa-gear"></i></a>
					</span>
					<?php echo $this->buildToggleButton('featured'); ?>
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
		$promos = $this->fetchAll("WHERE `featured` = '1' AND `post_date` <= NOW() AND (`remove_date` >= CURDATE() OR `remove_date` = '0000-00-00')","ORDER BY `date` DESC");
		if(sizeof($promos)){
		?>
					<div class="col-md-7 coupon-area">
						<?php if($promos[0]->getImage()): ?>
						<img src="<?php echo $promos[0]->buildImageSrc(); ?>" alt="Save!" data-aos="zoom-in-left" />
						<?php endif; ?>
						<p class="homepage-coupon-title" data-aos="zoom-in-right"><?php echo $promos[0]->getHeadline(); ?></p>
						<p class="homepage-coupon-info" data-aos="zoom-in-down"><?php echo strip_tags($promos[0]->getLead(), '<br>'); ?><span><?php echo $promos[0]->getSavings(); ?></span></p>
					</div>
        <?php }
		return ob_get_clean();
	}

	public function listingAction(){
		ob_start();
		?>
        <div class="news">
        	<span class="date"><?php echo printDate($this->getDate()); ?></span>&nbsp;-&nbsp;<span class="headline"><?php echo $this->buildLink(); ?></span>
            <?php if($this->getImage()): ?>
			<?php echo $this->buildImageThumbnail(); ?>
			<?php endif; ?>
			<?php echo $this->getLead(); ?>
        </div>
        <?php
		return ob_get_clean();
	}

	public function featuredAction(){
		ob_start();
		?>
        <div class="news_featured">
        	<span class="headline"><?php echo $this->buildLink(); ?></span><br />
            <span class="date"><?php echo printDate($this->getDate()); ?></span><br />
            <?php echo generateBlurb(strip_tags($this->getLead()),250); ?>&nbsp;<a href="<?php echo $this->buildHref(); ?>">&hellip;read more</a>
        </div>
        <?php
		return ob_get_clean();
	}

	public function adminNewRecord($category){
		$this->setCategory($category)
		->setDate(date('Y')."-".date('m')."-".date('d'))
		->setPostDate(date('Y')."-".date('m')."-".date('d').' 00:00:00')
		->setRemoveDate('0000-00-00 00:00:00');
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
					<!-- Headline -->
					<div class="form-group">
						<label for="headline" class="required">Title</label>
						<input type="text" name="headline" class="form-control required" value="<?php echo process($this->getHeadline()); ?>" placeholder="Headline" />
						<div class="error headline"></div>
					</div>
					<!-- Category -->
					<div class="form-group">
						<label for="category" class="required">Category</label><br>
						<?php echo $this->listCategories($this->getCategory()); ?>
						<div class="error category"></div>
					</div>
					<div class="form-group">
						<label for="savings">Discount / Savings</label>
						<input type="text" name="savings" class="form-control" value="<?php echo process($this->getSavings()); ?>" placeholder="$600" />
						<div class="error savings"></div>
					</div>
					<!-- Do Not Show on News -->
					<!--<div class="form-group">
						<label for="">Do Not Show On News</label>
						<?php
						$label = '<div class="instruction">Checking this box will remove this item from showing on the News Page. It can however, still be featured.</div>';
						echo FormComponent::checkBox('do_not_show_on_news','1',$this->getDoNotShowOnNews(),'do_not_show_on_news',$label); ?>
						<div class="error url_override"></div>
					</div>-->
				</div>
				<div class="col-sm-6">
					<div class="panel panel-custom">
						<div class="panel-heading"><i class="fa fa-calendar"></i>Dates</div>
						<div class="panel-body">
							<!-- Date -->
							<div class="form-group">
								<label for="date" class="required">Date</label>
								<?php echo FormComponent::dateTimePicker(array ('format'=>'MM/DD/YYYY','defaultDate'=>$this->getDate())); ?>
								<div class="error date"></div>
							</div>
							<!-- Post Date/Time -->
							<div class="form-group">
								<label for="post_date" class="required">Start Date/Time</label>
								<?php echo FormComponent::dateTimePicker(array ('format'=>'MM/DD/YYYY LT', 'name'=>'post_date', 'defaultDate'=>$this->getPostDate())); ?>
								<div class="instruction">
									<ul>
										<li>Will be posted beginning on the day and time selected</li>
									</ul>
								</div>
							</div>
							<!-- Remove Date/Time -->
							<div class="form-group">
								<label for="remove_date" class="">Expiration Date/Time</label>
								<?php echo FormComponent::dateTimePicker(array ('format'=>'MM/DD/YYYY LT', 'name'=>'remove_date', 'defaultDate'=>$this->getRemoveDate())); ?>
								<div class="instruction">
									<ul>
										<li>Will be removed after the day and time selected</li>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12">
					<!-- Brief Description -->
					<div class="form-group">
						<label for="lead">Brief Description</label>
						<div class="error lead"></div>
						<?php echo FormComponent::textareaSmall("lead",process($this->getLead()),"lead", "form-control required"); ?>
					</div>
				</div>
				<!--<div class="col-sm-12">
					<!-- Complete Description -->
					<!--<div class="form-group">
						<label for="body" class="required">Complete Description</label>
						<div class="error body"></div>
						<?php echo FormComponent::textareaFull("body",process($this->getBody()),"body", "form-control required"); ?>
					</div>
				</div>-->
				<div class="col-sm-6">
					<div class="panel panel-info">
						<div class="panel-heading"><i class="fa fa-globe"></i>URL</div>
						<div class="panel-body">
							<div class="row">
								<div class="col-sm-12">
									<!-- URL -->
									<div class="form-group">
										<label for="url">Link to Product or Service</label>
										<input type="text" name="url" class="form-control" value="<?php echo process($this->getUrl()); ?>" placeholder="URL" />
										<div class="instruction">
											<ul>
												<li>Examples: "/privacy-policy" or "http://www.stantonstreet.com"</li>
											</ul>
										</div>
										<div class="error url"></div>
									</div>
								</div>
								<!--<div class="col-sm-6">
									<!-- URL Override -->
									<!--<div class="form-group">
										<label for="">URL Override</label>
										<?php
										$label = '<div class="instruction">Checking this box will ensure that the News Item links to the URL rather than its default detail page.</div>';
										echo FormComponent::checkBox('url_override','1',$this->getUrlOverride(),'url_override',$label); ?>
										<div class="error url_override"></div>
									</div>
								</div>-->
							</div>
						</div>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="panel panel-info">
						<div class="panel-heading"><i class="fa fa-paperclip"></i>Promotion Image</div>
						<div class="panel-body">
							<div class="row">
								<div class="col-sm-12">
									<!-- Image -->
									<?php $image = new Image();
									$image->setDirectory(DIRECTORY_SEPARATOR.$this->getFilePath());
									echo $image->manageImage($this->getDbTable(),$this->getId(),1,$this->getImage(),$this->_moduleImageCanvasX,$this->_moduleImageCanvasY); ?>
								</div>
								<!--<div class="col-sm-6">
									<!-- PDF Document -->
									<!--<?php $file = new File(); echo $file->manageFile(get_class($this),$this->getId(),2,$this->getPdf(),array('pdf'),'pdf','PDF Document'); ?>
								</div>-->
							</div>
						</div>
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
