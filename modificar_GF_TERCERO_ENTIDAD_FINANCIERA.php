<?php 
	require_once('Conexion/conexion.php');
  require_once 'head.php'; 
  //Captura de ID y consulta del registro correspondiente.
  $id = " ";
  if (isset($_GET["id"]))
  { 
    $id = (($_GET["id"]));

//Consulta general
    $queryAsociadoJur ="SELECT 
                        t.id_unico, 
                        t.razonsocial,
                        t.tipoidentificacion,
                        ti.id_unico,
                        ti.nombre,
                        t.numeroidentificacion,
                        t.sucursal,
                        s.id_unico,
                        s.nombre,
                        t.tiporegimen,
                        tr.id_unico,
                        tr.nombre,
                        t.tipoempresa,
                        tem.id_unico,
                        tem.nombre,
                        t.tipoentidad,
                        ten.id_unico,
                        ten.nombre,
                        t.representantelegal,
                        r.id_unico,
                        CONCAT(r.nombreuno,' ',r.nombredos,' ',r.apellidouno,' ',r.apellidodos),
                        t.ciudadidentificacion,
                        t.contacto,
                        c.id_unico,
                        CONCAT(c.nombreuno,' ',c.nombredos,' ',c.apellidouno,' ',c.apellidodos),
                        t.zona,
                        z.id_unico,
                        z.nombre,
                        cd.departamento,
                        cd.nombre,
                        d.nombre
        FROM gf_perfil_tercero pt
        LEFT JOIN gf_tercero t  		 	ON pt.tercero = t.id_unico
        LEFT JOIN gf_tipo_identificacion ti ON t.tipoidentificacion = ti.id_unico        
        LEFT JOIN gf_sucursal s             ON t.sucursal = s.id_unico
        LEFT JOIN gf_tipo_regimen tr        ON t.tiporegimen = tr.id_unico
        LEFT JOIN gf_tipo_empresa tem       ON t.tipoempresa = tem.id_unico
        LEFT JOIN gf_tipo_entidad ten       ON t.tipoentidad = ten.id_unico
        LEFT JOIN gf_tercero r  			ON t.representantelegal = r.id_unico
        LEFT JOIN gf_tercero c  			ON t.contacto = c.id_unico
        LEFT JOIN gf_zona z     			ON t.zona = z.id_unico
        LEFT JOIN gf_ciudad cd		        ON t.ciudadidentificacion = cd.id_unico
        LEFT JOIN gf_departamento d         ON cd.departamento = d.id_unico
        WHERE md5(t.id_unico) = '$id'";
  }

  $resultado = $mysqli->query($queryAsociadoJur);
  $row = mysqli_fetch_row($resultado);

  //Variables de sesión para determinar el id del tercero que se está consultando y la url para regresar.
  $_SESSION['id_tercero'] = $row[0];
  $_SESSION['perfil'] = "EF"; //Jurídica.
  $_SESSION['url'] = "modificar_GF_TERCERO_ENTIDAD_FINANCIERA.php?id=".(($_GET["id"]));
  $_SESSION['tipo_perfil']='Entidad Financiera';

  //Consultas para el listado de los diferentes combos correspondientes.
  //Tipo Identificación.
  $sqlTipoIden = "SELECT Id_Unico, Nombre 
  FROM gf_tipo_identificacion
  WHERE Id_Unico != $row[2]
  ORDER BY Nombre ASC";
  $tipoIden = $mysqli->query($sqlTipoIden);

  //Sucursal.
  $suc = 0;
  if(!empty($row[6])){
      $suc = $row[6];
  }
 $sqlSucursal = "SELECT Id_Unico, Nombre 
  FROM gf_sucursal
  WHERE Id_Unico != $suc 
  ORDER BY Nombre ASC";
  $sucursal = $mysqli->query($sqlSucursal);

  //Tipo Régimen.
  $tipoR=0;
  if(!empty($row[9])){
  $tipoR = $row[9];
  }
  $sqlTipoReg = "SELECT Id_Unico, Nombre 
  FROM gf_tipo_regimen
  WHERE Id_Unico != $tipoR 
  ORDER BY Nombre ASC";
  $tipoReg = $mysqli->query($sqlTipoReg);

  //Tipo Empresa.
  $tipoE=0;
  if(!empty($row[12])){
  $tipoE = $row[12];
  }
  $sqlTipoEmp = "SELECT Id_Unico, Nombre 
  FROM gf_tipo_empresa
  WHERE Id_Unico != $tipoE 
  ORDER BY Nombre ASC";
  $tipoEmp = $mysqli->query($sqlTipoEmp);


  //Representante Legal.
  $sqlReprLeg = "SELECT t.Id_Unico, t.NombreUno, t.NombreDos, t.ApellidoUno, t.ApellidoDos, t.NumeroIdentificacion, ti.Nombre 
  FROM gf_tercero t, gf_tipo_identificacion ti, gf_perfil_tercero pt  
  WHERE t.TipoIdentificacion = ti.Id_Unico
  AND t.Id_Unico = pt.Tercero 
  AND pt.Perfil != 1
  AND t.Id_Unico != $row[18]
  ORDER BY t.NombreUno ASC";
  $repreLegal = $mysqli->query($sqlReprLeg);

  //Contacto.
  $sqlContacto = "SELECT t.Id_Unico, t.NombreUno, t.NombreDos, t.ApellidoUno, t.ApellidoDos, t.NumeroIdentificacion, ti.Nombre 
  FROM gf_tercero t, gf_tipo_identificacion ti, gf_perfil_tercero pt     
  WHERE t.TipoIdentificacion = ti.Id_Unico 
  AND t.Id_Unico = pt.Tercero 
  AND pt.Perfil = 10
  AND t.Id_Unico != $row[22]
  ORDER BY t.NombreUno ASC";
  $contacto = $mysqli->query($sqlContacto);

  //Zona
  $sqlZona = "SELECT Id_Unico, Nombre 
  FROM gf_zona
  WHERE Id_Unico != $row[25]
  ORDER BY Nombre ASC";
  $zona = $mysqli->query($sqlZona);
  //Fin de las consultas para combos.
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



<title>Modificar Entidad Financiera</title>
</head>
   <body>
        <!-- Inicio de Contenedor principal -->
    <div class="container-fluid text-center" >
        <!-- Inicio de Fila de Contenido -->
        <div class="content row">
            <!-- Lllamado de menu -->
            <?php require_once 'menu.php'; ?>
            <!-- Inicio de contenedor de cuerpo contenido -->
            <div class="col-sm-7 text-left" style="margin-left: -16px;margin-top: -20px;"> 
                <!-- Titulo de Formulario -->
                <h2 align="center" class="tituloform">Modificar Entidad Financiera</h2>
                <!-- Contenedor del formulario -->
                <div class="client-form contenedorForma">
                    <!-- Inicio de Formulario -->
                    <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificarEntidadFinancieraJson.php">
                        <!-- Párrafo de texto-->
                        <p align="center" class="parrafoO" >Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>


            <input type="hidden" name="id" value="<?php echo $row[0];?>">

            <div class="form-group form-inline" style="margin-top:-20px">

                            <label for="noIdent" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Número Identificación:</label>
                            
                            <select name="tipoIdent" id="tipoIdent" class="form-control col-sm-5" style="height: 33px;width:113px" title="Tipo Identificación" required>
                                <option value="<?php echo $row[3]; ?>"><?php echo $row[4]; ?></option>
                                <?php while ($ma = mysqli_fetch_assoc($tipoIden)) { ?>
                                    <option value="<?php echo $ma["Id_Unico"]; ?>"><?php echo ucwords( (strtolower($ma["Nombre"]))); ?>
                                    </option>
                                <?php } ?>
                            </select>
                            
                            <span class="col-sm-1" style="width:1px; margin-top:8px;"></span>
                            
                            <input type="text" value="<?php echo $row[5]; ?>" name="noIdent" id="noIdent" class="form-control col-sm-5" maxlength="20" title="Ingrese el número de identificación" onkeypress="return txtValida(event,'num')" placeholder="Número" style="width:95px" style="height: 30px" required onblur="CalcularDv();return existente()" />

                            <span class="col-sm-1" style="width:1px; margin-top:8px;"><strong> - </strong></span>

                            <input type="text" value="<?php echo $row[3]; ?>" name="digitVerif" id="digitVerif" class="form-control " style="width:30px" maxlength="1" placeholder="0" title="Dígito de verificación" onkeypress="return txtValida(event,'num')" placeholder="" readonly="" style="height: 30px"/>

                        </div>
            <!--Modificación de Sucursal-->
            <div class="form-group" style="margin-top: -22px; ">
              <label for="sucursal" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Sucursal:</label>
              <select name="sucursal" id="sucursal" class="form-control" title="Ingrese sucursal" >
                  <?php if(empty($row[6])){ echo '<option value=""> - </option>';}  
                  else {echo '<option value="'.$row[6].'">'.$row[8].'</option>';}?>
                  <?php while($rowS = mysqli_fetch_row($sucursal))
                  {  ?>
                <option value="<?php echo $rowS[0] ?>"><?php echo ucwords( (strtolower($rowS[1]))); ?></option>
                  <?php
                  }  ?>
              </select> 
            </div>

            <!--Modificación de Razón Social-->                    
            <div class="form-group" style="margin-top: -22px; ">
              <label for="razoSoci" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Razón Social:</label>
                <input type="text" name="razoSoci" id="razoSoci" class="form-control" maxlength="500" title="Ingrese la razón social" value="<?php echo  ($row[1]);?>" onkeypress="return txtValida(event,'num_car')"  onkeyup="javascript:this.value=this.value.toUpperCase();" placeholder="Razón Social" required>
            </div>

            <!--Modificación de Tipo Régimen-->
            <div class="form-group" style="margin-top: -22px; ">
              <label for="tipoReg" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Tipo Régimen:</label>
              <select name="tipoReg" id="tipoReg" class="form-control" title="Ingrese el tipo de régimen" >
                  <?php if(empty($row[9])){ echo '<option value=""> - </option>';}  
                  else {echo '<option value="'.$row[9].'">'.$row[11].'</option>';}?>
                  <?php while($rowTR = mysqli_fetch_row($tipoReg))
                   {  ?>
                <option value="<?php echo $rowTR[0] ?>"><?php echo ucwords( (strtolower($rowTR[1]))); ?></option>
                   <?php
                   }  ?>
              </select> 
            </div>


            <!--Modificación de Tipo Empresa-->
            <div class="form-group" style="margin-top: -22px; ">
              <label for="tipoEmp" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Tipo Empresa:</label>
              <select name="tipoEmp" id="tipoEmp" class="form-control" title="Ingrese el tipo de empresa">
                  <?php if(empty($row[12])){ echo '<option value=""> - </option>';}  
                  else {echo '<option value="'.$row[12].'">'.$row[14].'</option>';}?>
                   <?php while($rowTE = mysqli_fetch_row($tipoEmp))
                   {  ?>
                <option value="<?php echo $rowTE[0] ?>"><?php echo ucwords( (strtolower($rowTE[1]))); ?></option>
                   <?php
                  }  ?>
              </select> 
            </div>


            <!--Modificación de Representante Legal-->
           <div class="form-group" style="margin-top: -22px; ">
              <label for="repreLegal" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Representante Legal:</label>
              <select name="repreLegal" id="repreLegal" class="form-control" title="Ingrese el representante legal" >
                <?php
                 $sqlElReprLeg = "SELECT t.Id_Unico, t.NombreUno, t.NombreDos, t.ApellidoUno, t.ApellidoDos, t.NumeroIdentificacion, ti.Nombre 
                  FROM gf_tercero t, gf_tipo_identificacion ti  
                  WHERE t.TipoIdentificacion = ti.Id_Unico
                  AND t.Id_Unico = $row[18]";
                  $elReprLeg = $mysqli->query($sqlElReprLeg);
                  $rowElReprLeg = mysqli_fetch_row($elReprLeg);
                  ?>
                <option value="<?php echo $rowElReprLeg[0] ?>">
                  <?php echo ucwords( (strtolower($rowElReprLeg[1]." ".$rowElReprLeg[2]." ".$rowElReprLeg[3]." ".$rowElReprLeg[4]." (".$rowElReprLeg[6].", ".$rowElReprLeg[5].")"))); ?>
                </option>

                  <?php while($rowRL = mysqli_fetch_row($repreLegal))
                   {  ?>
                <option value="<?php echo $rowRL[0] ?>">
                  <?php echo ucwords( (strtolower($rowRL[1]." ".$rowRL[2]." ".$rowRL[3]." ".$rowRL[4]." (".$rowRL[6].", ".$rowRL[5].")"))); ?>
                </option>
                 <?php
                }  ?>
              </select> 
           </div>


<!--  Inicio combos dinámicos -->
          <div class="form-group form-inline" style="margin-top: -20px">
                            <label for="depto" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Ubicación:</label>     
                            
                            <div class="classDepto">
                                
                                <select name="depto" id="depto" class="form-control col-sm-5" style="height: 20%;width:170px" title="Seleccione Departamento" required>
                                </select>
                                <script type="text/javascript">
                                    $(document).ready(function(){                   
                                        $.ajax({       
                                            data: {"id_ciudad_depto": "<?php echo $row[28];?>"},
                                            type: "POST",
                                            url: "MDepartamento.php",
                                            success: function(response){
                                                $('.classDepto select').html(response).fadeIn();
                                            }
                                        });
                                    });
                                </script>
                            </div>
                            
                            <span class="col-sm-1" style="width:1px"></span>
                            
                            <div class="ClassCiudad">
                                <select name="ciudad" style="height: 24%;width:100px" id="ciudad" class="form-control" title="Seleccione Ciudad" required>
                                    <option value="">Ciudad</option>
                                </select>
                                <script type="text/javascript">
                                  $(document).ready(function(){
                                      var cambio = 0;
                                      //Este evento change funciona cuando se cambia de departamento.
                                      $(".classDepto select").change(function(){
                                        cambio = 1;
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
                                            }
                                        });
                                      });

                    // Se eliminó el evento click y el select caragará junto con la página.
                    //$(".ClassCiudad select").click(function()
                    //{
                                      if (cambio == 0) {
                          //cambio = 1;
                                          $.ajax({
                                            data: {"id_ciudad": "<?php echo $row[21];?>", "id_ciudad_depto": "<?php echo $row[28];?>"},
                                              type: "POST",
                                              url: "MCiudad.php",
                                              success: function(response){
                                                $('.ClassCiudad select').html(response).fadeIn();
                                          }
                                      });
                    
                                    }
                    //});

                                  });
                                </script>
                            </div>
                        </div>
<!--  Fin combos dinámicos  -->
            <div class="form-group" style="margin-top: -22px; ">
                 <label for="contacto" class="col-sm-5 control-label">Contacto:</label>
              <select name="contacto" id="contacto" class="form-control" title="Ingrese el contacto">
                   <?php
                      if(!empty($row[21]))
                      {

                        $sqlElContacto = "SELECT t.Id_Unico, t.NombreUno, t.NombreDos, t.ApellidoUno, t.ApellidoDos, t.NumeroIdentificacion, ti.Nombre 
                        FROM gf_tercero t, gf_tipo_identificacion ti  
                        WHERE t.TipoIdentificacion = ti.Id_Unico
                        AND t.Id_Unico = $row[21]";
                        $elContacto = $mysqli->query($sqlElContacto);
                        $rowElCon = mysqli_fetch_row($elContacto);
                         echo '<option value="'.$row[13].'">'.ucwords(strtolower($rowElCon[1]." ".$rowElCon[2]." ".$rowElCon[3]." ".$rowElCon[4]." (".$rowElCon[6].", ".$rowElCon[5].")")).'</option>'; 
                         $sqlContactos = "SELECT t.Id_Unico, t.NombreUno, t.NombreDos, t.ApellidoUno, t.ApellidoDos, t.NumeroIdentificacion, ti.Nombre 
                            FROM gf_tercero t
                            LEFT JOIN gf_tipo_identificacion ti ON t.TipoIdentificacion = ti.Id_Unico 
                            LEFT JOIN gf_perfil_tercero pt   ON   t.Id_Unico = pt.Tercero 
                            WHERE pt.Perfil = 10
                            AND t.Id_Unico != $row[21]
                            ORDER BY t.NombreUno ASC";
                            $contactos = $mysqli->query($sqlContactos);
                            while($con = mysqli_fetch_row($contactos)){
                              echo '<option value="'.$con[0].'">'.$con[1].' '.$con[2].' '.$con[3].' '.$con[4].'('.$con[5].' - '.$con[6].')'.'</option>';
                            } 
                            echo '<option value=""></option>';
                      }
                    
                          else {
                            echo '<option value="">Contacto</option>';
                            $sqlContactos = "SELECT t.Id_Unico, t.NombreUno, t.NombreDos, t.ApellidoUno, t.ApellidoDos, t.NumeroIdentificacion, ti.Nombre 
                            FROM gf_tercero t, gf_tipo_identificacion ti, gf_perfil_tercero pt     
                            WHERE t.TipoIdentificacion = ti.Id_Unico 
                            AND t.Id_Unico = pt.Tercero 
                            AND pt.Perfil = 10
                            ORDER BY t.NombreUno ASC";
                            $contactos = $mysqli->query($sqlContactos);
                            while($con = mysqli_fetch_row($contactos)){
                              echo '<option value="'.$con[0].'">'.$con[1].' '.$con[2].' '.$con[3].' '.$con[4].'('.$con[5].' - '.$con[6].')'.'</option>';
                            }                            
                        }
                         ?>
              </select> 
          </div>


            <div class="form-group" style="margin-top: -20px; ">
              <label for="zona" class="col-sm-5 control-label">Zona:</label>
              <select name="zona" id="zona" class="form-control" title="Ingrese la zona">
              <?php   
                if(empty($row[25])){
                  echo '<option value="">Zona</option>';
                   $sqlZonas = "SELECT Id_Unico, Nombre 
                              FROM gf_zona  
                              ORDER BY Nombre ASC";
                    $zonas = $mysqli->query($sqlZonas);
                    while ($zon = mysqli_fetch_row($zonas)) {
                      echo '<option value="'.$zon[0].'">'.$zon[1].'</option>';
                    }
                }else{ ?>
                    <option value="<?php echo $row[25];?>"><?php echo  ($row[27]);?></option>
                    <?php while($rowZ = mysqli_fetch_row($zona))
                     {  ?>
                        <option value="<?php echo $rowZ[0] ?>"><?php echo ucwords( (strtolower($rowZ[1]))); ?></option>
                      <?php
                       }      ?>
              <?php
                echo '<option value=""></option>';}
               ?>                
              </select> 
            </div>


            <div class="form-group" style="margin-top: 10px;">
             <label for="no" class="col-sm-5 control-label"></label>
             <button type="submit" class="btn btn-primary sombra" style=" margin-top: -40px; margin-bottom:-30px; margin-left: 0px;">Guardar</button>
            </div>


            <input type="hidden" name="MM_insert" >
          </form>
        </div>      
    </div> <!-- Cierra clase col-sm-7 text-left -->

<!-- Botones de consulta -->            
            <!-- Fin de Contenedor Principal -->
            <?php require_once('footer.php'); ?>

</body>
</html>