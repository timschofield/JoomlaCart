/**
 * @version SVN: $Id: ziper.js 389 2011-05-10 23:49:53Z elkuku $
 * @package EasyCreator
 * @subpackage Javascript
 * @author Nikolai Plath {@link http://www.nik-it.de}
 * @author Created on 11-Oct-2009
 * @license GNU/GPL, see JROOT/LICENSE.php
 */

function deleteZipFile(path, file) {
	url = 'index.php?option=com_easycreator&tmpl=component&format=raw';
	url += '&controller=ziper&task=delete';
	url += '&file_path=' + path;
	url += '&file_name=' + file;

	var box = $('ajaxMessage');
	var debug = $('ajaxDebug');

	switch (ECR_JVERSION) 
	{
		case '1.5':
			var fx = box.effects( {
				duration : 1000,
				transition : Fx.Transitions.Quart.easeOut
			});
			break;
		case '1.6':
			var fx = new Fx.Morph(box, {});
			break;
		default:
			alert(jgettext('Undefined Joomla! version: %s', ECR_JVERSION));
			break;
	}// switch

	new Request({
		url: url,

		onRequest : function() {
			box.innerHTML = jgettext('Deleting...');
		},

		onComplete : function(response) {
			resp = Json.evaluate(response);

			box.set('text', resp.message);

			box.style.color = 'green';

			if (resp.status) {
				box.style.color = 'red';
				debug = resp.debug;
				
				return;
			} else {
				$('row' + file).setStyle('display', 'none');
			}

			fx.start( {}).chain(function() {
				this.start.delay(1000, this, {
					'opacity' : 0
				});
			}).chain(function() {
				box.style.display = "none";
				this.start.delay(100, this, {
					'opacity' : 1
				});
			});
		}
	}).send();
}//function
