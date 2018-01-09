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
    $conn=sql_conn();
	$cl=$_GET['cl'];
	$cl=mysqli_escape_string($conn, $cl);
	$query="SELECT c.CodAuto, c.PrefixNome
			FROM Classe c
			WHERE c.CodAuto='$cl'";
	$classe=mysqli_query($conn,$query) or fail("Query fallita: ".mysqli_error($conn));
	$row_cl=mysqli_fetch_row($classe);
	if($row_cl[0]==$cl){
		$query="SELECT a.CodAuto, a.AccessMod, a.Nome, a.Tipo, a.Descrizione
				FROM Attributo a
				WHERE a.Classe='$cl'
				ORDER BY a.Nome";
		$attr=mysqli_query($conn,$query) or fail("Query fallita: ".mysqli_error($conn));
		$title="$row_cl[1] - Attributi";
		startpage_builder($title);
echo<<<END

			<div id="content">
				<h2>$row_cl[1] -  Attributi</h2>
				<div class="widget-area-left secondary" role="complementary">
					<aside id="export" class="widget">
						<h4 class="widget-title">Link Utili</h4>
						<ul>
							<li><a class="link-color-pers" href="$absurl/Classi/classi.php">Torna a Tabella Classi</a></li>
						</ul>
					</aside>
				</div>
				<div class="widget-area-right secondary" role="complementary">
					<aside id="operations" class="widget">
						<h4 class="widget-title">Operazioni</h4>
						<ul>
							<li><a class="link-color-pers" href="$absurl/Classi/Attributi/inserisciattributo.php?cl=$cl">Inserisci Attributo</a></li>
						</ul>
					</aside>
				</div>
				<table>
					<thead>
						<tr>
							<th>Accessibilità</th>
							<th>Nome</th>
							<th>Tipo</th>
							<th>Descrizione</th>
							<th>Operazioni</th>
						</tr>
					</thead>
					<tbody>
END;
		while($row=mysqli_fetch_row($attr)){
echo<<<END

						<tr>
END;
			attr_table($row);
echo<<<END

							<td>
								<ul>
									<li><a class="link-color-pers" href="$absurl/Classi/Attributi/modificaattributo.php?id=$row[0]">Modifica</a></li>
									<li><a class="link-color-pers" href="$absurl/Classi/Attributi/eliminaattributo.php?id=$row[0]">Elimina</a></li>
								</ul>
							</td>
						</tr>
END;
		}
echo<<<END

					</tbody>
				</table>
END;
	}
	else{
		$title="Attributi - Classe Non Trovata";
		startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Errore</h2>
				<p>La classe con id "$cl" non è presente nel database.</p>
END;
	}
echo<<<END

			</div>
END;
	endpage_builder();
}
?>