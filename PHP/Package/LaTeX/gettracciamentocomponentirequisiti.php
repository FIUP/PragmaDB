<?php

require('../../Functions/get_tex.php');
require('../../Functions/mysql_fun.php');
require('../../Functions/urlLab.php');

session_start();

date_default_timezone_set("Europe/Rome");

$absurl=urlbasesito();

if(empty($_SESSION['user'])){
	header("Location: $absurl/error.php");
}
else{
	header('Content-type: application/x-tex');
	header('Content-Disposition: attachment; filename="tracciamentoComponentiRequisiti.tex"');
	header('Expires: 0');
	header('Cache-Control: no-cache, must-revalidate');
	
	$conn=sql_conn();
	//$query_update="CALL automatizeRequisitiPackage()";
	$query_pkg="SELECT DISTINCT p.CodAuto, p.PrefixNome
				FROM Package p JOIN RequisitiPackage rp ON p.CodAuto=rp.CodPkg
				WHERE p.PrefixNome<>'Premi'
				ORDER BY p.PrefixNome";
	//$upd=mysqli_query($conn, $query_update) or fail("Query fallita: ".mysqli_error($conn));
	$pkg=mysqli_query($conn, $query_pkg) or fail("Query fallita: ".mysqli_error($conn));
echo<<<END
\\subsection{Tracciamento Componenti-Requisiti}
\\normalsize
\\begin{longtabu} to \\textwidth{>{\centering}m{10cm}m{3cm}}
\\caption[Tracciamento Componenti-Requisiti]{Tracciamento Componenti-Requisiti}
\\label{tabella:pack-requi}
\\endlastfoot
\\rowfont{\bfseries\sffamily\leavevmode\color{white}}
\\rowcolor{tableHeader}
%\hline 
\\textbf{Componente} & \\textbf{Requisiti}\\\
%\hline
\\endhead
END;
	//$query_ord="CALL sortForest('Requisiti')";
	//$ord=mysqli_query($conn, $query_ord) or fail("Query fallita: ".mysqli_error($conn));
	while($row_pkg=mysqli_fetch_row($pkg)){
		componentiRequisitiTex($conn, $row_pkg);
	}
echo<<<END

\\end{longtabu}
\\clearpage

END;
}
?>