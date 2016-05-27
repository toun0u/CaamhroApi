<?php
echo $skin->open_widgetbloc($_DIMS['cste']['_DOCS'], 'width:100%', 'font-weight:bold;padding-bottom:2px;padding-left:10px;vertical-align:bottom;', './common/img/widget_doc.png','26px', '26px', '-17px', '-5px', '', '', '');
?>
    <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:10px;margin-bottom:10px;">
        <tbody>
			<tr class="trl1">
				<td width="100%" colspan="4" style="text-align:right">
				<?php
				$id_module=$ent->fields['id_module'];
				$id_object=dims_const::_SYSTEM_OBJECT_TIERS;
				$id_record=$ent->fields['id'];
				echo dims_createAddFileLink($id_module,$id_object,$id_record);
				?>
				</td>
			</tr>
			<?php
                        require_once DIMS_APP_PATH.'include/functions/files.php';
			// collecte des fichiers deja ins�r�s
			$lstfiles=dims_getFiles($dims,$id_module,$id_object,$id_record);

			if (!empty($lstfiles)) {
				echo "<tr class=\"trl1\">
					<td style=\"width:5%;padding-left:10px;\"></td>
	                <td style=\"width:38%;padding-left:10px;\">Document</td>
		            <td style=\"width:25%;\">Modifi&eacute; le</td>
			        <td style=\"width:37%;\">Par</td>
				</tr>";
				$licolor=2;
				foreach ($lstfiles as $file) {
					if ($licolor==1) $licolor=2;
					else $licolor=1;
					$ldate = dims_timestamp2local($file['timestp_modify']);

					if (dims_isadmin() || $file['id_user']==$_SESSION['dims']['userid']) {
						$hrefsup="<td><a href=\"javascript:dims_confirmlink('".dims_urlencode("$scriptenv?dims_op=doc_file_delete&docfile_id=".$file['id'])."','".$_DIMS['cste']['_DIMS_CONFIRM']."');\"><img src=\"./common/img/delete.png\" alt=\"".$_DIMS['cste']['_DELETE']."\"></a></td>";
					}
					else $hrefsup="";

					echo "<tr class=\"trl2\">".$hrefsup."
						<td style=\"cursor: default;padding-left:10px;\" onmouseout=\"javascript:this.style.cursor='default';\" onmouseover=\"javascript:this.style.cursor='pointer';\" style=\"cursor: default;\">
						<a href=".$file['downloadlink']." title=\"Voir le document.\">".$file['name']."</a></td>
						<td onmouseout=\"javascript:this.style.cursor='default';\" onmouseover=\"javascript:this.style.cursor='pointer';\" style=\"cursor: default;font-weight:normal;\">".$ldate['date']."</td>
						<td onmouseout=\"javascript:this.style.cursor='default';\" onmouseover=\"javascript:this.style.cursor='pointer';\" style=\"cursor: default;font-weight:normal;\">".$file['firstname']." ".$file['lastname']."</td></tr>";
				}
			}
			?>
    </table>
<?php echo $skin->close_widgetbloc(); ?>
