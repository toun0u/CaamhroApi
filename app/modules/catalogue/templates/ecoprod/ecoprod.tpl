<div class="home-right-box-offer">
    <h2>Les produits écolos</h2>
    <div class="content">
        <div id="slideshow_eco">
            <div id="slidesContainer_eco">
                {foreach from=$ecoprod item=art_ecoprod name=art_ecoprod}
                    <div class="slide_eco">
                        {include file='slide_accueil.tpl' article=$art_ecoprod}
                    </div>
                {/foreach}
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    {literal}
    var currentPosition_eco = 0;
    var slideWidth_eco = 136;
    var slides = jQuery('.slide_eco');
    var numberOfSlides = slides.length;

    // Remove scrollbar in JS
    jQuery('#slidesContainer_eco').css('overflow', 'hidden');

    // Wrap all .slides with #slideInner div
    slides
      .wrapAll('<div id="slideInner_eco"></div>')
      // Float left to display horizontally, readjust .slides width
      .css({
        'float' : 'left',
        'width' : slideWidth_eco
      });

    // Set #slideInner width equal to total width of all slides
    jQuery('#slideInner_eco').css('width', slideWidth_eco * numberOfSlides);

    // Insert controls in the DOM
    jQuery('#slideshow_eco')
      .prepend('<span class="control_eco" id="leftControl_eco">Clicking moves left</span>')
      .append('<span class="control_eco" id="rightControl_eco">Clicking moves right</span>');

    // Hide left arrow control on first load
    manageControls_eco(currentPosition_eco);

    // Create event listeners for .controls clicks
    jQuery('.control_eco')
      .bind('click', function(){
      // Determine new position
      currentPosition_eco = (jQuery(this).attr('id')=='rightControl_eco') ? currentPosition_eco+1 : currentPosition_eco-1;

      // Hide / show controls
      manageControls_eco(currentPosition_eco);
      // Move slideInner using margin-left
      jQuery('#slideInner_eco').animate({
        'marginLeft' : slideWidth_eco*(-currentPosition_eco)
      });
    });

    // manageControls: Hides and Shows controls depending on currentPosition
    function manageControls_eco(position){
      // Hide left arrow if position is first slide
      if(position==0){ jQuery('#leftControl_eco').css('visibility', 'hidden') } else{ jQuery('#leftControl_eco').css('visibility', 'visible') }
      // Hide right arrow if position is last slide
      if(position==numberOfSlides-1){ jQuery('#rightControl_eco').css('visibility', 'hidden') } else{ jQuery('#rightControl_eco').css('visibility', 'visible') }
    }
    {/literal}
</script>
