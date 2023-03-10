<?php
require_once('Conexion/conexion.php');
require_once ('./Conexion/conexion.php');
# session_start();
$id = (($_GET["id"]));
$sql = "SELECT   tc.id_unico,
                   tc.codigo,
                   tc.nombre,
                   tc.sigla_sancion,
                   tc.sancion,
                   tc.valor_sancion, 
                   tc.anno 
                FROM gu_tipo_comparendo tc	 
                where md5(tc.id_unico) = '$id'";
$resultado = $mysqli->query($sql);
$row = mysqli_fetch_row($resultado);

$id_u = $row[0];
$cod = $row[1];
$nom = $row[2];
$sigla = $row[3];
$sancion = $row[4];
$vlr = $row[5];
$anno = $row[6];

require_once './head.php';
?>
<title>Modificar Tipo Comparendo</title>
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php';
            ?>
            <div class="col-sm-10 text-left" style="margin-top: -20px">
                <h2 id="forma-titulo3" align="center" style="margin-right: 4px; margin-left: 4px;">Modificar Tipo Comparendo</h2>
                <a href="listar_GU_TIPO_COMPARENDO.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: white; border-radius: 5px">Tipo:<?php echo $cod;?></h5>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                    <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificarTipoComparendoJson.php">
                        <input type="hidden" name="id" value="<?php echo $row[0] ?>">
                        <p align="center" style="margin-bottom: 25px; margin-top: 25px;margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="anno" class="col-sm-5 control-label"><strong class="obligado">*</strong>A??o:</label>
                            <input type="text" name="anno" id="anno" value="<?php echo $anno;?>" class="form-control" maxlength="4" title="Ingrese el valor" onkeypress="return txtValida(event, 'num')" placeholder="A??o" required="required">
                        </div> 
                        <!----------Campo para llenar Codigo Interno-->
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="codigo" class="col-sm-5 control-label"><strong class="obligado">*</strong>C??digo:</label>
                            <input type="text" name="txtCodigo" id="txtCodigo" class="form-control" value="<?php echo $cod ?>" maxlength="100" title="Ingrese el c??digo" onkeypress="return txtValida(event, 'num_car')" placeholder="C??digo" required>
                        </div>                                    
                        <!----------Fin Campo Codigo Interno-->
                        <!----------Campo para llenar Nombre-->
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="Nombre" class="col-sm-5 control-label"><strong class="obligado"></strong>Nombre:</label>
                            <input type="text" name="txtNombre" id="txtNombre" class="form-control" value="<?php echo $nom ?>" maxlength="100" title="Ingrese el nombre" onkeypress="return txtValida(event, 'car')" placeholder="Nombre">
                        </div>                                    
                        <!----------Fin Campo Nombre-->
                        <!----------Campo para llenar Sigla Sanci??n-->
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="Sigla" class="col-sm-5 control-label"><strong class="obligado"></strong>Sigla Sanci??n:</label>
                            <input type="text" name="txtSigla" id="txtSigla" class="form-control" value="<?php echo $sigla ?>" maxlength="100" title="Ingrese la Sigla Sanci??n" onkeypress="return txtValida(event, 'car')" placeholder="Sigla Sanci??n">
                        </div>                                    
                        <!----------Fin Campo Sigla Sanci??n-->

                        <!----------Campo para llenar Sanci??n-->
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="Sancion" class="col-sm-5 control-label"><strong class="obligado"></strong>Sanci??n (Detallada):</label>
                            <input type="text" name="txtSancion" id="txtSancion" class="form-control" value="<?php echo $sancion ?>" maxlength="100" title="Ingrese la Sanci??n" onkeypress="return txtValida(event, 'car')" placeholder="Sanci??n">
                        </div>                                    
                        <!----------Fin Campo Sanci??n-->

                        <!----------Campo para llenar Gasto Representaci??n-->
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="Valor" class="col-sm-5 control-label"><strong class="obligado">*</strong>Valor Sanci??n:</label>
                            <input type="text" name="txtValor" id="txtValor" class="form-control" value="<?php echo $vlr ?>" maxlength="100" title="Ingrese el valor" onkeypress="return txtValida(event, 'num')" placeholder="Valor Sanci??n" required>
                        </div>                                    
                        <!-------------------------Fin campo Gasto Representaci??n-->
                        <div class="form-group" style="margin-top: 10px;">
                            <label for="no" class="col-sm-5 control-label"></label>
                            <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left: 0px;">Guardar</button>
                        </div>


                    </form>
                </div>
            </div>                  
        </div>
    </div>
<?php require_once './footer.php'; ?>
</body>
</html>
