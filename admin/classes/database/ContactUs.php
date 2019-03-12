<?php
class ContactUs extends Model{
	// Module Configuration
	public $_moduleName  = 'Contact Us Submissions';
	public $_moduleDir   = 'contact_us';
	public $_moduleTable = 'contact_us';
	public $_moduleClassName = 'ContactUs';
	public $_moduleDescription = 'This section allows administrators to view submissions captured using the Contact Us Form.';
	public $_moduleIcon = 'fa-envelope';
	
	// Static Variables
	protected static $_addLabel = 'Add Contact Us Submission';
	protected static $_editLabel = 'Edit Contact Us Submission';

	// Inherited Variables
	protected $_dbTable = 'contact_us';
	//protected $_permalinkField = 'permalink';
	protected $_action = 'index';
	
	// Table Variables
	protected $_id;
	protected $_name;
	protected $_dateSubmitted;
	protected $_company;
	protected $_email;
	protected $_phoneNumber;
	protected $_body;
	protected $_archived = '0';
	
	// Instance Variables
	protected $_requiredFields = array(
									'name',
									'date_submitted',
									'email',
									'body');
									
	protected $_saveFields = array(
									'name',
									'date_submitted',
									'company',
									'email',
									'phone_number',
									'body',
									'archived');
	
	
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
	public function setId($value){$this->_id = (int) $value; return $this;}
	public function getId(){ return $this->_id;}
	public function setName($value){$this->_name = (string) $value; return $this;}
	public function getName(){ return $this->_name;}
	public function setDateSubmitted($value){$this->_dateSubmitted = (string) $value; return $this;}
	public function getDateSubmitted(){ return $this->_dateSubmitted;}
	public function setCompany($value){$this->_company = (string) $value; return $this;}
	public function getCompany(){ return $this->_company;}
	public function setEmail($value){$this->_email = (string) $value; return $this;}
	public function getEmail(){ return $this->_email;}
	public function setPhoneNumber($value){$this->_phoneNumber = (string) $value; return $this;}
	public function getPhoneNumber(){ return $this->_phoneNumber;}
	public function setBody($value){$this->_body = (string) $value; return $this;}
	public function getBody(){ return $this->_body;}
	public function setArchived($value){$this->_archived = (string) $value; return $this;}
	public function getArchived(){ return $this->_archived;}

	// Instance Methods	
	public function install($username,$password){
		# Register module
		$this->register();
		
		# Create table
		if(!$this->isInstalled()){
			$query = "
			CREATE TABLE `".$this->_dbTable."` (
			 `id` int(11) NOT NULL AUTO_INCREMENT,
			 `name` varchar(255) NOT NULL,
			 `date_submitted` datetime NOT NULL,
			 `company` varchar(255) NOT NULL,
			 `email` varchar(255) NOT NULL,
			 `phone_number` varchar(255) NOT NULL,
			 `body` text NOT NULL,
			 `archived` enum('0','1') NOT NULL,
			 PRIMARY KEY (`id`)
			) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8
			";
			$result = $this->create($query);
		}
		
		return $this;
	}
	
	public function validate(){
		if(!$this->checkRequired()){
			$this->addMessage('general',array('type'=>'failure','text'=>'Please complete all <b>required</b> fields'));
		}
		if(strlen($this->getEmail()) && !Email::validateEmail($this->getEmail())){
			$this->addMessage('email',array('type'=>'failure','text'=>'e-mail address is invalid'));
		}
		if($this->hasMessages() && !$this->hasMessage('general')){
			$this->addMessage('general',array('type'=>'failure','text'=>'Your submission contains errors<br />Please correct them and try again'));
		}
	}
	
	public static function checkDontFillMeOut(){
		if(strlen(trim($_REQUEST['dontfillmeout']))){
			header('Location: /');
			exit;
		}
	}
	
	
	public function formatDateSubmitted(){
		$date = date_create($this->getDateSubmitted());
		$date = date_format($date, "F j, Y g:i A");
		return $date;
	}

	// Action Methods
	public function moduleIndexAction(){
		# Get the list of records
		$moduleClasses = new $this->_moduleClassName();
		$moduleClasses = $moduleClasses->fetchAll("WHERE `archived` = '0'","ORDER BY `date_submitted` DESC");
		
		ob_start(); ?>
		
		<div class="index-wrapper">
			<table class="table table-hover">
				<thead>
					<tr>
						<th>Name</th>
						<th>
							<a class="btn btn-custom pull-right" href="/admin/modules/<?php echo $this->_moduleDir; ?>/archive.php"><i class="fa fa-archive"></i>View Archive</a>
						</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					if(count($moduleClasses) <= 0){?>
					<tr>
						<td colspan="2"><div class='no_records'><i class='fa fa-times-circle'></i>No records available.</div></td>
					</tr>
					<?php }
					foreach($moduleClasses as $moduleClass){
						echo $moduleClass->toHtml('row');
					}
					?>
				</tbody>
			</table>
		</div>
		
		<?php
		return ob_get_clean();
	}
	
	public function archiveAction(){
		# Get the list of records
		$moduleClasses = new $this->_moduleClassName();
		$moduleClasses = $moduleClasses->fetchAll("WHERE `archived` = '1'","ORDER BY `date_submitted` DESC");
		
		ob_start(); ?>
		
		<div class="index-wrapper">
			<table class="table table-hover">
				<thead>
					<tr>
						<th colspan="2">Name</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					if(count($moduleClasses) <= 0){?>
					<tr>
						<td colspan="2"><div class='no_records'><i class='fa fa-times-circle'></i>No records available.</div></td>
					</tr>
					<?php }
					foreach($moduleClasses as $moduleClass){
						echo $moduleClass->toHtml('row');
					}
					?>
				</tbody>
			</table>
		</div>
		
		<?php
		return ob_get_clean();
	}
	
	public function rowAction(){
		ob_start(); ?>
		
		<tr id="<?php echo $this->_moduleClassName."_".$this->getId();?>">
			<td class="name">
				<?php echo $this->getName(); ?><br>
				<small><em><?php echo $this->formatDateSubmitted(); ?></em></small>
			</td>
			<td>
				<span class="action_menu">
					<a tabindex="0" class="pull-right" role="button" data-toggle="popover" data-trigger="focus" data-placement="bottom" data-content="<?php echo $this->toHtml('menu'); ?>" data-original-title="" title=""><i class="fa fa-gear" aria-hidden="true"></i></a>
				</span>
			</td>
		</tr>
		
		<?php
		return ob_get_clean();
	}
	
	public function menuAction(){
		ob_start(); ?>
        <ul class="actions">
        	<li><a href="<?php echo $this->buildModalUrl('view'); ?>" data-toggle="modal" data-target="#moduleModal"><i class="fa fa-search"></i>View</a></li>
        	<?php if(!$this->getArchived()){?><li><?php echo $this->buildToggle("archived"); ?></li><?php } ?>
        	<li><a href="<?php echo $this->buildModalUrl('confirm','delete_confirm'); ?>" class="delete" data-toggle="modal" data-target="#confirmModal"><i class="fa fa-trash-o"></i>Delete</a></li>
        </ul>
        <?php
		$html = ob_get_clean();
		$html = str_replace('"', "'", $html);
		return $html;	
	}
	
	public function adminViewAction(){
		ob_start();?>
		
		<div class="view-modal">
			<div class="row">
				<div class="col-sm-4">
					<strong>Name</strong>
				</div>
				<div class="col-sm-8"><?php echo $this->getName(); ?></div>
			</div>
			<div class="row">
				<div class="col-sm-4">
					<strong>Email</strong>
				</div>
				<div class="col-sm-8"><?php echo $this->getEmail(); ?></div>
			</div>
			<div class="row">
				<div class="col-sm-4">
					<strong>Company</strong>
				</div>
				<div class="col-sm-8"><?php echo $this->getCompany(); ?></div>
			</div>
			<div class="row">
				<div class="col-sm-4">
					<strong>Phone Number</strong>
				</div>
				<div class="col-sm-8"><?php echo $this->getPhoneNumber(); ?></div>
			</div>
			<div class="row">
				<div class="col-sm-4">
					<strong>Date Submitted</strong>
				</div>
				<div class="col-sm-8"><?php echo $this->formatDateSubmitted(); ?></div>
			</div>
			<div class="row">
				<div class="col-sm-4">
					<strong>Body</strong>
				</div>
				<div class="col-sm-8"><?php echo $this->getBody(); ?></div>
			</div>
		</div>
		
		<?php 
		$content = ob_get_clean();
		$modal = new Module();
		return $modal->buildInnerModal("View Contact Submission", $content, 0);
	}
	
	public function formAction(){
		ob_start(); ?>
		<form method="post" action="/contact-us-action.php" class="ajax-form">
			<h2>We are happy to answer your questions</h2>
			<p>Fill the form below to send us an inquiry.</p>

			<div class="status-alert"></div>
			<div class="error general"></div>

			<div class="form-group">
				<label class="acumin caps required" for="name">Name</label>
				<input type="text" class="form-control" name="name" placeholder="Name" />
				<div class="error name"></div>
			</div>
			<div class="form-group">
				<label class="acumin caps" for="company">Company</label>
				<input type="text" class="form-control" name="company" placeholder="Company" />
				<div class="error company"></div>
			</div>
			<div class="form-group">
				<label class="acumin caps required" for="email">Email</label>
				<input type="text" class="form-control" name="email" placeholder="Email" />
				<div class="error email"></div>
			</div>
			<div class="form-group">
				<label class="acumin caps" for="phone_number">Phone Number</label>
				<input type="text" class="form-control" name="phone_number" placeholder="Phone Number" />
				<div class="error phone_number"></div>
			</div>
			<div class="form-group">
				<label class="acumin caps required" for="body">Request Details</label>
				<textarea class="form-control" name="body" placeholder="Request Details"></textarea>
				<div class="error body"></div>
			</div>
			<div class="row">
				<div class="col-sm-12">
					<div class="g-recaptcha" data-sitekey="<?php echo $GLOBALS['recaptcha_site_key']; ?>"></div>
					<div class="error recaptcha"></div>
				</div>
			</div>
			<div class="dontfillmeout" style="display:none;">
				<label for="dontfillmeout">Don't Fill Me Out</label>
				<input type="text" id="dontfillmeout" name="dontfillmeout" />
			</div>
			<input type="hidden" name="date_submitted" value="<?php echo date('Y-m-d H:i:s') ?>" />        
			<input type="hidden" name="time" value="<?php echo time() ?>" />        
			<input type="hidden" name="action" value="contact-us" />
			<input type="submit" class="btn btn-primary btn-block btn-lg acumin caps" />
		</form>
		<?php 
		$content =  ob_get_clean();
		
		ob_start(); ?>
		<script src='https://www.google.com/recaptcha/api.js'></script>
		<?php
		$GLOBALS['JAVASCRIPT'] = ob_get_clean();
		
		return $content;
	}
	
	public function emailAction(){
		ob_start();
		?>
        The following Contact Us submisssion has been received from the web site.<br /><br />
        <b>Contact Information</b><br />
        <b>Name:</b> <?php echo process($this->getName()); ?><br />
        <b>Company:</b> <?php echo process($this->getCompany()); ?><br />
       	<b>E-mail Address:</b> <?php echo process($this->getEmail()); ?><br />
        <b>Phone Number:</b> <?php echo process($this->getPhoneNumber()); ?><br />
        <b>Date Submitted:</b> <?php echo process($this->getDateSubmitted()); ?><br />
        <b>Request Details:</b> <?php echo process(trim($this->getBody())); ?>
        <?php
		return ob_get_clean();
	}
}
?>