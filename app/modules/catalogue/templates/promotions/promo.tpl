<div class="background_promos_entete">
  <h2 class="bloc_promo">{$bloc_label}</h2>
  <div class="background_promos_cadre">
    <div class="home-right-box-offer">
        <div class="content">
            <div id="slideshow_{$slider.id}" class="slideshow">
                <div id="slidesContainer_{$slider.id}" class="slidesContainer">
                    {foreach from=$articles item=art_slide name=art_slide}
                        <div class="slide_{$slider.id} slide">
                            {include file='slide_promos.tpl' article=$art_slide}
                        </div>
                    {/foreach}
                </div>
            </div>
        </div>
    </div>
  </div>
</div>

<script type="text/javascript">
    var currentPosition_{$slider.id} = 0;
    var slideWidth_{$slider.id} = 281;
    var slides = jQuery('.slide_{$slider.id}');
    var numberOfSlides{$slider.id} = slides.length;

    // Remove scrollbar in JS
    jQuery('#slidesContainer_{$slider.id}').css('overflow', 'hidden');

    // Wrap all .slides with #slideInner div
    slides
      .wrapAll('<div id="slideInner_{$slider.id}"></div>')
      // Float left to display horizontally, readjust .slides width
      .css({ldelim}
        'float' : 'left',
        'width' : slideWidth_{$slider.id}
      {rdelim});

    // Set #slideInner width equal to total width of all slides
    jQuery('#slideInner_{$slider.id}').css({ldelim}'width': slideWidth_{$slider.id} * numberOfSlides{$slider.id},'height': '100%'{rdelim});

    // Insert controls in the DOM
    jQuery('#slideshow_{$slider.id}')
      .prepend('<span class="control_{$slider.id} control_slide left" id="leftControl_{$slider.id}" style="background-color:#805F94"></span>')
      .append('<span class="control_{$slider.id} control_slide right" id="rightControl_{$slider.id}" style="background-color:#805F94"></span>');

    // Hide left arrow control on first load
    manageControls_{$slider.id}(currentPosition_{$slider.id});

    // Create event listeners for .controls clicks
    jQuery('.control_{$slider.id}')
      .bind('click', function(){ldelim}
      // Determine new position
      currentPosition_{$slider.id} = (jQuery(this).attr('id')=='rightControl_{$slider.id}') ? currentPosition_{$slider.id}+1 : currentPosition_{$slider.id}-1;

      // Hide / show controls
      manageControls_{$slider.id}(currentPosition_{$slider.id});
      // Move slideInner using margin-left
      jQuery('#slideInner_{$slider.id}').animate({ldelim}
        'marginLeft' : slideWidth_{$slider.id}*(-currentPosition_{$slider.id})
      {rdelim});
    {rdelim});

    // manageControls: Hides and Shows controls depending on currentPosition
    function manageControls_{$slider.id}(position){ldelim}
      // Hide left arrow if position is first slide
      if(position==0){ldelim} jQuery('#leftControl_{$slider.id}').css('visibility', 'hidden') {rdelim} else{ldelim} jQuery('#leftControl_{$slider.id}').css('visibility', 'visible') {rdelim}
      // Hide right arrow if position is last slide
      if(position==numberOfSlides{$slider.id}-1){ldelim} jQuery('#rightControl_{$slider.id}').css('visibility', 'hidden') {rdelim} else{ldelim} jQuery('#rightControl_{$slider.id}').css('visibility', 'visible') {rdelim}
    {rdelim}
</script>
