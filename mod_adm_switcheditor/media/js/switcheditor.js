/**
 * @package    Switch Editor
 * @subpackage mod_switcheditor
 * @copyright  Copyright (C) 2024 ConseilGouz. All rights reserved.
 * From anything-digital.com Switch Editor
 * @license    GNU/GPLv2
 */
// import { JoomlaEditor } from 'editor-api';

document.addEventListener("DOMContentLoaded", function(){
swedoptions = Joomla.getOptions('mod_switcheditor');
let systemmsg = document.querySelector('#system-message-container');
let box = "";
const els = document.querySelectorAll('.adEditor');	

for (let i = 0; i < els.length; i++) {
	els[i].addEventListener('change', function (ev) {
		if (systemmsg && (swedoptions.auto == 1) ) {
			box = document.createElement('div');
			box.innerHTML = '<joomla-alert type="warning" role="alert" style="animation-name: joomla-alert-fade-in;"><div class="alert-heading"><span class="visually-hidden">info</span></div><div class="alert-wrapper"><div class="alert-message">'+swedoptions.automsg+'<span class="switching"></span></div></div></joomla-alert>';
			systemmsg.appendChild(box);
		}
		var csrf = Joomla.getOptions("csrf.token", "");
		var url = "?"+csrf+"=1&option=com_ajax&module=switcheditor&adEditor="+ev.srcElement.value+"&task=switch&format=json";
		Joomla.request({
			method : 'POST',
			url : url,
			onSuccess: function(data, xhr) {
				if (swedoptions.auto == 1) doSave();
			},
			onError: function(message) {console.log(message.responseText)}
		}) 
	});
}
function doSave() {
	let articletext = document.querySelector('#jform_articletext');
	let moduletext = document.querySelector('#jform_content');
	if (articletext) { // article
		Joomla.submitbutton('article.apply', null, true);
		return;
	} 
	if (moduletext) { // module
		Joomla.submitbutton('module.apply', null, true);
		return;
	}
	systemmsg.removeChild(box);
}
})