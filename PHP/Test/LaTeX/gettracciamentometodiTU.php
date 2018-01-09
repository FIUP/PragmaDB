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
	header('Content-Disposition: attachment; filename="tracciamentoMetodiTU.tex"');
	header('Expires: 0');
	header('Cache-Control: no-cache, must-revalidate');
	
	$conn=sql_conn();
	$query_met="SELECT DISTINCT m.CodAuto,m.AccessMod,m.Nome,m.ReturnType, c.PrefixNome, c.CodAuto
			   FROM (TestMetodi tm JOIN Metodo m ON tm.CodMet=m.CodAuto) JOIN Classe c ON m.Classe=c.CodAuto
			   ORDER BY c.PrefixNome, m.Nome";
	$met=mysqli_query($conn, $query_met) or fail("Query fallita: ".mysqli_error($conn));
echo<<<END
\\subsection{Tracciamento Metodi-Test di Unità}
\\normalsize
\\begin{longtabu} to \\textwidth {>{\centering}m{12cm}m{1cm}<{\centering}}
\\caption[Tracciamento Metodi-Test di Unità]{Tracciamento Metodi-Test di Unità}
\\label{tabella:met-tu}
\\endlastfoot
\\rowfont{\bfseries\sffamily\leavevmode\color{white}}
\\rowcolor{tableHeader}
%\hline
\\textbf{Metodo} & \\textbf{Test}\\\
%\hline
\\endhead
END;
	while($riga=mysqli_fetch_row($met)){
		$prefix=$riga[4]."::".$riga[2]."()";
		$prefix=fixMethodIntoBorder($prefix);
		$query_tu="SELECT DISTINCT t.IdTest
				   FROM TestMetodi tm JOIN Test t ON tm.CodTest=t.CodAuto
				   WHERE tm.CodMet='$riga[0]'
				   ORDER BY CONVERT(SUBSTRING(t.IdTest,3),UNSIGNED INT)";
		$tu=mysqli_query($conn, $query_tu) or fail("Query fallita: ".mysqli_error($conn));
echo<<<END
\\nogloxy{\\texttt{{$prefix}}}
END;
		while($row_tu = mysqli_fetch_row($tu)){
echo<<<END
 & \\hyperlink{{$row_tu[0]}}{{$row_tu[0]}}\\\
END;
		}
echo<<<END
 \\hline

END;
	}
echo<<<END

\\end{longtabu}
\\clearpage

END;
}
?>