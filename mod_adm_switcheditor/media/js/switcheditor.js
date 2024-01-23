/**
 * @package    Switch Editor
 * @subpackage mod_switcheditor
 * @copyright  Copyright (C) 2024 ConseilGouz. All rights reserved.
 * From anything-digital.com Switch Editor
 * @license    GNU/GPLv2
 */

document.addEventListener("DOMContentLoaded", function(){

const els = document.querySelectorAll('.adEditor');	
for (let i = 0; i < els.length; i++) {
	els[i].addEventListener('change', function (ev) {
			var csrf = Joomla.getOptions("csrf.token", "");
				url = "?"+csrf+"=1&option=com_ajax&module=switcheditor&adEditor="+ev.srcElement.value+"&task=switch&format=json";
				Joomla.request({
					method : 'POST',
					url : url,
					onSuccess: function(data, xhr) {
					},
					onError: function(message) {console.log(message.responseText)}
					}) 
		});
	}
})