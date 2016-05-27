<?php
if (isset($system_level)) $_SESSION['system_level'] = $system_level;
if (!isset($_SESSION['system_level'])) $_SESSION['system_level'] = '';

if ($_SESSION['dims']['action'] == 'admin' && ($_SESSION['system_level'] == dims_const::_SYSTEM_WORKSPACES || $_SESSION['system_level'] == dims_const::_SYSTEM_GROUPS))
{
	?>
	function system_showgroup(typetree, gid, str) {
		if (typetree=="workspaces") elt = dims_getelem('w'+gid+'_plus');
		else elt = dims_getelem('g'+gid+'_plus');

		if (elt.innerHTML.indexOf('plusbottom') != -1) elt.innerHTML = elt.innerHTML.replace('plusbottom', 'minusbottom');
		else  if (elt.innerHTML.indexOf('minusbottom')  != -1) elt.innerHTML = elt.innerHTML.replace('minusbottom', 'plusbottom');
		else  if (elt.innerHTML.indexOf('plus')  != -1) elt.innerHTML = elt.innerHTML.replace('plus', 'minus');
		else  if (elt.innerHTML.indexOf('minus')  != -1) elt.innerHTML = elt.innerHTML.replace('minus', 'plus');

		if (typetree=="workspaces") chobj = "w"+gid;
		else chobj = "g"+gid;

		if (elt = dims_getelem(chobj)) {
			if (elt.style.display == 'none') {
				if (elt.innerHTML.length < 20)
					dims_xmlhttprequest_todiv('admin-light.php','op=xml_detail_group&typetree='+typetree+'&gid='+gid+'&str='+str,'',chobj);

				dims_getelem(chobj).style.display='block';
			}
			else {
				dims_getelem(chobj).style.display='none';
			}
		}
	}
	<?
}
?>

function system_ticket_display(ticket_id,opened, isroot) {
	dims_xmlhttprequest('admin.php','op=ticket_open&ticket_id='+ticket_id);
}


function system_showheading(hid,str)
{
	elt = dims_getelem(hid+'_plus');
	if (elt.innerHTML.indexOf('plusbottom') != -1) elt.innerHTML = elt.innerHTML.replace('plusbottom', 'minusbottom');
	else  if (elt.innerHTML.indexOf('minusbottom')  != -1) elt.innerHTML = elt.innerHTML.replace('minusbottom', 'plusbottom');
	else  if (elt.innerHTML.indexOf('plus')  != -1) elt.innerHTML = elt.innerHTML.replace('plus', 'minus');
	else  if (elt.innerHTML.indexOf('minus')  != -1) elt.innerHTML = elt.innerHTML.replace('minus', 'plus');

	if (elt = dims_getelem(hid))
	{
		if (elt.style.display == 'none')
		{
			if (elt.innerHTML.length < 20) dims_getxmlhttp('<? echo dims::getInstance()->getScriptEnv(); ?>','xml_detail_heading','&hid='+hid+'&str='+str,'0',hid);
			dims_getelem(hid).style.display='block';
		}
		else
		{
			dims_getelem(hid).style.display='none';
		}
	}
}

function system_group_validate(form) {
	if (dims_validatefield("<? echo $_DIMS['cste']['_DIMS_LABEL']; ?>",form.group_label,"string")) return(true);

	return(false);
}

function system_workspace_validate(form) {
	if (dims_validatefield("<? echo $_DIMS['cste']['_DIMS_LABEL']; ?>",form.workspace_label,"string")) return(true);

	return(false);
}

function system_report_switch_style(tagid, bgcolor) {
	var tag = document.getElementById('tag_'+tagid);
	var seltag = document.getElementById('seltag_'+tagid);

	color = new dims_rgbcolor(bgcolor);
	rgbcolor = color.toRGB();

	if (tag.style.backgroundColor == rgbcolor) {
		tag.style.backgroundColor = '';
		seltag.checked = false;
	} else {
		tag.style.backgroundColor = rgbcolor;
		seltag.checked = true;
	}
}

function field_validate(form) {
	form.field_values.value = '';

	t = form.field_type;
	if (t.value == 'select' || t.value == 'radio' || t.value == 'checkbox')
	{
		for (i=0;i<form.f_values.length;i++)
		{
			if (form.field_values.value != '') form.field_values.value += '||';
			form.field_values.value += form.f_values[i].value;
		}
	}
	else if (t.value == 'tablelink') form.field_values.value = form.f_formfield.value;

	if (dims_validatefield('<? echo $_DIMS['cste']['_DIMS_LABEL']; ?>',form.field_name,"string"))
		return(true);

	return(false);
}

function add_value(lst,val)
{
	if (val.value != '')
	{
		if ((verifcolor && dims_validatefield('couleur', val, 'color')) || !verifcolor)
		{
			if (verifcolor)
			{
				color = new dims_rgbcolor(val.value);
				rgbcolor = color.toHex();
				lst.options[lst.length] = new Option('', rgbcolor);
				lst.options[lst.length-1].style.backgroundColor = rgbcolor;
			}
			else lst.options[lst.length] = new Option(val.value, val.value);

		}
	}
	val.value = '';
	val.focus();
}

function modify_value(lst,val)
{
	if ((verifcolor && dims_validatefield('couleur', val, 'color')) || !verifcolor)
	{
		sel = lst.selectedIndex;
		if (sel>-1)
		{
			if (verifcolor)
			{
				color = new dims_rgbcolor(val.value);
				rgbcolor = color.toHex();
				lst.options[sel].value = rgbcolor;
				lst.options[sel].text = '';
				lst.options[sel].style.backgroundColor = color.toHex();
			}
			else
			{
				lst.options[sel].value = val.value;
				lst.options[sel].text = val.value;
			}
		}
	}
	val.focus();
}

function delete_value(lst)
{
	sel = lst.selectedIndex;

	if (sel < lst.length-1)
	{
		lst[sel] = lst[sel+1];
		lst.selectedIndex = sel;
	}
	else lst.length--;
}

function move_value(lst,mv)
{
	sel = lst.selectedIndex;
	if (sel-mv>=0 && sel-mv< lst.length)
	{
		var tmp;
		tmp = lst[sel-mv].value;

		if (verifcolor)
		{
			lst[sel-mv].value = lst[sel].value;
			lst[sel-mv].style.backgroundColor = lst[sel-mv].value;

			lst[sel].value = tmp;
			lst[sel].style.backgroundColor = lst[sel].value;
		}
		else
		{
			lst[sel-mv].text = lst[sel].value;
			lst[sel-mv].value = lst[sel].value;

			lst[sel].text = tmp;
			lst[sel].value = tmp;
		}
		lst.selectedIndex=lst.selectedIndex-mv;
	}
}
