<?php

function classiRequisitiTex($conn, $row){
	$query="SELECT r.IdRequisito
			FROM RequisitiClasse rc JOIN (_MapRequisiti h JOIN Requisiti r ON h.CodAuto=r.CodAuto) ON rc.CodReq=r.CodAuto
			WHERE rc.CodClass='$row[0]'
			ORDER BY h.Position";
	$requi=mysqli_query($conn,$query) or fail("Query fallita: ".mysqli_error($conn));
	$requi_row=mysqli_fetch_row($requi);
	$prefix=$row[1];
	$prefix=fixIntoBorder($prefix);
echo<<<END

\\hyperref[{$row[1]}]{\\texttt{{$prefix}}} & $requi_row[0]
END;
	while($requi_row=mysqli_fetch_row($requi)){
echo<<<END
\\newline $requi_row[0]
END;
	}
echo<<<END
\\\ %\hline

END;
}

function componentiRequisitiTex($conn, $row){
	$query="SELECT r.IdRequisito
			FROM RequisitiPackage rp JOIN (_MapRequisiti h JOIN Requisiti r ON h.CodAuto=r.CodAuto) ON rp.CodReq=r.CodAuto
			WHERE rp.CodPkg='$row[0]'
			ORDER BY h.Position";
	$requi=mysqli_query($conn,$query) or fail("Query fallita: ".mysqli_error($conn));
	$requi_row=mysqli_fetch_row($requi);
	$prefix=$row[1];
	$prefix=fixIntoBorder($prefix);
echo<<<END

\\hyperref[{$row[1]}]{\\texttt{{$prefix}}} & $requi_row[0]
END;
	while($requi_row=mysqli_fetch_row($requi)){
echo<<<END
\\newline $requi_row[0]
END;
	}
echo<<<END
\\\ %\hline

END;
}

function fixIntoBorder($prefix){
	$ind1=38;
	$ind2=38;
	$el=false;
	$er=false;
	$resolved=false;
	while((strlen($prefix)>38) && ((!$el) || (!$er)) && (!$resolved)){
		if(($prefix[$ind1]==":") && ($prefix[$ind1-1]==":")){
			$prefix=substr_replace($prefix, "-\\linebreak ", ($ind1+1), 0);
			$resolved=true;
		}
		else{
			if($ind1<=1){
				$el=true;
			}
			else{
				$ind1--;
			}
		}
		if(($prefix[$ind2]==":") && ($prefix[$ind2+1]==":") && (!$resolved)){
			$prefix=substr_replace($prefix, "-\\linebreak ", ($ind2+2), 0);
			$resolved=true;
		}
		else{
			if($ind2>=(strlen($prefix)-2)){
				$er=true;
			}
			else{
				$ind2++;
			}
		}
	}
	return $prefix;
}

function fixMethodIntoBorder($prefix){
	$ind1=39;
	$ind2=39;
	$el=false;
	$er=false;
	$resolved=false;
	while((strlen($prefix)>39) && ((!$el) || (!$er)) && (!$resolved)){
		if(($prefix[$ind1]==":") && ($prefix[$ind1-1]==":")){
			$prefix=substr_replace($prefix, "-\\linebreak ", ($ind1+1), 0);
			$resolved=true;
		}
		else{
			if($ind1<=1){
				$el=true;
			}
			else{
				$ind1--;
			}
		}
		if(($prefix[$ind2]==":") && ($prefix[$ind2+1]==":") && (!$resolved)){
			$prefix=substr_replace($prefix, "-\\linebreak ", ($ind2+2), 0);
			$resolved=true;
		}
		else{
			if($ind2>=(strlen($prefix)-2)){
				$er=true;
			}
			else{
				$ind2++;
			}
		}
	}
	return $prefix;
}

function fontiRequisitiTex($conn, $row, $query, $is_uc){
	$requi=mysqli_query($conn,$query) or fail("Query fallita: ".mysqli_error($conn));
	if($row_requi=mysqli_fetch_row($requi)){
		if($is_uc==false){
echo<<<END

\\hyperlink{{$row[1]}}{{$row[1]}} & \\hyperlink{{$row_requi[0]}}{{$row_requi[0]}}
END;
		}
		else{
echo<<<END

\\hyperref[{$row[1]}]{{$row[1]}} & \\hyperlink{{$row_requi[0]}}{{$row_requi[0]}}
END;
		}
		while($row_requi=mysqli_fetch_row($requi)){
echo<<<END
\\newline
\\hyperlink{{$row_requi[0]}}{{$row_requi[0]}}
END;
		}
echo<<<END
\\\ %\hline
END;
	}
}

function glossarioTex(){
	$conn=sql_conn();
	$query="SELECT g.Identificativo, g.Name, g.Description, g.First, g.FirstPlural, g.Text, g.Plural
			FROM Glossario g
			ORDER BY g.Identificativo";
	$glo=mysqli_query($conn,$query) or fail("Query fallita: ".mysqli_error($conn));
	if($row=mysqli_fetch_row($glo)){
echo<<<END
\\newglossaryentry{{$row[0]}}
{
name={{$row[1]}},
description={{$row[2]}}
END;
		$headers= ['','','','first','firstplural','text','plural'];
		for($i=3;$i<7;$i++){
			if($row[$i]!=null){
echo<<<END
,
$headers[$i]={{$row[$i]}}
END;
			}
		}
echo<<<END

}
END;
	}
	while($row=mysqli_fetch_row($glo)){
echo<<<END


\\newglossaryentry{{$row[0]}}
{
name={{$row[1]}},
description={{$row[2]}}
END;
		$headers= ['','','','first','firstplural','text','plural'];
		for($i=3;$i<7;$i++){
			if($row[$i]!=null){
echo<<<END
,
$headers[$i]={{$row[$i]}}
END;
			}
		}
echo<<<END

}
END;
	}
}

function packageClassiCommonTex($conn, $riga, $flag){
echo<<<END

\\paragraph{{$riga[1]}}
\\label{{$riga[1]}}
END;

	if(($flag==true) && ($riga[4]!=null)){
echo<<<END

\\begin{figure}[H]
\\capstart
\\def\svgwidth{\columnwidth}
\\input{../../../bin/img/st/package/pdf_tex/{$riga[4]}.pdf_tex}
\\caption{{$riga[1]}}
\\end{figure}
END;
	}
echo<<<END

\\begin{itemize}
\\item \\textbf{Descrizione}\\\
$riga[2]
END;
	if($riga[3]!=null){
echo<<<END

\\item \\textbf{Utilizzo}\\\
$riga[3]
END;
	}
	$queryEredita="SELECT c.PrefixNome, c.Nome
				   FROM EreditaDa ed JOIN Classe c ON ed.Padre=c.CodAuto
				   WHERE ed.Figlio='$riga[0]'
				   ORDER BY c.PrefixNome";
	$padri=mysqli_query($conn, $queryEredita) or fail("Query fallita: ".mysqli_error($conn));
	$riga_sub=mysqli_fetch_row($padri);
	if($riga_sub[0]!=null){
echo<<<END

\\item \\textbf{Classi ereditate}:
\\begin{itemize}
\\item \\hyperref[{$riga_sub[0]}]{\\texttt{{$riga_sub[1]}}}
END;
		while($riga_sub=mysqli_fetch_row($padri)){
echo<<<END

\\item \\hyperref[{$riga_sub[0]}]{\\texttt{{$riga_sub[1]}}}
END;
		}
echo<<<END

\\end{itemize}
END;
	}
	$querySubClassi="SELECT c.PrefixNome, c.Nome
					 FROM EreditaDa ed JOIN Classe c ON ed.Figlio=c.CodAuto
					 WHERE ed.Padre='$riga[0]'
					 ORDER BY c.PrefixNome";
	$subclassi=mysqli_query($conn, $querySubClassi)or fail("Query fallita: ".mysqli_error($conn));
	$riga_sub=mysqli_fetch_row($subclassi);
	if($riga_sub[0]!=null){
echo<<<END

\\item \\textbf{Sottoclassi}:
\\begin{itemize}
\\item \\hyperref[{$riga_sub[0]}]{\\texttt{{$riga_sub[1]}}}
END;
		while($riga_sub=mysqli_fetch_row($subclassi)){
echo<<<END

\\item \\hyperref[{$riga_sub[0]}]{\\texttt{{$riga_sub[1]}}}
END;
		}
echo<<<END

\\end{itemize}
END;
	}
	$queryRelationsIN="SELECT c.PrefixNome, c.Nome, c.Descrizione
					   FROM Relazione r JOIN Classe c ON r.Da=c.CodAuto
					   WHERE r.A='$riga[0]'
					   ORDER BY c.PrefixNome";
	$queryRelationsOUT="SELECT c.PrefixNome, c.Nome, c.Descrizione
						FROM Relazione r JOIN Classe c ON r.A=c.CodAuto
						WHERE r.Da='$riga[0]'
						ORDER BY c.PrefixNome";
	$in=mysqli_query($conn, $queryRelationsIN) or fail("Query fallita: ".mysqli_error($conn));
	$out=mysqli_query($conn, $queryRelationsOUT) or fail("Query fallita: ".mysqli_error($conn));
	$riga_in=mysqli_fetch_row($in);
	$riga_out=mysqli_fetch_row($out);
	if(($riga_in[0]!=null) || ($riga_out[0]!=null)){
echo<<<END

\\item \\textbf{Relazioni con altre classi}:
\\begin{itemize}
END;
		if($riga_in[0]!=null){
echo<<<END

\\item \\textit{IN} \\hyperref[{$riga_in[0]}]{\\texttt{{$riga_in[1]}}}\\\
$riga_in[2]
END;
		}
		while($riga_in=mysqli_fetch_row($in)){
echo<<<END

\\item \\textit{IN} \\hyperref[{$riga_in[0]}]{\\texttt{{$riga_in[1]}}}\\\
$riga_in[2]
END;
		}
		if($riga_out[0]!=null){
echo<<<END

\\item \\textit{OUT} \\hyperref[{$riga_out[0]}]{\\texttt{{$riga_out[1]}}}\\\
$riga_out[2]
END;
		}
		while($riga_out=mysqli_fetch_row($out)){
echo<<<END

\\item \\textit{OUT} \\hyperref[{$riga_out[0]}]{\\texttt{{$riga_out[1]}}}\\\
$riga_out[2]
END;
		}
echo<<<END

\\end{itemize}
END;
	}
	if($flag==true){
		packageClassiDPTex($conn, $riga);
	}
echo<<<END

\\end{itemize}

END;
}

function packageClassiDPTex($conn, $riga){
	$query="SELECT a.AccessMod, a.Nome, a.Tipo, a.Descrizione
			FROM Attributo a
			WHERE a.Classe='$riga[0]'
			ORDER BY a.Nome";
	$attr=mysqli_query($conn,$query) or fail("Query fallita: ".mysqli_error($conn));
	if($row_attr = mysqli_fetch_row($attr)){
echo<<<END

\\item \\textbf{Attributi}:
\\begin{itemize}
END;
		if($row_attr[0]=="#"){
echo<<<END

\\item \\nogloxy{\\texttt{\\{$row_attr[0]} {$row_attr[1]}: {$row_attr[2]}}}
END;
		}
		else{
echo<<<END

\\item \\nogloxy{\\texttt{{$row_attr[0]} {$row_attr[1]}: {$row_attr[2]}}}
END;
		}
echo<<<END

\\\ {$row_attr[3]}
END;
		while($row_attr = mysqli_fetch_row($attr)){
			if($row_attr[0]=="#"){
echo<<<END

\\item \\nogloxy{\\texttt{\\{$row_attr[0]} {$row_attr[1]}: {$row_attr[2]}}}
END;
			}
			else{
echo<<<END

\\item \\nogloxy{\\texttt{{$row_attr[0]} {$row_attr[1]}: {$row_attr[2]}}}
END;
			}
echo<<<END

\\\ {$row_attr[3]}
END;
		}
echo<<<END

\\end{itemize}
END;
	}
	$query="SELECT m.CodAuto, m.AccessMod, m.Nome, m.ReturnType, m.Descrizione
			FROM Metodo m
			WHERE m.Classe='$riga[0]'
			ORDER BY m.Nome";
	$met=mysqli_query($conn,$query) or fail("Query fallita: ".mysqli_error($conn));
	$row_met = mysqli_fetch_row($met);
	if($row_met[0]!=null){
echo<<<END

\\item \\textbf{Metodi}:
\\begin{itemize}
END;
		if($row_met[1]=="#"){
echo<<<END

\\item \\nogloxy{\\texttt{\\{$row_met[1]} {$row_met[2]}(
END;
		}
		else{
echo<<<END

\\item \\nogloxy{\\texttt{{$row_met[1]} {$row_met[2]}(
END;
		}
		$conn=sql_conn();
		$query="SELECT p.Nome, p.Tipo, p.Descrizione
				FROM Parametro p
				WHERE p.Metodo=$row_met[0]
				ORDER BY p.CodAuto";
		$par=mysqli_query($conn,$query) or fail("Query fallita: ".mysqli_error($conn));
		$par_desc=mysqli_query($conn,$query) or fail("Query fallita: ".mysqli_error($conn));
		if($row_par=mysqli_fetch_row($par)){
echo<<<END
{$row_par[0]}: {$row_par[1]}
END;
		}
		while($row_par=mysqli_fetch_row($par)){
echo<<<END
, {$row_par[0]}: {$row_par[1]}
END;
		}
echo<<<END
)
END;
		if($row_met[3]!=null){
echo<<<END
: {$row_met[3]}
END;
		}
echo<<<END
}}
\\\ {$row_met[4]}
END;
		if($row_desc=mysqli_fetch_row($par_desc)){
echo<<<END

\\\ \\textbf{Parametri}:
\\begin{itemize}
\\item \\nogloxy{\\texttt{{$row_desc[0]}: {$row_desc[1]}}}
\\\ {$row_desc[2]}
END;
			while($row_desc=mysqli_fetch_row($par_desc)){
echo<<<END

\\item \\nogloxy{\\texttt{{$row_desc[0]}: {$row_desc[1]}}}
\\\ {$row_desc[2]}
END;
			}
echo<<<END

\\end{itemize}
END;
		}
		while($row_met = mysqli_fetch_row($met)){
			if($row_met[1]=="#"){
echo<<<END

\\item \\nogloxy{\\texttt{\\{$row_met[1]} {$row_met[2]}(
END;
			}
			else{
echo<<<END

\\item \\nogloxy{\\texttt{{$row_met[1]} {$row_met[2]}(
END;
			}
			$conn=sql_conn();
			$query="SELECT p.Nome, p.Tipo, p.Descrizione
					FROM Parametro p
					WHERE p.Metodo=$row_met[0]
					ORDER BY p.CodAuto";
			$par=mysqli_query($conn,$query) or fail("Query fallita: ".mysqli_error($conn));
			$par_desc=mysqli_query($conn,$query) or fail("Query fallita: ".mysqli_error($conn));
			if($row_par=mysqli_fetch_row($par)){
echo<<<END
{$row_par[0]}: {$row_par[1]}
END;
			}
			while($row_par=mysqli_fetch_row($par)){
echo<<<END
, {$row_par[0]}: {$row_par[1]}
END;
			}
echo<<<END
)
END;
			if($row_met[3]!=null){
echo<<<END
: {$row_met[3]}
END;
			}
echo<<<END
}}
\\\ {$row_met[4]}
END;
			if($row_desc=mysqli_fetch_row($par_desc)){
echo<<<END

\\\ \\textbf{Parametri}:
\\begin{itemize}
\\item \\nogloxy{\\texttt{{$row_desc[0]}: {$row_desc[1]}}}
\\\ {$row_desc[2]}
END;
				while($row_desc=mysqli_fetch_row($par_desc)){
echo<<<END

\\item \\nogloxy{\\texttt{{$row_desc[0]}: {$row_desc[1]}}}
\\\ {$row_desc[2]}
END;
				}
echo<<<END

\\end{itemize}
END;
			}
		}
echo<<<END

\\end{itemize}
END;
	}
}

function requisitiTex($conn, $row){
echo<<<END

\\hypertarget{{$row[1]}}{{$row[1]}} & $row[2] & $row[3] &
END;
	if($row[4]==0){
echo<<<END
 \\textcolor{red}{\\textit{Non Soddisfatto}}
END;
	}
	else{
echo<<<END
 \\textcolor{Green}{\\textit{Soddisfatto}}
END;
	}
echo<<<END
\\\ %\hline

END;
}

function requisitiArTex($conn, $row){
	$query="SELECT u.IdUC
			FROM RequisitiUC ruc JOIN (_MapUseCase h JOIN UseCase u ON h.CodAuto=u.CodAuto) ON ruc.UC=u.CodAuto
			WHERE ruc.CodReq='$row[0]'
			ORDER BY h.Position";
	$uc=mysqli_query($conn,$query) or fail("Query fallita: ".mysqli_error($conn));
echo<<<END

\\hypertarget{{$row[1]}}{{$row[1]}} & $row[2] & $row[3] &\\hyperlink{{$row[4]}}{{$row[4]}}
END;
	while($uc_row=mysqli_fetch_row($uc)){
echo<<<END
\\newline
\\hyperref[{$uc_row[0]}]{{$uc_row[0]}}
END;
	}
echo<<<END
\\\ %\hline

END;
}

function requisitiClassiTex($conn, $row){
	$query="SELECT c.PrefixNome
			FROM RequisitiClasse rc JOIN Classe c ON rc.CodClass=c.CodAuto
			WHERE rc.CodReq='$row[0]'
			ORDER BY c.PrefixNome";
	$cl=mysqli_query($conn,$query) or fail("Query fallita: ".mysqli_error($conn));
	$cl_row=mysqli_fetch_row($cl);
	$prefix=$cl_row[0];
	$prefix=fixIntoBorder($prefix);
echo<<<END

$row[1] & \\hyperref[{$cl_row[0]}]{\\texttt{{$prefix}}}
END;
	while($cl_row=mysqli_fetch_row($cl)){
		$prefix=$cl_row[0];
		$prefix=fixIntoBorder($prefix);
echo<<<END
\\newline \\hyperref[{$cl_row[0]}]{\\texttt{{$prefix}}}
END;
	}
echo<<<END
\\\ %\hline

END;
}

function requisitiComponentiTex($conn, $row){
	$query="SELECT p.PrefixNome
			FROM RequisitiPackage rp JOIN Package p ON rp.CodPkg=p.CodAuto
			WHERE rp.CodReq='$row[0]' AND p.PrefixNome<>'Premi'
			ORDER BY p.PrefixNome";
	$pkg=mysqli_query($conn,$query) or fail("Query fallita: ".mysqli_error($conn));
	$pkg_row=mysqli_fetch_row($pkg);
	$prefix=$pkg_row[0];
	$prefix=fixIntoBorder($prefix);
echo<<<END

$row[1] & \\hyperref[{$pkg_row[0]}]{\\texttt{{$prefix}}}
END;
	while($pkg_row=mysqli_fetch_row($pkg)){
		$prefix=$pkg_row[0];
		$prefix=fixIntoBorder($prefix);
echo<<<END
\\newline \\hyperref[{$pkg_row[0]}]{\\texttt{{$prefix}}}
END;
	}
echo<<<END
\\\ %\hline

END;
}

function requisitiFontiTex($conn, $row){
	$query="SELECT u.IdUC
			FROM RequisitiUC ruc JOIN (_MapUseCase h JOIN UseCase u ON h.CodAuto=u.CodAuto) ON ruc.UC=u.CodAuto
			WHERE ruc.CodReq='$row[0]'
			ORDER BY h.Position";
	$uc=mysqli_query($conn,$query) or fail("Query fallita: ".mysqli_error($conn));
echo<<<END

\\hyperlink{{$row[1]}}{{$row[1]}} & \\hyperlink{{$row[2]}}{{$row[2]}}
END;
	while($uc_row=mysqli_fetch_row($uc)){
echo<<<END
\\newline
\\hyperref[{$uc_row[0]}]{{$uc_row[0]}}
END;
	}
echo<<<END
\\\ %\hline

END;
}

function testTex($conn, $row){
echo<<<END

\\hypertarget{{$row[1]}}{{$row[1]}} & $row[2] &
END;
	if($row[3]==0){
echo<<<END
 \\textit{Non Implementato}
END;
	}
	else{
		if($row[4]==0){
echo<<<END
 \\textit{Non Eseguito}
END;
		}
		else{
			if($row[5]==0){
echo<<<END
 \\textcolor{red}{\\textit{Non Superato}}
END;
			}
			else{
echo<<<END
 \\textcolor{Green}{\\textit{Superato}}
END;
			}
		}
	}
echo<<<END
\\\ \\hline
END;
}