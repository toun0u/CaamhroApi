<?php
$date_create = dims_timestamp2local($this->fields['timestp_create']);

$user = new user();
if ($this->fields['id_user'] > 0 && $user->open($this->fields['id_user'])) {
    $user_name = $user->fields['firstname'][0].'. '.$user->fields['lastname'];
}
else {
    $user_name = 'Unknown';
}

// recherche de l'event / activité sur lequel a été déposé le document
$doc_ref = $this->getLightAttribute('ref');
?>

<table cellspacing="10" cellpadding="0">
    <tbody>
        <tr>
            <td style="width:40px">
				<?php
				$type = $this->getLightAttribute('type');
				switch($type){
					default:
					case search::RESULT_TYPE_DOCUMENT:
						?>
						<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/doc40.png">
						<?php
						break;
					case search::RESULT_TYPE_PICTURE:
						if(file_exists($this->getPicturePath(40))){
							?>
							<img src="<?php echo $this->getPictureWebPath(40);?>" />
							<?php
						}
						else
						{
							?>
							<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/doc40picture.png">
							<?php
						}
						break;
					case search::RESULT_TYPE_MOVIE:
						?>
						<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/doc40movie.png">
						<?php
						break;
				}
				?>
            </td>
            <td style="width: 100%;">
                <span class="title_fiche_bloc_document"><a href="admin.php?dims_op=doc_file_download&docfile_md5id=<?php echo $this->fields['md5id']; ?>"><?php echo $this->fields['name']; ?></a></span>
                <span class="text_fiche_bloc_document">Added by <?php echo $user_name; ?> the <?php echo $date_create['date']; ?></span>

                <?php
                if ($doc_ref > 0) {
                    $action = new action();
                    $action->openWithGB($doc_ref);

                    switch($action->fields['type']) {
                        default:
                        case dims_const::_PLANNING_ACTION_EVT: //fairs and missions
                            echo '
                                <img style="float:left;" src="'._DESKTOP_TPL_PATH.'/gfx/common/event_mini.png">
                                <span class="text_fiche_add_document">This document has been added to the '.$_SESSION['cste']['_DIMS_LABEL_EVENT'].' <a href="javascript:void(0);" onclick="javascript:document.location.href=\'/admin.php?action=swap_filter&filter_type=event&filter_value='.$action->fields['id_globalobject'].'\'" title="Go to the '.$_SESSION['cste']['_DIMS_LABEL_EVENT'].'">'.$action->fields['libelle'].'</a></span>';
                            break;
                        case dims_const::_PLANNING_ACTION_ACTIVITY ://activities
                            echo '
                                <img style="float:left;" src="'._DESKTOP_TPL_PATH.'/gfx/common/activity_grey_picto.png">
                                <span class="text_fiche_add_document">This document has been added to the '.$_SESSION['cste']['ACTIVITY'].' <a href="javascript:void(0);" onclick="javascript:document.location.href=\'/admin.php?action=swap_filter&filter_type=activity&filter_value='.$action->fields['id_globalobject'].'\'" title="Go to the '.$_SESSION['cste']['ACTIVITY'].'">'.$action->fields['libelle'].'</a></span>';
                            break;
                    }
                }
                ?>
            </td>
            <td class="filter" style="float:none;vertical-align:middle;">
                <img class="perform_cube" onclick="javascript:document.location.href='/admin.php?action=add_filter&filter_type=doc&filter_value=<?php echo $this->fields['id_globalobject']; ?>';" src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/home_cube.png" style="float:left;cursor:pointer;" />
                <a class="progressive download" href="<?php echo dims_urlencode(dims::getInstance()->getScriptEnv().'?dims_op=doc_file_download&docfile_md5id='.$this->fields['md5id']); ?>" title="<?php echo $_SESSION['cste']['_DIMS_DOWNLOAD']; ?>">
                <a class="progressive previsu" href="javascript:void(0);" onclick="javascript:preview_docfile('<?php echo $this->fields['md5id']; ?>');" title="<?php echo $_SESSION['cste']['_PREVIEW']; ?>">
            </td>
            <?php
            if (true) { //$this->getLightAttribute('concept_not_event')) {
                ?>
                <td class="filter" style="float:none;vertical-align:middle;">
                    <a title="<?php echo $_SESSION['cste']['DETACH_THIS_DOCUMENT']; ?>" href="javascript:void(0);" onclick="javascript:dims_confirmlink('/admin.php?action=del_concepts_link&link_type=<?php echo dims_const::_SYSTEM_OBJECT_DOCFILE; ?>&id=<?php echo $this->fields['id_globalobject']; ?>', '<?php echo $_SESSION['cste']['ARE_YOU_SURE_YOU_WANT_TO_DETACH_THIS_DOCUMENT']; ?>');">
                        <img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/detach.png" />
                    </a>
                </td>
                <?php
            }
            ?>
        </tr>
    </tbody>
</table>
