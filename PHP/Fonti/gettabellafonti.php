<?php

require('../Functions/mysql_fun.php');
require('../Functions/urlLab.php');

session_start();

date_default_timezone_set("Europe/Rome");

$absurl=urlbasesito();

if(empty($_SESSION['user'])){
	header("Location: $absurl/error.php");
}
else{
	header('Content-type: application/x-tex');
	header('Content-Disposition: attachment; filename="tabellafonti.tex"');
	header('Expires: 0');
	header('Cache-Control: no-cache, must-revalidate');
	
	$headers=array('Fonte','Nome','Descrizione');
	$conn=sql_conn();
	$query="SELECT f.IdFonte,f.Nome,f.Descrizione
			FROM Fonti f
			ORDER BY f.IdFonte";
	$fonti=mysqli_query($conn,$query) or fail("Query fallita: ".mysqli_error($conn));
	$row=mysqli_fetch_row($fonti);
	if($row[0]!=null){
echo<<<END
\\small
\\begin{longtable}{|c|m{4cm}|m{2.7cm}<{\\centering}|}
\\hline 
\\textbf{{$headers[0]}} & \\textbf{{$headers[1]}} & \\textbf{{$headers[2]}}\\\
\\hline
END;
		while($row=mysqli_fetch_row($fonti)){
echo<<<END

$row[0] & $row[1] & $row[2]\\\ \\hline

END;
		}
echo<<<END

\\end{longtable}
\\clearpage

END;
	}
}
?>