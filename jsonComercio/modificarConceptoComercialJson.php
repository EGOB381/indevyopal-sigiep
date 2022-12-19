<?php
    require_once('../Conexion/conexion.php');
    session_start();
    
    //Captura de datos e instrucción SQL para su modificación en la tabla 
    $codigo       = $_POST['codigo'];
    $descripcion  = $_POST['descripcion'];
    $tipo         = $_POST['tipo'];
    $formula      = $_POST['formula'];
    $concepto     = "NULL";
    $Tope         = $_POST['TO'];
    #$con_inf      = $_POST['con_inf'];
    $aplD			= $_POST['txtDesc'];
    $aplI			= $_POST['txtInt'];
    $aplA			= $_POST['txtAnt'];
    $clase          = $_POST['ClaseC'];
    if(empty($formula)){
        $formula = null;
    }

    $id  = '"'.$mysqli->real_escape_string(''.$_POST['id'].'').'"';

    $update = "UPDATE gc_concepto_comercial SET codigo='$codigo', descripcion='$descripcion', tipo=$tipo, formula='$formula', 
                concepto_rel=$concepto, tipo_ope = '$Tope', apli_descu = $aplD, apli_inte = $aplI, anticipo = $aplA, clase = $clase  WHERE id_unico=$id";
    $resultado = $mysqli->query($update);
  
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
<!-- Divs de clase Modal para las ventanillas de modificación. -->
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
<div class="modal fade" id="myModal2" role="dialog" align="center">
    <div class="modal-dialog">
        <div class="modal-content">   
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>No se ha podido modificar la información.</p>
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

<?php   if($resultado==true){ ?>
<!-- Script que redirige a la página inicial de Cargo. -->
            <script type="text/javascript">
                $("#myModal1").modal('show');
                $("#ver1").click(function(){
                    $("#myModal1").modal('hide');
                    window.location='../listar_GC_CONCEPTO_COMERCIAL.php';
                });
            </script>
<?php   }else{ ?>
            <script type="text/javascript">
                $("#myModal2").modal('show');
                $("#ver2").click(function(){
                    $("#myModal2").modal('hide');
                    window.location='../listar_GC_CONCEPTO_COMERCIAL.php';
                });  
            </script>
<?php   } ?>