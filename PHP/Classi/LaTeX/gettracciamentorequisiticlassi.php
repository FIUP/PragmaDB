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
	header('Content-Disposition: attachment; filename="tracciamentoRequisitiClassi.tex"');
	header('Expires: 0');
	header('Cache-Control: no-cache, must-revalidate');
	
	$conn=sql_conn();
	//$query_ord="CALL sortForest('Requisiti')";
	$query_requi="SELECT DISTINCT r.CodAuto, r.IdRequisito
				FROM (_MapRequisiti h JOIN Requisiti r ON h.CodAuto=r.CodAuto) JOIN RequisitiClasse rc ON r.CodAuto=rc.CodReq
				ORDER BY h.Position";
	//$ord=mysqli_query($conn, $query_ord) or fail("Query fallita: ".mysqli_error($conn));
	$requi=mysqli_query($conn, $query_requi) or fail("Query fallita: ".mysqli_error($conn));
echo<<<END
\\subsection{Tracciamento Requisiti-Classi}
\\normalsize
\\begin{longtabu} to \\textwidth {>{\centering}m{3cm}m{10cm}<{\centering}}
\\caption[Tracciamento Requisiti-Classi]{Tracciamento Requisiti-Classi}
\\label{tabella:requi-class}
\\endlastfoot
\\rowfont{\bfseries\sffamily\leavevmode\color{white}}
\\rowcolor{tableHeader}
%\hline 
\\textbf{Requisito} & \\textbf{Classi}\\\
%\hline
\\endhead
END;
	while($row_requi=mysqli_fetch_row($requi)){
		requisitiClassiTex($conn, $row_requi);
	}
echo<<<END

\\end{longtabu}
\\clearpage

END;
}
?>