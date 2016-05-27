CKEDITOR.plugins.add('ajaxsave',
    {
        init: function(editor)
        {
            var pluginName = 'ajaxsave';
            editor.addCommand( pluginName,
            {
                exec : function( editor )
                {
                    //$.post("/modules/wce/", {data : editor.getSnapshot() } );
                },
                canUndo : true
            });
            editor.ui.addButton('Ajaxsave',
            {
                label: 'Save',
                command: pluginName,
                 icon: this.path+'images/save.png'
            });
        }
    });