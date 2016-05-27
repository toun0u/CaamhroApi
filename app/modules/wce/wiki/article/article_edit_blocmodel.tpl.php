<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
global $_DIMS;
// recuperation des blocks
$blocks=$this->getBlocks(false,$_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang']);
// recuperation des modeles d'affichage
$models=$this->wce_site->getBlockModels();

$blockcontent='<div style="width:100%;clear:both;display:block;padding-bottom:10px;float:left;text-align:left;"><a href="javascript:void(0);" onclick="javascript:window.parent.wceAddBlock()"><img src="/common/modules/wce/img/add_section.png" border="0">Ajouter</a></div>';

// test si title

$position=0;
foreach($blocks as $pos=>$block) {
	$position++;
	// verification de la position des blocs
	if ($block['position']!=$position) {
		$bblock = new wce_block();
		$bblock->open($block['id']);
		$bblock->fields['position']=$position;
		$bblock->save();
	}

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

			$blockcontent.='<div style="clear:both;display:block;float:left;font-size:12px;color:#AAAAAA;width:99%;">'.$block['title'];
			// on place le bouton edition + status
			if ($ischange) {
				$blockcontent.='&nbsp;<img src="/common/modules/wce/img/ico_wait.gif" "'.$_DIMS['cste']['_MODIFY'].'">';
			}
			else {
				$blockcontent.='&nbsp;<img src="/common/modules/wce/img/ico_yes.gif">';
			}

			// on ajoute le bouton de modification
			//$blockcontent.='&nbsp;<a href="javascript:void(0);" onclick="window.parent.wceModifBlock('.$block['id'].');"><img src="./common/img/edit.png" border="0"></a>';

			// on ajoute le bouton de modification des proprietes avancees
			//$blockcontent.='&nbsp;<a href="javascript:void(0);" onclick="window.parent.wceModifBlockStyles('.$block['id'].');"><img src="./common/img/move.png" border="0"></a>';
			//$blockcontent.='&nbsp;<a href="javascript:void(0);" onclick="window.parent.wceSupBlock('.$block['id'].');"><img src="./common/img/delete.png" border="0"></a>';

			// on peut supprimer l'article
			$blockcontent.="&nbsp;<a href=\"javascript:void(0);\" onclick=\"javascript:window.parent.dims_confirmlink('/admin.php?op=manage_block&action=delete_block&block_id=".$block['id']."','Etes-vous sur de vouloir supprimer ce bloc ?')\"><img border=\"0\" src=\"./common/img/delete.png\"/></a>";

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

				$ctemp='<div style="border: dashed 1px #FF2222;overflow:auto;"><div style="float:left;"><a href="javascript:void(0);" onclick="window.parent.wceModifBlockContent('.$block['id'].');"><img src="./common/img/edit.png" border="0"></a>';

				// on regarde la position pour eventuellement inverser les contenus
				if ($i>1) {
					$ctemp.='<a href="javascript:void(0);" onclick="window.parent.wceMoveBlockContent(0,'.$block['id'].','.$i.');"><img src="/common/modules/wce/img/ico_left.gif" border="0"></a>';
				}

				if ($i<$maxblock) {
					$ctemp.='<a href="javascript:void(0);" onclick="window.parent.wceMoveBlockContent(1,'.$block['id'].','.$i.');"><img src="/common/modules/wce/img/ico_right.gif" border="0"></a>';
				}

				if ($ischange) {
					if ($block['draftcontent'.$i]=='') {
						$block['draftcontent'.$i]="&nbsp;";
					}

					$ctemp.='</div>'.$block['draftcontent'.$i].'</div>';
				}
				else {
					if ($block['content'.$i]=='') {
						$block['content'.$i]="&nbsp;";
					}
					$ctemp.='</div>'.$block['content'.$i].'</div>';
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

?>
