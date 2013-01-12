<?php header('Content-type: text/css') ?>
/*{{{RESET*/
html, body, div, span, applet, object, iframe,
h1, h2, h3, h4, h5, h6, p, blockquote, pre,
a, abbr, acronym, address, big, cite, code,
del, dfn, em, font, img, ins, kbd, q, s, samp,
small, strike, strong, sub, sup, tt, var,
dl, dt, dd, ol, ul, li,
fieldset, form, label, legend,
table, caption, tbody, tfoot, thead, tr, th, td {
	margin: 0;
	padding: 0;
	border: 0;
	outline: 0;
	font-weight: inherit;
	font-style: inherit;
	font-size: 100%;
	font-family: inherit;
	vertical-align: baseline;
}
:focus {
	outline: 0;
}
body {
	line-height: 1;
	color: black;
	background: white;
}
ol, ul {
	list-style: none;
}
table {
	border-collapse: separate;
	border-spacing: 0;
}
caption, th, td {
	text-align: left;
	font-weight: normal;
}
blockquote:before, blockquote:after, q:before, q:after {
	content: "";
}
blockquote, q {
	quotes: "" "";
}
/*}}}*/
/*{{{layout*/
#container { position: relative; margin: 30px 0 0 30px; width: 920px; }
#nav { margin-right: 20px; width: 140px; float: left; }
#content { margin-right: 0px; width: 760px; float: left; line-height: 1.5em; }
#sidebar { position: absolute; right: 0; width: 210px; }
#footer { width: 920px; }

/*}}}*/
/*{{{elements*/
body { font-size: 62.5%; font-family: Arial,sans-serif; background-color: <?php echo !is_null(Data::query('Admin', 'bgcolor')) ? Data::query('Admin', 'bgcolor') : '#FFFFFF'; ?>; }
h1, h2, h3, h4, h5, h6 { font-weight: bold; }
h1 { margin-bottom: 10px; font-size: 1.6em; }
.clear:after, .group:after, .field:after, .label:after { content: '.'; clear: both; display: block; visibility: hidden; height: 0; margin: 0; padding: 0; }
pre#developer { font-family: fixed; font-size: 1.2em; }
input, textarea, select { border: 1px solid #999; position: relative; }
a { color: #4896C5; text-decoration: none; cursor: pointer; }
a:hover { color: #4896C5; text-decoration: underline; }
p { margin-bottom: 1em; }
strong, b { font-weight: bold; }
small { font-size: 9px; }
.cleared:after { content: "."; clear: both; display: block; height: 0; margin: 0; visibility: hidden; }

/*}}}*/
/*{{{header/footer*/
#header { margin-bottom: 17px; color: #333; border-bottom: 1px solid #000; position: relative; height: 105px; }
#header .site { float: left; margin-bottom: 0; width: 300px; font-size: 1.6em; max-height: 100px; }
#header .site a { color: #000; }
#header .site img { max-height: 100px; }
#header .title { margin-bottom: 0; }
#header .description { font-style: italic; font-size: 1.4em; }
#header .user_ctrl { float: right; text-align: right; font-size: 12px; }
#header .admin { background-color: #000; position: absolute; bottom: 0px; right: 0px; font-size: 11px; }
#header .admin li { padding: 4px; float: left; color: #FFF; }
#header .admin li:hover { background-color: #4896C5; }
#header .admin a { color: #FFF; }
#header #quicklinks { display: none; position: relative; cursor: pointer; }
#header #quicklinks ul { position: absolute; background-color: #000; left: 0px; top: 19px; display: none; width: 177px; }
#header #quicklinks ul li { float: none; cursor: pointer; }
#header #quicklinks ul li a { display: block; }
#footer { margin: 50px 0px; border-top: 1px solid #000; padding-top: 5px; position: relative; font-size: 1.1em; }
#footer span { margin-right: 15px; }
#footer .site_credit { position: absolute; right: 0px; margin-right: 0px; }

/*}}}*/
/* {{{ body/content */
#body .header { text-align: right; font-size: 1.4em; color: #A6A8AB; margin-bottom: 29px; }
#content { padding-top: 1px; }
#content > ul, #content > p { font-size: 1.2em; }

/* }}} */
/*{{{nav*/
#nav { font-size: 1.2em; }
#nav li { margin: 0 0 8px 8px; }
#nav li li { margin-bottom: 6px; }
#nav li.modules { margin-bottom: 8px; padding: 0; }
#nav li.modules li { padding: 8px 0 0 8px; }
#nav li.modules li div { padding-bottom: 8px; cursor: pointer; color: #4896C5; }
#nav li.modules li li { padding: 0 0 8px 8px; }
#nav li.modules li ul { margin: 0; font-size: 0.85em; }
#nav ul ul { margin-top: 4px; }
#nav div { cursor: pointer; }

/*}}}*/
/*{{{forms*/
.form_label { margin-bottom: 6px; font-weight: bold; font-size: 1.2em; }
.form_wrapper { margin-bottom: 20px; }
.group { clear: left; margin-bottom: 20px; }
.group .label { margin: 20px 0 10px; font-size: 1.2em; font-weight: bold; }
.group .delete .field { float: right; }
.row { margin-bottom: 10px; }
.row .label, #sidebar h3 { margin: 0 0 4px; font-size: 1.4em; font-weight: bold; }
.tabbed .row .field .label { padding-left: 0; }
.row .description { margin-left: 10px; }
.row label { display: block; clear: both; font-size: 1.2em; line-height: 1.6em;}
.row .description { font-size: 1.0em; color: #666; margin-top: 0; margin-right: 5px; }
.row_hidden { display: none; }
.field label { display: inline; }
.field label.option { display: block; }
.field input.text, .field input.password { width: 506px; padding-left: 3px; height: 24px; font-size: 12px;}
.field input.text-short { width: 200px; }
.field textarea { width: 506px; }
.field input.checkbox { margin-bottom: 0; vertical-align: text-bottom; }
.field textarea { height: 50px; }
.field textarea.slim { height: 40px; }
.field select.select { width: 511px; }
.fields_submit_reset .field { float: left; display: inline; margin-right: 8px; }
.fields .meta { margin-bottom: 10px; }
.fields .field { margin-bottom: 3px; }
.hiddens { display: none; }
.buttons { width: 528px; } /* keep this same as .tsc width */
.tsc { position: relative; width: 528px; }
/*.tsc input.text { width: 200px; }*/
.tsc .title, .tsc .slug { clear: left; float: left; /*width: 200px;*/ }
.tsc .category { position: absolute; top: 0; right: 0; width: 310px; }
.tsc .category select { width: 310px; }
.tabbed .row { margin-bottom: 0; padding: 0 0 10px; }
.tabbed .row .label, .tabbed .row .fields { padding: 6px 10px; clear: both; }
.tabbed .row .label { padding: 10px 10px 0; }
.tabbed .row .content_multiple_fields_additional { border-top: 1px dotted #AAA; padding: 10px; }
.tabbed .content_multiple .fields { position: relative; padding-bottom: 25px; }
.content_multiple .fields .remove { position: absolute; padding: 2px 4px; right: 0; bottom: 0px; text-align: right; cursor: pointer; color: #4896C5; }

/*}}}*/
/*{{{ui*/
.ui-tabs .ui-tabs-hide { display: none; }
.ui-tabs-nav { width: 530px; }
.ui-tabs-nav li { position: relative; float: left; bottom: -1px; margin: 0 0 0 10px; padding: 4px 10px 2px; }
.ui-tabs-nav a { color: #444; }
.ui-tabs-nav .ui-state-default { background-color: #CDCDCD; }
.ui-tabs-nav .ui-state-active { background-color: #f0f0f0; }
.ui-tabs-nav .ui-state-hover a { color: #000; }
.ui-tabs-panel { background-color: #F0F0F0; padding: 0 0; width: 528px; position: relative; }

.fields_mouseover div.remove { display: block; }
.fields_mouseout div.remove { display: none; }

.field .list_double_ordered { width: 250px; }
.field .list_double_ordered li { cursor: pointer; }
.field .list_double_ordered_left { float: left; }
.field .list_double_ordered_right { float: right; }

.field input.date { width: 200px; }
.field input.timepickr { width: 50px; }

/*}}}*/
/*{{{dashboard*/
#admin_dashboard div.admin_dashboard_element { margin-bottom: 45px; }
#admin_dashboard .admin_dashboard_element_placeholder { margin-bottom: 20px; border: 2px dashed #CCC; }
#admin_dashboard div.admin_dashboard_element div.title { padding: 5px 0px; border-bottom: 1px solid #000; font-weight: bold; font-size: 14px; color: #4D4D4D; }
#admin_dashboard div.admin_dashboard_element div.content { padding: 9px 0px; }
#admin_dashboard div.admin_dashboard_element div.content li { margin-bottom: 10px; font-size: 12px; }
#admin_dashboard div.admin_dashboard_element div.content li small { color: #666; }
#admin_dashboard div.admin_dashboard_element_closed div.content { display: none; }
#admin_dashboard div.admin_dashboard_side { width: 370px; min-height: 50px; }
#admin_dashboard div.open_close { float: right; font-weight: normal; font-size: 9px; color: #666; cursor: pointer; }
#admin_dashboard_left { float: left; }
#admin_dashboard_right { float: right; }

/* trash */
#admin_dashboard #admin_dashboard_trash { clear: both; width: 760px; }
#admin_dashboard_trash div.admin_dashboard_element { margin-bottom: 15px; }
#admin_dashboard_trash div.open_close,
#admin_dashboard_trash div.content { display: none; }

/*}}}*/
/*{{{messages*/
#messages { margin-bottom: 5px; }
#messages ul { padding: 15px; }
#messages .success { background-color: #C6B0C4; }
#messages .error { background-color: #CC0000; }
#messages .notice { background-color: #CC9900; }
#messages .important { background-color: #CCFF00; }
#messages .close { float: right; }

/*}}}*/
