<?php
class FormSubmissions extends Model{
	// Module Configuration
	public $_moduleName  = 'Form Submissions';
	public $_moduleDir   = 'form_submissions';
	public $_moduleTable = 'form_submissions';
	public $_moduleClassName = 'FormSubmissions';
	public $_moduleDescription = '';
	public $_moduleIcon = 'fa-envelope';
	
	// Static Variables
	protected static $_addLabel = '';
	protected static $_editLabel = '';

	// Inherited Variables
	protected $_dbTable = 'form_submissions';
	//protected $_permalinkField = 'permalink';
	protected $_action = 'index';
	
	// Table Variables
	protected $_id;
	protected $_formId;
	protected $_formData;
	protected $_submissionData;
	protected $_archived='0';
	protected $_created = '0000-00-00 00:00:00';
	
	// Instance Variables
	protected $_saveFields = array(
									'form_id',
									'form_data',
									'submission_data',
									'archived',
									'created'
									);
	
	// Constructor
	public function __construct($id = 0)
	{
		parent::__construct($id);
	}
	
	// Static Methods
	public static function setAddLabel($v){self::$_addLabel = (string)$v;}
	public static function getAddLabel(){return self::$_addLabel;}
	public static function setEditLabel($v){self::$_editLabel = (string)$v;}
	public static function getEditLabel(){return self::$_editLabel;}

	// Accessor Methods
	public function setId($v){$this->_id = (int)$v; return $this;}
	public function getId(){return $this->_id;}
	public function setFormId($v){$this->_formId = (string)$v; return $this;}
	public function getFormId(){return $this->_formId;}
	public function setFormData($v){$this->_formData = (string)$v; return $this;}
	public function getFormData(){return $this->_formData;}
	public function setSubmissionData($v){$this->_submissionData = (string)$v; return $this;}
	public function getSubmissionData(){return $this->_submissionData;}
	public function setArchived($value){ $this->_archived = (string) $value; return $this;}
	public function getArchived(){ return $this->_archived; }
	public function setPartySize($value){ $this->_partySize = (string) $value; return $this;}
	public function getPartySize(){ return $this->_partySize; }
	public function setCreated($v){$this->_created = (string)$v; return $this;}
	public function getCreated(){return $this->_created;}


	
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
	
	public function formatDateSubmitted(){
		$date = date_create($this->getCreated());
		$date = date_format($date, "F j, Y g:i A");
		return $date;
	}

	public static function checkDontFillMeOut(){
		if(strlen(trim($_REQUEST['dontfillmeout']))){
			header('Location: /');
			exit;
		}
	}

	// Action Methods
	public function moduleIndexAction($formId){
		# Get the list of records
		$moduleClasses = new $this->_moduleClassName();
		$moduleClasses = $moduleClasses->fetchAll("WHERE `form_id` = '".$formId."'","ORDER BY `created` DESC");
		
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
				<?php $form = new Form($this->getFormId()); echo $form->getName(); ?><br>
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
        </ul>
        <?php
		$html = ob_get_clean();
		$html = str_replace('"', "'", $html);
		return $html;	
	}
	
	public function adminViewAction(){
		ob_start();?>
		
		<div class="view-modal">
		
		<?php	
			// double decode json i guess...
			$form = json_decode(json_decode(html_entity_decode($this->getFormData()), TRUE), TRUE);
			//echo '<pre>';print_r($form);echo '</pre>';	
			$submissionData = unserialize($this->getSubmissionData());
			//echo '<pre>'; print_r($submissionData);	echo '</pre>';
			?>
			<div class="row">
				<div class="col-sm-4">
					<strong>Date Submitted</strong>
				</div>
				<div class="col-sm-8"><?php echo process(printDateAndTime($this->getCreated())); ?></div>
			</div>
			<?php $counter=0; foreach($submissionData as $key => $value){ ?>
			<div class="row">
				<div class="col-sm-4">
					<strong><?php echo $form[$counter]['label']; ?>:</strong>
				</div>
				<div class="col-sm-8"><?php 
					if(strstr($value[0], 'undefined|')){
						$parts = explode('|',$value[0]);
						unset($parts[0]);
						$parts = array_unique($parts);
						foreach($parts as $parts){
							$selectionList .= ', '.$parts;
						}; 
						echo ltrim($selectionList, ', ');						
					}else{
						echo $value[0]; 
					}
					?></div>
			</div>
		<?php $counter++;} ?>
		</div>
		<?php 
		$content = ob_get_clean();
		$modal = new Module();
		return $modal->buildInnerModal("View Contact Submission", $content, 0);
	}
	
	

	
	public function sendEmail(){
		// double decode json i guess...
		$form = new Form($this->getFormId());
		$formStructure = json_decode(json_decode(html_entity_decode($this->getFormData()), TRUE), TRUE);
		$submissionData = unserialize($this->getSubmissionData());
		ob_start();
		?>
        The following <?php echo $form->getName(); ?> submisssion has been received from the web site.<br /><br />
        <b>Submission Information</b><br />
        <b>Date Submitted:</b> <?php echo process(printDateAndTime($this->getCreated())); ?><br />
        <?php $counter=0; foreach($submissionData as $key => $value){ ?>
        <b><?php echo $formStructure[$counter]['label']; ?>:</b> <?php 
		if(strstr($value[0], 'undefined|')){
			$parts = explode('|',$value[0]);
			unset($parts[0]);
			$parts = array_unique($parts);
			foreach($parts as $parts){
				$selectionList .= ', '.$parts;
			}; 
			echo ltrim($selectionList, ', ');						
		}else{
			echo $value[0]; 
		}
		?><br />
		<?php $counter++;} 
		$msg = ob_get_clean();
		
		# Send the email
		# Create Email Object
		$mail = new Email();

		# Add Client Email Address
		foreach(preg_split("/[;, \n]+/", $form->getRecipients()) as $recipient){
			if(strlen(trim($recipient)) > 1){
				$mail->AddAddress(trim($recipient),'Website Feedback');
			}
		}
		# BCC Stanton Street
		$mail->AddBCC('mason@stantonstreet.com','Stanton Street');

		# Set From Email Address
		$mail->SetFrom('donotreply'.$GLOBALS['email_domain'],$GLOBALS['site_name']);

		# Set Email Subject
		$mail->Subject = $form->getSubject();

		# Set Message
		$mail->setMsg($msg);

		# Send Email
		$mail->send();
	}
}
?>