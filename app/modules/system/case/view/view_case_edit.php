<?php

/**
 * Description of view_case_edit
 *
 * @author Thomas Metois
 * @copyright Wave Software / Netlor 2011
 */
class view_case_edit {

    public static function buildViewCase($id_case = 0) {
		$case = new dims_case();
		$case->init_description();
		if ($id_case > 0)
			$case->open($id_case);

		?>
		<input type="hidden" name="id_case" value="<? echo $id_case; ?>">
		<table cellpadding="2" cellspacing="0" style="margin-left:10px;">
			<tr>
				<td colspan="2">
					Label :
				</td>
				<td>
					<input type="text" style="width:300px;" id="case_label" name="case_label" value="<? echo $case->getLabel(); ?>">
				</td>
			</tr>
		</table>
		<script type="text/javascript">
			$("#case_label").focus();
		</script>
		<?
    }

}

?>
