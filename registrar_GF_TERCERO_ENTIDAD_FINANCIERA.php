<?php
#03/03/2017 --- Nestor B --- se modifico el value="0" del contacto por value="" en el formulario
#05/06/2017 --- Nestor B --- se agregó la libreria de búsqueda rápida, se arreglaron los margenes, se agregó el tipo de entidad y se corrigieron las tildes 
  require_once 'head.php'; 
	require_once('Conexion/conexion.php');

  $_SESSION['perfil'] = "EF"; //Entidad Financiera
  $_SESSION['url'] = "registrar_GF_TERCERO_ENTIDAD_FINANCIERA.php";

  //Consultas para el listado de los diferentes combos correspondientes.
  //Tipo Identificación.
  $sqlTipoIden = "SELECT DISTINCT id_unico, nombre 
  FROM gf_tipo_identificacion
  ORDER BY Nombre ASC";
  $tipoIden = $mysqli->query($sqlTipoIden);

  //Sucursal.
  $sqlSucursal = "SELECT id_unico, nombre 
  FROM gf_sucursal
  ORDER BY Nombre ASC";
  $sucursal = $mysqli->query($sqlSucursal);

  //Tipo Régimen.
  $sqlTipoReg = "SELECT id_Unico, nombre 
  FROM gf_tipo_regimen
  ORDER BY Nombre ASC";
  $tipoReg = $mysqli->query($sqlTipoReg);

  //Tipo Empresa.
  $sqlTipoEmp = "SELECT id_Unico, nombre 
  FROM gf_tipo_empresa
  ORDER BY Nombre ASC";
  $tipoEmp = $mysqli->query($sqlTipoEmp);

  $sqlTipoEnt = "SELECT id_unico, nombre
  FROM gf_tipo_entidad
  ORDER BY nombre ASC";
  $tipoEnt = $mysqli->query($sqlTipoEnt);


  //Representante Legal.
  $sqlReprLeg = "SELECT t.Id_Unico, t.NombreUno, t.NombreDos, t.ApellidoUno, t.ApellidoDos, t.NumeroIdentificacion, ti.Nombre 
  FROM gf_tercero t, gf_tipo_identificacion ti, gf_perfil_tercero pt   
  WHERE t.TipoIdentificacion = ti.Id_Unico
  AND t.Id_Unico = pt.Tercero 
  AND pt.Perfil = 10
  ORDER BY t.NombreUno ASC";
  $repreLegal = $mysqli->query($sqlReprLeg);

  //Contacto.
  $sqlContacto = "SELECT t.Id_Unico, t.NombreUno, t.NombreDos, t.ApellidoUno, t.ApellidoDos, t.NumeroIdentificacion, ti.Nombre 
  FROM gf_tercero t, gf_tipo_identificacion ti, gf_perfil_tercero pt   
  WHERE t.TipoIdentificacion = ti.Id_Unico
  AND t.Id_Unico = pt.Tercero 
  AND pt.Perfil = 10
  ORDER BY t.NombreUno ASC";
  $contacto = $mysqli->query($sqlContacto);

  //Zona.
  $sqlZona = "SELECT Id_Unico, Nombre 
  FROM gf_zona
  ORDER BY Nombre ASC";
  $zona = $mysqli->query($sqlZona);  

  
?>
<!-- Script para calcular el dígito de verificación. -->
<script type="text/javascript">
    function CalcularDv()
{ 
 var arreglo, x, y, z, i, nit1, dv1;
 nit1=document.form.noIdent.value;
  if (isNaN(nit1))
  {
  document.form.digitVerif.value="X";
      alert('Número del Nit no valido, ingrese un número sin puntos, ni comas, ni guiones, ni espacios');   
  } else {
  arreglo = new Array(16); 
  x=0 ; y=0 ; z=nit1.length ;
  arreglo[1]=3;   arreglo[2]=7;   arreglo[3]=13; 
  arreglo[4]=17;  arreglo[5]=19;  arreglo[6]=23;
  arreglo[7]=29;  arreglo[8]=37;  arreglo[9]=41;
  arreglo[10]=43; arreglo[11]=47; arreglo[12]=53;  
  arreglo[13]=59; arreglo[14]=67; arreglo[15]=71;
  for(i=0 ; i<z ; i++)
  { 
   y=(nit1.substr(i,1));
     x+=(y*arreglo[z-i]);
  } 
  y=x%11
  if (y > 1){ dv1=11-y; } else { dv1=y; }
  document.form.digitVerif.value=dv1;
  }
}
  </script>

<title>Registrar Entidad Financiera</title>
<link rel="stylesheet" href="css/select2.css">
<link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
</head>

   <body>
        <!-- Inicio de Contenedor principal -->
    <div class="container-fluid text-center" >
        <!-- Inicio de Fila de Contenido -->
        <div class="content row">
            <!-- Lllamado de menu -->
            <?php require_once 'menu.php'; ?>
            <!-- Inicio de contenedor de cuerpo contenido -->
            <div class="col-sm-7 text-left" style="margin-left: -16px;margin-top: -20px"> 
                <!-- Titulo de Formulario -->
                <h2 align="center" class="tituloform">Registrar Entidad Financiera</h2>
                <!-- Contenedor del formulario -->
                <div class="client-form contenedorForma">
                    <!-- Inicio de Formulario -->
                    <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarEntidadFinancieraJson.php">
                        <!-- Párrafo de texto-->
                        <p align="center" class="parrafoO" >Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>

            

                         <div class="form-group form-inline" style="margin-top:-20px">

                            <label for="noIdent" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Número Identificación:</label>
                            
                            <select name="tipoIdent" id="tipoIdent" class="form-control col-sm-5" style="height: 33px;width:113px" title="Tipo Identificación" required>
                                <option>Tipo Ident.</option>
                                <?php while ($ma = mysqli_fetch_assoc($tipoIden)) { ?>
                                    <option value="<?php echo $ma["id_unico"]; ?>">
                                        <?php echo ucwords( (mb_strtolower($ma["nombre"]))); ?>
                                    </option>
                                <?php } ?>
                            </select>
                            
                            <span class="col-sm-1" style="width:1px; margin-top:8px;"></span>
                            
                            <input type="text" name="noIdent" id="noIdent" class="form-control col-sm-5" maxlength="20" title="Ingrese el número de identificación" onkeypress="return txtValida(event,'num')" placeholder="Número" style="width:95px" style="height: 30px" required onblur="CalcularDv();return existente()" />

                            <span class="col-sm-1" style="width:1px; margin-top:8px;"><strong> - </strong></span>

                            <input type="text" name="digitVerif" id="digitVerif" class="form-control " style="width:30px" maxlength="1" placeholder="0" title="Dígito de verificación" onkeypress="return txtValida(event,'num')" placeholder="" readonly="" style="height: 30px"/>

                        </div>



            <div class="form-group" style="margin-top: -22px; ">
              <label for="sucursal" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Sucursal:</label>
              <select name="sucursal" id="sucursal" class="select2_single form-control col-sm-1" title="Ingrese el tipo de identificación" required style="width: 34%">
                <option value="">Sucursal</option>
                   <?php while($rowS = mysqli_fetch_row($sucursal))
                   {  ?>
                <option value="<?php echo $rowS[0] ?>"><?php echo ucwords( (mb_strtolower($rowS[1]))); ?></option>
                   <?php
                   }  ?>
              </select> 
            </div>


            <div class="form-group" style="margin-top: -10px;">
              <label for="razoSoci" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Razón Social:</label>
                <input type="text" name="razoSoci" id="razoSoci" class="form-control" maxlength="500" title="Ingrese la razón social" onkeypress="return txtValida(event)" onkeyup="javascript:this.value=this.value.toUpperCase();" placeholder="Razón Social" required style="width: 34%">
               
            </div>


            <div class="form-group" style="margin-top: -10px; ">
              <label for="tipoReg" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Régimen:</label>
              <select name="tipoReg" id="tipoReg" class="select2_single form-control col-sm-1" title="Ingrese el tipo de régimen" required style="width: 34%">
                <option value="">Tipo Régimen</option>
                  <?php while($rowTR = mysqli_fetch_row($tipoReg))
                {  ?>
                <option value="<?php echo $rowTR[0] ?>"><?php echo ucwords( (mb_strtolower($rowTR[1]))); ?></option>
                  <?php
                }  ?>
              </select> 
            </div>



            <div class="form-group" style="margin-top: -10px; ">
              <label for="tipoEmp" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Empresa:</label>
              <select name="tipoEmp" id="tipoEmp" class="select2_single form-control col-sm-1" title="Ingrese el tipo de empresa" required style="width: 34%">
                <option value="">Tipo Empresa</option>
                   <?php while($rowTE = mysqli_fetch_row($tipoEmp))
                   {  ?>
                <option value="<?php echo $rowTE[0] ?>"><?php echo ucwords( (mb_strtolower($rowTE[1]))); ?></option>
                   <?php
                }  ?>
              </select> 
            </div>

            <div class="form-group" style="margin-top: -10px; ">
              <label for="tipoEnt" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Entidad:</label>
              <select name="tipoEnt" id="tipoEnt" class="select2_single form-control col-sm-1" title="Ingrese el tipo de empresa" required style="width: 34%">
                <option value="">Tipo Entidad</option>
                   <?php while($rowTEN = mysqli_fetch_row($tipoEnt))
                   {  ?>
                <option value="<?php echo $rowTEN[0] ?>"><?php echo ucwords( (mb_strtolower($rowTEN[1]))); ?></option>
                   <?php
                }  ?>
              </select> 
            </div>


             <div class="form-group" style="margin-top: -10px; ">
              <label for="repreLegal" class="col-sm-5 control-label">Representante Legal:</label>
              <select name="repreLegal" id="repreLegal" class="select2_single form-control col-sm-1" title="Ingrese el representante legal" style="width: 34%" >
                <option value="">Representante Legal</option>
                  <?php while($rowRL = mysqli_fetch_row($repreLegal))
                  {  ?>
                <option value="<?php echo $rowRL[0] ?>">
                  <?php echo ucwords( (mb_strtolower($rowRL[1]." ".$rowRL[2]." ".$rowRL[3]." ".$rowRL[4]." (".$rowRL[6].", ".$rowRL[5].")"))); ?>
                </option>
                  <?php
                }  ?>
              </select> 
            </div>

            <div class="form-group form-inline" style="margin-top: -10px">
                            <label for="depto" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Ubicación:</label>     
                            
                            <div class="classDepto">
                                
                                <select name="depto" id="depto" class="select2_single form-control col-sm-5" style="height: 20%;width:170px" title="Seleccione Departamento" required>
                                   <option value="">Departamento</option>
                                </select>

                                <script type="text/javascript">
                                        $(document).ready(function(){
                                        $.ajax({
                                        type: "POST",
                                        url: "Departamento.php",
                                        success: function(response){
                                                $('.classDepto select').html(response).fadeIn();
                                                $('#depto').css('display','none');
                                        }
                                        });

                                        });
                                </script>
                            </div>
                            
                            <span class="col-sm-1" style="width:1px"></span>
                            
                            <div class="ClassCiudad">
                                <select name="ciudad" style="height: 24%;width:100px" id="ciudad" class="select2_single form-control col-sm-1" title="Seleccione Ciudad" required ">
                                    <option value="">Ciudad</option>
                                </select>
                                <script type="text/javascript">
                                        $(document).ready(function(){
                                        $(".classDepto select").change(function(){
                                                var form_data = {
                                                is_ajax: 1,
                                                id_depto: +$(".classDepto select").val()
                                                };
                                                $.ajax({
                                                type: "POST",
                                                url: "Ciudad.php",
                                                data: form_data,
                                                success: function(response){
                                                $('.ClassCiudad select').html(response).fadeIn();
                                                 $('#ciudad').css('display','none');
                                        }
                                                });
                                        });
                                        });
                                </script>
                            </div>
                        </div>

            <div class="form-group" style="margin-top: -10px; ">
              <label for="contacto" class="col-sm-5 control-label">Contacto:</label>
              <select name="contacto" id="contacto" class="select2_single form-control col-sm-1" title="Ingrese el contacto" style="width: 34%">
                <option value="">Contacto</option>
          <?php while($rowCon = mysqli_fetch_row($contacto))
                {  ?>
                <option value="<?php echo $rowCon[0] ?>">
                <?php echo ucwords( (mb_strtolower($rowCon[1]." ".$rowCon[2]." ".$rowCon[3]." ".$rowCon[4]." (".$rowCon[6].", ".$rowCon[5].")"))); ?>
                </option>
          <?php
                }  ?>
              </select> 
            </div>


            <div class="form-group" style="margin-top: -10px; ">
              <label for="zona" class="col-sm-5 control-label">Zona:</label>
              <select name="zona" id="zona" class="select2_single form-control col-sm-1" title="Ingrese la zona" style="width: 34%">
                <option value="">Zona</option>
          <?php while($rowZ = mysqli_fetch_row($zona))
                {  ?>
                <option value="<?php echo $rowZ[0] ?>"><?php echo ucwords( (mb_strtolower($rowZ[1]))); ?></option>
          <?php
                }  
                echo '<option value=""></option>';
                ?>
              </select> 
            </div>

            <div class="form-group" style="margin-top: 5px">
             <label for="no" class="col-sm-5 control-label"></label>
             <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left: 0px;">Guardar</button>
            </div>

            <div class="texto" style="display:none"></div>
            <input type="hidden" name="MM_insert" >
          
          </form>
        </div>         
   
    </div> <!-- Cierra clase col-sm-7 text-left -->

           <!-- Botones de consulta -->
            <div class="col-sm-2 col-sm-1">
                <table class="tablaC table-condensed" style="margin-left: -5px;margin-top:-22px; ">
                    <thead>
                        <th>
                            <h2 class="titulo" align="center" style=" font-size:17px; width: 250px; height:36px ">Información adicional</h2>
                        </th>
                    </thead>
                    <tbody>
                            <td>
                              <a href="GF_CUENTA_BANCARIA_TERCERO.php" ><button class="btn btn-primary btnInfo" <?php if (!isset($_SESSION['id_tercero'])){ echo ' disabled title="Debe primero ingresar un  asociado jurídica."';}?> disabled="true" >CUENTA BANCARIA</button></a><br/>
                            </td>
                        </tr>
                        <tr>
                            <td>
                               <a href="GF_TIPO_ACTIVIDAD_TERCERO.php" ><button class="btn btn-primary btnInfo" <?php if (!isset($_SESSION['id_tercero'])){ echo ' disabled title="Debe primero ingresar un  asociado jurídica."';}?> disabled="true" >TIPO ACTIVIDAD</button></a><br/>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <a href="GF_TELEFONO.php" ><button class="btn btn-primary btnInfo" <?php if (!isset($_SESSION['id_tercero'])){ echo ' disabled title="Debe primero ingresar un  asociado jurídica."';}?> disabled="true" >TELEFONO</button></a><br/>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <a href="GF_CONDICION_TERCERO.php" ><button class="btn btn-primary btnInfo" <?php if (!isset($_SESSION['id_tercero'])){ echo ' disabled title="Debe primero ingresar un  asociado jurídica."';}?> disabled="true" >CONDICIÓN</button></a><br/>

                            </td>
                        </tr>
                        <tr>
                          <td>
                            <a href="registrar_TERCERO_CONTACTO_NATURAL.php" <?php if (!isset($_SESSION['id_tercero'])){ echo ' disabled title="Debe primero ingresar una compañía."';}?> disabled="true" class="btn btnInfo btn-primary">CONTACTO</a>
                          </td>
                        </tr>
                        <tr>
                          <td>
                            <a disabled="true" class="btn btnInfo btn-primary" style="margin-top:15px">PERFIL CONDICIÓN</a>
                          </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            </div>
            </div>
            <!-- Fin de Contenedor Principal -->
            <?php require_once('footer.php'); ?>





<!-- Divs clase Modal para notificar la existencia del número de identificación y su posible modificación  -->
<div class="modal fade" id="myModal1" role="dialog" align="center" >
      <div class="modal-dialog">
          <div class="modal-content">
              <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>Este número de identificación  ya existe.¿Desea actualizar la información?</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                <button type="button" class="btn" style="color: #000; margin-top: 2px"  data-dismiss="modal" id="ver2">Cancelar</button>
            </div>
          </div>
      </div>
    </div>

    <div class="modal fade" id="myModal2" role="dialog" align="center" >
      <div class="modal-dialog">
          <div class="modal-content">
              <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>Seleccione un Tipo Identificación.</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="ver3" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                
            </div>
          </div>
      </div>
    </div>
  

  <script type="text/javascript" src="../js/menu.js"></script>
  <link rel="stylesheet" href="../css/bootstrap-theme.min.css">
  <script src="../js/bootstrap.min.js"></script>


  <script type="text/javascript">
      function existente(){
        var tipoD = document.form.tipoIdent.value;     
        var numI = document.form.noIdent.value;
        var result = '';
        
        if(tipoD == null || tipoD == '' || tipoD == "Tipo Ident." || numI == null){

          $("#myModal2").modal('show');
          
        }else{

          $.ajax({
            data: {"num" : numI},
            type: "POST",
            url: "consultarJuridica.php",
            success:  function (data) {
                      
              var res  = data.split(";");

              if(res[1] == 'true1'){
                $('.texto').html(data);
                $("#myModal1").modal('show');

              }                           
            }
          });
          }
      }
  </script>
<script type="text/javascript" src="js/select2.js"></script>
<script type="text/javascript"> 
          $(".select2_single").select2({
            allowClear: true
      });
         
</script>
  <script type="text/javascript">
    $('#ver1').click(function(){
      var id = document.form.id.value;
        document.location = 'modificar_GF_TERCERO_ENTIDAD_FINANCIERA.php?id='+id;
      });

  </script>
   
</body>
</html>