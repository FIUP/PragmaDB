<?php

require('../Functions/mysql_fun.php');
require('../Functions/page_builder.php');
require('../Functions/urlLab.php');

session_start();

date_default_timezone_set("Europe/Rome");

$absurl=urlbasesito();

if(empty($_SESSION['user'])){
	header("Location: $absurl/error.php");
}
else{
	if(isset($_REQUEST['submit'])){
		$nomef=$_POST["nome"];
		$descf=$_POST["desc"];
		$err_nome=false;
		$errors=0;
		if($nomef==null){
			$err_nome=true;
			$errors++;
		}
		if($errors>0){
			$title="Errore";
			startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Errore nell'inserimento dell'attore</h2>
				<p>Non è stato inserito correttamente il campo 'Nome'. <a class="link-color-pers" href="$absurl/Attori/inserisciattore.php">Riprova</a>.</p>
			</div>
END;
		}
		else{
            $conn=sql_conn();
			$nomef=mysqli_escape_string($conn, $nomef);
			$descf=mysqli_escape_string($conn, $descf);
			$query="CALL insertAttore('$nomef','$descf');";
			$query=mysqli_query($conn,$query) or fail("Query fallita: ".mysqli_error($conn));
			$title="Attore Inserito";
			startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Operazione effettuata</h2>
				<p>L'attore è stato inserito con successo.</p>
				<p><a class="link-color-pers" href="$absurl/Attori/attori.php">Torna a Attori</a>.</p>
			</div>
END;
		}
	}
	else{
		$title="Inserisci Attore";
		startpage_builder($title);
echo<<<END

			<div id="content">
				<h2>Inserisci Attore</h2>
				<div id="form">
					<form action="$absurl/Attori/inserisciattore.php" method="post">
						<fieldset>
							<p>
								<label for="nome">Nome*:</label>
								<input type="text" id="nome" name="nome" maxlength="200" />
							</p>
							<p>
								<label for="desc">Descrizione:</label>
								<textarea rows="2" cols="0" id="desc" name="desc" maxlength="10000"></textarea>
							</p>
							<p>
								<input type="submit" id="submit" name="submit" value="Inserisci" />
								<input type="reset" id="reset" name="reset" value="Cancella" />
							</p>
						</fieldset>
					</form>
				</div>
			</div>
END;
	}
	endpage_builder();
}
?>