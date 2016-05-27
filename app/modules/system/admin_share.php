<?
require_once(DIMS_APP_PATH . '/modules/system/class_share.php');
require_once(DIMS_APP_PATH . '/modules/system/class_user.php');
require_once(DIMS_APP_PATH . '/modules/system/class_group.php');

switch($dims_op)
{
	case 'begin_share':
		/* retreive current share */
		unset($_SESSION['share']['elemsign']);
		unset($_SESSION['share']['elements']);
		unset($_SESSION['share']);

		// vérification de droit du répertoire
		$tabgroup = explode(',',dims_viewworkspaces($_SESSION['dims']['moduleid']));

		// stop share if not authorized
		if(isset($idgroup) && !in_array($idgroup,$tabgroup))	die();

		if (isset($idrecord) && isset($idobject) && isset($idgroup))
		{
			$_SESSION['share']['idobject']=$idobject;
			$_SESSION['share']['idrecord']=$idrecord;
			$_SESSION['share']['idgroup']=$idgroup;

			$idmodule=$_SESSION['dims']['moduleid'];

			$_SESSION['share']['idmodule']=$idmodule;
			$res=$db->query("SELECT * FROM dims_share WHERE id_module= :idmodule and id_record= :idrecord and id_object= :idobject",
							array(':idmodule' => $idmodule, ':idrecord' => $idrecord, ':idobject' => $idobject) );

			if ($db->numrows($res)>0)
			{
				while ($fields=$db->fetchrow($res))
				{
					$_SESSION['share']['elements'][$fields['id']]=$fields;
					$_SESSION['share']['elemsign'][$idmodule."_".$idobject."_".$idrecord."_".$fields['type_share']."_".$fields['id_share']]=1;
				}
			}

			require_once(DIMS_APP_PATH . '/modules/system/admin_share_list.php');
		}
	break;

	case 'contentattach':
			@ob_end_clean();
			ob_start();
			dims_share_getelements();
			@ob_end_flush();
			die();
			break;
	case 'add_elem':
			if (isset($id_share) && isset($type_share))
			{
				$idmodule=$_SESSION['share']['idmodule'];
				$idrecord=$_SESSION['share']['idrecord'];
				$idobject=$_SESSION['share']['idobject'];

				$res=$db->query("SELECT id FROM dims_share WHERE id_module= :idmodule and id_object= :idobject and id_record= :idrecord and id_share= :idshare and type_share= :typeshare ",
								array(':idmodule' => $idmodule,  ':idobject' => $idobject, ':idrecord' => $idrecord, ':idshare' => $id_share, ':typeshare' => $type_share) );
				if ($db->numrows($res)==0)
				{
					$objshare=new share();
					$objshare->fields['id_module']=$idmodule;
					$objshare->fields['id_record']=$idrecord;
					$objshare->fields['id_object']=$idobject;
					$objshare->fields['type_share']=$type_share;
					$objshare->fields['id_share']=$id_share;
					$objshare->save();

					$fields['id_module']=$idmodule;
					$fields['id_record']=$idrecord;
					$fields['id_object']=$idobject;
					$fields['type_share']=$type_share;
					$fields['id_share']=$id_share;

					// add element to $_SESSION
					$_SESSION['share']['elements'][$objshare->fields['id']]=$fields;
					$_SESSION['share']['elemsign'][$idmodule."_".$idobject."_".$idrecord."_".$fields['type_share']."_".$fields['id_share']]=1;
				}
			}

			@ob_end_clean();
			ob_start();
			dims_share_getelements();
			@ob_end_flush();
			die();
		break;

	case 'del_elem':
			//dims_print_r($_GET);
			if (isset($id_share) && isset($type_share))
			{
				$idmodule=$_SESSION['share']['idmodule'];
				$idrecord=$_SESSION['share']['idrecord'];
				$idobject=$_SESSION['share']['idobject'];

				$res=$db->query("SELECT id from dims_share where id_module= :idmodule and id_object= :idobject and id_record= :idrecord and id_share= :idshare and type_share= :typeshare ",
								array(':idmodule' => $idmodule,  ':idobject' => $idobject, ':idrecord' => $idrecord, ':idshare' => $id_share, ':typeshare' => $type_share) );
				if ($db->numrows($res)>0)
				{
					if ($fields=$db->fetchrow($res))
					{
						unset($_SESSION['share']['elements'][$fields['id']]);
						unset($_SESSION['share']['elemsign'][$idmodule."_".$idobject."_".$idrecord."_".$fields['type_share']."_".$fields['id_share']]);
						$sql="DELETE from dims_share where id_module= :idmodule and id_record= :idrecord and id_object= :idobject and type_share= :typeshare and id_share= :idshare ";
						$res=$db->query($sql, array(':idmodule' => $idmodule,  ':idobject' => $idobject, ':idrecord' => $idrecord, ':idshare' => $id_share, ':typeshare' => $type_share) );
					}
				}
			}
			//die();

			@ob_end_clean();
			ob_start();
			dims_share_getelements();
			@ob_end_flush();
			die();

		break;

		case 'search_list_group':
			@ob_end_clean();
			ob_start();

			$idobject=$_SESSION['share']['idobject'];
			$idmodule=$_SESSION['share']['idmodule'];
			$idrecord=$_SESSION['share']['idrecord'];

			if (isset($idgroup))
			{
				// on s'occupe maintenant des utilisateurs rattaché directement à ce groupe de travail
				$querygrp="	SELECT	distinct u.id,u.firstname,u.lastname
							from	dims_user as u,dims_group_user as gu,dims_group as gp
							where	gu.id_user=u.id and gu.id_group=gp.id and gp.id= :idgroup ";

				if (isset($text) && $text!="")	$querygrp.=" and (u.lastname like \"%".str_replace("\"","\"\"",$text)."%\" or u.firstname like \"".str_replace("\"","\"\"",$text)."\")";

				$resuser=$db->query($querygrp, array(':idgroup' => $idgroup) );

				$icount=1;
				$maxcount=$db->numrows($resuser);

				$ico="ico_user_blue.gif";

				if ($db->numrows($resuser))
				{
					echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">";
					echo "<tr><td align=\"right\" colspan=\"2\" height=\"40\"><a href=\"#\" onclick=\"javascript:maskdiv('contentsearch$idgroup')\"><img src=\"./common/modules/system/img/ico_arrow_up.gif\" border=\"0\" alt=\"\">&nbsp;&nbsp;".$_DIMS['cste']['_LIST_HIDE']."</a></td></tr>";
					while ($fields=$db->fetchrow($resuser))
					{
						if ($color==$skin->values['bgline1']) $color=$skin->values['bgline2'];
						else $color=$skin->values['bgline1'];


						echo "<tr bgcolor=\"".$color."\" valign=middle align=\"left\"><td align=\"left\"><table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=100%><tr><td>";
						$name=$fields['firstname']." ".$fields['lastname']." (".$_DIMS['cste']['_DIMS_LABEL_USER'].")";

						echo "<img src=\"./common/modules/system/img/empty.gif\" border=\"0\" alt=\"\">";

						if ($icount>=$maxcount) echo "<img src=\"./common/modules/system/img/join.gif\" border=\"0\" alt=\"\">";
						else echo "<img src=\"./common/modules/system/img/joinbottom.gif\" border=\"0\" alt=\"\">";

						echo "<img src=\"./common/modules/system/img/$ico\" border=\"0\" alt=\"0\">";
						echo "</td><td>&nbsp;".$name."</td></tr></table></td>";

						if (isset($_SESSION['share']['elemsign'][$idmodule."_".$idobject."_".$idrecord."_user_".$fields['id']]))
						{
							echo "<td><a href=\"javascript:dims_share_del_elem('user',".$fields['id'].");\"><img src=\"./common/modules/system/img/ico_red_cross.gif\" border=\"0\"/></a>";
						}
						else
							echo "<td><a href=\"javascript:dims_share_add_elem('user',".$fields['id'].");\"><img src=\"./common/modules/system/img/ico_green_cross.gif\" border=\"0\"/></a>";

						$icount++;
					}

					echo "<tr><td align=\"right\" colspan=\"2\" height=\"40\"><a href=\"#\" onclick=\"javascript:maskdiv('contentsearch$idgroup')\"><img src=\"./common/modules/system/img/ico_arrow_up.gif\" border=\"0\" alt=\"\">&nbsp;&nbsp;".$_DIMS['cste']['_LIST_HIDE']."</a></td></tr>";
					echo "</table>";
				}
			}
			@ob_end_flush();
			die();
		break;

		case 'search_list':
			@ob_end_clean();
			ob_start();

			dims_share_searchlist($text);

			@ob_end_flush();
			die();
		break;
}
?>
