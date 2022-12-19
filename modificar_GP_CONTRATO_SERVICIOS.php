<?php 
//llamado a la clase de conexion
  require_once('Conexion/conexion.php');
  session_start();
  //validacion preguntando si la variable enviada del listar viene vacia
$id = (($_GET["id"]));
  $sql = "SELECT 		cs.id_unico,
                                cs.numero_contrato,
                                cs.unidad_vivienda_servicio,
                                uvs.id_unico,
                                uvs.unidad_vivienda,
                                uv.id_unico,
                                uv.tipo_unidad,
                                tu.id_unico,
                                tu.nombre,
                                uvs.tipo_servicio,
                                ts.id_unico,
                                ts.nombre,
                                cs.formato,
                                f.id_unico,
                                f.nombre,
                                cs.fecha_contrato
                FROM		gp_contrato_servicios cs	 
                LEFT JOIN	gp_unidad_vivienda_servicio uvs on cs.unidad_vivienda_servicio = uvs.id_unico
                LEFT JOIN	gp_unidad_vivienda uv 		on uvs.unidad_vivienda = uv.id_unico
                LEFT JOIN	gp_tipo_unidad_vivienda tu	on uv.tipo_unidad = tu.id_unico
                LEFT JOIN	gp_tipo_servicio ts 		on uvs.tipo_servicio = ts.id_unico
                LEFT JOIN	gf_formato f 			on cs.formato = f.id_unico
                where md5(cs.id_unico) = '$id'";
    $resultado = $mysqli->query($sql);
    $row = mysqli_fetch_row($resultado);
    $numcont=$row[1];
    $form=$row[14];
    $fec=$row[15];
    $uvi=$row[8].' - '.$row[11];
    
?>
<!-- Llamado a la cabecera del formulario -->
<?php require_once 'head.php'; ?>
<title>Modificar Contrato Servicios</title>
</head>
<body>
 
<!-- contenedor principal -->  
<div class="container-fluid text-center">
  <div class="row content">

<!-- Llamado al menu del formulario -->    
  <?php require_once 'menu.php'; ?>
    <div class="col-sm-8 text-left" style="margin-left: -16px;margin-top: -20px"> 
            <h2 align="center" class="tituloform">Modificar Contrato Servicios</h2>
      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
<!-- inicio del formulario --> 
          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificarContratoServiciosJson.php">
          <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
          <input type="hidden" name="id" value="<?php echo $row[0] ?>">
<!------------------------- Captura de Número de Contrato -->                            
        <div class="form-group" style="margin-top:-20px">
            <label for="txtNumeroC" class="control-label col-sm-5">
                <strong class="obligado">*</strong>Número Contrato:
            </label>
            <input type="text" name="txtNumeroC" id="txtNumeroC" class="form-control" value="<?php echo $numcont;?>" title="Ingrese número contrato" />
        </div>
<!------------------------- Consulta para llenar campos Unidad Vivienda Servicio-->
          <div class="form-group">
             <?php 
                $numcont=$row[1];
                $idform=$row[13];
                $form=$row[14];
                $iduvi=$row[7];
                $uvi=$row[8];                
                $idserv=$row[10];
                $serv=$row[11];
                $uvs = "SELECT 		uvs.id_unico,
                                        uvs.unidad_vivienda,
                                        uv.id_unico,
                                        uv.tipo_unidad,
                                        tu.id_unico,
                                        tu.nombre,
                                        uvs.tipo_servicio,
                                        ts.id_unico,
                                        ts.nombre
                    FROM		gp_unidad_vivienda_servicio uvs 
                    LEFT JOIN           gp_unidad_vivienda uv on uvs.unidad_vivienda = uv.id_unico
                    LEFT JOIN           gp_tipo_unidad_vivienda tu on uv.tipo_unidad = tu.id_unico
                    LEFT JOIN           gp_tipo_servicio ts on uvs.tipo_servicio = ts.id_unico
                    WHERE uvs.id_unico != '$row[2]]'";
                $rsUvs = $mysqli->query($uvs);

            ?> 
             <label for="Uvs" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Unidad Vivienda Servicio:</label>
             <select name="sltUvs" id="sltUvs" class="form-control" title=
                     "Seleccione Unidad vivienda - Servicio" style="height: 30px">
                 <option value="<?php echo $iduvi;?>"><?php echo $uvi.' - '.$serv;?></option>
             <?php 
                 while ($filaUvs = mysqli_fetch_row($rsUvs)) 
             { 
             ?>
                <option value="<?php echo $filaUvs[0];?>"><?php echo ucwords(($filaUvs[5].' - '.$filaUvs[8]));?></option>                                
             <?php 
             }
              ?>                                    
             </select>
          </div>
<!------------------------- Consulta para llenar campos Formato-->
            <?php 
            $sql = "SELECT id_unico, nombre FROM gf_formato where id_unico != '$idform'";
            $formato = $mysqli->query($sql);
            ?>
            <div class="form-group" style="margin-top: -5px">
                <label class="control-label col-sm-5">
                        <strong class="obligado">*</strong>Formato:
                </label>
                <select name="sltFormato" class="form-control" id="sltFormato" title="Seleccione formato" style="height: 30px" required="">
                <option value=<?php echo $idform;?>"><?php echo $form?></option>
                    <?php 
                    while ($fila1 = mysqli_fetch_row($formato)) { ?>
                    <option value="<?php echo $fila1[0];?>"><?php echo ucwords((strtolower($fila1[1]))); ?></option>
                    <?php
                    }
                    ?>
                </select>   
            </div>
<!----------Script para invocar Date Picker-->
<script type="text/javascript">
$(document).ready(function() {
   $("#datepicker").datepicker();
});
</script>
<!----------Fin Script para invocar Date Picker-->
<!--Campo para captura de Fecha-->
           <div class="form-group" style="margin-top: -10px;">
                <label for="fecha" type = "date" class="col-sm-5 control-label"><strong class="obligado">*</strong>Fecha:</label>
                <input type="date" name="fecha" id="fecha" class="form-control" maxlength="10" value="<?php echo $fec?>" title="Fecha contrato" value="<?php echo $row[0].' - '.$row[1].' - '.$row[2].' - '.$row[3].' - '.$row[4].' - '.$row[5].' - '.$row[6].' - '.$row[7].' - '.$row[8].' - '.$row[9].' - '.$row[10].' - '.$row[11].' - '.$row[12].' - '.$row[13].' - '.$row[14].' - '.$row[15]?>" onkeypress="return txtValida(event,'num_car')" placeholder="" required>
                
           </div>
<!----------Fin Captura de Fecha -->           
            <div class="form-group" style="margin-top: 10px;">
              <label for="no" class="col-sm-5 control-label"></label>
                <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left: 0px;">Guardar</button>
         </form>
<!-- Fin de división y contenedor del formulario -->

        </div>     
    </div>
  </div>
  <!-- Fin del Contenedor principal -->
  <!--Información adicional -->
</div>
<!-- Llamado al pie de pagina -->
<?php require_once 'footer.php' ?>  

</body>
</html>