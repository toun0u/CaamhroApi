<?
	require_once 'tools/browser_detect.php' ;


	function NS_CSS_SEGMENT($path="") {
			$CSS = '

				<link rel="stylesheet" href="'.$path.'css/NSButton.css" type="text/css"/>
				<link rel="stylesheet" href="'.$path.'css/NSFrame.css" type="text/css"/>
				' ;

		/*
		if (IsFx()) {

			$CSS .= '
					<link rel="stylesheet" href="'.$path.'css/firefox.css" type="text/css" />
					<link rel="stylesheet" href="'.$path.'css/NSApplication_fx.css" type="text/css"/>
					<link rel="stylesheet" href="'.$path.'css/NSDataListBrowser_fx.css" type="text/css"/>
					<link rel="stylesheet" href="'.$path.'css/NSFrame_fx.css" type="text/css"/>
					<link rel="stylesheet" href="'.$path.'css/NSGrid_fx.css" type="text/css"/>
					<link rel="stylesheet" href="'.$path.'css/NSInputField_fx.css" type="text/css"/>
			';

		}

		if (IsSaf()) {
			$CSS .= '
					<link rel="stylesheet" href="'.$path.'css/NSDataTable_saf.css" type="text/css"/>
					<link rel="stylesheet" href="'.$path.'css/NSItemCard_saf.css" type="text/css"/>
					' ;
		}

		$CSS .= '
					<link rel="stylesheet" href="'.$path.'css/footer.css" type="text/css"/>
					' ;
		*/
		return $CSS ;
	}

?>
