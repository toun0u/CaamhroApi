<?php
$http_host = (isset($_SERVER['HTTP_HOST'])) ? $_SERVER['HTTP_HOST'] : '';

// detection du point en fin du nom de host
if (substr($http_host,strlen($http_host)-1) =='.') {
	$http_host=substr($http_host, 0,strlen($http_host)-1);

	if (isset($_SERVER['REQUEST_URI'])) {
		$http_host.=$_SERVER['REQUEST_URI'];
	}

	if ((isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS'])=="on")
				|| (isset($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS'])=='on'))  {
		$http_host="https://".$http_host;
	}
	else {
		$http_host="http://".$http_host;
	}

	header("Location: ".$http_host);
	die();
}

date_default_timezone_set("Europe/Paris");
defined('_DIMS_DEFAULT_TIMEZONE')						OR define ('_DIMS_DEFAULT_TIMEZONE', 1); // GMT +1
defined('_DIMS_DEFAULT_COUNTRY')						OR define ('_DIMS_DEFAULT_COUNTRY', 73); // on suppose que la table des country restera inchangée

// Path management
defined('DIMS_APP_PATH')								OR define ('DIMS_APP_PATH', realpath("../") . '/app/');
defined('DIMS_ROOT_PATH')								OR define ('DIMS_ROOT_PATH', realpath("../") . '/');
defined('DIMS_WEB_PATH')								OR define ('DIMS_WEB_PATH', $_SERVER['DOCUMENT_ROOT']);
defined('DIMS_TMP_PATH')								OR define ('DIMS_TMP_PATH', realpath("../")."/tmp/");
defined('_DIMS_PATHDATA')								OR define ('_DIMS_PATHDATA', realpath(realpath("../") . "/data")."/");
defined('_WCE_MODELS_PATH')								OR define ('_WCE_MODELS_PATH', realpath(realpath("../") . '/data/templates'));
defined('_DIMS_WEBPATHDATA')							OR define ('_DIMS_WEBPATHDATA', './data/');
defined('_DIMS_MAXFILESIZE')							OR define ('_DIMS_MAXFILESIZE', '4500000000'); // dont forget php.ini upload_max_filesize value
defined('_WCE_WEB_MODELS_PATH')							OR define ('_WCE_WEB_MODELS_PATH', './data/templates');

// Assets
defined('ASSETS_PATH')									OR define ('ASSETS_PATH', 'assets/');
defined('STYLES_PATH')									OR define ('STYLES_PATH', ASSETS_PATH.'stylesheets/');
defined('STYLES_COMMON_PATH')							OR define ('STYLES_COMMON_PATH', STYLES_PATH.'common/');
defined('IMAGES_PATH')									OR define ('IMAGES_PATH', ASSETS_PATH.'images/');
defined('IMAGES_COMMON_PATH')							OR define ('IMAGES_COMMON_PATH', IMAGES_PATH.'common/');
defined('SCRIPTS_PATH')									OR define ('SCRIPTS_PATH', ASSETS_PATH.'javascripts/');
defined('SCRIPTS_COMMON_PATH')							OR define ('SCRIPTS_COMMON_PATH', SCRIPTS_PATH.'common/');

defined('_DIMS_DB_LOGIN')								OR define ('_DIMS_DB_LOGIN','');
defined('_DIMS_DB_PASSWORD')							OR define ('_DIMS_DB_PASSWORD','');
defined('_DIMS_DB_DATABASE')							OR define ('_DIMS_DB_DATABASE','');
defined('_DIMS_DEFAULT_TIMEZONE')						OR define ('_DIMS_DEFAULT_TIMEZONE', 1);
defined('_DIMS_CHAT_ACTIVE')							OR define ('_DIMS_CHAT_ACTIVE', false);
defined('_CONSTANTIZER_TOOL')							OR define ('_CONSTANTIZER_TOOL', false);
defined('_DIMS_SOAP_SERVER')							OR define ('_DIMS_SOAP_SERVER', false);
defined('_DEBUG_EMAIL_ADDRESS')							OR define ('_DEBUG_EMAIL_ADDRESS', '');

// Desktop V2
defined('_ACTIVE_DESKTOP_V2')							OR define ('_ACTIVE_DESKTOP_V2', false);
defined('_DESKTOP_TPL_PATH')							OR define ('_DESKTOP_TPL_PATH', '/common/modules/system/desktopV2/templates/');
defined('_DESKTOP_TPL_LOCAL_PATH')						OR define ('_DESKTOP_TPL_LOCAL_PATH', DIMS_APP_PATH . 'modules/system/desktopV2/templates/');

defined('_ACTIVE_GESCOM')								OR define ('_ACTIVE_GESCOM', false);
defined('_DIMS_CATCH_DUPLICATE_ENTRIES')				OR define ('_DIMS_CATCH_DUPLICATE_ENTRIES', false);

// Dims XMPP identifier definitions
defined('_DIMS_XMPP_HOST')								OR define ('_DIMS_XMPP_HOST', ''); // ex: talk.google.com
defined('_DIMS_XMPP_PORT')								OR define ('_DIMS_XMPP_PORT', 5222);
defined('_DIMS_XMPP_USER')								OR define ('_DIMS_XMPP_USER', '');
defined('_DIMS_XMPP_PASSWORD')							OR define ('_DIMS_XMPP_PASSWORD', '');
defined('_DIMS_XMPP_RESOURCE')							OR define ('_DIMS_XMPP_RESOURCE', 'dims-xmpphp');
defined('_DIMS_XMPP_SERVER')							OR define ('_DIMS_XMPP_SERVER', ''); // ex: gmail.com

// system configuration
defined('_DIMS_SQL_LAYER')								OR define ('_DIMS_SQL_LAYER','mysql'); // layers supported : mysql only (oracle, postgres, ms-access... to come)
defined('_DIMS_DB_SERVER')								OR define ('_DIMS_DB_SERVER','localhost');
defined('_DIMS_APACHE_GROUP')							OR define ('_DIMS_APACHE_GROUP',"www-data");
defined('_DIMS_MYSQLPATH')								OR define ('_DIMS_MYSQLPATH','/usr/local/mysql');
defined('_DIMS_JAVAPATH')								OR define ('_DIMS_JAVAPATH','java');
defined('_DOCUMENT_ROOT')								OR define ('_DOCUMENT_ROOT', $_SERVER['DOCUMENT_ROOT']);
defined('_DIMS_BINPATH')								OR define ('_DIMS_BINPATH','/usr/bin/');

defined('_DIMS_JOOCONVERTER_PATH')                      OR define('_DIMS_JOOCONVERTER_PATH', realpath(DIMS_APP_PATH . '/scripts/jodconverter-2.2.2/lib/jodconverter-cli-2.2.2.jar'));
defined('_DIMS_OOCONVERTER')                            OR define('_DIMS_OOCONVERTER', 'jooconverter'); /* unoconv, jooconverter */

//Configuration de la synchronisation XML du Dims --------------------------------------------------
defined('_DIMS_SYNC_XSD_SCHEMA')						OR define ('_DIMS_SYNC_XSD_SCHEMA', './include/schemas/ddo_syncxml.xsd');
defined('_DIMS_SYNC_EXPORT_PATH')						OR define ('_DIMS_SYNC_EXPORT_PATH', './export/xml/'); //part du principe que le path existe

//export
defined('_DIMS_SYNC_OUT_NOTHING_TO_EXPORT')				OR define ('_DIMS_SYNC_OUT_NOTHING_TO_EXPORT', 0); //rien à exporter sur l'objet
defined('_DIMS_SYNC_OUT_OK')							OR define ('_DIMS_SYNC_OUT_OK', 1); //export xml réussi

//import
defined('_DIMS_SYNC_IN_MALFORMED')						OR define ('_DIMS_SYNC_IN_MALFORMED', 0);//rien à exporter sur l'objet
defined('_DIMS_SYNC_WRONG_IDS_POSITION')				OR define ('_DIMS_SYNC_WRONG_IDS_POSITION', 1);//la position des ids est incorrecte
defined('_DIMS_SYNC_UNKNOWN_PRIMARY_COLUMN')			OR define ('_DIMS_SYNC_UNKNOWN_PRIMARY_COLUMN', 2);//la colonne en cours d'import n'est pas connue
defined('_DIMS_SYNC_IDS_MISSING')						OR define ('_DIMS_SYNC_IDS_MISSING', 3);//si il n'y a aucun id
defined('_DIMS_SYNC_WRONG_DATA_OBJECT')					OR define ('_DIMS_SYNC_WRONG_DATA_OBJECT', 4);//si l'import se fait pour un objet de la mauvaise table de bdd
defined('_DIMS_SYNC_IN_UNKNOWN_MODE')					OR define ('_DIMS_SYNC_IN_UNKNOWN_MODE', 5);//mode d'import inconnu
defined('_DIMS_SYNC_IN_ADD_OBJECT_ALREADY_EXISTING')	OR define ('_DIMS_SYNC_IN_ADD_OBJECT_ALREADY_EXISTING', 6);//on tente d'ajouter un objet dont l'id existe déjà
defined('_DIMS_SYNC_IN_UNKNOWN_OBJECT')					OR define ('_DIMS_SYNC_IN_UNKNOWN_OBJECT', 7);//on tente de faire un update sur un objet inexistant
defined('_DIMS_SYNC_NOTHING_TO_DO')						OR define ('_DIMS_SYNC_NOTHING_TO_DO', 8);//on fait un import dans lequel il n'y a aucun champ
defined('_DIMS_SYNC_IN_REQUIRED_FIELD_MISSING')			OR define ('_DIMS_SYNC_IN_REQUIRED_FIELD_MISSING', 9);//un champ requis est manquant
defined('_DIMS_SYNC_IN_UNKNOWN_ENUMERE')				OR define ('_DIMS_SYNC_IN_UNKNOWN_ENUMERE', 10);//la valeur fournie par l'xml pour un champ n'est pas valide parmi ses énumérés
defined('_DIMS_SYNC_IN_OK')								OR define ('_DIMS_SYNC_IN_OK', 11);//la valeur fournie par l'xml pour un champ n'est pas valide parmi ses énumérés
defined('_DIMS_SYNC_UNKNOWN_GLOBALOBJECT')				OR define('_DIMS_SYNC_UNKNOWN_GLOBALOBJECT', 12);//Id globalobject à traiter inconnu
defined('_DIMS_SYNC_UNKNOWN_RELATED_GLOBALOBJECT')		OR define('_DIMS_SYNC_UNKNOWN_RELATED_GLOBALOBJECT', 13);//tentative de création de relation sur un id_globalobject inconnu
defined('_DIMS_SYNC_RELATED_OBJECT_ORIGIN_MISSING')		OR define('_DIMS_SYNC_RELATED_OBJECT_ORIGIN_MISSING', 14);//objet en relation inconnu
defined('_DIMS_SYNC_NOT_UPTODATE')						OR define('_DIMS_SYNC_NOT_UPTODATE', 15);//Synchro inutile, l'objet qu'on te renvoie n'est pas uptodate
defined('_DIMS_SYNC_UNKNOWN_DIMS_KEY')					OR define('_DIMS_SYNC_UNKNOWN_DIMS_KEY', 16);//Si la clef du dims de l'objet qui envoie n'est pas connu localement
defined('_DIMS_SYNC_UNAUTHORIZED')						OR define('_DIMS_SYNC_UNAUTHORIZED', 17);//Clef de dims non authorisée à communiquer avec le dims local

//---------------------------------------------------------------------------------------------------

// Charset encoding
defined('_DIMS_ENCODING')								OR define ('_DIMS_ENCODING','UTF-8'); // caracters encoding

// Smarty engine
defined('_SMARTY_API')									OR define ('_SMARTY_API', DIMS_APP_PATH.'smarty/src/Smarty-3.1.19/libs/Smarty.class.php');
defined('_SMARTY_DEBUG')								OR define ('_SMARTY_DEBUG', false);
defined('_SMARTY_PATH')									OR define ('_SMARTY_PATH', DIMS_APP_PATH.'smarty');

// IDS Part
defined('_DIMS_IDS')									OR define ('_DIMS_IDS',false);
defined('_DIMS_IDS_SECURITY_LEVEL')						OR define ('_DIMS_IDS_SECURITY_LEVEL',10);
defined('_IDS_API')										OR define ('_IDS_API', '/usr/local/lib/php/phpids/lib/');

// GNUPG - Gnu Privacy Guard configuration
defined('_DIMS_GNUPG')									OR define ('_DIMS_GNUPG',false);
defined('_DIMS_GNUPG_KEYPATH')							OR define ('_DIMS_GNUPG_KEYPATH', '/data/www/.gnupg');

if (_DIMS_GNUPG && file_exists(_DIMS_GNUPG_KEYPATH)) {
	putenv('GNUPGHOME='._DIMS_GNUPG_KEYPATH);
}

//JPGRAPH
defined('TTF_DIR')										OR define('TTF_DIR','/usr/share/fonts/truetype/msttcorefonts/');

// security and report management
defined('_DIMS_SESSIONTIME')							OR define ('_DIMS_SESSIONTIME','600'); // time in second
defined('_DIMS_SECURITY_CRITICAL_TIMEROUT')				OR define ('_DIMS_SECURITY_CRITICAL_TIMEROUT',600);
defined('_DIMS_DEBUGMODE')								OR define ('_DIMS_DEBUGMODE', false);
isset($_SESSION['dims_debug'])							OR $_SESSION["dims_debug"]["ip"][]	= "127.0.0.1";
defined('_DIMS_DEBUGMODE_PROD')							OR define ('_DIMS_DEBUGMODE_PROD', true);
defined('_DIMS_DISPLAY_ERRORS')							OR define ('_DIMS_DISPLAY_ERRORS', false);
defined('_DIMS_MAIL_ERRORS')							OR define ('_DIMS_MAIL_ERRORS', false);
defined('_DIMS_ADMINMAIL')								OR define ('_DIMS_ADMINMAIL', 'admin@netlor.fr');
defined('_DIMS_ERROR_REPORTING')						OR define ('_DIMS_ERROR_REPORTING', 0); //	E_ALL E_ERROR	E_WARNING  E_PARSE	E_NOTICE
defined('_DIMS_ACTIVELOG')								OR define ('_DIMS_ACTIVELOG', false);
defined('_DIMS_URL_ENCODE')								OR define ('_DIMS_URL_ENCODE', false);
defined('_DIMS_FRONTOFFICE')							OR define ('_DIMS_FRONTOFFICE', true); // true if frontoffice is activated
defined('_DIMS_FRONTOFFICE_REWRITERULE')				OR define ('_DIMS_FRONTOFFICE_REWRITERULE', false); // true if frontoffice rewrite rules are activated
defined('_DIMS_DEFAULT_TEMPLATE')						OR define ('_DIMS_DEFAULT_TEMPLATE', 'dims_v5');
defined('_DIMS_USE_COMPLEXE_PASSWORD')					OR define ('_DIMS_USE_COMPLEXE_PASSWORD', false);

// uploads document
defined('_DIMS_PROGRESSBAR_USED')						OR define ('_DIMS_PROGRESSBAR_USED',true); // active progress upload bar
defined('_DIMS_TEMPORARY_UPLOADING_FOLDER')				OR define ('_DIMS_TEMPORARY_UPLOADING_FOLDER', DIMS_TMP_PATH); // define temporary temp folder for uploading file

// Rewriting front content
defined('_DIMS_URLREWRITE')								OR define ('_DIMS_URLREWRITE',true);
defined('_DIMS_SEP_URLREWRITE')							OR define ('_DIMS_SEP_URLREWRITE',"/");
defined('_DIMS_DYNAMIC_INCORRECT_REWRITE')				OR define ('_DIMS_DYNAMIC_INCORRECT_REWRITE',true);

// Specific additional CRM module
isset($_SESSION['dims']['_DIMS_SPECIFIC'])				OR $_SESSION['dims']['_DIMS_SPECIFIC'] = false;
isset($_SESSION['dims']['_PREFIX'])						OR $_SESSION['dims']['_PREFIX'] = 'cram';

// Multi server configuration
defined('_DIMS_MULTI_SERVER')							OR define ('_DIMS_MULTI_SERVER', false);//Gestion des multi-serveurs (pour load-balancing)
defined('_DIMS_TTL_LOAD_BALANCING')						OR define ('_DIMS_TTL_LOAD_BALANCING', 300);				//En seconde

// Dims XMPP identifier definitions

// Sync constantes
defined('_DIMS_SYNC_ACTIVE')							OR define ('_DIMS_SYNC_ACTIVE', false);

// Sync constantes
defined('_DIMS_SOAP_SERVER')							OR define ('_DIMS_SOAP_SERVER', false);

// Debug view mode
defined('_VIEW_DEBUG')									OR define ('_VIEW_DEBUG', false);

if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) { //Verifie si l'utilisateur est passe via un load balancer
	if (!defined('_DIMS_IP_CLIENT'))					define("_DIMS_IP_CLIENT", $_SERVER["HTTP_X_FORWARDED_FOR"]);
	if (isset( $_SERVER["REMOTE_ADDR"])) {
		if (!defined('_DIMS_IP_LOAD_BALANCER'))			define("_DIMS_IP_LOAD_BALANCER", $_SERVER["REMOTE_ADDR"]);
	}
}

if (!defined('_DIMS_SECURITY_CRYPTO_SALT'))             define ('_DIMS_SECURITY_CRYPTO_SALT','Na*t:3,?'); // une fois les premiers user créé ne pas toucher
if (!defined('_DIMS_SECURITY_CRYPTO_MODE'))             define ('_DIMS_SECURITY_CRYPTO_MODE','crypt');
if (!defined('_DIMS_SECURITY_CRYPTO_COST'))             define ('_DIMS_SECURITY_CRYPTO_COST','13');

// Prompt de connexion - couleurs
if (! defined('_DIMS_CONNEXION_COLOR'))					define('_DIMS_CONNEXION_COLOR', '#59B259');

if (!defined('_DIMS_BLOCK_MBR_PURGE'))                  define ('_DIMS_BLOCK_MBR_PURGE', false);//uniquement pendant les développements, permet de ne pas réinitialiser les mb_object relation
if (!defined('APC_EXTENSION_LOADED'))                   define('APC_EXTENSION_LOADED', extension_loaded('apc') && ini_get('apc.enabled'));
if (!defined('APC_CACHE_TIME'))							define ('APC_CACHE_TIME', 3600);

// Prompt de connexion - couleurs
if (! defined('_DIMS_CONNEXION_COLOR'))                 define('_DIMS_CONNEXION_COLOR', '#59B259');

defined('DIMS_VERSION')                                 OR define ('DIMS_VERSION', 'dims6.1.0');
defined('CATAKERNEL_VERSION')                           OR define ('CATAKERNEL_VERSION', 'cata6.5.7');
