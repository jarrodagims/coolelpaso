<?php
# Debugging
//ini_set('display_errors','1');
//ini_set("error_reporting", E_ALL);

include_once("../includes/library.php");

$GLOBALS['page_title'] = "Form Components";

ob_start();
?>

<table class="table table-bordered table-hover">
	<thead>
		<tr>
			<td width="40%">Name</td>
			<td>Component</td>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>
				<strong>Select List</strong> <br>
				selectList()
			</td>
			<td>
				<?php
					$name = 'name';
					$values = [1, 2, 3, 4, 5];
					$labels = ['one', 'two', 'three', 'four', 'five'];
					$selected = 4;
					$id = 'id';
					$class = 'class';
					echo FormComponent::selectList($name,$values,$labels,$selected,$id,$class);
				?>
			</td>
		</tr>
		<tr>
			<td>
				<strong>Select List with Option Groups</strong> <br>
				selectList()
			</td>
			<td>
				<?php
					$name = 'name';
					$values = [["1", ["1.0", "1.1", "1.2"]], ["2", ["2.0", "2.1"]], ["3", ["3.0", "3.1", "3.2", "3.3"]], ["4", ["4.0", "4.1", "4.2"]], ["5", ["5.0"]]];
					$labels = [["Section 1", ["Option 1.0", "Option 1.1", "Option 1.2"]], ["Section 2", ["Option 2.0", "Option 2.1"]], ["Section 3", ["Option 3.0", "Option 3.1", "Option 3.2", "Option 3.3"]], ["Section 4", ["Option 4.0", "Option 4.1", "Option 4.2"]], ["Section 5", ["Option 5.0"]]];
					$selected = "2.1";
					$id = 'id';
					$class = 'class';
					echo FormComponent::selectList($name,$values,$labels,$selected,$id,$class);
				?>
			</td>
		</tr>
		<tr>
			<td>
				<strong>Multi-Select List</strong> <br>
				multipleSelectList()
			</td>
			<td>
				<?php
					$name = 'name';
					$values = [1, 2, 3, 4, 5];
					$labels = ['one', 'two', 'three', 'four', 'five'];
					$selected = [4, 2];
					$id = 'id';
					$class = 'class';
					echo FormComponent::multipleSelectList($name,$values,$labels,$selected,$id,$class);
				?>
			</td>
		</tr>
		<tr>
			<td>
				<strong>Multi-Select List with Option Groups</strong> <br>
				multipleSelectList()
			</td>
			<td>
				<?php
					$name = 'name';
					$values = [["1", ["1.0", "1.1", "1.2"]], ["2", ["2.0", "2.1"]], ["3", ["3.0", "3.1", "3.2", "3.3"]], ["4", ["4.0", "4.1", "4.2"]], ["5", ["5.0"]]];
					$labels = [["Section 1", ["Option 1.0", "Option 1.1", "Option 1.2"]], ["Section 2", ["Option 2.0", "Option 2.1"]], ["Section 3", ["Option 3.0", "Option 3.1", "Option 3.2", "Option 3.3"]], ["Section 4", ["Option 4.0", "Option 4.1", "Option 4.2"]], ["Section 5", ["Option 5.0"]]];
					$selected = ["2.1", "3.3", "1.0"];
					$id = 'id';
					$class = 'class';
					echo FormComponent::multipleSelectList($name,$values,$labels,$selected,$id,$class);
				?>
			</td>
		</tr>
		<tr>
			<td>
				<strong>Multi-Select List with Select All Button</strong><br>
				multipleSelectList()
			</td>
			<td>
				<?php
					$name = 'name';
					$values = [1, 2, 3, 4, 5];
					$labels = ['one', 'two', 'three', 'four', 'five'];
					$selected = [4, 2];
					$id = 'id';
					$class = 'class';
					echo FormComponent::multipleSelectList($name,$values,$labels,$selected,$id,$class,1);
				?>
			</td>
		</tr>
		<tr>
			<td>
				<strong>Countries List</strong> <br>
				listCountries()
			</td>
			<td>
				<?php echo FormComponent::listCountries(); ?>
			</td>
		</tr>
		<tr>
			<td>
				<strong>States List</strong> <br>
				listStates()
			</td>
			<td>
				<?php echo FormComponent::listStates(); ?>
			</td>
		</tr>
		<tr>
			<td>
				<strong>Linked Module</strong> <br>
				linkedModule()
			</td>
			<td>
				See <a href="/admin/modules/linked_html_pages/">Linked HTML Pages</a> for examples
			</td>
		</tr>
		<tr>
			<td>
				<strong>Date Picker</strong> <br>
				dateTimePicker()
			</td>
			<td>
				<?php
					$format = 'MM/DD/YYYY';
					$name = 'name';
					$defaultDate = currentDate();
					echo FormComponent::dateTimePicker(array ('format'=>$format, 'name'=>$name, 'defaultDate'=>$defaultDate));
				?>
			</td>
		</tr>
		<tr>
			<td>
				<strong>Date &amp; Time Picker</strong> <br>
				dateTimePicker()
			</td>
			<td>
				<?php
					$format = 'MM/DD/YYYY LT';
					$name = 'name';
					$defaultDate = currentDate().''.currentTime();
					echo FormComponent::dateTimePicker(array ('format'=>$format, 'name'=>$name, 'defaultDate'=>$defaultDate));
				?>
			</td>
		</tr>
		<tr>
			<td>
				<strong>Color Picker</strong> <br>
				colorPicker()
			</td>
			<td>
				<?php
					$format = 'hex';
					$name = 'name';
					$defaultColor = '#000000';
					echo FormComponent::colorPicker(array ('format'=>$format, 'name'=>$name, 'defaultColor'=>$defaultColor));
				?>
			</td>
		</tr>
		<tr>
			<td>
				<strong>Check Box</strong> <br>
				checkBox()
			</td>
			<td>
				<?php
					$name = 'name';
					$value = "1";
					$label = "one";
					$selected = 1;
					$id = 'id';
					echo FormComponent::checkBox($name,$value,$selected,$id,$label);
				?>
			</td>
		</tr>
		<tr>
			<td>
				<strong>Check Box List</strong> <br>
				checkBoxList()
			</td>
			<td>
				<?php
					$name = 'name';
					$values = [1, 2, 3, 4, 5];
					$labels = ['one', 'two', 'three', 'four', 'five'];
					$selected = [1, 4, 2];
					$id = 'id';
					echo FormComponent::checkBoxList($name,$values,$labels,$selected);
				?>
			</td>
		</tr>
		<tr>
			<td>
				<strong>Radio Button</strong> <br>
				radioButton()
			</td>
			<td>
				<?php
					$name = 'name';
					$value = "1";
					$label = "one";
					$selected = 1;
					$id = 'id';
					echo FormComponent::radioButton($name,$value,$selected,$id,$label);
				?>
			</td>
		</tr>
		<tr>
			<td>
				<strong>Radio Button List</strong> <br>
				radioButtonList()
			</td>
			<td>
				<?php
					$name = 'name';
					$values = [1, 2, 3, 4, 5];
					$labels = ['one', 'two', 'three', 'four', 'five'];
					$selected = 2;
					$id = 'id';
					echo FormComponent::radioButtonList($name,$values,$labels,$selected);
				?>
			</td>
		</tr>
		<tr>
			<td>
				<strong>Text Area - Small</strong> <br>
				textareaSmall()
			</td>
			<td>
				<?php
					$name = 'name';
					$value = "This is a Small Text Area";
					$id = 'tinymcesmall';
					$class = 'class';
					echo FormComponent::textareaSmall($name,$value,$id,$class);
				?>
			</td>
		</tr>
		<tr>
			<td>
				<strong>Text Area - Full</strong> <br>
				textareaFull()
			</td>
			<td>
				<?php
					$name = 'name';
					$value = "This is a Full Text Area";
					$id = 'tinymcefull';
					$class = 'class';
					echo FormComponent::textareaFull($name,$value,$id,$class);
				?>
			</td>
		</tr>
		<tr>
			<td>
				<strong>Text Area - Normal</strong> <br>
				textarea()
			</td>
			<td>
				<?php
					$name = 'name';
					$value = "This is a Full Text Area";
					$id = 'tinymce';
					$class = 'class';
					echo FormComponent::textarea($name,$value,$id,$class);
				?>
			</td>
		</tr>
	</tbody>
</table>

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

include_once("../template.php");
?>
