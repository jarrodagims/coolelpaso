<?php
class ManageAdmin extends Model{
	// Module Configuration
	public $_moduleName  = 'Manage Administrators';
	public $_moduleDir   = 'manage_admins';
	public $_moduleTable = 'manage_admins';
	public $_moduleClassName = 'ManageAdmin';
	// $_SESSION key that holds a rejected form for this module
	public $_moduleSessionFormName = 'MANAGE_ADMINS_REJECTED_FORM';
	public $_moduleDescription = 'This section allows administrators to create, modify, and delete CMS Administrators.';
	public $_moduleIcon = 'fa-user-plus';
	public $_moduleAllowedFailedAttempts = 2; // Once exceeded the account will be locked.
	public $_modulePasswordSalt = "Sstg1336!xyz";
	public $_modulePasswordMinLength = 8;
	public $_modulePasswordDigitCount = 1;
	public $_modulePasswordSpecialCharCount = 1;
	public $_modulePasswordUppercaseCount = 1;
	public $_modulePasswordLowercaseCount = 0;
	public $_modulePasswordAlphaNumericOnly = false;
	public $_modulePasswordDaysExpires = 90;
	
	// Static Variables
	protected static $_addLabel = 'Add Admin User';
	protected static $_editLabel = 'Edit Admin User';

	// Inherited Variables
	protected $_dbTable = 'manage_admins';
	//protected $_permalinkField = 'permalink';
	protected $_action = 'index';
	
	// Table Variables
	protected $_id;
	protected $_name;
	protected $_username;
	protected $_password;
	protected $_accessLevels;
	protected $_resetPassword = '0';
	protected $_passwordExpires;
	protected $_lastLogin = '';
	protected $_lastLoginData = '';
	protected $_lastLoginIp = '';
	protected $_failedAttempts = 0;
	protected $_locked = '0';
	
	// Instance Variables
	protected $_confirmPassword;
	protected $_newPassword;
	protected $_confirmNewPassword;
	protected $_oldPassword;
	protected $_updatePassword = false;
	protected $_requiredFields = array(
									'name',
									'username'
									);
									
	protected $_saveFields = array(
									'name',
									'username',
									'password',
									'access_levels',
									'reset_password',
									'password_expires',
									'last_login',
									'last_login_data',
									'last_login_ip',
									'failed_attempts',
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
	public function setUsername($value){$this->_username = (string) $value; return $this;}
	public function getUsername(){return $this->_username;}
	public function setPassword($value){$this->_password = (string) $value; return $this;}
	public function getPassword(){return $this->_password;}
	public function setAccessLevels($value){$this->_accessLevels = (string) $value; return $this;}
	public function getAccessLevels(){return $this->_accessLevels;}
	public function setResetPassword($value){$this->_resetPassword = (string) $value; return $this;}
	public function getResetPassword(){return $this->_resetPassword;}
	public function setPasswordExpires($value){$this->_passwordExpires = (string) $value; return $this;}
	public function getPasswordExpires(){return $this->_passwordExpires;}
	public function setLastLogin($value){$this->_lastLogin = (string) $value; return $this;}
	public function getLastLogin(){return $this->_lastLogin;}
	public function setLastLoginData($value){$this->_lastLoginData = (string) $value; return $this;}
	public function getLastLoginData(){return $this->_lastLoginData;}
	public function setLastLoginIp($value){$this->_lastLoginIp = (string) $value; return $this;}
	public function getLastLoginIp(){return $this->_lastLoginIp;}
	public function setFailedAttempts($value){$this->_failedAttempts = (string) $value; return $this;}
	public function getFailedAttempts(){return $this->_failedAttempts;}
	public function setLocked($value){$this->_locked = (string) $value; return $this;}
	public function getLocked(){return $this->_locked;}

	// Instance Methods
	public function setConfirmPassword($value){$this->_confirmPassword = (string) $value; return $this;}
	public function getConfirmPassword(){return $this->_confirmPassword;}
	public function setNewPassword($value){$this->_newPassword = (string) $value; return $this;}
	public function getNewPassword(){return $this->_newPassword;}
	public function setConfirmNewPassword($value){$this->_confirmNewPassword = (string) $value; return $this;}
	public function getConfirmNewPassword(){return $this->_confirmNewPassword;}
	public function setOldPassword($value){ $this->_oldPassword = (string) $value; return $this;}
	public function getOldPassword(){return $this->_oldPassword;}
	public function setUpdatePassword($value){$this->_updatePassword = (bool) $value; return $this;}
	public function getUpdatePassword(){return $this->_updatePassword;}
	
	public function install($username,$password){
		# Register module
		$this->register('','0','1');
		
		# Create table
		if(!$this->isInstalled()){
			$query = "
			CREATE TABLE `".$this->_dbTable."` (
			 `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
			 `name` varchar(255) NOT NULL DEFAULT '',
			 `username` varchar(255) NOT NULL DEFAULT '',
			 `password` varchar(255) NOT NULL DEFAULT '',
			 `access_levels` varchar(255) NOT NULL DEFAULT '',
			 `reset_password` enum('0','1') NOT NULL DEFAULT '0',
			 `password_expires` varchar(255) NOT NULL DEFAULT '',
			 `last_login` varchar(255) NOT NULL DEFAULT '',
			 `last_login_data` text NOT NULL,
			 `last_login_ip` varchar(255) NOT NULL DEFAULT '',
			 `failed_attempts` varchar(255) NOT NULL DEFAULT '',
			 `locked` enum('0','1') NOT NULL DEFAULT '0',
			 PRIMARY KEY (`id`)
			) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8
			";
			$result = $this->create($query);
		}
		
		# Add the first administrator account.
		$this->setUsername($username)
			 ->setPassword($password)
			 ->setPassword($this->md5Password())
			 ->setPasswordExpires(date('Y-m-d',strtotime('Today +'.$this->_modulePasswordDaysExpires.' days')))
			 ->setAccessLevels('All')
			 ->setLastLogin('')
			 ->setLastLoginData('')
			 ->setLastLoginIp('')
			 ->setFailedAttempts(0)
			 ->setLocked(0);
		
		parent::save();
		
		return $this;
	}
	
	public function save(){
		# Set password expiration
		if($this->getUpdatePassword()){
			$this->setPasswordExpires(date('Y-m-d',strtotime('Today +'.$this->_modulePasswordDaysExpires.' days')));
			$this->setPassword($this->md5Password());
		}
		else{
			if(!$this->getId() && $this->getPassword()){
				$this->setPassword($this->getPassword());
				$this->setPassword($this->md5Password());
			}
		}
		
		parent::save();
		
		return $this;
	}
	
	public function validate() {
		$this->checkRequired();
		return $this;
	}
	
	public function validatePassword(){
		if(strlen(trim($this->getPassword()))){
			if($this->getPassword() != $this->getConfirmPassword()){
				$this->addMessage('password',array('type'=>'failure','text'=>'Passwords do not match.'));
			}
			if($this->_modulePasswordMinLength > 0){
				if(strlen(trim($this->getPassword())) < $this->_modulePasswordMinLength){
					$this->addMessage('password_min_length',array('type'=>'failure','text'=>'Password must be at least '.$this->_modulePasswordMinLength.' character(s).'));
				}
			}
			if($this->_modulePasswordSpecialCharCount > 0){
				if(!preg_match("/[^a-zA-Z0-9]{".$this->_modulePasswordSpecialCharCount.",}/",$this->getPassword())){
					$this->addMessage('password_special_char_count',array('type'=>'failure','text'=>'Password must contain at least '.$this->_modulePasswordSpecialCharCount.' special character(s).'));
				}
			}
			if($this->_modulePasswordDigitCount > 0){
				if(!preg_match("/[0-9]{".$this->_modulePasswordDigitCount.",}/",$this->getPassword())){
					$this->addMessage('password_digit_count',array('type'=>'failure','text'=>'Password must contain at least '.$this->_modulePasswordDigitCount.' digit(s).'));
				}
			}
			if($this->_modulePasswordUppercaseCount > 0){
				if(!preg_match("/[A-Z]{".$this->_modulePasswordUppercaseCount.",}/",$this->getPassword())){
					$this->addMessage('password_uppercase_count',array('type'=>'failure','text'=>'Password must contain at least '.$this->_modulePasswordUppercaseCount.' uppercase character(s).'));
				}
			}
			if($this->_modulePasswordLowercaseCount > 0){
				if(!preg_match("/[a-z]{".$this->_modulePasswordLowercaseCount.",}/",$this->getPassword())){
					$this->addMessage('password_lowercase_count',array('type'=>'failure','text'=>'Password must contain at least '.$this->_modulePasswordLowercaseCount.' lowercase character(s).'));
				}
			}
			if($this->_modulePasswordAlphaNumericOnly){
				if(preg_match("/[^a-zA-Z0-9]+/",$this->getPassword())){
					$this->addMessage('password_alpha_numeric_only',array('type'=>'failure','text'=>'Password must contain only alphanumeric characters.'));
				}
			}
		}
		return $this;
	}
	
	public function md5Password(){
		return md5($this->_modulePasswordSalt.$this->getPassword());
	}
	
	public function getByUsername(){
		$manageAdmins = $this->fetchAll("WHERE `username` = '".$this->getUsername()."'");
		if(sizeof($manageAdmins)){
			return $manageAdmins[0];	
		}
		return false;
	}
	
	public function adminLogIn(){	
		$manageAdmins = $this->fetchAll("WHERE `username` = '".$this->getUsername()."' AND `password` = '".$this->md5Password()."'");
		if(sizeof($manageAdmins)){
			return $manageAdmins[0];	
		}
		return false;
	}
	
	public function logLogin($status = 'success'){
		switch($status){
			case 'success':
				$this->setLastLogin(date('Y-m-d H:i:s'))
					->setLastLoginData(serialize($_SERVER))
					->setLastLoginIp($_SERVER['REMOTE_ADDR'])
					->setFailedAttempts(0)
					->save();
			break;
			
			case 'failure':
				$failedAttempts = $this->getFailedAttempts() + 1;
				if($failedAttempts > $this->_moduleAllowedFailedAttempts){
					$this->setLocked(1);
				}
				$this->setLastLogin(date('Y-m-d H:i:s'))
					->setLastLoginData(serialize($_SERVER))
					->setLastLoginIp($_SERVER['REMOTE_ADDR'])
					->setFailedAttempts($failedAttempts)
					->save();
			break;
			
			default:
			// log nothing
			break;	
		}
	}
	
	public function buildPasswordCriteria(){
		ob_start();
		?>
		<div class="panel panel-custom">
			<div class="panel-heading"><i class="fa fa-key"></i>Password Requirements</div>
			<div class="panel-body">
				<ul>
				<?php
				if($this->_modulePasswordMinLength > 0){
					?>
					<li>Must be at least <?php echo $this->_modulePasswordMinLength; ?> characters in length.</li>
					<?php
				}
				if($this->_modulePasswordSpecialCharCount > 0){
					?>
					<li>Must contain at least <?php echo $this->_modulePasswordSpecialCharCount; ?> special character(s).</li>
					<?php
				}
				if($this->_modulePasswordDigitCount > 0){
					?>
					<li>Must contain at least <?php echo $this->_modulePasswordDigitCount; ?> digit(s).</li>
					<?php
				}
				if($this->_modulePasswordUppercaseCount > 0){
					?>
					<li>Must contain at least <?php echo $this->_modulePasswordUppercaseCount; ?> uppercase character(s).</li>
					<?php
				}
				if($this->_modulePasswordLowercaseCount > 0){
					?>
					<li>Must contain at least <?php echo $this->_modulePasswordLowercaseCount; ?> lowercase character(s).</li>
					<?php
				}
				if($this->_modulePasswordAlphaNumericOnly > 0){
					?>m
					<li>Must only contain alphanumeric characters.</li>
					<?php
				}
				if($this->_modulePasswordDaysExpires > 0){
					?>
					<li>Password will expire in <?php echo $this->_modulePasswordDaysExpires; ?> day(s) from date set or reset.</li>
					<?php	
				}
				?>
				</ul>
			</div>
		</div>
        <?php
        return ob_get_clean();
	}

	// Action Methods
	public function moduleIndexAction(){
		# Get the list of records
		$moduleClasses = new $this->_moduleClassName();
		if($_SESSION['session_user_id'] != 1){
			$moduleClasses = $moduleClasses->fetchAll("WHERE `id` <> 1","ORDER BY `name`,`id`");
		}else{
			$moduleClasses = $moduleClasses->fetchAll("","ORDER BY `id` = 1 DESC,`name`,`id`");
		}
		
		ob_start(); ?>
		
		<div class="index-wrapper">
			<table class="table table-hover">
				<thead>
					<tr>
						<th>Name</th>
						<th>
							<a class="btn btn-green pull-right" href="<?php echo $this->buildModalUrl('add'); ?>" data-toggle="modal" data-target="#moduleModal"><i class="fa fa-plus"></i>Create New Administrator</a>
						</th>
					</tr>
				</thead>
				<tbody>
					<?php 
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
	
		<tr id="<?php echo $this->_moduleClassName."_".$this->getId();?>" class="<?php echo ($this->getId() == 1)? "unsortable": "";?>">
			<td class="name"><?php echo ($this->getId() == 1)? '<b>'.$this->getName().'</b>' : $this->getName(); ?></td>
			<td>
				<?php if(!$this->getLocked() || ($_SESSION['session_user_id'] == 1 && $this->getLocked())){ ?>
				<span class="action_menu">
					<a tabindex="0" class="pull-right" role="button" data-toggle="popover" data-trigger="focus" data-placement="bottom" data-content="<?php echo $this->toHtml('menu'); ?>" data-original-title="" title=""><i class="fa fa-gear" aria-hidden="true"></i></a>
				</span>
				<?php } ?>
			</td>
		</tr>
		
		<?php
		return ob_get_clean();
	}
	
	public function menuAction(){
		ob_start(); ?>
        <ul class="actions">
        	<li><a href="<?php echo $this->buildModalUrl('edit'); ?>" data-toggle="modal" data-target="#moduleModal"><i class="fa fa-pencil"></i>Edit</a></li>
            <?php if($this->getId() != 1 && $this->getId() != $_SESSION['session_user_id']){?><li><a href="<?php echo $this->buildModalUrl('confirm','delete_confirm'); ?>" class="delete js_required" data-toggle="modal" data-target="#confirmModal"><i class="fa fa-trash-o"></i>Delete</a></li><?php }?>
        </ul>
        <?php
		$html = ob_get_clean();
		$html = str_replace('"', "'", $html);
		return $html;	
	}

	public function adminAddAction(){
		return $this->buildAdminAddEditHtml('add');
	}
	
	public function adminEditAction(){
		return $this->buildAdminAddEditHtml('edit');
	}
	
	protected function resetPasswordAction(){
		$messages = $this->prepareMessages();
		$this->clearMessages();
		
		ob_start();
		?>
		<div class="container">
			<div class="row">
				<div class="col-xs-12">
					<div id="toast-container">
						<div class="toast toast-prototype">
							<i class="fa"></i>
							<div class="toast-content">
								<div class="toast-title"></div>
								<div class="toast-message"></div>
							</div>
						</div>
					</div>
					<div class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2" id="reset_password_form">
						<form action="reset-password-action.php" method="post" autocomplete="off">
							<div class="form_content">
								<h3 id="reset_password_form_heading">Reset Password</h3>
								
								<div class="row">
									<div class="col-sm-12">
										<label for="old_password">Existing Password</label>
										<input type="password" id="old_password" class="form-control" placeholder="Existing Password" name="old_password" required="" autofocus value="">
									</div>
								</div>								
								<div class="row">
									<div class="col-md-6">
										<label for="password">New Password</label>
										<input type="password" id="password" class="form-control" placeholder="Password" name="password" required="" value="">

										<label for="confirm_password">Confirm New Password</label>
										<input type="password" id="confirm_password" class="form-control" placeholder="Confirm New Password" name="confirm_password" required="" value="">
									</div>
									<div class="col-md-6">
										<?php echo $this->buildPasswordCriteria(); ?>
									</div>
								</div>
								
								<input type="hidden" name="id" value="<?php echo $this->getId(); ?>">
								<button class="btn btn-custom btn-block" type="submit">Submit</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
        <?php
		return ob_get_clean();
	}
	
	protected function buildAdminAddEditHtml($action){
		if(!in_array($action,array('add','edit'))){
			return '';
		}
		$actionLabel = $this->{'get'.ucfirst($action).'Label'}();
		// Build arrays for access levels selection
		//$values = array("All");
		//$labels = array("All");
		
		$modules = new Module();
		$modules = $modules->fetchAll('WHERE `enabled` = 1');
		
		foreach($modules as $module){
			$values[] = $module->getId();
			$labels[] = $module->getName();
		}
	
		$values[] = 'webtraffic';
		$labels[] = 'Web Traffic Reporting';
		$values[] = 'support';
		$labels[] = 'Support';
		$values[] = 'postmarketer';
		$labels[] = 'Postmarketer';
		//
		$messages = $this->prepareMessages();
		$this->clearMessages();
		//
		
		//page title 
		$GLOBALS['page_title'] = $actionLabel;
		
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
        	<div class="form-group">
            	<label for="name" class="required">Name</label>
                <input type="text" id="name" class="form-control" name="name" size="40" placeholder="Name" value="<?php echo process($this->getName()); ?>" />
                <div class="error name"></div>
        	</div>
            <div class="form-group">
            	<label for="username" class="required">Username</label>
                <input type="text" id="username" class="form-control" name="username" size="40" placeholder="Username" value="<?php echo process($this->getUsername()); ?>" />
                <div class="error username"></div>
        	</div>
            <?php
			if($action == 'edit'){ $password_change = "New "; }
			else{ $password_change = ""; }?>
            <div class="row">
            	<div class="col-sm-6">
                	<div class="form-group">
                        <label for="password"><?php echo $password_change;?> Password</label>
                        <input type="text" id="password" class="form-control" name="password" size="40" placeholder="<?php echo $password_change; ?>Password" value="" />
						<div class="error password"></div>
						<div class="error password_min_length"></div>
						<div class="error password_special_char_count"></div>
						<div class="error password_digit_count"></div>
						<div class="error password_uppercase_count"></div>
						<div class="error password_lowercase_count"></div>
						<div class="error password_alpha_numeric_only"></div>	
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirm <?php echo $password_change." ";?>Password</label>
                        <input type="text" id="confirm_password" class="form-control" name="confirm_password" size="40" placeholder="Confirm Password" value="" />
                    </div>
                    <div class="form-group">
                        <label for="access_levels" class="block">Access Levels</label>
						<?php 

						if($this->getAccessLevels() == 'All'){ $selectedValues = $values; }
						else{ $selectedValues = unserialize($this->getAccessLevels()); }

						echo FormComponent::multipleSelectList("access_levels",$values,$labels,$selectedValues,"access_levels","",1);
						?>
                        <div class="error access_levels"></div>
                    </div>
                    <div class="form-group">
                        <label class="block">User must reset password at next login?</label>
                        <?php 
							echo FormComponent::checkBox("reset_password",'1',$this->getResetPassword(),"reset_password","Yes");
						?>
                        <div class="error reset_password"></div>
                    </div>
                </div>
                <div class="col-sm-6">
                	<?php echo $this->buildPasswordCriteria(); ?>
                </div>
            </div>
            <?php if($action == 'edit'): ?> 
			<input type="hidden" name="id" value="<?php echo $this->getId(); ?>" />
			<?php endif; ?> 
			<input type="hidden" name="action" value="<?php echo $action; ?>" />
		</form>
		<?php
		
		$form =  ob_get_clean();
		
		$modal = new Module();
		return $modal->buildInnerModal($actionLabel, $form);
	}

	public function adminAddJavascriptAction(){
		return $this->buildAdminAddEditJavascript('add');
	}

	public function adminEditJavascriptAction(){
		return $this->buildAdminAddEditJavascript('edit');
	}

	protected function buildAdminAddEditJavascript($action){
		if(!in_array($action,array('add','edit'))){
			return '';
		}
	}
	
	public function buildAdminJavascript(){	
		ob_start();?>
		<script>
			function moduleSpecificRefresh() {
				initPopOver();
				var parent = $('tbody');
				var toSort = parent.children('tr:not(.unsortable)').get();
				toSort.sort(function(a, b) {
				   return $(a).find('td').text().toUpperCase().localeCompare($(b).find('td').text().toUpperCase());
				})
				$.each(toSort, function(idx, itm) { parent.append(itm); });
			}
		</script>
	<?php return ob_get_clean();
	}
}
?>