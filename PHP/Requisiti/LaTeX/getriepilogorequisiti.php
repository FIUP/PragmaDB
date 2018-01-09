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
	header('Content-Disposition: attachment; filename="riepilogoRequisiti.tex"');
	header('Expires: 0');
	header('Cache-Control: no-cache, must-revalidate');
	
	$conn=sql_conn();
	//$query_ord="CALL sortForest('Requisiti')";
	$query="SELECT r.IdRequisito
			FROM _MapRequisiti h JOIN Requisiti r ON h.CodAuto=r.CodAuto
			ORDER BY h.Position";
	//$ord=mysqli_query($conn, $query_ord) or fail("Query fallita: ".mysqli_error($conn));
	$requi=mysqli_query($conn,$query) or fail("Query fallita: ".mysqli_error($conn));
	$FO=0; $FD=0; $FF=0; $PO=0; $PD=0; $PF=0; $QO=0; $QD=0; $QF=0; $VO=0; $VD=0; $VF=0;
	while($row_requi=mysqli_fetch_row($requi)){
		$id=$row_requi[0];
		${$id[1].$id[2]}++;
	}
echo<<<END
\\subsection{Riepilogo Requisiti}
\\normalsize
\\begin{longtabu} to \\textwidth {cccc}
\\caption[Riepilogo Requisiti]{Riepilogo Requisiti}
\\label{tabella:riepilogorequi}
\\endlastfoot
\\rowfont{\bfseries\sffamily\leavevmode\color{white}}
\\rowcolor{tableHeader}
%\hline
\\textbf{Tipo} & \\textbf{Obbligatorio} & \\textbf{Desiderabile} & \\textbf{Facoltativo}\\\
%\hline
Funzionale & $FO & $FD & $FF\\\ %\hline
Prestazionale & $PO & $PD & $PF\\\ %\hline
Di Qualità & $QO & $QD & $QF\\\ %\hline
Di Vincolo & $VO & $VD & $VF\\\ %\hline
\\end{longtabu}
\\clearpage

END;
}
?>