<?php 
  //llamado a la clase de conexion
  require_once 'Conexion/conexion.php';
  require_once 'head_listar.php';

  $datosTercero = "";
  $id = $_SESSION['id_tercero'];

  if($_SESSION['perfil'] == "N"){
    //Consulta para el listado de registro de la tabla gf_tercero para naturales.
    $queryTercero = "SELECT t.NombreUno,t.NombreDos, t.ApellidoUno, t.ApellidoDos, CONCAT('(', ti.Nombre, ': ', t.NumeroIdentificacion, ')') identificacion 
      FROM gf_tercero t 
      LEFT JOIN gf_tipo_identificacion ti ON t.TipoIdentificacion = ti.Id_Unico 
      WHERE t.Id_Unico =$id";
    }
      elseif($_SESSION['perfil'] == "J")
    {
      //Consulta para el listado de registro de la tabla gf_tercero para jur�dicos.
      $queryTercero = "SELECT t.razonsocial, CONCAT(', ',s.nombre) sucursal, CONCAT('(', ti.Nombre, ': ', t.NumeroIdentificacion, ')') identificacion 
      FROM gf_tercero t
      LEFT JOIN gf_tipo_identificacion ti ON t.tipoidentificacion = ti.id_unico 
      LEFT JOIN gf_sucursal s ON t.sucursal = s.id_unico 
      WHERE t.Id_Unico = $id";
    }

  $tercero = $mysqli->query($queryTercero);
  $rowTer = mysqli_fetch_row($tercero);
    foreach ($rowTer as $i)
    {
      $datosTercero .= $i." ";
    }

  // llamado al encanbezado del listar

 ?>
<!-- select2 -->
<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>

<script>
$().ready(function() {
  var validator = $("#form").validate({
      ignore: "",
    errorPlacement: function(error, element) {
      
      $( element )
        .closest( "form" )
          .find( "label[for='" + element.attr( "id" ) + "']" )
            .append( error );
    },
  });

  $(".cancel").click(function() {
    validator.resetForm();
  });
});
</script>
<style>
label#valor-error, #tel-error,#txtObservacion-error,#sltClaseR-error,#sltTipoR-error{
    display: block;
    color: #155180;
    font-weight: normal;
    font-style: italic;

}
</style>
<title>Registrar Retención</title>
</head>
<body>
	<div class="container-fluid text-center">	
		<div class="row content">

<!--Lllamado al menu    --> 				
			<?php require_once 'menu.php'; ?>
			<div class="col-sm-8 text-left">
				 <h2 id="forma-titulo3" align="center" style="margin-bottom: 5px; margin-right: 4px; margin-left: 4px; margin-top:5px">Registrar Tercero Retenci&oacute;n</h2>
<!-- Bot�n volver -->          
        <a href="<?php echo $_SESSION['url'];?>" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
<!-- Nombre del tercero -->
	<h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:5px;  background-color: #0e315a; color: white; border-radius: 5px"><?php echo ucwords((strtolower($datosTercero))); 
          ?></h5>
<!-- Caja para REGISTRAR la informacion -->
       <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form contenedorForma col-sm-12"> 

          <form name="form" id="form" method="POST" class="form-inline" enctype="multipart/form-data" action="json/registrarTerceroRetencionJson.php">

              <p align="center" style="margin-bottom: 10px; margin-top:10px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>

              <input type="hidden" name="tercero" value="<?php echo $id;?>">

<!-- Combo Clase Rtencion -->
      <div  class="form-group form-inline col-sm-12" style=" margin-bottom: 0px; padding: 0px;">

        <!--    -->
            <div class="col-sm-3" align="right">
              <label for="sltClaseR" class="control-label"><strong style="color:#03C1FB;">*</strong>Clase Retenci&oacute;n:</label>
            </div>
            
          <div class="col-sm-3">
            <select  name="sltClaseR" id="sltClaseR" class="select2_single form-control " title="Seleccione el Clase Retenci&oacute;n" required="required" style="width:180px" onchange="javascript: claseRet(0);">
                <option value="">Clase Retenci&oacute;n</option>
                <?php 

              //consulta para trear los datos del combo de Clase Rtencion
                $tipoA = "SELECT id_unico, nombre FROM gf_clase_retencion ORDER BY nombre ASC";
                $tipoAct = $mysqli->query($tipoA);

                while($rowC = mysqli_fetch_assoc($tipoAct)){?>
                <option value="<?php echo $rowC['id_unico'] ?>"><?php echo ucwords((strtolower($rowC['nombre'])));}?></option>;
              </select> 
         
            </div>
<!-- Combo Tipo Rtencion -->
            <div class="col-sm-3" align="right">
              <label for="sltTipoR" class="control-label"><strong style="color:#03C1FB;">*</strong>Tipo Retenci&oacute;n:</label>
            </div>
            
          <div class="col-sm-3">
            <select  name="sltTipoR" id="sltTipoR" class="select2_single form-control " title="Seleccione el Tipo Retenci&oacute;n" required="required" style="width:180px">
                <option value="">Tipo Retenci&oacute;n</option>
                
              </select> 
         
            </div>
              
                  
<!-- campo VALOR-->                  
          <br>
          <br>
          <br>
          <br>
          <div class="col-sm-3" align="right">
            <label for="valor" class="control-label" style="margin-right: 0px; margin-top: 0px;"><strong style="color:#03C1FB;">*</strong>Valor:</label>
          </div>
            
          <div class="col-sm-3">
            <input type="text" name="valor" id="valor" class="form-control" maxlength="20" title="Ingrese el valor" onkeypress="return txtValida(event,'num')" placeholder="Valor" style="width:180px" required>
          </div>
<!-- campo Observacion-->   
          <div class="col-sm-3" align="right">
            <label for="txtObservacion" class="control-label" style="margin-right: 0px; margin-top: 0px;"><strong style="color:#03C1FB;">*</strong>Observaciones:</label>
          </div>
            
          <div class="col-sm-3">
          <input type="text" name="txtObservacion" id="txtObservacion" class="form-control" maxlength="500" title="Ingrese el Observaciones" placeholder="Observaciones" style="width:180px" required>
          </div>
          <br>
          <br>
          <br>
          <br>
          <div class="col-sm-11" align="right">
          <button type="submit" class="btn btn-primary sombra form-control" >Guardar</button>        
          </div>
                  
            
            <!--   
             <input type="hidden" name="tercero" value="<?php //echo $id ?>">-->
         
             </div>          

              <div align="center"></div>
              <div class="texto" style="display:none"></div>
              <input type="hidden" name="MM_insert" >
          </form>       
  </div>

<!--  tabla para LISTAR la informacion -->                                   
       <div align="center" class="table-responsive col-sm-12" style="margin-left: 5px; margin-right: 5px; margin-top: 10px; margin-bottom: 5px;">          
          <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
            <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
              
              <thead>
                <tr>
                <td class="oculto">Identificador</td>
                <td width="7%"></td>
                <td class="cabeza"><strong>Clase Retenci&oacute;n</strong></td>
                <td class="cabeza"><strong>Tipo Retenci&oacute;n</strong></td>
                <td class="cabeza"><strong>Valor</strong></td>
                <td class="cabeza"><strong>Observaciones</strong></td>
                </tr>

                <tr>
                <th class="oculto">Identificador</th>
                <th width="7%"></th>
                <th>Clase Retenci&oacute;n</th>
                <th>Tipo Retenci&oacute;n</th>
                <th>Valor</th>
                <th>Observaciones</th>
                </tr>
              </thead>

              <tbody>   
                <?php

              //consulta para traer los datos a listar
                $tipoA2 = "SELECT tr.id_unico,cr.nombre,trr.nombre,tr.valor,tr.observaciones 
                          FROM gf_tercero_retencion tr 
                          LEFT JOIN  gf_clase_retencion cr ON  cr.id_unico = tr.clase_retencion
                          LEFT JOIN  gf_tipo_retencion  trr ON trr.id_unico=tr.tipo_retencion 
                          WHERE tr.tercero = $id";
                $tipoAct2 = $mysqli->query($tipoA2);


                while ($row = mysqli_fetch_row($tipoAct2)) { ?>
                  
                  <tr>               
                    <td style="display: none;"><?php echo $row[0]?></td>
                    <td align="center" class="campos">
                      <a href="#" onclick="javascript:eliminarItem(<?php echo $row[0];?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>
                    </td>
                    <td class="campos"><?php echo($row[1])?></td>
                    <td class="campos"><?php echo($row[2])?></td>
                    <td class="campos"><?php echo($row[3])?></td>
                    <td class="campos"><?php echo($row[4])?></td>
                  </tr>
                <?php
                }
                 ?>
              </tbody>
            </table>
          </div>
		  </div>
		  
         </div> <!-- Cierra col-sm-8 text-left -->
     
			
    			
<!--  Botones opcionales del lado derecho  -->
			<div class="col-sm-2 text-center" align="center">
				<h2 id="forma-titulo3" align="center" style="margin-bottom: 5px; margin-right: 4px; margin-left: 4px; margin-top:5px">Adicional</h2>
			
				<div  align="center">
					<a href="GF_CLASE_RETENCION.php" class="btn btn-primary sombra" style="margin-left:10px; margin-top:5px">CLASE RETENCIÓN</a>
				</div>
        <div  align="center">
					<a href="Registrar_GF_TIPO_RETENCION.php" class="btn btn-primary sombra" style="margin-left:10px; margin-top:5px">TIPO RETENCIÓN</a>
				</div>
			</div>
		  
     
  </div> <!-- Cierra row content -->
</div> <!-- Cierra container-fluid text-center --> 

<!--  LLamado al pie de pagina -->	
	<?php require_once 'footer.php'; ?>

<!--  MODAL y opcion  MODIFICAR  informacion  -->  
<div class="modal fade" id="myModalUpdate" role="dialog" align="center" >
  <div class="modal-dialog">
    <div class="modal-content client-form1">
      <div id="forma-modal" class="modal-header">       
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Modificar</h4>
      </div>

<!-- Consulta para modificar el combo TIPO TELEFONO   -->
      <?php 
      $tipoA3 = "SELECT id_unico, nombre FROM gf_tipo_telefono ORDER BY nombre ASC";
      $tipoAct3 = $mysqli->query($tipoA3);
       ?>

      <div class="modal-body ">
        <form id="frm2" name="frm2" method="POST" action="javascript:modificarItem()">
          <input type="hidden" id="tipoteledi" name="tipoteledi">
          <input type="hidden" id="valorAx" name="valorAx">
          <div class="form-group" style="margin-top: 13px;">
            <label style="display:inline-block; width:137px; padding-left: 27px;"><strong style="color:#03C1FB;">*</strong>Tipo Tel&eacute;fono:</label>
            <select style="display:inline-block; width:252px; margin-bottom:15px; height:40px" name="tipoActmodal" id="tipoActmodal" class="select2_single " title="Seleccione Tipo Tel&eacute;fono" required="required">
                <?php while ($modTel = mysqli_fetch_row($tipoAct3)) { ?>
                      <option value="<?php echo $modTel[0]; ?>">
                        <?php
                        echo ucwords((strtolower($modTel[1]))); 
                         ?>
                      </option>
                <?php  

                 } ?>
            </select>                                
          </div>

        

          <div class="form-group" >

          <table style="margin-right: 10px;">
          <tr>
            <td align="right">
              
                 <label for="valor"  style=" width:140px; margin-right: 16px; margin-bottom: 30px; margin-left: -6px;" ><strong style="color:#03C1FB;">*</strong>Valor:</label></td>
              <td>
                <input type="text" name="valorA" id="valorA" class="form-control" maxlength="20" title="Ingrese el valor" onkeypress="return txtValida(event,'num')" placeholder="Valor" style="width:252px" required>
              </td>

          </tr>
            
          </table>

           

          </div>

           <input type="hidden" id="id" name="id">  
      </div>


      <div id="forma-modal" class="modal-footer">
          <button type="submit" class="btn" style="color: #000; margin-top: 2px">Guardar</button>
        <button class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>       
      </div>
      </form>
    </div>
  </div>
</div>



<!--  MODAL para los mensajes del  modificar -->

<div class="modal fade" id="myModal5" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Informaci&oacuten</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
            <p>Informaci&oacuten modificada correctamente.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver5" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="myModal6" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Informaci&oacuten</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
           <p>La informaci&oacuten no se ha podido modificar.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver6" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>

<!--  MODAL para los mensajes de la opcion  eliminar -->

   <div class="modal fade" id="myModal" role="dialog" align="center" >
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">      
        <p>¿Desea eliminar el registro seleccionado de Retención?</p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="ver" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="myModal1" role="dialog" align="center" >
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Informaci&oacuten</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        
          <p>Informaci&oacute;n eliminada correctamente.</p>

      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="myModal2" role="dialog" align="center" >
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Informaci&oacuten</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>No se pudo eliminar la informaci&oacuten, el registro seleccionado est&aacute siendo utilizado por otra dependencia.</p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="myModalrd" role="dialog" align="center" >
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Informaci�n</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>El valor ya  existe.</p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
      </div>
    </div>
  </div>
</div>
<script src="js/select/select2.full.js"></script>

  <script>
    $(document).ready(function() {
      $(".select2_single").select2({
        
        allowClear: true
      });
    });
  </script>

<!-- librerias -->
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>
<!-- Funci�n para retornar al formulario principal. -->
<script type="text/javascript">

  $("#ver5").click(function(){
   
   document.location = "GF_TELEFONO.php?id=<?php echo $id ?>";
 });

$("#ver1").click(function(){
   
   document.location = "GF_TERCERO_RETENCION.php";
 });

$("#ver2").click(function(){
   
   document.location =  "GF_TERCERO_RETENCION.php";
 });

</script>

<!-- Funci�n para la opcion modificar. -->

   <script type="text/javascript">
  function modificarModal(id,tipoA,valor,tipotel){
    
    $("#tipoActmodal").val(tipotel);
    $("#tipoteledi").val(tipoA);
    document.getElementById('id').value = id;
    document.getElementById('valorA').value = valor;
    document.getElementById('valorAx').value = valor
    $("#myModalUpdate").modal('show');
  }
  function modificarItem()
    {
      var result = '';
      var id= document.getElementById('tipoteledi').value;
      var tipoActi= document.getElementById('tipoActmodal').value;
      var val=document.getElementById('valorA').value;
      var valx=document.getElementById('valorAx').value;
      console.log(valor);
      $.ajax({
        type:"GET",
        url:"json/modificarTelefono.php?p1="+id+"&p2="+tipoActi+"&p3="+val+'&p4='+valx,
        success: function (data) {
          result = JSON.parse(data);
          if(result==1){
            $("#myModalUpdate").modal('hide');
            $("#myModal5").modal('show');
            $("#ver5").click(function(){
            $("#myModal5").modal('hide');              
            });
          }else if (result ==0){
            $("#myModal6").modal('show');
          }else if (result ==2){
            $("#myModalrd").modal('show');
          }
        }
      });
    }

</script>

<!-- Funci�n para la opcion eliminar -->

<script type="text/javascript">
  function eliminarItem(id)
  {
   var result = '';
   $("#myModal").modal('show');
   $("#ver").click(function(){
    $("#myModal").modal('hide');
    $.ajax({
      type:"GET",
      url:"json/eliminarTerceroRetencion.php?id="+id,
      success: function (data) {
        result = JSON.parse(data);
        if(result==true)
          $("#myModal1").modal('show');
        else
          $("#myModal2").modal('show');
      }
    });
  });
 }
</script>
<script type="text/javascript"> 
        function claseRet() {
        

            var opcion = '<option value="">Tipo Retención</option>';
            if (($("#sltClaseR").val() != "") && ($("#sltClaseR").val() != 0)){
                var id_clas_rt = $("#sltClaseR").val();
                console.log(id_clas_rt);
                var form_data = {estruc: 1, id_clase_ret: id_clas_rt};
                $.ajax({
                    type: "POST",
                    url: "estructura_aplicar_retenciones.php",
                    data: form_data,
                    success: function (response){
                        if (response != 0 && response != "") {
                            opcion += response;
                            $("#sltTipoR").html(opcion).focus();
                            
                        } else {
                            opcion = '<option value="">No hay tipo retención</option>';
                            $("#sltTipoR").html(opcion);
                           
                        }
                    }
                }); 
            }
        }
    </script> 
</body>
</html>					