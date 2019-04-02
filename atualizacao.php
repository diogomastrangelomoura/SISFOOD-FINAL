<?php include("includes/topo.php"); ?>

<?php
	error_reporting(0);
	ini_set('display_errors', 0 );
	
	
	//CRIA CAMPOS//
	$sql = $db->select("ALTER TABLE configuracoes ADD modulo_entregas INT(1) NOT NULL");
	$sql = $db->select("ALTER TABLE configuracoes ADD modulo_pontuacao INT(1) NOT NULL");
	$sql = $db->select("ALTER TABLE configuracoes ADD valor_real_ponto DOUBLE(10,2) NOT NULL");
	$sql = $db->select("ALTER TABLE configuracoes ADD valor_ponto_troca DOUBLE(10,2) NOT NULL");
	$sql = $db->select("ALTER TABLE configuracoes ADD dias_expira_pontos INT(10) NOT NULL");
	$sql = $db->select("ALTER TABLE configuracoes ADD escolhe_motoqueiro INT(10) NOT NULL");
	$sql = $db->select("ALTER TABLE configuracoes ADD impressao_avulsa_item VARCHAR(99) NOT NULL");
	$sql = $db->select("ALTER TABLE configuracoes ADD ordem_exibicao_produtos VARCHAR(20) NOT NULL");
	$sql = $db->select("ALTER TABLE aguarda_venda ADD nome_cliente VARCHAR(100) NOT NULL");
	$sql = $db->select("ALTER TABLE aguarda_venda ADD pedido_entregue TIME NOT NULL");
	$sql = $db->select("ALTER TABLE produtos_venda ADD impresso INT(10) NOT NULL");
	$sql = $db->select("ALTER TABLE aguarda_venda ADD ocupou_mesa INT(10) NOT NULL");
	$sql = $db->select("ALTER TABLE tamanhos ADD opcao_obrigatoria INT(1) NOT NULL");
	
	$sql = $db->select("ALTER TABLE lanches CHANGE codigo codigo VARCHAR(90) NOT NULL;");
	$sql = $db->select("ALTER TABLE dados_loja ADD inscricao_estadual VARCHAR(99) NOT NULL");


	$sql = $db->select("ALTER TABLE aguarda_venda ADD pedido_saiu_entrega TIME NOT NULL");

	$sql = $db->select("ALTER TABLE clientes ADD obs TEXT NOT NULL");
	$sql = $db->select("ALTER TABLE clientes ADD senha VARCHAR(99) NOT NULL");
	$sql = $db->select("ALTER TABLE clientes ADD internet INT(1) NOT NULL");
	$sql = $db->select("ALTER TABLE clientes ADD hash VARCHAR(200) NOT NULL");
	$sql = $db->select("ALTER TABLE clientes ADD codigo_renova_senha VARCHAR(20) NOT NULL");

	$sql = $db->select("UPDATE produtos_venda SET impresso='1'");
	$sql = $db->select("UPDATE configuracoes SET ordem_exibicao_produtos='codigo'");
	$sql = $db->select("DELETE FROM clientes WHERE nome='CLIENTE AVULSO' AND endereco!='' AND numero!=''");

	$sql = $db->select("ALTER TABLE opcionais2 CHANGE id_produto id_produto TEXT NOT NULL");

	$sql = $db->select("ALTER TABLE sistema ADD url_servidor_pedidos VARCHAR(999) NOT NULL");
	$sql = $db->select("ALTER TABLE aguarda_venda ADD pedido_internet INT(10) NOT NULL");
	$sql = $db->select("ALTER TABLE aguarda_venda ADD cliente_internet INT(10) NOT NULL");


	$sql = $db->select("ALTER TABLE sistema ADD aviso_update_internet INT(1) NOT NULL");
	$sql = $db->select("ALTER TABLE sistema ADD data_update_internet DATE NOT NULL");
	
	$sql = $db->select("ALTER TABLE configuracoes ADD categorias_mobile INT(1) NOT NULL");

	$sql = $db->select("ALTER TABLE usuarios ADD online INT(1) NOT NULL");
	
	$sql = $db->select("ALTER TABLE aguarda_venda ADD md5_usuario VARCHAR(99) NOT NULL AFTER id_usuario");
	$sql = $db->select("ALTER TABLE aguarda_venda ADD venda_fiscal INT(1) NOT NULL");

	$sql = $db->select("ALTER TABLE configuracoes ADD fiscal_sempre_ativo INT(1) NOT NULL");

	
	//FISCAL//
	$sql = $db->select("ALTER TABLE categorias ADD ncm_categoria VARCHAR(99) NOT NULL");
	$sql = $db->select("ALTER TABLE categorias ADD cst_categoria VARCHAR(99) NOT NULL");
	$sql = $db->select("ALTER TABLE categorias ADD cfop_categoria VARCHAR(99) NOT NULL");
	$sql = $db->select("ALTER TABLE categorias ADD csosn_categoria VARCHAR(10) NOT NULL");

	$sql = $db->select("ALTER TABLE fiscal ADD ncm_sistema VARCHAR(99) NOT NULL");
	$sql = $db->select("ALTER TABLE fiscal ADD cst_sistema VARCHAR(99) NOT NULL");
	$sql = $db->select("ALTER TABLE fiscal ADD cfop_sistema VARCHAR(99) NOT NULL");
	$sql = $db->select("ALTER TABLE fiscal ADD csosn_sistema VARCHAR(10) NOT NULL");

	$sql = $db->select("ALTER TABLE lanches ADD ncm VARCHAR(99) NOT NULL");
	$sql = $db->select("ALTER TABLE lanches ADD cst VARCHAR(99) NOT NULL");
	$sql = $db->select("ALTER TABLE lanches ADD cfop VARCHAR(99) NOT NULL");
	$sql = $db->select("ALTER TABLE lanches ADD csosn VARCHAR(10) NOT NULL");

	$sql = $db->select("ALTER TABLE fiscal ADD caminho_acbr VARCHAR(999) NOT NULL");
	$sql = $db->select("ALTER TABLE dados_loja_internet ADD tipo_abertura_loja VARCHAR(25) NOT NULL");
	$sql = $db->select("ALTER TABLE dados_loja_internet ADD loja_aberta_manual INT(1) NOT NULL");


	$sql = $db->select("ALTER TABLE configuracoes ADD modulo_entregas_pedidos VARCHAR(1) NOT NULL");
	



	$sel = $db->select("SELECT modulo_entregas_pedidos FROM configuracoes LIMIT 1");
	$lnx = $db->expand($sel);
	if($lnx['modulo_entregas_pedidos']==''){
		$sql = $db->select("UPDATE configuracoes SET modulo_entregas_pedidos='1'");	
	}


	$sql = $db->select("ALTER TABLE produtos_venda CHANGE quantidade quantidade DOUBLE(10,2) NOT NULL;");
	


	//RODA O SCRIPT//
	$nome_do_arquivo = "atualizacoes/atualizacoes.sql"; 
	if(file_exists($nome_do_arquivo)){	
		$arquivo = Array();
		$arquivo = file($nome_do_arquivo);  
		$prepara = "";  
		foreach($arquivo as $v)$prepara.=$v; 
		echo $sql = explode(";",$prepara); 
		foreach($sql as $v) $db->select($v);
		
		//RENOMEIA PARA NAO FAZER DENOVO//
		//$renomeia = 'atualizacoes/ANTIGO_'.date('d-m-y').'_atualizacoes.sql';
		//rename($nome_do_arquivo, $renomeia);
	}
	

	////TABELA DE NCM
	$sql = $db->select("SELECT id FROM fiscal_ncm LIMIT 1");
	if(!$db->rows($sql)){
		
		//RODA O SCRIPT//
		$nome_do_arquivo = "atualizacoes/tabela_ncm.sql"; 
		if(file_exists($nome_do_arquivo)){	
			$arquivo = Array();
			$arquivo = file($nome_do_arquivo);  
			$prepara = "";  
			foreach($arquivo as $v)$prepara.=$v; 
			echo $sql = explode(";",$prepara); 
			foreach($sql as $v) $db->select($v);				
		}	
	}


	////TABELA DE CSOSN
	$sql = $db->select("SELECT id FROM fiscal_relacao_csosn LIMIT 1");
	if(!$db->rows($sql)){
		
		//RODA O SCRIPT//
		$nome_do_arquivo = "atualizacoes/tabela_csosn.sql"; 
		if(file_exists($nome_do_arquivo)){	
			$arquivo = Array();
			$arquivo = file($nome_do_arquivo);  
			$prepara = "";  
			foreach($arquivo as $v)$prepara.=$v; 
			echo $sql = explode(";",$prepara); 
			foreach($sql as $v) $db->select($v);				
		}	
	}

	

	echo '<br><center><h1>SISTEMA ATUALIZADO</h1></center>';


?>	


	
<?php include("includes/rodape.php"); ?>