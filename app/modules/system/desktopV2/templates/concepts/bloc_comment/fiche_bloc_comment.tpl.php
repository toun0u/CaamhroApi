<?
$us = $this->getLightAttribute('user');
$ct = $this->getLightAttribute('contact');
?>
<table cellspacing="10" cellpadding="0" style="width:100%">
    <tbody>
        <tr>
            <td class="picture_comment_contact">
				<?
				if ($ct->getPhotoWebPath(40) != '' && file_exists($ct->getPhotoPath(40)))
					echo '<img src="'.$ct->getPhotoWebPath(40).'" border="0" style="float:left;" />';
				else
					echo '<img src="'._DESKTOP_TPL_PATH.'/gfx/common/human40.png" border="0" style="float:left;" />';
				?>
            </td>
            <td style="float: left; width: 68%;">
                <div class="puce_title_comment_contact">
					<span style="font-weight:bold">
						<? echo $us->fields['firstname']." ".$us->fields['lastname'] ;?>
					</span>
				</div>
                <div class="desc_comment_contact">
					<? echo nl2br($this->fields['content']); ?>
				</div>
            </td>
            <td class="bulle_discussion">
                <img onclick="javascript:addCommentConcepts(event,<? echo $this->fields['id']; ?>);" src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/img_bulle.png" style="float:left;cursor:pointer;" />
            </td>
            <td class="bulle_discussion">
            	<a class="progressive close" title="<?php echo $_SESSION['cste']['DELETE_THIS_COMMENT']; ?>" href="javascript:void(0);" onclick="javascript:dims_confirmlink('/admin.php?action=del_comment_concepts&id=<?php echo $this->fields['id']; ?>', '<?php echo $_SESSION['cste']['ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_COMMENT']; ?>');"></a>
            </td>
        </tr>
        <tr>
            <td colspan="4">
                <div class="desc_comment_date">
					<?
					$dd = dims_timestamp2local($this->fields['date_annotation']);
					?>
					the <? echo $dd['date']; ?> at <? echo substr($dd['time'],0,-3); ?>
				</div>
            </td>
        </tr>
    </tbody>
</table>
<div class="cadre_fiche_bloc" style="width: 100%; border-bottom: 1px solid #D6D6D6; float: left; margin-bottom: 15px;">
<?
$lstChildrens = $this->getChildrens();
foreach($lstChildrens as $children)
	$children->display(_DESKTOP_TPL_LOCAL_PATH.'/concepts/bloc_comment/fiche_bloc_comment_child.tpl.php');
?>
</div>
