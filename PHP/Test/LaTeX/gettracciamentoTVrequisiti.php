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
	header('Content-Disposition: attachment; filename="tracciamentoTVRequisiti.tex"');
	header('Expires: 0');
	header('Cache-Control: no-cache, must-revalidate');
	
	$conn=sql_conn();
	//$query_ord="CALL sortForest('Requisiti')";
	$query_tv="SELECT CONCAT('TV',SUBSTRING(r.IdRequisito,2)), r.IdRequisito
			   FROM Test t JOIN (_MapRequisiti h JOIN Requisiti r ON h.CodAuto=r.CodAuto) ON t.Requisito=r.CodAuto
			   WHERE t.Tipo='Validazione'
			   ORDER BY h.Position";
	//$ord=mysqli_query($conn, $query_ord) or fail("Query fallita: ".mysqli_error($conn));
	$tv=mysqli_query($conn, $query_tv) or fail("Query fallita: ".mysqli_error($conn));
echo<<<END
\\subsection{Tracciamento Test di Validazione-Requisiti}
\\normalsize
\\begin{longtabu} to \\textwidth {>{\centering}m{5cm}m{5cm}<{\centering}}
\\caption[Tracciamento Test di Validazione-Requisiti]{Tracciamento Test di Validazione-Requisiti}
\\label{tabella:tv-requi}
\\endlastfoot
\\rowfont{\bfseries\sffamily\leavevmode\color{white}}
\\rowcolor{tableHeader}
%\hline
\\textbf{Test} & \\textbf{Requisito}\\\
%\hline
\\endhead
END;
	while($row_tv=mysqli_fetch_row($tv)){
echo<<<END

\\hyperlink{{$row_tv[0]}}{{$row_tv[0]}} & $row_tv[1]\\\ %\hline
END;
	}
echo<<<END

\\end{longtabu}
\\clearpage

END;
}
?>