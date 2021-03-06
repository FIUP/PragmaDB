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
	header('Content-Disposition: attachment; filename="tracciamentoTUMetodi.tex"');
	header('Expires: 0');
	header('Cache-Control: no-cache, must-revalidate');
	
	$conn=sql_conn();
	$query_tu="SELECT DISTINCT t.CodAuto, t.IdTest
			   FROM TestMetodi tm JOIN Test t ON tm.CodTest=t.CodAuto
			   WHERE t.Tipo='Unita'
			   ORDER BY CONVERT(SUBSTRING(t.IdTest,3),UNSIGNED INT)";
	$tu=mysqli_query($conn, $query_tu) or fail("Query fallita: ".mysqli_error($conn));
echo<<<END
\\subsection{Tracciamento Test di Unità-Metodi}
\\normalsize
\\begin{longtabu} to \\textwidth {>{\centering}m{1cm}m{12cm}<{\centering}}
\\caption[Tracciamento Test di Unità-Metodi]{Tracciamento Test di Unità-Metodi}
\\label{tabella:tu-met}
\\endlastfoot
\\rowfont{\bfseries\sffamily\leavevmode\color{white}}
\\rowcolor{tableHeader}
%\hline
\\textbf{Test} & \\textbf{Metodi}\\\
%\hline
\\endhead
END;
	while($row_tu=mysqli_fetch_row($tu)){
		$query="SELECT m.CodAuto,m.AccessMod,m.Nome,m.ReturnType, c.PrefixNome, c.CodAuto
				FROM (TestMetodi tm JOIN Metodo m ON tm.CodMet=m.CodAuto) JOIN Classe c ON m.Classe=c.CodAuto
				WHERE tm.CodTest='$row_tu[0]'
				ORDER BY c.PrefixNome, m.Nome"; //Query che carica i metodi della classe
		$met=mysqli_query($conn,$query) or fail("Query fallita: ".mysqli_error($conn));
echo<<<END

\\hyperlink{{$row_tu[1]}}{{$row_tu[1]}}
END;
		while($riga = mysqli_fetch_row($met)){
			$prefix=$riga[4]."::".$riga[2]."()";
			$prefix=fixMethodIntoBorder($prefix);
			/*if($riga[1]=="#"){
echo<<<END
 & \\hyperref[{$riga[4]}::{$riga[2]}]{\\texttt{\\{$riga[1]} {$riga[2]}(
END;
			}
			else{*/
echo<<<END
 & \\nogloxy{\\texttt{{$prefix}}}\\\
END;
		}
echo<<<END
 %\hline

END;
	}
echo<<<END

\\end{longtabu}
\\clearpage

END;
}
?>