CKEDITOR.plugins.add('removespace', {
    init: function(editor) {
        editor.addCommand('removespacecmd',
            {
                exec: function(editor){
                    var sel = editor.getSelection(),
                        element = sel.getSelectedText(),
                        spaceMatch;
                    element = element.replace(/&nbsp;/g,' ');
                    element = element.replace(/<br \/>/g,' ');
                    element = element.replace(/<br>/g,' ');
                    element = element.replace(/<br\/>/g,' ');

                    var lines = element.split("\n\n");
                    for(var i in lines){
                        lines[i] = CKEDITOR.tools.trim(lines[i]);
                    }
                    element = lines.join("\n\n");


                    editor.insertText(element);
                    //alert(lines);
                }
            });
        editor.ui.addButton( 'RemoveSpace',{
            label: 'Supprimer les espaces',
            command: 'removespacecmd',
            icon: this.path+"images/remove.png"
        } );

        if ( editor.contextMenu ){
            editor.addMenuGroup( 'wiki' );
            editor.addMenuItem('removespacemenu',
                               {
                                label : 'Supprimer les espaces',
                                icon : this.path+"images/remove.png",
                                command : 'removespacecmd',
                                group : 'wiki'
                                });
            editor.contextMenu.addListener( function( element ){
                if ( element && !element.isReadOnly() && !element.data( 'cke-realelement' ) )
                    return { removespacemenu : CKEDITOR.TRISTATE_OFF };
                return null;
            });
        }
    }
});