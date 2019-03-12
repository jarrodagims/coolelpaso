// Functions used to fire toasts
/* function notify() outputs a toast notification
 * @param JSON array representing the outcome of a form submission
 * @internal This function should only be used within the Admin system */
function notify(formOutcome){
	toast(formOutcome.status, formOutcome.title, formOutcome.message);
}

/* function toast() outputs a toast notification
 * @param type: string, @param title: string, @param message: string for toast notification
 * @internal This function should only be used within the Admin system */
function toast(type, title, message){
	// Container for toasts
	var toastContainer = $('#toast-container');
	// Create new toast item from exsisting toast prototype
	var toast = toastContainer.find('.toast-prototype').clone().appendTo(toastContainer);
	// Replace with appropriate content
	toast.find('.toast-title').html(title);
	toast.find('.toast-message').html(message);
	// Assign appropriate class based on type
	toast.addClass('toast-'+type);
	// Remove prototype class (Removes display: none, but is still opacity: 0)
	toast.removeClass('toast-prototype');
	// Fade in toast
	toast.addClass('animated fadeInDown').delay(4000).queue(function(){
		// Fade out toast
		$(this).addClass('fadeOutDown')
		// Remove toast
		setTimeout(function(){
			toast.remove();
		}, 1000);
	});

}

// Show Modal
$(document).on('click', '*[data-toggle="modal"]', function(e){
	e.preventDefault();

	//clear existing forms
	$("#form").remove();
	
});

function initFormElements() {
	// Init TinyMce
	$(".tiny-mce").each(function(){

		var id = $(this).attr('id');
		tinymce.execCommand('mceRemoveEditor', false, id);

		if($(this).hasClass('tiny-mce-small')){
			tinyMceSmall(id);
		}
		else if($(this).hasClass('tiny-mce-full')){
			tinyMceFull(id);
		}
		else{
			tinyMce(id);
		}
	});
}

// Popovers
function initPopOver(){
	$("a[data-toggle='popover']").popover({html: true, placement: 'bottom', content: $(this).attr('data-content')});
};

// Tiny MCE
function tinyMce(id){
	tinymce.init({
		selector: "#"+id,
		height: 450,
		theme: "modern",
		moxiemanager_title: "File Manager",
		plugins: [
		"advlist autolink lists link image charmap print preview hr anchor pagebreak",
		"searchreplace wordcount visualblocks visualchars code fullscreen",
		"insertdatetime media nonbreaking save table contextmenu directionality",
		"emoticons template paste textcolor colorpicker textpattern imagetools moxiemanager"
		],
		toolbar1: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
		toolbar2: "print preview media | forecolor backcolor emoticons",
		image_advtab: true,
		statusbar: false,
		relative_urls : false,
  		convert_urls: false,
  		remove_script_host : true
	});
}


function tinyMceSmall(id){
	tinymce.init({
		selector: "#"+id,
		height: 150,
		theme: "modern",
		moxiemanager_title: "File Manager",
		plugins: [
		"advlist autolink lists link image charmap print preview hr anchor pagebreak",
		"searchreplace wordcount visualblocks visualchars code fullscreen",
		"insertdatetime media nonbreaking save table contextmenu directionality",
		"emoticons template paste textcolor colorpicker textpattern imagetools moxiemanager"
		],
		toolbar1: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
		toolbar2: "print preview media | forecolor backcolor emoticons",
		image_advtab: true,
		statusbar: false,
		relative_urls : false,
  		convert_urls: false,
  		remove_script_host : true
	});
}

function tinyMceFull(id){
	tinymce.init({
		selector: "#"+id,
		height: 450,
		theme: "modern",
		moxiemanager_title: "File Manager",
		plugins: [
		"advlist autolink lists link image charmap print preview hr anchor pagebreak",
		"searchreplace wordcount visualblocks visualchars code fullscreen",
		"insertdatetime media nonbreaking save table contextmenu directionality",
		"emoticons template paste textcolor colorpicker textpattern imagetools moxiemanager"
		],
		toolbar1: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
		toolbar2: "print preview media | forecolor backcolor emoticons",
		image_advtab: true,
		statusbar: false,
  		relative_urls: false,
  		convert_urls: false,
  		remove_script_host : true
	});
}

$(function(){
	$(document).on("submit", "#form", function(event){
		event.preventDefault();
		$(this).parents(".modal").find("#moduleSave").trigger("click");
	});
});

$(function(){
	$(document).on("click", ".ajaxToggle", function(event){
		event.preventDefault();
		ajaxToggle($(this));
	});
});

$(function(){
	$(document).on("click", ".slider", function(event){
		event.preventDefault();

		if($(this).hasClass('locked')){
			toast("danger", "Failure", "This Item is locked to changes.");
		}
		else{
			var link = $(this);
			if($(this).hasClass('slider-on')){
				$(this).removeClass('slider-on');
				$(this).addClass('slider-off').delay(250).queue(function(){ajaxToggle(link);$(this).dequeue();});
			}else if($(this).hasClass('slider-off')){
				$(this).removeClass('slider-off');
				$(this).addClass('slider-on').delay(250).queue(function(){ajaxToggle(link);$(this).dequeue();});
			}
		}

	});
});

$(function(){
	$(document).on("change", "input:file", function(event){
		var fileSize = this.files[0].size;
		var response = checkUpload(fileSize);
		var name = $(this).attr('name');
		var control = $(this);
        if(response){
			$(this).parents('.form-group').find('.error').html(response).show();
			this.value = ''
			if(!/safari/i.test(navigator.userAgent)){
				  this.type = ''
				  this.type = 'file'
			}
		}else{
			$(this).parents('.form-group').find('.error').html('').hide();
		}
	});
});

// Replace Image or Add Image change
$(function(){
	$(document).on("change", ".btn-file > input", function(){
		var fileName = $(this).val();
		fileName = fileName.split("\\");
		fileName = fileName[fileName.length-1];
		$(this).parents('.btn-file').siblings('.file-name').html(fileName);
	});
});

function ajaxToggle(link){
	// Get url for action
	var url = $(link).attr('href');

	var action = getParameterByName('action', url);

	var modal = $('.modal');

	$.ajax({
		type: 'GET',
		url: url
	}).done(function(response){
		// Parse JSON array
		response = jQuery.parseJSON(response);

		formOutcome(response, action, modal);

		if(response.formOutcome.status === 'danger'){
			if(link.hasClass('slider-on')){
				link.removeClass('slider-on');
				link.addClass('slider-off');
			}else if(link.hasClass('slider-off')){
				link.removeClass('slider-off');
				link.addClass('slider-on');
			}
		}

	}); // END DONE AJAX
}

// Submit Add, Edit and Delete forms on modal save
$(function(){
	$(document).on("click", "#moduleSave", function(event){

		event.preventDefault();

		// If submit button already has disbled class - do nothing
		if($("#moduleSave").hasClass('disabled')){
			return;
		}

		// Disable submit button - shows spinner
		//$("#moduleSave").addClass('disabled');

		// Call save method on all editor instance in the collection.
		tinyMCE.triggerSave();

		// prepare data for FormData Object needed to submit images via ajax - does not work in ie below 10
		var form = document.getElementById('form');
		if(window.navigator.userAgent.indexOf("MSIE ") > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./) || window.navigator.userAgent.indexOf("Edge") > -1)
		{}else{
			for(i = 0; i < form.elements.length; i++){
				if(form.elements[i].type === 'file'){
					if(form.elements[i].value === ''){
						 form.elements[i].parentNode.removeChild(form.elements[i]);
					}
				}
			}
		}

		var formDataFormat = document.getElementById('form');
		var formData = new FormData(formDataFormat);
		// Get modal
		var modal = $(this).parents(".modal");
		// Get form
		var form = modal.find("form");

		if(form.length <= 0){
			return;
		}

		// Get action from serialized data
		var action = form.find('[name="action"]').val();
		// Get url for action
		var url = form.attr('action');

		// Submit the for using AJAX
		$.ajax({
			type: 'POST',
			url: url,
			data: formData,
			cache: false,
        	contentType: false,
        	processData: false
		}).done(function(response){
			// Parse JSON array
			//console.log(response);
			response = jQuery.parseJSON(response);
			//console.log(response);

			formOutcome(response, action, modal);

		}); // END DONE AJAX
	});
});

function formOutcome(response, action, modal){
	// Get form
	var form = modal.find("form");

	// Form Outcome (Submission Outcome)
	var formOutcome = response.formOutcome;
	console.log(response);

	// IF FORM OUTCOME WAS SUCCESS
	if(formOutcome.status == "success"){

		//Close Modal
		modal.modal('hide');

		// Enable submit button - hide spinner
		$("#moduleSave").removeClass('disabled');

		// REFRESH CONTENT
		var refreshData = response.refreshData;
		//console.log(refreshData);
		if(refreshData != undefined){
			for(var i = 0; i < refreshData.length; i++){
				// current element
				var element = refreshData[i];

				if(element.hasOwnProperty('selector')){

					if(!element.hasOwnProperty('action')){
						element.action = action;
					}

					// selector for the element to be refreshed
					var selector = element.selector;

					// content for the element to be refreshed
					var content = element.content;

					// action for the element to be refreshed
					var action = element.action;

					if($(selector).length > 0){
						if(action === 'edit'){
							$(selector).html(content);
						}
						else if(action === 'add'){
							$(selector).append(content);
						}
						else if(action === 'delete'){
							$(selector).remove();
						}
						else if(action === 'replace'){
							$(selector).replaceWith(content);
						}
					}
				}

				// create this function in module if additional executions are needed
				if (typeof moduleSpecificRefresh === "function"){
					moduleSpecificRefresh(content,action,selector);
				}
			}
		}

		// Notify user
		notify(formOutcome);
		form.remove();
	}
	// IF FORM OUTCOME WAS FAILURE
	else if(formOutcome.status == "danger"){
		// Clear All Error Messages
		$(".error").slideUp();
		$(".error").html("");

		if(response.hasOwnProperty('messages')){
			// Messages from form action
			var messages = response.messages;

			// For each Message
			for(var messageName in messages){
				var message = messages[messageName];
				// Create Error
				var errorContainer = $(".error."+messageName);
				// If general error - create bootstrap alert
				if(messageName == "general"){
					errorContainer.html("<div class='alert alert-"+message.type+"'><i class='fa fa-"+message.type+"'></i>"+message.text+"</div>");
				}
				// Else create bootsrap label
				else{
					errorContainer.html("<span class='label label-"+message.type+"'>"+message.text+"</span>");
				}
			}
			// Show errors
			$(".error").slideDown();

			// Scroll to the top
			modal.animate({ scrollTop: 0 }, '400');

			// Enable submit button - hide spinner
			$("#moduleSave").removeClass('disabled');
		}
		else{
			// Form Outcome (Submission Outcome)
			var formOutcome = response.formOutcome;
			//Close Modal
			modal.modal('hide');
			//Notify user
			notify(formOutcome);
		}
	}
}

function getParameterByName(name, url) {
    if (!url) {
      url = window.location.href;
    }
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
}

// override the built in block on focusin in bootstrap dialogs when using TinyMCE inside it.
$(document).on('focusin', function(e) {
  if ($(e.target).closest(".mce-window, .moxman-window").length) {
    e.stopImmediatePropagation();
  }
});

// When Document is ready
$(document).ready(function(){

	$('.external').attr('target', '_blank');

	initPopOver();

	//////modal clear/////
	$(document).on('hidden.bs.modal', '.modal', function () {
		var modalData = $(this).data('bs.modal');
		if (modalData && modalData.options.remote) {
			$(this).removeData('bs.modal');
			$(this).find(".modal-content").empty();
		}
	});



	$(".js_required").removeClass("js_required");

	if($(".validate").size()){
		$(".validate").bind("click", validate);
	}

	if($(".delete").size()){
		$(".delete").bind("click", confirmDelete);
	}

	if($(".autofocus").size()){
		$(".autofocus").focus();
	}

	if($(".checkbox_select_all").size()){
		$(".checkbox_select_all").bind('click',function(){
			$('.checkbox_select').prop('checked', true);
			return false;
		});
	}

	if($(".checkbox_deselect_all").size()){
		$(".checkbox_deselect_all").bind('click',function(){
			$('.checkbox_select').prop('checked', false);
			return false;
		});
	}


	/*<SORTABLE LISTS>*/
		var listItems = $('.sortable li');
		listItems.each(function(i, li){
			if($(this).is('.mjs-nestedSortable-branch')){
				$(this).find('.disclose').removeClass('hidden');
 			}else{
				$(this).find('.disclose').addClass('hidden');
				$(this).find('.disclose').find('i').toggleClass('fa-chevron-circle-up fa-chevron-circle-down');
			}
		});

		$('#content').on('click','.disclose', function(){
			$(this).closest('li').toggleClass('mjs-nestedSortable-collapsed').toggleClass('mjs-nestedSortable-expanded');
			$("i", this).toggleClass("fa-chevron-circle-up fa-chevron-circle-down");
		});
	/*<END SORTABLE LISTS>*/


	/*<BATCH ACTIONS>*/
	//$('.checkbox-custom').hide();

	$('#content').on('click','.batch-option', function(){
		$('.current-batch-action-options').children('span').html($(this).children('a').html());
		$('.current-batch-action-options').show('slide', {direction:'right'}, 200);
		$('.batch-actions').hide();
		$('.fa-gear').hide('slide', {direction:'right'}, 100, function(){
			$('.checkbox-custom').show('slide', {direction:'left'}, 100);
		});
	});

	$('#content').on('click','.batch-cancel, .batch-apply', function(){
		$('.current-batch-action-options').hide('slide', {direction:'right'}, 100, function(){
			$('.batch-actions').show('slide', {direction:'left'}, 100);
		});
		$('.checkbox-custom').hide('slide', {direction:'left'}, 200);
		$('.fa-gear').show('slide', {direction:'right'}, 200);
		$('input:checkbox').prop('checked',false);
		$('.batch-select-all').html('<i class="fa fa-check-square-o" aria-hidden="true"></i>Select All');
	});


	$('#content').on('click','.batch-select-all', function(){
		if ($(".batch-select-all i").hasClass("fa-check-square-o")){
			$('input:checkbox').prop('checked',true);
		}else{
			$('input:checkbox').prop('checked',false);
		}
		$(this).html($(this).html() == '<i class="fa fa-check-square-o" aria-hidden="true"></i>Select All' ? '<i class="fa fa-square-o" aria-hidden="true"></i>Select None' : '<i class="fa fa-check-square-o" aria-hidden="true"></i>Select All');
	});
	/*<BATCH ACTIONS>*/
});

function toggleVisible() {
	var listItems = $('.sortable li');
	listItems.each(function(i, li){
		//console.log($(this));
		// Collapse leaf nodes
		if(($(this).hasClass('PhotoGallery') || $(this).hasClass('DocumentCategory') || $(this).hasClass('DocumentSubCategory')) && $(this).hasClass('mjs-nestedSortable-leaf') && !$(this).hasClass('mjs-nestedSortable-collapsed')){
			$(this).removeClass('mjs-nestedSortable-expanded');
			$(this).addClass('mjs-nestedSortable-collapsed');
		}

		// Add empty ol if needed
		if($(this).find("ol").length <= 0){
			$(this).append("<ol></ol>");
		}

		// Add or Remove appropriate class (leaf or branch)
		if($(this).find("ol").children("li").length > 0){
			$(this).removeClass('mjs-nestedSortable-leaf');
			$(this).addClass('mjs-nestedSortable-branch');
		}
		else{
			$(this).removeClass('mjs-nestedSortable-branch');
			$(this).addClass('mjs-nestedSortable-leaf');
		}

		// Show or hide toggle icon
		if($(this).is('.mjs-nestedSortable-branch')){
			$(this).find('.disclose').removeClass('hidden');
		}
		if($(this).is('.mjs-nestedSortable-leaf')){
			$(this).find('.disclose').addClass('hidden');
		}

		// Show Plus or Minue Icon
		if($(this).hasClass('mjs-nestedSortable-expanded')){
			var icon = $(this).children('.menuDiv').find('.disclose i');
			icon.removeClass('fa-chevron-circle-down');
			icon.addClass('fa-chevron-circle-up');
		}
		if($(this).hasClass('mjs-nestedSortable-collapsed')){
			var icon = $(this).children('.menuDiv').find('.disclose i');
			icon.addClass('fa-chevron-circle-down');
			icon.removeClass('fa-chevron-circle-up');
		}
	});
}


// js session timeout redirect
var IDLE_TIMEOUT = 1440; //seconds
var _idleSecondsTimer = null;
var _idleSecondsCounter = 0;

document.onclick = function() {
    _idleSecondsCounter = 0;
	$.ajax({
		method: "POST",
		url:"/admin/includes/session.php"
	});
};
document.onmousemove = function() {
    _idleSecondsCounter = 0;
};
document.onmousedown = function() {
    _idleSecondsCounter = 0;
};
document.onkeypress = function() {
    _idleSecondsCounter = 0;
};
document.ontouchstart = function() {
    _idleSecondsCounter = 0;
};
document.onscroll = function() {
    _idleSecondsCounter = 0;
};

_idleSecondsTimer = window.setInterval(CheckIdleTime, 1000);

function CheckIdleTime() {
     _idleSecondsCounter++;
    if (_idleSecondsCounter >= IDLE_TIMEOUT) {
        window.clearInterval(_idleSecondsTimer);
        document.location.href = "/admin/timeout.php";
    }
}
