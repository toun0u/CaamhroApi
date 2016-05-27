<div class="title_description">
	<h2><? echo $_SESSION['cste']['_DIMS_LABEL_STEP']; ?> 5 - <? echo $_SESSION['cste']['_DIMS_LABEL_TAGS']; ?></h2>
</div>
<div class="tags_of_opportunity">
	<table cellspacing="10" cellpadding="0" style="width:80%">
		<tbody>
			<tr>
				<td class="text">
					<? echo $_SESSION['cste']['_DIMS_LABEL_TAGS']; ?>
				</td>
				<td style="width: 500px;">
					<?
					global $desktop;
					$lstTag = $desktop->getGenericTags();
					?>
					<select multiple="" style="width: 500px;" name="fck_tags[]" class="opportunity_tags">
						<option value=""></option>
						<?
						foreach($lstTag as $tag){
							?>
							<option value="<? echo $tag->fields['id']; ?>"><? echo $tag->fields['tag']; ?></option>
							<?
						}
						?>
					</select>
				</td>
				<td>
					<?php /*include _DESKTOP_TPL_LOCAL_PATH.'/tag/tag.tpl.php';*/ ?>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<div class="enter_title"><i><? echo $_SESSION['cste']['_DIMS_START_TYPE_TAG_NAME']; ?></i></div>
				</td>
			</tr>
		</tbody>
	</table>
</div>
<script type="text/javascript">
	$(document).ready(function(){
		$("select.opportunity_tags").chosen({no_results_text: "<div onclick=\"javascript:addNewTag('tags_of_opportunity');\" style=\"float:right;color:#E21C2C;cursor:pointer;\"><img style=\"float:left;\" src=\"<? echo _DESKTOP_TPL_PATH; ?>/gfx/common/add.png\" /><div style=\"float:right;margin-top:3px;\">Add it !</div></div>No results matched"});
	});
</script>
