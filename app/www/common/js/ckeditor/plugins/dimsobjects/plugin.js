

CKEDITOR.plugins.add('dimsobjects',
                     {
                    lang:['en','fr'],
                    init:function(editor){
                        editor.addCommand('openObjectDims',new CKEDITOR.dialogCommand('openObjectDims'));
                        editor.ui.addButton('dimsobjects',{ label: editor.lang.dimsobjects.dimsObject,
                                                            command: 'openObjectDims',
                                                            icon: this.path+'images/dimsobjects.gif'});
                        editor.on('instanceReady',function(e){

                            var parser = new CKEDITOR.htmlParser();
                            parser.onText = function( text ){
                                var regexpContent = /\[\[ \d+,\d+(,\d+)?\/.+ &gt; .+( &gt; .+)?( &gt; params:.+)? \]\]/g;
                                var resContent;
                                if((resContent = regexpContent.exec(text)) && resContent.length > 0){
                                    var elem = '<span style="color: #000000; background-color: #8CA9CF;" contenteditable="false" name="dimsobject">' + resContent[0] + '</span>';
                                    editor.setData(editor.getData().replace(resContent[0],elem));
                                }
                            };
                            parser.parse(editor.getData());
                        });
                        editor.on('doubleclick',function(evt){
                            var element = CKEDITOR.plugins.link.getSelectedLink( editor ) || evt.data.element;
                            if(element.isReadOnly() && element.is('span')){
                                editor.getSelection().selectElement( element );
                                evt.data.dialog = 'openObjectDims';
                            }
                        });
                    }
});
CKEDITOR.dialog.add('openObjectDims', function( editor ){
    var dt = jQuery.parseJSON(CKEDITOR.ajax.load('/admin-light.php?dims_op=wce_getdimsobjects2'));

    return {
        title: 'Objets DIMS',
        minWidth: 450,
        minHeight: 240,
        contents: [{ id: 'idDimsObjects',
                    label: editor.lang.dimsobjects.dimsObject,
                    elements: [{type:'html',
                               html:editor.lang.dimsobjects.chooseModule+' : '},
                               {type: 'select',
                               id: 'selectModule',
                               style:"width:200px;",
                               items:dt,
                               setup: function(data){
                                    var dialog = this.getDialog();
                                    var element = dialog.getContentElement( 'idDimsObjects', 'objectsoption' );
                                    element = element.getElement();
                                    element.hide();
                                    this.data = data.obj;
                                    this.setValue(data.module);
                               },
                               onChange : function(){
                                    var dialog = this.getDialog();
                                    var	typeValue = this.getValue();

                                	var params = dialog.getContentElement( 'idDimsObjects', 'objectsparams' );
                                	params = params.getElement();
                                	params.hide();

                                    var select = dialog.getContentElement( 'idDimsObjects', 'selectObject' );
                                    select.clear();

                                    var element = dialog.getContentElement( 'idDimsObjects', 'objectsoption' );
                                    element = element.getElement();

                                    if ( typeValue > 0 ){
                                        var objects = jQuery.parseJSON(CKEDITOR.ajax.load('/admin-light.php?dims_op=refresh_select_moduletype2&id_module='+typeValue));
                                        for (var i in objects){
                                            select.add(objects[i][0],objects[i][1]);
                                        }
                                        element.setValue(this.data);
                                        element.show();
                                    }
                                    else{
                                        element.hide();
                                    }
                                }},
                               {type : 'vbox',
                                id : 'objectsoption',
                                style: 'display:none;',
                                children : [
                                    {type:'html',
                                    html:editor.lang.dimsobjects.chooseObject+' : '},
                                {
                                    type : 'select',
                                    id : 'selectObject',
                                    style:"width:200px;",
                                    items: [],
                                    commit: function(data){
                                    	var value = this.getValue();
                                        if(value != 0){
                                        	var text = "[[ "+value+" ]]";
                                        	if(value.indexOf("Liste d'items [Objets dynamiques]") >= 0){
                                        		var dialog = this.getDialog();
                                        		var param1 = dialog.getContentElement( 'idDimsObjects', 'param_mode' );
                                        		var param2 = dialog.getContentElement( 'idDimsObjects', 'param_nb_elem' );
	                                    		var text = "[[ "+value+" > params:mode="+param1.getValue()+"&max-elem="+param2.getValue()+" ]]";
	                                    	}
                                            
                                            var elem = new CKEDITOR.dom.element( 'span' );
                                            elem.setAttributes({
                                                'style': 'color: #000000; background-color: #8CA9CF;',
                                                'contenteditable': 'false',
                                                'name': 'dimsobject'
                                            });
                                            elem.setText(text);
                                            editor.insertElement(elem);
                                            //data.setText('<span style="color: #000000; background-color: #8CA9CF" contenteditable="false" _fckplaceholder="dimsobject">' + text + '</span>');
                                        }else
                                            data.setText('');
                                    },
                                    setup: function(data){
                                        this.setValue(data.obj);
                                    },
                                    onChange: function(){
                                    	var value = this.getValue();
                                    	var dialog = this.getDialog();
                                    	var element = dialog.getContentElement( 'idDimsObjects', 'objectsparams' );
                                    	element = element.getElement();
                                    	element.hide();
                                    	if(value.indexOf("Liste d'items [Objets dynamiques]") >= 0){
                                    		element.show();
                                    	}
                                    	
                                    }
                                }]
                                },
                               {type : 'vbox',
                                id : 'objectsparams',
                                style: 'display:none;',
                                children : [
                                // PARAM : mode
                                {
                                    type : 'select',
                                    id : 'param_mode',
                                    style:"width:200px;",
                                    label: editor.lang.dimsobjects.paramMode,
                                    items: [["Full index","full_index"],["Home","home"]],
                                    'default': 'full_index',
                                    setup: function(data){
                                        this.setValue(data.paramMode);
                                    }
                                },
                                // PARAM : max-elem
                                {
                                    type : 'text',
                                    id : 'param_nb_elem',
                                    style:"width:200px;",
                                    label: editor.lang.dimsobjects.paramNb,
                                    'default': "0",
                                    setup: function(data){
                                        this.setValue(data.paramNbElem);
                                    }
                                }]
                                }
                               ]
                   }],
        onOk: function(data){
            var dialog = this,
			abbr = this.data;
            this.commitContent( abbr );
        },
        onShow: function(){
            var sel = editor.getSelection(),
            data = sel.getStartElement();

            this.data = data;
            this.data.module = 0;
            this.data.obj = 0;
            this.data.paramMode = "full_index";
            this.data.paramNbElem = "0";

            var split = data.getText().split('/');
            if (split.length == 2){
                var datas = split[0].split(',');
                if(datas.length >= 2){
                    this.data.module = datas[1];
                    this.data.obj = data.getText().substring(3,data.getText().length-3);
                    if(this.data.obj.indexOf(" > params:") > 0){
                    	var tmp = this.data.obj.split(' > ');
                    	tmp.pop();
                    	this.data.obj = tmp.join(" > ");
                    }
                }
                var params = split[1].substring(0,split[1].length-3).split(' > ');
                if(params[params.length-1].indexOf('params:') >= 0){
                	var p = params[params.length-1].substring(7).split('&');
                	for(i=0;i<p.length;i++){
                		if(p[i].indexOf('mode=') == 0){
                			this.data.paramMode = p[i].substring(5);
                		}else if(p[i].indexOf('max-elem=') == 0){
                			this.data.paramNbElem = p[i].substring(9);
                		}
                	}
                }
            }
            this.setupContent( this.data );
        }
        };
});

/*
// Register the related commands.
FCKCommands.RegisterCommand( 'DimsObjects'	,
                            new FCKDialogCommand( FCKLang['DlgDimsObjectsTitle']	,
                                                 FCKLang['DlgDimsObjectsTitle']		,
                                                 FCKConfig.BaseHref + 'admin-light.php?dims_op=wce_getdimsobjects'	, 450, 240 )
                            ) ;
// Create the "Find" toolbar button.
var oDimsObjects		= new FCKToolbarButton( 'DimsObjects', FCKLang['DlgDimsObjectsTitle'] ) ;
oDimsObjects.IconPath	= FCKConfig.PluginsPath + 'dimsobjects/dimsobjects.gif' ;

FCKToolbarItems.RegisterItem( 'DimsObjects', oDimsObjects ) ;			// 'DimsObjects' is the name used in the Toolbar config.

var elemSelected=null;
// The object used for all Placeholder operations.
var FCKPlaceholders = new Object() ;

// Add a new placeholder at the actual selection.
FCKPlaceholders.Add = function( name )
{
	var oSpan = FCK.CreateElement( 'SPAN' ) ;
	this.SetupSpan( oSpan, name ) ;
}

FCKPlaceholders.SetupSpan = function( span, name )
{
	span.innerHTML = '[[ ' + name + ' ]]' ;

	span.style.backgroundColor = '#8ca9cf' ;
	span.style.color = '#000000' ;

	if ( FCKBrowserInfo.IsGecko )
		span.style.cursor = 'default' ;

	span._fckplaceholder = name ;
	span.contentEditable = false ;

	// To avoid it to be resized.
	span.onresizestart = function()
	{
		FCK.EditorWindow.event.returnValue = false ;
		return false ;
	}
}

// On Gecko we must do this trick so the user select all the SPAN when clicking on it.
FCKPlaceholders._SetupClickListener = function()
{
	FCKPlaceholders._ClickListener = function( e )
	{
		if ( e.target.tagName == 'SPAN' && e.target._fckplaceholder ) {

			FCKSelection.SelectNode( e.target ) ;
			elemSelected=e.target;
		}
		else elemSelected=null;
	}

	FCK.EditorDocument.addEventListener( 'click', FCKPlaceholders._ClickListener, true ) ;
}

// Open the Placeholder dialog on double click.
FCKPlaceholders.OnDoubleClick = function( span )
{
	if ( span.tagName == 'SPAN' && span._fckplaceholder ) {
		FCKSelection.SelectNode( span ) ;
		FCKCommands.GetCommand( 'DimsObjects' ).Execute() ;
	}
}

FCK.RegisterDoubleClickHandler( FCKPlaceholders.OnDoubleClick, 'SPAN' ) ;

// Check if a Placholder name is already in use.
FCKPlaceholders.Exist = function( name )
{
	var aSpans = FCK.EditorDocument.getElementsByTagName( 'SPAN' )

	for ( var i = 0 ; i < aSpans.length ; i++ )
	{
		if ( aSpans[i]._fckplaceholder == name )
			return true ;
	}

	return false ;
}

if ( FCKBrowserInfo.IsIE )
{
	FCKPlaceholders.Redraw = function()
	{
		var aPlaholders = FCK.EditorDocument.body.innerText.match( /\[\[[^\[\]]+\]\]/g ) ;
		if ( !aPlaholders )
			return ;

		var oRange = FCK.EditorDocument.body.createTextRange() ;

		for ( var i = 0 ; i < aPlaholders.length ; i++ )
		{
			if ( oRange.findText( aPlaholders[i] ) )
			{
				var sName = aPlaholders[i].match( /\[\[\s*([^\]]*?)\s*\]\]/ )[1] ;
				oRange.pasteHTML( '<span style="color: #000000; background-color: #8ca9cf" contenteditable="false" _fckplaceholder="' + sName + '">' + aPlaholders[i] + '</span>' ) ;
			}
		}
	}
}
else
{
	FCKPlaceholders.Redraw = function()
	{
		var oInteractor = FCK.EditorDocument.createTreeWalker( FCK.EditorDocument.body, NodeFilter.SHOW_TEXT, FCKPlaceholders._AcceptNode, true ) ;

		var	aNodes = new Array() ;

		while ( oNode = oInteractor.nextNode() )
		{
			aNodes[ aNodes.length ] = oNode ;
		}

		for ( var n = 0 ; n < aNodes.length ; n++ )
		{
			var aPieces = aNodes[n].nodeValue.split( /(\[\[[^\[\]]+\]\])/g ) ;

			for ( var i = 0 ; i < aPieces.length ; i++ )
			{
				if ( aPieces[i].length > 0 )
				{
					if ( aPieces[i].indexOf( '[[' ) == 0 )
					{
						var sName = aPieces[i].match( /\[\[\s*([^\]]*?)\s*\]\]/ )[1] ;

						var oSpan = FCK.EditorDocument.createElement( 'span' ) ;
						FCKPlaceholders.SetupSpan( oSpan, sName ) ;

						aNodes[n].parentNode.insertBefore( oSpan, aNodes[n] ) ;
					}
					else
						aNodes[n].parentNode.insertBefore( FCK.EditorDocument.createTextNode( aPieces[i] ) , aNodes[n] ) ;
				}
			}

			aNodes[n].parentNode.removeChild( aNodes[n] ) ;
		}

		FCKPlaceholders._SetupClickListener() ;
	}

	FCKPlaceholders._AcceptNode = function( node )
	{
		if ( /\[\[[^\[\]]+\]\]/.test( node.nodeValue ) )
			return NodeFilter.FILTER_ACCEPT ;
		else
			return NodeFilter.FILTER_SKIP ;
	}
}

FCK.Events.AttachEvent( 'OnAfterSetHTML', FCKPlaceholders.Redraw ) ;

// We must process the SPAN tags to replace then with the real resulting value of the placeholder.
FCKXHtml.TagProcessors['span'] = function( node, htmlNode )
{
	if ( htmlNode._fckplaceholder )
		node = FCKXHtml.XML.createTextNode( '[[' + htmlNode._fckplaceholder + ']]' ) ;
	else
		FCKXHtml._AppendChildNodes( node, htmlNode, false ) ;

	return node ;
}
*/