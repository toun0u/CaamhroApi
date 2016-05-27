<?php
$lstRef = $this->getReferences();

$nbref=sizeof($lstRef);
?>
<h4>
	<img src="<?= module_wiki::getTemplateWebPath('/gfx/icone_article.png');?>"  /> <?= $_SESSION['cste']['REFERENCES']; ?>
</h4>
<div class="container_admin global_content_record todo_form">
	<div class="form_object_block">
		<form name="form_properties" id="form_properties" action="" method="POST">
			<?php
			$glob_message = $this->getLightAttribute("global_error");
			if(empty($glob_message)){
				?>
				<div class="global_message error_message" style="display: none;"></div>
				<?php
			}elseif(is_array($glob_message)) {
				?>
				<ul class="global_message error_message">
					<?php
					foreach($glob_message as $error) {
						?>
						<li>
							<?php echo $glob_message;?>
						</li>
						<?php
					}
					?>
				</ul>
				<?php
			}else {
				?>
				<div class="global_message error_message"><?php echo $glob_message;?></div>
				<?php
			}
			$dims=dims::getInstance();
			?>
			<div class="sub_bloc">
				<div class="sub_bloc_form">
					<div style="float:right;margin-bottom: 5px;">
						<a href="<?php echo module_wiki::getScriptEnv('sub='.module_wiki::_SUB_NEW_ARTICLE.'&action='.module_wiki::_PARAMETERS_VIEW.'&articleid='.$this->getId().'&params_op='.module_wiki::_ADD_REFERENCES."&lang=".$this->fields['id_lang']); ?>">
							<img src="<? echo module_wiki::getTemplateWebPath('/gfx/ajouter16.png'); ?>" alt="<? echo $_SESSION['cste']['_DIMS_ADD']; ?>" title="<? echo $_SESSION['cste']['_DIMS_ADD']; ?>" />
							<?php echo $_SESSION['cste']['_DIMS_ADD']; ?>
						</a>
					</div>

					<div class="table_article" style="clear:both;">

						<table cellpadding="0" cellspacing="0" border="1">
							<tbody>
								<tr>
									<td class="table_article_title">
										<? echo $_SESSION['cste']['_ARTICLE']; ?>
									</td>
									<td class="table_article_title">
										<? echo $_SESSION['cste']['_TYPE']; ?>
									</td>
									<td class="table_article_title">
										<? echo $_SESSION['cste']['_DIMS_LABEL_LINKS']; ?>
									</td>
									<td class="table_article_title center">
										<? echo $_SESSION['cste']['_POSITION']; ?>
									</td>
									<td class="table_article_title center" >
										<? echo $_SESSION['cste']['_DIMS_ACTIONS']; ?>
									</td>
								</tr>
								<?
								foreach($lstRef as $ref){
									?>
									<tr>
										<td class="table_article_text">
											<?php
											/*<a class="lien_bleu" href="<? echo module_wiki::getScriptEnv('sub='.module_wiki::_SUB_NEW_ARTICLE.'&action='.module_wiki::_PARAMETERS_VIEW.'&articleid='.$this->getId().'&params_op='.module_wiki::_ADD_REFERENCES."&id_reference=".$ref['id']); ?>">*/
											echo $ref['label'];
											if($ref['id_lang'] != '' && $ref['id_lang'] > 0){
												$lang = new wce_lang();
												$lang->open($ref['id_lang']);
												?>
												<img style="padding-right:5px;" src="<? echo $lang->getFlag(); ?>" alt="<? echo $lang->fields['label']; ?>" title="<? echo $lang->fields['label']; ?>" />
												<?
											}else{
												echo " (".$_SESSION['cste']['DEFAULT_MODEL'].")";
											}
											// </a>
											?>
										</td>
										<td class="table_article_text">
											<?
											if ($ref['typelink']==0)
												echo ucfirst ($_SESSION['cste']['_DIMS_LABEL_WEB_ADDRESS']);
											else
												echo ucfirst ($_SESSION['cste']['DOCUMENT']);
											?>
										</td>
										<td class="table_article_text">
											<?
											if ($ref['typelink']==0)
												echo '<a href="'.$ref['link'].'" target="_blank">'.$ref['link']."</a>";
											else {
												$doc = new docfile();
												$doc->open($ref['id_doc_link']);

												echo '<a href="'.$dims->getProtocol().$dims->getHttpHost().'/'.$doc->getwebpath().'">'.$ref['label']."</a>";
											}
											?>
										</td>
										<td class="table_article_text center">
											<?php

											if ($ref['position']<$nbref) {
												echo '<img style="cursor:pointer;" onclick="javascript:document.location.href=\''.module_wiki::getScriptEnv('sub='.module_wiki::_SUB_NEW_ARTICLE.'&action='.module_wiki::_PARAMETERS_VIEW.'&articleid='.$this->getId().'&params_op='.module_wiki::_CHANGEPOS_REFERENCES."&sens=2&id_reference=".$ref['id']).'\';" src="'.module_wiki::getTemplateWebPath('/gfx/icone_down.png').'" title="'.$_SESSION['cste']['_DIMS_DOWN'].'" alt="'.$_SESSION['cste']['_DIMS_DOWN'].'" />';
											}
											else {
												echo '<img src="'.module_wiki::getTemplateWebPath('/gfx/icone_down_dis.png').'" >';
											}

											if ($ref['position']>1) {
												echo '<img style="cursor:pointer;" onclick="javascript:document.location.href=\''.module_wiki::getScriptEnv('sub='.module_wiki::_SUB_NEW_ARTICLE.'&action='.module_wiki::_PARAMETERS_VIEW.'&articleid='.$this->getId().'&params_op='.module_wiki::_CHANGEPOS_REFERENCES."&sens=1&id_reference=".$ref['id']).'\';" src="'.module_wiki::getTemplateWebPath('/gfx/icone_up.png').'" title="'.$_SESSION['cste']['_DIMS_UP'].'" alt="'.$_SESSION['cste']['_DIMS_UP'].'" />';
											}
											else {
												echo '<img src="'.module_wiki::getTemplateWebPath('/gfx/icone_up_dis.png').'" >';
											}
											?>
										</td>
										<td class="table_article_text center">
											<img onclick="javascript:document.location.href='<? echo module_wiki::getScriptEnv('sub='.module_wiki::_SUB_NEW_ARTICLE.'&action='.module_wiki::_PARAMETERS_VIEW.'&articleid='.$this->getId().'&params_op='.module_wiki::_ADD_REFERENCES."&id_reference=".$ref['id']); ?>';" src="<? echo module_wiki::getTemplateWebPath('/gfx/icone_ouvrir.png'); ?>" title="<? echo $_SESSION['cste']['_DIMS_OPEN']; ?>" alt="<? echo $_SESSION['cste']['_DIMS_OPEN']; ?>" />
											<img onclick="javascript:duplicateRef(event,<? echo $ref['id']; ?>);" src="<? echo module_wiki::getTemplateWebPath('/gfx/icon_categ.png'); ?>" title="<? echo $_SESSION['cste']['_BUSINESS_LEGEND_RENEW']; ?>" alt="<? echo $_SESSION['cste']['_BUSINESS_LEGEND_RENEW']; ?>" />
											<?

											if ($ref['id']>0 && (dims_isadmin() || dims_isactionallowed(0) || $ref['id_user']==$_SESSION['dims']['userid']))
											?>
											<img onclick="<? echo "javascript:dims_confirmlink('/admin.php?dims_op=wiki&op_wiki=articlewiki_deleteref&id_reference=".$ref['id']."','Etes-vous certain de vouloir supprimer l\'article &laquo; ".addslashes($ref['label'])." &raquo; ?')"; ?>" src="<? echo module_wiki::getTemplateWebPath('/gfx/icone_suppression.png'); ?>" title="<? echo $_SESSION['cste']['_DELETE']; ?>" alt="<? echo $_SESSION['cste']['_DELETE']; ?>" />

										</td>
									</tr>
									<?
								}
								?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>
