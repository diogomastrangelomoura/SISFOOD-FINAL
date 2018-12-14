<?php
require("../../admin/class/class.db.php");
require("../../admin/class/class.seguranca.php");
require("../../includes/verifica_session.php");
require("../../includes/verifica_venda_aberta.php");
//require("../../includes/verifica_cliente_venda.php");
require("../../includes/verifica_dados_loja.php");
require("../../includes/verifica_configuracoes_loja.php");
require("../../diversos/funcoes_impressao.php");
	




	//CABEÇALHO//	
		$txt_cabecalho = array();
        
        $txt_cabecalho[] = $dados_loja['cabecalho_linha01'];         
        $txt_cabecalho[] = $dados_loja['cabecalho_linha02'];         
		$txt_cabecalho[] = $dados_loja['cabecalho_linha03'];     
		$txt_cabecalho[] = 'PEDIDO: #'.$id_venda;    
		$txt_cabecalho[] = data_mysql_para_user($dados_venda['data_pedido']).' AS '.substr($dados_venda['pedido_inicio'],0,5);
        		
		$txt_cabecalho[] = '----------------------------------------';

		//ENTREGA
		if($dados_venda['entrega']!=0){
			$txt_cabecalho[] = 'ENTREGA';
		} 

		//MESA
		if($dados_venda['id_mesa']!=0){
			$txt_cabecalho[] = 'MESA '.$dados_venda['id_mesa'];
		} 

		//RETIRADA/BALCAO
		if($dados_venda['id_mesa']==0 && ($dados_venda['entrega']==0 || $dados_venda['entrega']=='')){
			$txt_cabecalho[] = 'RETIRA/BALCAO';
			
			//EMBALA PARA VIAGEM
			if($dados_venda['embala_viagem']==1){					
				$txt_cabecalho[] = '(EMBALAR PARA VIAGEM)';
			}

		} 
	

		$cabecalho = array_map("centraliza", $txt_cabecalho);
	//CABEÇALHO



	////ITENS DO PEDIDO////
	$tot_itens = 0;


	$txt_itens_cabecalho[] = array('----', '------------------------------', '-------', '-------');
    $txt_itens_cabecalho[] = array('Qtd ', 'COD/Produto', 'V. UN', 'Total');
	$txt_itens_cabecalho[] = array('----', '------------------------------', '-------', '-------');	

	
		
		$total_itens_pedido=0;
		$sel = $db->select("SELECT * FROM produtos_venda WHERE id_venda='$id_venda' ORDER BY categoria_produto, id DESC");	
		
		if($db->rows($sel)){
						
			while($row = $db->expand($sel)){

				$total_itens_pedido = ($total_itens_pedido+$row['quantidade']);


				$id_selecionado = $row['id'];
				$nome_tamanho='';
				$total_prod = 0;
				$id_produto	= $row['id_produtos'];
				$id_tamanho = $row['tamanho'];
				$id_controle = $row['id'];

				//APENAS UM PRODUTO
				if(is_numeric($row['id_produtos'])){

					$pg = $db->select("SELECT produto, categoria, codigo FROM lanches WHERE id='$id_produto' LIMIT 1");
					$var = $db->expand($pg);					
					$nome_produto= $var['produto'];
					$categoria_produto= $var['categoria'];
					$codigo_produto= $var['codigo'];

        			//VERIFICA SE É PRA IMPRIMIR O CODIGO DO PRODUTO//
        			$sun = $db->select("SELECT categoria, imprime_codigo FROM categorias WHERE id='$categoria_produto' LIMIT 1");
        			$type = $db->expand($sun);
        			$nome_categoria = $type['categoria'];

        			if($type['imprime_codigo']==1){        				
        				$prod_cod= '('.$codigo_produto.') - ';
	        		} else {
	        			$prod_cod= '';
	        		}

				//MEIO A MEIO	
				} else {	

					$nome_produto='';
					$prod_cod='';
					$prods = explode(',', $row['id_produtos']);	
					foreach($prods as $prod) {

				    	$id_produto = trim($prod);		    	

				    	$pg = $db->select("SELECT produto, categoria, codigo FROM lanches WHERE id='$id_produto' LIMIT 1");
						$var = $db->expand($pg);
						$nome_produto= $nome_produto.$var['produto'].'/';
						$categoria_produto= $var['categoria'];
						$codigo_produto= $var['codigo'];

						$prod_cod = $prod_cod.$codigo_produto.'/';


					}					


					//REMOVE A ULTIMA VIRGULA
					$final = substr($prod_cod, -1);
					if($final=='/'){
						$size = strlen($prod_cod);
						$prod_cod = substr($prod_cod,0, $size-1);
					}

					//VERIFICA SE É PRA IMPRIMIR O CODIGO DO PRODUTO//
					$sun = $db->select("SELECT categoria, imprime_codigo FROM categorias WHERE id='$categoria_produto' LIMIT 1");
        			$type = $db->expand($sun);
        			$nome_categoria = $type['categoria'];
        			
	        		if($type['imprime_codigo']==1){	        			
	        			$prod_cod= '('.$prod_cod.') - ';
	        		} else {
	        			$prod_cod= '';
	        		}

					//REMOVE A ULTIMA VIRGULA
					$final = substr($nome_produto, -1);
					if($final=='/'){
						$size = strlen($nome_produto);
						$nome_produto = substr($nome_produto,0, $size-1);
					}
					
				}	


				///NOME DO TAMANHO
				$nome_tamanho='';
			    if($id_tamanho!=0){
			    	$sun2 = $db->select("SELECT tamanho FROM tamanhos WHERE id='$id_tamanho' LIMIT 1");
			        $type2 = $db->expand($sun2);
			        $nome_tamanho=' ('.$type2['tamanho'].')';	
			    }

				$prod = retira_acentos($nome_produto.$nome_tamanho);
				$total_prod = ($row['quantidade']*$row['valor']);
				if($row['quantidade']<10){$row['quantidade'] = '0'.$row['quantidade'];}


				$name_divisao = ''.retira_acentos($row['nome_cliente_divisao']).'';
			
				$txt_itens[] = array($row['quantidade'], ''.($prod_cod).''.retira_acentos($nome_categoria).'', ''.number_format($row['valor'],2,",",".").'', ''.number_format($total_prod,2,",",".").'', ''.retira_acentos($nome_produto).'', ''.retira_acentos($nome_tamanho).'',$id_controle, ''.$row['observacoes'].'',$name_divisao);



				$pg = $db->select("UPDATE produtos_venda SET impresso='1' WHERE id='$id_selecionado' LIMIT 1");	
				

			}


			

		}



	


	foreach ($txt_itens as $item) {
       	
		//VEM NOME DO CLIENTE DA DIVISAO NA MESA
		if(!empty($item[8])){
			$itens[] .= addEspacos('('.$item[8].')', 40, 'F')."\r\n";			    
		}

        $itens[] .= addEspacos($item[0], 4, 'F')
        	. addEspacos($item[1], 22, 'F')
        	. addEspacos($item[2], 7, 'I')
            . addEspacos($item[3], 7, 'I');        	
        	  

        	if($item[5]!=''){
        		$itens[] .= addEspacos('', 4, 'F')
        		.addEspacos($item[5], 36, 'F')
        		."\r\n".addEspacos('', 4, 'F')
        		.addEspacos($item[4], 36, 'F');        		
        	} else {
        		$itens[] .= addEspacos('', 4, 'F')
        		.addEspacos($item[4], 36, 'F');
        	}


        	//OPCOES SE HOUVER//
        		$id_pesq_controle = $item[6];
				$peg = $db->select("SELECT opcionais_produtos_venda2.*, opcionais2.opcional2 FROM opcionais_produtos_venda2
				LEFT JOIN opcionais2 ON opcionais_produtos_venda2.id_opcional=opcionais2.id
				WHERE opcionais_produtos_venda2.id_controle='$id_pesq_controle' ORDER BY opcionais_produtos_venda2.id
				");
				if($db->rows($peg)){

					while($ln = $db->expand($peg)){
						
						$opcional = retira_acentos($ln['opcional2']);
						$val_opcional = $ln['valor_opcional'];
						$total_opcional = ($item[0]*$val_opcional);												

						$itens[] .= addEspacos('+', 4, 'F')
			        	. addEspacos($opcional, 22, 'F')
			        	. addEspacos(number_format($val_opcional,2,",","."), 7, 'I')
			            . addEspacos(number_format($total_opcional,2,",","."), 7, 'I');    	
																		
					}
				}
			//OPCOES SE HOUVER//	


        	//ADICIONAIS SE HOUVER//
        		$id_pesq_controle = $item[6];
				$peg = $db->select("SELECT opcionais_produtos_venda.*, opcionais.opcional FROM opcionais_produtos_venda
				LEFT JOIN opcionais ON opcionais_produtos_venda.id_opcional=opcionais.id
				WHERE opcionais_produtos_venda.id_controle='$id_pesq_controle' ORDER BY opcionais_produtos_venda.id
				");
				if($db->rows($peg)){

					$itens[] .= addEspacos('', 4, 'F')
					. addEspacos('[ADICIONAR AO ITEM]', 22, 'F');

					while($ln = $db->expand($peg)){
						
						$opcional = retira_acentos($ln['opcional']);
						$val_opcional = $ln['valor_opcional'];
						$total_opcional = ($item[0]*$val_opcional);												

						$itens[] .= addEspacos('+', 4, 'F')
			        	. addEspacos($opcional, 22, 'F')
			        	. addEspacos(number_format($val_opcional,2,",","."), 7, 'I')
			            . addEspacos(number_format($total_opcional,2,",","."), 7, 'I');    	
																		
					}
				}
			//ADICIONAIS SE HOUVER//



			//OBSERVACOES DO PRODUTO SE HOUVER
			if(!empty($item[7])){
				$itens[] .= addEspacos('', 4, 'F')
					. addEspacos('[ATENCAO]', 22, 'F');

				$itens[] .= addEspacos('', 4, 'F')
			    . addEspacos($item[7], 36, 'F');			    
			}	
			

        	$itens[] .= addEspacos('------------------------------------------------------------------', 40, 'F');
            
            
    }

    foreach ($txt_itens_cabecalho as $cab) {
       
        $cabs[] = addEspacos($cab[0], 4, 'F')
        	. addEspacos($cab[1], 22, 'F')           
            . addEspacos($cab[2], 7, 'I')
            . addEspacos($cab[3], 7, 'I');
            
    }



    // SUBTOTAL //
    $aux_valor_total = 'SUBTOTAL';
	$aux_valor_total2 = 'R$ '.number_format(($dados_venda['valor_produtos']+$dados_venda['valor_acrescimos']),2,",",".");
	$total_espacos = $n_colunas - strlen($aux_valor_total);
	$total_espacos = $total_espacos- strlen($aux_valor_total2);
    $espacos = ''; 
    for($i = 0; $i < $total_espacos; $i++){
    	$espacos .= ' ';
    }
	$txt_valor_total = $aux_valor_total.$espacos.$aux_valor_total2;
    // SUBTOTAL //


    // DESCONTO //
    if($dados_venda['valor_desconto']!='0.00'){
	    $aux_valor_total = 'DESCONTO (-)';
		$aux_valor_total2 = 'R$ '.number_format($dados_venda['valor_desconto'],2,",",".");
		$total_espacos = $n_colunas - strlen($aux_valor_total);
		$total_espacos = $total_espacos- strlen($aux_valor_total2);
	    $espacos = ''; 
	    for($i = 0; $i < $total_espacos; $i++){
	    	$espacos .= ' ';
	    }
		$txt_valor_desconto = $aux_valor_total.$espacos.$aux_valor_total2. "\r\n";

		$aux_valor_total = 'TOTAL:';
		//$aux_valor_total2 = 'R$ '.number_format(($dados_venda['valor_total']-$dados_venda['valor_desconto']),2,",",".");
		$aux_valor_total2 = 'R$ '.number_format($dados_venda['valor_final_venda'],2,",",".");	
		$total_espacos = $n_colunas - strlen($aux_valor_total);
		$total_espacos = $total_espacos- strlen($aux_valor_total2);
		$espacos = ''; 
		for($i = 0; $i < $total_espacos; $i++){
			$espacos .= ' ';
		}
		$txt_valor_desconto = $txt_valor_desconto.$aux_valor_total.$espacos.$aux_valor_total2."\r\n";

	} else {
		$txt_valor_desconto ='';
	}	
    // DESCONTO //


    // ENTREGA //
    if($dados_venda['entrega']!=0){
    	$aux_valor_total = 'TAXA DE ENTREGA (+)';
		$aux_valor_total2 = 'R$ '.number_format($dados_venda['valor_entrega'],2,",",".");
		$total_espacos = $n_colunas - strlen($aux_valor_total);
		$total_espacos = $total_espacos- strlen($aux_valor_total2);
		$espacos = ''; 
		for($i = 0; $i < $total_espacos; $i++){
			$espacos .= ' ';
		}
		$txt_valor_entrega = $aux_valor_total.$espacos.$aux_valor_total2."\r\n";
	} else {
		$txt_valor_entrega='';
	}		
    // ENTREGA //


	// A RECEBER FINAL //    
    $aux_valor_total = 'TOTAL A RECEBER (=)';
	$aux_valor_total2 = 'R$ '.number_format($dados_venda['valor_final_venda'],2,",",".");
	$total_espacos = $n_colunas - strlen($aux_valor_total);
	$total_espacos = $total_espacos- strlen($aux_valor_total2);
	$espacos = ''; 
	for($i = 0; $i < $total_espacos; $i++){
		$espacos .= ' ';
	}
	$txt_valor_final_receber = $aux_valor_total.$espacos.$aux_valor_total2."\r\n";			
    // A RECEBER FINAL //
     

	//FORMAS DE PAGAMENTO SE HOUVER//	
    $sel = $db->select("SELECT pagamentos_vendas.*, formas_pagamento.forma, usuarios.nome FROM pagamentos_vendas 
		LEFT JOIN formas_pagamento ON pagamentos_vendas.forma_pagamento=formas_pagamento.id
	    LEFT JOIN usuarios ON pagamentos_vendas.id_usuario=usuarios.id
	    WHERE pagamentos_vendas.id_venda='$id_venda'
	    ORDER BY pagamentos_vendas.id");
		
		if($db->rows($sel)){

			$txt_formas_pgto = array();
		    $txt_formas_pgto[] = '----------------------------------------';
		    $txt_formas_pgto[] = 'PAGAMENTOS RECEBIDOS';         
		    $txt_formas_pgto[] = '----------------------------------------';
		    $formas_pgto = array_map("centraliza", $txt_formas_pgto);
		  
		    $txt_pgto_recebidos='';
		    $total_ja_recebido=0;

			while($dados_pgto = $db->expand($sel)){	

				$aux_valor_total = retira_acentos($dados_pgto['forma'].' (-)');
				$aux_valor_total2 = 'R$ '.number_format($dados_pgto['valor_caixa_real'],2,",",".");
				$total_espacos = $n_colunas - strlen($aux_valor_total);
				$total_espacos = $total_espacos- strlen($aux_valor_total2);
				$espacos = ''; 
				for($i = 0; $i < $total_espacos; $i++){
					$espacos .= ' ';
				}
				$total_ja_recebido = ($total_ja_recebido+$dados_pgto['valor_caixa_real']);
				$txt_pgto_recebidos .= $aux_valor_total.$espacos.$aux_valor_total2."\r\n";	

			}

			 $txt_pagamentos_recebidos =  "\r\n".$txt_pgto_recebidos;

			 $aux_valor_total = 'TOTAL RECEBIDO:';
			 $aux_valor_total2 = 'R$ '.number_format($total_ja_recebido,2,",",".");
			 $total_espacos = $n_colunas - strlen($aux_valor_total);
			 $total_espacos = $total_espacos- strlen($aux_valor_total2);
			 $espacos = ''; 
			 for($i = 0; $i < $total_espacos; $i++){
				$espacos .= ' ';
			 }

			 $falta_receber = ($dados_venda['valor_final_venda']-$total_ja_recebido);
			 if($falta_receber<0){$falta_receber=0;}

			 $txt_pagamentos_recebidos .=  $aux_valor_total.$espacos.$aux_valor_total2;	 

			 $aux_valor_total = 'RESTANTE A RECEBER:';
			 $aux_valor_total2 = 'R$ '.number_format($falta_receber,2,",",".");
			 $total_espacos = $n_colunas - strlen($aux_valor_total);
			 $total_espacos = $total_espacos- strlen($aux_valor_total2);
			 $espacos = ''; 
			 for($i = 0; $i < $total_espacos; $i++){
				$espacos .= ' ';
			 }

			 $txt_pagamentos_recebidos .=  "\r\n".$aux_valor_total.$espacos.$aux_valor_total2."\r\n";
			 

		} else {
			$txt_pagamentos_recebidos='';
			$formas_pgto='';
		}    
	//FORMAS DE PAGAMENTO SE HOUVER//


	//SE FOR ENTREGA EXIBE O ENDEREÇO E DADOS DO COMPRADOR//	
	if($dados_venda['entrega']!=0){

		$txt_dados_entrega = array();
		$txt_dados_entrega[] = '----------------------------------------';
		$txt_dados_entrega[] = 'DADOS PARA ENTREGA';         
		$txt_dados_entrega[] = '----------------------------------------';
		$txt_dados_entrega = array_map("centraliza", $txt_dados_entrega);

		$id_cliente = $dados_venda['id_cliente'];
		$selectx = $db->select("SELECT * FROM clientes WHERE id='$id_cliente' LIMIT 1");
		$dados_cliente = $db->expand($selectx);

		$dados_entrega = "\r\n".retira_acentos($dados_cliente['nome'])."\r\n";
		$dados_entrega .= 'FONE: ('.$dados_cliente['ddd'].') '.$dados_cliente['telefone']."\r\n";
		$dados_entrega .= retira_acentos($dados_cliente['endereco'].', '.$dados_cliente['numero'])."\r\n";
		$dados_entrega .= retira_acentos($dados_cliente['bairro'])."\r\n";
		if(!empty($dados_cliente['complemento'])){	
			$dados_entrega .= retira_acentos($dados_cliente['complemento'])."\r\n";
		}
		

		$dados_entrega .= '----------------------------------------'."\r\n";

		if($dados_venda['levar_maquina_cartao']!=0){
			$dados_entrega .= 'LEVAR A MAQUINA DE CARTAO'."\r\n";	
		}

		if($dados_venda['troco_para']!='0.00'){
			$dados_entrega .= '*LEVAR TROCO PARA: R$ '.number_format($dados_venda['troco_para'],2,",",".").' / (R$ '.number_format($dados_venda['levar_troco'],2,",",".").')'."\r\n";	
		}

	}	else {
		
		$txt_dados_entrega = array();
		$txt_dados_entrega[] = '----------------------------------------';
		$txt_dados_entrega[] = 'DADOS DO CLIENTE';         
		$txt_dados_entrega[] = '----------------------------------------';
		$txt_dados_entrega = array_map("centraliza", $txt_dados_entrega);

		$id_cliente = $dados_venda['id_cliente'];
		$selectx = $db->select("SELECT * FROM clientes WHERE id='$id_cliente' LIMIT 1");
		$dados_cliente = $db->expand($selectx);

		$dados_entrega = "\r\n".retira_acentos($dados_cliente['nome'])."\r\n";
		
		if(!empty($dados_cliente['telefone'])){					
			$dados_entrega .= 'FONE: ('.$dados_cliente['ddd'].') '.$dados_cliente['telefone']."\r\n";
		}
		
	}
	//SE FOR ENTREGA EXIBE O ENDEREÇO E DADOS DO COMPRADOR//	


	//EMBALA VIAGEM//
	if($dados_venda['embala_viagem']==1){
		$dados_entrega .= 'EMBALAR PARA VIAGEM'."\r\n";	
	}


	//IMPRIME O TOTAL DE ITENS DO PEDIDO//
	if($total_itens_pedido!=0){
		$dados_entrega .= '----------------------------------------'."\r\n";
		$dados_entrega .= 'TOTAL DE ITENS DO PEDIDO: '.$total_itens_pedido."\r\n";	
	}

	//IMPRIME AS CATEGORIAS DO PEDIDO//
	$dados_entrega .= '----------------------------------------'."\r\n";
	$dados_entrega .= 'PEDIDO CONTENDO:'."\r\n";	

	$categorias_pedido_gerais='';
	$select = $db->select("SELECT produtos_venda.categoria_produto, categorias.categoria AS nome_categoria FROM produtos_venda
		LEFT JOIN categorias ON produtos_venda.categoria_produto=categorias.id
		WHERE produtos_venda.id_venda='$id_venda' 
		GROUP BY produtos_venda.categoria_produto");
	while($ln = $db->expand($select)){
		$categorias_pedido_gerais .= $ln['nome_categoria'].'/';	
	}
	
	//REMOVE A ULTIMA BARRA DO NOME DA CATEGORIA
	$final = substr($categorias_pedido_gerais, -1);
	if($final=='/'){
		$size = strlen($categorias_pedido_gerais);
		$categorias_pedido_gerais = substr($categorias_pedido_gerais,0, $size-1);
	}

	$dados_entrega .= retira_acentos($categorias_pedido_gerais)."\r\n";	



	///GERA O ARQUIVO	
	$txt = implode("\r\n", $cabecalho)
	. "\r\n"
	.implode("\r\n", $cabs)
	. "\r\n"
	. implode("\r\n", $itens)
	. "\r\n"
	. $txt_valor_total // SubTotal	
	."\r\n"
	. $txt_valor_desconto // Desconto	
	.$txt_valor_entrega	//Entrega
    .$txt_valor_final_receber //Final	
	.implode("\r\n", $formas_pgto)
	.$txt_pagamentos_recebidos
	.implode("\r\n", $txt_dados_entrega)
	.$dados_entrega;

   //CAMINHO DO TXT CRIADO
   $file = '../../pedidos_imprimir/pedido.txt';

   // cria o arquivo
   $_file  = fopen($file,"w");
   fwrite($_file,$txt);
   fclose($_file);



?>