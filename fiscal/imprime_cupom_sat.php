<?php
require("../admin/class/class.db.php");
require("../admin/class/class.seguranca.php");
require("../includes/verifica_dados_fiscais.php");


	$caminho_acbr=$dados_fiscais['caminho_acbr'];
	@unlink("$caminho_acbr\sai.txt");	


	
	$ecf = 'SAT.ImprimirExtratoVenda("'.$arquivo_imprimir.'", "impressora")';
	
	
	$fp = fopen("$caminho_acbr\ENT.txt", "w");
	$escreve = fwrite($fp, $ecf);
	fclose($fp); 			


	$x=1;
	while($x==1){						
		if(file_exists("$caminho_acbr\sai.txt")){
			$x=2;
			//LÊ O ARQUIVO DE RESPOSTA//
			$ponteiro = fopen ("$caminho_acbr\sai.txt","r");
			echo $linha = trim(fgets($ponteiro));		

			


			fclose($ponteiro);	
			@unlink("$caminho_acbr\sai.txt");	
			exit();				
		} else {
			sleep(.2);			
		}			
	}				
	///INICIALIZA O SAT///
		

		
?>