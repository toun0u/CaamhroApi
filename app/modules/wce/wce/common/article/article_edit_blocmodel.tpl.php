<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
global $_DIMS;
// recuperation des blocks
$blocks=$this->getBlocks();

// recuperation des modeles d'affichage
$models=$this->getWceSite()->getBlockModels();

if (isset($section['title']) && $section['title']!= '') {
    $label = '<span style="font-weight:bold;">'.$section['title'].'</span>';
}
else {
    $label = '<span style="font-weight:bold;">&nbsp;<span>';
}

$blockcontent='<div style="font-size:12px;color:#424242;border: 1px dashed grey;overflow:hidden;padding:5px;">
	<div style="background:#F9F9F9;width:100%;clear:both;display:block;padding-bottom:2px;float:right;text-align:right;padding-top:5px;padding-left:5px;">';

if (isset($section['title']) && $section['title']!='') {
    $blockcontent.='<div style="float:left;font-size:13px;margin-left:5px;">
			'.$label.'
		</div>';
}

$blockcontent.='<a title="'.$_SESSION['cste']['_ADD_SECTION'].'" href="javascript:void(0);" onclick="javascript:window.parent.wceAddBlock(1,'.$_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang'].');">
			<img title="'.$_SESSION['cste']['_ADD_SECTION'].'" alt="'.$_SESSION['cste']['_ADD_SECTION'].'" src="'.module_wce::getTemplateWebPath('/gfx/icon_add.png').'" border="0">
		</a></div>';
$nblevel=wce_article::NB_LEVEL;

// test si title

$position=0;
$levelblock=0;
foreach($blocks as $pos=>$block) {
	$position++;
	// verification de la position des blocs
	if ($block['position']!=$position) {
		$bblock = new wce_block();
		$bblock->open($block['id']);
		$bblock->fields['position']=$position;
		$bblock->save();
	}

	$oldlevel=$levelblock;
	$levelblock=$block['level'];

	if (isset($models[$block['id_model']])) {
		$contenu=$models[$block['id_model']]['content'];
		$maxblock=0;

		if (isset($adminedit) && $adminedit==1) {

			// affichage de la box pour editer, changer de position
			$ischange=false;

			for($j=1;$j<=10;$j++) {
				if ($block['content'.$j]!=$block['draftcontent'.$j]) {
					$ischange=true;
				}

				if (!(strpos($contenu,"<CONTENT".$j.">")===false)) {
					$maxblock=$j;
				}
			}

			// construction des styles
			$float=($block['float']!='') ? $block['float'] : 'left';
			$width = ($block['width']!='') ? $block['width'] : '100%';
			$height = ($block['height']!='') ? $block['height'] : '';
			$display = ($block['display']!='') ? $block['display'] : 'block';
			$padding = ($block['padding']!='') ? $block['padding'] : '2px';
			$margin = ($block['margin']!='') ? $block['margin'] : '10px 0px 0px 0px';
			$fontfamily = ($block['font-family']!='') ? 'font-family:'.$block['font-family'] : '';
			$fontsize = ($block['font-size']!='' && $block['font-size']>0) ? 'font-size:'.$block['font-size'] : '';
			$fontweight = ($block['font-weight']!='') ? 'font-weight:'.$block['font-weight'] : '';
			$color = ($block['color']!='') ? 'color:'.$block['color'] : '';
			$backgroundcolor = ($block['background-color']!='') ? 'background-color:'.$block['background-color'] : '';
			$borderstyle = ($block['border-style']!='') ? 'border-style:'.$block['border-style'] : '';
			$bordercolor = ($block['border-color']!='') ? 'border-color:'.$block['border-color'] : '';
			$bordersize = ($block['border-size']!='') ? 'border-size:'.$block['border-size'] : '';

			$blockcontent.='<div style="border: dashed 1px #AAAAAA;'.$borderstyle.';'.$bordercolor.';'.$bordersize.';'.$backgroundcolor.';'.$color.';'.$fontfamily.';'.$fontsize.';'.$fontweight.';width:'.$width.';height:'.$height.';display:'.$display.';float:'.$float.';padding:'.$padding.';margin:'.$margin.';">';

			$blockcontent.='<div style="clear:both;display:block;float:left;font-size:12px;color:#AAAAAA;width:99%;">';
			// on place le bouton edition + status
			if ($ischange) {
				$blockcontent.='<span style="float:left;margin-right:5px;"><img id="icon_valid_'.$block['id'].'" src="/common/modules/wce/img/ico_wait.png" "'.$_SESSION['cste']['_MODIFY'].'"></span>';
			}else {
				$blockcontent.='<span style="float:left;margin-right:5px;"><img id="icon_valid_'.$block['id'].'" src="/common/modules/wce/img/ico_yes.png"></span>';
			}

			// on ajoute le bouton de modification
			//$blockcontent.='&nbsp;<a href="javascript:void(0);" onclick="window.parent.wceModifBlock('.$block['id'].');"><img src="./common/img/edit.png" border="0"></a>';

			// on ajoute le bouton de modification des proprietes avancees
			//$blockcontent.='&nbsp;<a href="javascript:void(0);" onclick="window.parent.wceModifBlockStyles('.$block['id'].');"><img src="./common/img/move.png" border="0"></a>';
			//$blockcontent.='&nbsp;<a href="javascript:void(0);" onclick="window.parent.wceSupBlock('.$block['id'].');"><img src="./common/img/delete.png" border="0"></a>';

			$titleblock='<span style="font-weight:bold;float:left;margin-left:5px;margin-right:5px;">';
			$indiceblock='';

			for ($k=1;$k<=$levelblock;$k++) {
				if ($k>1) $indiceblock.='.';
				$indiceblock.=$block['l'.$k];
			}
			if ($levelblock==1) $indiceblock.='.'; // on ajoute le point de premier element
			$titleblock.=$indiceblock.'</span>';

			// on construit le title + zone d'édition
			$titleblock.='<span id="zoneedit_dims_block'.$block['id'].'" style="color:#FA652B;cursor:pointer;"><span onclick="javascript:wceEditLittleBlock('.$block['id'].','.$block['id_lang'].',\'wce_block_title\');">'.$block['title'].'</span></span>';

			// on peut supprimer l'article
			$blockcontent.=$titleblock."
                <a style=\"text-decoration:none;float:right;margin-left:5px;\" href=\"javascript:void(0);\" onclick=\"javascript:window.parent.dims_confirmlink('".module_wce::get_url($_SESSION['dims']['wce']['sub']).'&sub='.module_wce::_SITE_PREVIEW."&action=".module_wce::_DELETE_BLOC."&block_id=".$block['id']."&lang=".$block['id_lang']."','".$_SESSION['cste']['_CONFIRM_DELETE_BLOCK']."');\">
                    <img border=\"0\" src=\"".module_wce::getTemplateWebPath('/gfx/icon_mini_suppr.png')."\"/>
                </a>";
			//$blockcontent.="&nbsp;<a href=\"javascript:void(0);\" onclick=\"javascript:window.parent.dims_confirmlink('/admin.php?op=manage_block&action=delete_block&block_id=".$block['id']."','Etes-vous sur de vouloir supprimer ce bloc ?')\"><img border=\"0\" src=\"./common/img/delete.png\"/></a>";

			$blockcontent.='
			<a style="text-decoration:none;float:right;margin-left:5px;" href="javascript:void(0);" onclick="window.parent.wceModifBlock('.$block['id'].','.$block['id_lang'].');">
					<img src="'.module_wce::getTemplateWebPath('/gfx/icon_mini_param.png').'" border="0">
			</a>';

			// calcul de la difference
			$diffpos=($levelblock-$oldlevel);
			// on va ajouter les commandes pour composer les niveaux
			// test non premiere ligne
			//echo $levelblock." - ".$oldlevel." - ".$nblevel. " ".$diffpos;
			if ($levelblock<=$nblevel && $block['position']>1 && $diffpos<1) {
				// on peut ajouter un niveau au bloc
				// ---------- TODDO : Mettre à jour le lien ---------- //
				$blockcontent.='
				<a style="text-decoration:none;float:right;margin-left:5px;" href="javascript:void(0);" onclick="window.parent.location.href=\''.module_wce::get_url($_SESSION['dims']['wce']['sub']).'&sub='.module_wce::_SITE_PREVIEW."&action=".module_wce::_ACTION_ART_RIGHT_BLOC."&block_id=".$block['id']."&lang=".$block['id_lang'].'\'">
						<img src="'.module_wce::getTemplateWebPath('/gfx/arrow-right.png').'" border="0">
				</a>';
			}
			else {
				$blockcontent.='<span style="float:right;margin-left:5px;"><img src="'.module_wce::getTemplateWebPath('/gfx/arrow-right-dis.png').'" border="0"></span>';
			}

			// on gère le cote gauche
			if ($levelblock>1) {
				// on peut ajouter un niveau au bloc
				$blockcontent.='
				<a style="text-decoration:none;float:right;margin-left:5px;"  href="javascript:void(0);" onclick="window.parent.location.href=\''.module_wce::get_url($_SESSION['dims']['wce']['sub']).'&sub='.module_wce::_SITE_PREVIEW."&action=".module_wce::_ACTION_ART_LEFT_BLOC."&block_id=".$block['id']."&lang=".$block['id_lang'].'\'">
						<img src="'.module_wce::getTemplateWebPath('/gfx/arrow-left.png').'" border="0">
				</a>';
			}
			else {
				$blockcontent.='<span style="float:right;margin-left:5px;"><img src="'.module_wce::getTemplateWebPath('/gfx/arrow-left-dis.png').'" border="0"></span>';
			}

			// on gère le haut
			if ($block['position']>1) {
				// on peut ajouter un niveau au bloc
				$blockcontent.='
				<a style="text-decoration:none;float:right;margin-left:5px;"  href="javascript:void(0);" onclick="window.parent.location.href=\''.module_wce::get_url($_SESSION['dims']['wce']['sub']).'&sub='.module_wce::_SITE_PREVIEW."&action=".module_wce::_ACTION_ART_UP_BLOC."&block_id=".$block['id']."&lang=".$block['id_lang'].'\'">
						<img src="'.module_wce::getTemplateWebPath('/gfx/arrow-up.png').'" border="0">
				</a>';
			}
			else {
				$blockcontent.='<span style="float:right;margin-left:5px;"><img src="'.module_wce::getTemplateWebPath('/gfx/arrow-up-dis.png').'" border="0"></span>';
			}

			// on gère le bas
			if ($block['position']<sizeof($blocks)) {
				// on peut ajouter un niveau au bloc
				$blockcontent.='
				<a style="text-decoration:none;float:right;margin-left:5px;"  href="javascript:void(0);" onclick="window.parent.location.href=\''.module_wce::get_url($_SESSION['dims']['wce']['sub']).'&sub='.module_wce::_SITE_PREVIEW."&action=".module_wce::_ACTION_ART_DOWN_BLOC."&block_id=".$block['id']."&lang=".$block['id_lang'].'\'">
						<img src="'.module_wce::getTemplateWebPath('/gfx/arrow-bottom.png').'" border="0">
				</a>';
			}
			else {
				$blockcontent.='<span style="float:right;margin-left:5px;"><img src="'.module_wce::getTemplateWebPath('/gfx/arrow-bottom-dis.png').'" border="0"></span>';
			}

			if($block['page_break'])
				$blockcontent .= '<span style="font-weight: bold; color: #7D7D7D;"> -- PAGE BREAK -- </span>';

			$blockcontent.='</div>';

			//if($block['page_break'])
			//	$blockcontent .= '<span style="font-weight: bold; color: #7D7D7D;"> -- PAGE BREAK -- </span>';

			//$blockcontent.='</div>';

			if ($block['display_title']==1) {
				$blockcontent.="<h2 style=\"clear:both\"><balise id=\"".$block['id']."\">".$block['title']."</balise></h2>";
			}

			// on boucle sur l'ensemble des contenus <CONTENT1, 2,3, etc.>
			for($i=1;$i<=10;$i++) {
				// detection de parametres eventuels
				$posstart=strpos($page,"<CONTENT$i>");

				$ctemp='<div style="font-size:12px;color:#424242;border: 1px dashed grey;overflow:hidden;padding:5px;">
                            <div id="wikiedit'.$block['id'].'_'.$i.'" style="float:left;clear:both;"><a href="javascript:void(0);" onclick="wceModifBlockContentCkeditor('.$block['id'].','.$block['id_lang'].','.$i.');$(this).unbind();"><img src="./common/img/edit.png" border="0"></a>';
				//$ctemp='<div style="border: dashed 1px #FF2222;overflow:auto;"><div style="float:left;"><a href="javascript:void(0);" onclick="window.parent.wceModifBlockContent('.$block['id'].');"><img src="./common/img/edit.png" border="0"></a>';

				// on regarde la position pour eventuellement inverser les contenus
				if ($i>1) {
					$ctemp.='<a href="javascript:void(0);" onclick="window.parent.wceMoveBlockContent(0,'.$block['id'].','.$i.');"><img src="/common/modules/wce/img/ico_left.gif" border="0"></a>';
				}

				if ($i<$maxblock) {
					$ctemp.='<a href="javascript:void(0);" onclick="window.parent.wceMoveBlockContent(1,'.$block['id'].','.$i.');"><img src="/common/modules/wce/img/ico_right.gif" border="0"></a>';
				}

				$chdiv='<form onsubmit="javascript:saveBloc('.$block['id'].','.$block['id_article'].',\''.module_wce::getTemplateWebPath('/gfx/puce_orange.png').'\',\''.$_SESSION['cste']['NOT_UP_TO_DATE'].'\');" class="ajaxForm" name="form_wce_block'.$block['id'].'_'.$i.'" id="form_wce_block'.$block['id'].'_'.$i.'" style="margin:0;" action="'.module_wce::get_url($_SESSION['dims']['wce']['sub']).'&sub='.module_wce::_SITE_PREVIEW."&action=".module_wce::_ACTION_ART_SAVE_BLOC_AJAX.'&block_id='.$block['id'].'&lang='.$block['id_lang'].'&content_id='.$i.'" method="post" enctype="multipart/form-data"><textarea style="display:none;visibility:hidden" name="fck_contentBlockReturn'.$block['id'].'_'.$i.'" id="fck_contentBlockReturn'.$block['id'].'_'.$i.'" value="dsd"></textarea><div style="clear:both;" class="ckeditoriz" id="block'.$block['id'].'_'.$i.'">';
				$chdivend='</div></form>';
				if ($ischange) {
					if ($block['draftcontent'.$i]=='') {
						$block['draftcontent'.$i]="&nbsp;";
					}

					$ctemp.='</div>'.$chdiv.$block['draftcontent'.$i].$chdivend.'</div>';
				}
				else {
					if ($block['content'.$i]=='') {
						$block['content'.$i]="&nbsp;";
					}
					$ctemp.='</div>'.$chdiv.$block['content'.$i].$chdivend.'</div>';
				}

				if ($posstart>=0 ) {
					$posend=strpos($page,"</CONTENT$i>");

					if (($posstart+strlen("<CONTENT$i>"))==$posend || ($posend==0))	{
						// cas ou pas de params en plus ou </content inexistant

						$contenu = str_replace("<CONTENT$i>",$ctemp, $contenu);
						$contenu = str_replace("</CONTENT$i>", "", $contenu);
					}
					else {
						// on  a qq chose
						$chparams=substr($page,$posstart+strlen("<CONTENT$i>"),$posend-($posstart+strlen("<CONTENT$i>")));
						$contenu = str_replace("<CONTENT$i>$chparams</CONTENT$i>", $ctemp, $contenu);
						$contenu = str_replace("</CONTENT$i>", "", $contenu);
					}

				}
			}

			$blockcontent.= $contenu.'</div>';
		}
	}
}
$blockcontent.='<script type="text/javascript"> window[\'saveBloc\'] = function saveBloc(id,idArt,ico,label){
	var elem = document.getElementById(\'icon_valid_\'+id);
    elem.src=ico;
	elem.title=label;
	elem.alt=label;
	var elem2 = window.parent.document.getElementById(\'icon_valid_article\');
	elem2.setAttribute(\'onclick\',"javascript:document.location.href=\''.module_wce::get_url($_SESSION['dims']['wce']['sub']).'&sub='.module_wce::_SITE_PREVIEW."&action=".module_wce::_ACTION_VALID_ARTICLE.'&lang='.$this->fields['id_lang'].'&articleid="+idArt+"\';");
	elem2.src = \'./common/modules/wce/wiki/gfx/icon_validation.png\';
    $(\'img#icon_valid_\'+id).attr(\'src\',ico);
} </script>';

?>
