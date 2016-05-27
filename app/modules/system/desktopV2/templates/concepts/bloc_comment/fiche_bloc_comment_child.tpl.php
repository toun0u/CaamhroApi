<?
$us = $this->getLightAttribute('user');
$ct = $this->getLightAttribute('contact');
?>
<table cellspacing="10" cellpadding="0" style="width:90%;float:right">
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
            <td style="float: left; width: 80%;">
                <div class="puce_title_comment_contact">
					<span>
						<strong><? echo $us->fields['firstname']." ".$us->fields['lastname'] ;?></strong>
					</span>
				</div>
                <div class="desc_comment_contact"><? echo $this->fields['content']; ?></div>
            </td>
            <td class="bulle_discussion">
            	<a class="progressive close" title="<?php echo $_SESSION['cste']['DELETE_THIS_COMMENT']; ?>" href="javascript:void(0);" onclick="javascript:dims_confirmlink('/admin.php?action=del_comment_concepts&id=<?php echo $this->fields['id']; ?>', '<?php echo $_SESSION['cste']['ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_COMMENT']; ?>');"></a>
            </td>
        </tr>
        <tr>
            <td colspan="3">
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
