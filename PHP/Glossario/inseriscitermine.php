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
		$identificativof=$_POST["identificativo"];
		$namef=$_POST["name"];
		$descf=$_POST["desc"];
		$firstf=$_POST["first"];
		$firstpluralf=$_POST["firstplural"];
		$textf=$_POST["text"];
		$pluralf=$_POST["plural"];
		$err_identificativo=false;
		$err_identificativo_special=false;
		$err_name=false;
		$err_desc=false;
		$errors=0;
		if($identificativof==null){
			$err_identificativo=true;
			$errors++;
		}
		if(preg_match('/[^a-z]/i', $identificativof)>0){
			$err_identificativo_special=true;
			$errors++;
		}
		if($namef==null){
			$err_name=true;
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
				<h2>Errore nell'inserimento dei seguenti campi:</h2>
				<ul>
END;
			if($err_identificativo){
echo<<<END

					<li>Identificativo: NON INDICATO</li>
END;
			}
			elseif($err_identificativo_special){
echo<<<END

					<li>Identificativo: DEVE CONTENERE SOLO CARATTERI ALFABETICI (DI CUI IL PRIMO MINUSCOLO) NON SEPARATI DA SPAZI</li>
END;
			}
			if($err_name){
echo<<<END

					<li>Name: NON INDICATO</li>
END;
			}
			if($err_desc){
echo<<<END

					<li>Description: NON INDICATA</li>
END;
			}
echo<<<END

				</ul>
				<p><a class="link-color-pers" href="$absurl/Glossario/inseriscitermine.php">Riprova</a>.</p>
			</div>
END;
		}
		else{
            $conn=sql_conn();
			$identificativof=lcfirst($identificativof);
			$identificativof=mysqli_escape_string($conn, $identificativof);
			$namef=mysqli_escape_string($conn, $namef);
			$descf=mysqli_escape_string($conn, $descf);
			$firstf=mysqli_escape_string($conn, $firstf);
			$firstpluralf=mysqli_escape_string($conn, $firstpluralf);
			$textf=mysqli_escape_string($conn, $textf);
			$pluralf=mysqli_escape_string($conn, $pluralf);
			$query="CALL insertGlossario('$identificativof','$namef','$descf',";
			if($firstf==null){
				$query=$query."null,";
			}
			else{
				$query=$query."'$firstf',";
			}
			if($firstpluralf==null){
				$query=$query."null,";
			}
			else{
				$query=$query."'$firstpluralf',";
			}
			if($textf==null){
				$query=$query."null,";
			}
			else{
				$query=$query."'$textf',";
			}
			if($pluralf==null){
				$query=$query."null)";
			}
			else{
				$query=$query."'$pluralf')";
			}
			$query=mysqli_query($conn,$query) or fail("Query fallita: ".mysqli_error($conn));
			$title="Termine Glossario Inserito";
			startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Operazione effettuata</h2>
				<p>Il termine è stato inserito con successo.</p>
				<p><a class="link-color-pers" href="$absurl/Glossario/glossario.php">Torna a Glossario</a>.</p>
			</div>
END;
		}
	}
	else{
		$title="Inserisci Termine Glossario";
		startpage_builder($title);
echo<<<END

			<div id="content">
				<h2>Inserisci Termine Glossario</h2>
				<div id="form">
					<form action="$absurl/Glossario/inseriscitermine.php" method="post">
						<fieldset>
							<p>
								<label for="identificativo">Identificativo*:</label>
								<input type="text" id="identificativo" name="identificativo" maxlength="50" />
							</p>
							<p>
								<label for="nome">Name*:</label>
								<input type="text" id="name" name="name" maxlength="50" />
							</p>
							<p>
								<label for="desc">Description*:</label>
								<textarea rows="2" cols="0" id="desc" name="desc" maxlength="10000"></textarea>
							</p>
							<p>
								<label for="nome">First:</label>
								<input type="text" id="first" name="first" maxlength="50" />
							</p>
							<p>
								<label for="nome">First Plural:</label>
								<input type="text" id="firstplural" name="firstplural" maxlength="50" />
							</p>
							<p>
								<label for="nome">Text:</label>
								<input type="text" id="text" name="text" maxlength="50" />
							</p>
							<p>
								<label for="nome">Plural:</label>
								<input type="text" id="plural" name="plural" maxlength="50" />
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