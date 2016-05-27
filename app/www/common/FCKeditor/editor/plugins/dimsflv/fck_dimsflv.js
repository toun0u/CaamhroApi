var oEditor			= window.parent.InnerDialogLoaded();
var FCK				  = oEditor.FCK;
var FCKLang			= oEditor.FCKLang ;
var FCKConfig		= oEditor.FCKConfig ;

var EmbedInObject = false;

// get the selected embedded movie and its container div (if available)
var oMovie = null;
var oContainerDiv = FCK.Selection.GetSelectedElement();
if (oContainerDiv)
{
	if(oContainerDiv.tagName == 'DIV' &&
		 oContainerDiv.childNodes.length > 0 &&
		 oContainerDiv.childNodes[0].tagName == (EmbedInObject ? 'OBJECT' : 'EMBED'))
	 oMovie = oContainerDiv.childNodes[0];
	else if (oContainerDiv.tagName == (EmbedInObject ? 'OBJECT' : 'EMBED') &&
	         oContainerDiv.parentNode.tagName == 'DIV')
	{
		oMovie = oContainerDiv;
		oContainerDiv  = oContainerDiv.parentNode;
	}
	else
		oContainerDiv = null;
}

function GetParam(e, pname, defvalue)
{
	if (!e) return defvalue;
	if (EmbedInObject)
	{
		for (var i = 0; i < e.childNodes.length; i++)
		{
			if (e.childNodes[i].tagName == 'PARAM' && GetAttribute(e.childNodes[i], 'name') == pname)
			{
				var retval = GetAttribute(e.childNodes[i], 'value');
				if (retval == "false") return false;
				return retval;
			}
		}
		return defvalue;
	}
	else
	{
		var retval = GetAttribute(e, pname, defvalue);
		if (retval == "false") return false;
		return retval;
	}
}

window.onload = function ()
{
	// First of all, translates the dialog box texts.
	oEditor.FCKLanguageManager.TranslatePage(document);

	// read settings from existing embedded movie or set to default
	GetE('txtUrl').value = GetParam(oMovie, (EmbedInObject ? 'url' : 'src'), '');
	GetE('chkAutosize').checked      = GetParam(oMovie,  'autosize',     true);
	GetE('txtWidth').value           = GetParam(oMovie,  'width',        250  );
	GetE('txtHeight').value          = GetParam(oMovie,  'height',       250  );

	// Show/Hide according to settings
	ShowE('divSize',  !GetE('chkAutosize').checked);
	ShowE('tdBrowse', FCKConfig.LinkBrowser);

	// Show Ok button
	window.parent.SetOkButton( true );
}

function BrowseServer() {
	var url;
	url="../../../../admin-light.php?dims_op=doc_selectvideo";
	OpenFileBrowser(url,FCKConfig.ScreenWidth * 0.7 ,FCKConfig.ScreenHeight * 0.7);
}

function SetUrl( url )
{
	alert(url);
	GetE('txtUrl').value = url;
}

function CreateEmbeddedMovie( url) {
	var bgcolor, pluginspace, codebase, classid;

	classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" ;
	codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" ;
	bgcolor="#FFFFFF";
	urlplayer="./FCKeditor/editor/plugins/dimsflv/player_flv_maxi.swf";

	var html;
	/*
	html  = '<OBJECT classid="'+ classid +'">';
	html += '<PARAM name="allowScriptAccess" value="'+ url +'" />';
	html += '<PARAM name="codebase" value="'+ codebase +'" />';
	html += '<PARAM name="movie" value="'+ url +'" />';
	html += '<PARAM name="quality" value="high" />';
	html += '<PARAM name="wmode" value="transparent" />';
	html += '<PARAM name="bgcolor" value="'+ bgcolor +'" />';
	html += '<embed src="'+url+'" quality="high" wmode="transparent" bgcolor="#731013" name="lecteur" align="middle" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />';
	html += '</OBJECT>';
	*/
   html  = '<object type="application/x-shockwave-flash" data="'+urlplayer+'" width="320" height="240">';
   html += '<param name="movie" value="'+urlplayer+'" />';
   html += '<param name="allowFullScreen" value="true" />';
   html += '<param name="FlashVars" value="flv='+url+'&amp;autoplay=1&amp;autoload=1&amp;margin=0&amp;showstop=1&amp;showvolume=1&amp;showtime=1&amp;showplayer=always&amp;showfullscreen=1" />';
	html += '</object>';

	//e.innerHTML = html;
	return html;
}

function Ok()
{

	if ( GetE('txtUrl').value.length == 0 )
	{
		window.parent.SetSelectedTab( 'Info' ) ;
		GetE('txtUrl').focus() ;

		alert( FCKLang.DimsFlvAlertUrl ) ;

		return false ;
	}

	oEditor.FCKUndo.SaveUndoStep();

	/*if (!oContainerDiv) {

		oContainerDiv = FCK.CreateElement('DIV');
	}*/

	html=CreateEmbeddedMovie( GetE('txtUrl').value);

	FCK.InsertHtml(html);
	return true;
}
