/**
 * @version $Id: menu.js 18 2010-11-08 01:10:19Z elkuku $
 * @package    EasyCreator
 * @subpackage Javascript
 * @author     Nikolai Plath {@link http://www.nik-it.de}
 * @author     Created on 16-Oct-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

/* Standard Joomla! menu images - css class */
var stdMenuImgs = new Array('archive', 'article', 'category', 'checkin',
        'component', 'config', 'content', 'cpanel', 'default', 'frontpage',
        'help', 'info', 'install', 'language', 'logout', 'massmail', 'media',
        'menu', 'menumgr', 'messages', 'module', 'plugin', 'section', 'static',
        'stats', 'themes', 'trash', 'user ');

function addSubmenu(text, link, image, ordering, menuid, parent)
{
    var ni = $('divSubmenu');
    var numi = $('totalSubmenu');
    var num = ($('totalSubmenu').value - 1) + 2;
    
    numi.value = num;
    
    var divIdName = 'submenu'+num+'Div';
    var newdiv = document.createElement('div');
    
    newdiv.setAttribute('id', divIdName);

    html = '';
    html += '<div style="border-bottom: 1px solid gray; margin-bottom: 0.5em;">';
    html += '<input type="hidden" name="submenu['+num+'][menuid]" value="'+menuid+'" />';
    
    if (parent)
    {
        html += "<input type=\"hidden\" name=\"submenu[" + num
                + "][parent]\" value=\"" + parent + "\" />";
    }

    html += "<span class=\"ecr_label2\">" + jgettext('Text')
            + "</span><input type=\"text\" name=\"submenu[" + num
            + "][text]\" size=\"15\" value=\"" + text
            + "\" style=\"font-size: 1.3em;\" />";
    html += '<span class="ecr_label2">' + jgettext('Link') + '</span>';
    html += "<input type=\"text\" name=\"submenu[" + num
            + "][link]\" size=\"25\" value=\"" + link + "\" />";
    html += '<span class="ecr_label2">' + jgettext('Ordering') + '</span>';
    html += '<input type="text" name="submenu['+num+'][ordering]" size="2" value="'+ordering+'" />';
    html += '<br />';
    html += '<br />';
    html += "<span class=\"ecr_label2\">" + jgettext('Image') + "</span>";
    html += "<div id=\"menuPic-" + num + "\" style=\"display: inline;\"></div>";
    html += "<div id=\"prev-" + num + "\" style=\"display: inline;\"></div>";
    html += "<input type=\"text\" name=\"submenu[" + num
            + "][img]\" size=\"30\" value=\"" + image + "\" id=\"img-" + num + "\" />";
    html += "<div style=\"float: right\" class=\"ecr_button img icon-16-delete\" onclick=\"removeElement(\'"
        + divIdName + "\', 'divSubmenu')\">" + jgettext('Delete') + "</div>";
    html += '<br />';
    html += '</div>';

    newdiv.innerHTML = html;
    ni.appendChild(newdiv);
    drawPicChooser(num, image);
}//function

function chgMenuPic(num)
{
    if (num == undefined)
    {
        num = '';
    }

    selection = $('opt-' + num).value;
    
    switch (selection)
    {
        case '':
            $('img-' + num).value = '';
            $('img-' + num).readOnly = true;
            $('prev-' + num).setAttribute('class', '');
            break;
    
        case 'user_defined':
            $('img-' + num).readOnly = false;
            $('prev-' + num).setAttribute('class', '');
            break;
    
        default:
            $('img-' + num).value = selection;
            $('img-' + num).readOnly = true;
            $('prev-' + num).setAttribute('class', 'img icon-16-' + selection);
            break;
    }// switch
}//function

function drawPicChooser(num, selectedImage)
{
    html = '';
    html += "<select name=\"opt-" + num + "\" id=\"opt-" + num
            + "\" onchange=\"chgMenuPic(" + num + ");\">";
    html += '<option value=\"\">' + jgettext('Select...') + '</option>';
    found = false;
    
    for ( var i = 0; i <= stdMenuImgs.length - 1; i++)
    {
        selected = '';
        
        if (selectedImage == stdMenuImgs[i])
        {
            selected = ' selected=\"selected\"';
            found = true;
        }

        html += "<option" + selected + " class=\"img icon-16-"+ stdMenuImgs[i]+"\">" + stdMenuImgs[i] + "</option>";
    }

    selected = '';
    
    if (selectedImage != '' && found == false)
    {
        selected = ' selected=\"selected\"';
    }

    html += '<option value=\"user_defined\"' + selected + '>'
            + jgettext('User defined') + '</option>';
    html += "</select>";

    $('menuPic-' + num).innerHTML = html;
    chgMenuPic(num);
}//function
