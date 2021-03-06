<?php

# Custom Color
$custom_color = $GLOBALS['primary_color'];

# Custom Button Colors
$custom_button_color = $GLOBALS['button_color'];
$custom_button_hover_color = $GLOBALS['button_color_hover'];
$custom_button_hover_border_color = $GLOBALS['button_color_hover'];

?>

<style>
/* CSS Document */
html, body{height: 100%;background-color: #FAFAFA;}
input[type="checkbox"]:focus + label::before{outline:0!important;}
*:focus{box-shadow:none; outline: none!important;outline-color: transparent;!important outline-style: none!important; -webkit-tap-highlight-color: rgba(0,0,0,0);}
.form-control:focus{border-color:<?php echo $custom_button_color ?>!important;}
	
/*** Dev **/
.container.dev,
.container-fluid.dev{border: 1px solid green;}
.container.dev .row,
.container-fluid.dev .row,
.row.dev{border: 1px solid blue;}
.container.dev .row [class*="col-"],
.container-fluid.dev .row [class*="col-"],
.row.dev [class*="col-"],
.row.dev [class*="col-"]{border: 1px solid red;}
form.dev [class*="col-"]{border: 1px solid red;}

/*** General Styles ***/
.btn i,
.alert i,
.panel-heading i{margin-right: 10px}
	.panel{box-shadow: none;}
.file-footer-buttons .toggle-active{width: 85px;}
.file-footer-buttons i{margin: 0;}
.noSelect {-webkit-touch-callout: none; -webkit-user-select: none; -khtml-user-select: none; -moz-user-select: none; -ms-user-select: none; user-select: none;}
.no_records{color: #777; margin: 20px;}
.no_records i{margin-right: 5px;}
.toggle-label{margin: 2px 10px 0 0;}
.label i{margin-right: 5px}
.file-preview-frame,
.file-preview-frame:hover{box-shadow: 0 1px 1px rgba(0,0,0,.07) !important;}
.btn.pull-left{margin-right: 10px;}
.btn{transition: 0.25s all ease;}
.pac-container{z-index: 5000;}

/*** Icons ***/
i.fa-danger::before,
i.fa-failure::before{content: "\f05e"}
i.fa-success::before{content: "\f00c"}
.fa-stack{line-height: inherit !important;}
.fa-globe.fa-stack-1x{font-size: 35px; margin-top: 27px;}

/*** Custom Button ***/
.btn-custom {
  color: #fff;
  background-color: <?php echo $custom_button_color ?>;
  border-color: <?php echo $custom_button_color ?>;
}
.btn-custom:hover,
.btn-custom:focus,
.btn-custom:active,
.btn-custom.active {
  color: #fff;
  background-color: <?php echo $custom_button_hover_color ?> !important;
  border-color: <?php echo $custom_button_hover_border_color ?> !important;
}
.btn-custom.disabled:hover,
.btn-custom.disabled:focus,
.btn-custom.disabled:active,
.btn-custom.disabled.active,
.btn-custom[disabled]:hover,
.btn-custom[disabled]:focus,
.btn-custom[disabled]:active,
.btn-custom[disabled].active,
fieldset[disabled] .btn-custom:hover,
fieldset[disabled] .btn-custom:focus,
fieldset[disabled] .btn-warning:active,
fieldset[disabled] .btn-custom.active,
.nav > li > a:hover,
.nav > .open > a:focus {
  background-color: <?php echo $custom_button_color ?>;
  border-color: <?php echo $custom_button_color ?>;
}
/** Custom Checkbox & Radio Button **/
.checkbox-list>div,
.radioButton-list>div{margin-bottom: 7px;}
.checkbox label::before{border-radius: 0px !important;}
.checkbox-custom label::after{color: #fff !important;}
.checkbox-custom input[type="checkbox"]:checked + label::before {background-color: <?php echo $custom_button_color ?>;border-color: <?php echo $custom_button_color ?>; }
.radio-custom input[type="radio"]:checked + label::before{border-color: <?php echo $custom_button_color ?> !important;}
.radio-custom label::after{background-color: <?php echo $custom_button_color ?> !important;}
input[type="radio"].styled:checked + label:after{content: "" !important;}

/** Custom Panel **/
.panel-custom {border-color: <?php echo $custom_button_hover_color ?>;}
.panel-custom > .panel-heading {color: #fff;background-color: <?php echo $custom_button_hover_color ?>;border-color: <?php echo $custom_button_hover_color ?>;}
.panel-custom > .panel-heading + .panel-collapse > .panel-body {border-top-color: <?php echo $custom_button_hover_color ?>;}
.panel-custom > .panel-heading .badge {color: #dff0d8;background-color: #3c763d;}
.panel-custom > .panel-footer + .panel-collapse > .panel-body {border-bottom-color: <?php echo $custom_button_hover_color ?>;}

/** Custom Label **/
.label-custom {background-color: <?php echo $custom_button_color ?>;}
.label-custom[href]:hover,
.label-custom[href]:focus {background-color: <?php echo $custom_button_hover_color ?>;}

/*** Modals ***/
#moduleSave i{display: none;}
#moduleSave.disabled i{display: inline-block;}
.modal-header{background-color: <?php echo $custom_button_hover_color ?>; color: #DDD;}

/*** Toast ***/
#toast-container{position: absolute; right: 10px; top: 5px; width: 280px; z-index: 1;}
.toast{opacity: 0;color: #fff; padding: 12px; margin: 10px 0; box-shadow: 0 0 1px 1px rgba(0,0,0,.2); float: left;width: 100%;}
.toast i,
.toast .toast-content{float: left;}
.toast i{width: 25px; margin-top: 2px;}
.toast .toast-content{width: 231px;}
.toast .toast-title{font-weight: 700; text-transform: capitalize;}
.toast.toast-prototype{display: none !important;}
.toast-success{background-color: #469408;}
.toast-success i:before{content: "\f00c"}
.toast-info{background-color: #4183D7;}
.toast-info i:before{content: "\f05a"}
.toast-warning{background-color: #F5AB35;}
.toast-warning i:before{content: "\f071"}
.toast-danger{background-color: #D64541;}
.toast-danger i:before{content: "\f05e"}

/*** Colorpicker ***/
.colorpicker-2x .colorpicker-saturation {width: 200px; height: 200px;}
.colorpicker-2x .colorpicker-hue,.colorpicker-2x .colorpicker-alpha {width: 30px; height: 200px;}
.colorpicker-2x .colorpicker-color,.colorpicker-2x .colorpicker-color div {height: 30px;}

/*** Slider ***/
/** Default Slider **/
.slider{line-height: 16px!important;border: 2px solid #949494; background: #949494; color: #FFF !important; border-radius: 31.5px; font-size: 11px; box-shadow: 0 1px 1px rgba(0,0,0,.1); transition: 0.25s all ease; margin: 0px 10px 0 0; display: block; width: 75px; height: 100%; min-height: 20px;position: relative; overflow: hidden;}
.slider.slider-on{border: 2px solid #4183D7;}
.slider:hover{box-shadow: 0 0 5px rgba(0,0,0,.2);}
.slider > div{width: 150px; height: 100%; position: absolute; top: 0; transition: 0.25s all ease; }
.slider.slider-on > div{left: -100%;margin-left: 12px;}
.slider.slider-off > div{left: 0%; margin-left: -4px;}
.slider>div>span{display: inline-block; box-shadow: inset 0 0px 1px rgba(0,0,0,.1) !important; width: 50%; float: left;}
.slider>div>span:first-child{padding: 0px 0px 0px 10px;}
.slider>div>span:last-child{padding: 0px 5px 0px 20px; margin-left: -9px; background: #4183D7; color: #fff;}
.slider>div>span:first-child::after{content: ""; background: white; border-radius: 50%; width: 16px; position: absolute; top: 0; bottom: 0; right: 50%; box-shadow: 0 1px 1px rgba(0,0,0,0.2) !important;}

/** Featured Slider **/
.slider.featured-toggle{width: 95px;}
.slider.featured-toggle > div{width: 190px;}
.slider.slider-on.featured-toggle{border: 2px solid #469408;}
.slider.featured-toggle>div>span:last-child{background: #469408;}

/*** Document Preview Icons ***/
.kv-file-content{position: relative;}
.file-icon-wrapper{position: absolute; top: 0; left:0; width: 100%; height: 100%;}
.file-icon-wrapper i{color: #fff; margin-top: 45px;}
.file-doc{background: #2980b9;}
.file-xls{background: #27ae60;}
.file-ppt{background: #d35400;}
.file-pdf{background: #c0392b;}
.file-txt{background: #9b59b6;}
.file-rtf{background: #16a085;}
.file-web{background: #34495e;}
.file-zip{background: #f39c12;}
.fa-file-globe-o::before{content: '\f016';}
.fa-file-globe-o::after{content: '\f0ac'; position: absolute; top: 43%; left: 42%; font-size: 30px;}
.fa-file-rtf-o::before{content: '\f016';}
.fa-file-rtf-o::after{content: 'RTF'; font-family: "roboto", sans-serif; position: absolute; top: 47%; left: 39%; font-size: 20px;}

/*** Navbar ***/
.nav-container{background: <?php echo $custom_color; ?>; box-shadow: 0 2px 2px -2px rgba(0,0,0,.5)}
.navbar{background: rgba(255, 255, 255, 0); margin: 0; border: 0px;}
.navbar-brand{cursor:pointer !important; background: <?php echo $custom_color; ?>!important;position: absolute;margin-left:0px!important;height: 85px;
    width: 263px;}
.navbar-brand img{width: 100%;}
.navbar-default .navbar-nav > li > a {color: #EFEFEF;}
.navbar-default .navbar-nav > li > a:hover,
.navbar-default .navbar-nav > li > a:focus{color: #FFF;}
.navbar-default .navbar-nav > li > span {color: #FFF;}
.navbar-default .navbar-nav > li > span {position: relative;display: block;padding: 15px 15px;}
.navbar i{padding-right: 5px;}
#navbar{border-top: 1px solid <?php echo $custom_button_hover_border_color;?>}
.navbar-toggle{background-color: <?php echo $custom_button_color;?> !important; border: 1px solid <?php echo $custom_button_color;?> !important;}
.navbar-toggle:hover{background-color: <?php echo $custom_button_hover_color;?> !important; border: 1px solid <?php echo $custom_button_hover_border_color; ?> !important;}
.navbar-default .navbar-toggle .icon-bar{background-color: white !important;}

/*** Login Form ***/
#login_form,
#reset_password_form,
#setup_form{margin-top: 10%;}
#login_form .form_content,
#reset_password_form .form_content,
#setup_form .form_content{background: rgba(255, 255, 255, 1); padding: 30px; box-shadow: 0 0 1px 1px rgba(0,0,0,.1);}
#login_form_heading,
#reset_password_form_heading,
#setup_form_heading{margin: 10px 0 20px !important; font-weight: 300; text-align: center; color: #777;}
#login_form input,
#reset_password_form input,
#setup_form input{margin: 0 0 20px 0;}
.credit{color: #777; font-size: 9pt; text-align: center; margin: 20px 0;}

/*** Tables ***/
.index-wrapper{border: 1px solid #ddd; background: white !important; padding: 10px 10px 5px; box-shadow: 0 1px 1px rgba(0,0,0,.07)}

/*** Logout Button ***/
#logout_button{padding: 6px 12px; margin: 9px; color: #fff;}

/*** Template Structure ***/
#side_menu{padding-top: 50px!important;}
#side_menu,
#content{padding-top: 30px;}
.action, .action_label{text-align: center}
.denote-required{color:#D64541;padding-top:0px;padding-bottom:20px;}
/** Side Menu **/
.list-group-item i{padding: 3px 5px 0 0;}
.list-group-item.active,
.list-group-item.active:hover,
.list-group-item.active:focus{background-color: <?php echo $custom_color; ?>; border: 1px solid <?php echo $custom_color; ?>;}

/** Overview Tiles **/
.overview-container{margin: 0 0 20px; text-align: center; }
.overview-item{background: #FFF; display: block; max-width: 320px; height: 200px; margin: 0 auto; border: 1px solid #ddd; padding: 10px; color: #777; vertical-align: middle; box-shadow: 0 1px 1px rgba(0,0,0,.07)}
.overview-item:hover,
.overview-item:focus{text-decoration: none; color: #777; background: #f5f5f5;}
.overview-item-content{padding: 0 13px; margin: 25px 0;}
.overview-item i{display: block; text-align: center; font-size: 30pt; color: <?php echo $custom_color ?>}
.overview-name{text-align: center; margin: 10px 0; font-weight: 700;}

/*** Forms ***/
.bold{font-weight: 700;}
.block{display: block}
.inline{margin: 0 0 0 10px;}
.checkbox label{padding: 0 0 0 5px !important;}
.tiny-mce-container{display: block;}
label{margin: 0;}
label.required{position: relative; margin: 0 0 0 13px;}
label.required::before{content: "\f069"; font-family: "FontAwesome"; color: #D64541; font-size: 7pt; font-weight: normal; margin: 0 5px 0 0; position: absolute; top: 1px; left: -13px;}
.instruction{margin: 3px 0 0 0;color:#666;}
.instruction ul{padding: 0 0 0 16px;}
.instruction b{color:#000;}
label .instruction{margin: 0 0 0 0;}
label .instruction ul{padding: 0 0 0 20px;}
.module-image-preview{margin-bottom:5px;}
.module-delete-file{margin-top:10px;}
.view-modal{ font-size: 14px; line-height: 2;}
.view-modal .col-sm-4{text-align: right;}

/*** Error Messages ***/
.error{display: none; font-size: 14px;margin: 0 !important;}

/*** Action Menu ***/
.action_menu a{color: #777; transition: 0.25s all ease; cursor: pointer; white-space: nowrap; margin: 1px 0 0; line-height: 1;}
.action_menu a:hover{color: <?php echo $custom_button_color ?>; transition: 0.25s all ease;}
.action_menu .popover{min-width: 150px;}
.action_menu .popover-content{padding: 5px 0;}
.actions{margin: 0; padding: 0; list-style-type: none;}
.actions li{}
.actions li a{display: block; padding: 5px 10px;}
.actions li a:hover{background: <?php echo $custom_button_color ?>; color: #FFF; text-decoration: none;}
.actions li i{margin-right: 10px;}

/*** Toggle Buttons ***/
.ajaxToggle.btn{padding: 2px 5px; margin-right: 10px; line-height: 1;}

/*** Batch Actions Menu ***/
.batch-container{margin-right:2px;}
.batch-actions li a{padding:6px 12px;}
.batch-dropdown li i{margin-right:10px; color:#A6A6A6;}
.batch-dropdown li a{padding:6px 10px; color:#777; transition:0.25s all ease;}
.batch-dropdown li a:hover{color:#fff; background:<?php echo $custom_button_color ?>; transition:0.25s all ease;}
.current-batch-action-options{display:none;}
.current-batch-action-options span{vertical-align:middle; color:#555; margin-right:20px; font-size:1.7em;}

.current-batch-action-options i{margin-right:8px;color:<?php echo $custom_button_color ?>}
.batch-select-all{width:125px;}
.batch-select-all i{color:#fff;}
.banner-actions{margin-bottom: 10px;}

/*** Sortable Items ***/
*:focus {outline: none!important;}
.disclose .fa{font-size:1.3em; margin-top: 1px;}
.action_menu .fa{font-size:1.3em;}
.placeholder{border:1px dashed <?php echo $custom_button_color ?>;}
.notice{color:#c33;}
.mjs-nestedSortable-error{background:#fbe3e4; border-color:transparent;}
#tree{margin:0;}
.sortable{max-width:100%;padding:0;margin: 10px 0px}
.sortable ol{padding-left:38px;}
.sortable,
.sortable ol{list-style-type:none;}
.sortable li.mjs-nestedSortable-collapsed.mjs-nestedSortable-hovering div{max-width:100%;border-color:#999;}
.sortable li.mjs-nestedSortable-collapsed > ol{display:none;}
.sortable .menuDiv{
-webkit-user-select: none; /* Chrome/Safari */
-moz-user-select: none; /* Firefox */
-ms-user-select: none; /* IE10+ */
 vertical-align:middle; background:#FCFCFC; margin: 6px 0; border: 1px solid #ddd; box-shadow: 0 1px 1px rgba(0,0,0,.07); height: 35px;}
.menuDiv:hover{background:#fff;}
.menuDiv .draghandle{border: 0px;border-right: 1px solid #ddd; cursor: -webkit-grabbing;}
.kv-file-content{overflow-y: auto;overflow-x: hidden;}

.menuDiv .branch_content{padding: 7px;height: 100%; line-height: 1;}
.disclose{cursor:pointer; margin-right:5px;color: #777;transition: 0.25s all ease;float: left;}
.disclose:hover{color: <?php echo $custom_color; ?>;}
.itemTitle{float: left; margin-top: 3px;}
.itemTitleGallery{padding-left:41px;}

/* Small devices (tablets, 768px and up) */
.bootstrap-select{width:100%!important;}
.dropdown-toggle{padding:7px;}
.checkbox label, .radio label{font-size: 1em;}
.checkbox label:after, 
.radio label:after {
    content: ''!important;
    display: table!important;
    clear: both!important;
}

.checkbox .cr,
.radio .cr {
    position: relative!important;
    display: inline-block!important;
    border: 1px solid #a9a9a9!important;
    border-radius: 0em!important;
    width: 1.3em!important;
    height: 1.3em!important;
    float: left!important;
    margin-right: .5em!important;
}

.radio .cr {
    border-radius: 50%!important;
}

.checkbox .cr .cr-icon,
.radio .cr .cr-icon {
    position: absolute!important;
    font-size: .8em!important;
    line-height: 0!important;
    top: 50%!important;
    left: 20%!important;
	color:<?php echo $custom_button_color ?>;
}

.radio .cr .cr-icon {
    margin-left: 0.04em!important;
}

.checkbox label input[type="checkbox"],
.radio label input[type="radio"] {
    display: none!important;
}

.checkbox label input[type="checkbox"] + .cr > .cr-icon,
.radio label input[type="radio"] + .cr > .cr-icon {
    transform: scale(3) rotateZ(-20deg)!important;
    opacity: 0!important;
    transition: all .3s ease-in!important;
}

.checkbox label input[type="checkbox"]:checked + .cr > .cr-icon,
.radio label input[type="radio"]:checked + .cr > .cr-icon {
    transform: scale(1) rotateZ(0deg)!important;
    opacity: 1!important;
}

.checkbox label input[type="checkbox"]:disabled + .cr,
.radio label input[type="radio"]:disabled + .cr {
    opacity: .5!important;
}
/*** Media Queries ***/
/* Small devices (tablets, 768px and up) */
@media (min-width: 768px) {
  /** Overview Tiles **/
  .overview-item{display: table-cell;}
  .overview-item-content{margin: auto;}
}
/* Medium devices (desktops, 992px and up) */
@media (min-width: 992px) {}
/* Large devices (large desktops, 1200px and up) */
@media (min-width: 1200px) {}
</style>
