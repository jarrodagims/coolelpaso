/**
* function show_hide
* Toggles the visibility of the specified element
* Inputs: id - HTML Document id value e.g. id="element1"
*/
function show_hide(id){
	var visible = document.getElementById(id).style.display;
	if(visible == 'none'){
		show(id);
	}else{
		hide(id);
	}
}

function show(id){
	document.getElementById(id).style.display='block';
}

function hide(id){
	document.getElementById(id).style.display='none';
}
/*************************************************************/
/**
* function appendValue
* Appends the entered value to the specified element
* Inputs: id - HTML Document id value e.g. id="element1", value - the value to be appended
*/
function appendValue(id,value){
	document.getElementById(id).value = document.getElementById(id).value + value;
}
/**
* function showContent
* Shows the entered content as the Inner HTML of the specified element
* Inputs: id - HTML Document id value e.g. id="element1", content - the content to be displayed
* Notes: Javascript required specific formatting of the content string
* e.g - "<table width=\'100%\' cellpadding=\'3\' cellspacing=\'0\'><tr><td></td></table>"
* escape single quotes ('), and do not put hard line breaks in the content string
* e.g. "<table width=\'100%\' cellpadding=\'3\' cellspacing=\'0\'>
			<tr>
				<td></td>
			</tr>
		</table>"
	*** This will not work ***
			
*/
function showContent(id,content){
	document.getElementById(id).style.display='block';
	document.getElementById(id).innerHTML = content;
}
/**
* function goToURL
* Redirects to the specified URL
* Inputs: url - the URL to redirect to
*/
function goToURL(url){
	window.location.href = url;
}
/**
* function changeBorderBorder
* Changes the borderColor of the specified element
* Inputs: id - HTML Document id value e.g. id="element1", color - the new color
*/
function changeBorderColor(id,color){
	document.getElementById(id).style.borderColor = color;
}
/**
* function changeFontColor
* Changes the font color of the specified element
* Inputs: id - HTML Document id value e.g. id="element1", color - the new color
*/
function changeFontColor(id,color){
	document.getElementById(id).style.color = color;
}
/**
* function changeBGColor
* Changes the background of the specified element
* Inputs: id - HTML Document id value e.g. id="element1", color - the new color
*/
function changeBGColor(id,color){
	document.getElementById(id).style.background = color;
}
/**
* function changeInnerHTML
* Toggles between the two specified html strings
* Inputs: id - HTML Document id value e.g. id="element1", html, html2 - HTML content to toggle between
*/
function changeInnerHTML(id,html,html2){
	var current_html = document.getElementById(id).innerHTML;
	current_html = replaceString(current_html,'"','\'');
	current_html = trimString(current_html);
	
	if(current_html == html){
		document.getElementById(id).innerHTML = html2;
	}else{
		document.getElementById(id).innerHTML = html;
	}
}
/**
* function replaceString
* Replaces matched needle in the haystack, on the entered string
* Inputs: string - the string to do the replacing on, haystack - the value to be replaced, needle - the new value
*/
function replaceString(string,haystack,needle) {
	var strLength = string.length, txtLength = haystack.length;
    if ((strLength == 0) || (txtLength == 0)) return string;

    var i = string.indexOf(haystack);
    if ((!i) && (text != string.substring(0,txtLength))) return string;
    if (i == -1) return string;

    var newstr = string.substring(0,i) + needle;

    if (i+txtLength < strLength)
        newstr += replaceString(string.substring(i+txtLength,strLength),haystack,needle);

    return newstr;
}
/**
* function replaceString
* Removes leading and trailing white space
* Inputs: str - the string to trim
*/
function trimString (str) {
  str = this != window? this : str;
  return str.replace(/^\s+/g, '').replace(/\s+$/g, '');
}

/**
 * function copyFields
 * Copies one set of field values to its mapped field
 */
function copyFields(field_map){
	for(var i = 0; i < field_map.length; i++){ 
		field_pair = field_map[i];
		pair = field_pair.split("->");
		new_value = "";
		// Handle pair values that are the combination of two or more fields
		if(pair[0].indexOf("&")){
			multiple_pairs = pair[0].split("&");
			for(var j = 0; j < multiple_pairs.length; j++){
				new_value += document.getElementById(multiple_pairs[j]).value + " ";	
			}
		}else{
			new_value = document.getElementById(pair[0]).value;	
		}
		new_value = trimString(new_value);
		//document.getElementById(pair[1]).value = document.getElementById(pair[0]).value;
		document.getElementById(pair[1]).value = new_value;
	}
	
}

/**
 * function updateDateDropDown()
 *
 */
function updateDateDropDown(calendar){
	//var source = calendar.params.inputField.id;
	date = document.getElementById(calendar).value;
	//
	if(date == ''){
	}else{
		var target_array = calendar.split("_");
		if(target_array[2] == null){
			target_year = "year";
		}else{
			target_year = target_array[0] + "_year";	
		}
		if(target_array[2] == null){
			target_month = "month";
		}else{
			target_month = target_array[0] + "_month";	
		}
		if(target_array[2] == null){
			target_day = "day";
		}else{
			target_day = target_array[0] + "_day";	
		}
		//
		var date_array = date.split("-");
		document.getElementById(target_year).value = date_array[0];
		document.getElementById(target_month).value = parseInt(parseFloat(date_array[1]));
		document.getElementById(target_day).value = date_array[2];
	}
}

/**
 * function updateCalendarSelector()
 *
 */
function updateCalendarSelector(prefix)
{
	if(prefix != ""){
		prefix += "_";
	}
	var year = document.getElementById(prefix+"year").value;
	var month = document.getElementById(prefix+"month").value.replace(/^(\d)$/,"0$1");
	var day = document.getElementById(prefix+"day").value.replace(/^(\d)$/,"0$1");
	
	if(year == "" || month == "" || day == ""){
		document.getElementById(prefix+"date_holder").value = "";
	}else{
		document.getElementById(prefix+"date_holder").value = year+"-"+month+"-"+day;
	}
}

/**
 * function confirmDelete()
 *
 */
function confirmDelete() {
  
  var agree = confirm('Delete this record?');
  
  if (agree == true) { return true; } else { return false; }

}