<?php
  require_once('../Conexion/conexion.php');
  //session_start();

  $num = 0;
  //Captura de datos e instrucción SQL para su inserción en la tabla gf_tercero_ingresos.
  @session_start();
    $param = $_SESSION['anno'];
  $tercero      = '"'.$mysqli->real_escape_string(''.$_POST['tercero'].'').'"';
  $valor        = $_POST['valor']; 

  $queryU = "SELECT id_unico FROM gf_tercero_ingresos WHERE tercero = $tercero";
  $tipot = $mysqli->query($queryU);
  $num = mysqli_num_rows($tipot);


  if($num == 0)//Si no existe registro, deja insertar. 
  {
    $insertSQL = "INSERT INTO gf_tercero_ingresos (tercero, parametrizacionanno, valor_ingresos) VALUES($tercero,$param,$valor)";
    $resultado = $mysqli->query($insertSQL);
   }
  else//Si no se encuentra, retronará false en el resultado.
  {
    $resultado = false;
  }
?>

<html>
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <link rel="stylesheet" href="../css/style.css">
 <script src="../js/md5.pack.js"></script>
 <script src="../js/jquery.min.js"></script>
 <link rel="stylesheet" href="../css/jquery-ui.css" type="text/css" media="screen" title="default" />
 <script type="text/javascript" language="javascript" src="../js/jquery-1.10.2.js"></script>
</head>
<body>
</body>
</html>
<!-- Divs de clase Modal para las ventanillas de confirmación de inserción de registro. -->
<div class="modal fade" id="myModal1" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Informaci&oacuten</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>Informaci&oacuten guardada correctamente.</p>
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
         <p><?php
              if($num != 0) //Si se encuentra el año, informará al usuario.
                echo "Ya existe un registro con el tercero actual.";
              else
                echo "No se ha podido guardar la informaci&oacuten.";
            ?>
        </p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>

<script type="text/javascript" src="../js/menu.js"></script>
  <link rel="stylesheet" href="../css/bootstrap-theme.min.css">
  <script src="../js/bootstrap.min.js"></script>
  
<!-- Script que redirige a la página inicial -->
<?php if($resultado==true){ ?>
<script type="text/javascript">
  $("#myModal1").modal('show');
  $("#ver1").click(function(){
    $("#myModal1").modal('hide');
    window.history.go(-2);
  });
</script>
<?php }else{ ?>
<script type="text/javascript">
  $("#myModal2").modal('show');
  $("#ver2").click(function(){
    $("#myModal2").modal('hide');
    window.history.go(-2);
  });
</script>
<?php } ?>