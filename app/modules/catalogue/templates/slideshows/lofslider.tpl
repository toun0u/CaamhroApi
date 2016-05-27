<script type="text/javascript" src="/templates/frontoffice/unifob/js/mootools.svn.js"></script>
<script type="text/javascript" src="/templates/frontoffice/unifob/js/lofslidernews.mt11.js"></script>
<div id="lofslidecontent_{$slideshow.id}" class="lof-slidecontent">
    <div class="preload"><div></div></div>
    <div class="lof-main-wapper">
        {foreach from=$slideshow.slide item=slide}
            <div class="lof-main-item">
                <a href="{$slide.lien}">
                    <img border="0" alt="recherche" src="/{$slide.image}">
                </a>
                <div class="lof-main-item-desc">
                    <h3><a target="_parent" title="{$slide.titre}" href="{$slide.lien}">{$slide.titre}</a></h3>
                 </div>
            </div>
        {/foreach}
    </div>

    <div class="lof-navigator-outer">
        <ul class="lof-navigator">
            {foreach from=$slideshow.slide item=slide}
                <li>
                    <div>
                        <img border="0" alt="recherche" src="/{$slide.miniature}" height="300" width="900">
                        <h3>{$slide.titre}</h3>
                        {$slide.descr_courte}
                    </div>
                </li>
            {/foreach}
        </ul>
    </div>
</div>
<link rel="stylesheet" type="text/css" href="/modules/catalogue/templates/slideshows/lofslider.css" media="screen" />
<script type="text/javascript">
    var _lofmain =  $('lofslidecontent_{$slideshow.id}');
    {literal}
    var _lofscmain = _lofmain.getElement('.lof-main-wapper');
    var _lofnavigator = _lofmain.getElement('.lof-navigator-outer .lof-navigator');
    var object = new LofFlashContent( _lofscmain,
                                      _lofnavigator,
                                      _lofmain.getElement('.lof-navigator-outer'),
                                      { fxObject:{ transition:Fx.Transitions.Quad.easeInOut,  duration:800},
                                         interval:3000,
                                         direction:'opacity' } );
    object.start( true, _lofmain.getElement('.preload') );
    {/literal}
</script>
