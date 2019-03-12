<?php

include_once("includes/library.php");

$manageAdmin = new ManageAdmin();

if(!$manageAdmin->isInstalled()){
	header("Location: setup/");
	exit;
}


if($_SESSION['session_logged_in'] == true){
	header("Location: admin.php");
	exit;
}

$GLOBALS['page_title'] = "Login";

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
			<div class="col-sm-6 col-sm-offset-3 col-lg-4 col-lg-offset-4" id="login_form">
				<? 	if($_SESSION['session_timeout']){ 
						unset($_SESSION['session_referer']); 
						if($_SERVER['HTTP_REFERER']){
							$_SESSION['session_referer'] = $_SERVER['HTTP_REFERER'];
						};?>
				<div class="alert alert-danger"><i class="fa fa-exclamation"></i><strong>Your Session has timed out.</strong> Please log in again.</div>
				<?php 
					unset($_SESSION['session_timeout']);
					} ?>
				<form action="login.php" method="post">
					<div class="form_content">
						<h3 id="login_form_heading">Login</h3>
						<?php echo $statusMessage; ?>
						<label for="username">Username</label>
						<input type="text" id="username" class="form-control" placeholder="Username" name="username" required="" autofocus>

						<label for="inputPassword">Password</label>
						<input type="password" id="password" class="form-control" placeholder="Password" name="password" required="">

						<button class="btn btn-custom btn-block" type="submit">Submit</button>
					</div>
				</form>
				<div class="credit">&copy; <?php echo date('Y'); ?> Stanton&nbsp;Street</div>
			</div>
		</div>
	</div>
</div>

<?php 

$content = ob_get_clean();

include_once("template.php");
?>