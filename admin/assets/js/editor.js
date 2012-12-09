/**
 * @version SVN: $Id: editor.js 389 2011-05-10 23:49:53Z elkuku $
 * @package EasyCreator
 * @subpackage Javascript
 * @author Nikolai Plath {@link http://www.nik-it.de}
 * @author Created on 03-Mar-2008
 * @license GNU/GPL, see JROOT/LICENSE.php
 */

function save_file() {
	url = 'index.php?option=com_easycreator&tmpl=component&format=raw';
	url += '&controller=ajax&task=save';

	code = encodeURIComponent(editAreaLoader.getValue('ecr_code_area'));

	post = '';
	post += 'file_path=' + $('file_path').value;
	post += '&file_name=' + $('file_name').value;
	post += '&c_insertstring=' + code;

	var box = $('ecr_status_msg');
	var title = $('ecr_title_file');

	switch (ECR_JVERSION) {
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
		alert('Undefined JVersion '.ECR_JVERSION);
		break;
	}// switch

	new Request({
		url: url,

		'onRequest' : function() {
			oldTitle = $('ecr_title_file').innerHTML;

			title.innerHTML = jgettext('Saving...');
			title.addClass('ajax_loading16-red');
		},

		'onComplete' : function(response) {
			resp = Json.evaluate(response);

			title.innerHTML = oldTitle;
			title.removeClass('ajax_loading16-red');

			box.innerHTML = resp.text;
			box.style.display = "inline";

			if (resp.status) {
				box.addClass('img icon-16-cancel');
				box.style.color = 'red';
			} else {
				box.removeClass('img icon-16-cancel')
				box.addClass('img icon-16-apply');
				box.style.color = 'green';
			}

			$('ajaxDebug').innerHTML = resp.debug;

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
		},

		'onFailure' : function(item) {
			alert(item.responseText);
		}
	}).send(post);
}//function
