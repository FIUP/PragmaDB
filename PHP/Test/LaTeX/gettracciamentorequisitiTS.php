<?php

require('../../Functions/mysql_fun.php');
require('../../Functions/urlLab.php');

session_start();

$absurl=urlbasesito();

if(empty($_SESSION['user'])){
	header("Location: $absurl/error.php");
}
else{
	header('Content-type: application/x-tex');
	header('Content-Disposition: attachment; filename="tracciamentoRequisitiTS.tex"');
	header('Expires: 0');
	header('Cache-Control: no-cache, must-revalidate');
	
	$conn=sql_conn();
	//$query_ord="CALL sortForest('Requisiti')";
	$query_requi="SELECT r.IdRequisito, CONCAT('TV',SUBSTRING(r.IdRequisito,2))
			   FROM (_MapRequisiti h JOIN Requisiti r ON h.CodAuto=r.CodAuto) JOIN Test t ON r.CodAuto=t.Requisito
			   WHERE t.Tipo='Sistema'
			   ORDER BY h.Position";
	//$ord=mysql_query($query_ord,$conn) or fail("Query fallita: ".mysql_error($conn));
	$requi=mysql_query($query_requi,$conn) or fail("Query fallita: ".mysql_error($conn));
echo<<<END
\\subsection{Tracciamento Requisiti-Test di Sistema}
\\normalsize
\\begin{longtabu}{>{\centering}m{5cm}m{5cm}<{\centering}}
\\caption[Tracciamento Requisiti-Test di Sistema]{Tracciamento Requisiti-Test di Sistema}
\\label{tabella:requi-tv}
\\endlastfoot
\\rowcolor{tableHeader}
\\rowfont{\bfseries\sffamily\leavevmode\color{white}}
%\hline
\\textbf{Requisito} & \\textbf{Test}\\\
%\hline
\\endhead
END;
	while($row_requi=mysql_fetch_row($requi)){
echo<<<END

$row_requi[0] & \\hyperlink{{$row_requi[1]}}{{$row_requi[1]}}\\\ \\hline
END;
	}
echo<<<END

\\end{longtabu}
\\clearpage

END;
}
?>