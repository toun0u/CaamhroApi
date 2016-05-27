/*
 * FCKeditor - The text editor for internet
 * Copyright (C) 2003-2006 Frederico Caldeira Knabben
 *
 * Licensed under the terms of the GNU Lesser General Public License:
 * 		http://www.opensource.org/licenses/lgpl-license.php
 *
 * For further information visit:
 * 		http://www.fckeditor.net/
 *
 * "Support Open Source software. What about a donation today?"
 *
 * File Name: fckplugin.js
 * 	Plugin for transparent background
 *
 *
 * File Authors:
 * 		Yogananthar Ananthapavan (rollbond@gmail.com)
 */

// Create the "Transparent" toolbar button

// The object used for all Transparent operations.

var oDimsUserItem = new FCKToolbarButton('DimsUser', FCKLang['DlgDimsUserTitle'], null, null, false, true);
oDimsUserItem.IconPath = FCKPlugins.Items['dimsuser'].Path + 'dimsuser.gif';

FCKToolbarItems.RegisterItem('DimsUser', oDimsUserItem);

// The object used for all Transparent operations.
var FCKDimsuser = new Object();

FCKDimsuser = function(name){
	this.Name = name;
}

FCKDimsuser.prototype.GetState = function() {
}

FCKDimsuser.prototype.Execute = function(){
	var oSpan = FCK.CreateElement( 'SPAN' ) ;
	var name=FCK.Config['DimsUser'];

	oSpan.innerHTML = '// ' + name  + ' ';

	//oSpan.style.backgroundColor = '#4e68a6' ;
	oSpan.style.color = '#4e68a6' ;

	if ( FCKBrowserInfo.IsGecko )
		oSpan.style.cursor = 'default' ;

	oSpan._fckplaceholder = name ;
	oSpan.contentEditable = false ;

	// To avoid it to be resized.
	oSpan.onresizestart = function()
	{
		FCK.EditorWindow.event.returnValue = false ;
		return false ;
	}
}

// Register the related command
FCKCommands.RegisterCommand('DimsUser', new FCKDimsuser(''));