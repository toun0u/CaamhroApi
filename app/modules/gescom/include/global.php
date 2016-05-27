<?php
//Lejal Simon
//Implémentation des helpers pour ce module
require_once DIMS_APP_PATH . '/modules/gescom/helpers/application_helper.php';
require_once DIMS_APP_PATH . '/include/class_todo.php';
require_once DIMS_APP_PATH.'modules/doc/class_docfile.php';

Gescom\include_dir(DIMS_APP_PATH . '/modules/forms/models/');

require_once DIMS_APP_PATH.'modules/catalogue/include/class_article.php';
require_once DIMS_APP_PATH.'modules/catalogue/include/class_famille.php';

require_once DIMS_APP_PATH.'modules/system/case/class_case.php';

require_once DIMS_APP_PATH.'modules/system/class_matrix.php';

Gescom\include_dir(DIMS_APP_PATH . '/modules/gescom/helpers/');
Gescom\include_dir(DIMS_APP_PATH . '/modules/gescom/models/');

define('_DASHBOARD_NB_ELEMS_DISPLAY',1);

global $listTypeForms;
$listTypeForms = array(
	article::MY_GLOBALOBJECT_CODE => "Article",
	cata_famille::MY_GLOBALOBJECT_CODE => "Famille",
); // plutot que 1/2 utiliser le MY_GLOBALOBJECT_CODE
