<?php
class FormComponent
{
	/*
	* Resources
	* Bootstrap-select: https://silviomoreto.github.io/bootstrap-select/
	* Bootstrap-colorpicker : https://farbelous.github.io/bootstrap-colorpicker/
	*
	**************************************************
	*
	* Table of Conents
	* - selectList()
	* - multipleSelectList()
	* - dropDownList()
	* - listCountries()
	* - listStates()
	* - linkedModule()
	* - dateTimePicker()
	* - colorPicker()
	* - checkBox()
	* - checkBoxList()
	* - radioButton()
	* - radioButtonList()
	* - textareaSmall()
	* - textareaFull()
	* - textarea()
	*/

	/**
	* function selectList()
	*
	* This function generates an HTML form selection box
	* @param string The HTML attribute 'name' of the selection box
	* @param array The values for each selection in the selection box
	* @param array The label for each selection in the selection box
	* @param mixed The selected values of the selection box as an array or as a space-separated string
	* @return string Returns an HTML selection drop down form element
	*/
	public static function selectList($name,$values,$labels,$selected="",$id="",$class="")
	{
		return FormComponent::dropDownList($name, $values, $labels, $selected, $id, $class, 0);
	}

	/**
	* function multipleSelectionList()
	*
	* This function generates an HTML form multiple selection box
	* @param string The HTML attribute 'name' of the multiple selection box
	* @param array The values for each selection in the multiple selection box
	* @param array The label for each selection in the multiple selection box
	* @param mixed The selected values of the multiple selection box as an array or as a space-separated string
	* @return string Returns an HTML multiple selection drop down form element
	*/
	public static function multipleSelectList($name,$values,$labels,$selected="",$id="",$class="",$selectAll=0)
	{
		# Convert selected to array
		$selectedItems = $selected;
		if(!is_array($selectedItems) && strlen(trim($selectedItems))){
			$selectedItems[] = $selectedItems;
		}

		return FormComponent::dropDownList($name.'[]', $values, $labels, $selectedItems, $id, $class, 1, $selectAll);
	}

	/**
	* function dropDownList()
	*
	* This function generates an HTML form drop down box
	* @param string The HTML attribute 'name' of the drop down
	* @param array The values for each selection in the drop down
	* @param array The label for each selection in the drop down
	* @param string The selected value of the drop down
	* @param string The HTML attribute 'id' of the drop down
	* @param bool If true - the form will submit on the onChange DOM event, if false - the form will not submit
	* @return string Returns an HTML drop down form element
	*/
	public static function dropDownList($name,$values,$labels,$selected="",$id="",$class="",$multiple=0,$selectAll=0)
	{
		# Begin Select
		$str = '<select name="'.$name.'" id="'.$id.'" title="Nothing Selected" class="selectpicker '.$class.'" '.(($multiple)? 'multiple="multiple"': '').' '.(($selectAll)? 'data-actions-box="true"': '').'>';

		# Create Empty Option
		//$str .= '<option value="0"></option>';

		# Iterate through option values
		for($i = 0; $i < sizeof($values); $i++){

			# If nested Array - Opt groups
			if(is_array($values[$i])){

				# Label
				$label = (strlen(trim($labels[$i][0])))? $labels[$i][0] : "&nbsp;";

				# Create Opt-group
				$str.= '<optgroup label="'.$label.'">';

				# Iterate through options for this Opt-group
				for($j = 0; $j < sizeof($values[$i][1]); $j++){
					# Label
					$label = (strlen(trim($labels[$i][1][$j])))? $labels[$i][1][$j] : "&nbsp;";

					# Selected
					if(is_array($selected)){
						$selectedClass = (in_array(trim($values[$i][1][$j]), $selected) && $selected != "")? 'selected="selected"' : '';
					}
					else{
						$selectedClass = ($selected == $values[$i][1][$j] && $selected != "")? 'selected="selected"' : '';
					}

					# Create Option
					$str .= '<option value="'.$values[$i][1][$j].'" '.$selectedClass.'>'.$label.'</option>';
				}

				#Close Obt-group
				$str.= '</optgroup>';

			}

			# Else - Regular Dropdown
			else{

				# Label
				$label = (strlen(trim($labels[$i])))? $labels[$i] : "&nbsp;";

				# Selected
				if(is_array($selected)){
					$selectedClass = (in_array(trim($values[$i]), $selected) && $selected != "")? 'selected="selected"' : '';
				}
				else{
					$selectedClass = ($selected == $values[$i] && $selected != "")? 'selected="selected"' : '';
				}

				# Create Option
				$str .= '<option value="'.$values[$i].'" '.$selectedClass.'>'.$label.'</option>';
			}

		}

		# End Select
		$str .= '</select>';

		ob_start(); ?>

		<script type="text/javascript">
			$(function(){
				$('.selectpicker').selectpicker();
			});
		</script>

		<?php
		$str .= ob_get_clean();
		return $str;
	}

	/**
	* function listCountries()
	*
	* This function generates a drop down list of countries
	* @param string The selected country
	* @param string  The name to be appended as "prefix_" to the names of the country drop down
	* @return string Returns a drop down list of countries
	*/
	public static function listCountries($selected="United States",$prefix="")
	{
		if(strlen($prefix)){
			$prefix .= "_";
		}
		$countries = array(
							"Afghanistan" => "Afghanistan",
							"Albania" => "Albania",
							"Algeria" => "Algeria",
							"Andorra" => "Andorra",
							"Angola" => "Angola",
							"Antigua and Barbuda" => "Antigua and Barbuda",
							"Argentina" => "Argentina",
							"Armenia" => "Armenia",
							"Australia" => "Australia",
							"Austria" => "Austria",
							"Azerbaijan" => "Azerbaijan",
							"Bahamas" => "Bahamas",
							"Bahrain" => "Bahrain",
							"Bangladesh" => "Bangladesh",
							"Barbados" => "Barbados",
							"Belarus" => "Belarus",
							"Belgium" => "Belgium",
							"Belize" => "Belize",
							"Benin" => "Benin",
							"Bhutan" => "Bhutan",
							"Bolivia" => "Bolivia",
							"Bosnia and Herzegovina" => "Bosnia and Herzegovina",
							"Botswana" => "Botswana",
							"Brazil" => "Brazil",
							"Brunei" => "Brunei",
							"Bulgaria" => "Bulgaria",
							"Burkina Faso" => "Burkina Faso",
							"Burundi" => "Burundi",
							"Cambodia" => "Cambodia",
							"Cameroon" => "Cameroon",
							"Canada" => "Canada",
							"Cape Verde" => "Cape Verde",
							"Central African Republic" => "Central African Republic",
							"Chad" => "Chad",
							"Chile" => "Chile",
							"China" => "China",
							"Colombia" => "Colombia",
							"Comoros" => "Comoros",
							"Congo (Brazzaville)" => "Congo (Brazzaville)",
							"Congo, Democratic Republic of the" => "Congo, Democratic Republic of the",
							"Costa Rica" => "Costa Rica",
							"Côte d'Ivoire" => "Côte d'Ivoire",
							"Croatia" => "Croatia",
							"Cuba" => "Cuba",
							"Cyprus" => "Cyprus",
							"Czech Republic" => "Czech Republic",
							"Denmark" => "Denmark",
							"Djibouti" => "Djibouti",
							"Dominica" => "Dominica",
							"Dominican Republic" => "Dominican Republic",
							"East Timor (Timor Timur)" => "East Timor (Timor Timur)",
							"Ecuador" => "Ecuador",
							"Egypt" => "Egypt",
							"El Salvador" => "El Salvador",
							"Equatorial Guinea" => "Equatorial Guinea",
							"Eritrea" => "Eritrea",
							"Estonia" => "Estonia",
							"Ethiopia" => "Ethiopia",
							"Fiji" => "Fiji",
							"Finland" => "Finland",
							"France" => "France",
							"Gabon" => "Gabon",
							"Gambia, The" => "Gambia, The",
							"Georgia" => "Georgia",
							"Germany" => "Germany",
							"Ghana" => "Ghana",
							"Greece" => "Greece",
							"Grenada" => "Grenada",
							"Guatemala" => "Guatemala",
							"Guinea" => "Guinea",
							"Guinea-Bissau" => "Guinea-Bissau",
							"Guyana" => "Guyana",
							"Haiti" => "Haiti",
							"Honduras" => "Honduras",
							"Hungary" => "Hungary",
							"Iceland" => "Iceland",
							"India" => "India",
							"Indonesia" => "Indonesia",
							"Iran" => "Iran",
							"Iraq" => "Iraq",
							"Ireland" => "Ireland",
							"Israel" => "Israel",
							"Italy" => "Italy",
							"Jamaica" => "Jamaica",
							"Japan" => "Japan",
							"Jordan" => "Jordan",
							"Kazakhstan" => "Kazakhstan",
							"Kenya" => "Kenya",
							"Kiribati" => "Kiribati",
							"Korea, North" => "Korea, North",
							"Korea, South" => "Korea, South",
							"Kuwait" => "Kuwait",
							"Kyrgyzstan" => "Kyrgyzstan",
							"Laos" => "Laos",
							"Latvia" => "Latvia",
							"Lebanon" => "Lebanon",
							"Lesotho" => "Lesotho",
							"Liberia" => "Liberia",
							"Liechtenstein" => "Liechtenstein",
							"Lithuania" => "Lithuania",
							"Luxembourg" => "Luxembourg",
							"Macedonia, Former Yugoslav Republic of" => "Macedonia, Former Yugoslav Republic of",
							"Madagascar" => "Madagascar",
							"Malawi" => "Malawi",
							"Malaysia" => "Malaysia",
							"Maldives" => "Maldives",
							"Mali" => "Mali",
							"Malta" => "Malta",
							"Marshall Islands" => "Marshall Islands",
							"Mauritania" => "Mauritania",
							"Mauritius" => "Mauritius",
							"Mexico" => "Mexico",
							"Micronesia, Federated States of" => "Micronesia, Federated States of",
							"Moldova" => "Moldova",
							"Monaco" => "Monaco",
							"Mongolia" => "Mongolia",
							"Morocco" => "Morocco",
							"Mozambique" => "Mozambique",
							"Myanmar (Burma)" => "Myanmar (Burma)",
							"Namibia" => "Namibia",
							"Nauru" => "Nauru",
							"Nepal" => "Nepal",
							"Netherlands" => "Netherlands",
							"New Zealand" => "New Zealand",
							"Nicaragua" => "Nicaragua",
							"Niger" => "Niger",
							"Nigeria" => "Nigeria",
							"Norway" => "Norway",
							"Oman" => "Oman",
							"Pakistan" => "Pakistan",
							"Palau" => "Palau",
							"Panama" => "Panama",
							"Papua New Guinea" => "Papua New Guinea",
							"Paraguay" => "Paraguay",
							"Peru" => "Peru",
							"Philippines" => "Philippines",
							"Poland" => "Poland",
							"Portugal" => "Portugal",
							"Qatar" => "Qatar",
							"Romania" => "Romania",
							"Russia" => "Russia",
							"Rwanda" => "Rwanda",
							"Saint Kitts and Nevis" => "Saint Kitts and Nevis",
							"Saint Lucia" => "Saint Lucia",
							"Saint Vincent and The Grenadines" => "Saint Vincent and The Grenadines",
							"Samoa" => "Samoa",
							"San Marino" => "San Marino",
							"Sao Tome and Principe" => "Sao Tome and Principe",
							"Saudi Arabia" => "Saudi Arabia",
							"Senegal" => "Senegal",
							"Serbia and Montenegro" => "Serbia and Montenegro",
							"Seychelles" => "Seychelles",
							"Sierra Leone" => "Sierra Leone",
							"Singapore" => "Singapore",
							"Slovakia" => "Slovakia",
							"Slovenia" => "Slovenia",
							"Solomon Islands" => "Solomon Islands",
							"Somalia" => "Somalia",
							"South Africa" => "South Africa",
							"Spain" => "Spain",
							"Sri Lanka" => "Sri Lanka",
							"Sudan" => "Sudan",
							"Suriname" => "Suriname",
							"Swaziland" => "Swaziland",
							"Sweden" => "Sweden",
							"Switzerland" => "Switzerland",
							"Syria" => "Syria",
							"Taiwan" => "Taiwan",
							"Tajikistan" => "Tajikistan",
							"Tanzania" => "Tanzania",
							"Thailand" => "Thailand",
							"Togo" => "Togo",
							"Tonga" => "Tonga",
							"Trinidad and Tobago" => "Trinidad and Tobago",
							"Tunisia" => "Tunisia",
							"Turkey" => "Turkey",
							"Turkmenistan" => "Turkmenistan",
							"Tuvalu" => "Tuvalu",
							"Uganda" => "Uganda",
							"Ukraine" => "Ukraine",
							"United Arab Emirates" => "United Arab Emirates",
							"United Kingdom" => "United Kingdom",
							"United States" => "United States",
							"Uruguay" => "Uruguay",
							"Uzbekistan" => "Uzbekistan",
							"Vanuatu" => "Vanuatu",
							"Vatican City" => "Vatican City",
							"Venezuela" => "Venezuela",
							"Vietnam" => "Vietnam",
							"Western Sahara" => "Western Sahara",
							"Yemen" => "Yemen",
							"Zambia" => "Zambia",
							"Zimbabwe" => "Zimbabwe"
		);

		foreach($countries as $value => $label){
			$values[] = $value;
			$labels[] = $label;
		}

		return FormComponent::selectList($prefix."country",$values,$labels,$selected,$prefix."country");
	}

	/**
	* function listStates()
	*
	* This function generates a drop down list of states
	* @param string The selected state
	* @param string  The name to be appended as "prefix_" to the names of the state drop down
	* @return string Returns a drop down list of states
	*/
	public static function listStates($selected="TX",$prefix="")
	{
		if(strlen($prefix)){
			$prefix .= "_";
		}
		$states = array(
						"AL" => "Alabama",
						"AK" => "Alaska",
						"AZ" => "Arizona",
						"AR" => "Arkansas",
						"CA" => "California",
						"CO" => "Colorado",
						"CT" => "Connecticut",
						"DE" => "Delaware",
						"DC" => "District of Columbia",
						"FL" => "Florida",
						"GA" => "Georgia",
						"HI" => "Hawaii",
						"ID" => "Idaho",
						"IL" => "Illinois",
						"IN" => "Indiana",
						"IA" => "Iowa",
						"KS" => "Kansas",
						"KY" => "Kentucky",
						"LA" => "Louisiana",
						"ME" => "Maine",
						"MD" => "Maryland",
						"MA" => "Massachusetts",
						"MI" => "Michigan",
						"MN" => "Minnesota",
						"MS" => "Mississippi",
						"MO" => "Missouri",
						"MT" => "Montana",
						"NE" => "Nebraska",
						"NV" => "Nevada",
						"NH" => "New Hampshire",
						"NJ" => "New Jersey",
						"NM" => "New Mexico",
						"NY" => "New York",
						"NC" => "North Carolina",
						"ND" => "North Dakota",
						"OH" => "Ohio",
						"OK" => "Oklahoma",
						"OR" => "Oregon",
						"PA" => "Pennsylvania",
						"PR" => "Puerto Rico",
						"RI" => "Rhode Island",
						"SC" => "South Carolina",
						"SD" => "South Dakota",
						"TN" => "Tennessee",
						"TX" => "Texas",
						"UT" => "Utah",
						"VT" => "Vermont",
						"VA" => "Virginia",
						"WA" => "Washington",
						"WV" => "West Virginia",
						"WI" => "Wisconsin",
						"WY" => "Wyoming"
		);

		foreach($states as $value => $label){
			$values[] = $value;
			$labels[] = $label;
		}
		return FormComponent::selectList($prefix."state",$values,$labels,$selected,$prefix."state");
	}

	/**
	* function linkedModule()
	*
	* This function generates an HTML form drop down or multiple selection box from data in the specified table
 	* If a related category table is also specified, the options within the select element will be divided into optgroups according to the categories
	* @param string The name of the linked class
	* @param string The name of the form element
	* @param mixed values of selected options
	* @param int if 1 multiselect drop down is built. if 0 single select
	* @param string optional id value of the form element
	* @param string The name of the field that will be accessed for values of options
	* @param string The name of the field that will be accessed for labels of options
	* @param string SQL WHERE and/or ORDER BY clause to filter linked items
	* @param string The name of the field that will be accessed for option group labels of
	* @param string SQL WHERE and/or ORDER BY clause to filter option group items
	*/
	public static function linkedModule(
										$linkedClassName,
										$name,
										$selectedValues = "",
										$multiSelect = 0,
										$id = "",
										$linkedValueField = "id",
										$linkedLabelField = "name",
										$linkedItemsFilter = "ORDER BY `sort_order`",
										$linkedCategoryLabelField = "name",
										$linkedItemsCategoryFilter = "ORDER BY `sort_order`"
									   )
	{

		$values = array();
		$labels = array();

		# Getter for Value
		$valueGetter = "get".ucwords(str_replace("_", " ", strtolower($linkedValueField)));

		# Getter for Label
		$labelGetter = "get".ucwords(str_replace("_", " ", strtolower($linkedLabelField)));

		# Get Eligible items to link
		$linkedItem = new $linkedClassName();

		# If linked items have Categories
		if(property_exists($linkedItem, "_moduleCategoryClassName") && trim($linkedItem->_moduleCategoryClassName) != ""){

			# Get category name
			$linkedItemCategoryName = $linkedItem->_moduleCategoryClassName;

			# Get all categories
			$linkedItemCategories = new $linkedItemCategoryName();
			$linkedItemCategories = $linkedItemCategories->fetchAll($linkedItemsCategoryFilter);

			# Getter for Label
			$categoryLabelGetter = "get".ucwords(str_replace("_", " ", strtolower($linkedCategoryLabelField)));

			# Iterate through categories
			foreach($linkedItemCategories as $linkedItemCategory){

				# Label for Option groups
				$label[0] = $linkedItemCategory->$categoryLabelGetter();
				$value[0] = "";

				# Children items
				$items = $linkedItemCategory->getRecords();

				# Iterate through children items
				foreach($items as $item){
					$label[1][] = $item->$labelGetter();
					$value[1][] = $item->$valueGetter();
				}

				$labels[] = $label;
				$label = array();
				$values[] = $value;
				$value = array();
			}
		}

		# linked items do not have categories
		else{

			# Get all linked items
			$linkedItems = $linkedItem->fetchAll($linkedItemsFilter);

			# Iterate through children items
			foreach($linkedItems as $linkedItem){
				$labels[] = $linkedItem->$labelGetter();
				$values[] = $linkedItem->$valueGetter();
			}

		}


		if($multiSelect){
			return FormComponent::multipleSelectList($name,$values,$labels,$selectedValues,$id);
		}
		else{
			return FormComponent::selectList($name,$values,$labels,$selectedValues,$id);
		}
	}

	/**
	* function dateTimePicker()
	*
	* This function generates bootstrap datepicker dropdowns for months,days, and years
	* @param array The data in array format matching any configuration Bootstrap 3 Datepicker accepts
	* @return date input
	*/
	public static function dateTimePicker($data= array())
	{
		ob_start();
		//add defaults as we need them for modules.
		$name = 'date';
		$id = rand();
		$format = 'false';
		$disabled = 0;

		extract($data);

		if($defaultDate == ''){
			$defaultDate = date('m/d/Y', time());
		}
		else{
			// String to time in milliseconds
			$date = strtotime($defaultDate);

			// If $date is less than 0
			if($date < 0){
				$date = 0;
			}
			// Otherwise, format string
			else{
				// If user specifies time
				if(strpos($format, 'LT')){
					// format to 00/00/0000 00:00 XM
					$date = date('Y-m-d H:i:s',$date);
					$date = date(DATE_ISO8601, strtotime($date));

				}
				else{
					// format to 00/00/0000
					$date = date('Y-m-d',$date);
				}
			}
		}

		?>
		<div class='input-group date' id='datetimepicker_<?php echo $id; ?>'>
			<input type='text' class="form-control" name="<?php echo $name ?>" <?php echo ($disabled)? 'disabled' : ''; ?> />
			<span class="input-group-addon">
				<?php
				if($format == 'LT'){ ?>
				<span class="fa fa-clock-o"></span>
				<?php
				}
				else{ ?>
				<span class="fa fa-calendar"></span>
				<?php
				} ?>
			</span>
		</div>
		<script type="text/javascript">
			$(function () {
				$('#datetimepicker_<?php echo $id; ?>').datetimepicker({
					format: '<?php echo $format; ?>',
					<?php if($date != 0){ ?>
					defaultDate: '<?php echo $date; ?>',
					<?php }?>
				});
			});
		</script>
		<?php return ob_get_clean();
	}

	/**
	* function colorPicker()
	*
	* This function generates bootstrap colorpicker
	* @param array The data in array format matching any configuration Bootstrap 3 Datepicker accepts
	* @return date input
	*/
	public static function colorPicker($data= array())
	{
		ob_start();
		//add defaults as we need them for modules.
		$name = 'color';
		$id = rand();
		$format = '';
		$defaultColor = '#000000';
		$disabled = 0;

		extract($data);

		?>
		<div id="colorpicker_<?php echo $id; ?>" class="input-group colorpicker-component">
			<input type="text" name="<?php echo $name; ?>" value="<?php echo $defaultColor; ?>" class="form-control" />
			<span class="input-group-addon"><i></i></span>
		</div>
		<script>
			$(function() {
				$('#colorpicker_<?php echo $id; ?>').colorpicker({
					customClass: 'colorpicker-2x',
					sliders: {
						saturation: {
							maxLeft: 200,
							maxTop: 200
						},
						hue: {
							maxTop: 200
						},
						alpha: {
							maxTop: 200
						}
					},
					<?php if($format != ''){ ?>
					format: '<?php echo $format; ?>',
					<?php }?>
				});
			});
		</script>
		<?php return ob_get_clean();
	}

	/**
	* function checkBox()
	*
	* This function generates an HTML form checkbox
	* @param string The HTML attribute 'name' of the checkbox
	* @param string The HTML attribute 'value' of the checkbox
	* @param string The selected value of the checkbox
	* @param string The HTML attribute 'id' of the checkbox
	* @param string The HTML attribute 'label' of the checkbox
	* @param string The HTML attribute 'instructions' of the checkbox
	* @param string The HTML attribute 'class' of the checkbox
	* @return string Returns an HTML checkbox form element
	*/
	public static function checkBox($name,$value,$selected="",$id="",$label="",$class="")
	{
		if(strlen(trim($id))){
			$id_html = ' id="'.$id.'" ';
		}else{
			$id_html = ' ';
		}

		if($value == $selected){
			$selected_html = ' checked="checked" ';
		}else{
			$selected_html = ' ';
		}
		ob_start(); ?>
        <div class="checkbox <?php echo $class; ?>">
            <label for="<?php echo $id; ?>">
                <input type="hidden" name="<?php echo $name; ?>" value="0" />
                <input type="checkbox" id="<?php echo $id; ?>" name="<?php echo $name; ?>" value="<?php echo $value; ?>"  <?php echo $selected_html; ?> />
                <span class="cr"><em class="cr-icon fa fa-check"></em></span>
                <?php echo $label; ?>
            </label>
        </div>
		<?php
		return ob_get_clean();
	}

	/**
	* function checkBoxList()
	*
	* This function generates a series of HTML for checkboxes
	* @param string The HTML attribute 'name' of the checkbox array
	* @param array The values for each checkbox in the list
	* @param array The label for each checkbox in the list
	* @param string The selected checkbox values separated by "\n"
	* @return string Returns a series of HTML checkbox elements
	*/
	function checkBoxList($name,$values,$labels,$selected="",$ids)
	{
		$name = $name.'[]';

		$str = '<div class="checkbox-list" >';

		for($i = 0; $i < sizeof($values); $i++){
			if(in_array($values[$i],$selected)){
				$str .= FormComponent::checkBox($name, $values[$i], $values[$i], $ids[$i], $labels[$i]);
			}else{
				$str .= FormComponent::checkBox($name, $values[$i], "", $ids[$i], $labels[$i]);
			}
		}

		$str .= '</div>';

		return $str;
	}

	/**
	* function radioButton()
	*
	* This function generates an HTML for a radio button
	* @param string The HTML attribute 'name' of the radio button
	* @param string The HTML attribute 'value' of the radio button
	* @param string The selected value of the radio button
	* @param string The HTML attribute 'id' of the radio button
	* @param string The HTML attribute 'label' of the radio button
	* @param string The HTML attribute 'instructions' of the radio button
	* @param string The HTML attribute 'class' of the radio button
	* @return string Returns an HTML radio button form element
	*/
	public static function radioButton($name,$value,$selected="",$id="",$label="",$class="")
	{
		if(strlen(trim($id))){
			$id_html = ' id="'.$id.'" ';
		}else{
			$id_html = ' ';
		}

		if($value == $selected){
			$selected_html = ' checked="checked" ';
		}else{
			$selected_html = ' ';
		}
		ob_start(); ?>
			<div class="radio <?php echo $class; ?>">
				<label for="<?php echo $id; ?>">
					<input type="radio" id="<?php echo $id; ?>" name="<?php echo $name; ?>" value="<?php echo $value; ?>" <?php echo $selected_html; ?> />
					<span class="cr"><i class="cr-icon fa fa-circle"></i></span>
					<?php echo $label; ?>
				</label>
			</div>
		<?php
		return ob_get_clean();
	}

	/**
	* function radioButtonList()
	*
	* This function generates a series of HTML form checkboxes
	* @param string The HTML attribute 'name' of the checkbox array
	* @param array The values for each checkbox in the list
	* @param array The label for each checkbox in the list
	* @param string The selected checkbox values separated by "\n"
	* @return string Returns a series of HTML checkbox elements
	*/
	function radioButtonList($name,$values,$labels,$selected="",$ids)
	{
		$name = $name;

		$str = '<div class="radioButton-list" >';

		for($i = 0; $i < sizeof($values); $i++){
			if($values[$i] == $selected){
				$str .= FormComponent::radioButton($name, $values[$i], $values[$i], $ids[$i], $labels[$i]);
			}else{
				$str .= FormComponent::radioButton($name, $values[$i], "", $ids[$i], $labels[$i]);
			}
		}

		$str .= '</div>';

		return $str;
	}

	/**
	* function textareaSmall()
	*
	* @param string The HTML attribute 'name' of the textarea
	* @param string The value for the textarea
	* @return string Returns a textarea
	*/
	public static function textareaSmall($name,$value,$id="",$class="")
	{
		return FormComponent::textarea($name, $value, $id, $class." tiny-mce-small");
	}

	/**
	* function textareaFull()
	*
	* @param string The HTML attribute 'name' of the textarea
	* @param string The value for the textarea
	* @return string Returns a textarea
	*/
	public static function textareaFull($name,$value,$id="",$class="")
	{
		return FormComponent::textarea($name, $value, $id, $class." tiny-mce-full");
	}

	/**
	* function textarea()
	*
	* @param string The HTML attribute 'name' of the textarea
	* @param string The value for the textarea
	* @return string Returns a textarea
	*/
	public static function textarea($name,$value,$id="",$class="")
	{
		$component = '<textarea id="'.$id.'" name="'.$name.'" class="tiny-mce '.$class.'">'.$value.'</textarea>';

		ob_start();
		?>

		<script type="text/javascript">
			$(function(){
				var id = "<?php echo $id; ?>";
				tinymce.execCommand('mceRemoveEditor', false, id);

				<?php
				if(preg_match('/tiny-mce-small/', $class)){ ?>
				tinyMceSmall(id);
				<?php
				}
				else if(preg_match('/tiny-mce-full/', $class)){ ?>
				tinyMceFull(id);
				<?php
				}
				else { ?>
				tinyMce(id);
				<?php
				} ?>
			})
		</script>

		<?php
		$component .= ob_get_clean();

		return $component;
	}
}
?>
