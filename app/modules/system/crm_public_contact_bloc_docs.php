<?php
	echo $skin->open_widgetbloc($_DIMS['cste']['_DOCS'], 'width:100%', 'font-weight:bold;padding-bottom:2px;padding-left:10px;vertical-align:bottom;', './common/img/widget_doc.png','26px', '26px', '-17px', '-5px', '', '', '');
?>
	<table width="100%" cellpadding="0" cellspacing="0" style="margin-top:2px;margin-bottom:2px;">
		<tr class="trl1">
			<td width="100%" style="text-align:right">
			<?php
			$id_module=$contact->fields['id_module'];
			$id_object=dims_const::_SYSTEM_OBJECT_CONTACT;
			$id_record=$contact->fields['id'];
			require_once DIMS_APP_PATH."include/functions/files.php";
			echo dims_createAddFileLink($id_module,$id_object,$id_record);
			?>
			</td>
		</tr>
		<tr>
			<td>
			<?php
			// collecte des fichiers deja ins�r�s
			require_once(DIMS_APP_PATH . '/modules/doc/class_docfolder.php');
			require_once(DIMS_APP_PATH . '/include/class_dims_globalobject.php');
			$gb_contact = new dims_globalobject();
			$gb_contact->open($contact->fields['id_globalobject']);
			$lst_file = $gb_contact->searchLink(dims_const::_SYSTEM_OBJECT_DOCFILE);
			$lst_arbo = array();
			$liste_files = array();

			function create_treeArray($val){
				$res = array();
				if (strlen($val) > 0){
					$arbo = explode(';',$val);
					if (count($arbo) > 0)
						$res['fold_'.$arbo[0]] = create_treeArray(implode(';',array_slice($arbo,1)));
					else
						$res['fold_'.$val] = create_treeArray(implode(';',''));
				}
				return $res;
			}

			function place_docs_tree($arbo, $files){
				$res = $arbo;
				foreach ($files as $id_file){
					$doc_file = new docfile();
					$doc_file->open($id_file);
					$res = place_doc_tree($res,$doc_file->fields['id_folder'],$doc_file->fields['id']);
				}
				return $res;
			}

			function place_doc_tree($arbo, $id, $id_file){
				$res = $arbo;
				foreach ($arbo as $id_fold => $val)
					if (strpos($id_fold,'fold_') !== false && substr($id_fold,5) == $id)
						$res[$id_fold]['docfile'][$id_file] = $id_file;
					elseif (strpos($id_fold,'fold_') !== false && count($val) > 0)
						$res[$id_fold] = place_doc_tree($val,$id,$id_file);
				return $res;
			}

			function echo_docs_tree($liste,$sep,$nb_sep=1){
				$res = '';
				if (isset($liste['docfile'])){
					foreach ($liste['docfile'] as $id_file){
						$res .= '<tr><td style="cursor:pointer;" onclick="javascript:document.location.href=\''.dims_urlencode("admin-light.php?dims_op=doc_file_download&docfile_id=".$id_file).'\';">';
						$file = new docfile();
						$file->open($id_file);
						if(strlen($file->fields['name']) > 10)
							$res .= str_repeat($sep,$nb_sep).'&nbsp;'.substr($file->fields['name'], 0, 10).'[...]</td><td style="text-align:right;">';
						else
							$res .= str_repeat($sep,$nb_sep).'&nbsp;'.$file->fields['name'].'</td><td style="text-align:right;">';
						$res.="<td>";
						if (dims_isadmin() || $file->fields['id_user']==$_SESSION['dims']['userid'])
							$res.="	<a href=\"javascript:dims_confirmlink('".dims_urlencode("$scriptenv?dims_op=doc_file_delete&docfile_id=".$file->fields['id'])."','".$_DIMS['cste']['_DIMS_CONFIRM']."');\">
										<img src=\"./common/img/delete.png\" alt=\"".$_DIMS['cste']['_DELETE']."\">
									</a>";
						$res .= '</td></tr>';
					}
				}
				foreach ($liste as $id_fold => $val){
					if (strpos($id_fold,'fold_') !== false){
						$res .= '<tr><td>';
						if (substr($id_fold,5) == 0){
							$res .= 'Racine';
						}else{
							$folder = new docfolder();
							$folder->open(substr($id_fold,5));
							if(strlen($folder->fields['name']) > 15)
								$res .= str_repeat($sep,$nb_sep).'&nbsp;'.substr($folder->fields['name'], 0, 15).'[...]';
							else
								$res .= str_repeat($sep,$nb_sep).'&nbsp;'.$folder->fields['name'];
						}
						$res .= '</td><td></td></tr>';
						$res .= echo_docs_tree($val,$sep,$nb_sep+1);
					}
				}
				return $res;
			}

			foreach ($lst_file as $id_gb){
				$gb_file = new dims_globalobject();
				$gb_file->open($id_gb);

				$doc_file = new docfile();
				$doc_file->open($gb_file->fields['id_record']);
				$liste_files[$doc_file->fields['id']] = $doc_file->fields['id'];

				$doc_folder = new docfolder();
				$doc_folder->open($doc_file->fields['id_folder']);

				$lst_arbo = array_merge_recursive($lst_arbo,create_treeArray($doc_folder->fields['parents'].';'.$doc_folder->fields['id']));
				//foreach($arbo as $id_arbo)
				//	$lst_arbo[];
			}
			$lst_complete = place_docs_tree($lst_arbo,$liste_files);

			//$lstfiles=dims_getFiles($dims,$id_module,$id_object,$id_record,true);
			if (count($lst_complete) > 0){
				echo '<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"margin-top:2px;margin-bottom:2px;\">';
				echo echo_docs_tree($lst_complete,'&#150;');
				echo '</table>';
			}
			?>
			</td>
		</tr>
	</table>
<?php echo $skin->close_widgetbloc(); ?>
