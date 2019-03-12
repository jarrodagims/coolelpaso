<?php
$dev = 0;

# Debugging 
if($dev){
	ini_set('display_errors','1');
	ini_set("error_reporting", E_ALL);
}
extract($_GET);
# Includes
include_once("includes/library.php");

# If admin system is already installed, throw an error.
$manageAdmin = new ManageAdmin();

if($manageAdmin->isInstalled()){
	$_SESSION['LOGIN_SUCCESSFUL'] = 0;
	$_SESSION['LOGIN_OUTCOME'] = failure("Administration system already installed.");
	header("Location: index.php");
	exit;
}

# Check database character set and collation
$model = new Model();
$query = "SELECT @@character_set_database AS `character_set`, @@collation_database AS `collation`";
$result = $model->query($query);

if(!$error){
	if(!$result || !sizeof($result)){
		$_SESSION['LOGIN_SUCCESSFUL'] = 0;
		$_SESSION['LOGIN_OUTCOME'] = failure("Could not verify database character set and collation.");
		header("Location: setup.php?error=true");
		exit;
	}else{
		$row = $result[0];
		extract($row);
		if($character_set != 'utf8' || $collation != 'utf8_general_ci'){
			$_SESSION['LOGIN_SUCCESSFUL'] = 0;
			$_SESSION['LOGIN_OUTCOME'] = failure('Warning: The database character set and collation for this database are not UTF-8. Continuing the setup process will create a database in which module tables will not be able to use UTF-8 characters.');
			header("Location: setup.php?error=true");
			exit;
		}
	}
}
# Handle rejected form
if(array_key_exists('SESSION_REJECTED_FORM',$_SESSION)){
	$HTTP_FORM = $_SESSION['SESSION_REJECTED_FORM'];
	unset($_SESSION['SESSION_REJECTED_FORM']);
}

# Page Title
$GLOBALS['page_title'] = "Setup";

# Required Fields
$required_fields = array('name','username','password','confirm_password');

ob_start();?>


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
			<div class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2" id="setup_form">
				<form action="install.php" method="post" autocomplete="off">
					<div class="form_content">
						<h3 id="setup_form_heading">Create Administrator Account</h3>
						<div class="denote-required">
							<i class="fa fa-asterisk"></i>
							Denotes a required field
						</div>
						<div class="row" style="display:none;">
							<div class="col-sm-12">
								<label for="name" class="required">Full Name</label>
								<input type="text" id="name" class="form-control" placeholder="Full Name" name="name" required="" value="Stanton Street Administrator">
							</div>
						</div>
						<div class="row" style="display:none;">
							<div class="col-sm-12">
								<label for="username" class="required">Username</label>
								<input type="text" id="username" class="form-control" placeholder="Username" name="username" required="" value="admin">
							</div>
						</div>							
						<div class="row">
							<div class="col-md-6">
								<label for="password" class="required">New Password</label>
								<input type="password" id="password" class="form-control" placeholder="Password" autofocus name="password" required="" value="">

								<label for="confirm_password" class="required">Confirm New Password</label>
								<input type="password" id="confirm_password" class="form-control" placeholder="Confirm New Password" name="confirm_password" required="" value="">
							</div>
							<div class="col-md-6">
								<?php echo $manageAdmin->buildPasswordCriteria(); ?>
							</div>
						</div>

						<input type="hidden" name="id" value="<?php //echo $this->getId(); ?>">
						<button class="btn btn-custom btn-block" type="submit">Submit</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<?php
$content .= ob_get_clean();

include_once("template.php");
?>