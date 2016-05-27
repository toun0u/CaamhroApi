<?php
/*
 * Created on 7 ao�t 2007
 *
 * Dims Version 3.0.x - See the ./include/config.php file for the full version number.
 * This program is provided WITHOUT warranty under the GNU/GPL license.
 * See the LICENSE file for more information about the GNU/GPL license.
 * Contributors are listed in the CREDITS and CHANGELOG files in this package.
 * Copyright (C) 2000 - 2009, SARL Netlor, http://www.netlor.fr/
 * Do NOT edit or remove this copyright or licence information upon redistribution.
 *
 */
dims_init_module('system');
require_once(DIMS_APP_PATH . "/modules/system/include/business.php");
$moduleid=dims_load_securvalue('moduleid',dims_const::_DIMS_NUM_INPUT,true,false,false);
$workspaces = dims_viewworkspaces(dims_load_securvalue('moduleid', dims_const::_DIMS_NUM_INPUT, true, true, true));

switch($dims_op) {
	case 'title':
		$moduleid= dims_load_securvalue('moduleid', dims_const::_DIMS_NUM_INPUT, true, true, true);
		switch($idobject) {
			case dims_const::_SYSTEM_OBJECT_ACTION:
				require_once(DIMS_APP_PATH . '/modules/system/class_action.php');
				$obj=new action();
				$obj->open($idrecord);
				$label=$obj->fields['libelle'];
				break;
			/*case dims_const::_SYSTEM_OBJECT_INTERLOCUTEUR:
				require_once(DIMS_APP_PATH . '/modules/system/class_interlocuteur.php');
				$obj=new interlocuteur();
				$obj->open($idrecord);
				$label=$obj->fields['nom'];
				break;*/
			case dims_const::_SYSTEM_OBJECT_CONTACT:
				require_once(DIMS_APP_PATH . '/modules/system/class_contact.php');
				$obj=new contact();
				$obj->open($idrecord);
				if (isset($obj->fields['lastname'])) {
					$label=$obj->fields['lastname']." ".$obj->fields['firstname'];
				}
				else {
					$label='';
				}
				break;
			case dims_const::_SYSTEM_OBJECT_TIERS:
				require_once(DIMS_APP_PATH . '/modules/system/class_tiers.php');
				$obj=new tiers();
				$obj->open($idrecord);
				$label=$obj->fields['intitule'];
				break;
		}
		break;
	case 'content':
		// on affiche les propriétés par défaut de l'objet
		$moduleid= dims_load_securvalue('moduleid', dims_const::_DIMS_NUM_INPUT, true, true, true);
		switch($idobject) {
			case dims_const::_SYSTEM_OBJECT_ACTION:
				require_once(DIMS_APP_PATH . '/modules/system/class_action.php');
				$obj=new action();
				$obj->open($idrecord);
				$label=$obj->fields['libelle'];
				break;
			case dims_const::_SYSTEM_OBJECT_INTERLOCUTEUR:
				require_once(DIMS_APP_PATH . '/modules/system/class_interlocuteur.php');
				$obj=new interlocuteur();
				$obj->open($idrecord);
				$label=$obj->fields['nom'];
				break;
			case dims_const::_SYSTEM_OBJECT_CONTACT:
				require_once(DIMS_APP_PATH . '/modules/system/class_contact.php');
				$obj=new contact();
				$obj->open($idrecord);
				$label=$obj->fields['lastname']." ";$obj->fields['firstname'];
				break;
			case dims_const::_SYSTEM_OBJECT_TIERS:
				require_once(DIMS_APP_PATH . '/modules/system/class_tiers.php');
				$obj=new tiers();
				$obj->open($idrecord);
				$label=$obj->fields['intitule'];
				break;
		}
		echo dims_getContent($moduleid,$idobject,$idrecord,$obj,$label);
		break;
	case 'searchfavorites':
	case 'search':
	case 'newsearch':
	case 'searchnews':
		if ($_SESSION["dims"]["connected"] && $_SESSION['dims']['workspaceid'] >0) {
			if ($_SESSION['dims']['desktop']=="portal") {
				if (dims_ismanager()) {
					if ($_SESSION['dims']['adminlevel'] >= dims_const::_DIMS_ID_LEVEL_GROUPMANAGER) {
						if ($_SESSION['dims']['adminlevel'] >= dims_const::_DIMS_ID_LEVEL_SYSTEMADMIN) {
							echo "<div style=\"width:80px;float:left;text-align:center;margin-top:2px;margin-right:10px;\">";
							echo "<a href=\"".dims_urlencode("admin.php?dims_moduleid=".dims_const::_DIMS_MODULE_SYSTEM."&dims_desktop=block&dims_action=admin&system_level=system")."\"><img src=\"./common/modules/system/img/system.png\" alt=\"\"><br/>".$_DIMS['cste']['_DIMS_LABEL_SYSTEM']."</a>";
							echo "</div>";
						}
						echo "<div style=\"width:120px;float:left;text-align:center;margin-top:2px;margin-right:10px;\">";
						echo "<a href=\"".dims_urlencode("admin.php?dims_moduleid=".dims_const::_DIMS_MODULE_SYSTEM."&dims_desktop=block&dims_action=admin&system_level=".dims_const::_SYSTEM_WORKSPACES)."\"><img src=\"./common/modules/system/img/workspace.png\" alt=\"\"><br/>".$_DIMS['cste']['_DIMS_ADMIN_WORKSPACES']."</a>";
						echo "</div>";
						if ($_SESSION['dims']['adminlevel'] >= dims_const::_DIMS_ID_LEVEL_SYSTEMADMIN) {
						echo "<div style=\"width:80px;float:left;text-align:center;margin-top:2px;margin-right:10px;\">";
						echo "<a href=\"".dims_urlencode("admin.php?dims_moduleid=".dims_const::_DIMS_MODULE_SYSTEM."&dims_desktop=block&dims_action=admin&system_level=system&dims_moduleicon=system_logs")."\"><img src=\"./common/modules/system/img/log.png\" alt=\"\"><br/>".$_DIMS['cste']['_DIMS_ADMIN_LOGS']."</a>";
						echo "</div>";
						}
					}
				}
				echo "<div style=\"clear:both;width:100%;margin-top:2px;float:left;\">";

				echo "<table style=\"width:100%;\">";

				echo "<tr class=\"trl1\"><td>".$_DIMS['cste']['_DIMS_LABEL_CONNECTEDUSERS']."</td><td>".$_SESSION['dims']['connectedusers']."</td></tr>";
				echo "<tr class=\"trl2\"><td>".$_DIMS['cste']['_DIMS_LABEL_ACTIVITY_WORKSPACE']."</td><td>";

				// calcul du nombre d'action
				$dday=Date("Ym");
				$lst="";

				foreach ($_SESSION['dims']['currentworkspace']['modules'] as $key=>$value) {
					if ($lst!="") $lst.=",$key";
					else $lst="$key";
				}
				if ($lst!="") {
					$dlast=Date("Ym",mktime(0, 0, 0, date("m")-1, "01",   date("Y")));
					$query="SELECT count( id_action ) as cpte, left( timestp, 6 ) AS temps
						FROM `dims_user_action_log`
						WHERE left( timestp, 6 ) = :dday
						OR left( timestp, 6 ) = :dlast
						and id_module in (".$lst.")
						GROUP BY left( timestp, 6 )
						order by left( timestp, 6 )";
					// initialize variables
					$cpteday=0;
					$cptelast=0;
					//query

					$res=$db->query($query, array(':dday' => $dday, ':dlast' => $dlast) );
					while ($f=$db->fetchrow($res)) {
						if ($f['temps']==$dlast) $cptelast=$f['cpte'];
						else $cpteday=$f['cpte'];
					}

					if ($cpteday>$cptelast) echo "<img src=\"./common/modules/system/img/arrow_up.png\">";
					else echo "<img src=\"./common/modules/system/img/arrow_down.png\">";

					echo "&nbsp;".$cpteday." ".$_DIMS['cste']['_LABEL_ACTIONS']." (".$cptelast." ".$_DIMS['cste']['_DIMS_LABEL_LAST_MONTH'].")";
				}
				echo "</td></tr>";

				if ($_SESSION['dims']['adminlevel'] >= dims_const::_DIMS_ID_LEVEL_SYSTEMADMIN) {
					echo "<tr class=\"trl1\"><td>".$_DIMS['cste']['_DIMS_LABEL_ACTIVITY']."</td><td>";

					// calcul du nombre d'action
					$dday=Date("Ym");

					$dlast=Date("Ym",mktime(0, 0, 0, date("m")-1, "01",   date("Y")));
					$query="SELECT count( id_action ) as cpte, left( timestp, 6 ) AS temps
						FROM `dims_user_action_log`
						WHERE left( timestp, 6 ) =  :dday
						OR left( timestp, 6 ) = :dlast
						GROUP BY left( timestp, 6 )
						order by left( timestp, 6 )";
					// initialize variables
					$cpteday=0;
					$cptelast=0;

					//query
					$res=$db->query($query, array(':dday' => $dday, ':dlast' => $dlast) );
					while ($f=$db->fetchrow($res)) {
						if ($f['temps']==$dlast) $cptelast=$f['cpte'];
						else $cpteday=$f['cpte'];
					}

					if ($cpteday>$cptelast) echo "<img src=\"./common/modules/system/img/arrow_up.png\">";
					else echo "<img src=\"./common/modules/system/img/arrow_down.png\">";

					echo "&nbsp;".$cpteday." ".$_DIMS['cste']['_LABEL_ACTIONS']." (".$cptelast." ".$_DIMS['cste']['_DIMS_LABEL_LAST_MONTH'].")";
					echo "</td></tr>";
				}

				$query="SELECT count( id_record ) as cpte
						FROM dims_keywords_index
						WHERE id_workspace= :workspaceid ";

				// initialize variables
				$cptecorresp=0;
				$cpteword=0;

				//query
				$res=$db->query($query, array(':workspaceid' => $_SESSION['dims']['workspaceid']) );
				if ($f=$db->fetchrow($res)) {
					$cptecorresp=$f['cpte'];
				}

				$query="SELECT count( id) as cpte
					FROM dims_keywords";

				$res=$db->query($query);
				if ($f=$db->fetchrow($res)) {
					$cpteword=$f['cpte'];
				}
				echo "<tr><td>".$_DIMS['cste']['_DIMS_LABEL_WORDS_INDEXED']."</td><td>".$cptecorresp." ".$_DIMS['cste']['_DIMS_LABEL_KEYWORDS']."</td><tr>";
				echo "<tr><td>".$_DIMS['cste']['_DIMS_LABEL_DICO']."</td><td>".$cpteword." ".$_DIMS['cste']['_DIMS_LABEL_KEYWORDS']."</td></tr>";
				echo "</table></div>";
			}
			else {
				// mode block
				require_once(DIMS_APP_PATH . '/modules/system/block_portal_search.php');
			}
		}
		break;

	default:
		//require_once(DIMS_APP_PATH . '/modules/agenda/block_portal_search.php');
		break;
}
?>
