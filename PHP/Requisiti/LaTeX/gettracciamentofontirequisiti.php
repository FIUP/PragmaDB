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
	header('Content-Disposition: attachment; filename="tracciamentoFontiRequisiti.tex"');
	header('Expires: 0');
	header('Cache-Control: no-cache, must-revalidate');
	
	$conn=sql_conn();
	$query_fonti="SELECT DISTINCT f.CodAuto,f.Nome
					FROM Fonti f JOIN Requisiti r ON f.CodAuto=r.Fonte
					ORDER BY f.Nome";
	//$query_ord="CALL sortForest('UseCase')";
	$query_uc="SELECT DISTINCT u.CodAuto,u.IdUC
				FROM (_MapUseCase h JOIN UseCase u ON h.CodAuto=u.CodAuto) JOIN RequisitiUC ruc ON u.CodAuto=ruc.UC
				ORDER BY h.Position";
	$fonti=mysqli_query($conn, $query_fonti) or fail("Query fallita: ".mysqli_error($conn));
	//$ord=mysqli_query($conn, $query_ord) or fail("Query fallita: ".mysqli_error($conn));
	$uc=mysqli_query($conn, $query_uc) or fail("Query fallita: ".mysqli_error($conn));
echo<<<END
\\subsection{Tracciamento Fonti-Requisiti}
\\normalsize
\\begin{longtabu} to \\textwidth{>{\centering}m{5cm}m{5cm}}
\\caption[Tracciamento Fonti-Requisiti]{Tracciamento Fonti-Requisiti}
\\label{tabella:fonti-requi}
\\endlastfoot
\\rowfont{\bfseries\sffamily\leavevmode\color{white}}
\\rowcolor{tableHeader}
%\hline 
\\textbf{Fonte} & \\textbf{ID Requisiti}\\\
%\hline
\\endhead
END;
	//$query_ord="CALL sortForest('Requisiti')";
	//$ord=mysqli_query($conn, $query_ord) or fail("Query fallita: ".mysqli_error($conn));
	while($row_fonti=mysqli_fetch_row($fonti)){
		$query_requi="SELECT r.IdRequisito
						FROM _MapRequisiti h JOIN Requisiti r ON h.CodAuto=r.CodAuto
						WHERE r.Fonte='$row_fonti[0]'
						ORDER BY h.Position";
		$is_uc=false;
		fontiRequisitiTex($conn,$row_fonti,$query_requi,$is_uc);
	}
	while($row_uc=mysqli_fetch_row($uc)){
		$query_requi="SELECT r.IdRequisito
						FROM RequisitiUC ruc JOIN (_MapRequisiti h JOIN Requisiti r ON h.CodAuto=r.CodAuto) ON ruc.CodReq=r.CodAuto
						WHERE ruc.UC='$row_uc[0]'
						ORDER BY h.Position";
		$is_uc=true;
		fontiRequisitiTex($conn,$row_uc,$query_requi,$is_uc);
	}
echo<<<END

\\end{longtabu}
\\clearpage

END;
}
?>