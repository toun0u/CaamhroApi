<?php
require_once DIMS_APP_PATH."modules/wce/include/classes/class_article_tags.php";
?>
<ul>
<?
foreach(article_tags::getTags() as $tag){
	?>
	<span class="cloud<?echo $tag->fields['indice'];?>">
		<?
		echo $tag->fields['tag'];
		?>
		</span>>
	<?
}
?>
</ul>
<?
?>
