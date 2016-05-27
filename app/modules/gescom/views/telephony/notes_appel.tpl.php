<?php
// Lejal Simon 
//Formulaire de consultation et d'édition d'un depuis le journal d'appel de cheval liberté (gescom)

// Récupération des variables des vars post
$resume = dims_load_securvalue('resume', dims_const::_DIMS_CHAR_INPUT, true, true);
$ref = dims_load_securvalue('ref', dims_const::_DIMS_CHAR_INPUT, true, true);
$new_resume = dims_load_securvalue('new_resume', dims_const::_DIMS_CHAR_INPUT, true, true);
$event = dims_load_securvalue('event', dims_const::_DIMS_CHAR_INPUT, true, true);

	// Si le formulaire a été rempli
	if(isset($new_resume)&&($new_resume!='')){
		
	?>
		<!-- On update la bdd -->
		<script type="text/javascript">
			var ref = '<?php echo ($ref); ?>';
			var res = '<?php echo ($new_resume); ?>';
			$.ajax({
				type: 'POST',
				url: "/admin-light.php?dims_op=telephony&todo_op=update_resume&ajax=1",
				data: {"ref":ref, "res":res},
				dataType: "html",
				async:false
			});
		</script>

	<?
	    echo "<h4> Résumé modifié avec succès!</h4></br>"; 
		echo '<a href="'.Gescom\get_path(array('c'=>'telephony', 'a'=>'index')).'">Retour</a>';
	}else{
		// On affiche le formulaire
		echo '<h4>Resumé de l\'appel : </h4><br/>
			  <form name="form_business_c"  method="post" href="'.Gescom\get_path(array('c'=>"telephony", 'a'=>"notes_appel")).'">
					<textarea name="new_resume">';
						echo $resume;
		echo		'</textarea>
				<input name="update" type="submit" value="Modifier" />
				</form>
			</br></br>';
		if($event=='NORMAL_OUT_CALL'){
			//echo '<h5>Resumé audio de l\'appel : </h5><br/>';
			?>
				<!-- Audio en DVLP -->
				<!-- <audio controls> -->
		   			<!-- <source src="/home/stagiaire2/Téléchargements/prix.mp3"></source> -->
				<!-- </audio>	 	 -->
			<?
			echo '</br>';	
		}
	
		echo '</br></br><a href="'.Gescom\get_path(array('c'=>"telephony", 'a'=>"index")).'"> Retour</a>';

	}

?>

<!-- css responsive-->
<style>
	
	input{
		display : inline;
		min-width: 160px;
		margin: 15px;
		padding: 10px;
	}

	select{
		padding: 10px;
	}

	textarea{
		display : block;
		min-width: 900px;
	}

	input[type=submit]{
		margin: 0px;
		margin-top: 15px;
		min-width: 180px;
		border-radius: 5px;
	}

	/*responsive*/
	@media screen and (max-width: 1300px){
		
		select{
			padding: 5px;
			margin: 0;
			margin-bottom: 10px;
			min-width: 20%;
		}

		input{
			display : block ;
			margin: 0;
			margin-bottom: 10px;
			padding: 5px;
			min-width: 40%;
		}

		textarea{
			min-width: 40%;
			margin-bottom: 10px;
			min-height: 100px;
		}

		input[type=submit]{
			min-width: 30%;
		}

	}
</style>