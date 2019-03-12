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

ContactUs::checkDontFillMeOut();
Security::xssProtect();

extract($_POST);


switch ($form_action){
    case 'submit':
		$form = new Form($form_id); 
		$formSubmissions = new FormSubmissions(); 
		$messages = array();
		$type = 'success';

		foreach($form_inputs as $name => $value){
			if(in_array($name,$form_required) && $value[0] == ''){
				$messages[$name] = array("type"=>'failure', "text"=>'This field is required');$type = 'failure';
			}else{
				$messages[$name] = array("type"=>'success', "text"=>'');
			}
			if($value[1] == 'email' && !Sanitize::isValidEmail($value[0]) && $value[0] != ''){
				$messages[$name] = array("type"=>'failure', "text"=>'e-mail address is invalid');$type = 'failure';
			}
			if($value[1] == 'tel' && !Sanitize::isValidPhone($value[0]) && $value[0] != ''){
				$messages[$name] = array("type"=>'failure', "text"=>'phone is invalid');$type = 'failure';
			}
			$outcome = array("formOutcome" => array("status" => $type, "name" => $name), "messages" => $messages);
		}
		if($type == 'success'){
			$formSubmissions->setFormId($form_id);
			$formSubmissions->setFormData($form->getFormStructure());
			$formSubmissions->setSubmissionData(serialize($form_inputs));
			$formSubmissions->setCreated(date('Y-m-d H:i:s'));
			$formSubmissions->save();
			$formSubmissions->sendEmail();
		}

		echo json_encode($outcome);
        break;
    default: 
		echo json_encode(array('failure'=>'No form action specified.')); 
		exit;
}
?>