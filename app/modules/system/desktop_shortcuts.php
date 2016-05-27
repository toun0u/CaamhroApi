<div style="position:relative;display:block;clear:both;">
	<div class="ui-widget-header">
		<span class="ui-icon ui-icon-arrowreturnthick-1-n" style="float:left;"></span>
		<? echo $_DIMS['cste']['_DIMS_LABEL_SHORCUTS']; ?>
	</div>
<div style="clear:both;">
<?
// on passe a l'administration
$currentworkspace=$dims->getWorkspaces($_SESSION['dims']['workspaceid']);

if ($currentworkspace['activecontact']) {
	echo '<div style="float:left;width:24%;text-align:center;margin:2px auto;">
		<img src="./common/img/contact_add.png">
                <br><input type="button" onclick="document.location.href=\'/admin.php?dims_mainmenu=9&cat=0&dims_desktop=block&dims_action=public&action='._BUSINESS_TAB_CONTACTSTIERS.'&part='._BUSINESS_TAB_CONTACTSTIERS.'&case=1&id_from='.$_SESSION['dims']['user']['id_contact'].'&type_from=cte\'" class="ui-button ui-state-default ui-corner-all activebutton" aria-disabled="false" value="'.$_DIMS['cste']['_IMPORT_TAB_NEW_CONTACT'].'"/>
	</div>';

	echo '<div style="float:left;width:24%;text-align:center;margin:2px auto;">
                    <img src="./common/img/tiers_add.png">
                <br><input type="button" onclick="document.location.href=\'/admin.php?dims_mainmenu=9&cat=0&action=43&part=43&case=2&id_from='.$_SESSION['dims']['user']['id_contact'].'&type_from=ent\'" class="ui-button ui-state-default ui-corner-all activebutton" aria-disabled="false" value="'.$_DIMS['cste']['_IMPORT_TAB_NEW_COMPANY'].'"/>
		</div>';

}
// test si on a une instance de module doc
$isadmindoc=$dims->isModuleTypeEnabled('doc');
if ($isadmindoc) {
	foreach($dims->getModuleByType('doc') as $i =>$mod) {
            if ($mod['active'] && $mod['visible']) {
			echo '<div style="float:left;width:24%;text-align:center;margin:2px auto;">
						<img src="./common/img/doc_add.png">
					<br><input type="button" onclick="\''.dims_createAddFileLink($mod['instanceid'],0,0,'',true).'\'" class="ui-button ui-state-default ui-corner-all activebutton" aria-disabled="false" value="'.dims_strcut($mod['instancename'],30).'"/>
			</div>';
		}
	}
}

$isadminevent=$dims->isModuleTypeEnabled('events') && ($currentworkspace['activeevent'] || $currentworkspace['activeeventstep']);

//$isadminevent=$dims->isModuleTypeEnabled('events') && $currentworkspace['activeevent'];
// admin des events
if ($isadminevent) {
	foreach($dims->getModuleByType('events') as $i =>$mod) {
             echo '<div style="float:left;width:24%;text-align:center;margin:2px auto;">
                        <img src="./common/img/event_add.png">
                        <br><input type="button" onclick="document.location.href=\'/admin.php?dims_moduleid='.$mod['instanceid'].'&dims_desktop=block&admin.php?dims_mainmenu=events&dims_action=public&action=add_evt&ssubmenu=11&type=2&id=0\'" class="ui-button ui-state-default ui-corner-all activebutton" aria-disabled="false" value="'.$_DIMS['cste']['_DIMS_LABEL_NEW_EVENT'].'"/>
                </div>';
	}
}

if ($currentworkspace['activecontact']) {
     echo '<div style="float:left;width:24%;clear:both;text-align:center;margin:2px auto;">
                    <img src="'.$_SESSION['dims']['template_path'].'/media/contact32.png">
                <br><input type="button" onclick="document.location.href=\'/admin.php?dims_mainmenu=9&cat=0&dims_desktop=block&dims_action=public&init=1&dims_moduleid=1\'" class="ui-button ui-state-default ui-corner-all activebutton" aria-disabled="false" value="'.$_DIMS['cste']['_DIMS_LABEL_CONTACT'].'"/>
            </div>';

	 echo '<div style="float:left;width:24%;text-align:center;margin:2px auto;">
                    <img src="'.$_SESSION['dims']['template_path'].'/media/tags32.png">
                <br><input type="button" onclick="document.location.href=\'/admin.php?dims_mainmenu=9&cat=0&action='._BUSINESS_TAB_CONTACT_GROUP.'&part='._BUSINESS_TAB_CONTACT_GROUP.'&dims_desktop=block&dims_action=public&init=1&dims_moduleid=1\'" class="ui-button ui-state-default ui-corner-all activebutton" aria-disabled="false" value="'.$_DIMS['cste']['_DIMS_LABEL_TAGS'].'"/>
            </div>';
}

$isadminvcard=$dims->isModuleTypeEnabled('importVcard');
if ($isadmindoc) {
	foreach($dims->getModuleByType('importVcard') as $i =>$mod) {
             echo '<div style="float:left;width:24%;text-align:center;margin:2px auto;">
                            <img src="./common/img/email.png">
                    <br><input type="button" onclick="document.location.href=\'/admin.php?dims_moduleid='.$mod['instanceid'].'&dims_desktop=block&dims_action=public\'" class="ui-button ui-state-default ui-corner-all activebutton" aria-disabled="false" value="'.$_DIMS['cste']['_DIMS_LABEL_EMAIL'].'"/>
            </div>';

          echo '<div style="float:left;width:24%;text-align:center;margin:2px auto;">
			<img src="./common/img/vcard.png">
                <br><input type="button" onclick="document.location.href=\'/admin.php?dims_moduleid='.$mod['instanceid'].'&dims_desktop=block&dims_action=public\'" class="ui-button ui-state-default ui-corner-all activebutton" aria-disabled="false" value="'.$_DIMS['cste']['_DIMS_LABEL_VCARD'].'"/>
	</div>';

    }
}

$isadminwce=$dims->isModuleTypeEnabled('wce');
$partialwce=false;
foreach($dims->getModuleByType('wce') as $i =>$mod) {
    $partialwce=$partialwce || dims_isactionallowed(-1,$_SESSION['dims']['workspaceid'],$mod['instanceid']);
}

$isadminwce=$isadminwce && (dims_isadmin() || $partialwce);
$idadminnewsletter = $currentworkspace['activenewsletter'] &&  dims_isadmin();

// on va faire le lien sur le module id du type
if (file_exists("./common/modules/events/img/mod32.png")) $imgevent="./common/modules/events/img/mod32.png";
else $imgevent='';
if (file_exists("./common/modules/wce/img/mod32.png")) $imgwce="./common/modules/wce/img/mod32.png";
else $imgwce='';

if ($isadminevent) {
    // accès en liste des events
    foreach($dims->getModuleByType('events') as $i =>$mod) {
          echo '<div style="float:left;width:24%;text-align:center;margin:2px auto;">
                    <img src="'.$imgevent.'">
                <br><input type="button" onclick="document.location.href=\'/admin.php?dims_moduleid='.$mod['instanceid'].'&dims_desktop=block\'" class="ui-button ui-state-default ui-corner-all activebutton" aria-disabled="false" value="'.ucfirst($mod['label']).'"/>
	</div>';
    }
}
echo '</div>';
// check if user have admin rules
if ($isadminevent || $isadminwce || $idadminnewsletter) {
?>
    <div style="position:relative;display:block;clear:both;">
	<div class="ui-widget-header">
		<span class="ui-icon ui-icon-arrowreturnthick-1-n" style="float:left;"></span>
		 <? echo $_DIMS['cste']['_DIMS_LABEL_SHORCUTS']." ".$_DIMS['cste']['_DIMS_LABEL_ADMIN']; ?>
	</div>
        <div style="clear:both;">
    <?
    // admin des events
    if ($isadminevent) {
        foreach($dims->getModuleByType('events') as $i =>$mod) {
            if ($mod['visible']) {
                echo '<div style="float:left;width:24%;text-align:center;margin:2px auto;">
                        <img src="'.$imgevent.'">
                    <br><input type="button" onclick="document.location.href=\'/admin.php?dims_moduleid='.$mod['instanceid'].'&dims_desktop=block\'" class="ui-button ui-state-default ui-corner-all activebutton" aria-disabled="false" value="'.ucfirst($mod['instancename']).'"/>
            </div>';
            }
        }
    }

    if ($isadminwce) {
        foreach($dims->getModuleByType('wce') as $i =>$mod) {

            if ((dims_isadmin() || dims_isactionallowed(-1,$_SESSION['dims']['workspaceid'],$mod['instanceid'])) && $mod['visible']) {

            echo '<div style="float:left;width:24%;text-align:center;margin:2px auto;">
                        <img src="'.$imgwce.'">
                    <br><input type="button" onclick="document.location.href=\'/admin.php?dims_moduleid='.$mod['instanceid'].'&dims_desktop=block&dims_action=admin\'" class="ui-button ui-state-default ui-corner-all activebutton" aria-disabled="false" value="'.ucfirst($mod['instancename']).'"/>
            </div>';
            }
        }
    }

    if ($idadminnewsletter) {
         echo '<div style="float:left;width:24%;text-align:center;margin:2px auto;">
                        <img src="'.$_SESSION['dims']['template_path'].'/media/news32.png">
                    <br><input type="button" onclick="document.location.href=\''.dims_urlencode("/admin.php?dims_mainmenu=11&cat=0&dims_desktop=block&dims_action=public&init=1&dims_moduleid=1").'\'" class="ui-button ui-state-default ui-corner-all activebutton" aria-disabled="false" value="'.ucfirst($_DIMS['cste']['_DIMS_LABEL_NEWSLETTER']).'"/>
            </div>';

    }
}
echo '</div></div>';
if($isadminevent=$dims->isModuleTypeEnabled('elearning')) {

	?>
	<div style="position:relative;display:block;clear:both;">
		<div class="ui-widget-header">
			<span class="ui-icon ui-icon-arrowreturnthick-1-n" style="float:left;"></span>
			<? echo $_DIMS['cste']['_DIMS_LABEL_ELEARNING']; ?>
		</div>
	<?php
	  echo '<div style="float:left;width:24%;text-align:center;margin:2px auto;clear:both;">
                        <img src="'.$_SESSION['dims']['template_path'].'/media/videos32.png">
                    <br><input type="button" onclick="document.location.href=\''.dims_urlencode("/admin.php?dims_mainmenu=elearning").'\'" class="ui-button ui-state-default ui-corner-all activebutton" aria-disabled="false" value="'.ucfirst($_DIMS['cste']['_DIMS_LABEL_ELEARNING']).'"/>
            </div>';
          echo "</div>";
}

 // on doit calculer pour le nombre de nouveauté
if (isset($_SESSION['dims']['sub_activities'][1]) && !empty($_SESSION['dims']['sub_activities'][1]) && false) {
    echo '<div style="float:left;width:50%;clear:both">';


    echo '</div><div style="float:left;width:50%;clear:both">';
    $tot=sizeof($_SESSION['dims']['sub_activities'][1]);

    foreach($_SESSION['dims']['sub_activities'][1] as $i=>$act) {
         if ($i==$desktop_sublink) $sel=true;
        else $sel=false;

        if ($act['cpte']==0) {
            $stylelink='class="fontgray";';
        }
        else {
             $stylelink='';
        }

        // check selection
        if ($sel) echo $act['cpte'].' '.$act['title'];
        else {
            echo '<a href="'.$act['link'].'&desktop_filter_type=1"><font '.$stylelink.'>'.$act['cpte'].' '.$act['title'].'</font></a>&nbsp;';
        }

        if ($i<$tot) {
            echo "- ";
        }
    }
    echo '</div>';
}
echo '</div>';
?>
