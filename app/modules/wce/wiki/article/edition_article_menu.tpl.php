<?
$user = $this->getLightAttribute('user');
$contactadd = $this->getLightAttribute('contact');
$dd = $this->getLightAttribute('dd');
$versionid=dims_load_securvalue("versionid",dims_const::_DIMS_NUM_INPUT,true,true);
$lstLanguesDispo = $this->getListArticleLangVersion();
$ok = false;
if (count($lstLanguesDispo) > 0){
    foreach($lstLanguesDispo as $lang){
        if($lang->fields['id'] == $_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang'])
            $ok = true;
    }
    if (!$ok){
        if (count($lstLanguesDispo) > 0)
            $l = $lstLanguesDispo[0]->fields['id'];
        else{
            $site = new wce_site($this->db,$this->fields['id_module']);
            $l = $site->getDefaultLanguage();
        }
        dims_redirect(module_wiki::getScriptEnv('sub='.module_wiki::_SUB_NEW_ARTICLE."&articleid=".$this->fields['id']."&action=".module_wiki::_ACTION_SHOW_ARTICLE."&lang=".$l));
    }
}
?>
<div class="header_article">
	<div class="cadre_zone_article bloc_gauche">
		<h2 style="float:left;font-size:20px;font-weight:bold;margin-bottom:10px;">
			<? echo $this->fields['title'];
			if ( ! $this->isUptodate()) {
				?>
				<img id="global_article_puce" src="<? echo module_wiki::getTemplateWebPath('/gfx/puce_orange.png'); ?>" title="<?= $_SESSION['cste']['NOT_UP_TO_DATE']; ?>" alt="<?= $_SESSION['cste']['NOT_UP_TO_DATE']; ?>" />
				<?
			}else {
				?>
				<img id="global_article_puce" src="<? echo module_wiki::getTemplateWebPath('/gfx/puce_verte.png'); ?>" title="<?= $_SESSION['cste']['UP_TO_DATE']; ?>" alt="<?= $_SESSION['cste']['UP_TO_DATE']; ?>" />
				<?
			}
			?>
	    </h2>

		<div class="zone_picture_date">
	        <div class="date_depose">
				<p class="par">
					<? echo $_SESSION['cste']['_DIMS_LABEL_FROM']; ?>
					<span class="maj_par"><? echo $user->fields['firstname']." ".$user->fields['lastname']; ?></span>
				</p>
	        </div>
	        <div class="label" style="clear:both;float:left;">
				<? echo $_SESSION['cste']['_DIMS_LABEL_LANG']." : "; ?>
	        </div>
	        <div class="label" style="float:left;">
				<select id="article_id_lang" name="article_id_lang">
					<?
					//$listArticleVersion=$this->getListArticleLangVersion();
					foreach(wce_lang::getInstance()->getAll(true) as $lang){
						$class = $etoile = "";
						if (!isset($lstLanguesDispo[$lang->fields['id']])){
							$class = 'class="create_bloc_lang"';
							$etoile = "&nbsp;&#42;";
						}
						if ($_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang'] == $lang->fields['id']){
							?>
							<option <? echo $class; ?> value="<? echo $lang->fields['id']; ?>" selected=true><? echo $lang->fields['label'].$etoile; ?></option>
							<?
						}else {
							?>
							<option <? echo $class; ?> value="<? echo $lang->fields['id']; ?>"><? echo $lang->fields['label'].$etoile; ?></option>
							<?
						}
					}
					?>
				</select>
				<script type="text/javascript">
					$(document).ready(function(){
						$('select#article_id_lang').change(function(){
							if($('option:selected',$(this)).hasClass('create_bloc_lang')){
								if(confirm('<? echo $_SESSION['cste']['_CONFIRM_INITIALIZE_NEW_LANGUAGE']; ?>'))
									document.location.href='<? echo module_wiki::getScriptEnv('sub='.module_wiki::_SUB_NEW_ARTICLE."&articleid=".$this->fields['id']."&action=".module_wiki::_ACTION_GENERATE_NEW_LANG."&lang=".$_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang']."&newlang="); ?>'+$(this).val();
								else
									$(this).val(<? echo $_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang']; ?>);
							}else
								document.location.href='<? echo module_wiki::getScriptEnv('sub='.module_wiki::_SUB_NEW_ARTICLE."&articleid=".$this->fields['id']."&action=".module_wiki::_ACTION_SHOW_ARTICLE."&lang="); ?>'+$(this).val();
						});
					});
				</script>
	        </div>
            <?
            if($this->getLightAttribute('wce_mode') != 'render'){
                ?>
                <div id="openTradMode" class="label" style="float:left;margin-left:10px;cursor:pointer;">
                    <img style="margin-top: 4px;" src="<? echo module_wiki::getTemplateWebPath('/gfx/split16.png'); ?>" alt="<? echo $_SESSION['cste']['_TRANSLATION_MODE']; ?>" title="<? echo $_SESSION['cste']['_TRANSLATION_MODE']; ?>" />
                </div>
                <?
            }
            ?>
            <div class="label" style="float:left;margin-left:10px;">
                <?php echo $_SESSION['cste']['_VERSIONS']." : "; ?>
                <select id="selectVersionArticle" name="selectVersionArticle" onchange="javascript:changeViewEditArticle(<? echo $this->fields['id']; ?>,<?php echo $_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang'];?>);">
                    <?php
                // on calcul les version de l'article
                    $listversion=$this->getListVersion($_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang']);
                    $c=0;

                    if (!empty($listversion)) {

                        foreach ($listversion as $version) {
                            if ($c==0 && !$this->isUptodate()) echo '<option value="0">'.$version.'.x</option>';
                            if ($version == $versionid) $selectversion='selected';
                            else $selectversion='';
                            echo '<option '.$selectversion.' value="'.$version.'">'.$version.'</option>';
                            $c++;
                        }
                    }
                    else {
                        if ($c==0 && !$this->isUptodate()) echo '<option value="0">0.x</option>';
                    }
                    ?>
                </select>
            </div>
			<?
			$lst = $this->searchGbLink(dims_const::_SYSTEM_OBJECT_CATEGORY);
			if (count($lst) > 0){
			?>
	        <div class="label" style="float:left;margin-left:10px;">
				<? echo $_SESSION['cste']['_RSS_LABEL_CATEGORY']." : "; ?>
	        </div>
	        <div class="label" style="float:left;">
                        <?
                            $par = new category();
                            $par->openWithGB(current($lst));
                            echo $par->fields['label'];
                        ?>
	        </div>
			<? } ?>
		</div>
	</div>

	<div class="cadre bloc_droite">
		<div class="zone_picture_date info_article">
			<div class="picture">
			<?
			if (isset($contactadd) && ($contactadd->getPhotoWebPath(60) != '' && file_exists($contactadd->getPhotoPath(60))))
				echo '<img class="ab_desc_image" src="'.$contactadd->getPhotoWebPath(60).'" border="0" title="picture" alt="picture" />';
			else
				echo '<img class="ab_desc_image" src="'.module_wiki::getTemplateWebPath('/gfx/human40.png').'" border="0" title="picture" alt="picture" />';
			?>

			</div>
			<div class="date_depose">
				<p>
					<?
					echo $_SESSION['cste']['_SYSTEM_LABEL_FICHCREATED']." ".$dd['date']." - ".$dd['time'];
					?>
				</p>
				<p class="par"><? echo $_SESSION['cste']['_DIMS_LABEL_FROM']; ?> <span class="maj_par"><? echo $user->fields['firstname']." ".$user->fields['lastname']; ?></span></p>
			</div>
		</div>
		<div class="categories">
	        <?/*
			<span><img src="<? echo module_wiki::getTemplateWebPath('/gfx/icon_categ.png'); ?>" title="retour" alt="retour" /></span>
			<a class="lien_bleu" href="#">Attaques</a></span>*/
	        ?>
		</div>
		<div class="table_article_options">
			<table cellpadding="0" cellspacing="0" class="article_tab">
				<tbody>
					<tr>
						<td class="article_tab">
							<?
							if (isset($_SESSION['dims']['wiki']['wce_mode']) && $_SESSION['dims']['wiki']['wce_mode']=='edit' || !isset($_SESSION['dims']['wiki']['wce_mode']) ) {
							?>
								<a href="<?php echo module_wiki::getScriptEnv('sub='.module_wiki::_SUB_NEW_ARTICLE.'&action='.module_wiki::_ACTION_SHOW_ARTICLE); ?>&articleid=<? echo $this->fields['id']."&lang=".$_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang']; ?>&wce_mode=render"><img style="cursor:pointer;" src="<? echo module_wiki::getTemplateWebPath('/gfx/icon_voir.png'); ?>" title="voir" alt="voir" /></a>
							<?
							}
							else {
							?>
								<a href="<?php echo module_wiki::getScriptEnv('sub='.module_wiki::_SUB_NEW_ARTICLE.'&action='.module_wiki::_ACTION_SHOW_ARTICLE); ?>&articleid=<? echo $this->fields['id']."&lang=".$_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang']; ?>&wce_mode=edit"><img style="cursor:pointer;" src="<? echo module_wiki::getTemplateWebPath('/gfx/icone_article.png'); ?>" title="voir" alt="voir" /></a>
							<?
							}
							?>
						</td>
						<?php // onclick="javascript:editArticleProperties(<? echo $this->fields['id'];   ?>
						<td class="article_tab">
							<a href="<?php echo module_wiki::getScriptEnv('sub='.module_wiki::_SUB_NEW_ARTICLE.'&articleid='.$this->getId().'&action='.module_wiki::_PARAMETERS_VIEW.'&wce_mode=render').'&params_op='.module_wiki::_SHOW_INFO_GENERALES."&lang=".$_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang']; ?>" alt="<? echo $_SESSION['cste']['_PARAMETERS']; ?>", title="<? echo $_SESSION['cste']['_PARAMETERS']; ?>">
								<img  src="<? echo module_wiki::getTemplateWebPath('/gfx/icon_param.png'); ?>" title="<? echo $_SESSION['cste']['_PARAMETERS']; ?>" alt="<? echo $_SESSION['cste']['_PARAMETERS']; ?>" />
							</a>
						</td>
						<td class="article_tab">
							<a href="<?php echo module_wiki::getScriptEnv('sub='.module_wiki::_SUB_NEW_ARTICLE.'&articleid='.$this->getId().'&action='.module_wiki::_COLLABORATION_VIEW.'&wce_mode=render').'&collab_op='.module_wiki::_SHOW_COLLABORATION."&lang=".$_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang'];?>" alt="<? echo $_SESSION['cste']['_COLLABORATION']; ?>", title="<? echo $_SESSION['cste']['_COLLABORATION']; ?>">
								<img src="<? echo module_wiki::getTemplateWebPath('/gfx/icon_commentaire.png'); ?>" title="<? echo $_SESSION['cste']['_COLLABORATION']; ?>" alt="<? echo $_SESSION['cste']['_COLLABORATION']; ?>" />
							</a>
						</td>
						<td class="article_tab">
							<?
							if ( ! $this->isUptodate()) {
							?>
							<img id="icon_valid_article" onclick="javascript:document.location.href='<?php echo module_wiki::getScriptEnv('sub='.module_wiki::_SUB_NEW_ARTICLE.'&articleid='.$this->getId().'&action='.module_wiki::_ACTION_VALID_ARTICLE."&lang=".$_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang']); ?>';" style="cursor:pointer;" src="<? echo module_wiki::getTemplateWebPath('/gfx/icon_validation.png'); ?>" title="<? echo $_SESSION['cste']['_DIMS_VALID']; ?>" alt="<? echo $_SESSION['cste']['_DIMS_VALID']; ?>" />
							<?
							}else{
							?>
							<img id="icon_valid_article" style="cursor:pointer;" src="<? echo module_wiki::getTemplateWebPath('/gfx/icon_validation_dis.png'); ?>" title="validation" alt="validation" />
							<? } ?>
						</td>

						<td class="article_tab">
							<a href="<?php echo module_wiki::getScriptEnv('dims_op=wiki&op_wiki=articlewiki_xml&id_article='.$this->getId()); ?>" alt="<? echo $_SESSION['cste']['_COLLABORATION']; ?>" title="<? echo $_SESSION['cste']['_COLLABORATION']; ?>">
								<img style="cursor:pointer;" src="<? echo module_wiki::getTemplateWebPath('/gfx/wordbee.png'); ?>" title="Wordbee" alt="Wordbee" />
                            </a>
						</td>
                        <td class="article_tab">
							<a onclick="javascript:importNewLang(<? echo $this->fields['id']; ?>);" href="javascript:void(0);" alt="<? echo $_SESSION['cste']['_LABEL_IMPORT']; ?>" title="<? echo $_SESSION['cste']['_LABEL_IMPORT']; ?>">
								<img style="cursor:pointer;" src="<? echo module_wiki::getTemplateWebPath('/gfx/wordbee2.png'); ?>" title="<? echo $_SESSION['cste']['_LABEL_IMPORT']; ?>" alt="<? echo $_SESSION['cste']['_LABEL_IMPORT']; ?>" />
                            </a>
						</td>
							<td class="phishing">
								<img src="<? echo module_wiki::getTemplateWebPath('/gfx/icon_refus.png'); ?>" title="refus" alt="refus" />
						</td>
						<td class="article_tab">
							<?
							if (isset($this->fields['id']) && $this->fields['id']>0 && (dims_isactionallowed(0) || $this->fields['id_user']==$_SESSION['dims']['userid']))
							?>
							<a href="javascript:void(0);" onclick="<? echo "javascript:dims_confirmlink('/admin.php?dims_op=wiki&op_wiki=articlewiki_delete&id_article=".$this->fields['id']."','Etes-vous certain de vouloir supprimer l\'article &laquo; ".addslashes($this->fields['title'])." &raquo; ?')"; ?>"> <img src="<? echo module_wiki::getTemplateWebPath('/gfx/icon_suppr.png'); ?>" title="<? echo $_SESSION['cste']['_DELETE']; ?>" alt="<? echo $_SESSION['cste']['_DELETE']; ?>" /> </a>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>
<?
$site = new wce_site($this->db,$this->fields['id_module']);
$defLang = $site->getDefaultLanguage();
?>
<script type="text/javascript">
    $(window).load(function(){
        $('div#openTradMode').click(function(){
            openTradMode();
        });
    });
    var oldSize = 0;
    var oldUrl = '';
    var diffHeight = 60;
	function openTradMode(){
        oldUrl = $("div#page_content div.contener #wce_frame_editor").attr('src');
        <?
        if($this->getLightAttribute('wce_mode') == 'render'){
            ?>
            $("div#page_content div.contener #wce_frame_editor").attr('src',"<? echo module_wiki::getScriptEnv("sub=".module_wiki::_SUB_NEW_ARTICLE."&action=".module_wiki::_ACTION_EDIT_ARTICLE."&articleid=".$this->fields['id']."&wce_mode=edit&readonly=0&adminedit=1&lang=".$_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang']."&resize=1"); ?>");
            <?
        }
        ?>
		$('div.header').hide();
		$('div.header_article').hide();
        oldSize = $("div#page_content div.contener #wce_frame_editor").height();
		var height = $(window).height()-$('div#top').height()-diffHeight;
        var iframeHeight = height/2;
        var origin1 = iframeHeight;
        var origin2 = iframeHeight;
        var url = "<? echo module_wiki::getScriptEnv("sub=".module_wiki::_SUB_NEW_ARTICLE."&action=".module_wiki::_ACTION_EDIT_ARTICLE."&articleid=".$this->fields['id']."&wce_mode=render&readonly=0&adminedit=1&resize=1&lang="); ?>";

        $("div#page_content div.contener #wce_frame_editor").attr('height',iframeHeight).css({'border':'0px', 'width':'100%', 'height': iframeHeight+'px', 'margin':'0px','padding':'0px'});
        var content = ' <div class="iframe_separator">\
                            <div class="label">\
                                <? echo $_SESSION['cste']['_LIST_LANGUAGES']; ?>&nbsp;:&nbsp;\
                                <select onchange="javascript:$(\'div#page_content div.contener iframe#wce_frame_lang\').attr(\'src\',\'<? echo module_wiki::getScriptEnv("sub=".module_wiki::_SUB_NEW_ARTICLE."&action=".module_wiki::_ACTION_EDIT_ARTICLE."&articleid=".$this->fields['id']."&wce_mode=render&readonly=0&adminedit=1&resize=1"); ?>&lang=\'+$(this).val());">\
                                    <?
                                    $PersoLang = 0;
                                    foreach($lstLanguesDispo as $lang){
                                        if($lang->fields['id'] != $_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang']){
                                            $sel = ($lang->fields['id'] == $defLang)?"selected=true":"";
                                            echo '<option value="'.$lang->fields['id'].'" '.$sel.'>'.$lang->fields['label'].'</option>';
                                            if($PersoLang == 0)
                                                $PersoLang = $lang->fields['id'];
                                        }
                                    }
                                    ?>
                                </select>\
                            </div>\
                            <div class="ui-state-default" style="float:left;width:17px; height:17px; margin-top:3px;">\
                                <span class="ui-icon ui-icon-arrowthick-2-n-s" style="cursor: row-resize; height:20px;width:20px;">&nbsp;</span>\
                            </div>\
                            <img onclick="javascript:closeTradMode();" src="<? echo module_wiki::getTemplateWebPath('/gfx/icon_mini_suppr.png'); ?>" alt="<? echo $_SESSION['cste']['_DIMS_CLOSE']; ?>" title="<? echo $_SESSION['cste']['_DIMS_CLOSE']; ?>" style="cursor:pointer;float:right;margin-top: 4px;" />\
                        </div>\
                        <iframe src="'+url+'<? echo $PersoLang; ?>" style="border:0;width:100%;height:'+iframeHeight+'px;margin:0px;padding:0px;" id="wce_frame_lang"></iframe>';
        $("div#page_content div.contener").append(content);
        $("div#page_content div.contener div.iframe_separator div.ui-state-default").css('margin-left',(($("div#page_content div.contener div.iframe_separator").width()/2)-$("div#page_content div.contener div.iframe_separator div.label").width())+"px");
        $(window).resize(function(){
            var height2 = $(window).height()-$('div#top').height()-diffHeight;
            var iframeHeight2 = (height2*origin1)/height;
            origin1 = iframeHeight2;
            origin2 = height2-iframeHeight2;
            $("div#page_content div.contener iframe#wce_frame_editor").attr('height',origin1).css({'border':'0px', 'width':'100%', 'height': origin1+'px', 'margin':'0px','padding':'0px'});
            $("div#page_content div.contener iframe#wce_frame_lang").attr('height',origin2).css({'border':'0px', 'width':'100%', 'height': origin2+'px', 'margin':'0px','padding':'0px'});
            $("div#page_content div.contener div.iframe_separator div.ui-state-default").css('margin-left',(($("div#page_content div.contener div.iframe_separator").width()/2)-$("div#page_content div.contener div.iframe_separator div.label").width())+"px");
            height = $(window).height()-$('div#top').height()-diffHeight;
        });
        var clicking = -1;
        $("div#page_content div.contener div.iframe_separator div.ui-state-default").mousedown(function(event){
            clicking = event.pageY;
            origin1 = $("div#page_content div.contener iframe#wce_frame_editor").height();
            origin2 = $("div#page_content div.contener iframe#wce_frame_lang").height();
        });
        $("body").mouseup(function(event){
			if(clicking >= 0){
				clicking = -1;
				origin1 = $("div#page_content div.contener iframe#wce_frame_editor").height();
				origin2 = $("div#page_content div.contener iframe#wce_frame_lang").height();
			}
        }).mousemove(function(event){
            if(clicking >= 0){
                var diff = event.pageY-clicking;
                var height1 = origin1+diff;
                $("div#page_content div.contener iframe#wce_frame_editor").attr('height',height1).css({'border':'0px', 'width':'100%', 'height': height1+'px', 'margin':'0px','padding':'0px'});
                var height2 = origin2-diff;
                $("div#page_content div.contener iframe#wce_frame_lang").attr('height',height2).css({'border':'0px', 'width':'100%', 'height': height2+'px', 'margin':'0px','padding':'0px'});
            }
        }).mouseout(function(event){
			if(clicking >= 0){
				var diff = event.pageY-clicking;
                var height1 = origin1+diff;
                $("div#page_content div.contener iframe#wce_frame_editor").attr('height',height1).css({'border':'0px', 'width':'100%', 'height': height1+'px', 'margin':'0px','padding':'0px'});
                var height2 = origin2-diff;
                $("div#page_content div.contener iframe#wce_frame_lang").attr('height',height2).css({'border':'0px', 'width':'100%', 'height': height2+'px', 'margin':'0px','padding':'0px'});
			}
		});
		$("div#page_content div.contener iframe").mouseup(function(event){ // pour IE & Safari
			if(clicking >= 0){
				clicking = -1;
				origin1 = $("div#page_content div.contener iframe#wce_frame_editor").height();
				origin2 = $("div#page_content div.contener iframe#wce_frame_lang").height();
			}
		});
		$("div#page_content div.contener div.iframe_separator div.ui-state-default").mousedown(function(event){
            clicking = event.pageY;
            origin1 = $("div#page_content div.contener iframe#wce_frame_editor").height();
            origin2 = $("div#page_content div.contener iframe#wce_frame_lang").height();
        });
	}
    function closeTradMode(){
        $("div#page_content div.contener div.iframe_separator").remove();
        $("div#page_content div.contener iframe#wce_frame_lang").remove();
        if(oldUrl != '' && oldUrl != $("div#page_content div.contener #wce_frame_editor").attr('src'))
            $("div#page_content div.contener #wce_frame_editor").attr('height',oldSize).css({'border':'0px', 'width':'100%', 'height': oldSize+'px', 'margin':'0px','padding':'0px'}).attr('src',oldUrl);
        else
            $("div#page_content div.contener #wce_frame_editor").attr('height',oldSize).css({'border':'0px', 'width':'100%', 'height': oldSize+'px', 'margin':'0px','padding':'0px'});
        $('div.header').show();
		$('div.header_article').show();
    }
</script>
