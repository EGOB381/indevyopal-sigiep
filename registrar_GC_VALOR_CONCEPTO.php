<?php


require_once('Conexion/conexion.php');
require_once 'head.php'; 

@$clase = $_GET['ClaseC'];

if(empty($clase)){
    $Datos = "Clase Concepto";
}else{
    $nombreC = "SELECT id_unico , nombre FROM  gc_clase_concepto WHERE id_unico = '$clase'";
    $nomC = $mysqli->query($nombreC);
    $NC = mysqli_fetch_row($nomC);
    $Datos = $NC[1];
}

?>
<title>Registrar Valor Concepto</title>
</head>
<body>

    <link href="css/select/select2.min.css" rel="stylesheet">
    <script src="dist/jquery.validate.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
    <script src="js/jquery-ui.js"></script>

    <style>
        label #sltctai-error,#sltctaiT-error,#sltctaiR-error, #sltPerG-error, #txtvalor-error, #sltClase-error{
            display: block;
            color: #155180;
            font-weight: normal;
            font-style: italic;

        }

        body{
            font-size: 12px;
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
                    sltmes: {
                        required: true
                    },
                    sltcni: {
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

    <style>
        .form-control {font-size: 12px;}

    </style>

    <!-- contenedor principal -->
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once ('menu.php'); ?>
            <div class="col-sm-7 text-left" style="margin-top:-10px">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;margin-bottom: 10px;">Registrar Valor Concepto</h2>
                <!--volver-->
                <a href="listar_GC_VALOR_CONCEPTO.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:8px;margin-top: -5.5px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                <h5 id="forma-titulo3a" align="center" style="width:95%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-10px;  background-color: #0e315a; color: white; border-radius: 5px;color:#0e315a;">.</h5> 
                <!---->
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                    <!-- inicio del formulario --> 
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="jsonComercio/registrarValorConceptoJson.php" >                 <!-- <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarFuenteJson.php">-->
                        <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <input type="hidden" name="id" value="<?php echo $row[0] ?>">
                    
                        <div class="form-group">
                            
                            <?php
                                if(empty($clase)){
                                    $ClaseCo = "SELECT id_unico, nombre FROM gc_clase_concepto ";
                                }else{
                                     $ClaseCo = "SELECT id_unico, nombre FROM gc_clase_concepto WHERE id_unico != $clase";
                                }
                               
                                $claC = $mysqli->query($ClaseCo);
                            ?>
                            <div class="form-group" style="margin-top: -10px">
                                <label for="claCo" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Clase Concepto:</label>
                                <?php
                                    if(empty($clase)){
                                ?>
                                        <input type="radio" name="claCo" id="cla1" value="1" required="required"> Comercio
                                        <input type="radio" name="claCo" id="cla2" value="2" > Ica
                                <?php
                                    }else{
                                        
                                        if($clase == 1){
                                ?>
                                        <input type="radio" name="claCo" id="cla1" value="1" required="required" checked> Comercio
                                        <input type="radio" name="claCo" id="cla2" value="2" > Ica
                                <?php
                                        }else{
                                ?>
                                            <input type="radio" name="claCo" id="cla1" value="1" required="required"> Comercio
                                            <input type="radio" name="claCo" id="cla2" value="2" checked > Ica
                                <?php
                                        }
                                    }
                                ?>
                                
                                <!--<select name="sltClase" id="sltClase" required="required" style="height: auto" class="select2_single form-control"  title="Clase Concepto">
                                    <?php
                                        /*echo "<option value=\"$NC[0]\">$Datos</option>";
                                        while($row=mysqli_fetch_row($claC)){ ?> 
                                            <option value="<?php echo $row[0]?>"><?php echo $row[1]?></option>
                                    <?php 
                                        } */
                                    ?>
                                </select>-->
                            </div><br>   
                            <script type="text/javascript">
                                $("#cla1").click(function(){
                                    var val1 = ($("#cla1").val());  
                                    document.location = 'registrar_GC_VALOR_CONCEPTO.php?ClaseC='+val1; 
                                });
                                
                                $("#cla2").click(function(){
                                    var val1 = ($("#cla2").val());  
                                    document.location = 'registrar_GC_VALOR_CONCEPTO.php?ClaseC='+val1; 
                                });
                            </script>
                            <?php
                                $cuentaI = "SELECT id_unico, descripcion FROM gc_concepto_comercial WHERE clase = '$clase' ";
                                $rsctai = $mysqli->query($cuentaI);
                            ?>
                            <div class="form-group" style="margin-top: -10px">
                                <label for="sltctai" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Concepto Comercial:</label>
                                <select name="act_comer" id="sltctai" required="true" style="height: auto" class="select2_single form-control"  title="Seleccione Concepto Comercial">
                                    <option value="">Concepto Comercial</option>
                                    <?php while($row=mysqli_fetch_row($rsctai)){ ?> 
                                        <option value="<?php echo $row[0]?>"><?php echo $row[1]?></option>
                                    <?php } ?>
                                </select>
                            </div><br>      


                            <?php
                                $cuentaI = "SELECT t.id_unico,tt.nombre,t.periodo from gp_tarifa  t
                                            LEFT JOIN gp_tipo_tarifa tt ON tt.id_unico=t.tipo_tarifa";
                                $rsctai = $mysqli->query($cuentaI);
                            ?>
                            <div class="form-group" style="margin-top: -10px">
                                <label for="txtvalor" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Valor:</label>
                                <input type="text"  style="height: 30px;" name="txtvalor" id="txtvalor" required title="Ingrese un valor">
                            </div><br>  

                            
                            <?php
                                $vigencia = "SELECT id_unico, vigencia FROM gc_vigencia_comercial ORDER BY vigencia DESC";
                                $vige = $mysqli->query($vigencia);
                                
                            ?>
                            <div class="form-group" style="margin-top: -10px">
                                <label for="sltPerG" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Vigencia:</label>
                                <select name="sltPerG" id="sltPerG" required="true" style="height: auto" class="select2_single form-control"  title="Seleccione Régimen">
                                    <option value="">Vigencia</option>
                                    <?php while($rowPG=mysqli_fetch_array($vige)){ ?> 
                                        <option value="<?php echo $rowPG[0]?>"><?php echo $rowPG[1] ?></option>
                                    <?php } ?>
                                </select>
                            </div><br>
                        
                      
                        <div class="form-group" style="margin-top: 10px;">
                            <label for="no" class="col-sm-5 control-label"></label>
                            <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left: 0px;">Guardar</button>
                        </div>
                    </div>             
                </form>
                <!-- Fin de división y contenedor del formulario -->           
            </div>     
        </div>
        <div class="col-sm-3 col-sm-3" style="margin-top:-12px">
            <table class="tablaC table-condensed" >
                <thead>
                    <tr>
                        <th><h2 class="titulo" align="center" style=" font-size:17px;">Adicional</h2></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <a href="registrar_GC_CONCEPTO_COMERCIAL.php" class="btn btn-primary btnInfo">Concepto Comercial</a><br>
                            
                        </td>
                    </tr>
                    <tr>
                    </tr>
                    <tr>
                        <td></td>
                    </tr>
                </tbody>
            </table>                
        </div>
        <!-- Fin del Contenedor principal -->
        <!--Información adicional -->
    </div>
    <script src="js/select/select2.full.js"></script>
    <script>
        $(document).ready(function() {
        $(".select2_single").select2({
            allowClear: true
        });
        });
    </script>
        <!-- Llamado al pie de pagina -->


    <?php require_once 'footer.php' ?>
</body>
</html>