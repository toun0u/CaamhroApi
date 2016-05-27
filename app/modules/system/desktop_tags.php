<div style="position:relative;display:block;">
	<div class="ui-widget-header">
		<span class="title action" style="float:right;">
			<?
			$arrayChoice = array();
			$arrayChoice[0]=$_DIMS['cste']['_DIMS_LABEL_PERSO'];
			$arrayChoice[1]=$_DIMS['cste']['_WORKSPACE'];
			$arrayChoice[2]=$_DIMS['cste']['_DIMS_ALLS'];

			$taille=sizeof($arrayChoice);
			foreach($arrayChoice as $i=>$val) {
				if ($i==$desktop_tag_type) $sel="ui-state-active ui-state-hover";
				else $sel="";
				if($i==0) $corner="ui-corner-left";
				else if($i==($taille-1)) $corner="ui-corner-right";
				else $corner = "";

				echo '<a class="ui-button ui-widget ui-state-default ui-button-text-only '.$corner.' '.$sel.'" href="/admin.php?desktop_tag_type='.$i.'">
						<span class="ui-button-text">'.$val.'</span>
					</a>';
			}
			?>
		</span>
		<span class="ui-icon ui-icon-tag" style="float:left;"></span>Tags
		<div>
		   <?
		   echo "<img src=\"./common/img/arrow_ltr.png\"><font style=\"font-style:normal;\">".$_DIMS['cste']['_DIMS_LABEL_VIEWMODE']." : </font>";
		   $arrayChoice = array();
		   $arrayChoice[0]=$_DIMS['cste']['_DIMS_LABEL_BY_USAGE'];
		   $arrayChoice[1]=$_DIMS['cste']['_DIMS_LABEL_BY_CATEGORY'];

		   $taille=sizeof($arrayChoice);
		   $i=0;
		   foreach($arrayChoice as $i=>$val) {
			   if ($i==$desktop_filter_tag_categ) $sel=true;
			   else $sel=false;

			   if ($sel) echo $val;
			   else {
				   echo '<a href="/admin.php?desktop_filter_tag_categ='.$i.'">'.$val.'</a>';
			   }

			   if ($i<$taille-1) {
				   echo "&nbsp;-&nbsp;";
			   }
		   }
		   ?>
	       </div>
	</div>
<?

$sql_sup="";

/*
switch ($action) {
	case "contact_modify":
		$sql_sup="	INNER JOIN		dims_mod_business_contact u
					ON				u.id = ti.id_record
					AND				u.timestp_modify >= ".$date_since2."000000
					AND				u.inactif != 1";
		$idobject=dims_const::_SYSTEM_OBJECT_CONTACT;
		break;
	case "contact_new":
		$sql_sup="	INNER JOIN		dims_mod_business_contact u
				ON				u.id = ti.id_record
				AND				u.date_create >= ".$date_since2."000000
				AND				u.inactif != 1";
		$idobject=dims_const::_SYSTEM_OBJECT_CONTACT;
		break;
	case "ent_modify":
		$sql_sup="	INNER JOIN		dims_mod_business_tiers u
					ON				u.id = ti.id_record
					AND				u.timestp_modify >= ".$date_since2."000000
					AND				u.inactif != 1";
		$idobject=dims_const::_SYSTEM_OBJECT_TIERS;
		break;
	case "ent_new":
		$sql_sup="	INNER JOIN		dims_mod_business_tiers u
				ON				u.id = ti.id_record
				AND				u.date_create >= ".$date_since2."000000
				AND				u.inactif != 1";
		$idobject=dims_const::_SYSTEM_OBJECT_TIERS;
		break;
}*/
$params = array();
$sql= "SELECT count(ti.id) as cpte,t.id,t.type,t.tag,ti.id_workspace FROM `dims_tag` as t
		inner join dims_tag_index as ti on ti.id_tag=t.id";

$tabtags="";

if ($sql_sup) {
	$sql.=" and ti.id_object= :idobject and id_module=1".$sql_sup;
	$params[':idobject'] = $idobject;
}

switch($desktop_tag_type) {
	case 0: // personal
		$sql.="	where ti.id_user= :userid or t.type>1";
		$params[':userid'] = $_SESSION['dims']['userid'];
		break;
	case 2 : // all
		$sql.="	where t.private = 0 and ti.id_workspace in (".$db->getParamsFromArray(explode(',', $dims->getListWorkspaces()), 'idworkspace', $params).") or t.type>1";
		break;
	default :
		$sql.="	where t.private = 0 and ti.id_workspace= :workspaceid or t.type>1";
		$params[':workspaceid'] = $_SESSION['dims']['workspaceid'];
		break;
}

$sql.= " group by tag";

if ($desktop_filter_tag_categ==1) {
    $sql.=" order by t.type,ti.id_workspace,t.tag desc";
}
else {
    $sql.=" order by ti.id_workspace,cpte desc";
}

//echo $sql;
$res=$db->query($sql, $params);
$listags='';
$st='';
$old_workspace=0;

$tot=$db->numrows($res);
if ($tot>0) {
	$total=0;
	$nb=0;
	while ($f=$db->fetchrow($res)) {
		$total+=$f['cpte'];
		$nb++;
	}
	$moyenne=$total/$nb;

        $type=-1;
	$res=$db->query($sql, $params);
	echo '<div style="display:block;display: block; margin: 5px 0px 20px 5px;width:98%">';
	$i=0;
        $arraytypetag=array();
        $arraytypetag[0]['label']=$_DIMS['cste']['_DIMS_LABEL_LFB_GEN']; // generique
        $arraytypetag[1]['label']=$_DIMS['cste']['_DIMS_LABEL_CONTACT_GOUPS']; // groupe de contacts
        $arraytypetag[2]['label']=$_DIMS['cste']['_DIMS_LABEL_ENT_SECTACT'];
        $arraytypetag[3]['label']=$_DIMS['cste']['_DIMS_LABEL_COUNTRY'];
        $arraytypetag[4]['label']=$_DIMS['cste']['_DIMS_LABEL_YEAR'];
        $tot=$db->numrows($res);
        $i=1;
	while ($f=$db->fetchrow($res)) {
                if ($desktop_filter_tag_categ==1) {
                    if ($f['type']!=$type) {
                        if ($type>0) {
                            echo "</span>";
                        }
                        // on ecrite
                        $type=$f['type'];
                        $old_workspace=0;
                        echo '<span style="width:100%;clear:both;margin-top:10px;margin-bottom:2px;font-weight:bold;color:#8A8A8A;">';
                        if (isset($arraytypetag[$type])) {
                            echo "<img src='./common/img/bullet.png'>".$arraytypetag[$type]['label'];
                        }
                        echo '</span>';
                        echo '<span style="width:100%;clear:both;">';
                    }
                }

                // on ecrit le workspace courant
		if ($old_workspace!=$f['id_workspace'] && $desktop_tag_type==2) {

			$w=$dims->getWorkspaces($f['id_workspace']);
			if ($old_workspace>0) echo "<br/><br/>";
			echo $w['label']."<br> ";
			$old_workspace=$f['id_workspace'];
		}

		$cour=($f['cpte']*100)/$total;
		// calcul de taille
		$size=10;
		if ($f['cpte']>$moyenne) {

			if ($cour>40) {
				$size=13;
			}
			elseif ($cour>30) {
				$size=12;
			}

		}
		else {
			if ($f['cpte']<($moyenne/2)) $size=10;
			else $size=11;
		}

		// remplacement de la langue
		if (isset($_DIMS['cste'][$f['tag']])) {
			$f['tag'] = $_DIMS['cste'][$f['tag']];
		}

                if (isset($_SESSION['dims']['desktop_tagused'][$f['id']])) {
                    $f['tag'].= " (".$_SESSION['dims']['desktop_tagused'][$f['id']].")";
                    $stylelink="";
                }
                else {
                    $stylelink=' class="fontgray"';
                }

		if (isset($desktop_tag_filter) && $desktop_tag_filter==$f['id']) {
                    $stylelink="";
                    echo "<a href='".$dims->getUrlPath()."?tagfilter=".urlencode($f['id'])."' ".$stylelink." style='    font-weight:bold;font-size:".$size."px;margin:2px;'>".ucfirst(strtolower($f['tag']))."</a>";
		}
		else {
			echo "<a href='".$dims->getUrlPath()."?tagfilter=".urlencode($f['id'])."' ".$stylelink." style='font-size:".$size."px;margin:2px;'>".ucfirst(strtolower($f['tag']))."</a>";
		}
		if ($i<$tot) {
                    echo '&nbsp;-&nbsp;';
                }
                $i++;
	}
        if ($type) {
            echo "</span>";
        }
	echo '</div>';
}
echo '</div>';
?>


