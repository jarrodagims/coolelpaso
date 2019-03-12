<?php
include_once("includes/library.php");

if(sizeof($_SESSION['session_fullname'])){
	$user_info = 'Hello, ' . $_SESSION['session_fullname'];
}

// Output page
header("Content-Type:text/html; charset=utf-8");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=EDGE" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php echo $GLOBALS['admin_title']; ?></title>

        <!-- CSS -->
        <!-- Bootstrap core CSS -->
        <link rel="stylesheet" href="/admin/bootstrap/css/bootstrap.css">
 		<!-- Bootrap DateTimePicker CSS -->
        <link rel="stylesheet" href="/admin/bootstrap/css/datetimepicker.css">
        <!-- Bootrap Select CSS -->
        <link rel="stylesheet" href="/admin/javascript/bootstrap-select/bootstrap-select.min.css">
        <!-- Bootrap Color Picker -->
        <link rel="stylesheet" href="/admin/javascript/bootstrap-colorpicker/bootstrap-colorpicker.min.css">

        <!-- FontAwesome -->
        <script src="https://use.fontawesome.com/f2b8dd2b4d.js"></script>

        <!-- Fileinput -->
        <link rel="stylesheet" href="/admin/stylesheets/fileinput.css" />

       	<!-- Custom styles for this template
       	<link rel="stylesheet" href="/admin/stylesheets/style.css" /> -->
       	<link rel="stylesheet" href="/admin/stylesheets/animate.css" />
       	<link rel="stylesheet" href="/admin/stylesheets/buttons.css" />
       	<?php include_once('stylesheets/style.php'); ?>
		<meta name="theme-color" content="<?php echo $custom_color;?>" />
        <!-- jQuery UI -->
        <link type="text/css" rel="stylesheet" href="/admin/javascript/jquery-ui-1.11.4.custom/jquery-ui.min.css" />

		<?php if(isset($GLOBALS['CSS'])){echo $GLOBALS['CSS'];} ?>
        <!-- Javascript Includes -->
        <!-- jQuery -->
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="/js/jquery.min.js"><\/script>')</script>
		<!-- Bootstrap file Input - loads before bootstrap-->
		<script src="/admin/javascript/sortable.min.js" type="text/javascript"></script>
		<script src="/admin/javascript/purify.min.js" type="text/javascript"></script>
		<script src="/admin/javascript/canvas-to-blob.min.js" type="text/javascript"></script>
		<script src="/admin/javascript/fileinput.js" type="text/javascript"></script>
		<script src="/admin/stylesheets/fileinput/themes/fa/theme.js" type="text/javascript"></script>
		<!-- Bootstrap core JavaScript-->
       	<script src="/admin/bootstrap/js/moment.js" crossorigin="anonymous"></script>
        <script src="/admin/bootstrap/js/bootstrap.min.js" crossorigin="anonymous"></script>
        <script src="/admin/bootstrap/js/datetimepicker.js" crossorigin="anonymous"></script>

		<!-- Bootstrap Select JS-->
		<script src="/admin/javascript/bootstrap-select/bootstrap-select.min.js"></script>
		<!-- Bootstrap Colorpicker JS-->
		<script src="/admin/javascript/bootstrap-colorpicker/bootstrap-colorpicker.min.js"></script>

        <script type="text/javascript" src="/admin/javascript/functions.js"></script>
        <script type="text/javascript" src="/admin/javascript/jquery-ui-1.11.4.custom/jquery-ui.min.js"></script>
        <script type="text/javascript" src="/admin/javascript/tinymce/tinymce.min.js"></script>
        <script type="text/javascript" src="/admin/javascript/jquery.mjs.nestedSortable.js"></script>

        <script type="text/javascript">
        function loadDatepickers(){
            <?php echo $GLOBALS['JAVASCRIPT_DATEPICKER']; ?>
        }
        </script>
        <?php
		if(isset($_SESSION['LOGIN_SUCCESSFUL'])){ ?>
			<script>
			$(document).ready(function(){
				var formOutcome = <?php echo $_SESSION['LOGIN_OUTCOME']; ?>.formOutcome;
				notify(formOutcome);
			});
			</script>
		<?php
			unset($_SESSION['LOGIN_SUCCESSFUL']);
			unset($_SESSION['LOGIN_OUTCOME']);
		}?>
    	<?php
		if(isset($_SESSION['PAGE_RELOAD_TOAST'])){ ?>
			<script>
			$(document).ready(function(){
				var formOutcome = <?php echo $_SESSION['PAGE_RELOAD_TOAST']; ?>.formOutcome;
				notify(formOutcome);
			});
			</script>
		<?php
			unset($_SESSION['PAGE_RELOAD_TOAST']);
		}
		if($_SESSION['session_logged_in'] == true){
			$modules = new Module();
			$modules = $modules->fetchAll("WHERE `enabled` = '1' AND `super` = '0'", "ORDER BY `sort_order`, `name`");
		}
		?>
    </head>
    <body>
        <div class="nav-container">
        	<nav class="navbar navbar-default navbar-static-top">
                <div class="container">
                    <div class="navbar-header">
                       <?php
						if($_SESSION['session_logged_in'] == true){?>
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <?php } ?>
                        <a class="navbar-brand" href="/admin/admin.php">
                        	<center>
                            	<img src="/admin/images/logo-barrett-airworks.png" alt="<?php echo $GLOBALS['admin_title']; ?>">
							</center>
                        </a>
                    </div>
                    <div id="navbar" class="navbar-collapse collapse">
                        <?php
						if($_SESSION['session_logged_in'] == true){?>
						<ul class="nav navbar-nav">
							<?php echo showMobileMenu($modules); ?>
                        </ul>
                        <?php } ?>
                        <ul class="nav navbar-nav navbar-right">
                           <?php
							if($_SESSION['session_logged_in'] == true){?>
                            <li><span><? echo $user_info; ?></span></li>
                            <li><a href="/admin/logout.php" class="btn btn-custom" id="logout_button" >Logout</a></li>
                            <?php
							}
							else{?>
                      		<li><span>Website Administration</span></li>
                       		<?php
							} ?>
                        </ul>
                    </div><!--/.nav-collapse -->
                </div>
            </nav>
        </div>
         <?php
		if($_SESSION['session_logged_in'] == true){?>
			<div class="container">
				<div class="row">
					<div class="col-sm-3 hidden-xs" id="side_menu"><?php echo showMenu($modules); ?></div>
					<div class="col-sm-9" id="content">
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
						<?php echo $statusMessage; ?>
						<?php echo $content;?>
					</div>
				</div>
			</div>
       	<?php
		}
		else{
			echo $content;
		}
        ?>
        <?php if(isset($GLOBALS['JAVASCRIPT'])){echo $GLOBALS['JAVASCRIPT'];} ?>
        <script type="text/javascript">
			function checkUpload(size){
				if(size><?php
				   	$sSize = ini_get('post_max_size');
				   	$sSuffix = substr($sSize, -1);
    				$iValue = substr($sSize, 0, -1);
					switch(strtoupper($sSuffix)){
					case 'P':
						$iValue *= 1024;
					case 'T':
						$iValue *= 1024;
					case 'G':
						$iValue *= 1024;
					case 'M':
						$iValue *= 1024;
					case 'K':
						$iValue *= 1024;
						break;
					}echo $iValue;
				   ?>){
				   	size = size / 1024 / 1024;
				   	var n = size.toFixed(2);
					return'<span class="label label-failure">This file\'s size is  ' + n + "MB, which exceeds the maximum upload size of <?php echo ini_get('post_max_size');?>B</span>";
				}
			}
		</script>
        <script type="text/javascript" src="/admin/javascript/init.js"></script>
    </body>
</html>
