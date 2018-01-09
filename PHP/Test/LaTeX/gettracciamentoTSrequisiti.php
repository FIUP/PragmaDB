<?php

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
	header('Content-Disposition: attachment; filename="tracciamentoTSRequisiti.tex"');
	header('Expires: 0');
	header('Cache-Control: no-cache, must-revalidate');
	
	$conn=sql_conn();
	//$query_ord="CALL sortForest('Requisiti')";
	$query_ts="SELECT CONCAT('TS',SUBSTRING(r.IdRequisito,2)), r.IdRequisito
			   FROM Test t JOIN (_MapRequisiti h JOIN Requisiti r ON h.CodAuto=r.CodAuto) ON t.Requisito=r.CodAuto
			   WHERE t.Tipo='Sistema'
			   ORDER BY h.Position";
	//$ord=mysqli_query($conn, $query_ord) or fail("Query fallita: ".mysqli_error($conn));
	$ts=mysqli_query($conn, $query_ts) or fail("Query fallita: ".mysqli_error($conn));
echo<<<END
\\subsection{Tracciamento Test di Sistema-Requisiti}
\\normalsize
\\begin{longtable} to \\textwidth {>{\centering}m{5cm}m{5cm}<{\centering}}
\\caption[Tracciamento Test di Sistema-Requisiti]{Tracciamento Test di Sistema-Requisiti}
\\label{tabella:ts-requi}
\\endlastfoot
\\rowfont{\bfseries\sffamily\leavevmode\color{white}}
\\rowcolor{tableHeader}
%\hline
\\textbf{Test} & \\textbf{Requisito}\\\
%\hline
\\endhead
END;
	while($row_ts=mysqli_fetch_row($ts)){
echo<<<END

\\hyperlink{{$row_ts[0]}}{{$row_ts[0]}} & $row_ts[1]\\\ %\hline
END;
	}
echo<<<END

\\end{longtabu}
\\clearpage

END;
}
?>