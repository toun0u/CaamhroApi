<?
$view = view::getInstance();
$familles = $view->get('finder_familles');
if(count($familles)){
    ?>
    <div style="height: 20px;margin-left: 2%;">
        <div class="finder_ariane"></div>
        <div style="float:right;margin-right:2%;" class="actions_finder">
            <a href="javascript:void(0);" style="text-decoration:none;" onclick="javascript:displayFinder();">
                <?
                if($view->get('display_finder')){
                ?>
                <img src="<? echo $view->getTemplateWebPath("/gfx/masquer16.png"); ?>" title="<? echo dims_constant::getVal('_HIDE'); ?>" alt="<? echo dims_constant::getVal('_HIDE'); ?>" />
                <span style="text-decoration:underline;float:right;line-height:16px;margin-left:5px;">
                    <? echo dims_constant::getVal('_HIDE'); ?>
                </span>
                <?
                }else{
                ?>
                <img src="<? echo $view->getTemplateWebPath("/gfx/afficher16.png"); ?>" title="<? echo dims_constant::getVal('_DISPLAY'); ?>" alt="<? echo dims_constant::getVal('_DISPLAY'); ?>" />
                <span style="text-decoration:underline;float:right;line-height:16px;margin-left:5px;">
                    <? echo dims_constant::getVal('_DISPLAY'); ?>
                </span>
                <?
                }
                ?>
            </a>
        </div>
    </div>
    <div id="recurs_fam" <? echo ($view->get('display_finder'))?'':'style="display:none;"'; ?>>
        <div class="blocfam_interne">
            <?
            if(isset($familles->childrens) && !is_null($familles->childrens)){
                $familles->display(DIMS_APP_PATH."modules/catalogue/admin/views/familles/famille_finder_node.tpl.php");
            }
            ?>
        </div>
    </div>

    <script type="text/javascript">
        function displayFinder(){
            $("div#recurs_fam").slideToggle('slow',function(){
                if($("div.actions_finder img").attr('title') == '<? echo dims_constant::getVal('_DISPLAY'); ?>'){
                    $("div.actions_finder img").attr('src','<? echo $view->getTemplateWebPath("/gfx/masquer16.png"); ?>').attr('title','<? echo dims_constant::getVal('_HIDE'); ?>').attr('alt','<? echo dims_constant::getVal('_HIDE'); ?>');
                    $("div.actions_finder span").html('<? echo dims_constant::getVal('_HIDE'); ?>');
                }else{
                    $("div.actions_finder img").attr('src','<? echo $view->getTemplateWebPath("/gfx/afficher16.png"); ?>').attr('title','<? echo dims_constant::getVal('_DISPLAY'); ?>').attr('alt','<? echo dims_constant::getVal('_DISPLAY'); ?>');
                    $("div.actions_finder span").html('<? echo dims_constant::getVal('_DISPLAY'); ?>');
                }
                $.ajax({
                    type: "POST",
                    url: "<? echo $view->get('url_switch_finder'); ?>"
                });
            });
        }
        var nodeWidth = 250;
        $(document).ready(function(){
            var count = $('div.blocfam_interne').find('div.browser_column').length;
            $('div.blocfam_interne').append('<p style="clear: both; height: 1px;"></p>');
            $('div.blocfam_interne').width(count*nodeWidth + 10);
            $('div#recurs_fam').scrollLeft(count*nodeWidth + 20 );
            if ($('div.blocfam_interne').innerWidth() > $('div#recurs_fam').innerWidth())
                $('div.blocfam_interne div.browser_column:last').css({'border': '0px', 'width': nodeWidth+'px'});

            $('div.blocfam_interne div.browser_column').each(function(){
                var id = $(this).attr('id');
                var sel_item = $('div.blocfam_interne div#'+id+' li.selected');
                $(this).scrollTop((sel_item.attr('rel')-1) *21);
            });

            $(window).resize(function(){
                $('div.blocfam_interne').width(count*nodeWidth + 10);
                if ($('div.blocfam_interne').innerWidth() > $('div#recurs_fam').innerWidth())
                    $('div.blocfam_interne div.browser_column:last').css({'border-right': '0px', 'width': nodeWidth+'px'});
                else
                    $('div.blocfam_interne div.browser_column:last').css({'border-right': '2px solid #D6D6D6', 'width': nodeWidth+'px'});
            });
            var len = $('div.blocfam_interne div.browser_column li.elem.selected').length;
            if(len > 0){
                $('div.finder_ariane').append('<a href="<? echo $view->get('url_finder')."&id=".$familles->get('id'); ?>"><? echo dims_constant::getVal('_DOC_ROOT'); ?></a> > ');
                $('div.blocfam_interne div.browser_column li.elem.selected').each(function(index,element){
                    var elem = $("a:first",$(this));
                    if(index == len-1){
                        $('div.finder_ariane').append(elem.attr('title'));
                    }else{
                        $('div.finder_ariane').append('<a href="'+elem.attr('href')+'">'+elem.attr('title')+'</a> > ');
                    }
                });
            }else{
                $('div.finder_ariane').append('<? echo dims_constant::getVal('_DOC_ROOT'); ?>');
            }
        });
    </script>
    <?php
}
?>
