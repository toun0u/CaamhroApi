CKEDITOR.plugins.add('wiki', {
    lang:['en','fr'],
    init: function(editor) {
        editor.addCommand('wikicommand', new CKEDITOR.dialogCommand( 'wikiDialog' ));
        editor.ui.addButton( 'Wiki',{
            label: editor.lang.wiki.artWiki,
            command: 'wikicommand',
            icon: this.path+"images/wiki16.png"
        } );
        //editor.execCommand('testcommand');
        if ( editor.contextMenu ){
            editor.addMenuGroup( 'wiki' );
            editor.addMenuItem('wikimenu',
                               {
                                label : editor.lang.wiki.artWiki,
                                icon : this.path+"images/wiki16.png",
                                command : 'wikicommand',
                                group : 'wiki'
                                });
            editor.contextMenu.addListener( function( element ){
                //if ( element )
                //    element = element.getAscendant( 'wiki', true );
                if ( element && !element.isReadOnly() && !element.data( 'cke-realelement' ) )
                    return { wikimenu : CKEDITOR.TRISTATE_OFF };
                return null;
            });
        }
        editor.on('doubleclick',function(evt){
            var element = CKEDITOR.plugins.link.getSelectedLink( editor ) || evt.data.element;
            if(element.is('a')){
                var articleidRegex = /[?&]articleid=([0-9]+)/;
                var sectionidRegex = /[?&]articleid=[0-9]+(&WCE_section_[0-9]+_[0-9]+=[0-9]+#[0-9]+)/;
                var href = ( element  && ( element.data( 'cke-saved-href' ) || element.getAttribute( 'href' ) ) ) || '';
                if (href && (href.match(articleidRegex) || href.match(sectionidRegex))){
                    editor.getSelection().selectElement( element );
                    evt.data.dialog = 'wikiDialog';
                }
            }
        });
        CKEDITOR.dialog.add( 'wikiDialog', function( editor ){
            return {
				title : editor.lang.wiki.linkArtWiki,
				minWidth : 400,
				minHeight : 100,
				contents :
				[
                    {
						id : 'tab1',
						label : editor.lang.wiki.selArt,
						elements :
						[
                            {
                                type : 'select',
                                label : editor.lang.wiki.selArt,
                                id : 'article_wiki',
                                title : editor.lang.wiki.artOfWiki,
                                items: jQuery.parseJSON(CKEDITOR.ajax.load('/admin-light.php?dims_op=wiki&op_wiki=sel_article_wiki')),
                                onChange : function(){
                                    var dialog = this.getDialog();
                                    var select = dialog.getContentElement( 'tab1', 'section_wiki' );
                                    var element = dialog.getContentElement( 'tab1', 'optionssection' );
                                    element = element.getElement();
                                    select.clear();
                                    if (this.getValue() > 0){
                                        var objects = jQuery.parseJSON(CKEDITOR.ajax.load('/admin-light.php?dims_op=wiki&op_wiki=sel_section_wiki&id='+this.getValue()));
                                        for (var i in objects){
                                            var url = "&WCE_section_"+this.getValue()+"_"+objects[i][2]+"="+objects[i][3]+"#"+objects[i][1];
                                            select.add(objects[i][0],url);
                                        }
                                        element.show();
                                    }else{
                                        element.hide();
                                    }
                                },
                                setup : function( element ){
                                    if ( element.localPage ){
                                        this.setValue( element.localPage );
                                        if (element.localPage > 0){
                                            var dialog = this.getDialog();
                                            var select = dialog.getContentElement( 'tab1', 'section_wiki' );
                                            var element2 = dialog.getContentElement( 'tab1', 'optionssection' );
                                            element2 = element2.getElement();
                                            select.clear();
                                            var objects = jQuery.parseJSON(CKEDITOR.ajax.load('/admin-light.php?dims_op=wiki&op_wiki=sel_section_wiki&id='+element.localPage));
                                            for (var i in objects){
                                                var url = "&WCE_section_"+element.localPage+"_"+objects[i][2]+"="+objects[i][3]+"#"+objects[i][1];
                                                select.add(objects[i][0],url);
                                            }
                                            if ( element.sectionPage )
                                                select.setValue(element.sectionPage);
                                            element2.show();
                                        }
                                    }
                                },
                                commit : function( element )
                                {
                                    if ( !element.localPage )
                                        element.localPage = {};
                                    element.localPage = this.getValue();
                                }
                            },
                            {   type : 'vbox',
                                id : 'optionssection',
                                style: 'display:none;',
                                children : [
                                        {
                                            type : 'select',
                                            label : editor.lang.wiki.section,
                                            id : 'section_wiki',
                                            title : editor.lang.wiki.section,
                                            items: [editor.lang.wiki.nobody, 0],
                                            commit : function( element ){
                                                if ( !element.sectionPage )
                                                    element.sectionPage = {};
                                                element.sectionPage = this.getValue();
                                            },
                                            setup : function( element ){}
                                        }
                                        ]
                            }
                        ]
                    },
                    {
						id : 'tab2',
						label : editor.lang.wiki.createArt,
						elements :
						[
                            {
                                type : 'text',
                                id : 'nameArt',
                                label : editor.lang.wiki.nameArt,
                                setup : function( element )
                                {
                                    this.setValue( element.getText() );
                                },
                                commit : function( element )
                                {
                                    if ( !element.name )
                                        element.name = {};
                                    element.name = this.getValue();
                                }
                            },
                            {
                                type : 'checkbox',
                                id : 'validation',
                                label : editor.lang.wiki.validate,
                                setup : function( element )
                                {
                                    if ( !element.isNew )
                                        element.isNew = false;
                                    this.setValue( element.isNew );
                                },
                                commit : function( element )
                                {
                                    if ( !element.isNew )
                                        element.isNew = {};
                                    element.isNew = this.getValue();
                                }
                            }
                        ]
                    }
                ],
				onShow : function(){
					var sel = editor.getSelection(),
                        element = sel.getStartElement(),
                        articleMatch,
                        sectionMatch;

					this.element = element;

                    /*if ( ( element = plugin.getSelectedLink( editor ) ) && element.hasAttribute( 'href' ) )
                        sel.selectElement( element );*/


                    this.element.localPage = "0";

                    var articleidRegex = /[?&]articleid=([0-9]+)/;
                    var sectionidRegex = /[?&]articleid=[0-9]+(&WCE_section_[0-9]+_[0-9]+=[0-9]+#[0-9]+)/;
                    var href = ( element  && ( element.data( 'cke-saved-href' ) || element.getAttribute( 'href' ) ) ) || '';
                    if ( ( articleMatch = href.match( articleidRegex ) ) ){
                        this.element.localPage = articleMatch[1];
                        if ( ( sectionMatch = href.match( sectionidRegex ) ) ){
                            this.element.sectionPage = sectionMatch[1];
                        }
                    }
					this.setupContent( this.element );
				},
				onOk : function(){
					var element = {},
                        attributes = {},
                        editor = this.getParentEditor(),
                        ok = true;

                    this.commitContent( element );
                    if(element.isNew){
                        var id = CKEDITOR.ajax.load('/admin-light.php?dims_op=wiki&op_wiki=create_new_article&name='+element.name);
                        if (id > 0)
                            attributes['data-cke-saved-href' ] = 'index.php?articleid='+id;
                        else
                            ok = false;
                    }else{
                        var id = element.localPage;
                        if (id > 0){
                            attributes['data-cke-saved-href'] = 'index.php?articleid='+id;
                            var sect = element.sectionPage;
                            if (sect != '&WCE_section_'+id+'_1=1#0')
                                attributes['data-cke-saved-href'] += sect;
                        }
                        else
                            ok = false;
                    }

                    if(ok){
                        var element = CKEDITOR.plugins.link.getSelectedLink( editor );

                        // Browser need the "href" fro copy/paste link to work. (#6641)
                        attributes.href = attributes[ 'data-cke-saved-href' ];
                        if ( !element ){
                            var selection = editor.getSelection();
                            // Create element if current selection is collapsed.
                            var ranges = selection.getRanges( true );
                            if ( ranges.length == 1 && ranges[0].collapsed )
                            {
                                // Short mailto link text view (#5736).
                                var text = new CKEDITOR.dom.text( attributes[ 'data-cke-saved-href' ], editor.document );
                                ranges[0].insertNode( text );
                                ranges[0].selectNodeContents( text );
                                selection.selectRanges( ranges );
                            }

                            // Apply style.
                            var style = new CKEDITOR.style( { element : 'a', attributes : attributes } );
                            style.type = CKEDITOR.STYLE_INLINE;		// need to override... dunno why.
                            style.apply( editor.document );
                        }else{
                            var selection = editor.getSelection().selectElement( element ) || editor.getSelection();
                            // We're only editing an existing link, so just overwrite the attributes.
                            //var element = this._.selectedElement,
                            //href = element.data( 'cke-saved-href' ),
                            textView = element.getHtml();

                            element.setAttributes( attributes );

                            selection.selectElement( element );
                            delete this._.selectedElement;
                        }
                    }else
                        alert(editor.lang.wiki.invalidLink);
				}
            };
        });
    }
});