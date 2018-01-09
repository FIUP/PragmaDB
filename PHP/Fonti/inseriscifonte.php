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
		$err_desc=false;
		$errors=0;
		if($nomef==null){
			$err_nome=true;
			$errors++;
		}
		if($descf==null){
			$err_desc=true;
			$errors++;
		}
		if($errors>0){
			$title="Errore";
			startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Errore nell'inserimento della fonte</h2>
END;
			if($errors>1){
echo<<<END

				<p>Non sono stati inseriti correttamente i campi 'Nome' e 'Descrizione'. <a class="link-color-pers" href="$absurl/Fonti/inseriscifonte.php">Riprova</a>.</p>
			</div>
END;
			}
			else{
				if($nomef==null){
echo<<<END

				<p>Non è stato inserito correttamente il campo 'Nome'. <a class="link-color-pers" href="$absurl/Fonti/inseriscifonte.php">Riprova</a>.</p>
			</div>
END;
				}
				else{
echo<<<END

				<p>Non è stato inserito correttamente il campo 'Descrizione'. <a class="link-color-pers" href="$absurl/Fonti/inseriscifonte.php">Riprova</a>.</p>
			</div>
END;
				}
			}
		}
		else{
            $conn=sql_conn();
			$nomef=mysqli_escape_string($conn, $nomef);
			$descf=mysqli_escape_string($conn, $descf);
			$query="CALL insertFonte('$nomef','$descf');";
			$query=mysqli_query($conn,$query) or fail("Query fallita: ".mysqli_error($conn));
			$title="Fonte Inserita";
			startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Operazione effettuata</h2>
				<p>La fonte è stata inserita con successo.</p>
				<p><a class="link-color-pers" href="$absurl/Fonti/fonti.php">Torna a Fonti</a>.</p>
			</div>
END;
		}
	}
	else{
		$title="Inserisci Fonte";
		startpage_builder($title);
echo<<<END

			<div id="content">
				<h2>Inserisci Fonte</h2>
				<div id="form">
					<form action="$absurl/Fonti/inseriscifonte.php" method="post">
						<fieldset>
							<p>
								<label for="nome">Nome*:</label>
								<input type="text" id="nome" name="nome" maxlength="10000" />
							</p>
							<p>
								<label for="desc">Descrizione*:</label>
								<textarea rows="2" cols="0" id="desc" name="desc" maxlength="200"></textarea>
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