/**
 * @version    $Id$
 * @package    Switch Editor
 * @subpackage mod_switcheditor
 * @copyright  Copyright (C) 2012 Anything Digital. All rights reserved.
 * @copyright  Copyright (C) 2008 Netdream - Como,Italy. All rights reserved.
 * @license    GNU/GPLv2
 */

window.addEvent('load', function() {
	Array.each($$('.adEditor'), function(el, idx) {
		var fx = new Fx.Morph(el, {duration:200, wait:false});
		el.addEvent('change', function(ev) {
			fx.cancel().start('.adEditorBusy');
			var p = el.getParent();
			var req = new Request.HTML({
				url: p.get('action')
			,	onSuccess: function() {
					fx.cancel().start('.adEditor');
				}
			}).post(p);
		});
	});
});
