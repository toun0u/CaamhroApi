CKEDITOR.plugins.add('ajaxclose',
    {
        init: function(editor)
        {
            var pluginName = 'ajaxclose';
            editor.addCommand( pluginName,
            {
                exec : function( editor )
                {

                },
                canUndo : true
            });
            editor.ui.addButton('Ajaxclose',
            {
                label: 'Close',
                command: pluginName,
                icon: '/common/modules/wce/wiki/gfx/icone_suppression.png'
            });
        }
    });