<script type="text/javascript">
<?
include DIMS_APP_PATH.'modules/wce/include/javascript.php';
?>
</script>
<script type="text/javascript" src="./common/js/jquery.slidingmessage.js"></script>
<?
if($this->getLightAttribute('url') != '')
	$url = $this->getLightAttribute('url');
else
	$url = module_wce::get_url(module_wce::_SUB_SITE)."&sub=".module_wce::_SITE_PREVIEW."&action=".module_wce::_PREVIEW_ART."&readonly=0&adminedit=1&articleid=".$this->fields['id']."&wce_mode=".$this->getLightAttribute('wce_mode')."&lang=".$this->fields['id_lang'];

foreach ($_GET as $elemget => $valueget) {
	if (substr($elemget, 0,12)=='WCE_section_' && is_numeric($valueget)) {
		$url.='&'.$elemget.'='.$valueget;
	}
}
?>
<iframe name="wce_frame_editor" id="wce_frame_editor" style="border:0;width:100%;height:750px;margin:0;padding:0;" src="<? echo $url; ?>"></iframe>
<script type="text/javascript">
	$(document).ready(function(){
		$("#historic").load('admin.php?dims_op=wiki&op_wiki=get_historic');
        <?
        if(dims_load_securvalue('action',dims_const::_DIMS_CHAR_INPUT,true,true,false) == module_wce::_PREVIEW_EDIT){
            ?>
            $('div.wce_article_info_sup').append('<div id="openTradMode" onclick="openTradMode();" class="label" style="float:left;margin-left:10px;cursor:pointer;">\
                <img style="margin-top: 4px;" src="<? echo module_wce::getTemplateWebPath('/gfx/split16.png'); ?>" alt="<? echo $_SESSION['cste']['_TRANSLATION_MODE']; ?>" title="<? echo $_SESSION['cste']['_TRANSLATION_MODE']; ?>" />\
            </div>');
            <?
        }
        ?>
	});
    <?
    if(dims_load_securvalue('action',dims_const::_DIMS_CHAR_INPUT,true,true,false) == module_wce::_PREVIEW_EDIT){
        ?>
        var oldSize = 0;
        var oldUrl = '';
        var diffHeight = 50;
        var contenerWidth = 0;
        var topDisplay = leftDisplay = false;
        function openTradMode(){
            oldUrl = $("div#page_content div.contener #wce_frame_editor").attr('src');
            <?
            if($this->getLightAttribute('wce_mode') == 'render'){
                ?>
                $("div#page_content div.contener #wce_frame_editor").attr('src',"<? echo module_wiki::getScriptEnv("sub=".module_wiki::_SUB_NEW_ARTICLE."&action=".module_wiki::_ACTION_EDIT_ARTICLE."&articleid=".$this->fields['id']."&wce_mode=edit&readonly=0&adminedit=1&lang=".$_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang']."&resize=1"); ?>");
                <?
            }
            ?>
            contenerWidth = $('div.content_arbo').css('width');
            topDisplay = leftDisplay = false;
            if($('div.menu_principal').is(':visible')){
                $('div.menu_principal').hide();
                topDisplay = true;
            }
            if($('div.arborescence').is(':visible')){
                $('div.arborescence').hide();
                leftDisplay = true;
            }
            $('div.repli_depli').hide();
            $('div.title_h2').hide();
            $('div.sous_rubrique').hide();
            $('div.arbo_repli').hide();
            $('div.sous_rubrique').hide();
            $('div.wce_article_info_sup').hide();
            $('div.content_arbo').css('width', '100%');


            oldSize = $("div#page_content div.content_arbo #wce_frame_editor").height();
            var height = $(window).height()-$('div#top').height()-diffHeight;
            var iframeHeight = height/2;
            var origin1 = iframeHeight;
            var origin2 = iframeHeight;
            var url = "<? echo module_wce::get_url(module_wce::_SUB_SITE)."&action=".module_wce::_PREVIEW_ART."&articleid=".$this->fields['id']."&wce_mode=render&readonly=0&adminedit=1&resize=1&lang="; ?>";

            $("div#page_content div.content_arbo #wce_frame_editor").attr('height',iframeHeight).css({'border':'0px', 'width':'100%', 'height': iframeHeight+'px', 'margin':'0px','padding':'0px'});
            var content = ' <div class="iframe_separator">\
                                <div class="label" style="float:left;">\
                                    <? echo $_SESSION['cste']['_LIST_LANGUAGES']; ?>&nbsp;:&nbsp;\
                                    <select onchange="javascript:$(\'div#page_content div.content_arbo iframe#wce_frame_lang\').attr(\'src\',\'<? echo module_wce::get_url(module_wce::_SUB_SITE)."&action=".module_wce::_PREVIEW_ART."&articleid=".$this->fields['id']."&wce_mode=render&readonly=0&adminedit=1&lang="; ?>\'+$(this).val());">\
                                        <?
                                        $PersoLang = 0;
                                        foreach($this->getListArticleLangVersion() as $lang){
                                            if($lang->fields['id'] != $_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang']){
                                                $sel = ($lang->fields['id'] == $_SESSION['dims']['wce_default_lg'])?"selected=true":"";
                                                echo '<option value="'.$lang->fields['id'].'" '.$sel.'>'.$lang->fields['label'].'</option>';
                                                if($PersoLang == 0)
                                                    $PersoLang = $lang->fields['id'];
                                            }
                                        }
                                        ?>
                                    </select>\
                                </div>\
                                <div class="ui-state-default" style="float:left;width:17px; height:17px; margin-top:3px;position: fixed;">\
                                    <span class="ui-icon ui-icon-arrowthick-2-n-s" style="cursor: row-resize; height:20px;width:20px;">&nbsp;</span>\
                                </div>\
                                <img onclick="javascript:closeTradMode();" src="<? echo module_wce::getTemplateWebPath('/gfx/icon_mini_suppr.png'); ?>" alt="<? echo $_SESSION['cste']['_DIMS_CLOSE']; ?>" title="<? echo $_SESSION['cste']['_DIMS_CLOSE']; ?>" style="cursor:pointer;float:right;margin-top: 4px;" />\
                            </div>\
                            <iframe src="'+url+'<? echo $PersoLang; ?>" style="border:0;width:100%;height:'+iframeHeight+'px;margin:0px;padding:0px;" id="wce_frame_lang"></iframe>';
            $("div#page_content div.content_arbo").append(content);
            $("div#page_content div.content_arbo div.iframe_separator div.ui-state-default").css('margin-left',(($("div#page_content div.content_arbo div.iframe_separator").width()/2)-$("div#page_content div.contener div.iframe_separator div.label").width())+"px");
            $(window).resize(function(){
                var height2 = $(window).height()-$('div#top').height()-diffHeight;
                var iframeHeight2 = (height2*origin1)/height;
                origin1 = iframeHeight2;
                origin2 = height2-iframeHeight2;
                $("div#page_content div.content_arbo iframe#wce_frame_editor").attr('height',origin1).css({'border':'0px', 'width':'100%', 'height': origin1+'px', 'margin':'0px','padding':'0px'});
                $("div#page_content div.content_arbo iframe#wce_frame_lang").attr('height',origin2).css({'border':'0px', 'width':'100%', 'height': origin2+'px', 'margin':'0px','padding':'0px'});
                $("div#page_content div.content_arbo div.iframe_separator div.ui-state-default").css('margin-left',(($("div#page_content div.content_arbo div.iframe_separator").width()/2)-$("div#page_content div.contener div.iframe_separator div.label").width())+"px");
                height = $(window).height()-$('div#top').height()-diffHeight;
            });
            var clicking = -1;
            $("div#page_content div.content_arbo div.iframe_separator div.ui-state-default").mousedown(function(event){
                clicking = event.pageY;
                origin1 = $("div#page_content div.content_arbo iframe#wce_frame_editor").height();
                origin2 = $("div#page_content div.content_arbo iframe#wce_frame_lang").height();
            });
            $("body").mouseup(function(event){
                if(clicking >= 0){
                    clicking = -1;
                    origin1 = $("div#page_content div.content_arbo iframe#wce_frame_editor").height();
                    origin2 = $("div#page_content div.content_arbo iframe#wce_frame_lang").height();
                }
            }).mousemove(function(event){
                if(clicking >= 0){
                    var diff = event.pageY-clicking;
                    var height1 = origin1+diff;
                    $("div#page_content div.content_arbo iframe#wce_frame_editor").attr('height',height1).css({'border':'0px', 'width':'100%', 'height': height1+'px', 'margin':'0px','padding':'0px'});
                    var height2 = origin2-diff;
                    $("div#page_content div.content_arbo iframe#wce_frame_lang").attr('height',height2).css({'border':'0px', 'width':'100%', 'height': height2+'px', 'margin':'0px','padding':'0px'});
                }
            }).mouseout(function(event){
                if(clicking >= 0){
                    var diff = event.pageY-clicking;
                    var height1 = origin1+diff;
                    $("div#page_content div.content_arbo iframe#wce_frame_editor").attr('height',height1).css({'border':'0px', 'width':'100%', 'height': height1+'px', 'margin':'0px','padding':'0px'});
                    var height2 = origin2-diff;
                    $("div#page_content div.content_arbo iframe#wce_frame_lang").attr('height',height2).css({'border':'0px', 'width':'100%', 'height': height2+'px', 'margin':'0px','padding':'0px'});
                }
            });
            $("div#page_content div.content_arbo iframe").mouseup(function(event){ // pour IE & Safari
                if(clicking >= 0){
                    clicking = -1;
                    origin1 = $("div#page_content div.content_arbo iframe#wce_frame_editor").height();
                    origin2 = $("div#page_content div.content_arbo iframe#wce_frame_lang").height();
                }
            });
            $("div#page_content div.content_arbo div.iframe_separator div.ui-state-default").mousedown(function(event){
                clicking = event.pageY;
                origin1 = $("div#page_content div.content_arbo iframe#wce_frame_editor").height();
                origin2 = $("div#page_content div.content_arbo iframe#wce_frame_lang").height();
            });
        }
        function closeTradMode(){
            $("div#page_content div.content_arbo div.iframe_separator").remove();
            $("div#page_content div.content_arbo iframe#wce_frame_lang").remove();
            if(oldUrl != '' && oldUrl != $("div#page_content div.content_arbo #wce_frame_editor").attr('src'))
                $("div#page_content div.content_arbo #wce_frame_editor").attr('height',oldSize).css({'border':'0px', 'width':'100%', 'height': oldSize+'px', 'margin':'0px','padding':'0px'}).attr('src',oldUrl);
            else
                $("div#page_content div.content_arbo #wce_frame_editor").attr('height',oldSize).css({'border':'0px', 'width':'100%', 'height': oldSize+'px', 'margin':'0px','padding':'0px'});
            $('div.header').show();
            $('div.header_article').show();
            if(topDisplay)
                $('div.menu_principal').show();
            if(leftDisplay)
                $('div.arborescence').show();
            $('div.repli_depli').show();
            $('div.title_h2').show();
            $('div.sous_rubrique').show();
            $('div.arbo_repli').show();
            $('div.sous_rubrique').show();
            $('div.wce_article_info_sup').show();
            $('div.content_arbo').css('width', contenerWidth);
        }
        <?
    }
    ?>
</script>
