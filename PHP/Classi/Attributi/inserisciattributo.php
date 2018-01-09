<?php

require('../../Functions/mysql_fun.php');
require('../../Functions/page_builder.php');
require('../../Functions/urlLab.php'); 

session_start();

date_default_timezone_set("Europe/Rome");

$absurl=urlbasesito();

if(empty($_SESSION['user'])){
	header("Location: $absurl/error.php");
}
else{
	if(isset($_REQUEST['submit'])){
		$cl=$_GET['cl'];
		if(isset($_POST["acc"])){
			$accf=$_POST["acc"];
		}
		$nomef=$_POST["nome"];
		$tipof=$_POST["tipo"];
		$descf=$_POST["desc"];
		$timestampf=$_POST["timestamp"];
		$err_acc=false;
		$err_nome=false;
		$err_tipo=false;
		$err_desc=false;
		$err_pres=false;
		$errors=0;
		if(!(isset($accf))){
			$err_acc=true;
			$errors++;
		}
		if($nomef==null){
			$err_nome=true;
			$errors++;
		}
		if($tipof==null){
			$err_tipo=true;
			$errors++;
		}
		if($descf==null){
			$err_desc=true;
			$errors++;
		}
        $conn=sql_conn();
		if(isset($accf)){
			$accf=mysqli_escape_string($conn,$accf);
		}
		$nomef=mysqli_escape_string($conn, $nomef);
		$tipof=mysqli_escape_string($conn,$tipof);
		$descf=mysqli_escape_string($conn, $descf);
		$query="SELECT a.CodAuto
				FROM Attributo a
				WHERE a.Nome='$nomef' AND a.Classe='$cl'";
		$pres=mysqli_query($conn,$query) or fail("Query fallita: ".mysqli_error($conn));
		$pres=mysqli_fetch_row($pres);
		if($pres[0]!=null){
			$err_pres=true;
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
			if($err_acc){
echo<<<END

					<li>Accessibilità: NON INDICATA</li>
END;
			}
			if($err_nome){
echo<<<END

					<li>Nome: NON INSERITO</li>
END;
			}
			if($err_tipo){
echo<<<END

					<li>Tipo: NON INSERITO</li>
END;
			}
			if($err_desc){
echo<<<END

					<li>Descrizione: NON INSERITA</li>
END;
			}
			if($err_pres){
echo<<<END

					<li>L'ATTRIBUTO E' GIA' PRESENTE NEL DB!</li>
END;
			}
echo<<<END

				</ul>
				<p><a class="link-color-pers" href="$absurl/Classi/Attributi/inserisciattributo.php?cl=$cl">Riprova</a>.</p>
END;
		}
		else{
			$timestamp_query="SELECT c.Time
							  FROM Classe c
							  WHERE c.CodAuto='$cl'";
			$timestamp_query=mysqli_query($conn,$timestamp_query)or fail("Query fallita: ".mysqli_error($conn));
			if($row=mysqli_fetch_row($timestamp_query)){
				$timestamp_db=$row[0];
				$timestamp_db=strtotime($timestamp_db);
				if($timestampf<$timestamp_db){
					$title="Errore";
					startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Errore nell'inserimento:</h2>
				<p>La classe contenitore è stata modificata da un altro utente; <a class="link-color-pers" href="$absurl/Classi/Attributi/attributi.php?cl=$cl">ottieni i dati aggiornati e riprova</a>.</p>
END;
				}
				else{
					$query="CALL insertAttributo('$accf','$nomef','$tipof','$descf','$cl')";
					$query=mysqli_query($conn,$query) or fail("Query fallita: ".mysqli_error($conn));
					$title="Attributo Inserito";
					startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Operazione effettuata</h2>
				<p>L'attributo è stato inserito con successo.</p>
				<p><a class="link-color-pers" href="$absurl/Classi/Attributi/attributi.php?cl=$cl">Torna ad Attributi</a>.</p>
END;
				}
			}
			else{
				$title="Errore";
				startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Errore nell'inserimento:</h2>
				<p>La classe contenitore è stata eliminata da un altro utente.</p>
				<p><a class="link-color-pers" href="$absurl/Classi/classi.php">Torna a Classi</a>.</p>
END;
			}
		}
	}
	else{
		$cl=$_GET['cl'];
        $conn=sql_conn();
		$cl=mysqli_escape_string($conn, $cl);
		$query="SELECT c.CodAuto, c.PrefixNome
				FROM Classe c
				WHERE c.CodAuto='$cl'";
		$classe=mysqli_query($conn,$query) or fail("Query fallita: ".mysqli_error($conn));
		$timestamp=time();
		$row_cl=mysqli_fetch_row($classe);
		if($row_cl[0]==$cl){
			$title="$row_cl[1] - Inserisci Attributo";
			startpage_builder($title);
echo<<<END

			<div id="content">
				<h2>$row_cl[1] - Inserisci Attributo</h2>
				<div id="form">
					<form action="$absurl/Classi/Attributi/inserisciattributo.php?cl=$cl" method="post">
						<fieldset>
							<p>
								<label for="acc1">Accessibilità*:</label>
								<input type="radio" id="acc1" name="acc" value="-" /> <span class="mancante">- (Private)</span>
								<input type="radio" id="acc2" name="acc" value="#" /> # (Protected)
								<input type="radio" id="acc3" name="acc" value="+" /> <span class="completato">+ (Public)</span>
							</p>
							<p>
								<label for="nome">Nome*:</label>
								<input type="text" id="nome" name="nome" maxlength="800" />
							</p>
							<p>
								<label for="tipo">Tipo*:</label>
								<input type="text" id="tipo" name="tipo" maxlength="800" />
							</p>
							<p>
								<label for="desc">Descrizione*:</label>
								<textarea rows="2" cols="0" id="desc" name="desc" maxlength="10000"></textarea>
							</p>
							<input type="hidden" id="timestamp" name="timestamp" value="$timestamp" />
							<p>
								<input type="submit" id="submit" name="submit" value="Inserisci" />
								<input type="reset" id="reset" name="reset" value="Cancella" />
							</p>
						</fieldset>
					</form>
				</div>
END;
		}
		else{
			$title="Inserisci Attributo - Classe Non Trovata";
			startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Errore</h2>
				<p>La classe con id "$cl" non è presente nel database.</p>
				<p><a class="link-color-pers" href="$absurl/Classi/classi.php">Torna a Classi</a>.</p>
END;
		}
	}
echo<<<END

			</div>
END;
	endpage_builder();
}
?>