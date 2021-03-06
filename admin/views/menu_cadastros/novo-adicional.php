<?php require("../../includes/topo.php"); ?>

<?php
$edit=0;
if(isset($id)){
	$sql = $db->select("SELECT * FROM opcionais WHERE id='$id' LIMIT 1");	
	$ln = $db->expand($sql);
	$edit=1;
}
?>

<div class="slim-pageheader">
  <ol class="breadcrumb slim-breadcrumb">
    <li class="breadcrumb-item"><a href="home">HOME</a></li>
    <li class="breadcrumb-item active" aria-current="page">ADICIONAIS</li>
  </ol>
  <h6 class="slim-pagetitle upper">
  		<?php
  			if($edit==1){
  				echo $ln['opcional'];
  			} else {
  				echo 'NOVO ADICIONAL';
  			}
  		?>
  </h6>
</div>


<form method="post" action="adicionais/save">
<div class="section-wrapper">
  		
		<input class="form-control" type="hidden" name="id" value="<?php if($edit==1){ echo $id;} else {echo 0;} ?>">

  		<div class="form-layout">
            <div class="row mg-b-25">
              <div class="col-lg-6">
                <div class="form-group">
                  <label class="form-control-label">Opcional: <span class="tx-danger">*</span></label>
                  <input class="form-control" type="text" name="opcional" required="required" value="<?php if($edit==1){ echo $ln['opcional'];} ?>">
                </div>
              </div><!-- col-4 -->
              <div class="col-lg-3">
                <div class="form-group">
                  <label class="form-control-label">Valor: <span class="tx-danger">*</span></label>
                  <input class="form-control valores" type="text"  name="preco" required="required" value="<?php if($edit==1){ echo $ln['valor'];} ?>">
                </div>
              </div><!-- col-4 -->
              

              <div class="col-lg-3">
                <div class="form-group mg-b-10-force">
                  <label class="form-control-label">Ativo: <span class="tx-danger">*</span></label>
                  <select class="form-control select2" name="ativo"  required="required">
	                    <?php
	                		if($edit==1){
	                			
	                			if($ln['ativo']==1){
	                				echo '<option value="1" selected>SIM</option>';
	                				echo '<option value="0">NÃO</option>';
	                			}	

	                			else if($ln['ativo']==0){
	                				echo '<option value="0" selected>NÃO</option>';
	                				echo '<option value="1">SIM</option>';                				
	                			}

	                		} else {
	                				echo '<option value="1" selected>SIM</option>';
	                				echo '<option value="0">NÃO</option>';

	                		}
	                	?>	
                  </select>
                </div>
              </div><!-- col-4 -->


              <div class="col-lg-12 top10">
                <label class="form-control-label">ESCOLHA EM QUAIS CATEGORIAS O ADICIONAL SERÁ EXIBIDO:</label>  
                <small class="tx-primary upper"><br>*CASO NENHUMA CATEGORIA SEJA SELECIONADA, O ADICIONAL APARECERÁ EM TODAS AS QUE FOREM PERMITIDAS.<BR>**SÃO LISTADAS ABAIXO SOMENTE AS CATEGORIAS EM QUE É PERMITIDO ADICIONAIS.</small>    
                <hr style="margin-top: 9px">
              </div>

          
              <?php
              
                $sel = $db->select("SELECT id, categoria FROM categorias WHERE ativo='1' AND adicionais='1' ORDER BY categoria");

                    if($db->rows($sel)){
                    
                   // echo '<div class="row row-xs">';  
                      
                      while($yy = $db->expand($sel)){

                        $id_cat = $yy['id'];

                        $check='';
                        if($edit==1){
                          $sel2 = $db->select("SELECT id FROM opcionais_categorias_relacao WHERE id_categoria='$id_cat' AND id_opcional='$id' LIMIT 1");  
                          if($db->rows($sel2)){
                            $check = 'checked';
                          }
                        }
                        
                        echo '<div class="col-md-3 text-center">';

                          echo '<div class="col-md-12 text-center" style="border:1px solid #efefef; margin-bottom:10px; padding:10px">';

                            echo '<input class="categoria" name="categorias[]" '.$check.' type="checkbox" value="'.$yy['id'].'"><br>';
                            echo '<span style="text-transform:uppercase; margin-top:8px; font-weight:300">'.$yy['categoria'].'</span>';

                          echo '</div>';  

                        echo '</div>';
                      }

                  //  echo '</div>';  



                    }  

                ?>
            

            </div><!-- row -->

            <div class="form-layout-footer">
              <button type="submit" class="btn btn-primary bd-0">SALVAR</button>  
              <button type="submit" onclick="javascript:salva_cadastro_insere();" class="btn btn-primary bd-0 pull-right">SALVAR E INSERIR MAIS</button>               
            </div><!-- form-layout-footer -->
          </div><!-- form-layout -->
             
  
</div>
</form>

<?php require("../../includes/rodape.php"); ?>