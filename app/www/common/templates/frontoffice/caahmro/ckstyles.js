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

	{ name : 'Titre contenu'	, element : 'h1', styles : { 'class' : 'h1_caahmro' } , styles : { 'font-family' : 'helvetica' , 'font-size' : '2em' , 'text-indent' : '0.3em' , 'color' : 'rgb(100, 100, 100) !important' , 'border-bottom' : '1px solid rgb(239, 93, 53)' , 'border-left' : '4px solid #DC5B40' }  },
	{ name : 'Orange H2'	, element : 'h2', styles : { 'class' : 'h2_orange' } , styles : { 'font-family' : 'helvetica' , 'color' : '#DC5B40' , 'font-size' : '18px' }  },
	{ name : 'Orange H3'	, element : 'h3', styles : { 'class' : 'h3_orange' } , styles : { 'font-family' : 'helvetica' , 'color' : '#DC5B40' , 'font-size' : '16px' }  },
	{ name : 'Noir'	, element : 'h3', styles : { 'class' : 'h3_noir' } , styles : { 'font-family' : 'helvetica' , 'color' : '#646464' , 'font-size' : '16px' }  },
	{ name : 'Normal'	, element : 'p', styles : { 'class' : 'p_normal' } , styles : { 'font-family' : 'helvetica' , 'color' : '#646464' , 'font-size' : '11px' }  },



]);
