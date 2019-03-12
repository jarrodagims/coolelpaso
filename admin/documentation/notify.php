<?php
include_once("/admin/includes/library.php");

$GLOBALS['page_title'] = "Components";

ob_start();
?>
<div class="row">
	<div class="col-sm-12">
		<button class="btn btn-success" onClick="testSuccess()">Success Toast</button>
		<button class="btn btn-danger" onClick="testDanger()">Danger Toast</button>
		<button class="btn btn-warning" onClick="testWarning()">Warning Toast</button>
		<button class="btn btn-info" onClick="testInfo()">Info Toast</button>
	</div>
</div>

<div class="row">
	<div class="col-sm-12"><br>
		<button class="btn btn-custom">Custom Button</button>
		<button class="btn btn-default">Default Button</button>
	</div>
</div>

<div class="row">
	<div class="col-sm-12" style="margin: 10px 0 0 0">
		<div class="alert alert-success" role="alert"><i class="fa fa-check"></i><strong>Well done!</strong> You successfully read this important alert message. </div>
		<div class="alert alert-danger" role="alert"><i class="fa fa-times"></i><strong>Oh snap!</strong> Change a few things up and try submitting again. </div>
		<div class="alert alert-warning" role="alert"><i class="fa fa-warning"></i><strong>Warning!</strong> Better check yourself, you're not looking too good. </div>
		<div class="alert alert-info" role="alert"><i class="fa fa-info-circle"></i><strong>Heads up!</strong> This alert needs your attention, but it's not super important. </div>
	</div>
</div>

<div class="row">
	<div class="col-sm-12" style="margin: 10px 0 0 0">
		<div class="alert alert-success-o" role="alert"><i class="fa fa-check"></i><strong>Well done!</strong> You successfully read this important alert message. </div>
		<div class="alert alert-danger-o" role="alert"><i class="fa fa-times"></i><strong>Oh snap!</strong> Change a few things up and try submitting again. </div>
		<div class="alert alert-warning-o" role="alert"><i class="fa fa-warning"></i><strong>Warning!</strong> Better check yourself, you're not looking too good. </div>
		<div class="alert alert-info-o" role="alert"><i class="fa fa-info-circle"></i><strong>Heads up!</strong> This alert needs your attention, but it's not super important. </div>
	</div>
</div>

<div class="row">
	<div class="col-sm-12">
		<span class="label label-default">Default</span>
		<span class="label label-custom">Custom</span>
		<span class="label label-primary">Primary</span>
		<span class="label label-success">Success</span>
		<span class="label label-info">Info</span>
		<span class="label label-warning">Warning</span>
		<span class="label label-danger">Danger</span>
		<br /><br />
	</div>
</div>

<div class="row">
	<div class="col-sm-6">
		<div class="panel panel-default">
			<div class="panel-heading">Panel Default</div>
			<div class="panel-body"></div>
		</div>
	</div>
	<div class="col-sm-6">
		<div class="panel panel-custom">
			<div class="panel-heading">Panel Custom</div>
			<div class="panel-body"></div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-sm-6">
		<div class="panel panel-success">
			<div class="panel-heading">Panel Success</div>
			<div class="panel-body"></div>
		</div>
	</div>
	<div class="col-sm-6">
		<div class="panel panel-danger">
			<div class="panel-heading">Panel Danger</div>
			<div class="panel-body"></div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-sm-6">
		<div class="panel panel-warning">
			<div class="panel-heading">Panel Warning</div>
			<div class="panel-body"></div>
		</div>
	</div>
	<div class="col-sm-6">
		<div class="panel panel-info">
			<div class="panel-heading">Panel Info</div>
			<div class="panel-body"></div>
		</div>
	</div>
</div>
<?php $content = ob_get_clean(); 

if($_SESSION['session_logged_in'] != true){
	ob_start();?>

	<div class="container">
		<div class="row">
			<div class="col-sm-12" id="content">
				<div id="toast-container">
					<div class="toast toast-prototype">
						<i class="fa"></i>
						<div class="toast-content">
							<div class="toast-title"></div>
							<div class="toast-message"></div>
						</div>
					</div>
				</div>
				<h1><?php echo $GLOBALS['page_title']; ?></h1>
			</div>
		</div>
		<?php echo $content ?>
	</div>

	<?php 

	$content = ob_get_clean();
}

ob_start();
?>
<script>
function testSuccess(){
	toast('success', 'Well Done!', 'You successfully read this important message.');
}
function testDanger(){
	toast('danger', 'Well Done!', 'You successfully read this important message.');
}
function testWarning(){
	toast('warning', 'Well Done!', 'You successfully read this important message.');
}
function testInfo(){
	toast('info', 'Well Done!', 'You successfully read this important message.');
}
</script>
<?php
$GLOBALS['JAVASCRIPT'] = ob_get_clean();

include_once("../template.php");
?>