<?php
$form = new Dims\form(array(
    'name'              => 'filtre',
    'include_actions'   => false
));
$Already = $this->get('already_doc');
foreach($this->get('lstArticles') as $article){
    $label = $article->getLabel();
    foreach($article->getThumbnails($Already) as $thumb){
        $doc = $thumb->getDocFile();
        ?>
        <div class="sel_thumb">
            <?php
            echo $form->radio_field(array(
                        'name'      => 'thumbnails',
                        'id'        => 'thumb_'.$thumb->fields['id_article']."-".$thumb->fields['id_doc'],
                        'value'     => $thumb->fields['id_article']."-".$thumb->fields['id_doc'],
                        'additionnal_attributes' => 'style="display:none;"'
                    ));
            ?>
            <img ref="<?= $thumb->fields['id_article']."-".$thumb->fields['id_doc']; ?>" src="<?= $doc->getThumbnail(150); ?>" alt="<?= $label." - ".$doc->fields['name']; ?>" title="<?= $label." - ".$doc->fields['name']; ?>" />
        </div>
        <?php
    }
}
?>
<script type="text/javascript">
    $(document).ready(function(){
        $("div.sel_thumb").click(function(){
            if(!$(this).hasClass('selected')){
                $("div.sel_thumb").removeClass('selected');
                $("div.sel_thumb input").removeAttr('checked');
                $(this).toggleClass('selected');
                $("input",$(this)).attr('checked',!$("input",$(this)).is(":checked"));
            }
        });
    });
</script>