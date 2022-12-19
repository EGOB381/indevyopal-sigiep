<?php

#14/06/2017 --- Nestor B --- se agregó la validacion si el periodo está cerrrado
  require_once('../Conexion/conexion.php');
session_start();

//obtiene los datos que se van a modificar

$empleado      = '"'.$mysqli->real_escape_string(''.$_POST['sltEmpleado'].'').'"';
$id            = '"'.$mysqli->real_escape_string(''.$_POST['id'].'').'"';
$concepto      = '"'.$mysqli->real_escape_string(''.$_POST['sltConcepto'].'').'"';
$conc          =  ''.$mysqli->real_escape_string(''.$_POST['txtcoper'].'').'';
if($mysqli->real_escape_string(''.$_POST['sltEntidad'].'')=="")
    $entidad       = "null";
else    
    $entidad       = '"'.$mysqli->real_escape_string(''.$_POST['sltEntidad'].'').'"';

if($mysqli->real_escape_string(''.$_POST['sltTipo'].'')=="")
    $tipoproceso = "null";
else
    $tipoproceso   = '"'.$mysqli->real_escape_string(''.$_POST['sltTipo'].'').'"';

if($mysqli->real_escape_string(''.$_POST['txtNumeroC'].'')=="")
    $numerocredito = "null";
else
    $numerocredito = '"'.$mysqli->real_escape_string(''.$_POST['txtNumeroC'].'').'"';

if($mysqli->real_escape_string(''.$_POST['Fecha'].'')=="")
    $fecha = "null";
else
    $fecha         = '"'.$mysqli->real_escape_string(''.$_POST['Fecha'].'').'"';

if($mysqli->real_escape_string(''.$_POST['sltPeriodo'].'')=="")
    $periodoinicia = "null";
else
    $periodoinicia = '"'.$mysqli->real_escape_string(''.$_POST['sltPeriodo'].'').'"';

if($mysqli->real_escape_string(''.$_POST['txtValorCr'].'')=="")
    $valorcredito ="null";
else
    $valorcredito  = '"'.$mysqli->real_escape_string(''.$_POST['txtValorCr'].'').'"';

if($mysqli->real_escape_string(''.$_POST['txtNCuotas'].'')=="")
    $numerocuotas = "null";
else
    $numerocuotas  = '"'.$mysqli->real_escape_string(''.$_POST['txtNCuotas'].'').'"';

if($mysqli->real_escape_string(''.$_POST['txtValorCu'].'')=="")
    $valorcuota = "null";
else
    $valorcuota    = '"'.$mysqli->real_escape_string(''.$_POST['txtValorCu'].'').'"';

$perce = "SELECT * FROM gn_periodo WHERE id_unico = $conc AND liquidado !=1";
$per = $mysqli->query($perce);
$nper = mysqli_num_rows($per);

if($nper > 0){
   
    //modificar ne la base de datos
    $insertSQL = "UPDATE gn_credito SET empleado=$empleado, entidad=$entidad, tipoproceso=$tipoproceso, numerocredito=$numerocredito, fecha=$fecha, periodoinicia=$periodoinicia, valorcredito=$valorcredito, numerocuotas=$numerocuotas, valorcuota=$valorcuota WHERE id_unico = $id";
    $resultado = $mysqli->query($insertSQL);
    
    $x = 0;
}else{
    
    $resultado = 0;
    $x = 1;
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
<!--Modal para informar al usuario que se ha modificado-->
<div class="modal fade" id="myModal1" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>Información modificada correctamente.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>
  <!--Modal para informar al usuario que no se ha podido modificar la información-->
  <div class="modal fade" id="myModal2" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
           <?php if($x == 0){ ?>
                    <p>No se ha podido modificar la información.</p>
           <?php }else{ ?>
                    <p>No se ha podido modificar la información, debido a que el periodo se encuentra cerrado</p>
           <?php } ?>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>
<!--Links para dar estilos a la página-->
<script type="text/javascript" src="../js/menu.js"></script>
  <link rel="stylesheet" href="../css/bootstrap-theme.min.css">
  <script src="../js/bootstrap.min.js"></script>
<!--Vuelve a carga la página de listar mostrando la informacion modificada-->
<?php if($resultado==true){ ?>
<script type="text/javascript">
  $("#myModal1").modal('show');
  $("#ver1").click(function(){
    $("#myModal1").modal('hide');
    window.location='../listar_GN_CREDITO.php';
  });
</script>
<?php }else{ ?>
<script type="text/javascript">
  $("#myModal2").modal('show');
</script>
<?php } ?>