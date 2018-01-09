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
	header('Content-Disposition: attachment; filename="tracciamentoComponentiTI.tex"');
	header('Expires: 0');
	header('Cache-Control: no-cache, must-revalidate');
	
	$conn=sql_conn();
	$query_pkg="SELECT p.PrefixNome, t.IdTest
				FROM Package p JOIN Test t ON p.CodAuto=t.Package
				WHERE t.Tipo='Integrazione'
				ORDER BY p.PrefixNome";
	$pkg=mysqli_query($conn, $query_pkg) or fail("Query fallita: ".mysqli_error($conn));
echo<<<END
\\subsection{Tracciamento Componenti-Test di Integrazione}
\\normalsize
\\begin{longtabu} to \\textwidth {>{\centering}m{9cm}m{3cm}<{\centering}}
\\caption[Tracciamento Componenti-Test di Integrazione]{Tracciamento Componenti-Test di Integrazione}
\\label{tabella:pkg-ti}
\\endlastfoot
\\rowfont{\bfseries\sffamily\leavevmode\color{white}}
\\rowcolor{tableHeader}
%\hline
\\textbf{Componente} & \\textbf{Test}\\\
%\hline
\\endhead
END;
	while($row_pkg=mysqli_fetch_row($pkg)){
echo<<<END

\\nogloxy{\\texttt{{$row_pkg[0]}}} & \\hyperlink{{$row_pkg[1]}}{{$row_pkg[1]}}\\\ %\hline
END;
	}
echo<<<END

\\end{longtabu}
\\clearpage

END;
}
?>