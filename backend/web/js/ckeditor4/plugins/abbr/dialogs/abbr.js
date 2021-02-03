CKEDITOR.dialog.add( 'abbrDialog', function( editor ) {
    console.log(editor.config.customValues.varsSelect);
    return {
        title: 'Вставить переменную',
        minWidth: 400,
        minHeight: 200,
        contents: [
            {
                id: 'tab-basic',
                elements: [
                    {
                        type: 'select',
                        id: 'abbr',
                        label: 'Переменная',
                        items: editor.config.customValues.varsSelect,
                        default: 'Football',
                    },
                ]
            }
        ],
        onOk: function() {
            var dialog = this;

            var abbr = editor.document.createElement( 'span' );
            abbr.setText( dialog.getValueOf( 'tab-basic', 'abbr' ) );
            editor.insertElement( abbr );
        }
    };
});
