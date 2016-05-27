<?php
ob_start();

				$sql = "SELECT 	*
						FROM 	dims_mod_business_tiers_import
						WHERE 	exist LIKE '0'";
				$res = $db->query($sql);
				$retour = '';
				$retour .= $skin->open_simplebloc($_DIMS['cste']['_LABEL_LIST_NEW_ENT'],'width:100%;float:left;clear:none;','','');
				$retour .= '<table width="100%" cellpadding="0" cellspacing="0" style="border:#738CAD 1px solid;margin-top:10px;margin-bottom:10px;">
							<tr class="trl1" style="font-size:12px;">
								<td style="width: 5%;"></td>
								<td style="width: 50%;">'.$_DIMS['cste']['_DIMS_LABEL_ENT_NAME'].'</td>
								<td style="width: 30%;">'.$_DIMS['cste']['_DIMS_LABEL_CITY'].'</td>
								<td style="width: 15%;">'.$_DIMS['cste']['_DIMS_OPTIONS'].'</td>
							</tr>';
				$i = 0;
				$class_col = 'trl1';
				while($tab_imp = $db->fetchrow($res)) {
					if ($class_col == 'trl2') $class_col = 'trl1'; else $class_col = 'trl2';
					$date_c = dims_timestamp2local($tab_imp['ent_datecreation']);
					$code_nace = '';
					if($tab_imp['ent_codenace'] != '') {
						$code_nace = $_DIMS['cste'][$tab_imp['ent_codenace']];
					}
					$retour .= '<tr class="'.$class_col.'">
									<td><input type="checkbox" id="ent_imp_'.$i.'" value="'.$tab_imp['id'].'"/></td>
									<td>'.$tab_imp['intitule'].'</td>
									<td>'.$tab_imp['ville'].'</td>
									<td>
										<a href="javascript:void(0);" onclick="javascript:view_detail_imp(\'detail_imp_'.$tab_imp['id'].'\');"><img src="./common/img/view.png" style="border:0px;"/></a>
										 / <a href="javascript:void(0);" onclick="javascript:add_imp_ent('.$tab_imp['id'].')"><img src="./common/img/add.gif" style="border:0px;"/></a>
										 / <a href="javascript:void(0);" onclick="javascript:del_imp_ent('.$tab_imp['id'].')"><img src="./common/img/del.png" style="border:0px;"/></a>
									</td>
								</tr>
								<tr class="'.$class_col.'">
									<td colspan="4">
										<div id="detail_imp_'.$tab_imp['id'].'" style="width:100%;display:none;">
											<table width="100%">
												<tr>
													<td width="20%" align="right">'.$_DIMS['cste']['_DIMS_LABEL_ADDRESS'].' : </td>
													<td width="30%" align="left">'.$tab_imp['adresse'].'</td>
													<td width="20%" align="right">'.$_DIMS['cste']['_DIMS_LABEL_ENT_WSITE'].' : </td>
													<td width="30%" align="left">'.$tab_imp['site_web'].'</td>
												</tr>
												<tr>
													<td width="20%" align="right">'.$_DIMS['cste']['_DIMS_LABEL_CP'].' : </td>
													<td width="30%" align="left">'.$tab_imp['codepostal'].'</td>
													<td width="20%" align="right">'.$_DIMS['cste']['_DIMS_LABEL_ENT_DIR'].' : </td>
													<td width="30%" align="left">'.$tab_imp['dirigeant'].'</td>
												</tr>
												<tr>
													<td width="20%" align="right">'.$_DIMS['cste']['_DIMS_LABEL_CITY'].' : </td>
													<td width="30%" align="left">'.$tab_imp['ville'].'</td>
													<td width="20%" align="right">'.$_DIMS['cste']['_DIMS_LABEL_ENT_CAPITAL'].' : </td>
													<td width="30%" align="left">'.$tab_imp['ent_capital'].'</td>
												</tr>
												<tr>
													<td width="20%" align="right">'.$_DIMS['cste']['_PHONE'].' : </td>
													<td width="30%" align="left">'.$tab_imp['telephone'].'</td>
													<td width="20%" align="right">'.$_DIMS['cste']['_DIMS_LABEL_ENT_EFFECTIF'].' : </td>
													<td width="30%" align="left">'.$tab_imp['ent_effectif'].'</td>
												</tr>
												<tr>
													<td width="20%" align="right">'.$_DIMS['cste']['_DIMS_LABEL_FAX'].' : </td>
													<td width="30%" align="left">'.$tab_imp['telecopie'].'</td>
													<td width="20%" align="right">'.$_DIMS['cste']['_DIMS_LABEL_ENT_DATEC'].' : </td>
													<td width="30%" align="left">'.$date_c['date'].'</td>
												</tr>
												<tr>
													<td width="20%" align="right">'.$_DIMS['cste']['_DIMS_LABEL_EMAIL'].' : </td>
													<td width="30%" align="left">'.$tab_imp['mel'].'</td>
													<td width="20%" align="right">'.$_DIMS['cste']['_DIMS_LABEL_ENT_CODE_NACE'].' : </td>
													<td width="30%" align="left">'.$code_nace.'</td>
												</tr>
												<tr>
													<td colspan="4">
														<table width="100%">
															<tr>
																<td>'.$_DIMS['cste']['_DIMS_PRESENTATION'].' : </td>
																<td>'.$tab_imp['presentation'].'</td>
															</tr>
														</table>
													</td>
												</tr>
											</table>
										</div>
									</td>
								</tr>';
					$i++;
				}
				$retour .= '</table>';
				$retour .= '<div style="float: left;"><img border="0" alt="0" src="./common/img/arrow_ltr.png"/></div>';
				$retour .= '<div style="float: left; margin-top: 4px;"><a onclick="javascript:selectAll();" style="color: rgb(115, 140, 173);" href="javascript:void(0);">Tout cocher</a> / <a onclick="javascript:unselectAll();" style="color: rgb(115, 140, 173);" href="javascript:void(0);">Tout décocher</a></div>';
				$retour .= '<div style="float:right;margin-top:4px;"><select id="ent_action"><option value="suppr">Supprimer</option></select>';
				$retour .= $skin->close_simplebloc();
				echo $retour;
				ob_end_flush();
				die();
?>
