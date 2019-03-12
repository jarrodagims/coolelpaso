<?php
$dev = 0;

# Debugging 
if($dev){
	$messages = $_POST;
	$messages = json_encode($messages);
	echo $messages;
	
	ini_set('display_errors','1');
	ini_set("error_reporting", E_ALL);
}

# Includes
include_once('admin/includes/library.php');

Security::xssProtect();
Email::checkDontFillMeOut();
Email::spamFilter();
extract($_POST);


# Calculate time duration for submission
$finishTime = time();
$duration = $finishTime - $time;

# Contact Us Action
if($action == 'contact-us'){	
	
	# Spam detected
	if(detectSpam($comments,$duration,2) || !empty($url)){
		$from = $email;
		$fromName = $name;
		$message = 'The following Contact Us submission form has been submitted.<br /><br />
		<b>Name:</b> '.$fromName.'<br />
		<b>E-mail Address:</b> '.$email.'<br />
		<b>Phone:</b> '.$phone.'<br />
		<b>Message:</b><br />'.nl2br($comments).'<br />
		<b>IP ADDRESS:</b><br />'.$_SERVER['REMOTE_ADDR'].'<br />
		<b>Data:</b><br />'.serialize($_SERVER).'<br />
		<b>Time:</b>'.$date_submitted.'
		';
		$mail = new Email();
		$mail->AddAddress('karen@stantonstreet.com','Stanton Street');
		$mail->SetFrom($from,$fromName);
		$mail->Subject = 'SPAM DETECTION - Barrett Airworks - Contact Us Form Submission';
		$mail->setMsg($message);
		$mail->send();
		
		header("HTTP/1.0 404 Not Found");
		exit;
	}
	
	# Create instance of Contact Us Object
	$contactUs = new ContactUs($_POST);
	
	# Clear messages from object
	$contactUs->clearMessages();
	
	# Validate fields
	$contactUs->validate();

	# reCAPTCHA
	if($_SESSION['contact_recaptcha'] != 1)
	{
		$secret = $GLOBALS['recaptcha_secret_key'];
		$response = null;
		$reCaptcha = new ReCaptcha($secret);

		# Verify reCAPTCHA Response
		if($_POST["g-recaptcha-response"]){
			$response = $reCaptcha->verifyResponse(
				$_SERVER["REMOTE_ADDR"],
				$_POST["g-recaptcha-response"]
			);
		}

		# Validate reCAPTCHA Response
		if ($response == null || !$response->success){
			$contactUs->addMessage('recaptcha', array('type'=>'failure','text'=>'reCAPTCHA Response is not valid.'));
		}
		else if ($response->success){
			$_SESSION['contact_recaptcha'] = 1;
		}
	}
	
	# Failure
	# If Contact Us Object has messages
	if($contactUs->hasMessages()){
		echo failure("Please correct the following errors in the form before submitting.", $contactUs->getMessages());
		exit;
	}
	
	# Save Contact Us Submission to the database
	$contactUs->save();
	
	# Send the email
	# Create Email Object
	$mail = new Email();

	# Add Client Email Address
	#$mail->AddAddress('jag@elpasojag.com', 'Barrett Airworks');
	$mail->AddAddress('brian@stantonstreet.com', 'Barrett Airworks');

	# BCC Stanton Street
	#$mail->AddBCC('karen@stantonstreet.com','Stanton Street');

	# Set From Email Address
	$mail->SetFrom('donotreply@coolelpaso.com','Barrett Airworks');

	# Set Email Subject
	$mail->Subject = 'Barrett Airworks - Contact Us Submission';

	# Set Message
	$mail->setMsg($contactUs->toHtml('email'));

	# Send Email
	$mail->send();
	
	# Success	
	echo success("Thank you for contacting Barrett Airworks");
	unset($_SESSION['contact_recaptcha']);
	exit;
}
?>