<?php 
require_once('Conexion/conexion.php');
require_once('Conexion/ConexionPDO.php');
require_once('jsonPptal/funcionesPptal.php');
require_once('head_listar.php');
$con        = new ConexionPDO();
$anno       = $_SESSION['anno'];
$compania   = $_SESSION['compania'];
$tr         = tipo_cambio($compania);
$id         = " ";
$queryCond  ="";

$id     = (($_GET["id"]));
$_SESSION['url'] = 'Modificar_GP_CONCEPTO.php?id='.$id;
$queryCond = "SELECT c.id_unico, c.nombre,  tc.id_unico, tc.nombre,  
    top.id_unico,top.nombre, 
    pi.id_unico, pi.codi, pi.nombre, 
    fb.id_unico, fb.nombre, 
    c.alojamiento,
    ca.id_unico,
    ca.nombre,
    c.ajuste, 
    c.traduccion 
    FROM gp_concepto c 
    LEFT JOIN gp_tipo_concepto tc ON c.tipo_concepto=tc.id_unico 
    LEFT JOIN gp_tipo_operacion top ON c.tipo_operacion = top.id_unico 
    LEFT JOIN gf_plan_inventario pi ON c.plan_inventario = pi.id_unico 
    LEFT JOIN gp_factor_base fb ON c.factor_base = fb.id_unico 
    LEFT JOIN gp_concepto ca ON c.concepto_asociado = ca.id_unico 
    WHERE md5(c.id_unico)='$id'"; 
$resul = $mysqli->query($queryCond);
$row = mysqli_fetch_row($resul);

//Tipo concepto
$tipo_con = "SELECT id_unico, nombre FROM gp_tipo_concepto WHERE id_unico != $row[2] ORDER BY nombre ASC";
$tipoC = $mysqli->query($tipo_con);

//Tipo Operación
$tipo_op= "SELECT id_unico, nombre FROM gp_tipo_operacion WHERE id_unico != $row[4] ORDER BY nombre ASC";
$tipoO = $mysqli->query($tipo_op); 

//Plan inventario
if(empty($row[6])){
    $plan_in= "SELECT id_unico,codi,nombre FROM gf_plan_inventario WHERE compania = $compania  ORDER BY nombre ASC";
} else {
    $plan_in= "SELECT id_unico,codi,nombre FROM gf_plan_inventario WHERE id_unico != $row[6] AND movimiento = 1 AND compania = $compania ORDER BY codi ASC";
}
$planI = $mysqli->query($plan_in);
//Factor base
if(empty($row[9])){
    $factor_b= "SELECT id_unico, nombre FROM gp_factor_base ORDER BY nombre ASC";
} else {
    $factor_b= "SELECT id_unico, nombre FROM gp_factor_base WHERE id_unico != $row[9] ORDER BY nombre ASC";
}    
$factorB = $mysqli->query($factor_b);

#Concepto Asociado 
if(empty($row[12])){
    $concepto_a  = "SELECT id_unico, nombre FROM gp_concepto 
        WHERE  compania = $compania AND id_unico NOT IN (SELECT id_unico FROM gp_concepto WHERE concepto_asociado IS NOT NULL)";
} else {
    $concepto_a  = "SELECT id_unico, nombre FROM gp_concepto 
        WHERE id_unico != $row[12] AND compania = $compania AND id_unico NOT IN (SELECT id_unico FROM gp_concepto WHERE concepto_asociado IS NOT NULL)";
}
$concepto_a  = $mysqli->query($concepto_a);
?>

<title>Modificar Concepto</title>
<link href="css/select/select2.min.css" rel="stylesheet">
<script src="lib/jquery.js"></script>
<script src="dist/jquery.validate.js"></script>
<style>
label#TipoConcepto-error,#nombre-error, #TipoOperacion-error{
    display: block;
    color: #bd081c;
    font-weight: bold;
    font-style: italic;

}


</style>
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
    rules: {
        param: {
          required: true
        },
        mes: {
          required: true
        },
        sltAnnio: {
          required: true
        }
     }
  });

  $(".cancel").click(function() {
    validator.resetForm();
  });
});
</script>
</head>
<body>

<div class="container-fluid text-center">
    <div class="row content">
    <?php require_once 'menu.php'; ?>
        <div class="col-sm-8 text-left" style="margin-left: -16px;margin-top: -20px"> 
            <h2 align="center" class="tituloform">Modificar Concepto</h2>
            <a href="LISTAR_GP_CONCEPTO.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
            <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: white; border-radius: 5px">Concepto:<?php echo  $row[5]?></h5>
            <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top: -5px" class="client-form">
                <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificar_GP_CONCEPTOJson.php">
                    <p align="center" style="margin-bottom: 25px; margin-top: 5px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                    <input type="hidden" name="id" value="<?php echo $row[0] ?>">
                    <div class="form-group" style="margin-top: -15px;">
                        <label for="TipoConcepto" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Concepto:</label>
                        <select name="TipoConcepto" id="TipoConcepto" class="select2_single form-control" title="Seleccione Tipo Concepto" required="required">
                            <option value="<?php echo $row[2] ?>"><?php echo ucwords(mb_strtolower($row[3]));?></option>
                            <?php while($rowC = mysqli_fetch_assoc($tipoC)){?>
                            <option value="<?php echo $rowC['id_unico'] ?>"><?php echo ucwords((mb_strtolower($rowC['nombre']))); }?></option>            
                        </select> 
                    </div>
                    <div class="form-group" style="margin-top: -5px;">
                        <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                        <input type="text" name="nombre" id="nombre" class="form-control" onkeypress="return txtValida(event,'car')" maxlength="100" title="Ingrese el nombre"  placeholder="Nombre" value="<?php echo ucwords(mb_strtolower($row[1])); ?>" required>
                    </div>
                    <div class="form-group" style="margin-top: -15px;">
                        <label for="TipoOperacion" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Operación:</label>
                        <select name="TipoOperacion" id="TipoConcepto" class="select2_single form-control" title="Seleccione Tipo Operación" required="required">
                            <option value="<?php echo $row[4] ?>"><?php echo ucwords(mb_strtolower($row[5]));?></option>
                            <?php while($rowO = mysqli_fetch_assoc($tipoO)){?>
                            <option value="<?php echo $rowO['id_unico'] ?>"><?php echo ucwords((mb_strtolower($rowO['nombre']))); }?></option>            
                        </select> 
                    </div>
                    <div class="form-group" style="margin-top: -5px;">
                        <label for="planInventario" class="col-sm-5 control-label">Plan Inventario:</label>
                        <select name="planInventario" id="planInventario" class="select2_single form-control" title="Seleccione Plan Inventario" >
                            <?php   
                            if (empty($row[6])) {
                                ECHO '<option value=""> - </option>';
                            } ELSE {
                                echo '<option value="'.$row[6].'">'.$row[7].' - '.$row[8].'</option>';
                                ECHO '<option value=""> - </option>';
                            }
                            while($rowI = mysqli_fetch_assoc($planI)){ ?>
                                <option value="<?php echo $rowI['id_unico'] ?>"><?php echo $rowI['codi'].' - '.ucwords((mb_strtolower($rowI['nombre']))); ?></option>            
                            <?php } ?>
                        </select> 
                    </div>
                    <div class="form-group" style="margin-top:-5px">
                        <label for="factorBase" class="col-sm-5 control-label">Factor Base:</label>
                        <select name="factorBase" id="factorBase" class="select2_single form-control" title="Seleccione Factor Base" >
                            <?php   
                            if (empty($row[9])) {
                                echo '<option value=""> - </option>';
                            } else {
                                echo '<option value="'.$row[9].'">'.$row[10].'</option>';
                                echo '<option value=""> - </option>';
                            }
                            while($rowF = mysqli_fetch_assoc($factorB)) { ?>
                                  <option value="<?php echo $rowF['id_unico'] ?>"><?php echo ucwords((mb_strtolower($rowF['nombre']))); ?></option>  
                            <?php } ?>
                        </select> 
                    </div>
                    <div class="form-group" style="margin-top: -5px;">
                        <label for="alojamiento" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Alojamiento:</label>
                        <div class="col-sm-4 col-md-4 col-md-4">
                            <?php if ($row[11]==1) { ?>
                            <label for="alojamiento" class="radio-inline"><input type="radio" name="alojamiento" id="alojamiento" value="1" checked>Sí</label>
                            <label for="alojamiento" class="radio-inline"><input type="radio" name="alojamiento" id="alojamiento" value="2" >No</label>
                            <?php }  else { ?>
                            <label for="alojamiento" class="radio-inline"><input type="radio" name="alojamiento" id="alojamiento" value="1" >Sí</label>
                            <label for="alojamiento" class="radio-inline"><input type="radio" name="alojamiento" id="alojamiento" value="2" checked>No</label>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="form-group" style="margin-top:-5px;">
                        <label for="concepto_asociado" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Concepto Asociado:</label>
                        <select name="concepto_asociado" id="concepto_asociado"  class="select2_single form-control col-sm-1" title="Seleccione Concepto Asociado" >
                            <?php if (empty($row[12])) { 
                                echo '<option value=""> - </option>';
                            } else {
                                echo '<option value="'.$row[12].'">'.$row[13].'</option>';
                                echo '<option value=""> - </option>';
                            } ?>
                            <?php while($rowca = mysqli_fetch_assoc($concepto_a)){?>
                            <option value="<?php echo $rowca['id_unico'] ?>"><?php echo ucwords((mb_strtolower($rowca['nombre'])));?></option>;
                            <?php } ?>
                        </select> 
                    </div>
                     <?php if ($tr!=0){ ?>
                    <div class="form-group" style="margin-top: -5px;">
                      <label for="ajuste" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Ajuste:</label>
                      <input value="<?php echo $row[14]?>" type="text" name="ajuste" id="ajuste" class="form-control"  maxlength="100" title="Ingrese el Ajuste"  placeholder="Ajuste" >
                    </div>
                    <div class="form-group" style="margin-top: -15px;">
                      <label for="traduccion" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Traducción:</label>
                        <input value="<?php echo $row[15]?>" type="text" name="traduccion" id="traduccion" class="form-control" onkeypress="return txtValida(event,'num_car')"  title="Ingrese Traducción"  placeholder="Traducción">
                    </div>
                     <?php } ?>
                    <div class="form-group" style="margin-top: 10px;">
                        <label for="no" class="col-sm-5 control-label"></label>
                        <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left:0px">Guardar</button>
                    </div>
                    <input type="hidden" name="MM_insert" >
                </form>
            </div>
        </div>
        <div class="col-sm-6 col-sm-2" style="margin-top:-22px" >
            <table class="tablaC table-condensed" style="margin-left: -3px; ">
                <thead>
                    <th>
                        <h2 class="titulo" align="center" style=" font-size:17px; height:36px">Adicional</h2>
                    </th>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <a href="GP_CONCEPTO_TARIFA.php?id=<?php echo $id;?>"><button class="btn btnInfo btn-primary" >TARIFA</button></a><br/>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php  require_once 'footer.php';?>
</body>
<script src="js/select/select2.full.js"></script>
<link rel="stylesheet" href="css/bootstrap-theme.min.css">
<script src="js/bootstrap.min.js"></script>
<script>
    $(document).ready(function() {
      $(".select2_single").select2({
        
        allowClear: true
      });
     
      
    });
</script>
