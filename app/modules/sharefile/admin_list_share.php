<?
$mod = dims_load_securvalue("dims_moduleid",dims_const::_DIMS_NUM_INPUT,true,true);

// valeur par défaut du filtre
if (!isset($_SESSION['sharefile']['filtre'])) $_SESSION['sharefile']['filtre']='all';

// filtre passé en paramètre
$filtre = dims_load_securvalue("filtre",dims_const::_DIMS_CHAR_INPUT,true,true);
if ($filtre!="") {
	$_SESSION['sharefile']['filtre']=$filtre;
}

// affectation du filtre
$filtre=$_SESSION['sharefile']['filtre'];

$id_share = dims_load_securvalue("id_share",dims_const::_DIMS_NUM_INPUT,true,true);
if ($id_share>0) {
	if ($_SESSION['sharefile']['current_share']!=$id_share) unset($_SESSION['sharefile']['current_reponse']);
	$_SESSION['sharefile']['current_share']=$id_share;
}

$_SESSION['admin_share'] = array();

$sqlq="SELECT 		distinct s.*,u.lastname, u.firstname,
					count(distinct h.id ) AS cpte
		FROM 		dims_mod_sharefile_share as s
		LEFT JOIN 	dims_user u ON u.id = s.id_user
		left join 	dims_mod_sharefile_history as h on h.id_share=s.id
		and			h.action=1
		where 		s.id_workspace IN ($workspaces)
		";

$sqlq.="
		group by 	s.id";
$tab_shares=array();
$res_q = $db->query($sqlq);
while($value = $db->fetchrow($res_q)) {
	if (($filtre=="wait" && $value['cpte']==0) || $filtre!="wait" )
    	$tab_shares[$value['id_share']] = $value;
}

?>
<table width="100%" cellpadding="4" cellspacing="0" border="0">
	<tr>
		<td colspan="3" align="right" style="height:40px">
		<?php
			echo dims_create_button($_DIMS['cste']['_DIMS_ADD'],"./common/img/add.gif","javascript:document.location.href='".$scriptenv."?op=new_share'","enreg1",'');
		?>
		</td>
	</tr>
	    <tr>
        <td width="50%"><b>Partage</b></td>
		<td width="30%"><b>Par</b></td>
        <td width="20%" align="center"><b>Nb<br> t&eacute;l&eacute;charg.</b>
        </td>
    </tr>
    <?
    $dims_favorites=$dims_user->getFavorites($_SESSION['dims']['moduleid']);
    $c=0;
    foreach($tab_shares as $id_share => $tab_share) {

    	if (!isset($_SESSION['sharefile']['current_share'])) $_SESSION['sharefile']['current_share']=$id_share;

    	dims_createOptions($tab_share['id_workspace'],$tab_share['id_module'],_SHAREFILE_OBJECT_SHARE,$tab_share['id'],dims_strcut($tab_share['label'],150),$tab_share['id_user']);

        $nb_rep = 0;
        $nb_rep = $tab_share['cpte'];

        if ($_SESSION['sharefile']['current_share']==$id_share) {
        	$debbold="<b>";
        	$endbold="</b>";
        }
        else {
        	$debbold="";
        	$endbold="";
        }

        $c++;
        if ($c%2==0) $class="class=\"trl1\"";
        else $class="class=\"trl2\"";

        $var=dims_timestamp2local($tab_share['date_create']);
		$datecreate=$var['date'];
		$timecreate=$var['time'];

        echo "  <tr $class >
                    <td  colspan=\"3\">";
        if ($tab_share['firstname']!="" && $tab_share['lastname']!="") {
        	echo "{$tab_share['firstname']} {$tab_share['lastname']}";
        }
        elseif ($tab_share['emailq']!="") {
        	echo $tab_share['emailq'];
        }
        else echo "";

        echo " le $datecreate &agrave; ".$timecreate."</td></tr><tr $class>
                    <td>
                        <a href=\"$scriptenv?op=view_share&id_share=".$id_share."\">".$debbold.dims_strcut($tab_share['label'],80).$endbold."</a>
                    </td>
                    <td align=\"center\">$nb_rep
                    </td>
                    ";
			/*
				if (dims_isadmin() || dims_isactionallowed(0) || dims_isactionallowed(_FAQ_ACTION_MANAGE)) {
				$href=dims_urlencode("{$scriptenv}?op=modify_question&id_share={$tab_share['id']}");
				dims_addOptions($tab_share['id_workspace'],$tab_share['id_module'],_SHAREFILE_OBJECT_SHARE,$id_share,$href,"","","modify","");
			}

			if (dims_isactionallowed(_FAQ_ACTION_MANAGE) ) {
				$href="javascript:dims_confirmlink('".dims_urlencode("$scriptenv?op=delete_question&id_share=".$id_share)."','"._DIMS_CONFIRM."');";

				dims_addOptions($tab_share['id_workspace'],$tab_share['id_module'],_SHAREFILE_OBJECT_SHARE,$id_share,$href,"","","delete","");
			}

			echo dims_displayOptions($tab_share['id_workspace'],$tab_share['id_module'],_SHAREFILE_OBJECT_SHARE,$id_share);
			*/
        echo "
                </tr>";

    }
    ?>

</table>
