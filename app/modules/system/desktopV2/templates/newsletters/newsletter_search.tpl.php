<?
$dims = dims::getInstance();

$lstNewsletters = $desktop->getSearchNewsletters($_SESSION['dims']['mynewsletters']);
?>

<div class="zone_generic">
	<?

        foreach ($lstNewsletters as $new){
                $new->setLightAttribute('from_desktop', true);
                $new->setLightAttribute('id_fiche',$_SESSION['dims']['currentobject_newsletter']);
                $new->display(_DESKTOP_TPL_LOCAL_PATH.'/newsletters/newsletter_elem.tpl.php');
        }
        ?>
</div>
