//<?php//
/**
 * ajax.js - AJAX Callback functions
 *
 * This file contains AJAX Callback functions
 *
 * @filesource
 */
 
/**
 * function refresh_drop_down_cb()
 *
 * This functions refreshes the the specified drop down
 *
 * - drop_down HTML markup of the new drop down
 * - id HTML element id in which the drop_down resides
 * - opener Boolean, true - if the drop_down exists in the opening window, false - if the drop_down exists in the current window
 * @param array Ordered as drop_down, id, opener
 * @see refreshDropDown()
 */
function refresh_drop_down_cb(args){
	var drop_down = args[0];
	var id = args[1];
	var opener = args[2];
	
	if(opener == 'true'){
		window.opener.document.getElementById(id).innerHTML = drop_down;
	}else{
		document.getElementById(id).innerHTML = drop_down;
	}
}

/**
 * function inline_module_form()
 *
 * This function displays an inline form field for adding or editing an element
 *
 * @param string ID of the HTML element that is being affected by the adding/editing action
 * @param string Name of the module that the inline module is to affect
 * @param string The value of the action as either 'add','edit', or 'delete'
 * @see inlineModuleForm()
 */
function inline_module_form(name,module,action){
	show_progress(module);
	//
	selected_id = document.getElementById(name).value;
	//
	x_inlineModuleForm(module,selected_id,action,inline_module_form_cb);
}

/**
 * function inline_module_form_cb()
 *
 * This function replaces the HTML of the element specified as the placeholder of the inline module
 *
 * @param array An array return from inlineModuleForm(). 
 * [0] ID of the HTML element that is being affected by the adding/editing action
 * [1] Name of the module that the inline module is to affect
 * [2] The new HTML
 * @see inlineModuleForm()
 */
function inline_module_form_cb(args){
	name = args[0];
	module = args[1];
	html = args[2];
	//
	if(html == 'delete'){
		x_saveInlineModuleForm(module,'','','delete',save_inline_module_form_cb);
	}else{
		document.getElementById(module + '_div_in_place').innerHTML = html;
	}
}

/**
 * function save_inline_module_form()
 *
 * This function saves the added/edited value from the inline module
 *
 * @param string ID of the HTML element that is being affected by the adding/editing action
 * @param string Name of the module that the inline module is to affect
 * @param integer The ID value of the selected item being edited
 * @param string The value of the action as either 'add','edit', or 'delete'
 * @see saveInlineModuleForm()
 */
function save_inline_module_form(name,module,selected_id,value,action){
	show_progress(module);
	//
	x_saveInlineModuleForm(module,selected_id,value,action,save_inline_module_form_cb);
}

/**
 * function save_inline_module_form_cb()
 *
 * This function displays the HTML of the updated inline module
 *
 * @param array An array return from saveInlineModuleForm(). 
 * [0] ID of the HTML element that is being affected by the adding/editing action
 * [1] Name of the module that the inline module is to affect
 * [2] The new HTML
 * [3] The status of the save operation
 * @see saveInlineModuleForm()
 */
function save_inline_module_form_cb(args){
	name = args[0];
	module = args[1];
	html = args[2];
	status = args[3];
	//
	document.getElementById(module + '_div').innerHTML = html;
	//
	hide_progress(module);
}

/**
 * function show_progress()
 *
 * This function displays the progress animation gif in the specified HTML element
 * @param string Name of the module that the progress animation is for
 */
function show_progress(module){
	document.getElementById(module + '_div_in_place').innerHTML = '<img src="/admin/images/progress.gif" />';	
}

/**
 * function hide_progress()
 *
 * This function hides the progress animation gif in the specified HTML element
 * @param string Name of the module that the progress animation is for
 */
function hide_progress(module){
	document.getElementById(module + '_div_in_place').innerHTML = '';	
}

function add_document(){
	// Get the last row
	var tbl = document.getElementById('document_table');
	lastRow = tbl.rows.length;
	var finalRow = tbl.rows[lastRow - 1];
	finalRow_id = finalRow.getAttribute('id');
	finalRow_array = finalRow_id.split("_");
	lastRow_id = finalRow_array[1];
	nextRow_id = parseInt(lastRow_id) + 1;
	//
	x_addDocument(lastRow_id,add_document_cb);	
}

function add_document_cb(html){
	var tbl = document.getElementById('document_table');
	lastRow = tbl.rows.length;
	// Get the last row
	var finalRow = tbl.rows[lastRow - 1];
	finalRow_id = finalRow.getAttribute('id');
	finalRow_array = finalRow_id.split("_");
	lastRow_id = finalRow_array[1];
	nextRow_id = parseInt(lastRow_id) + 1;
	//
	var row = tbl.insertRow(lastRow);
	var td = document.createElement("td");
	
	document.getElementById('document_' + lastRow_id).appendChild(td);
	document.getElementById('document_' + lastRow_id).firstChild.innerHTML = html;
	row.setAttribute('id', 'document_' + nextRow_id);
}

function add_image(){
	// Get the last row
	var tbl = document.getElementById('image_table');
	lastRow = tbl.rows.length;
	var finalRow = tbl.rows[lastRow - 1];
	finalRow_id = finalRow.getAttribute('id');
	finalRow_array = finalRow_id.split("_");
	lastRow_id = finalRow_array[1];
	nextRow_id = parseInt(lastRow_id) + 1;
	//
	x_addImage(lastRow_id,add_image_cb);	
}

function add_image_cb(html){
	var tbl = document.getElementById('image_table');
	lastRow = tbl.rows.length;
	// Get the last row
	var finalRow = tbl.rows[lastRow - 1];
	finalRow_id = finalRow.getAttribute('id');
	finalRow_array = finalRow_id.split("_");
	lastRow_id = finalRow_array[1];
	nextRow_id = parseInt(lastRow_id) + 1;
	//
	var row = tbl.insertRow(lastRow);
	var td = document.createElement("td");
	
	document.getElementById('image_' + lastRow_id).appendChild(td);
	document.getElementById('image_' + lastRow_id).firstChild.innerHTML = html;
	row.setAttribute('id', 'image_' + nextRow_id);
}

function add_file(){
	// Get the last row
	var tbl = document.getElementById('file_table');
	lastRow = tbl.rows.length;
	var finalRow = tbl.rows[lastRow - 1];
	finalRow_id = finalRow.getAttribute('id');
	finalRow_array = finalRow_id.split("_");
	lastRow_id = finalRow_array[1];
	nextRow_id = parseInt(lastRow_id) + 1;
	//
	x_addFile(lastRow_id,add_file_cb);	
}

function add_file_cb(html){
	var tbl = document.getElementById('file_table');
	lastRow = tbl.rows.length;
	// Get the last row
	var finalRow = tbl.rows[lastRow - 1];
	finalRow_id = finalRow.getAttribute('id');
	finalRow_array = finalRow_id.split("_");
	lastRow_id = finalRow_array[1];
	nextRow_id = parseInt(lastRow_id) + 1;
	//
	var row = tbl.insertRow(lastRow);
	var td = document.createElement("td");
	
	document.getElementById('file_' + lastRow_id).appendChild(td);
	document.getElementById('file_' + lastRow_id).firstChild.innerHTML = html;
	row.setAttribute('id', 'file_' + nextRow_id);
}

function add_product_option_cb(html){
	var tbl = document.getElementById('product_option_table');
	lastRow = tbl.rows.length;
	// Get the last row
	var finalRow = tbl.rows[lastRow - 1];
	finalRow_id = finalRow.getAttribute('id');
	finalRow_array = finalRow_id.split("_");
	lastRow_id = finalRow_array[2];
	nextRow_id = parseInt(lastRow_id) + 2;
	//
	var row = tbl.insertRow(lastRow);
	var td = document.createElement("td");
	
	document.getElementById('product_option_' + lastRow_id).appendChild(td);
	document.getElementById('product_option_' + lastRow_id).firstChild.innerHTML = html;
	row.setAttribute('id', 'product_option_' + nextRow_id);
}

function add_product_option(){
	// Get the last row
	var tbl = document.getElementById('product_option_table');
	lastRow = tbl.rows.length;
	var finalRow = tbl.rows[lastRow - 1];
	finalRow_id = finalRow.getAttribute('id');
	finalRow_array = finalRow_id.split("_");
	lastRow_id = finalRow_array[2];
	nextRow_id = parseInt(lastRow_id) + 2;
	//
	x_addProductOption(lastRow_id,add_product_option_cb);	
}

function remove_product_option(i){
  
  var tbl = document.getElementById('product_option_table');
  var row = document.getElementById('product_option_' + i);
  var rowIndex = row.sectionRowIndex;
  
  if(i > 1){
  	tbl.deleteRow(rowIndex);
	//tbl.removeChild(row);
	//lastRow = lastRow - 1;
	//tbl.deleteRow();
  }
}
//?>//