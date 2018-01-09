<?php

require('../Functions/mysql_fun.php');
require('../Functions/urlLab.php');

session_start();

date_default_timezone_set("Europe/Rome");

$absurl=urlbasesito();

if(empty($_SESSION['user'])){
	header("Location: $absurl/error.php");
}
else{
	header('Content-type: application/x-tex');
	header('Content-Disposition: attachment; filename="useCase.tex"');
	header('Expires: 0');
	header('Cache-Control: no-cache, must-revalidate');
	
	$conn=sql_conn();
	//$query_ord="CALL sortForest('UseCase')";
	$query="SELECT u.CodAuto,u.IdUC,u.Nome,u.Diagramma,u.Descrizione,u.Precondizioni,u.Postcondizioni,u.ScenarioPrincipale,u.Inclusioni,u.Estensioni,u.ScenariAlternativi,u.Padre
			FROM _MapUseCase h JOIN UseCase u ON h.CodAuto=u.CodAuto
			ORDER BY h.Position";
	//$ord=mysqli_query($conn, $query_ord) or fail("Query fallita: ".mysqli_error($conn));
	$uc=mysqli_query($conn,$query) or fail("Query fallita: ".mysqli_error($conn));
	while($row=mysqli_fetch_row($uc)){
echo<<<END
\\subsection{{$row[1]}: {$row[2]}}
\\label{{$row[1]}}
END;
		if($row[3]!=null){
echo<<<END

\\begin{figure}[H]
\\capstart
\\def\svgwidth{\columnwidth}
\\input{../../../bin/img/ar/pdf_tex/{$row[3]}.pdf_tex}
\\caption{{$row[1]}: {$row[2]}}
\\end{figure}
END;
		}
		$query="SELECT a.Nome
				FROM AttoriUC auc JOIN Attori a ON auc.Attore=a.CodAuto
				WHERE auc.UC='$row[0]'
				ORDER BY a.Nome";
		$attori=mysqli_query($conn,$query) or fail("Query fallita: ".mysqli_error($conn));
		$row_attore=mysqli_fetch_row($attori);
echo<<<END

\\begin{itemize}
\\item \\textbf{Attori}: $row_attore[0]
END;
		while($row_attore=mysqli_fetch_row($attori)){
echo<<<END
, $row_attore[0]
END;
		}
echo<<<END
;
\\item \\textbf{Descrizione}: $row[4]
\\item \\textbf{Precondizione}: $row[5]
\\item \\textbf{Postcondizione}: $row[6]
\\item \\textbf{Scenario principale}:
$row[7]
END;
		if($row[8]!=null){
echo<<<END

\\item \\textbf{Inclusioni}:
$row[8]
END;
		}
		if($row[9]!=null){
echo<<<END

\\item \\textbf{Estensioni}:
$row[9]
END;
		}
		if($row[10]!=null){
echo<<<END

\\item \\textbf{Scenari alternativi}:
$row[10]
END;
		}
echo<<<END

\\end{itemize}


END;
	}
}
?>