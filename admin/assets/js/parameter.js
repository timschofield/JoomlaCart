/**
 * @version $Id: parameter.js 245 2010-12-10 07:54:28Z elkuku $
 * @package    EasyCreator
 * @subpackage Javascript
 * @author     Nikolai Plath {@link http://www.nik-it.de}
 * @author     Created on 20-Sep-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

/*
Struktur::
<div 'divParameters'>
	<div 'div-group-GROUPNAME'>
		<div 'div-PARAMETERNAME'>
*
*
*/

/**
 * Parameter definition Object[name] = array(extra fields)
 */
var paramTypes = new Object();
paramTypes['calendar']		= ['format'];
paramTypes['category']		= ['class', 'section', 'scope'];
paramTypes['editors']		= [];
paramTypes['filelist']		= ['directory', 'filter', 'exclude', 'hide_none', 'hide_default', 'stripext'];
paramTypes['folderlist']	= ['directory', 'filter', 'exclude', 'hide_none', 'hide_default'];
paramTypes['helpsites']		= [];
paramTypes['hidden']		= ['class'];
paramTypes['imagelist']		= ['directory', 'filter', 'exclude', 'hide_none', 'hide_default', 'stripext'];
paramTypes['languages']		= ['client'];
paramTypes['list']			= [];
paramTypes['menu']			= [];
paramTypes['menuitem']		= ['state'];
paramTypes['password']		= ['class', 'size'];
paramTypes['radio']			= [];
paramTypes['section']		= [];
paramTypes['spacer']		= [];
paramTypes['sql']			= ['query', 'key_field', 'value_field'];
paramTypes['text']			= ['class', 'size'];
paramTypes['textarea']		= ['rows', 'cols', 'class'];
paramTypes['timezones']		= [];
paramTypes['usergroup']		= ['class', 'multiple', 'size'];

/*
 * So geht's ;)
 * 
 * for (var name in paramTypes) { //--alert("My " + name + "'s name is " +
 * paramTypes[name]); paramTypes[name].each(function(item) { alert(item); }); }
 * 
 */

if(FBPresent) console.log (paramTypes);

// --define the fields
var defaultFields = new Array('label', 'default', 'description');
var optionalFields = new Array(
	'class', 'client', 'cols', 'directory', 'exclude', 'filter', 'format'
	, 'hide_none', 'hide_default', 'key_field', 'multiple', 'query'
	, 'rows', 'scope', 'section', 'size', 'state', 'stripext', 'value_field');

var NL = "\n";
var jscnt_spacers = 0;

// var paramSliders = new Array();
// var sliderCount = 0;

function addGroup(name)
{
	html = "<div id='div-group-"+name+"'><h1>"+name+"</h1></div>";
	$('divParameters').innerHTML += html;
}//function

function newParameter()
{
	ctrlName = $('addParamName').value
	ctrlGroup = $('addParamGroup').value
	if( ctrlName == '' )
	{
		$('addParamMessage').innerHTML = '<div style="color: red;">'+jgettext('You must provide a name') + '</div>';
		var div = $('addParamMessage').setStyles({
			display:'block',
			opacity: 0
		});
		new Fx.Style(div, 'opacity', {duration: 1500} ).start(1);
		$('addParamName').focus();
		div_new_parameter.slideIn();
		return;
	}
	else
	{
		attribs = {"name":""+ctrlName+"","label":"","default":"","description":"","type":"text"};
		children = {};
		startParam(ctrlGroup, attribs, children);
		$('addParamMessage').innerHTML = '';
		$('addParamName').value = '';
		div_new_parameter.slideOut();
	}
}//function

function startParam(groupName, attribs, children)
{
	if(FBPresent)	console.log('start...',groupName, attribs, children);
	
	tName = 'div_'+attribs.name;
	if( attribs.type == 'spacer' )
	{
		tName += 'spc'+jscnt_spacers;
		jscnt_spacers ++;
	}
	
	html = "";
	html += "<table><tr><td>";
// html += NL+"<div class='ecr_toggle' id='"+tName+"-toggler'
// onclick='paramSliders["+sliderCount+"].toggle();'>";
	html += NL+"<div class='ecr_toggle' id='"+tName+"-toggler'>";
	html += NL+"<span id='"+attribs.name+"-type' style='color: blue;'>"+attribs.type+"</span>&nbsp:&nbsp;";
	html += NL+"<span id='"+tName+"-name'>"+attribs.name+"</span>";
	html += NL+"</div>";
	html += NL+"</td></tr><tr><td>";
	html += "<div class='moofx-slider' id='"+tName+"-slider'>";
	html += "<table style='border: 1px dotted black'>"+NL;
	html += "	<tr valign='top'>"+NL;
	html += "		<td>"+NL;
	html += "			<table>"+NL;
	html += "				<tr>"+NL;
	html += "					<td>type</td>"+NL;
	html += "					<td>";

	html += 						drawTypeSelector(groupName, attribs.name, attribs.type);

	html += "						Name";// TODO: <?php echo
                                            // JText::_('Name'); ?>";
	html += NL+"<input type='text' name='params["+groupName+"]["+attribs.name+"][name]' id='namefield_"+attribs.name+"' value='"+attribs.name+"' onkeyup=\"$('"+tName+"-name').innerHTML=$('namefield_"+attribs.name+"').value;\"/>";
	html += "					</td>"+NL;
	html += "				</tr>"+NL;

	// --draw default fields--('label', 'default', 'description')
	for ( var i=0, len=defaultFields.length; i<len; ++i )
	{
		html += "			<tr>"+NL;
		html += "				<td>"+defaultFields[i]+"</td>"+NL;
		html += "				<td>"+NL;
		tValue =( attribs[defaultFields[i]] == undefined ) ? '' : attribs[defaultFields[i]];
		html += "<input type='text' name='params["+groupName+"]["+attribs.name+"]["+defaultFields[i]+"]' value='"+tValue+"' />";
		html += "				</td>"+NL;
		html += "			</tr>"+NL;
	}// for

	// --draw extra fields--(paramTypes)
	for ( var i=0, len=optionalFields.length; i<len; ++i )
	{
		html += "			<tr id='"+attribs.name+"-optional-"+optionalFields[i]+"'>"+NL;
		html += "				<td>"+optionalFields[i]+"</td>"+NL;
		html += "				<td>"+NL;
		tValue =( attribs[optionalFields[i]] == undefined ) ? '' : attribs[optionalFields[i]];
		html += "<input type='text' name='params["+groupName+"]["+attribs.name+"]["+optionalFields[i]+"]' value='"+tValue+"' />";
		html += "				</td>"+NL;
		html += "			</tr>"+NL;
	}// for

	html += "			</table>"+NL;
	html += "		</td>"+NL;
	html += "		<td>"+NL;

	// --options panel
	html += "			<table id='"+attribs.name+"-divOption' style='border: 1px solid blue;'>"+NL;
	html += "				<tr>";
	html += "					<th>Options</th>";
	html += "				<td class='ecr_button img icon-16-add' onclick=\"addOption('"+groupName+"', '"+attribs.name+"', '', '');\">";
	html += "				Add option";
	html += "				<input type='hidden' name='"+attribs.name+"-options' id='"+attribs.name+"-options' value='"+i+"' />";
	html += "				</td>";
	html += "				</tr>"+NL;
	i = 0;

	// --draw the children
	for( x in children)
	{
		childName = "params["+groupName+"]["+attribs.name+"][children]["+i+"]";
		html += "			<tr>"+NL;
		html += "				<td>value"+NL;
		html += "					<input type='text' name='"+childName+"[value]' id='"+childName+"[value]' value='"+x+"' />";
		html += "				</td>"+NL;
		html += "				<td>data"+NL;
		html += "					<input type='text' name='"+childName+"[data]' value='"+children[x]+"' />";
		html += "				</td>"+NL;
		html += "			</tr>"+NL;
		i++;
	}// for

	if( i > 0 ) i -= 1;
	html += "			</table>"+NL;
	html += "		 </td>"+NL;
	html += "	 </tr>"+NL;
	html += "</table>"+NL;
	html += "</div>";
	html += "</td></tr></table>";
	
	// --add the whole thingy to the page.. TODO other option ???
	$("div-group-"+groupName).innerHTML += html;
	// tName+"-slider
// paramSliders[sliderCount] = new Fx.Slide(tName+'-slider');
// paramSliders[sliderCount].hide();
// sliderCount ++;
	// --show or hide panels
	switchType(attribs.name, attribs.type);
	
	
// new Accordion(
// $('div_'+attribs.name+'-toggler')
// , $('div_'+attribs.name+'-slider')
// , {
// onActive: function(toggler, i)
// {
// toggler.addClass('moofx-toggler-down');
// }
// ,onBackground: function(toggler, i)
// {
// toggler.removeClass('moofx-toggler-down');
// }
// ,duration: 300
// ,opacity: false
// , alwaysHide:true
// , show: 1
// });
	
	
// if(FBPresent) console.log(html);
}//function

function addOption(groupName, ctrlName, value, data)
{
if(FBPresent)console.log('add option: ', groupName, ctrlName, value, data);

	// --get index from hidden form field
	num = parseInt($(ctrlName+'-options').value);
	num += 1;
	// --write index to hidden form field
	$(ctrlName+'-options').value = num;
	
	optionName = "params["+groupName+"]["+ctrlName+"][children]["+num+"]";

	html = '';
	html += "			<tr>"+NL;
	html += "				<td>value"+NL;
	html += "					<input type='text' name='"+optionName+"[value]' value='"+value+"' />";
	html += "				</td>"+NL;
	html += "				<td>data"+NL;
	html += "					<input type='text' name='"+optionName+"[data]' value='"+data+"' />";
	html += "				</td>"+NL;
	html += "			</tr>"+NL;

	$(ctrlName+"-divOption").innerHTML += html;
// console.log(html);
}//function

function switchType(ctrlName, type)
{
if(FBPresent)console.log('switchType:'+ctrlName+'---'+type);
	found = true;
	switch (type)
	{
		case 'spacer':
			fadeOut(ctrlName+'-divOption');
			$('namefield_'+ctrlName).value="@spacer";
			$('namefield_'+ctrlName).disabled="disabled";
	// $(ctrlName+'-name').innerHTML = "@spacer";
		break;
		
		case 'calendar':
		case 'category':
		case 'editors':
		case 'filelist':
		case 'folderlist':
		case 'helpsites':
		case 'hidden':
		case 'imagelist':
		case 'languages':
		case 'menu':
		case 'menuitem':
		case 'password':
		case 'section':
		case 'sql':
		case 'text':
		case 'textarea':
		case 'timezones':
		case 'usergroup':
			fadeOut(ctrlName+'-divOption');
			$('namefield_'+ctrlName).disabled="";
		break;

		case 'radio':
		case 'list':
			fadeIn(ctrlName+'-divOption');
			$('namefield_'+ctrlName).disabled="";
		break;

		default:
			alert('UNDEFINED TYPE: '+type);
			if(FBPresent) console.error('UNDEFINED TYPE: '+type);
			found = false;
		break;
	}// switch

	if(found)
	{
		optionalFields.each(function(item) {
			$(ctrlName+"-optional-"+item).setStyles({ display:'none' })
		});
		
		paramTypes[type].each(function(item) {
			$(ctrlName+"-optional-"+item).setStyles({ display:'table-row' });
		});
		$(ctrlName+'-type').innerHTML = type;
	}// if found
	
}//function

function fadeIn(ctrl)
{
if(FBPresent)console.log('fadeIn',ctrl);
	var div = $(ctrl).setStyles({
		display:'block'
// opacity: 0
	});
// new Fx.Style(div, 'opacity', {duration: 1000} ).start(1);
}//function

function fadeOut(ctrl)
{
if(FBPresent)console.log('fadeOut',ctrl);
/*
 * not working...gave up ;( - HELP !! :D
 */	
// ;(
	var div = $(ctrl);
	
	div.setStyles({
		display:'none'
// opacity: 0
	});
// new Fx.Style(div, 'opacity', {duration: 1000} ).start(1);
	
}//function

function drawTypeSelector(groupName, ctrlName, selected)
{
	html = "";
	js = " onchange='switchType(\""+ctrlName+"\", this.value);'";
	html +=	NL+"<select name='params["+groupName+"]["+ctrlName+"][type]'"+js+">";

// for(type in paramTypes)
	for( var xtype in paramTypes ) 
		{
		sSelected = '';
		if( xtype == selected )
		{
			sSelected = " selected='selected'";
		}

		html +=	"<option"+sSelected+">"+xtype+"</option>";
	}// for

	html +=	"</select>";
	
	// console.log(html);
	return html;
}//function

function drawLine( div, ctrlName, option, field, value )
{
	var ni = document.getElementById('divParameters');
	var numi = document.getElementById('totalParameters');
	var num = (document.getElementById("totalParameters").value -1)+ 2;
	numi.value = num;
	var divIdName = "my"+num+"Div";
	var newdiv = document.createElement('tr');
	newdiv.setAttribute("id",divIdName);
	if( option == "" )
	{
		name = "params["+ctrlName+glueChar+field+"]";
	}
	else
	{
		name = "params["+ctrlName+glueChar+field+"]["+option+"]";
	}

	insertText = "";
	insertText += "<td>"+field+"</td>";
	insertText += "<td><input type=\"text\" name=\""+name+"\" value=\""+value+"\" /></td>";

// function drawLine($ctrlName, $option, $field, $value, $glueChar)

  newdiv.innerHTML = insertText;
  // "<td><input type=\"text\" name=\"copys["+num+"][source]\" size=\"60\"
    // value=\""+txtsource+"\" /></td><td><input type=\"text\"
    // name=\"copys["+num+"][dest]\" size=\"30\" value=\""+txtdest+"\"
    // /></td><td><a href=\"javascript:;\"
    // onclick=\"removeElement(\'"+divIdName+"\',
    // 'divCopys')\">Remove</a></td>";
  ni.appendChild(newdiv);
}//function

function removeElement(row, table)
{
  var d = document.getElementById(table);
  var toremove = document.getElementById(row);
  d.removeChild(toremove);
}//function

function xxremoveElement(divNum, divName)
{
  var d = document.getElementById(divName);
  var olddiv = document.getElementById(divNum);
  d.removeChild(olddiv);
}//function
