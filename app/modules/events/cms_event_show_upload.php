<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
ob_end_clean();
ob_start();
//require_once DIMS_APP_PATH.'modules/system/class_action.php';
require_once DIMS_APP_PATH.'modules/doc/include/global.php';
require_once(DIMS_APP_PATH . "/modules/doc/class_docfile.php");

$id_etap = dims_load_securvalue('id_etape',dims_const::_DIMS_NUM_INPUT,true,true,false);
$id_ct = dims_load_securvalue('id_ct',dims_const::_DIMS_NUM_INPUT,true,true,false);
$id_doc = dims_load_securvalue('id_doc',dims_const::_DIMS_NUM_INPUT,true,true,false);
$id_evt = dims_load_securvalue('id_evt',dims_const::_DIMS_NUM_INPUT,true,true,false);

$docfile = new docfile();
$docfile->init_description();

$view = '';
$view .= '	<table celpadding="0" cellspacing="0" style="border:#000000 1px solid;background-color:#dddddd;" width="100%">
				<tr>
					<td style="background-color:#777777;color:#ffffff;font-size:14px;font-weight:bold;padding:5px;" align="left">
						'.$_DIMS['cste']['_DIMS_LABEL_UPLOAD_DOCUMENT'].'
					</td>
				</tr>
				<tr>
					<td>
						<div class="doc_fileform">';
	$view .= '				<form id="docfile_add" name="docfile_add" action="index.php?action=save_eventfile" method="post" enctype="multipart/form-data">
								<input type="hidden" name="id_etape" value="'.$id_etap.'">
								<input type="hidden" name="id_ct" value="'.$id_ct.'">
								<input type="hidden" name="id_doc" value="'.$id_doc.'">
								<input type="hidden" name="id_evt" value="'.$id_evt.'">

								<div class="doc_fileform_main">
									<div class="dims_form" style="padding:2px;">
										<div id="ScrollBox" style="overflow:auto;">
											<table id="list_body" cellspacing="0" cellpadding="5" border="0" width="100%"><tbody></tbody></table>
											<iframe id="uploadForm" name="uploadForm" scrolling="No" style="visibility:hidden;" src=""></iframe>
										</div>
									</div>
								</div>
								<div id="sharefile_button" style="padding:20px;clear:both;float:left;width:30%;">
									 <span id="btn_upload" style="width:100%;display:block;float:left;">';
										  //<input type="button" class="flatbutton" style="" name="savefile" onclick="javascript:upload();" value="'.$_DIMS['cste']['_DIMS_SAVE'].'" />';
$view .= dims_create_button_nofloat($_DIMS['cste']['_DIMS_SAVE'],'./common/img/save.gif','javascript:upload();');
$view .= '							</span>
								</div>
								<div id="sharefile_button" style="padding:20px;float:right;width:30%;">
									<span style="width:50%;display:block;float:right;">';
										//<input type="button" class="flatbutton" style="" name="close" onclick="" value="'.$_DIMS['cste']['_DIMS_CLOSE'].'" />';
$view .= dims_create_button_nofloat($_DIMS['cste']['_DIMS_CLOSE'],'./common/img/close.png','javascript:dims_switchdisplay(\'dims_popup\');');
$view .= '							</span>
								</div>
							</form>
						</div>';
$view .= '			</td>
				</tr>
			</table>';
echo $view;
ob_end_flush();
?>
