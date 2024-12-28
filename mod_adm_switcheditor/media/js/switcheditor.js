/**
 * @package    Switch Editor
 * @subpackage mod_switcheditor
 * @copyright  Copyright (C) 2024 ConseilGouz. All rights reserved.
 * From anything-digital.com Switch Editor
 * @license    GNU/GPLv2
 */
// import { JoomlaEditor } from 'editor-api';
var swedoptions = [];
document.addEventListener("DOMContentLoaded", function(){
    
const boxs = document.querySelectorAll('.adEditorFormBox');	    
let systemmsg = document.querySelector('#system-message-container');
let toolbar = document.querySelector('#toolbar');
if (toolbar){
    let pos = toolbar.querySelector('.ms-auto');
    if (!pos) {
        pos = toolbar.querySelector('#toolbar-inlinehelp');
    }
}
let xtdbuttons = document.querySelector('.editor-xtd-buttons');

for(var i=0; i<boxs.length; i++) {
    let one = boxs[i];
	oneid = one.getAttribute("data");
	if (typeof Joomla === 'undefined' || typeof Joomla.getOptions === 'undefined') {
		console.error('Joomla.getOptions not found!\nThe Joomla core.js file is not being loaded.');
		return false;
	}
    swedoptions[oneid] = Joomla.getOptions('mod_switcheditor_'+oneid);

    let swe = "";
    const els = one.querySelector('.adEditor');	
    if (swedoptions[oneid].fixed == '1') { // fixed position
        if (swedoptions[oneid].horizontal != 'top') {
            one.style.setProperty('--animation-duration','1s');
            one.classList.add('animate__animated');
            one.classList.add('animate__flipInY');
        }
        one.classList.add(swedoptions[oneid].vertical+'_'+swedoptions[oneid].horizontal);
    }
    if (swedoptions[oneid].fixed == '2') { // toolbox
        els.style.marginLeft = 'auto';
        if (toolbar) toolbar.insertBefore(els,pos);
    }
    if (swedoptions[oneid].fixed == '3') { // editor xtd buttons
        if (xtdbuttons) xtdbuttons.appendChild(els); 
    }
    if (swedoptions[oneid].fixed == '4') { // in tabs list
        tablist = document.querySelector('[role="tablist"]');
        if (tablist) tablist.appendChild(els); 
    }
    
	els.addEventListener('change', function (ev) {
		if (systemmsg && (swedoptions[oneid].auto == 1) ) {
			swe = document.createElement('div');
			swe.innerHTML = '<joomla-alert type="warning" role="alert" style="animation-name: joomla-alert-fade-in;"><div class="alert-heading"><span class="visually-hidden">info</span></div><div class="alert-wrapper"><div class="alert-message">'+swedoptions[oneid].automsg+'<span class="switching"></span></div></div></joomla-alert>';
			systemmsg.appendChild(swe);
		}
		var csrf = Joomla.getOptions("csrf.token", "");
		var url = "?"+csrf+"=1&option=com_ajax&module=switcheditor&adEditor="+ev.srcElement.value+"&task=switch&format=json";
		Joomla.request({
			method : 'POST',
			url : url,
			onSuccess: function(data, xhr) {
				if (swedoptions[oneid].auto == 1) doSave();
			},
			onError: function(message) {console.log(message.responseText)}
		}) 
	});
}

function doSave() {
	let articletext = document.querySelector('#jform_articletext');
	let moduletext = document.querySelector('#jform_content');
	let title = document.querySelector('#jform_title');
	if (articletext && title.value) { // article + title ok
		Joomla.submitbutton('article.apply', null, true);
		return;
	} 
	if (moduletext && title.value )  { // module + title ok
		Joomla.submitbutton('module.apply', null, true);
		return;
	}
	systemmsg.removeChild(box);
}
})