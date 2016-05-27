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

	{ name : 'Titre contenu'	, element : 'h1', styles : { 'class' : 'h1_zooparis' } },
	{ name : 'Orange'	, element : 'h3', styles : { 'class' : 'h3_orange' } },
	{ name : 'Noir'	, element : 'h3', styles : { 'class' : 'h3_noir' } },	
	{ name : 'Normal'	, element : 'p', styles : { 'class' : 'p_normal' } },


	/* Object Styles */
        {
		name : 'Titre contenu',
		element : 'h1',
		attributes :
		{
			'class' : 'h1_zooparis'
		}
	},
        {
		name : 'Orange',
		element : 'h3',
		attributes :
		{
			'class' : 'h3_orange'
		}
	},
        {
		name : 'Noir',
		element : 'h3',
		attributes :
		{
			'class' : 'h3_noir'
		}
	},	
        {
		name : 'Normal',
		element : 'p',
		attributes :
		{
			'class' : 'p_normal'
		}
	},	
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
