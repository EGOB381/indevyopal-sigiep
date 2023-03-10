<?php 
//llamado a la clase de conexion
  require_once('Conexion/conexion.php');
  session_start();
//declaracion que recibe la variable que recibe el ID
  $id= " ";
//validacion preguntando si la variable enviada del listar viene vacia
  if (isset($_GET["id"])){ 
    $id_TT= (($_GET["id"]));
//Query o sql de consulta
  $queryTT = "SELECT id_unico, nombre FROM gf_tipo_telefono  WHERE md5(id_unico) ='$id_TT'";

}

/*Variable y proceso en el que se llama de manera embebida con la conexión el cual pérmite realizar el proceso de consulta*/
$resultado = $mysqli->query($queryTT);
$row = mysqli_fetch_row($resultado);

?>

<!-- Llamado a la cabecera del formulario -->
<?php require_once 'head.php';  ?>
  <title>Modificar Tipo Teléfono</title>
</head>

<!-- contenedor principal -->  
<div class="container-fluid text-center">
  <div class="row content">
<!-- Llamado al menú del formulario -->   
    <?php require_once 'menu.php'; ?>

    <div class="col-sm-10 text-left">
      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Modificar Tipo Teléfono</h2>
      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">

<!-- Inicio del formulario -->
          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificarTipoTelJson.php">

          <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>

           
            <input type="hidden" name="id" value="<?php echo $row[0] ?>">


            <div class="form-group" style="margin-top: -10px;">
              <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                <input type="text" name="nombre" id="nombre" class="form-control" maxlength="150" title="Ingrese el nombre" onkeypress="return txtValida(event,'car')" placeholder="Nombre" value="<?php echo $row[1] ?>" required>
            </div>
          
<div class="form-group" style="margin-top: 10px;">
              <label for="no" class="col-sm-5 control-label"></label>
                <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left: 0px;">Guardar</button>
            </div>
            <input type="hidden" name="MM_insert" >
          </form>
<!-- Fin de división y contenedor del formulario -->          
        </div>     
    </div>
  </div>
<!-- Fin del Contenedor principal -->   
</div>

<!-- Llamado al pie de pagina -->
<?php require_once 'footer.php'; ?>
  </div>

</body>
</html>
