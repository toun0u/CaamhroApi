dims_window_onload_functions = new Array();

function switchElementDisplay(id) {
	var elem	= dims_getelem(id);
	var img		= dims_getelem('img'+id);

	if (elem.style.display=="block") {
		elem.style.display		= "none";
		elem.style.visibility	= "hidden";
		img.src					= "./common/img/plus.gif";
	}else {
		elem.style.display		= "block";
		elem.style.visibility	= "visible";
		img.src					= "./common/img/minus.gif";
	}

}

function dims_window_onload_stock(func) {
	dims_window_onload_functions[dims_window_onload_functions.length] = func;
}

function dims_window_onload_launch()
{
	window.onload = function() {
		for(i = 0; i < dims_window_onload_functions.length; i++) {
			dims_window_onload_functions[i]();
		}
	}
}

function dims_openwin(url,w,h,name) {
   var top = (screen.height-(h+60))/2;
   var left = (screen.width-w)/2;

   if(name == '') name = 'dimswin';
   dimswin=window.open(url,name,'top='+top+',left='+left+',width='+w+', height='+h+', status=no, menubar=no, toolbar=no, scrollbars=yes, resizable=yes, screenY=20, screenX=20');
   dimswin.focus();
}


function dims_confirmform(form, message) {
	if (confirm(message)) form.submit();
}

function dims_confirmlink(link, message) {
	if (confirm(message)) location.href=link;
}

function dims_switchstyle(obj, opacity) {
	obj.style.filter='alpha(opacity:'+(opacity)+')';
	obj.style.MozOpacity = opacity/100;
}

function dims_switchdisplay(id) {
	e = dims_getelem(id);
	if (e) e.style.display = (e.style.display == 'none') ? 'block' : 'none';
}

// FIXME : Les champs checkbox/radio ne sont pas detecté en requis
function dims_validatefield(field_label, field_object, field_type,objform) {
	var ok = true;
	var i;
	var nbpoint = 0;
	var msg = new String();
	var reg = new RegExp("<FIELD_LABEL>","gi");

	if (field_object) {

		field_value = field_object.value;
		if (field_type == 'selected' || field_type == 'select') {
			msg = lstmsg[9];
			ok = (field_object.selectedIndex > 0);
		}

		if (field_type == 'checked' || field_type == 'radio') {
			msg = lstmsg[9];
			ok = false;
						var checks = document.getElementsByName(field_object);

			for (c = 0; c < checks.length; c++) {
				if (checks[c].checked) ok = true;
			}
		}

		if (field_type == 'email') {

			var email = field_value;
			var aroba = email.indexOf("@");

			if (aroba == -1)
			{
				ok = false;
				msg = lstmsg[0];
			}

			if (ok)
			{
				var point = email.indexOf(".", aroba);
				if ((point == -1) || (point == (aroba + 1)))
				{
					ok=false;
					msg = lstmsg[1];
				}
			}

			if (ok)
			{
				var point = email.lastIndexOf(".");
				if ((point + 1) == email.length)
				{
					ok = false;
					msg = lstmsg[2];
				}
			}

			if (ok)
			{
				point = email.indexOf("..")
				if (point != -1)
				{
					ok = false;
					msg = lstmsg[3];
				}
			}
		}

		if (field_type == 'emptyemail')
		{
			if (field_value.length!=0)
			{
				var email = field_value;
				var aroba = email.indexOf("@");

				if (aroba == -1)
				{
					ok = false;
					msg = lstmsg[0];
				}

				if (ok)
				{
					var point = email.indexOf(".", aroba);
					if ((point == -1) || (point == (aroba + 1)))
					{
						ok=false;
						msg = lstmsg[1];
					}
				}

				if (ok)
				{
					var point = email.lastIndexOf(".");
					if ((point + 1) == email.length)
					{
						ok = false;
						msg = lstmsg[2];
					}
				}

				if (ok)
				{
					point = email.indexOf("..")
					if (point != -1)
					{
						ok = false;
						msg = lstmsg[3];
					}
				}
			}
		}

		if (field_type == 'color')
		{
			var color = new dims_rgbcolor(field_value);
			if (!color.ok)
			{
				ok = false;
				msg = lstmsg[10];
			}
		}

		if (field_type == 'string' || field_type == 'text')
		{
			if (field_value.replace(/(^\s*)|(\s*$)/g,'').length==0)
			{
				ok = false;
				msg = lstmsg[4];
			}
		}

		if (field_type == 'int')
		{
			if (field_value.length==0 || field_value.length>12) ok = false;
			for (i=0;i<field_value.length;i++)
			{
				if (field_value.charAt(i)<'0' || field_value.charAt(i)>'9') ok = false;
			}
			if (!ok) msg = lstmsg[5];
		}

		if (field_type == 'emptyint')
		{
			if (field_value.length>12) ok = false;
			for (i=0;i<field_value.length;i++)
			{
				if (field_value.charAt(i)<'0' || field_value.charAt(i)>'9') ok = false;
			}
			if (!ok) msg = lstmsg[5];
		}

		if (field_type == 'float')
		{
			if (field_value.length==0) ok = false;
			for (i=0;i<field_value.length;i++)
			{
				if (field_value.charAt(i)=='.' || field_value.charAt(i)==',') nbpoint++;
				else if (field_value.charAt(i)<'0' || field_value.charAt(i)>'9') ok = false;
			}
			if (nbpoint>1) ok = false;

			if (!ok) msg = lstmsg[6];
		}

		if (field_type == 'emptyfloat')
		{
			for (i=0;i<field_value.length;i++)
			{
				if (field_value.charAt(i)=='.' || field_value.charAt(i)==',') nbpoint++;
				else if (field_value.charAt(i)<'0' || field_value.charAt(i)>'9') ok = false;
			}
			if (nbpoint>1) ok = false;

			if (!ok) msg = lstmsg[6];
		}

		if (field_type == 'date')
		{
			var thedate = field_value.split("/");
			if (thedate.length != 3 || isNaN(parseInt(thedate[0])) || isNaN(parseInt(thedate[1])) || isNaN(parseInt(thedate[2]))) thedate = field_value.split("-");
			if (thedate.length != 3 || isNaN(parseInt(thedate[0])) || isNaN(parseInt(thedate[1])) || isNaN(parseInt(thedate[2]))) thedate = field_value.split(":");
			if (thedate.length != 3 || isNaN(parseInt(thedate[0])) || isNaN(parseInt(thedate[1])) || isNaN(parseInt(thedate[2]))) ok = false;
			if (ok)
			{
				var datetotest = new Date(eval(thedate[2]),eval(thedate[1])-1,eval(thedate[0]));
				var year = datetotest.getYear()
				if ((Math.abs(year)+"").length < 4) year = year + 1900
				ok = ((datetotest.getDate() == eval(thedate[0])) && (datetotest.getMonth() == eval(thedate[1])-1) && (year == eval(thedate[2])));
			}
			if (!ok) msg = lstmsg[7];
		}

		if (field_type == 'time')
		{
			if (field_value.length!=5) ok = false;
			else
			{
				h=field_value.substring(0,2);
				m=field_value.substring(3,5);
				if (parseInt(h)<0 || parseInt(h)>23) ok = false;
				if (parseInt(m)<0 || parseInt(m)>59) ok = false;
				madate=new Date(01,01,2000,h,m);
				if (madate=="NaN" || field_value.charAt(2)!=':') ok = false;
			}
			if (!ok) msg = lstmsg[8];
		}

		if (field_type=='emptydate')
		{
			if (field_value.length!=0)
			{
				var thedate = field_value.split("/");
				if (thedate.length != 3 || isNaN(parseInt(thedate[0])) || isNaN(parseInt(thedate[1])) || isNaN(parseInt(thedate[2]))) thedate = field_value.split("-");
				if (thedate.length != 3 || isNaN(parseInt(thedate[0])) || isNaN(parseInt(thedate[1])) || isNaN(parseInt(thedate[2]))) thedate = field_value.split(":");
				if (thedate.length != 3 || isNaN(parseInt(thedate[0])) || isNaN(parseInt(thedate[1])) || isNaN(parseInt(thedate[2]))) ok = false;
				if (ok)
				{
					var datetotest = new Date(eval(thedate[2]),eval(thedate[1])-1,eval(thedate[0]));
					var year = datetotest.getYear()
					if ((Math.abs(year)+"").length < 4) year = year + 1900
					ok = ((datetotest.getDate() == eval(thedate[0])) && (datetotest.getMonth() == eval(thedate[1])-1) && (year == eval(thedate[2])));
				}
				if (!ok) msg = lstmsg[7];
			}
		}

		if (field_type=='emptytime')
		{
			if (field_value.length!=0)
			{
				if (field_value.length!=5) ok = false;
				else
				{
					h=field_value.substring(0,2);
					m=field_value.substring(3,5);
					if (parseInt(h)<0 || parseInt(h)>23) ok = false;
					if (parseInt(m)<0 || parseInt(m)>59) ok = false;
					madate=new Date(01,01,2000,h,m);
					if (madate=="NaN" || field_value.charAt(2)!=':') ok = false;
				}
				if (!ok) msg = lstmsg[8];
			}
		}
	}
	else
	{
		ok = false;
	}

	if (!ok) {
				alert(msg.replace(reg,field_label));
		if (field_type != 'checked' && field_type != 'radio') {
			field_object.style.background = error_bgcolor;
			field_object.focus();
		}
	}

	return (ok);
}

function dims_checkall(form, mask, value, byid)
{
	var len = form.elements.length;
	var reg = new RegExp(mask,"g");

	if (isNaN(byid)) byid = false;

	for (var i = 0; i < len; i++)
	{
		var e = form.elements[i];
		if (byid) { if (e.id.match(reg)) e.checked = value; }
		else if (e.name.match(reg)) e.checked = value;
	}
}

var	timer_started = false;
var popup_displayed = false;
var	posx = 0;
var	positiony = 0;
var posy = 0;
var	msg = '';


function dims_showpopup_delayed(w,namepopup,h) {
	if (timer_started) {
		w = parseInt(w);
		if (h==null) h="auto";
		else h=parseInt(h)+'px';

		if (namepopup=="" || namepopup==null) namepopup="dims_popup";
		var dims_popup = dims_getelem(namepopup);

//		with (dims_popup.style)
//		{

			//dims_popup.style.display = 'none';
			//dims_popup.innerHTML = msg+' '+posx+','+posy;
			if (msg!="") dims_popup.innerHTML = msg;

			tmpleft = parseInt(posx) + 20;

			if(navigator.appName == 'Microsoft Internet Explorer')
				tmptop = parseInt(positiony);
			else
				tmptop = parseInt(posy);

			if (w > 0) dims_popup.style.width = w+'px';
			else w = parseInt(dims_popup.offsetWidth);

			dims_popup.style.height = h;

			if (20 + w + parseInt(tmpleft) > parseInt(document.body.offsetWidth)) {
				tmpleft =parseInt(document.body.offsetWidth) - w - 10;
			}

			//if (tmptop+200>parseInt(document.body.offsetHeight)) // && tmptop > 300)
			//	dims_popup.style.top = (tmptop-200)+'px';
			//else
			dims_popup.style.top = tmptop+'px';

			dims_popup.style.left = tmpleft+'px';
			dims_popup.style.display = 'block';
			dims_popup.style.visibility = 'visible';
			//dims_popup.style.overflow = "auto";
//		}

		popup_displayed = true;
		timer_started=false;
	}
}


function dims_addslashes(str)
{
	str = str.replace(/\\/g,"\\\\");
	str = str.replace(/\'/g,"\\'");
	str = str.replace(/\"/g,"\\\"");
	return(str);
}

function dims_redirect(url){
	parent.location = url;
}

function dims_showcenteredpopup(message,w,h,namepopup) {
	blockclosepopup=true;
	if (namepopup=="" || namepopup==null) namepopup="dims_popup";
	if (w=="") w=500;
	if (h=="") h=500;

	var msg = message;

	if( window.innerWidth) {
		largeur=window.innerWidth;
	}else{
		largeur=document.body.offsetWidth;
	}

   if( window.innerHeight) {
		hauteur=window.innerHeight;
	}
	else {
		if (w > 500)
			hauteur=document.body.offsetHeight/3;
		else
			hauteur=document.body.offsetHeight/2;
	}

	hcal=(hauteur-h)/2;
	if (hcal<0) hcal=100;

	posx=(largeur-w)/2;

	if (self.pageYOffset){
		posy = hcal+self.pageYOffset;
	}else if (document.documentElement && document.documentElement.scrollTop){
		posy = hcal+document.documentElement.scrollTop;
	}else if (document.body) {
		posy = hcal+document.body.scrollTop;
	}else{
		posy=hcal+window.scrollY;
	}

	if (posy<50) posy=50;
	positiony = posy ;

	if (!timer_started) {
		timer_started = true;
		setTimeout("dims_showpopup_delayed('"+w+"','"+namepopup+"')", 10);
	}
}

function dims_showpopup(message, w, e, origine,namepopup,decalx,decaly,decalfix_x,decalfix_y) {
	if (namepopup=="" || namepopup==null) namepopup="dims_popup";
	if (decalx=="" || decalx==null) decalx=0;
	else decalx=decalx*1;

	if (decaly=="" || decaly==null) decaly=0;
	else decaly=decaly*1;

	if (decalfix_x=="" || decalfix_x==null) decalfix_x=0;
	if (decalfix_y=="" || decalfix_y==null) decalfix_y=0;

	msg = message;

	if (!origine) var origine = '';

	if( window.innerWidth) {
		largeur=window.innerWidth;
	}
	else {
		largeur=document.body.offsetWidth;
	}
	if( window.innerHeight) {
		hauteur=window.innerHeight;
	}
	else {
		hauteur=document.body.offsetHeight;
	}

	if (!e) var e = window.event;

	if (e.pageX || e.pageY)	{
		posx = e.pageX;
		posy = e.pageY ;
	}
	else if (e.clientX || e.clientY) {
		posx = e.clientX + document.body.scrollLeft;
		posy = e.clientY + document.body.scrollTop;
	}


	/* calul de la position fixe du block */
	if (decalfix_x!=0) {
		if (decalfix_x<0) {
			// largeur - decalx
			posx=(largeur+decalfix_x)*1;
			posy-=50;
		}
		else {
			posx=decalfix_x;
		}
	}
	else if (decalx!=0) posx=posx+decalx;

   /* calul de la position fixe du block */
	if (decalfix_y!=0) {
		if (decalfix_y<0) {
			// largeur - decalx
			posy=(hauteur+decalfix_y)*1;
		}
		else {
			posy=decalfix_y;
		}
	}
	else if (decaly!=0) posy=posy+decaly;

	positiony = posy ;

	document.getElementById(namepopup).innerHTML="";
	if (origine == 'click') {
		timer_started = true;
		dims_showpopup_delayed(w,namepopup);
	}
	else {
		if (!timer_started) {
			timer_started = true;

			setTimeout("dims_showpopup_delayed('"+w+"','"+namepopup+"')", 10*timerdelay);
		}

		if (!popup_displayed) dims_showpopup_delayed(w,namepopup);
	}
}

function dims_movepopup(e,namepopup) {
	if (namepopup=="" || namepopup==null) namepopup="dims_popup";
	if (!e) var e = window.event;

	if (e.pageX || e.pageY)	{
		posx = e.pageX;
		posy = e.pageY;
	}
	else if (e.clientX || e.clientY) {
		posx = e.clientX + document.body.scrollLeft;
		posy = e.clientY + document.body.scrollTop;
	}

	if (popup_displayed) dims_showpopup_delayed(0,namepopup);
}

function dims_hidepopup(namepopup) {
	if (namepopup=="" || namepopup==null) namepopup="dims_popup";
	timer_started = false;
	popup_displayed = false;

	var dims_popup = (document.getElementById) ? document.getElementById(namepopup) : eval("document.all["+namepopup+"]");
	with (dims_popup.style) {
		visibility = 'hidden';
		display = 'none';
	}

	cur_idobject=-10;
	cur_idworkspace=-10;
	cur_idmodule = -10;
	cur_idrecord=-10;
}

function dims_sleep(t)
{
	setTimeout("dims_wakeup()", t*timerdelay);
}

function dims_wakeup() {}

function dims_getelem(elem, doc) {
	if (!doc) doc = document;
	return (doc.getElementById) ? doc.getElementById(elem) : eval("document.all['"+dims_addslashes(elem)+"']");
}

function dims_tickets_search_users() {
	clearTimeout(timerdisplayresult);
	timerdisplayresult = setTimeout("dims_tickets_search_users_exec()", 500);
}

function dims_tickets_search_users_exec() {
	clearTimeout(timerdisplayresult);
	timerdisplayresult = setTimeout("searchExecuteTag()", 500);
	filter=dims_getelem('ticket_search').value;
	if(filter != ""){
		dims_xmlhttprequest_todiv('index-light.php','dims_op=tickets_search_users&dims_ticket_userfilter='+filter,'','div_ticket_search_result');
	}else{
		dims_getelem('div_ticket_search_result').innerHTML = "";
	}
}

function dims_tickets_new(event, id_object, id_record, object_label) {
	var data = '';

	if (object_label) data += '&object_label='+object_label;
	if (id_object) data += '&id_object='+id_object;
	if (id_record) data += '&id_record='+id_record;

	dims_showpopup('',500,event,'click');dims_xmlhttprequest_todiv('admin.php','dims_op=tickets_new'+data,'','dims_popup');
}


function dims_gethttpobject(callback) {
	var xmlhttp = false;

	/* on essaie de cr�er l'objet si ce n'est pas d�j� fait */
	if (!xmlhttp && typeof XMLHttpRequest != 'undefined') {
		try {
			xmlhttp = new XMLHttpRequest();
		}
		catch (e) {
			xmlhttp = false;
		}
	}

	return xmlhttp;
}

/**
  * Envoie des donn�es � l'aide d'XmlHttpRequest?
  * @param string methode d'envoi ['GET'|'POST']
  * @param string url
  * @param string donn�es � envoyer sous la forme var1=value1&var2=value2...
  */
function dims_sendxmldata(method, url, data, xmlhttp, asynchronous) {
	if (!xmlhttp) {
		return false;
	}

	if(method == "GET") {
		if(data == 'null') {
			xmlhttp.open("GET", url, asynchronous);
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded;'); //charset=ISO-8859-15
		}
		else {
			xmlhttp.open("GET", url+"?"+data, asynchronous);
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded;');//charset=ISO-8859-15
		}
		xmlhttp.send(null);
	}
	else if(method == "POST") {
		xmlhttp.open("POST", url+"?"+data, asynchronous);

		xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded;'); //charset=ISO-8859-15
		xmlhttp.send(data);
	}
	return true;
}

function dims_xmlhttprequest(url, data, asynchronous, getxml) {
	if (isNaN(asynchronous)) asynchronous = false;
	if (isNaN(getxml)) getxml = false;

	xmlhttp = dims_gethttpobject();
	dims_sendxmldata('GET', url, data, xmlhttp, asynchronous);

	// if asynchronous = false => return request content
	if (getxml) return(xmlhttp.responseXML);
	else return(xmlhttp.responseText);
}

function dims_xmlhttprequest_post(url, data, asynchronous, getxml) {
	if (isNaN(asynchronous)) asynchronous = false;
	if (isNaN(getxml)) getxml = false;

	xmlhttp = dims_gethttpobject();
	dims_sendxmldata('POST', url, data, xmlhttp, asynchronous);

	// if asynchronous = false => return request content
	if (getxml) return(xmlhttp.responseXML);
	else return(xmlhttp.responseText);
}

function dims_xmlhttprequest_tofunction(url, data, callback, ticket, getxml) {
	var xmlhttp = dims_gethttpobject();

	if (isNaN(getxml)) getxml = false;

	if (xmlhttp) {
		/* on d�finit ce qui doit se passer quand la page r�pondra */
		xmlhttp.onreadystatechange=function() {
			if (xmlhttp.readyState == 4) {/* 4 : �tat "complete" */
				if (xmlhttp.status == 200) {/* 200 : code HTTP pour OK */
					if (getxml) callback(xmlhttp.responseXML,ticket);
					else {
						callback(xmlhttp.responseText,ticket);
					}
				}
			}
		}
	}
	return !dims_sendxmldata('GET', url, data, xmlhttp, true);
}

function dims_xmlhttprequest_todiv(url, data, sep) {
	var xmlhttp = dims_gethttpobject();
	var args;

	if (xmlhttp) {
		if (dims_xmlhttprequest_todiv.arguments!=null) {
			args = dims_xmlhttprequest_todiv.arguments;

			/* on d�finit ce qui doit se passer quand la page r�pondra */
			xmlhttp.onreadystatechange=function() {
				if (!isNaN(xmlhttp.readyState) && xmlhttp.readyState == 4) {/* 4 : �tat "complete" */
					if (xmlhttp.status == 200) {/* 200 : code HTTP pour OK */
						var tabxmlvalue = new Array();
						var result= xmlhttp.responseText;

						if (sep == '') tabxmlvalue[0] = result;
						else tabxmlvalue=result.split(sep);
						if (args.length!=null) {
							for(i=0;i<args.length-3;i++) {
								if (tabxmlvalue[i]) {
									dims_getelem(args[i+3]).innerHTML = tabxmlvalue[i];
									/* modify by pat to evaluate javascript */
									var x = dims_getelem(args[i+3]).getElementsByTagName("script");
									for(var j=0;j<x.length;j++) {
										if (x[j].text!="") {
											eval(x[j].text);
										}
									}
								}
								else dims_getelem(args[i+3]).innerHTML = '';
							}
						}
					}
				}
			}
		}
	}
	return !dims_sendxmldata('GET', url, data, xmlhttp, true);
}

function dims_xmlhttprequest_todivpost(url, data, sep,arg) {
	var xmlhttp = dims_gethttpobject();
	var args= new Array();

	args[0]=arg;

	if (xmlhttp) {
		//if (dims_xmlhttprequest_todiv.arguments!=null) {
			//args = dims_xmlhttprequest_todiv.arguments;
			//alert('la');
			/* on d�finit ce qui doit se passer quand la page r�pondra */
			xmlhttp.onreadystatechange=function() {
				if (!isNaN(xmlhttp.readyState) && xmlhttp.readyState == 4) {/* 4 : �tat "complete" */
					if (xmlhttp.status == 200) {/* 200 : code HTTP pour OK */

						var tabxmlvalue = new Array();
						var result= xmlhttp.responseText;

						if (sep == '') tabxmlvalue[0] = result;
						else tabxmlvalue=result.split(sep);
						if (args.length!=null) {
							for(i=0;i<args.length;i++) {
								if (tabxmlvalue[i]) {
									dims_getelem(args[i]).innerHTML = tabxmlvalue[i];
									/* modify by pat to evaluate javascript */
									var x = dims_getelem(args[i]).getElementsByTagName("script");
									for(var j=0;j<x.length;j++) {
										if (x[j].text!="") {
											eval(x[j].text);
										}
									}
								}
								else dims_getelem(args[i+3]).innerHTML = '';
							}
						}
					}
				}
			}
		//}
	}
	return !dims_sendxmldata('POST', url, data, xmlhttp, true);
}

//fonction dupliquée pour ne pas faire de bétise par rapport à la précédente, qui tient compte des arguments passés en param
function dims_xmlhttprequest_todivpostargs(url, data, sep) {
	var xmlhttp = dims_gethttpobject();
	var args;


	if (xmlhttp) {
		if (dims_xmlhttprequest_todivpostargs.arguments!=null) {
			args = dims_xmlhttprequest_todivpostargs.arguments;

			/* on définit ce qui doit se passer quand la page répondra */
			xmlhttp.onreadystatechange=function() {
				if (!isNaN(xmlhttp.readyState) && xmlhttp.readyState == 4) {/* 4 : état "complete" */
					if (xmlhttp.status == 200) {/* 200 : code HTTP pour OK */

						var tabxmlvalue = new Array();
						var result= xmlhttp.responseText;

						if (sep == '') tabxmlvalue[0] = result;
						else tabxmlvalue=result.split(sep);
						if (args.length!=null) {
							for(i=0;i<args.length -3;i++) {
								if (tabxmlvalue[i]) {
									dims_getelem(args[i+3]).innerHTML = tabxmlvalue[i];
									/* modify by pat to evaluate javascript */
									var x = dims_getelem(args[i+3]).getElementsByTagName("script");
									for(var j=0;j<x.length;j++) {
										if (x[j].text!="") {
											eval(x[j].text);
										}
									}
								}
								else dims_getelem(args[i+3]).innerHTML = '';
							}
						}
					}
				}
			}
		}
	}
	return !dims_sendxmldata('POST', url, data, xmlhttp, true);
}

function dims_ajaxloader(div) {
	var ajaxloader = '<div style="text-align:center;padding:40px 10px;"><img src="./common/img/ajax-loader.gif"></div>';
	if (div && $(div)) $(div).innerHTML = ajaxloader;
	else return ajaxloader;
}

function dims_innerHTML(div, html)
{
	if ($(div)) {
		$(div).innerHTML = html;
		$(div).innerHTML.evalScripts();
	}
}

// SHARE MANAGEMENT

function dims_share_searchdata()
{
	dims_xmlhttprequest_todiv('index-light.php','dims_op=search_list&text=','','contentsearch');
}

function dims_share_add_elem(typeshare,idshare)
{
	dims_xmlhttprequest_todiv('index-light.php','dims_op=add_elem&type_share='+typeshare+'&id_share='+idshare,'','contentattach');
}

function dims_share_del_elem(typeshare,idshare)
{
	dims_xmlhttprequest_todiv('index-light.php','dims_op=del_elem&type_share='+typeshare+'&id_share='+idshare,'','contentattach');
}

function dims_share_contentattach()
{
	var ts=document.getElementById('contentattach');
	dims_xmlhttprequest_todiv('index-light.php','dims_op=contentattach','','contentattach');
}



var tag_timer;
var tag_search;
var tag_results = new Array();

var tag_last_array = new Array();
var tag_new_array = new Array();

var tag_lastedit = '';
var tag_modified = -1

function dims_tag_init(idrecord)
{
	dims_getelem('dims_annotationtags_'+idrecord).onkeyup = dims_tag_keyup;
	dims_getelem('dims_annotationtags_'+idrecord).onkeypress = dims_tag_keypress;
}

function dims_tag_search(idrecord, search)
{
	clearTimeout(tag_timer);
	tag_search = search;
	tag_timer = setTimeout("dims_tag_searchtimeout("+idrecord+")", 100);
}

function dims_tag_searchtimeout(idrecord)
{
	// replace(/(^\s*)|(\s*$)/g,'') = TRIM
	list_tags = tag_search.split(' ');

	if (list_tags.length>0) dims_xmlhttprequest_tofunction('index-quick.php','dims_op=tags_search&tag='+list_tags[list_tags.length-1],dims_tag_display,idrecord);
}

function dims_tag_display(result,ticket)
{
	if (result != '')
	{
		tag_results = new Array();

		splited_result = result.split('|');
		tagstoprint = '';

		for (i=0;i<splited_result.length;i++)
		{
			detail = splited_result[i].split(';');
			if (tagstoprint != '') tagstoprint += ' ';
			if (i==0) tagstoprint += '<b>';
			tagstoprint += '<a href="javascript:dims_tag_complete('+ticket+','+i+')">'+detail[0]+'</a> ('+detail[1]+')';
			if (i==0) tagstoprint += '</b>';
			tag_results[i] = detail[0];
		}

		dims_getelem('tagsfound_'+ticket).innerHTML = tagstoprint;
	}
	else
	{
		dims_getelem('tagsfound_'+ticket).innerHTML = '';
		tag_results = new Array();
	}
}

function dims_tag_prevent(e)
{
	if (window.event) window.event.returnValue = false
	else e.preventDefault()
}



function dims_tag_keypress(e)
{
	e=e||window.event;
	src = (e.srcElement) ? e.srcElement : e.target;

	switch(e.keyCode)
	{
		case 38: case 40:
			prevent(e)
		break
		case 9:
			dims_tag_prevent(e)
		break
		case 13:
			dims_tag_prevent(e)
		break
		default:
			tag_lastedit = dims_getelem(src.id).value;
		break;
	}
}

function dims_tag_keyup(e)
{
	e=e||window.event;
	src = (e.srcElement) ? e.srcElement : e.target; // get source field
	idrecord = src.id.split('_')[2]; // get id record from source field id

	switch(e.keyCode)
	{
		case 38: case 40:
			prevent(e);
		break
		case 9:
			dims_tag_complete(idrecord);
			dims_tag_prevent(e);
		break
		case 13:
			dims_tag_complete(idrecord);
			dims_tag_prevent(e);
		break
		case 35: //end
		case 36: //home
		case 39: //right
		case 37: //left
		//case 32: //space
		break
		default:
			tag_last_array = new Array();
			tag_new_array = new Array();

			tag_last_array = tag_lastedit.split(' ');
			tag_new_array = dims_getelem(src.id).value.split(' ');

			tag_modified = -1;
			for (i=0;i<tag_new_array.length;i++)
			{
				if (tag_new_array[i] != tag_last_array[i])
				{
					if (tag_modified == -1) tag_modified = i;
					else tag_modified = -2
				}
			}
			if (tag_modified>=0) dims_tag_search(idrecord, tag_new_array[tag_modified]);
		break;
	}
}

function dims_tag_complete(idrecord, idtag)
{
	if (!(idtag>=0)) idtag = 0;

	if (tag_results[idtag])
	{
		tag_new_array[tag_modified] = tag_results[idtag];

		taglist = '';
		for (i=0;i<tag_new_array.length;i++)
		{
			if (taglist != '') taglist += ' ';
			taglist += tag_new_array[i]
		}

		dims_getelem('dims_annotationtags_'+idrecord).value = taglist.replace(/(^\s*)|(\s*$)/g,'')+' ';
		dims_getelem('tagsfound_'+idrecord).innerHTML = '';
	}

	tag_results = new Array();
}

function dims_calendar_open(inputfield_id, event,funct) {
	dims_showpopup('',164,event,'click');
	dims_xmlhttprequest_todiv('admin-light.php','dims_op=calendar_open&selected_date='+dims_getelem(inputfield_id).value+'&inputfield_id='+inputfield_id+'&funct='+funct,'','dims_popup');
}

function dims_submitmac()
{
	document.formlogin.dims_usermac.value = document.mac.getMacaddress();
}

/* COLORPICKER FUNCTIONS */

var rgb, hsv;

function colorpicker_hex2rgb(hex_string, default_)
{
	if (default_ == undefined)
	{
		default_ = null;
	}

	if (hex_string.substr(0, 1) == '#')
	{
		hex_string = hex_string.substr(1);
	}

	var r;
	var g;
	var b;
	if (hex_string.length == 3)
	{
		r = hex_string.substr(0, 1);
		r += r;
		g = hex_string.substr(1, 1);
		g += g;
		b = hex_string.substr(2, 1);
		b += b;
	}
	else if (hex_string.length == 6)
	{
		r = hex_string.substr(0, 2);
		g = hex_string.substr(2, 2);
		b = hex_string.substr(4, 2);
	}
	else
	{
		return default_;
	}

	r = parseInt(r, 16);
	g = parseInt(g, 16);
	b = parseInt(b, 16);
	if (isNaN(r) || isNaN(g) || isNaN(b))
	{
		return default_;
	}
	else
	{
		return {r: r / 255, g: g / 255, b: b / 255};
	}
}

function colorpicker_rgb2hex(r, g, b, includeHash)
{
	r = Math.round(r * 255);
	g = Math.round(g * 255);
	b = Math.round(b * 255);
	if (includeHash == undefined)
	{
		includeHash = true;
	}

	r = r.toString(16);
	if (r.length == 1)
	{
		r = '0' + r;
	}
	g = g.toString(16);
	if (g.length == 1)
	{
		g = '0' + g;
	}
	b = b.toString(16);
	if (b.length == 1)
	{
		b = '0' + b;
	}
	return ((includeHash ? '#' : '') + r + g + b).toUpperCase();
}

function colorpicker_hsv2rgb(hue, saturation, value)
{
	var red;
	var green;
	var blue;
	if (value == 0.0)
	{
		red = 0;
		green = 0;
		blue = 0;
	}
	else
	{
		var i = Math.floor(hue * 6);
		var f = (hue * 6) - i;
		var p = value * (1 - saturation);
		var q = value * (1 - (saturation * f));
		var t = value * (1 - (saturation * (1 - f)));
		switch (i)
		{
			case 1: red = q; green = value; blue = p; break;
			case 2: red = p; green = value; blue = t; break;
			case 3: red = p; green = q; blue = value; break;
			case 4: red = t; green = p; blue = value; break;
			case 5: red = value; green = p; blue = q; break;
			case 6: // fall through
			case 0: red = value; green = t; blue = p; break;
		}
	}
	return {r: red, g: green, b: blue};
}

function colorpicker_rgb2hsv(red, green, blue)
{
	var max = Math.max(Math.max(red, green), blue);
	var min = Math.min(Math.min(red, green), blue);
	var hue;
	var saturation;
	var value = max;
	if (min == max)
	{
		hue = 0;
		saturation = 0;
	}
	else
	{
		var delta = (max - min);
		saturation = delta / max;
		if (red == max)
		{
			hue = (green - blue) / delta;
		}
		else if (green == max)
		{
			hue = 2 + ((blue - red) / delta);
		}
		else
		{
			hue = 4 + ((red - green) / delta);
		}
		hue /= 6;
		if (hue < 0)
		{
			hue += 1;
		}
		if (hue > 1)
		{
			hue -= 1;
		}
	}
	return {
		h: hue,
		s: saturation,
		v: value
	};
}

function colorpicker_initelements()
{
	x = (hsv.v*199)-5;
	if (x<-5) x=-5;
	if (x>194) x=194;
	dims_getelem('colorpicker_crosshairs').style.left = x.toString() + 'px';
	y = ((1-hsv.s)*199)-5;
	if (y<-5) y=-5;
	if (y>194) y=194;
	dims_getelem('colorpicker_crosshairs').style.top = y.toString() + 'px';
	x = (hsv.h*199)-5;
	if (x<-5) x=-5;
	if (x>194) x=194;
	dims_getelem('colorpicker_position').style.top = x.toString() + 'px';
}

function colorpicker_colorchanged()
{
	var hex = colorpicker_rgb2hex(rgb.r, rgb.g, rgb.b);
	var hueRgb = colorpicker_hsv2rgb(hsv.h, 1, 1);
	var hueHex = colorpicker_rgb2hex(hueRgb.r, hueRgb.g, hueRgb.b);
	dims_getelem('colorpicker_selectedcolor').style.background = hex;
	dims_getelem('colorpicker_inputcolor').value = hex;
	dims_getelem('colorpicker_sv').style.background = hueHex;
}

function colorpicker_rgbchanged()
{
	hsv = colorpicker_rgb2hsv(rgb.r, rgb.g, rgb.b);
	colorpicker_colorchanged();
}
function colorpicker_hsvchanged()
{
	rgb = colorpicker_hsv2rgb(hsv.h, hsv.s, hsv.v);
	colorpicker_colorchanged();
}

function colorpicker_pagecoords(node)
{
	var x = node.offsetLeft;
	var y = node.offsetTop;
	var parent = node.offsetParent;
	while (parent != null)
	{
		x += parent.offsetLeft;
		y += parent.offsetTop;
		parent = parent.offsetParent;
	}
	return {x: x, y: y};
}


function colorpicker_fixcoords(node, x, y)
{
	var nodePageCoords = colorpicker_pagecoords(node);
	x = (x - nodePageCoords.x) + document.documentElement.scrollLeft;
	y = (y - nodePageCoords.y) + document.documentElement.scrollTop;
	if (x < 0) x = 0;
	if (y < 0) y = 0;
	if (x > node.offsetWidth - 1) x = node.offsetWidth - 1;
	if (y > node.offsetHeight - 1) y = node.offsetHeight - 1;
	return {x: x, y: y};
}

function colorpicker_onmousedown(e)
{
	e=e||window.event;
	src = (e.srcElement) ? e.srcElement : e.target; // get source field

	coords = colorpicker_fixcoords(src, e.clientX, e.clientY);

	if (src.id == 'colorpicker_sv')
	{
		colorpicker_placeelement('colorpicker_crosshairs',coords.x,coords.y);
	}
	else if (src.id == 'colorpicker_crosshairs')
	{
		x = parseInt(dims_getelem('colorpicker_crosshairs').style.left) + coords.x;
		y = parseInt(dims_getelem('colorpicker_crosshairs').style.top) + coords.y;
		colorpicker_placeelement('colorpicker_crosshairs',x,y);
	}
	else if (src.id == 'colorpicker_h')
	{
		colorpicker_placeelement('colorpicker_position',0,coords.y);
	}
	else if (src.id == 'colorpicker_position')
	{
		y = parseInt(dims_getelem('colorpicker_position').style.top) + coords.y;
		colorpicker_placeelement('colorpicker_position',0,y);
	}
}

function colorpicker_placeelement(element,x,y)
{
	if (x<0) x=0;
	if (x>199) x=199;

	if (y<0) y=0;
	if (y>199) y=199;

	if (element == 'colorpicker_position')
	{
		dims_getelem('colorpicker_position').style.top = (y-5) + 'px';
		hsv.h = y/199;
	}
	else if (element == 'colorpicker_crosshairs')
	{
		dims_getelem('colorpicker_crosshairs').style.left = (x-5) + 'px';
		dims_getelem('colorpicker_crosshairs').style.top = (y-5) + 'px';
		hsv.s = 1-(y/199);
		hsv.v = (x/199);
	}
	colorpicker_hsvchanged();
}


function colorpicker_input_onchange()
{
	rgb = colorpicker_hex2rgb(dims_getelem('colorpicker_inputcolor').value, {r: 0, g: 0, b: 0});
	colorpicker_rgbchanged();
	colorpicker_initelements();
}

function colorpicker_start()
{
	dims_getelem('colorpicker_sv').onmousedown = colorpicker_onmousedown;
	dims_getelem('colorpicker_h').onmousedown = colorpicker_onmousedown;
	dims_getelem('colorpicker_position').onmousedown = colorpicker_onmousedown;
	dims_getelem('colorpicker_crosshairs').onmousedown = colorpicker_onmousedown;
	dims_getelem('colorpicker_inputcolor').onchange = colorpicker_input_onchange;

	colorpicker_input_onchange();
}

function dims_colorpicker_open(inputfield_id, event)
{
	dims_showpopup('','241',event,'click');
	data = 'dims_op=colorpicker_open&inputfield_id='+inputfield_id+'&colorpicker_value='+escape(dims_getelem(inputfield_id).value);
	colorpickerhtml = dims_xmlhttprequest('admin-light.php',data);
	dims_getelem('dims_popup').innerHTML = colorpickerhtml;
	colorpicker_start();
}

/* DOCUMENTS FUNCTIONS */

function dims_documents_openfolder(currentfolder, documentsfolder_id, event)
{
	dims_showpopup('',300,event,'click');
	dims_xmlhttprequest_todiv('admin-light.php','dims_op=documents_openfolder&currentfolder='+currentfolder+'&documentsfolder_id='+documentsfolder_id,'','dims_popup');
}

function dims_documents_openfile(currentfolder, documentsfile_id, event)
{
	dims_showpopup('',380,event,'click');
	dims_xmlhttprequest_todiv('admin-light.php','dims_op=documents_openfile&currentfolder='+currentfolder+'&documentsfile_id='+documentsfile_id,'','dims_popup');
}

function dims_documents_deletefile(currentfolder, documents_id, documentsfile_id)
{
	dims_xmlhttprequest_todiv('admin-light.php','dims_op=documents_deletefile&currentfolder='+currentfolder+'&documentsfile_id='+documentsfile_id,'','dimsdocuments_'+documents_id);
}

function dims_documents_deletefolder(currentfolder, documents_id, documentsfolder_id)
{
	dims_xmlhttprequest_todiv('admin-light.php','dims_op=documents_deletefolder&currentfolder='+currentfolder+'&documentsfolder_id='+documentsfolder_id,'','dimsdocuments_'+documents_id);
}

function dims_documents_browser(currentfolder, documents_id)
{
	dims_xmlhttprequest_todiv('admin-light.php','dims_op=documents_browser&currentfolder='+currentfolder,'','dimsdocuments_'+documents_id);
}

function dims_documents_validate(form)
{
	if (dims_validatefield('Fichier',form.documentsfile_file,"string"))
	if (dims_validatefield('Libell�',form.documentsfile_label,"string"))
		return true;

	return false;
}

function autofitIframe(){
	try {
		if (document.getElementById || !window.opera && !document.mimeType && document.all && document.getElementById) {
			hauteur = this.document.body.scrollHeight + 40;

			if (hauteur<650) hauteur=650;
			parent.document.getElementById('wce_frame_editor').style.height=hauteur+"px";
		}
	}
	catch (e) {
		hauteur =this.document.body.offsetHeight + 40;
		if (hauteur<650) hauteur=650;
		parent.document.getElementById('wce_frame_editor').style.height=hauteur+"px";
	}
}

function addElement(parentId, elementTag, elementId, html,xdeb,ydeb,xfin,yfin) {
	// Adds an element to the document
	var p = document.getElementById(parentId);

	var newElement = document.createElement(elementTag);
	newElement.setAttribute('id', elementId);

	var width=Math.abs(xfin-xdeb);
	var height=Math.abs(yfin-ydeb);

	if (width==0) width=1;
	if ( height==0) height=1;

	// definition de la taille du canvas
	if (width!=null) newElement.setAttribute('width', width);
	if (height!=null) newElement.setAttribute('height',height);

	//Calcul du plus petit top et left, pour largeur et hauteur : valeur absolue
	if (xfin<xdeb) {
		minx=xfin;
		xfin=0;
		xdeb=width;
	}
	else {
		minx=xdeb;
		xdeb=0;
		xfin=width;
	}

	if (yfin<ydeb) {
		miny=yfin;
		yfin=0;
		ydeb=height;
	}
	else {
		miny=ydeb;
		ydeb=0;
		yfin=height;
	}

	newElement.style.left=minx+'px';
	newElement.style.top=miny+'px';
	newElement.style.position='absolute';
	p.appendChild(newElement);

	if(!window.innerWidth) {
		newElement=G_vmlCanvasManager.initElement(newElement);
	}

	p.appendChild(newElement);
	newElement.innerHTML = html;

	if (elementTag=="canvas") draw(elementId,xdeb,ydeb,xfin,yfin,1);
}

function removeElement(elementId) {
// Removes an element from the document
var element = document.getElementById(elementId);
element.parentNode.removeChild(element);
}

function draw(elem,xdep,ydep,xfin,yfin,sens){
	if (sens==null) sens=0;

	var canvas = document.getElementById(elem);
	if (canvas.getContext){

		var ctx = canvas.getContext('2d');
		ctx.save();
		ctx.lineWidth = 1;
		ctx.strokeStyle = '#BCBCBC';
		ctx.beginPath();
		ctx.moveTo(xdep,ydep);
		// si sens = 0 => horizontal sinon vertical
		if (sens==0) {
			dist=(xfin-xdep)*1/3;
			ctx.bezierCurveTo(dist,ydep,xfin-(dist/2),yfin,xfin,yfin);
		}
		else {
			dist=(yfin-ydep)*1;
			ctx.bezierCurveTo(xdep,dist,xfin,yfin-(dist/2),xfin,yfin);
		}
		ctx.stroke();
		ctx.restore();
	}
}

function dims_print_r(theObj){
	if(theObj.constructor == Array ||
	   theObj.constructor == Object){
		document.write("<ul>")
		for(var p in theObj){
			if(theObj[p].constructor == Array||
			   theObj[p].constructor == Object){
				document.write("<li>["+p+"] => "+typeof(theObj)+"</li>");
				document.write("<ul>")
				print_r(theObj[p]);
				document.write("</ul>")
			}
			else {
				document.write("<li>["+p+"] => "+theObj[p]+"</li>");
			}
		}
		document.write("</ul>")
	}
}
