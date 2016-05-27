<?
$article_object = new article_object();
$article_object->open($this->getLightAttribute('returnId'));

$article_title = "";
$lstObj = array();
if ($this->fields['id_article'] != '' && $this->fields['id_article'] > 0) {
	$article = new wce_article();
	$article->open($this->fields['id_article']);
	$article_title=$article->fields['title'];
	$lstObj = array_merge($lstObj, $article->getObjectCorresp(false));
}

$heading_title = "";
if ($this->fields['id_heading'] != '' && $this->fields['id_heading'] > 0) {
	$heading = new wce_heading();
	$heading->open($this->fields['id_heading']);
	$heading_title=$heading->fields['label'];
	$lstObj = array_merge($lstObj, $heading->getObjectCorresp(false));
}
?>
<div class="form_object_block">
	<form method="POST" action="<? echo module_wce::get_url(module_wce::_SUB_DYN); ?>" name="save_object">
		<input type="hidden" name="action" value="<? echo module_wce::_DYN_OBJ_SAVE_ART; ?>" />
		<input type="hidden" name="id" value="<? echo $this->getLightAttribute('returnId'); ?>" />
		<input type="hidden" name="id_head" value="<? echo $this->fields['id_heading']; ?>" />
		<input type="hidden" name="id_art" value="<? echo $this->fields['id_article']; ?>" />
		<div class="sub_bloc">
			<div class="sub_bloc_form">
				<table>
					<tr>
						<td rowspan="5">
							<?
							$this->display(module_wce::getTemplatePath("obj_dynamics/arborescence/display_browser_site.tpl.php"));
							?>
						</td>
						<td class="label_field" style="text-align: center;height: 25px;" colspan="2">
							<label>
								<? echo $_SESSION['cste']['_CHOOSING_TOPIC_OR_ARTICLE']; ?>
							</label>
						</td>
					</tr>
					<tr>
						<td class="label_field" style="height: 25px;">
							<label>
								<? echo $_SESSION['cste']['_SELECTED_TOPIC']; ?>
							</label>
						</td>
						<td style="height: 25px;">
							<input type="hidden" id="id_linkedheading" name="obj_id_heading" value="<? echo $this->fields['id_heading']; ?>" />
							<input type="text" readonly class="text" id="linkedheading_displayed" value="<? echo $heading_title; ?>" />
						</td>
					</tr>
					<tr>
						<td class="label_field" style="height: 25px;">
							<label>
								<? echo $_SESSION['cste']['_SELECTED_ARTICLE']; ?>
							</label>
						</td>
						<td style="height: 25px;">
							<input type="hidden" id="id_article_link" name="obj_id_article" value="<? echo $this->fields['id_article']; ?>" />
							<input type="text" readonly class="text" id="linkedpage_displayed" value="<? echo $article_title; ?>" />
						</td>
					</tr>
					<tr>
						<td class="label_field" style="text-align: center;height: 25px;" colspan="2">
							<label>
								<? echo $_SESSION['cste']['_DYNAMIC_OBJECTS']; ?>
							</label>
						</td>
					</tr>
					<tr>
						<td colspan="2" style="vertical-align: top;">
							<table cellpadding="0" cellspacing="0" style="width: 100%; border: 1px dashed #9E9E9E; border-bottom: 0px;">
								<?
								$db = dims::getInstance()->getDb();
								$sel = "SELECT 	*
										FROM 	".article_object::TABLE_NAME."
										WHERE 	id_module = :id_module";
								$res = $db->query($sel,array(':id_module'=>array('value'=>$_SESSION['dims']['moduleid'],'type'=>PDO::PARAM_INT)));
								while($r = $db->fetchrow($res)){
									?>
									<tr>
										<td style="padding:5px;border-bottom: 1px dashed #9E9E9E;">
											<?
											switch($r['type']){
												case WCE_OBJECT_TYPE_NEWS:
													echo $_SESSION['cste']['_DIMS_LABEL_NEWS'];
													break;
												case WCE_OBJECT_TYPE_UNE:
													echo $_SESSION['cste']['_DIMS_LABEL_TOP_NEWS'];
													break;
												case WCE_OBJECT_TYPE_NEWSLETTER:
													echo $_SESSION['cste']['_DIMS_LABEL_NEWSLETTER'];
													break;
												case WCE_OBJECT_TYPE_SONDAGE:
													echo substr($_SESSION['cste']['SMILE_SURVEYS'],0,-1);
													break;
												case WCE_OBJECT_TYPE_ALL_BREVES:
													echo "Toutes les br&egrave;ves";
													break;
												case WCE_OBJECT_TYPE_ALERTES:
													echo "Alertes";
													break;
												default:
													echo $_SESSION['cste']['_DIMS_LABEL_UNDEFINED'];
													break;
											}
											?>
										</td>
										<td style="padding:5px;border-bottom: 1px dashed #9E9E9E;">
											<? echo $r['label']; ?>
										</td>
										<td style="width:15px;padding:5px;border-bottom: 1px dashed #9E9E9E;">
											<input type="checkbox" name="lst_affect[]" value="<? echo $r['id']; ?>" <? echo (isset($lstObj[$r['id']]) || $r['id'] == $this->getLightAttribute('returnId'))?"checked=true":""; ?> />
										</td>
									</tr>
									<?
								}
								?>
							</table>
						</td>
					</tr>
				</table>
			</div>
			<div class="sub_form">
				<div class="form_buttons">
					<div>
						<input type="submit" value="<? echo $_SESSION['cste']['_DIMS_SAVE']; ?>"/>
					</div>
					<div>
						<? echo $_SESSION['cste']['_DIMS_OR']; ?>
						<a href="<? echo module_wce::get_url(module_wce::_SUB_DYN)."&action=".module_wce::_DYN_OBJ_VIEW."&id=".$this->getLightAttribute('returnId'); ?>">
							<? echo $_SESSION['cste']['_DIMS_LABEL_CANCEL']; ?>
						</a>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>