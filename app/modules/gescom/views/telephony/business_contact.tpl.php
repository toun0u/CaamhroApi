<?php
// Lejal Simon 
//Formulaire d'ajout d'un business contact depuis le journal d'appel de cheval liberté (gescom)

// Récupération des variables du formulaires ci-dessous
$civil = dims_load_securvalue('civil_bc', dims_const::_DIMS_CHAR_INPUT, true, true); 
$ln = dims_load_securvalue('lastname_bc', dims_const::_DIMS_CHAR_INPUT, true, true); 
$fn = dims_load_securvalue('firstname_bc', dims_const::_DIMS_CHAR_INPUT, true, true); 
$email = dims_load_securvalue('email_bc', dims_const::_DIMS_CHAR_INPUT, true, true); 
$phone = dims_load_securvalue('phone_bc', dims_const::_DIMS_CHAR_INPUT, true, true); 
$comment = dims_load_securvalue('comment_bc', dims_const::_DIMS_CHAR_INPUT, true, true); 

	//on verifie que tous les champs required sont là et non vides
	if($civil!=null && $ln != null && $fn !=null && $phone!=null){
		$contact=new contact();
		$contact->init_description();
		$contact->setugm();
		$contact->set('lastname',$ln);
		$contact->set('firstname',$fn);
		$contact->set('civilite',$civil);
		$contact->set('email',$email);
		$contact->set('mobile',$phone);
		$contact->set('comments',$comment);
		$contact->save();
		echo "<h4> Contact ajouté avec succès!</h4></br>";
?>

<!-- script de maj du contact ajouté dans les logs des appels -->
	<script type="text/javascript">
		var num='<?php echo ($phone); ?>';

		$.ajax({
			type: 'POST',
			url: "/admin-light.php?dims_op=telephony&todo_op=update_contact&ajax=1",
			data: {"number":num},
			dataType: "html",
			sucess:function(data){
				console.log(data);
			}
		});		
	</script>

<?
		echo '<a href="'.Gescom\get_path(array('c'=>'telephony', 'a'=>'index')).'">Retour</a>';
	
	//Sinon on affiche le formulaire
	}else{
		echo '<h4>Nouveau Contact</h4><br/>
			  <form name="form_business_c"  method="post" href="'.Gescom\get_path(array('c'=>'telephony', 'a'=>"form_contact")).'">
					<select name="civil_bc" required="required">
						<option value=""></option>
						<option value="M.">M.</option>
						<option value="Mme">Mme</option>
						<option value="Melle">Melle</option>
					</select>
					<input type="text" name="lastname_bc" placeholder="Nom" required="required">
					<input type="text" name="firstname_bc" placeholder="Prénom" required="required">
					<input type ="email" name="email_bc" placeholder="Email">';	
					//recupere le numéro de la page de log
					$a = dims_load_securvalue('num', dims_const::_DIMS_CHAR_INPUT, true, true); 
					$a="0".substr($a, 2);
					echo '<input type="tel" name="phone_bc" value='.$a.' required="required">
						<textarea name="comment_bc" placeholder="Commentaires..."></textarea>
					<input name="add_bc" type="submit" value="Ajouter" />
				</form>
			</br></br>
			<a href="'.Gescom\get_path(array('c'=>'telephony', 'c'=>"index")).'"> Retour</a>';
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