<?
define ('_DOC_PAGE_TITLE', 'Gestion des Documents du module \'LABEL\'');

define ('_DOC_MYDOCUMENTS', 'Mes Documents');
define ('_DOC_ALLDOCUMENTS', 'Tous les Documents');
define ('_DOC_FOLDER', 'Dossiers');
define ('_DOC_NAME', 'Nom');
define ('_DOC_ROOT', 'Racine');
define ('_DOC_SEARCH', 'Recherche');
define ('_DOC_NEWFILE', 'Nouveau fichier');
define ('_DOC_PUBLISH', 'Publier');
define ('_DOC_DESK', 'Documents');

define ('_DOC_NEWFOLDER', 'Nouveau dossier');
define ('_DOC_LABEL_FILESFOUND', 'fichiers trouv&eacute;s');
define ('_DOC_LABEL_FILEFOUND', 'fichier trouv&eacute;');
define ('_DOC_LABEL_FILESNOFOUND', 'Aucun fichier trouv&eacute;');
define ('_DOC_LABEL_NO_FILESFOUND', 'Aucun fichier trouv&eacute;');
define ('_DOC_LABEL_ADD_OTHER_FILE', 'Ajout d\'un autre fichier');
define ('_DOC_MSG_UPLOAD_FILE', 'Envoi en cours');
define ('_DOC_MSG_COPY_FILE', 'Copie en cours');
define ('_DOC_MSG_UPLOAD_WAITING', 'Attente de connexion');
define ('_DOC_MSG_UPLOAD_ERROR', 'Erreur d\'envoi de fichier');
define ('_DOC_MSG_UPLOAD_ERROREXT', 'Cette extension n\'est pas autoris&eacute;e');
define ('_DOC_LABEL_ARBO', 'Arborescence');

define ('_DOC_LABEL_ADMIN', 'Administration');
define ('_DOC_LABEL_OPERATION', 'Op&eacute;ration');
define ('_DOC_LABEL_MOVE', 'D&eacute;placer');
define ('_DOC_LABEL_DELETE', 'Supprimer');
define ('_DOC_LABEL_UNCOMPRESS', 'D&eacute;compresser');

define ("_DOC_MSG_CONFIRMDELETEFILE", "Etes-vous sûr de vouloir supprimer ce fichier ?");
define ("_DOC_MSG_CONFIRMDELETEFOLDER", "Etes-vous sûr de vouloir supprimer ce dossier ?");

define ("_DOC_LABEL_ERROR_FOLDEREXISTS",		'<b>Erreur ! </b>Cr&eacute;ation du dossier impossible - le dossier existe d&eacute;jà');
define ("_DOC_LABEL_ERROR_UNAVAILABLEPATH",		'<b>Erreur ! </b>Cr&eacute;ation du dossier impossible - le chemin n\'est pas valide');
define ("_DOC_LABEL_ERROR_FOLDERNOTWRITABLE",	'<b>Erreur ! </b>Cr&eacute;ation du dossier impossible - le r&eacute;pertoire physique n\'est pas accessible en &eacute;criture');

define ("_DOC_LABEL_ERROR_EMPTYFILE",			'<b>Erreur ! </b>Enregistrement du fichier impossible - le fichier est vide');
define ("_DOC_LABEL_ERROR_FILENOTWRITABLE",		'<b>Erreur ! </b>Enregistrement du fichier impossible - le fichier n\'est pas accessible en &eacute;criture');
define ("_DOC_LABEL_ERROR_MAXFILESIZE",			'<b>Erreur ! </b>Enregistrement du fichier impossible - le fichier est trop volumineux');

define ("_DOC_LABEL_ERROR_FOLDERZIP",	'<b>Erreur ! </b>Impossible de d&eacute;zipper cette archive');
define ("_DOC_LABEL_ERROR_RULES",	'<b>Erreur ! </b>Vous n\'avez pas les droits n&eacute;cessaires');

define ('_DOC_LABEL_UNAUTHORIZED_DELETEFOLDER','Vous ne disposez pas des autorisations n&eacute;cessaires pour supprimer ce dossier');
define ('_DOC_LABEL_UNAUTHORIZED_DELETEFILE','Vous ne disposez pas des autorisations n&eacute;cessaires pour supprimer ce fichier');

global $foldertypes;
$foldertypes = array ('private' => 'Personnel', 'shared' => 'Partag&eacute;', 'public' => 'Public');

?>
