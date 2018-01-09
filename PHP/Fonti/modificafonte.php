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
		$id=$_GET['id'];
		$nomef=$_POST["nome"];
		$descf=$_POST["desc"];
		$timestampf=$_POST["timestamp"];
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
				<h2>Errore nella modifica della fonte</h2>
END;
			if($errors>1){
echo<<<END

				<p>Non sono stati inseriti correttamente i campi 'Nome' e 'Descrizione'. <a class="link-color-pers" href="$absurl/Fonti/modificafonte.php?id=$id">Riprova</a>.</p>
END;
			}
			else{
				if($nomef==null){
echo<<<END

				<p>Non è stato inserito correttamente il campo 'Nome'. <a class="link-color-pers" href="$absurl/Fonti/modificafonte.php?id=$id">Riprova</a>.</p>
END;
				}
				else{
echo<<<END

				<p>Non è stato inserito correttamente il campo 'Descrizione'. <a class="link-color-pers" href="$absurl/Fonti/modificafonte.php?id=$id">Riprova</a>.</p>
END;
				}
			}
		}
		else{
            $conn=sql_conn();
			$nomef=mysqli_escape_string($conn, $nomef);
			$descf=mysqli_escape_string($conn, $descf);
			$timestamp_query="SELECT f.Time
							  FROM Fonti f
							  WHERE f.CodAuto='$id'";
			$timestamp_query=mysqli_query($conn,$timestamp_query)or fail("Query fallita: ".mysqli_error($conn));
			if($row=mysqli_fetch_row($timestamp_query)){
				$timestamp_db=$row[0];
				$timestamp_db=strtotime($timestamp_db);
				if($timestampf<$timestamp_db){
					$title="Errore";
					startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Errore nella modifica:</h2>
				<p>La fonte è stata modificata da un altro utente; <a class="link-color-pers" href="$absurl/Fonti/modificafonte.php?id=$id">ottieni i dati aggiornati e riprova</a>.</p>
END;
				}
				else{
					$query="CALL modifyFonte('$id','$nomef','$descf');";
					$query=mysqli_query($conn,$query) or fail("Query fallita: ".mysqli_error($conn));
					$title="Fonte Modificata";
					startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Operazione effettuata</h2>
				<p>La fonte è stata modificata con successo.</p>
				<p><a class="link-color-pers" href="$absurl/Fonti/fonti.php">Torna a Fonti</a>.</p>
END;
				}
			}
			else{
				$title="Errore";
				startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Errore nella modifica:</h2>
				<p>La fonte è stata eliminata da un altro utente.</p>
				<p><a class="link-color-pers" href="$absurl/Fonti/fonti.php">Torna a Fonti</a>.</p>
END;
			}
		}
	}
	else{
		$id=$_GET['id'];
        $conn=sql_conn();
		$id=mysqli_escape_string($conn, $id);
		$query="SELECT f.CodAuto, f.IdFonte, f.Nome, f.Descrizione, f.Time
				FROM Fonti f
				WHERE f.CodAuto='$id'";
		$fonte=mysqli_query($conn,$query) or fail("Query fallita: ".mysqli_error($conn));
		$timestamp=time();
		$row=mysqli_fetch_row($fonte);
		if($row[0]==$id){
			$title="Modifica Fonte - $row[1]";
			startpage_builder($title);
echo<<<END

			<div id="content">
				<h2>Modifica - $row[1]</h2>
				<div id="form">
					<form action="$absurl/Fonti/modificafonte.php?id=$id" method="post">
						<fieldset>
							<p>
								<label for="nome">Nome*:</label>
								<input type="text" id="nome" name="nome" maxlength="20" value="$row[2]"/>
							</p>
							<p>
								<label for="desc">Descrizione*:</label>
								<textarea rows="2" cols="0" id="desc" name="desc" maxlength="10000">$row[3]</textarea>
							</p>
							<input type="hidden" id="timestamp" name="timestamp" value="$timestamp" />
							<p>
								<input type="submit" id="submit" name="submit" value="Modifica" />
								<input type="reset" id="reset" name="reset" value="Cancella" />
							</p>
						</fieldset>
					</form>
				</div>
END;
		}
		else{
			$title="Modifica Fonte - Fonte Non Trovata";
			startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Errore</h2>
				<p>La fonte con id "$id" non è presente nel database.</p>
				<p><a class="link-color-pers" href="$absurl/Fonti/fonti.php">Torna a Fonti</a>.</p>
END;
		}
	}
echo<<<END

			</div>
END;
	endpage_builder();
}
?>