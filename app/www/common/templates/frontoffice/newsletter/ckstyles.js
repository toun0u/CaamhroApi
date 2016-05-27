CKEDITOR.stylesSet.add( 'default',
[
	/* Block Styles */

	// These styles are already available in the "Format" combo, so they are
	// not needed here by default. You may enable them to avoid placing the
	// "Format" combo in the toolbar, maintaining the same features.
	/*
	{ name : 'Paragraph'		, element : 'p' },
	{ name : 'Heading 1'		, element : 'h1' },
	{ name : 'Heading 2'		, element : 'h2' },
	{ name : 'Heading 3'		, element : 'h3' },
	{ name : 'Heading 4'		, element : 'h4' },
	{ name : 'Heading 5'		, element : 'h5' },
	{ name : 'Heading 6'		, element : 'h6' },
	{ name : 'Preformatted Text', element : 'pre' },
	{ name : 'Address'			, element : 'address' },
	*/

	{ name : 'Titre bleu H1'		, element : 'h1', styles : { 'color' : '#00328D' } },
	{ name : 'Titre vert H1'		, element : 'h1', styles : { 'color' : '#48B06A' } },
	{ name : 'Titre bleu H3'		, element : 'h3', styles : { 'color' : '#00328D' } },
	{ name : 'Titre vert H3'		, element : 'h3', styles : { 'color' : '#48B06A' } },	
	{ name : 'Sous titre bleu H3'		, element : 'h3', styles : { 'color' : '#00328D' } },
	{ name : 'Sous titre vert H3'		, element : 'h3', styles : { 'color' : '#48B06A' } },
	{ name : 'Sous titre orange H3'		, element : 'h3', styles : { 'color' : '#B95814' } },
	{ name : 'Texte normal'		, element : 'p', styles : { 'color' : '#424242' } },
	{ name : 'Texte Accueil'	, element : 'h3', styles : { 'font-size' : '1.2em' } },
	{ name : 'Titre contenu bordure'	, element : 'h3', styles : { 'color' : 'white', 'font-size' : '2em' , 'border-left' : '5px solid #48B06A' , 'border-bottom' : '1px solid rgb(200, 200, 200)' , 'text-indent' : '0.3em' , 'margin-bottom' : '0.5em' } },
	{ name : 'Titre contenu bordure orange'	, element : 'h3', styles : { 'color' : 'white', 'font-size' : '2em' , 'border-left' : '5px solid #B95814' , 'border-bottom' : '2px solid #B95814' , 'text-indent' : '0.3em' , 'margin-bottom' : '0.5em' } },	
	{ name : 'En savoir plus'	, element : 'p', attributes : { 'class' : 'btn btn-primary right' } },


	/* Object Styles */
        {
		name : 'Paragraph',
		element : 'p',
		attributes :
		{
			'class' : 'wiki_paragraph'
		}
	},

	{
		name : 'Image on Left',
		element : 'img',
		attributes :
		{
			'style' : 'padding: 5px; margin-right: 5px',
			'border' : '2',
			'align' : 'left'
		}
	},

	{
		name : 'Image on Right',
		element : 'img',
		attributes :
		{
			'style' : 'padding: 5px; margin-left: 5px',
			'border' : '2',
			'align' : 'right'
		}
	},

	{ name : 'Borderless Table', element : 'table', styles: { 'border-style': 'hidden', 'background-color' : '#E6E6FA' } },
	{ name : 'Square Bulleted List', element : 'ul', styles : { 'list-style-type' : 'square' } }
]);
