<?
if (!isset($op)) $op = '';
dims_init_module('forms');
?>

var verifcolor = false;

function forms_validate(form) {
	if (dims_validatefield('<? echo $_DIMS['cste']['_DIMS_LABEL_LABEL']; ?>',form.forms_label,"string"))
	if (dims_validatefield('<? echo $_DIMS['cste']['_FORMS_PUBDATESTART']; ?>',form.forms_pubdate_start,"emptydate"))
	if (dims_validatefield('<? echo $_DIMS['cste']['_FORMS_PUBDATEEND']; ?>',form.forms_pubdate_end,"emptydate"))
		return(true);

	return(false);
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

	if (dims_validatefield('<? echo $_DIMS['cste']['_FORMS_FIELD_NAME']; ?>',form.field_name,"string"))
		return(true);

	return(false);
}

window['add_value'] = function add_value(lst,val)
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

