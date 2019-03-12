<?php
class Event extends Model{
	// Module Config
	public $_moduleName	= 'Events';
	public $_moduleDir = 'events';
	public $_moduleTable = 'events';
	public $_moduleTableCategories = 'event_categories';
	public $_moduleClassName = 'Event';
	public $_moduleCategoryClassName = 'EventCategory';
	public $_moduleDescription = 'This section allows administrators to create, modify, and delete Events';
	public $_moduleIcon = 'fa-calendar';
	public $_moduleImageCanvasX = 830;
	public $_moduleImageCanvasY = 738;
	public $_moduleThumbCanvasX = 225;
	public $_moduleThumbCanvasY = 200;
	public $_moduleFeaturedLimit = 3;
	public $_moduleCategoryLevelDepth = 2;

	// Static Variables
	protected static $_addLabel = 'Add Event';
	protected static $_editLabel = 'Edit Event';
	protected static $_eventUrl = '/events';
	protected static $_fileTypes = array(
									'pdf'
									);

	// Inherited Variables
	protected $_filePath = 'files/events/';
	protected $_dbTable	= 'events';
	protected $_permalinkField = 'permalink';
	protected $_action = 'index';

	// Table Variables
	protected $_id;
	protected $_category;
	protected $_name = '';
	protected $_startDate = '0000-00-00';
	protected $_startTime = '00:00:00';
	protected $_endDate = '0000-00-00';
	protected $_endTime = '00:00:00';
	protected $_allDay = '0';
	protected $_description = '';
	protected $_image = '0';
	protected $_pdf = '0';
	protected $_pdfLabel = '';
	protected $_url = '';
	protected $_urlLabel = '';
	protected $_permalink = '';
	protected $_active = '0';
	protected $_repeating = '0';
	protected $_repeatType = '';
	protected $_repeatEvery = 1;
	protected $_repeatSunday = '0';
	protected $_repeatMonday = '0';
	protected $_repeatTuesday = '0';
	protected $_repeatWednesday = '0';
	protected $_repeatThursday = '0';
	protected $_repeatFriday = '0';
	protected $_repeatSaturday = '0';
	protected $_repeatBy = 1;
	protected $_repeatNum = 0;
	protected $_repeatEnd = '0000-00-00';
	protected $_summary = '';

	// Instance Variables
	protected $_categoryObject = NULL;
	protected $_messagesArray;
	protected $_hrefPrefix = '';
	protected $_requiredFields = array(
									'category',
									'name',
									'start_date',
									'end_date'
								);
	protected $_saveFields = array(
									'id',
									'category',
									'name',
									'start_date',
									'start_time',
									'end_date',
									'end_time',
									'all_day',
									'description',
									'image',
									'pdf',
									'pdf_label',
									'url',
									'url_label',
									'permalink',
									'active',
									'repeating',
									'repeat_type',
									'repeat_every',
									'repeat_sunday',
									'repeat_monday',
									'repeat_tuesday',
									'repeat_wednesday',
									'repeat_thursday',
									'repeat_friday',
									'repeat_saturday',
									'repeat_by',
									'repeat_num',
									'repeat_end',
									'summary'
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
	public static function getEventUrl(){return self::$_eventUrl;}

	// Accessor Methods
	public function setId($value){$this->_id = $value; return $this;}
	public function getId(){return $this->_id;}
	public function setCategory($value){$this->_category = $value; return $this;}
	public function getCategory(){return $this->_category;}
	public function setName($value){$this->_name = $value; return $this;}
	public function getName(){return $this->_name;}
	public function setStartDate($value){$this->_startDate = $this->formatDate((string) $value); return $this;}
	public function getStartDate(){return $this->_startDate;}
	public function setStartTime($value){$this->_startTime = $this->formatDate((string) $value, "H:i:s"); return $this;}
	public function getStartTime(){return $this->_startTime;}
	public function setEndDate($value){$this->_endDate = $this->formatDate((string) $value); return $this;}
	public function getEndDate(){return $this->_endDate;}
	public function setEndTime($value){$this->_endTime = $this->formatDate((string) $value, "H:i:s"); return $this;}
	public function getEndTime(){return $this->_endTime;}
	public function setAllDay($value){$this->_allDay = $value; return $this;}
	public function getAllDay(){return $this->_allDay;}
	public function setDescription($value){$this->_description = $value; return $this;}
	public function getDescription(){return $this->_description;}
	public function setImage($value){$this->_image = $value; return $this;}
	public function getImage(){return $this->_image;}
	public function setPdf($value){$this->_pdf = $value; return $this;}
	public function getPdf(){return $this->_pdf;}
	public function setPdfLabel($value){$this->_pdfLabel = $value; return $this;}
	public function getPdfLabel(){return $this->_pdfLabel;}
	public function setUrl($value){$this->_url = $value; return $this;}
	public function getUrl(){return $this->_url;}
	public function setUrlLabel($value){$this->_urlLabel = $value; return $this;}
	public function getUrlLabel(){return $this->_urlLabel;}
	public function setPermalink($value){$this->_permalink = $value; return $this;}
	public function getPermalink(){return $this->_permalink;}
	public function setActive($value){$this->_active = $value; return $this;}
	public function getActive(){return $this->_active;}
	public function setRepeating($value){$this->_repeating = $value; return $this;}
	public function getRepeating(){return $this->_repeating;}
	public function setRepeatType($value){$this->_repeatType = $value; return $this;}
	public function getRepeatType(){return $this->_repeatType;}
	public function setRepeatEvery($value){$this->_repeatEvery = $value; return $this;}
	public function getRepeatEvery(){return $this->_repeatEvery;}
	public function setRepeatSunday($value){$this->_repeatSunday = $value; return $this;}
	public function getRepeatSunday(){return $this->_repeatSunday;}
	public function setRepeatMonday($value){$this->_repeatMonday = $value; return $this;}
	public function getRepeatMonday(){return $this->_repeatMonday;}
	public function setRepeatTuesday($value){$this->_repeatTuesday = $value; return $this;}
	public function getRepeatTuesday(){return $this->_repeatTuesday;}
	public function setRepeatWednesday($value){$this->_repeatWednesday = $value; return $this;}
	public function getRepeatWednesday(){return $this->_repeatWednesday;}
	public function setRepeatThursday($value){$this->_repeatThursday = $value; return $this;}
	public function getRepeatThursday(){return $this->_repeatThursday;}
	public function setRepeatFriday($value){$this->_repeatFriday = $value; return $this;}
	public function getRepeatFriday(){return $this->_repeatFriday;}
	public function setRepeatSaturday($value){$this->_repeatSaturday = $value; return $this;}
	public function getRepeatSaturday(){return $this->_repeatSaturday;}
	public function setRepeatBy($value){$this->_repeatBy = $value; return $this;}
	public function getRepeatBy(){return $this->_repeatBy;}
	public function setRepeatNum($value){$this->_repeatNum = $value; return $this;}
	public function getRepeatNum(){return $this->_repeatNum;}
	public function setRepeatEnd($value){$this->_repeatEnd = $value; return $this;}
	public function getRepeatEnd(){return $this->_repeatEnd;}
	public function setSummary($value){$this->_summary = $value; return $this;}
	public function getSummary(){return $this->_summary;}

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
			 `color` varchar(255) NOT NULL,
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
			 `name` varchar(255) NOT NULL DEFAULT '',
			 `start_date` date NOT NULL DEFAULT '0000-00-00',
			 `start_time` time NOT NULL DEFAULT '00:00:00',
			 `end_date` date NOT NULL DEFAULT '0000-00-00',
			 `end_time` time NOT NULL DEFAULT '00:00:00',
			 `all_day` enum('0','1') NOT NULL DEFAULT '0',
			 `description` text NOT NULL,
			 `image` varchar(255) NOT NULL,
			 `pdf` enum('0','1') NOT NULL DEFAULT '0',
			 `pdf_label` varchar(255) NOT NULL DEFAULT '',
			 `url` varchar(255) NOT NULL DEFAULT '',
			 `url_label` varchar(255) NOT NULL DEFAULT '',
			 `permalink` varchar(255) NOT NULL DEFAULT '',
			 `active` enum('0','1') NOT NULL DEFAULT '0',
			 `repeating` enum('0','1') NOT NULL DEFAULT '0',
			 `repeat_type` enum('daily','weekly','monthly','yearly') DEFAULT NULL,
			 `repeat_every` int(10) unsigned NOT NULL,
			 `repeat_sunday` enum('0','1') NOT NULL DEFAULT '0',
			 `repeat_monday` enum('0','1') NOT NULL DEFAULT '0',
			 `repeat_tuesday` enum('0','1') NOT NULL DEFAULT '0',
			 `repeat_wednesday` enum('0','1') NOT NULL DEFAULT '0',
			 `repeat_thursday` enum('0','1') NOT NULL DEFAULT '0',
			 `repeat_friday` enum('0','1') NOT NULL DEFAULT '0',
			 `repeat_saturday` enum('0','1') NOT NULL DEFAULT '0',
			 `repeat_by` enum('day_of_month','day_of_week') NOT NULL,
			 `repeat_num` int(11) NOT NULL DEFAULT '0',
			 `repeat_end` date NOT NULL DEFAULT '0000-00-00',
			 `summary` text NOT NULL,
			 PRIMARY KEY (`id`),
			 KEY `category` (`category`) USING BTREE,
			 KEY `permalink` (`permalink`) USING BTREE
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
		return $this->_eventUrl.'/'.$this->getPermalink();
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

	public function buildImageThumbnail(){
		return '<img src="/'.$this->getFilePath().'/'.$this->getDbTable().$this->getId().'_t.'.$this->getImage().'" class="thumbnail_image" alt="'.process($this->getName()).'" />';
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

		if($_POST['start_date'] == ''){
			$this->addMessage('start_date',array('type'=>'failure','text'=>'Required'));
		}

		if($_POST['end_date'] == ''){
			$this->addMessage('end_date',array('type'=>'failure','text'=>'Required'));
		}

		if($this->getRepeating()){

			// Check Repeat Every is at least 1
			if($this->getRepeatEvery() < 1){
				$this->addMessage('repeat_every',array('type'=>'failure','text'=>'Must be at least 1.'));
			}

			// Check Occurrances is at least 1
			if($_POST['repeat_end_type'][0] == 'never'){
				$this->setRepeatEnd('0000-00-00');
				$this->setRepeatNum('0');
			}
			else if($_POST['repeat_end_type'][0] == 'number_occurrences'){
				if($_POST['repeat_num'] < 1){
					$this->addMessage('repeat_num', array('type'=>'failure', 'text'=>'Must be at least 1.'));
				}

				$this->setRepeatEnd($_POST['repeat_num_date']);
			}
			else{
				$this->setRepeatEnd($this->formatDate($_POST['repeat_end']));
			}

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
					<span data-id="<?php echo $this->getId(); ?>" class="itemTitle"><strong><?php echo process($this->getName()); ?></strong> - <em><?php echo $this->formatDate($this->getStartDate(), "F j, Y"); ?></em></span>
					<span data-id="<?php echo $this->getId(); ?>" class="action_menu">
						<a tabindex="0" class="pull-right" role="button" data-toggle="popover" data-trigger="focus" data-placement="bottom" data-content='<?php echo $this->toHtml('menu'); ?>'><i class="fa fa-gear"></i></a>
					</span>
					<?php echo $this->buildToggleButton('active'); ?>
				</div>
			</div>
        <?php
		return ob_get_clean();
	}

	public function calendarAction()
	{
		ob_start();
		?>
            <a class="event category_<?php echo $this->getCategory(); ?>" href="<?php echo $this->buildHref() ?>" title="<?php echo $this->getName(); ?>">
				<?php echo $this->getName(); ?>
            </a>
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
		?>
        <div class="news">
        	<?php
            /*<h2><?php echo process($this->getName()); ?></h2>
			*/
			?>
			<?php if($this->getImage()): ?>
			<?php echo $this->buildImage(); ?>
			<?php endif; ?>
            <span class="date"><?php echo printDate($this->getStartDate()); ?></span>
            <?php if(strlen(trim($this->getUrl()))): ?>
			<br /><span class="url"><?php echo $this->buildUrlLink(); ?></span>
            <?php endif; ?>
            <?php if($this->getPdf()): ?>
			<br /><span class="pdf"><?php echo $this->buildPdfLink(); ?></span>
            <?php endif; ?>
			<?php echo $this->getBody(); ?>
        </div>
        <?php
		return ob_get_clean();
	}

	public function listingAction(){
		ob_start();
		?>
        <div class="news">
        	<span class="date"><?php echo printDate($this->getStartDate()); ?></span>&nbsp;-&nbsp;<span class="headline"><?php echo $this->buildLink(); ?></span>
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
            <span class="date"><?php echo printDate($this->getStartDate()); ?></span><br />
            <?php echo generateBlurb(strip_tags($this->getLead()),250); ?>&nbsp;<a href="<?php echo $this->buildHref(); ?>">&hellip;read more</a>
        </div>
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
					<!-- Event Name -->
					<div class="form-group">
						<label for="name" class="required">Name</label>
						<input type="text" name="name" class="form-control required" value="<?php echo process($this->getName()); ?>" placeholder="Event Name" />
						<div class="error name"></div>
					</div>
				</div>
				<div class="col-sm-6">
					<!-- Category -->
					<div class="form-group">
						<label for="category" class="required">Category</label><br>
						<?php echo $this->listCategories($this->getCategory()); ?>
						<div class="error category"></div>
					</div>
				</div>
				<div class="col-sm-12">
					<!-- Description -->
					<div class="form-group">
						<label for="description">Description</label>
						<div class="error description"></div>
						<?php echo FormComponent::textareaSmall("description",process($this->getDescription()),"description", "form-control required"); ?>
					</div>
				</div>
				<div class="col-sm-12">
					<div class="panel panel-custom">
						<div class="panel-heading"><i class="fa fa-calendar"></i>When is this event?</div>
						<div class="panel-body">
							<div id="event-date" class="row">
								<div class="<?php echo ($this->getAllDay())? 'col-sm-6' : 'col-sm-3'; ?>">
									<!-- Start Date -->
									<div class="form-group">
										<label for="start_date" class="required">Start Date</label>
										<?php echo FormComponent::dateTimePicker(array ('format'=>'MM/DD/YYYY','name'=>'start_date','id'=>'1','defaultDate'=>(($this->getStartDate() == '0000-00-00')? date_format(date_create(), 'm/d/Y') : $this->getStartDate()) )); ?>
										<div class="error start_date"></div>
									</div>
								</div>
								<div class="col-sm-3 <?php echo ($this->getAllDay())? 'hidden' : 'show'; ?>">
									<!-- Start Time -->
									<div class="form-group">
										<label for="start_time" class="">Start Time</label>
										<?php echo FormComponent::dateTimePicker(array ('format'=>'LT','name'=>'start_time','defaultDate'=>$this->getStartTime())); ?>
										<div class="error start_time"></div>
									</div>
								</div>
								<div class="<?php echo ($this->getAllDay())? 'col-sm-6' : 'col-sm-3'; ?>">
									<!-- End Date -->
									<div class="form-group">
										<label for="end_date" class="required">End Date</label>
										<?php echo FormComponent::dateTimePicker(array ('format'=>'MM/DD/YYYY','name'=>'end_date','id'=>'2','defaultDate'=>(($this->getEndDate() == '0000-00-00')? $this->getStartDate() : $this->getEndDate()))); ?>
										<div class="error end_date"></div>
									</div>
								</div>
								<div class="col-sm-3 <?php echo ($this->getAllDay())? 'hidden' : 'show'; ?>">
									<!-- End Time -->
									<div class="form-group">
										<label for="end_time" class="">End Time</label>
										<?php echo FormComponent::dateTimePicker(array ('format'=>'LT','name'=>'end_time','defaultDate'=>$this->getEndTime())); ?>
										<div class="error end_time"></div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-3">
									<!-- All Day -->
									<div class="form-group">
										<?php echo FormComponent::checkBox('all_day','1',$this->getAllDay(),'all_day','All Day Event?'); ?>
									</div>
								</div>
								<div class="col-sm-3">
									<!-- Repeat -->
									<div class="form-group">
										<?php echo FormComponent::checkBox('repeating','1',$this->getRepeating(),'repeating','Reoccurring Event?'); ?>
									</div>
								</div>
							</div>
							<div class="col-sm-12">
								<div class="well <?php echo ($this->getRepeating())? '' : 'hidden'; ?>">
									<div class="row">
										<div class="col-sm-3" style="text-align: right;">
											<label for="repeat_type">Repeat: </label>
										</div>
										<div class="col-sm-9">
											<?php
												$name = 'repeat_type';
												$values = ['daily', 'weekly', 'monthly', 'yearly'];
												$labels = ['Daily', 'Weekly', 'Monthly', 'Yearly'];
												$selected = ($this->getRepeatType())? $this->getRepeatType() : 'daily';
												$id = 'repeat_type';
												$class = '';
												echo FormComponent::selectList($name,$values,$labels,$selected,$id,$class);
											?>
										</div>
									</div>
									<div class="row" style="padding-top: 10px;">
										<div class="col-sm-3" style="text-align: right;">
											<label for="repeat_every">Repeat Every: </label>
										</div>
										<div class="col-sm-2">
											<input type="number" name="repeat_every" id="repeat_every" class="form-control" value="<?php echo process($this->getRepeatEvery()); ?>" placeholder="" />
											<div class="error repeat_every"></div>
										</div>
									</div>
									<div id="repeat_choices" class="row <?php echo ($this->getRepeatType() == 'weekly' || $this->getRepeatType() == 'monthly')? '' : 'hidden'; ?>" style="padding-top: 10px;">
										<div class="col-sm-3" style="text-align: right;">
											<label for="repeat_on">Repeat On: </label>
										</div>
										<div class="col-sm-9">
											<div id="repeat_on_weekly" class="inline-checkboxes">
												<?php echo FormComponent::checkBox('repeat_sunday','1',$this->getRepeatSunday(),'repeat_sunday','Sunday'); ?>
												<?php echo FormComponent::checkBox('repeat_monday','1',$this->getRepeatMonday(),'repeat_monday','Monday'); ?>
												<?php echo FormComponent::checkBox('repeat_tuesday','1',$this->getRepeatTuesday(),'repeat_tuesday','Tuesday'); ?>
												<?php echo FormComponent::checkBox('repeat_wednesday','1',$this->getRepeatWednesday(),'repeat_wednesday','Wednesday'); ?>
												<?php echo FormComponent::checkBox('repeat_thursday','1',$this->getRepeatThursday(),'repeat_thursday','Thursday'); ?>
												<?php echo FormComponent::checkBox('repeat_friday','1',$this->getRepeatFriday(),'repeat_friday','Friday'); ?>
												<?php echo FormComponent::checkBox('repeat_saturday','1',$this->getRepeatSaturday(),'repeat_saturday','Saturday'); ?>
											</div>
											<div id="repeat_on_monthly" class="inline-checkboxes">
												<?php
													$name = 'repeat_by';
													$values = ['day_of_month', 'day_of_week'];
													$labels = ['Day of the Month', 'Day of the Week'];
													$selected = $this->getRepeatBy();
													$id = 'repeat_by';
													echo FormComponent::radioButtonList($name,$values,$labels,$selected);
												?>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-sm-3" style="text-align: right; padding-top: 10px;">
											<label for="repeat_end">Ends: </label>
										</div>
										<div class="col-sm-4" style="padding-top: 10px;">
											<div class="radioButton-list" id="repeat_end_type">
												<div class="radio radio-custom">
													<input type="radio" class="styled" id="" name="repeat_end_type[]" value="never" <?php echo ($this->getRepeatNum() == 0 && $this->getRepeatEnd() == "0000-00-00")? 'checked="checked"' : ''; ?>>
													<label for="repeat_end_type[]">Never</label>
												</div>
												<div class="radio radio-custom">
													<input type="radio" class="styled" id="" name="repeat_end_type[]" value="number_occurrences" <?php echo ($this->getRepeatNum() != 0 && $this->getRepeatEnd() != "0000-00-00")? 'checked="checked"' : ''; ?>>
													<label for="repeat_end_type[]"><span>After </span><input type="number" name="repeat_num"  id="repeat_num" value="<?php echo ($this->getRepeatNum())? $this->getRepeatNum() : ''; ?>" <?php echo ($this->getRepeatNum() != 0 && $this->getRepeatEnd() != "0000-00-00")? '' : 'disabled'; ?>><span> Occurrences</span></label>
													<input type="hidden" name="repeat_num_date" value="<?php echo $this->getRepeatEnd(); ?>" <?php echo ($this->getRepeatNum() == 0 && $this->getRepeatEnd() != "0000-00-00")? 'disabled' : ''; ?> />
													<span class="error repeat_num"></span>
												</div>
												<div class="radio radio-custom">
													<input type="radio" class="styled" id="" name="repeat_end_type[]" value="date" <?php echo ($this->getRepeatNum() == 0 && $this->getRepeatEnd() != "0000-00-00")? 'checked="checked"' : ''; ?>>
													<label for="repeat_end_type[]">On</label>
													<?php echo FormComponent::dateTimePicker(array ('format'=>'MM/DD/YYYY','name'=>'repeat_end','id'=>'3','defaultDate'=>$this->getRepeatEnd(), 'disabled'=>(($this->getRepeatNum() == 0 && $this->getRepeatEnd() != "0000-00-00")? 0 : 1))); ?>
												</div>
											</div>
											<div class="error repeat_end"></div>
										</div>
									</div>
									<div class="row">
										<div class="col-sm-3" style="text-align: right; padding-top: 10px;">
											<label for="summary">Summary: </label>
										</div>
										<div class="col-sm-9" style="padding-top: 10px;">
											<div id="summary"><?php echo $this->getSummary(); ?></div>
											<input type="hidden" name="summary" value="<?php echo $this->getSummary(); ?>" />
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-sm-12">
					<div class="panel panel-custom">
						<div class="panel-heading"><i class="fa fa-globe"></i>URL</div>
						<div class="panel-body">
							<div class="row">
								<div class="col-sm-6">
									<!-- URL -->
									<div class="form-group">
										<label for="url">URL</label>
										<input type="text" name="url" class="form-control" value="<?php echo process($this->getUrl()); ?>" placeholder="URL" />
										<div class="instruction">
											<ul>
												<li>Examples: "/privacy-policy" or "http://www.stantonstreet.com"</li>
											</ul>
										</div>
										<div class="error url"></div>
									</div>
								</div>
								<div class="col-sm-6">
									<!-- URL Label -->
									<div class="form-group">
										<label for="url">URL Label</label>
										<input type="text" name="url_label" class="form-control" value="<?php echo process($this->getUrlLabel()); ?>" placeholder="URL Label" />
										<div class="error url_label"></div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-sm-12">
					<div class="panel panel-custom">
						<div class="panel-heading"><i class="fa fa-paperclip"></i>Attachments</div>
						<div class="panel-body">
							<div class="row">
								<div class="col-sm-6">
									<!-- Image -->
									<?php $image = new Image();
									$image->setDirectory(DIRECTORY_SEPARATOR.$this->getFilePath());
									echo $image->manageImage($this->getDbTable(),$this->getId(),1,$this->getImage(),$this->_moduleImageCanvasX,$this->_moduleImageCanvasY); ?>
								</div>
								<div class="col-sm-6">
									<!-- PDF Document -->
									<?php $file = new File();
									$file->setDirectory(DIRECTORY_SEPARATOR.$this->getFilePath());
									echo $file->manageFile(get_class($this),$this->getId(),2,$this->getPdf(),array('pdf'),'pdf','PDF Document'); ?>
									<!-- PDF Label -->
									<div class="form-group">
										<label for="pdf_label">PDF Label</label>
										<input type="text" name="pdf_label" class="form-control" value="<?php echo process($this->getPdfLabel()); ?>" placeholder="PDF Label" />
										<div class="error pdf_label"></div>
									</div>
								</div>
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
		<script type="text/javascript">
			$(function(){
				initModalForm();
			});
		</script>
		<?php

		$form = ob_get_clean();

		$modal = new Module();
		return $modal->buildInnerModal($actionLabel, $form);
	}

	public function buildCss(){
		ob_start(); ?>
		<style>
		.inline-checkboxes .radio,
		.inline-checkboxes .checkbox{display: inline; margin: 0 10px 0 0;}
		.inline-checkboxes .radio label,
		.inline-checkboxes .checkbox label{padding: 0 !important;}
		label input{width: 50px; text-align: center;}
		</style>
		<?php
		return ob_get_clean();
	}

	public function buildAdminListJavascript(){
		ob_start();?>
		<script>

			function initModalForm(){
				$('#datetimepicker_1').datetimepicker();
				$('#datetimepicker_2').datetimepicker({
				   useCurrent: false
				});
				$("#datetimepicker_1").on("dp.change", function (e) {
					// Generate Repeat Summary
	 			   generateRepeatSummary();
				   $('#datetimepicker_2').data("DateTimePicker").minDate(e.date);
				});
				$("#datetimepicker_2").on("dp.change", function (e) {
					// Generate Repeat Summary
	 			   generateRepeatSummary();
				   $('#datetimepicker_1').data("DateTimePicker").maxDate(e.date);
				});

				// Ends On
				$("#datetimepicker_3").on("dp.change", function (e) {
					// Generate Repeat Summary
	 			   generateRepeatSummary();
				});
			}

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

			// When is this event? - Validation
			// All Day checkbox
			$(document).on('change', '#all_day', function(){
				if($(this).is(":checked")) {
 				   $('#event-date > .col-sm-3').removeClass('col-sm-3').addClass('col-sm-6');
 				   $('#event-date > .show').removeClass('show').addClass('hidden');
			   }
			   else{
				   $('#event-date > .col-sm-6').removeClass('col-sm-6').addClass('col-sm-3');
				   $('#event-date > .hidden').removeClass('hidden').addClass('show');
			   }
			});
			// Reoccurring Event checkbox
			$(document).on('change', '#repeating', function(){
				if($(this).is(":checked")) {
					$('.well').removeClass('hidden').slideDown();

					// Generate Repeat Summary
					generateRepeatSummary();
				}
				else{
					$('.well').slideUp();
				}
			});
			// Repeat type
			$(document).on('changed.bs.select', '#repeat_type', function (event, clickedIndex, newValue, oldValue) {
				var selected = $(this).find('option').eq(clickedIndex).val();

				if(selected == 'daily'){
					$('#repeat_choices').removeClass('hidden').slideUp();
				}
				else if(selected == 'weekly'){
					$('#repeat_on_weekly').show();
					$('#repeat_on_monthly').hide();
					$('#repeat_choices').removeClass('hidden').slideDown();

					// Pre-Select day of the week
					var start = $('#datetimepicker_1').data("DateTimePicker").date();
					if(start != null){
						$('input[name="repeat_'+start.format('dddd').toLowerCase()+'"]').prop('checked', true);
					}
				}
				else if(selected == 'monthly'){
					$('#repeat_on_weekly').hide();
					$('#repeat_on_monthly').show();
					$('#repeat_choices').removeClass('hidden').slideDown();

					// Pre-Select Repeat By
					$('input[value="day_of_month"]').prop('checked', true);

				}
				else if(selected == 'yearly'){
					$('#repeat_choices').removeClass('hidden').slideUp();
				}

				// Generate Repeat Summary
				generateRepeatSummary();
			});
			// Repeat Every, Repeat By, Repeat On
			$(document).on('change', 'input[name="repeat_every"], input[name="repeat_by[]"], input[type="checkbox"][name*="repeat_"] ', function(){
				// Generate Repeat Summary
				generateRepeatSummary();
			});
			// Number of Occurrences
			$(document).on('change', 'input[name="repeat_num"] ', function(){
				// Generate Repeat Summary
				generateRepeatSummary();
			});
		 	// Reoccurring Ends radio button
			$(document).on('change', 'input[name="repeat_end_type[]"]', function(){
				var value = $(this).val();

				if(value == 'never'){
					// disable inputs
					$('#repeat_num').prop("disabled", true);
					$('input[name="repeat_num_date"]').prop("disabled", true);
					$('input[name="repeat_end"]').prop("disabled", true);
				}
				else if(value == 'number_occurrences'){
					$('#repeat_num').prop("disabled", false);
					$('input[name="repeat_num_date"]').prop("disabled", false);
					$('input[name="repeat_end"]').prop("disabled", true);

					// Pre-Select After Number of Occurrences
					if($('#repeat_num').val() == ''){
						$('#repeat_num').val('2');
					}
				}
				else if(value == 'date'){
					// disable inputs
					$('#repeat_num').prop("disabled", true);
					$('input[name="repeat_num_date"]').prop("disabled", true);
					$('input[name="repeat_end"]').prop("disabled", false);

					// Pre-Select Date
					if($('#datetimepicker_3').data("DateTimePicker").date() == null){
						var moment = $('#datetimepicker_1').data("DateTimePicker").date().add(1, 'M').format('MM/D/YYYY');
						$('#datetimepicker_3').data("DateTimePicker").minDate($('#datetimepicker_1').data("DateTimePicker").date());
						$('#datetimepicker_3').data("DateTimePicker").date(moment);
					}
				}

				// Generate Repeat Summary
				generateRepeatSummary();
			});

			function calculateEndDate(){
				if($('#repeat_end_type').find(':checked').val() == 'number_occurrences'){
					if(Number($('#repeat_num').val()) > 1){
						// Calculate End Date when Number of Occurrences is defined
						var repeatStart = $('#datetimepicker_1').data("DateTimePicker").date();
						var repeatType = $('#repeat_type').val();
						var repeatEvery = $('#repeat_every').val();
						var repeatDays = [
							{"day": "0", "value" : $('#repeat_sunday').prop('checked')},
							{"day": "1", "value" : $('#repeat_monday').prop('checked')},
							{"day": "2", "value" : $('#repeat_tuesday').prop('checked')},
							{"day": "3", "value" : $('#repeat_wednesday').prop('checked')},
							{"day": "4", "value" : $('#repeat_thursday').prop('checked')},
							{"day": "5", "value" : $('#repeat_friday').prop('checked')},
							{"day": "6", "value" : $('#repeat_saturday').prop('checked')},
						];
						var repeatBy = $('#repeat_on_monthly').find(':checked').val();
						var repeatNumber = Number($('#repeat_num').val());

						var repeatEnd = '';

						if(repeatType == 'daily'){
							// Get last appropriate day
							repeatEnd = repeatStart.add(repeatEvery*(repeatNumber-1), 'days');
						}
						else if(repeatType == 'weekly'){
							repeatEnd = repeatStart;

							var start = repeatDays.slice(0,repeatEnd.day());
							var end = repeatDays.slice(repeatEnd.day(), 7);
							var repeatDays = end.concat(start);

							weekdays = [];
							for(var i = 0; i < repeatDays.length; i++){
								if(repeatDays[i]['value']){
									weekdays.push(Number(repeatDays[i]['day']));
								}
							}

							var w = 0;
							while(repeatNumber > 0){
								for(var i = 0; i < weekdays.length; i++){
									if(weekdays[i] == 0 && w != 0){
										repeatEnd = repeatEnd.add(1, 'weeks');
									}
									// if haven't passed the day of the week that needed -
									if (repeatEnd.day() <= weekdays[i]) {
										// then just give me this week's instance of that day
										repeatEnd = repeatEnd.startOf('week').add(weekdays[i], 'days');
									} else {
										// otherwise, give me next week's instance of that day
										repeatEnd = repeatEnd.add(Number(repeatEvery), 'weeks').startOf('week').add(weekdays[i], 'days')
									}
									repeatNumber--;
									if(repeatNumber <= 0){
										break;
									}
								}
								w++;
							}

						}
						else if(repeatType == 'monthly'){
							if(repeatBy == 'day_of_month'){
								// Get last appropriate month
								repeatEnd = repeatStart.add(repeatEvery*(repeatNumber-1), 'months');
							} else if(repeatBy == 'day_of_week'){
								dayOfWeekOccurrance = Math.ceil(Number($('#datetimepicker_1').data("DateTimePicker").date().format('D'))/7);
								// Get last appropriate month
								repeatEnd = repeatStart.add(repeatEvery*(repeatNumber-1), 'months').startOf('month');
								// Get appropriate day of the week - first instance in month
								repeatEnd = repeatEnd.add(Number(7-repeatStart.day()), 'days');
								// Get appropriate week - add number of weeks - 1
								repeatEnd = repeatEnd.add(dayOfWeekOccurrance-1, 'weeks');
							}
						}
						else if(repeatType == 'yearly'){
							// Get last appropriate year
							repeatEnd = repeatStart.add(repeatEvery*(repeatNumber-1), 'years');
						}
					}
					else{
						var repeatStart = $('#datetimepicker_1').data("DateTimePicker").date();
						repeatEnd = repeatStart;
					}

					$("input[name='repeat_num_date']").val(repeatEnd.format('YYYY-MM-DD'));
				}
			}

			function generateRepeatSummary(){
				calculateEndDate();
				var repeatType = $('#repeat_type').val();
				var repeatEvery = $('#repeat_every').val();
				var repeatDays = {
					Sunday: $('#repeat_sunday').prop('checked'),
					Monday: $('#repeat_monday').prop('checked'),
					Tuesday: $('#repeat_tuesday').prop('checked'),
					Wednesday: $('#repeat_wednesday').prop('checked'),
					Thursday: $('#repeat_thursday').prop('checked'),
					Friday: $('#repeat_friday').prop('checked'),
					Saturday: $('#repeat_saturday').prop('checked'),
				};
				var repeatBy = $('#repeat_on_monthly').find(':checked').val();
				var repeatEndType = $('#repeat_end_type').find(':checked').val();
				var repeatNumber = $('#repeat_num').val();
				var repeatEndDate = $('#datetimepicker_3').data("DateTimePicker").date();

				var summary = "";

				// Get Repeat Type
				if(repeatEvery > 1){
					summary += 'Every ' + repeatEvery;
					switch (repeatType){
						case "daily":
							summary += ' days';
							break;
						case "weekly":
							summary += ' weeks';
							break;
						case "monthly":
							summary += ' months';
							break;
						case "yearly":
							summary += ' years';
							break;
						default:
					}
				}
				else{
					if(repeatType == 'yearly'){
						summary += 'Annually';
					}
					else{
						summary += repeatType.charAt(0).toUpperCase() + repeatType.slice(1);
					}
				}

				// Get Repeat Days
				if(repeatType == 'weekly'){
					summary += ' on';
					comma = false;
					for(var day in repeatDays){
						if(repeatDays[day]){summary += ((comma)?',':'')+' '+day;comma = true; }
					}
				}
				else if(repeatType == 'monthly'){
					if(repeatBy == 'day_of_month'){
						summary += ' on day '+$('#datetimepicker_1').data("DateTimePicker").date().format('DD');
					}
					else {
						dayOfWeekOccurrance = Math.ceil(Number($('#datetimepicker_1').data("DateTimePicker").date().format('D'))/7);
						if(dayOfWeekOccurrance == 1){dayOfWeekOccurrance = dayOfWeekOccurrance+'st';}
						else if(dayOfWeekOccurrance == 2){dayOfWeekOccurrance = dayOfWeekOccurrance+'nd';}
						else{dayOfWeekOccurrance = dayOfWeekOccurrance+'th';}
						summary += ' on the ' + dayOfWeekOccurrance + ' ' + $('#datetimepicker_1').data("DateTimePicker").date().format('dddd') + ' of the month';
					}
				}

				// Get Repeat End summary
				if(repeatEndType == 'number_occurrences'){
					if (repeatNumber == 1){
						summary = 'Once';
						$("#summary").html(summary);
						$("input[name='summary']").val(summary);
						return;
					}
					summary += ", " + repeatNumber + " times";
				}
				else if(repeatEndType == 'date'){
					if(repeatEndDate != null){
						repeatEndDate = repeatEndDate.format('MMMM D, Y');
					}
					summary += ", until " + repeatEndDate;
				}

				$("#summary").html(summary);
				$("input[name='summary']").val(summary);
				return;
			}
		</script>
			<?php return ob_get_clean();
	}
}

?>
