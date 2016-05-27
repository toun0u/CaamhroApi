/*
 *  FCKPlugin.js
 *  ------------
 *  This is a generic file which is needed for plugins that are developed
 *  for FCKEditor. With the below statements that toolbar is created and
 *  several options are being activated.
 *
 *  See the online documentation for more information:
 *  http://wiki.fckeditor.net/
 */

// Register the related commands.
/*
FCKCommands.RegisterCommand( 'DimsFlv',new FCKDialogCommand('DimsFlv',FCKLang["DimsFlvDlgTitle"],FCKPlugins.Items['DimsFlv'].Path + 'fck_dimsflv.html',450,270));

var oDimsFlvItem = new FCKToolbarButton( 'DimsFlv', FCKLang["DimsFlvBtn"], FCKLang["DimsFlvTooltip"], null, false, true );
oDimsFlvItem.IconPath = FCKConfig.PluginsPath + 'DimsFlv/DimsFlv.gif';

FCKToolbarItems.RegisterItem( 'DimsFlv', oDimsFlvItem );
*/

FCKCommands.RegisterCommand( 'dimsflv'	,
	new FCKDialogCommand( FCKLang['DlgDimsFlvTitle']	,
		FCKLang['DlgDimsFlvTitle'],
		FCKPlugins.Items['dimsflv'].Path + 'fck_dimsflv.html'	,
		450,
		240 ) ) ;
// Create the "Find" toolbar button.
var oDimsFlv		= new FCKToolbarButton( 'dimsflv', FCKLang['DlgDimsFlvTitle'] ) ;
oDimsFlv.IconPath	= FCKConfig.PluginsPath + 'dimsflv/dimsflv.gif' ;

FCKToolbarItems.RegisterItem( 'dimsflv', oDimsFlv ) ;

