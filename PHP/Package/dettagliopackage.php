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
	$id=$_GET['id'];
    $conn=sql_conn();
	$id=mysqli_escape_string($conn, $id);
	$query="SELECT p1.CodAuto,p1.PrefixNome,p1.Nome,p1.Descrizione,p2.PrefixNome,p1.UML,p1.Time,p2.CodAuto
			FROM Package p1 LEFT JOIN Package p2 ON p1.Padre=p2.CodAuto
			WHERE p1.CodAuto='$id'"; //query che carica il package di id = $id
	$pack=mysqli_query($conn,$query) or fail("Query fallita: ".mysqli_error($conn));
	$row=mysqli_fetch_row($pack);
	if($row[0]==$id){
		$title="Dettaglio Package - $row[1]";
		startpage_builder($title);
echo<<<END

			<div id="content">
				<h2>Dettaglio - $row[1]</h2>
				<dl class="widget">
END;
		//$heads: header dei campi di $row
		$heads=array('','PrefixNome:','Nome:','Descrizione:','Padre:','Diagramma:','Time:');
		for($i=1;$i<7;$i++){
			//stampo il titolo del campo
echo<<<END

					<dt class="widget-title">$heads[$i]</dt>
END;
			if($row[$i]!=null){
				//se non è nullo
				if($i==4){
					//se è il nome del padre
echo<<<END

					<dd><a class="link-color-pers" href="$absurl/Package/dettagliopackage.php?id=$row[7]">$row[$i]</a></dd>
END;
				}
				else{
echo<<<END

					<dd>$row[$i]</dd>
END;
				}
			}
			else{
				//altrimenti stampo N/D
echo<<<END

					<dd>N/D</dd>
END;
			}
		}
		//------- Stampa sotto package
		$query="SELECT p1.CodAuto,p1.PrefixNome
				FROM Package p1
				WHERE p1.Padre='$id'
				ORDER BY p1.PrefixNome"; //Query che carica i sotto package del package $id
		$subPackages=mysqli_query($conn,$query) or fail("Query fallita: ".mysqli_error($conn));
		$riga = mysqli_fetch_row($subPackages);
		if($riga[0]!=null){
echo<<<END

					<dt class="widget-title">Package Contenuti:</dt>
					<dd><a class="link-color-pers" href="$absurl/Package/dettagliopackage.php?id=$riga[0]">$riga[1]</a></dd>
END;
		}
		while($riga = mysqli_fetch_row($subPackages)){
echo<<<END

					<dd><a class="link-color-pers" href="$absurl/Package/dettagliopackage.php?id=$riga[0]">$riga[1]</a></dd>
END;
		}
		//------- Stampa classi contenute
		$query="SELECT c1.CodAuto,c1.Nome
				FROM Classe c1
				WHERE c1.ContenutaIn='$id'
				ORDER BY c1.Nome"; //Query che carica le classi contenute nel package $id
		$classes=mysqli_query($conn,$query) or fail("Query fallita: ".mysqli_error($conn));
		$riga = mysqli_fetch_row($classes);
		if($riga[0]!=null){
echo<<<END

					<dt class="widget-title">Classi Contenute:</dt>
					<dd><a class="link-color-pers" href="$absurl/Classi/dettaglioclasse.php?id=$riga[0]">$row[1]::$riga[1]</a></dd>
END;
		}
		while($riga = mysqli_fetch_row($classes)){
echo<<<END

					<dd><a class="link-color-pers" href="$absurl/Classi/dettaglioclasse.php?id=$riga[0]">$row[1]::$riga[1]</a></dd>
END;
		}
		//------- Stampa le relazioni con gli altri package
		$query="SELECT p.CodAuto,p.PrefixNome
				FROM RelatedPackage rp JOIN Package p ON rp.Pack2=p.CodAuto
				WHERE rp.Pack1='$id'
				UNION
				SELECT p.CodAuto,p.PrefixNome
				FROM RelatedPackage rp JOIN Package p ON rp.Pack1=p.CodAuto
				WHERE rp.Pack2='$id'
				ORDER BY PrefixNome"; //Query che carica l'id e il nome dei package che sono in relazione tra loro. Occhio che deve considerare i casi che
		//il package corrente sia a destra sia a sinistra
		$relpack=mysqli_query($conn,$query) or fail("Query fallita: ".mysqli_error($conn));
		$riga = mysqli_fetch_row($relpack);
		if($riga[0]!=null){
echo<<<END

					<dt class="widget-title">Package Correlati:</dt>
					<dd><a class="link-color-pers" href="$absurl/Package/dettagliopackage.php?id=$riga[0]">$riga[1]</a></dd>
END;
		}
		while($riga = mysqli_fetch_row($relpack)){
echo<<<END

					<dd><a class="link-color-pers" href="$absurl/Package/dettagliopackage.php?id=$riga[0]">$riga[1]</a></dd>
END;
		}
		//------- Stampa i requisiti correlati
		//$query_ord="CALL sortForest('Requisiti')";
		//$query_update="CALL automatizeRequisitiPackage()";
		$query="SELECT r.CodAuto, r.IdRequisito, r.Descrizione
				FROM RequisitiPackage rp JOIN (_MapRequisiti h JOIN Requisiti r ON h.CodAuto=r.CodAuto) on rp.CodReq=r.CodAuto
				WHERE rp.CodPkg='$id'
				ORDER BY h.Position"; //Query che carica i requisiti correlati
		//$ord=mysqli_query($conn, $query_ord) or fail("Query fallita: ".mysqli_error($conn));
		//$upd=mysqli_query($conn, $query_update) or fail("Query fallita: ".mysqli_error($conn));
		$req=mysqli_query($conn,$query) or fail("Query fallita: ".mysqli_error($conn));
		$riga = mysqli_fetch_row($req);
		if($riga[0]!=null){
echo<<<END

					<dt class="widget-title">Requisiti Correlati:</dt>
					<dd><a class="link-color-pers" href="$absurl/Requisiti/dettagliorequisito.php?id=$riga[0]">$riga[1] - $riga[2]</a></dd>
END;
		}
		while($riga = mysqli_fetch_row($req)){
echo<<<END

					<dd><a class="link-color-pers" href="$absurl/Requisiti/dettagliorequisito.php?id=$riga[0]">$riga[1] - $riga[2]</a></dd>
END;
		}
		//------- Stampa il link per la modifica
echo<<<END

					<dt class="widget-title">Modifica Package:</dt>
					<dd><a class="link-color-pers" href="$absurl/Package/modificapackage.php?id=$id">Modifica $row[1]</a></dd>
				</dl>
END;
	}
	else{
		//Non ho trovato niente con questo $id
		$title="Dettaglio Package - Package Non Trovato";
		startpage_builder($title);
echo<<<END

			<div id="content" class="alerts">
				<h2>Errore</h2>
				<p>Il package con id "$id" non è presente nel database.</p>
END;
	}
echo<<<END

			</div>
END;
	endpage_builder();
}
?>