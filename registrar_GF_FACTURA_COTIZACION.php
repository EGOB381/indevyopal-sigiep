<?php
##########################################################################################
#   ****************************    Modificaciones      ****************************    #
##########################################################################################
# 22/03/2018 | Erica G. | Busqueda por tipo de comprobante
##########################################################################################
# Fecha         :   24/08/2017
# Modifico      :   Alexander Numpaque
# Descripción   :   Se agrego registro y validación cuando el concepto no tiene relacionado rubro fuente, de manera que se ocultara el campo de rubros y mostrara
# dos nuevos campos uno para seleccionar los rubros relacionados al concepto en concepto rubro y otro para seleccionar la fuente que se va a relacionar.
# También se incluyo metodó de suma para cuando se modificá el campo de iva, este sume el valor al valor total y lo ajuste al valor final
##################################################################################################################################################################
# Fecha         :   24/05/2017
# Modifico      :   Alexander Numpaque
# Descripción   :   Se valido proceso para modifcar comprobante cnt y pptal (cabezas)
##################################################################################################################################################################
# Fecha         :   17/05/2017
# Modifico      :   Alexander Numpaque
# Descripción   :   Se cambio validaciòn para procesar el valor de la tarifa
##################################################################################################################################################################
# Fecha         :   25/04/2017
# Modifico      :   Alexander Numpaque
# Descripción   :   Se agrego botón y envio ajax para eliminado en cascada
##################################################################################################################################################################
# Fecha : 23/02/2017
# Hora  : 02:50 p.m
# Modificó : Jhon Numpaque
# Descripción : Se agrego Validación para cuando tarifa es valor 0, y al ser valor 0 se cambia de forma automatica el select por un input y se habilita
# el proceso de escritura para los campos, en lo cual el usuario tan solo debe ingresar el porcentaje, para que el sistema haga de manera automatizada,
# la generación de valores
##################################################################################################################################################################
#Referencias de cabezera y conexión
require_once './head.php';
require_once './Conexion/conexion.php';
require_once './funciones/funciones_consulta.php';
require_once './modelFactura/factura.php';
require_once './modelFactura/comprobanteContable.php';
require_once './modelFactura/comprobantePptal.php';
require_once './modelFactura/detallefactura.php';

list($fecha, $factura, $tipofactura, $numeroFactura, $tercero, $centroCosto, $fechaVencimiento, $descripcion, $estado, $idFactura, $idPptal, $idCnt, $estFat, $vendedor, $salida, $anno )
    = array("", "", "", '', "", "",  "", "", "", "", 0, 0, "", "", "", $_SESSION['anno']);

$fat = new factura();
$cnt = new comprobanteContable();
$ptl = new comprobantePptal();
$det = new detalleFactura();

if(!empty($_GET['factura'])){
    $factura          = $_GET['factura'];
    $valoresFactura   = $fat->obtnerFactura($_GET['factura']);
    list( $idFactura, $tipofactura, $numeroFactura, $tercero, $centroCosto, $fecha, $fechaVencimiento, $descripcion, $estado, $vendedor )
        = array( $valoresFactura[0] ,$valoresFactura[1], $valoresFactura[2], $valoresFactura[3], $valoresFactura[4], $valoresFactura[5], $valoresFactura[6], $valoresFactura[7], $valoresFactura[8], $valoresFactura[19] );
    if(!empty($_GET['cnt'])){ $idCnt = $cnt->obtner($_GET['cnt']); }
    if(!empty($_GET['pptal'])){ $idPptal = $ptl->obtner($_GET['pptal']);}
    $estFat  = ucwords(mb_strtolower($fat->obtnerEstado($estado)));
}

if(empty($estado)){ $estFat  = ucwords(mb_strtolower($fat->obtnerEstado(4))); }

$tipo_co  = $fat->obtnerTipoCompania($_SESSION['compania']);

$url   = "access.php?controller=Factura&action=registrarCot";
$url  .= !empty($_GET['mov'])?'&mov='.$_GET['mov']:'';
$urlD  = "access.php?controller=DetalleFactura&action=registrarCot";
$urlD .= !empty($_GET['mov'])?'&mov='.$_GET['mov']:'';

if(!empty($_GET['mov'])){ $salida = 'registrar_GR_SALIDA_ALMACEN.php?movimiento='.$_GET['mov'];}
?>
 <link rel="stylesheet" href="css/jquery-ui.css">
        <script src="js/jquery-ui.js"></script>
        <link rel="stylesheet" href="css/select2.css">
        <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
        <link rel="stylesheet" href="css/desing.css">
        
        <link href="css/select/select2.min.css" rel="stylesheet">
        <script src="dist/jquery.validate.js"></script>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">

<script type="text/javascript">
    $(function(){
        $.datepicker.regional['es'] = {
            closeText: 'Cerrar',
            prevText: 'Anterior',
            nextText: 'Siguiente',
            currentText: 'Hoy',
            monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
            monthNamesShort: ['Enero','Febrero','Marzo','Abril', 'Mayo','Junio','Julio','Agosto','Septiembre', 'Octubre','Noviembre','Diciembre'],
            dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
            dayNamesShort: ['Dom','Lun','Mar','Mié','Juv','Vie','Sáb'],
            dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sá'],
            weekHeader: 'Sm',
            dateFormat: 'dd/mm/yy',
            firstDay: 1,
            isRTL: false,
            showMonthAfterYear: false,
            yearSuffix: ''
        };
        $.datepicker.setDefaults($.datepicker.regional['es']);
        $("#fecha").datepicker({changeMonth: true}).val();
        $("#fechaV").datepicker({changeMonth: true}).val();
        $("#txtFechaC").datepicker({changeMonth: true}).val();
    });
    /*
     * En esta función enviamos el valor el cual es número, esta función
     * redondea automaticamente los valores
     * @param {double} numero
     * @param {double} decimales
     * @returns {redo}
     */
    function redondeo(numero, decimales){
        var flotante = parseFloat(numero);
        var resultado = Math.round(flotante*Math.pow(10,decimales))/Math.pow(10,decimales);
        var falta = resultado - flotante;
        var redo = falta.toFixed(2);
        return redo;
    }

    /*
     * x = al número o valor decimal
     * r = al valor de redondeo puede ser 1,10,100.. etc
     * t = es el valor que hace falta para el redondeo
     * @param {double} x
     * @param {double} r
     * @returns {t}
     */
    function redondeaAlAlza(x,r) {
        xx = Math.floor(x/r)
        if (xx!=x/r) {xx++}
        var val = (xx*r);
        var rt = (val-x);
        var t = rt.toFixed(2);
        return t;
    }

    /*
     *
     * @param {type} id
     * @returns {undefined}
     */

    function redondeoTotal(valor,ajuste) {
        xx = Math.round(valor);
        return xx;
    }

    $(document).ready(function() {
        var i= 1;
        $('#tabla thead th').each( function () {
            if(i != 1){
                var title = $(this).text();
                switch (i){
                    case 3:
                        $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                        break;
                    case 4:
                        $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                        break;
                    case 5:
                        $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                        break;
                    case 6:
                        $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                        break;
                    case 7:
                        $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                        break;
                    case 8:
                        $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                        break;
                    case 9:
                        $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                        break;
                }
                i = i+1;
            }else{
                i = i+1;
            }
        });
        // DataTable
        var table = $('#tabla').DataTable({
            "autoFill": true,
            "scrollX": true,
            "pageLength": 5,
            "language": {
                "lengthMenu": "Mostrar _MENU_ registros",
                "zeroRecords": "No Existen Registros...",
                "info": "Página _PAGE_ de _PAGES_ ",
                "infoEmpty": "No existen datos",
                "infoFiltered": "(Filtrado de _MAX_ registros)",
                "sInfo":"Mostrando _START_ - _END_ de _TOTAL_ registros","sInfoEmpty":"Mostrando 0 - 0 de 0 registros"
            },
            'columnDefs': [{
                'targets': 0,
                'searchable':false,
                'orderable':false,
                'className': 'dt-body-center'
            }]
        });
        var i = 0;
        table.columns().every( function () {
            var that = this;
            if(i!=0){
                $( 'input', this.header() ).on( 'keyup change', function () {
                    if ( that.search() !== this.value ) {
                        that
                            .search( this.value )
                            .draw();
                    }
                } );
                i = i+1;
            }else{
                i = i+1;
            }
        } );
    } );
</script>
<title>Cotización</title>
<style>
    table.dataTable thead th,table.dataTable thead td{padding:1px 18px;font-size:10px}
    table.dataTable tbody td,table.dataTable tbody td{padding:1px}
    .dataTables_wrapper .ui-toolbar{padding:2px}

    body{
        font-family: Arial;
        font-size: 10px;
    }

    .valorLabel{
        font-size: 10px;
        white-space:nowrap
    }
    .valorLabel:hover{
        cursor: pointer;
        color:#1155CC;
    }

    .campos{
        padding: 0px;
        font-size: 10px
    }

    .campoD{
        font-size: 12px;
        height: 19px;
        padding: 2px;
    }

    .cabeza{
        white-space:nowrap;
        padding: 20px;
    }

    .campos{
        padding:-20px;
    }

    .client-form input[type="text"]{
        width: 100%;
    }

    .client-form textarea{
        width: 100%;
        height: 34px;
    }

    .privada, .herencia{
        display: none;
    }
</style>
</head>
<body onload="return limpiarCampos()">
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 col-md-10 col-lg-10 text-left">
                <h2 align="center" style="margin-top:-2px;" class="tituloform">Pre-Factura</h2>
                <div class="col-sm-12 col-md-12 col-lg-12">
                    <div style="margin-top:-7px; border:4px solid #020324;border-radius: 10px;" class="client-form">
                        <form id="form" name="form" class="form-horizontal" method="POST" enctype="multipart/form-data" action="<?php echo $url; ?>" style="margin-bottom: -20px;">
                            <p align="center" class="parrafoO" style="margin-bottom: -0.00005em;">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                            <div class="form-group">
                                <label for="sltTipoFactura" class="control-label col-sm-2 col-md-2 col-lg-2"><strong class="obligado">*</strong>Tipo Factura:</label>
                                <div class="col-sm-2 col-md-2 col-lg-2">
                                    <select name="sltTipoFactura" id="sltTipoFactura" class="form-control"  title="Seleccione el tipo de factura" required="">
                                        <?php
                                        if(empty($tipofactura)){
                                            echo "<option value=\"\">Tipo Factura</option>";
                                            $sqlT = "SELECT id_unico, nombre FROM gp_tipo_factura WHERE servicio = 2 AND clase_factura = 4 ORDER BY nombre ASC";
                                            $rest = $mysqli->query($sqlT);
                                            while($rowt = mysqli_fetch_row($rest)){
                                                echo "<option value=\"$rowt[0]\">".ucwords(mb_strtolower($rowt[1]))."</option>";
                                            }
                                        }else{
                                            $sqlT="SELECT id_unico, nombre FROM gp_tipo_factura WHERE id_unico = $tipofactura AND clase_factura = 4";
                                            $resultT=$mysqli->query($sqlT);
                                            $tpf= mysqli_fetch_row($resultT);
                                            echo '<option value="'.$tpf[0].'">'.ucwords(mb_strtolower($tpf[1])).'</option>';

                                        }
                                        ?>
                                    </select>
                                </div>
                                <label for="txtNumeroF" class="control-label col-sm-1 col-md-1 col-lg-1"><strong class="obligado">*</strong>Nro:</label>
                                <div class="col-sm-2 col-md-2 col-lg-2">
                                    <input type="text" name="txtNumeroF" id="txtNumeroF" class="form-control" style="cursor:pointer;padding:2px;" title="Número de factura" placeholder="Nro de Factura" value="<?php echo $numeroFactura; ?>" required="" readonly/>
                                </div>
                                <label for="fecha" class="control-label col-sm-1 col-md-1 col-lg-1"><strong class="obligado">*</strong>Fecha:</label>
                                <div class="col-sm-2 col-md-2 col-lg-2">
                                    <input class="form-control" value="<?php echo $fecha ?>" type="text" name="fecha" id="fecha" onchange="validarFecha();change_date()" title="Ingrese la fecha" placeholder="Fecha" readonly required>
                                </div>
                            </div>
                            <div class="form-group" style="margin-top: -15px;">
                                <label class="control-label col-sm-2 col-md-2 col-lg-2"><strong class="obligado">*</strong>Fecha Vencimiento:</label>
                                <div class="col-sm-2 col-md-2 col-lg-2">
                                    <input class="form-control" value="<?php echo $fechaVencimiento ?>" type="text" name="fechaV" id="fechaV" onchange="diferents_date()" title="Ingrese la fecha" placeholder="Fecha Vencimiento" readonly required>
                                </div>
                                <label class="control-label col-sm-1 col-md-1 col-lg-1"><strong class="obligado">*</strong>Centro Costo:</label>
                                <div class="col-sm-2 col-md-2 col-lg-2">
                                    <select name="sltCentroCosto" id="sltCentroCosto" class="form-control select2" title="Seleccione centro de costo" style="padding:-2px;" required>
                                        <?php
                                        if(!empty($centroCosto)){
                                            $sqlC="select id_unico,nombre from gf_centro_costo where id_unico = $centroCosto";
                                            cargar_combos($sqlC);
                                            $sqlD="select id_unico,nombre from gf_centro_costo where id_unico != $centroCosto AND parametrizacionanno = $anno";
                                            cargar_combos($sqlD);
                                        }else{
                                            $sqlC = "SELECT id_unico,nombre from gf_centro_costo WHERE id_unico != 'Varios' AND parametrizacionanno = $anno ORDER BY nombre DESC";
                                            cargar_combos($sqlC);
                                        }
                                        ?>
                                    </select>
                                </div>
                                <label class="control-label col-sm-1 col-sm-1 col-lg-1"><strong class="obligado">*</strong>Tercero:</label>
                                <div class="col-sm-2 col-md-2 col-lg-2">
                                    <select class="form-control select2 t" name="sltTercero" id="sltTercero" id="single" title="Seleccione un tercero para consultar" required>
                                        <?php
                                        if(!empty($tercero)){
                                            $sqltercero="SELECT DISTINCT
                                                                IF(CONCAT_WS(' ', t.nombreuno, t.nombredos, t.apellidouno, t.apellidodos) = '',
                                                                (t.razonsocial),
                                                                CONCAT_WS(' ', t.nombreuno, t.nombredos, t.apellidouno, t.apellidodos)) AS NOMBRE,
                                                                t.id_unico,
                                                                IF(t.digitoverficacion='', t.numeroidentificacion, CONCAT(t.numeroidentificacion, ' - ', t.digitoverficacion))
                                                        FROM    gf_tercero t
                                                        WHERE   t.id_unico = $tercero";
                                            $ter = $mysqli->query($sqltercero);
                                            $per = mysqli_fetch_row($ter);
                                            echo '<option value="'.$per[1].'">'.ucwords(mb_strtolower($per[0].' - '.$per[2])).'</option>';
                                            $tersql="SELECT DISTINCT
                                                        IF(CONCAT_WS(' ', t.nombreuno, t.nombredos, t.apellidouno, t.apellidodos) = '',
                                                          (t.razonsocial),
                                                          CONCAT_WS(' ', t.nombreuno, t.nombredos, t.apellidouno, t.apellidodos)) AS NOMBRE,
                                                          t.id_unico,
                                                          IF(t.digitoverficacion='', t.numeroidentificacion, CONCAT(t.numeroidentificacion, ' - ', t.digitoverficacion))
                                                        FROM gf_tercero t
                                                        WHERE t.id_unico != $tercero";
                                            $tercer = $mysqli->query($tersql);
                                            while($per1 = mysqli_fetch_row($tercer)){
                                                echo '<option value="'.$per1[1].'">'.ucwords(mb_strtolower($per1[0].' - '.$per1[2])).'</option>';
                                            }
                                        }else{
                                            echo "<option value=\"\">Tercero</option>";
                                            $ter2="SELECT DISTINCT
                                                        IF(CONCAT_WS(' ', t.nombreuno, t.nombredos, t.apellidouno, t.apellidodos) = '',
                                                          (t.razonsocial),
                                                          CONCAT_WS(' ', t.nombreuno, t.nombredos, t.apellidouno, t.apellidodos)) AS NOMBRE,
                                                          t.id_unico,
                                                          IF(t.digitoverficacion='', t.numeroidentificacion, CONCAT(t.numeroidentificacion, ' - ', t.digitoverficacion))
                                                   FROM gf_tercero t LIMIT 2000";
                                            $tercero2 = $mysqli->query($ter2);
                                            while($per2 = mysqli_fetch_row($tercero2)){
                                                echo '<option value="'.$per2[1].'">'.ucwords(mb_strtolower($per2[0].' - '.$per2[2])).'</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group" style="margin-top: -15px;" id="cambiar">
                                <label class="control-label col-sm-2 col-md-2 col-lg-2">Estado:</label>
                                <div class="col-sm-2 col-md-2 col-lg-2">
                                    <input type="text" name="txtEstado" id="txtEstado" class="form-control" value="<?php echo $estFat ?>" title="Estado" placeholder="Estado" readonly=""/>
                                </div>
                                <label class="control-label col-sm-1 col-md-1 col-lg-1" for="txtDescripcion">Descripción:</label>
                                <div class="col-sm-5 col-md-5 col-lg-5">
                                    <textarea class="form-control" style="margin-top:0px;" rows="2" name="txtDescripcion" id="txtDescripcion"  maxlength="500" placeholder="Descripción" onkeypress="return txtValida(event,'num_car')" ><?php echo $descripcion ?></textarea>
                                </div>
                            </div>
                            <div class="form-group" style="margin-top: -15px;">
                                <label class="control-label col-sm-2 col-md-2 col-lg-2" for="sltVendedor">Vendedor:</label>
                                <div class="col-sm-2 col-md-2 col-lg-2">
                                    <select class="form-control select2" name="sltVendedor" id="sltVendedor" title="Seleccione un tercero para consultar" required>
                                        <?php
                                        if(!empty($vendedor)){
                                            $sqltercero="SELECT   IF(CONCAT_WS(' ', t.nombreuno, t.nombredos, t.apellidouno, t.apellidodos) = '', 
                                                                    (t.razonsocial),
                                                                    CONCAT_WS(' ', t.nombreuno, t.nombredos, t.apellidouno, t.apellidodos)) AS NOMBRE,
                                                                    t.id_unico,
                                                                    IF(t.digitoverficacion IS NULL OR t.digitoverficacion='',
                                                                      t.numeroidentificacion,
                                                                    CONCAT(t.numeroidentificacion, ' - ', t.digitoverficacion))
                                                            FROM gf_tercero t
                                                            WHERE     t.id_unico = $vendedor";
                                            $ter = $mysqli->query($sqltercero);
                                            $per = mysqli_fetch_row($ter);
                                            echo '<option value="'.$per[1].'">'.ucwords(mb_strtolower($per[0].' - '.$per[2])).'</option>';
                                            $tersql="SELECT DISTINCT
                                                            IF(CONCAT_WS(' ',
                                                            t.nombreuno,
                                                            t.nombredos,
                                                            t.apellidouno,
                                                            t.apellidodos)
                                                            IS NULL OR CONCAT_WS(' ',
                                                            t.nombreuno,
                                                            t.nombredos,
                                                            t.apellidouno,
                                                            t.apellidodos) = '',
                                                            (t.razonsocial),
                                                            CONCAT_WS(' ',
                                                            t.nombreuno,
                                                            t.nombredos,
                                                            t.apellidouno,
                                                            t.apellidodos)) AS NOMBRE,
                                                            t.id_unico,
                                                            IF(t.digitoverficacion IS NULL OR t.digitoverficacion='',
                                                                t.numeroidentificacion,
                                                                CONCAT(t.numeroidentificacion, ' - ', t.digitoverficacion))
                                                   FROM gf_tercero t
                                                   WHERE     tr.id_unico != $vendedor
                                                   ORDER BY  t.numeroidentificacion ASC LIMIT 2000";
                                            $tercer = $mysqli->query($tersql);
                                            while($per1 = mysqli_fetch_row($tercer)){
                                                echo '<option value="'.$per1[1].'">'.ucwords(mb_strtolower($per1[0].' - '.$per1[2])).'</option>';
                                            }
                                        }else{
                                            echo "<option value=\"\">Vendedor</option>";
                                            $ter2="SELECT DISTINCT
                                                        IF(CONCAT_WS(' ',
                                                            t.nombreuno,
                                                            t.nombredos,
                                                            t.apellidouno,
                                                            t.apellidodos)
                                                            IS NULL OR CONCAT_WS(' ',
                                                            t.nombreuno,
                                                            t.nombredos,
                                                            t.apellidouno,
                                                            t.apellidodos) = '',
                                                            (t.razonsocial),
                                                            CONCAT_WS(' ',
                                                            t.nombreuno,
                                                            t.nombredos,
                                                            t.apellidouno,
                                                            t.apellidodos)) AS NOMBRE,
                                                            t.id_unico,
                                                            IF(t.digitoverficacion IS NULL OR t.digitoverficacion='',
                                                                t.numeroidentificacion,
                                                                CONCAT(t.numeroidentificacion, ' - ', t.digitoverficacion))
                                                   FROM gf_tercero t
                                                   ORDER BY  t.numeroidentificacion ASC LIMIT 2000";
                                            $tercero2 = $mysqli->query($ter2);
                                            while($per2 = mysqli_fetch_row($tercero2)){
                                                echo '<option value="'.$per2[1].'">'.ucwords(mb_strtolower($per2[0].' - '.$per2[2])).'</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <label class="control-label col-sm-1 col-md-1 col-lg-1" for="sltBuscar">Buscar Factura:</label>
                                <div class="col-sm-2 col-md-2 col-lg-2">
                                    <select name="sltTipoBuscar" id="sltTipoBuscar" title="Tipo Comprobante" class="form-control">
                                        <option value="">Tipo Factura</option>
                                        <?php $sqlT = "SELECT id_unico, nombre FROM gp_tipo_factura WHERE clase_factura = 4 ORDER BY nombre ASC";
                                        $rest = $mysqli->query($sqlT);
                                        while($rowt = mysqli_fetch_row($rest)){
                                            echo "<option value=\"$rowt[0]\">".ucwords(mb_strtolower($rowt[1]))."</option>";
                                        } ?>
                                    </select>
                                </div>
                                <div class="form-group col-sm-3 col-md-3 col-lg-3">
                                    <select name="sltBuscar" id="sltBuscar" title="Buscar comprobante" class="form-control">
                                        <option value="">Buscar Comprobante</option>
                                    </select>
                                </div>
                                <script>
                                    $("#sltTipoBuscar").change(function(e){
                                        var tipo = e.target.value;
                                        var form_data = { estruc:26, tipo: tipo }
                                        var option = '<option value="">Buscar Comprobante</option>';
                                        $.ajax({
                                            type:'POST',
                                            url:'jsonPptal/consultas.php',
                                            data:form_data,
                                            success: function(data){
                                                var option = option + data;
                                                $("#sltBuscar").html(option);
                                            }
                                        });
                                    })
                                </script>
                                <input type="hidden" name="id" id="id" value="<?php echo $idFactura; ?>" />
                                <div class="col-sm-1 col-md-1 col-lg-1 col-sm-offset-1 col-md-offset-1 col-lg-offset-1" style="margin-top: -140px;">
                                    <a id="btnNuevo" onclick="javascript:nuevo()" class="btn btn-primary borde-sombra btn-group" title="Ingresar nueva factura"><li class="glyphicon glyphicon-plus"></li></a>
                                    <button type="submit" id="btnGuardar" class="btn btn-primary borde-sombra btn-group" title="Guardar factura" style="margin-top: 5px;"><span class="glyphicon glyphicon-floppy-disk"></span></button>
                                    <a class="btn btn-primary borde-sombra btn-group" id="btnImprimir" onclick="informe()" title="Imprimir" style="margin-top: 5px;"><li class="glyphicon glyphicon glyphicon-print"></li></a>
                                    <a class="btn btn-primary borde-sombra btn-group" id="btnModificar" onclick="modificarPago(<?php echo empty($idCnt)?0:$idCnt ?>,<?php echo empty($idPptal)?0:$idPptal ?>, '<?php echo empty($_GET['mov'])?0:$_GET['mov'] ?>')" title="Editar" style="margin-top: 5px;"><li class="glyphicon glyphicon glyphicon-edit"></li></a>
                                    <a class="btn btn-primary borde-sombra btn-group" id="btnEliminar" onclick="eliminarDatos(<?php echo $idFactura ?>,<?php echo empty($idCnt)?0:$idCnt ?>,<?php echo empty($idPptal)?0:$idPptal ?>,'<?php echo empty($_GET['mov'])?0:$_GET['mov'] ?>')" title="Eliminar" style="margin-top: 5px; display:none;"><li class="glyphicon glyphicon-remove"></li></a>
                                    <a class="btn btn-primary borde-sombra btn-group" id="btnRebuilt" onclick="reconstruirComprobantes(<?php echo $idFactura ?>,<?php echo empty($idCnt)?0:$idCnt ?>,<?php echo empty($idPptal)?0:$idPptal ?>)" title="Reconstruir comprobantes cnt y pptal" style="margin-top: 5px; display: none;"><i class="glyphicon glyphicon-retweet"></i></a>
                                </div>
                            </div>
                            <!-- Cierre del formulario -->
                        </form>
                        <!-- Cierre de contenedor de la cabezera -->
                    </div>
                </div>
                <!-- Cierre de cabezera de formulario -->
            </div>
            <!-- Cierre de grib de boostrap -->
            <!-- Inicio de Ingreso de datos de detalle -->
            <div class="col-sm-10 col-md-10 col-lg-10">

            </div>
            <div class="col-sm-10 col-md-10 col-lg-10 text-center" style="margin-left: -20px;" align="">
                <div class="client-form" style="margin-left: 60px;" class="col-sm-12 col-md-12 col-lg-12">
                    <form name="form" id="form-detalle" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="<?php echo $urlD ?>"  style="margin-top:-5px;">
                        <input type="hidden" name="txtIdFactura" id="txtIdFactura" class="hidden" value="<?php echo $idFactura; ?>"/>
                        <input type="hidden" name="txtIdCnt" id="txtIdCnt" class="hidden" value="<?php echo !empty($idCnt)?$idCnt:''; ?>"/>
                        <input type="hidden" name="txtIdPptal" id="txtIdPptal" class="hidden" value="<?php echo !empty($idPptal)?$idPptal:''; ?>"/>
                        <div class="col-sm-2" style="margin-left:-30px; margin-right: 13px;">
                            <div class="form-group"  align="left">
                                <label class="control-label">
                                    <strong class="obligado">*</strong>Concepto:
                                </label>
                                <select name="sltConcepto" id="sltConcepto" class="form-control" title="Seleccione concepto" required="">
                                    <?php
                                    $html  = '<option value="">Concepto</option>';
                                    $sqlConcepto = "SELECT    DISTINCTROW cnp.id_unico, cnp.nombre, pln.codi, unf.nombre
                                                    FROM      gp_concepto_tarifa AS cont
                                                    LEFT JOIN gp_concepto        AS cnp ON cont.concepto           = cnp.id_unico
                                                    LEFT JOIN gf_plan_inventario AS pln ON cnp.plan_inventario     = pln.id_unico
                                                    LEFT JOIN gf_unidad_factor   AS unf ON pln.unidad              = unf.id_unico
                                                    WHERE     cnp.id_unico IS NOT NULL 
                                                    AND cnp.compania = ".$_SESSION['compania']."
                                                    -- AND cnp.parametrizacionanno = $anno 
                                                    ";
                                    $res_c = $mysqli->query($sqlConcepto);

                                    while($row_c = mysqli_fetch_row($res_c)){
                                        $html .= "<option value='$row_c[0]'>$row_c[2] $row_c[1]</option>";
                                    }
                                    echo $html;
                                    ?>
                                </select>
                            </div>
                        </div>
                        <input type="hidden" name="txtConceptoRubro" id="txtConceptoRubro" class="hidden" value="" />
                        <input type="hidden" name="txtFecha" id="txtFecha" class="hidden" value="<?php echo $fecha; ?>"/>
                        <input type="hidden" name="txtTercero" id="txtTercero" class="hidden" value="<?php echo $tercero; ?>"/>
                        <input type="text" name="txtCentroCosto" id="txtCentroCosto" class="hidden" value="<?php echo $centroCosto; ?>"/>
                        <input type="text" name="txtDescr" id="txtDescr" class="hidden" value="<?php echo $descripcion; ?>" />
                        <input type="hidden" name="txtConcepto" value="">
                        <script type="text/javascript">
                            <?php if(!empty($_GET['cnt']) || !empty($_GET['pptal'])){ ?>
                            $("#Rbro").css('display','block');
                            $("#sltConcepto").change(function(){
                                var form_data={
                                    existente:34,
                                    concepto:$("#sltConcepto").val()
                                };
                                var datos='';
                                $.ajax({
                                    type: 'POST',
                                    url: "consultasBasicas/consultarNumeros.php",
                                    data: form_data,
                                    success: function (data, textStatus, jqXHR) {
                                        if(data.length > 0){
                                            datos=data.split(';');
                                            $("#sltRubroFuente").html(datos[0]).fadeIn();
                                            $("#txtConceptoRubro").val(datos[1]);
                                            $("#Rbro").css("display","block");
                                            $("#rubros").css("display", "none");
                                            $("#fuentes").css("display", "none");
                                        }else{
                                            $("#Rbro").css("display","none");
                                            $("#rubros").css("display", "block");
                                            $("#fuentes").css("display", "block");
                                            $.ajax({
                                                type:"POST",
                                                url:"consultasBasicas/consultas_factura.php",
                                                data:{concepto:$("#sltConcepto").val(), x:1},
                                                success:function(data, textStatus, jqXHR){
                                                    $("#sltRubros").html(data).fadeIn();
                                                    $("#sltRubros").css("display","none");
                                                }
                                            });
                                        }
                                    }
                                });
                            });
                            <?php }else{ ?>
                            $("#Rbro").css('display','none');
                            <?php } ?>
                        </script>
                        <div class="col-sm-1" style="margin-right:12px;">
                            <div class="form-group" align="left">
                                <label class="control-label">
                                    <strong class="obligado"></strong>Unidad:
                                </label>
                                <select name="sltUnidad" id="sltUnidad" class="select2 form-control" placeholder="Unidad" style="width: 100%;">
                                    <option value=""></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-1" style="margin-right:11px;width: 40px;">
                            <div class="form-group" align="left">
                                <label class="control-label">
                                    <strong class="obligado"></strong>Cantidad:
                                </label>
                                <input type="text" name="txtCantidad" class="form-control" placeholder="Cantidad" onkeypress="return justNumbers(event);" id="txtCantidad" maxlength="50" style="padding:2px;width:100%;" required="" />
                                <input type="hidden" name="txtCantidadE" id="txtCantidadE">
                            </div>
                        </div>
                        <div class="col-sm-1" style="margin-right:11px;">
                            <div class="form-group" align="left">
                                <label class="control-label">
                                    <strong class="obligado">*</strong>Valor Unit.:
                                </label>
                                <select class="form-control" name="sltValor" id="sltValor" title="Seleccione valor" style="width:100%; padding:2px;" required>
                                    <option value="">Valor Unitario</option>
                                </select>
                                <input type="hidden" name="txtValorX" id="txtValorX">
                            </div>
                        </div>
                        <div class="col-sm-1" style="margin-right:11px; width: 70px;">
                            <div class="form-group" align="left">
                                <label class="control-label">
                                    <strong class="obligado">*</strong>Iva:
                                </label>
                                <input type="text" name="txtIva" class="form-control" placeholder="Iva" onkeypress="return justNumbers(event);" value="" id="txtIva" maxlength="50" style="padding:2px;width:100%;" required="" readonly=""/>
                            </div>
                        </div>
                        <div class="col-sm-1" style="margin-right:11px;">
                            <div class="form-group" align="left">
                                <label class="control-label">
                                    <strong class="obligado">*</strong>Impoconsumo:
                                </label>
                                <input type="text" name="txtImpoconsumo" class="form-control" placeholder="Impoconsumo" onkeypress="return justNumbers(event);" value="" id="txtImpoconsumo" maxlength="50" style="padding:2px;width:100%;" required="" readonly=""/>
                            </div>
                        </div>
                        <div class="col-sm-1" style="margin-right:11px; width: 70px;">
                            <div class="form-group" align="left">
                                <label class="control-label">
                                    <strong class="obligado">*</strong>Ajuste Peso:
                                </label>
                                <input type="text" name="txtAjustePeso" class="form-control" placeholder="Ajuste al Peso" onkeypress="return justNumbers(event);" value="" id="txtAjustePeso" maxlength="50" style="padding:2px;width:100%;" required="" readonly=""/>
                                <?php
                                $sqlAjuste = "SELECT valor FROM gs_parametros_basicos WHERE id_unico = 4";
                                $rsAjuste = $mysqli->query($sqlAjuste);
                                $ajuste   = mysqli_fetch_row($rsAjuste);
                                ?>
                                <script type="text/javascript" >
                                    var Impo = 0.00;
                                    var iva = 0.00;
                                    var valor = 0;
                                    var totalIva = 0;
                                    var totalImpo = 0;
                                    var ajuste = <?php echo $ajuste[0]; ?>;
                                    $(document).ready(function () {
                                        $("#sltValor").attr('disabled',true);
                                        $("#txtAjuste").attr('disabled',true);
                                    });
                                    $("#sltConcepto").change(function() {
                                        var form_data = {
                                            concepto:$("#sltConcepto").val(),
                                            proceso:1
                                        };
                                        $.ajax({
                                            type: 'POST',
                                            url: "consultasFacturacion/consultarValor.php",
                                            data:form_data,
                                            success: function (data) {
                                                if(data!=""){
                                                    $("#sltValor").attr('disabled',false);
                                                    $("#txtAjuste").attr('disabled',false);
                                                    $("#sltValor").html(data).fadeIn();
                                                }else{
                                                    $("#sltValor").attr('disabled',true);
                                                    $("#txtAjuste").attr('disabled',true);
                                                }
                                            }
                                        });
                                    });

                                    $("#sltValor").change(function(){
                                        let valors = $("#sltValor").val();
                                        CambiarValor(valors);
                                    });
                                </script>
                            </div>
                        </div>
                        <div class="col-sm-1" style="margin-right:3px;">
                            <div class="form-group" align="left">
                                <label class="control-label">
                                    <strong class="obligado">*</strong>Valor Total:
                                </label>
                                <input type="text" name="txtValorA" class="form-control" placeholder="Valor Total" onkeypress="return justNumbers(event);" id="txtValorA" maxlength="50" style="padding:2px; width:100%;" required="" readonly=""/>
                            </div>
                        </div>
                        <div class="col-sm-1" align="left" style="margin-top: 20px; width: 40px;">
                            <button type="submit" id="btnGuardarDetalle" class="btn btn-primary borde-sombra"><li class="glyphicon glyphicon-floppy-disk"></li></button>
                            <script type="text/javascript">
                                $(document).ready(function(){
                                    <?php if(empty($idFactura)){ ?>
                                    $("#btnGuardarDetalle").prop('disabled',true);
                                    <?php
                                    }else{ ?>
                                    $("#btnGuardarDetalle").prop('disabled',false);
                                    <?php }  ?>
                                });
                            </script>
                        </div>
                    </form>
                    <!-- Cierre de contenedor de formulario -->
                </div>
                <!-- Fin de ingreso de datos de detalle -->
            </div>
            <!-- Inicio de forma de tabla -->
            <div class="col-sm-10 col-md-10 col-lg-10" style="margin-top: -25px;">
                <!-- Campos ocultos en los que guardamos la id anterior y la nueva id -->
                <input type="hidden" id="idPrevio" value="">
                <input type="hidden" id="idActual" value="">
                <div class="table-responsive contTabla" >
                    <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                        <thead>
                        <tr>
                            <td class="oculto">Identificador</td>
                            <td width="7%" class="cabeza"></td>
                            <td class="cabeza"><strong>Concepto</strong></td>
                            <td class="cabeza"><strong>Cantidad</strong></td>
                            <td class="cabeza"><strong>Valor</strong></td>
                            <td class="cabeza"><strong>Iva</strong></td>
                            <td class="cabeza"><strong>Impoconsumo</strong></td>
                            <td class="cabeza"><strong>Ajuste del peso</strong></td>
                            <td class="cabeza"><strong>Valor Total Ajustado</strong></td>
                        </tr>
                        <tr>
                            <th class="oculto">Identificador</th>
                            <th width="7%" class="cabeza"></th>
                            <th class="cabeza">Concepto</th>
                            <th class="cabeza">Cantidad</th>
                            <th class="cabeza">Valor</th>
                            <th class="cabeza">Iva</th>
                            <th class="cabeza">Impoconsumo</th>
                            <th class="cabeza">Ajuste del peso</th>
                            <th class="cabeza">Valor Total Ajustado</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $sumaCantidad   = 0;
                        $sumaValor      = 0;
                        $sumaIva        = 0;
                        $sumaImpo       = 0;
                        $sumaAjuste     = 0;
                        $sumaValortotal = 0;
                        if(!empty($_GET['factura'])){
                            $result = $det->obtnerListados($idFactura);
                            while($row=mysqli_fetch_row($result)){ ?>
                                <tr>
                                    <td class="oculto"></td>
                                    <td class="campos" onloadstart="javascript:inhabilitar(<?php echo $row[0] ?>)">
                                        <?php
                                        $id_dd = "$row[0],";
                                        $id_dd .= !empty($row[10])?$row[10]:0;
                                        ?>
                                        <a href="#<?php echo md5($row[0]);?>" onclick="javascript:eliminar(<?php echo $id_dd; ?>)" id="btnDel<?php echo $row[0]; ?>" title="Eliminar">
                                            <li class="glyphicon glyphicon-trash"></li>
                                        </a>
                                        <a href="#<?php echo md5($row[0]);?>" title="Modificar" id="mod" onclick="javascript:modificar(<?php echo $row[0]; ?>);javascript:cargarValor(<?php echo $row[0]; ?>);javascript:cambioValor(<?php echo $row[0]; ?>);javascript:calcularValores(<?php echo $row[0]; ?>);javascript:calcularValoresEscrito(<?php echo $row[0]; ?>)">
                                            <li class="glyphicon glyphicon-edit"></li>
                                        </a>
                                    </td>
                                    <td class="campos">
                                        <?php echo '<label class="valorLabel" style="font-weight: normal;" id="concepto'.$row[0].'">'.ucwords(strtolower($row[2])).'</label>'; ?>
                                        <select class="col-sm-12 campoD form-control" name="sltconcepto<?php echo $row[0] ?>" id="sltconcepto<?php echo $row[0] ?>" title="Seleccione concepto" style="display: none;">
                                            <option value="<?php echo $row[1]; ?>"><?php echo $row[2]; ?></option>
                                            <?php
                                            $sqlCn = "SELECT cnp.id_unico,cnp.nombre FROM gf_concepto con
                                                        LEFT JOIN gp_concepto cnp ON cnp.concepto_financiero = con.id_unico
                                                        WHERE cnp.id_unico != $row[1]
                                                        ORDER BY cnp.nombre DESC";
                                            $resc = $mysqli->query($sqlCn);
                                            while($row2 = mysqli_fetch_row($resc)){
                                                echo '<option value="'.$row2[0].'">'.$row2[1].'</option>';
                                            }
                                            ?>
                                        </select>
                                    </td>
                                    <td class="campos text-right">
                                        <?php echo '<label class="valorLabel" style="font-weight:normal" id="lblCantidad'.$row[0].'">'.$row[3].'</label>';
                                        echo '<input maxlength="50" onkeypress="return justNumbers(event)" style="display:none;" class="col-sm-12 campoD text-left form-control"  type="text" name="txtcantidad'.$row[0].'" id="txtcantidad'.$row[0].'" value="'.$row[3].'" />';
                                        $sumaCantidad += $row[3];
                                        ?>
                                    </td>
                                    <td class="campos text-right">
                                        <?php echo '<label class="valorLabel" style="font-weight:normal" id="lblValor'.$row[0].'">'.number_format($row[4], 2, '.', ',').'</label>';
                                        //echo '<input maxlength="50" onkeypress="return justNumbers(event)" style="display:none;padding:2px;height:19px" class="col-sm-12 campoD text-left"  type="text" name="txtValor'.$row[0].'" id="txtValor'.$row[0].'" value="'.$row[4].'" />';
                                        $sumaValor += $row[4];
                                        ?>
                                        <select class="col-sm-12 campoD form-control" name="txtValor<?php echo $row[0] ?>" id="txtValor<?php echo $row[0] ?>" title="Seleccione valor" style="display: none;">
                                        </select>
                                    </td>
                                    <td class="campos text-right">
                                        <?php echo '<label class="valorLabel" style="font-weight:normal" id="lblIva'.$row[0].'">'.number_format($row[5], 2, '.', ',').'</label>';
                                        echo '<input maxlength="50" onkeypress="return justNumbers(event);" onkeyup="return sum_v('.$row[3].','.$row[4].',$(this).val(),'.$row[0].')" style="display:none;" class="col-sm-12 campoD text-left form-control" type="text" name="txtIva'.$row[0].'" id="txtIva'.$row[0].'" value="'.$row[5].'" />';
                                        $sumaIva += $row[5];
                                        ?>
                                    </td>
                                    <td class="campos text-right">
                                        <?php echo '<label class="valorLabel" style="font-weight:normal" id="lblImpoconsumo'.$row[0].'">'.number_format($row[6], 2, '.', ',').'</label>';
                                        echo '<input maxlength="50" onkeypress="return justNumbers(event)" style="display:none; padding:2px;" class="col-sm-12 campoD text-left form-control" type="text" name="txtImpoconsumo'.$row[0].'" id="txtImpoconsumo'.$row[0].'" value="'.$row[6].'" />';
                                        $sumaImpo += $row[6];
                                        ?>
                                    </td>
                                    <td class="campos text-right">
                                        <?php echo '<label class="valorLabel" style="font-weight:normal" id="lblAjustepeso'.$row[0].'">'.number_format($row[7], 2, '.', ',').'</label>';
                                        echo '<input maxlength="50" onkeypress="return justNumbers(event)" style="display:none;" class="col-sm-12 campoD text-left form-control" type="text" name="txtAjustepeso'.$row[0].'" id="txtAjustepeso'.$row[0].'" value="'.$row[7].'" />';
                                        $sumaAjuste += $row[7];
                                        ?>
                                    </td>
                                    <td class="campos text-right">
                                        <?php echo '<label class="valorLabel" style="font-weight:normal" id="lblValorAjuste'.$row[0].'">'.number_format($row[9], 2, '.', ',').'</label>';
                                        echo '<input maxlength="50" onkeypress="return justNumbers(event)" style="display:none; width:100.5px;" class="col-sm-9 campoD text-left form-control"  type="text" name="txtValorAjuste'.$row[0].'" id="txtValorAjuste'.$row[0].'" value="'.$row[9].'" readonly ="true"/>';
                                        $sumaValortotal += $row[9];
                                        ?>
                                        <div >
                                            <table id="tab<?php echo $row[0] ?>" style="padding: 0px; background : transparent;" class="col-sm-1">
                                                <tbody>
                                                <tr style="background-color: transparent;">
                                                    <td style="background-color: transparent;">
                                                        <a  href="#<?php echo md5($row[0]);?>" title="Guardar" id="guardar<?php echo $row[0]; ?>" style="display: none;" onclick="javascript:guardarCambios(<?php echo $id_dd; ?>)">
                                                            <li class="glyphicon glyphicon-floppy-disk"></li>
                                                        </a>
                                                    </td>
                                                    <td style="background-color: transparent;">
                                                        <a href="#<?php echo md5($row[0]);?>" title="Cancelar" id="cancelar<?php echo $row[0] ?>" style="display: none;" onclick="javascript:cancelar(<?php echo $row[0];?>)" >
                                                            <i title="Cancelar" class="glyphicon glyphicon-remove" ></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                                <?php
                            }
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
                <!-- Fin de forma de tabla -->
            </div>
            <!-- Inicio de totales -->
            <div class="col-sm-8 col-md-8 col-lg-8 col-sm-offset-1 col-md-offset-1 col-lg-offset-1" style="margin-top : 5px;">
                <div class="col-sm-1" style="margin-right : 30px;">
                    <div class="form-group" style="" align="left">
                        <label class="control-label">
                            <strong>Totales:</strong>
                        </label>
                    </div>
                </div>
                <div class="col-sm-1" style="margin-right : 20px;">
                    <label class="control-label valorLabel" title="Total cantidad"><?php echo number_format($sumaCantidad, 2, '.', ','); ?></label>
                </div>
                <div class="col-sm-1" style="margin-right : 30px;">
                    <label class="control-label valorLabel" title="Total valor"><?php echo number_format($sumaValor, 2, '.', ','); ?></label>
                </div>
                <div class="col-sm-1" style="margin-right : 50px;">
                    <label class="control-label valorLabel" title="Total iva"><?php echo number_format($sumaIva, 2, '.', ','); ?></label>
                </div>
                <div class="col-sm-1" style="margin-right : 30px;">
                    <label class="control-label valorLabel" title="Total impuesto al consumo"><?php echo number_format($sumaImpo, 2, '.', ','); ?></label>
                </div>
                <div class="col-sm-1" style="margin-right : 30px;">
                    <label class="control-label valorLabel" title="Total ajsute al peso"><?php echo number_format($sumaAjuste, 2, '.', ','); ?></label>
                </div>
                <div class="col-sm-1" style="margin-right : 30px;">
                    <label class="control-label valorLabel" title="Total valor ajustado"><?php echo number_format($sumaValortotal, 2, '.', ','); ?></label>
                </div>
                <!-- Fin de totales -->
            </div>
        </div>
        <!-- Inicio de scripts -->
        <script type="text/javascript">
            //funcion para ihnabilitar el campo
            function inhabilitar(id){
                $.post("access.php?controller=DetalleFactura&action=inhabilitar", { cnt: <?php echo !empty($idCnt)?$idCnt:0 ?>, ptal: <?php echo !empty($idPptal)?$idPptal:0 ?> }, function(data){
                    if(data > 0){
                        $("#btnDel"+id).prop('disabled', true);
                    }else{
                        $("#btnDel"+id).prop('disabled',false);
                    }
                });
            }

            //Función para eliminar
            function eliminar(id, mov){
                var result = '';
                var form_data = { action : 6, id_unico : id };
                $("#myModal").modal('show');
                $("#ver").click(function(){
                    $("#mymodal").modal('hide');
                    $.ajax({
                        type:"POST",
                        data:form_data,
                        url: "jsonPptal/gf_facturaJson.php",
                        success: function (data) {
                            result = JSON.parse(data);
                            if(result == 1) {
                                var form_data = { action : 'Eliminar', id_unico : id, mov : mov };
                                $.ajax({
                                    type:"POST",
                                    data:form_data,
                                    url: "access.php?controller=DetalleFactura&action=Eliminar",
                                    success: function (data) {
                                        result = JSON.parse(data);
                                        if(result==true)
                                            $("#mdlEliminado").modal('show');
                                        else
                                            $("#mdlNoeliminado").modal('show');
                                    }
                                });
                            } else {
                                $("#mdlNoeliminado").modal('show');
                            }
                        }
                    });

                });
            }
            //Función para guardar datos del detalle
            function guardarCambios(id, mov){
                var sltConcepto    = 'sltconcepto'+id;
                var txtCantidad    = 'txtcantidad'+id;
                var txtValor       = 'txtValor'+id;
                var txtIva         = 'txtIva'+id;
                var txtImpoconsumo = 'txtImpoconsumo'+id;
                var txtAjustepeso  = 'txtAjustepeso'+id;
                var txtValorAjuste = 'txtValorAjuste'+id
                var form_data = {
                    id       : id,
                    concepto : $("#"+sltConcepto).val(),
                    cantidad : $("#"+txtCantidad).val(),
                    valor    : $("#"+txtValor).val(),
                    iva      : $("#"+txtIva).val(),
                    impoconsumo : $("#"+txtImpoconsumo).val(),
                    ajustepeso  : $("#"+txtAjustepeso).val(),
                    valorAjuste : $("#"+txtValorAjuste).val(),
                    mov         : mov
                };
                var result = '';
                $.ajax({
                    type: 'POST',
                    url: "access.php?controller=DetalleFactura&action=Modificar",
                    data:form_data,
                    success: function (data) {
                        result = JSON.parse(data);
                        if(result == true){
                            $("#mdlModificado").modal('show');
                        }else{
                            $("#mdlNomodificado").modal('show');
                        };
                    }
                });
            }

            //función para ocultar los label y mostrar los campos para modificar
            function modificar(id){
                //En el que valida si el campos idPrevio tiene un valor
                //en el que asignamos los nombres de los labels y campos
                //y el asignamos la idPrevio y a su vez solo mostramos los labels
                if( ( $("#idPrevio").val() !== 0 ) || ( $("#idPrevio").val() !== "" ) ){
                    var lblConceptoC    = 'concepto'+$("#idPrevio").val();
                    var sltConceptoC    = 'sltconcepto'+$("#idPrevio").val();
                    var lblCantidadC    = 'lblCantidad'+$("#idPrevio").val();
                    var txtCantidadC    = 'txtcantidad'+$("#idPrevio").val();
                    var lblValorC       = 'lblValor'+$("#idPrevio").val();
                    var txtValorC       = 'txtValor'+$("#idPrevio").val();
                    var lblIvaC         = 'lblIva'+$("#idPrevio").val();
                    var txtIvaC         = 'txtIva'+$("#idPrevio").val();
                    var lblImpoconsumoC = 'lblImpoconsumo'+$("#idPrevio").val();
                    var txtImpoconsumoC = 'txtImpoconsumo'+$("#idPrevio").val();
                    var lblAjustepesoC  = 'lblAjustepeso'+$("#idPrevio").val();
                    var txtAjustepesoC  = 'txtAjustepeso'+$("#idPrevio").val();
                    var guardarC        = 'guardar'+$("#idPrevio").val();
                    var cancelarC       = 'cancelar'+$("#idPrevio").val();
                    var tablaC          = 'tab'+$("#idPrevio").val();
                    var lblValorAjusteC = 'lblValorAjuste'+$("#idPrevio").val();
                    var txtValorAjusteC = 'txtValorAjuste'+$("#idPrevio").val();

                    $("#"+lblConceptoC).css('display','block');
                    $("#"+sltConceptoC).css('display','none');
                    $("#"+lblCantidadC).css('display','block');
                    $("#"+txtCantidadC).css('display','none');
                    $("#"+lblValorC).css('display','block');
                    $("#"+txtValorC).css('display','none');
                    $("#"+lblIvaC).css('display','block');
                    $("#"+txtIvaC).css('display','none');
                    $("#"+lblImpoconsumoC).css('display','block');
                    $("#"+txtImpoconsumoC).css('display','none');
                    $("#"+lblAjustepesoC).css('display','block');
                    $("#"+txtAjustepesoC).css('display','none');
                    $("#"+guardarC).css('display','none');
                    $("#"+cancelarC).css('display','none');
                    $("#"+tablaC).css('display','none');
                    $("#"+lblValorAjusteC).css('display','block');
                    $("#"+txtValorAjusteC).css('display','none');
                }
                //Aqui creamos las variables similares a las anteriores en la que asignamos el nombre y el id
                var lblConcepto    = 'concepto'+id;
                var sltConcepto    = 'sltconcepto'+id;
                var lblCantidad    = 'lblCantidad'+id;
                var txtCantidad    = 'txtcantidad'+id;
                var lblValor       = 'lblValor'+id;
                var txtValor       = 'txtValor'+id;
                var lblIva         = 'lblIva'+id;
                var txtIva         = 'txtIva'+id;
                var lblImpoconsumo = 'lblImpoconsumo'+id;
                var txtImpoconsumo = 'txtImpoconsumo'+id;
                var lblAjustepeso  = 'lblAjustepeso'+id;
                var txtAjustepeso  = 'txtAjustepeso'+id;
                var lblValorAjuste = 'lblValorAjuste'+id;
                var txtValorAjuste = 'txtValorAjuste'+id;
                var guardar        = 'guardar'+id;
                var cancelar       = 'cancelar'+id;
                var tabla          = 'tab'+id;
                //ocultamos los labels y mostramos los campos ocultos
                $("#"+sltConcepto).css('display','block');
                $("#"+lblConcepto).css('display','none');
                $("#"+txtCantidad).css('display','block');
                $("#"+lblCantidad).css('display','none');
                $("#"+txtValor).css('display','block');
                $("#"+lblValor).css('display','none');
                $("#"+txtIva).css('display','block');
                $("#"+lblIva).css('display','none');
                $("#"+txtImpoconsumo).css('display','block');
                $("#"+lblImpoconsumo).css('display','none');
                $("#"+lblAjustepeso).css('display','none');
                $("#"+txtAjustepeso).css('display','block');
                $("#"+lblValorAjuste).css('display','none');
                $("#"+txtValorAjuste).css('display','block')
                $("#"+guardar).css('display','block');
                $("#"+cancelar).css('display','block');
                $("#"+tabla).css('display','block');
                //Asignamos el valor de la id al campo id actual
                $("#idActual").val(id);
                //Y preguntamos si el valor del idPrevio es diferente a la id
                //y se la asignamos
                if($("#idPrevio").val() != id){
                    $("#idPrevio").val(id);
                }
            }

            function cambioValor(id){
                $("#sltconcepto"+id).change(function() {
                    var form_data = { concepto : $("#sltconcepto"+id).val(), proceso : 1 };
                    $.ajax({
                        type: 'POST',
                        url: "consultasFacturacion/consultarValor.php",
                        data:form_data,
                        success: function (data) {
                            if(data !== ""){
                                $("#txtValor"+id).html(data).fadeIn();
                            }
                        }
                    });
                });
            }

            function calcularValores(id) {
                var ajuste    = <?php echo $ajuste[0]; ?>;
                var Impo      = 0.00;
                var iva       = 0.00;
                var valor     = 0;
                var totalIva  = 0;
                var totalImpo = 0;

                $("#txtValor"+id).change(function(){
                    //Validación para el campo de valor no tome valores cero
                    if($("#txtValor"+id).val() !== '0'){
                        var form_data = { concepto : $("#sltconcepto"+id).val(), proceso : 2 };
                        $.ajax({
                            type: 'POST',
                            url: "consultasFacturacion/consultarValor.php",
                            data:form_data,
                            success: function (data) {
                                var cantidad = $("#txtcantidad"+id).val();
                                if(cantidad==0 || cantidad==''){ cantidad = 1; }else{ cantidad = $("#txtcantidad"+id).val(); }
                                iva      = data;
                                valor    = $("#txtValor"+id).val() * cantidad;
                                totalIva = (iva*valor)/100;
                                if (isNaN(totalIva)) { totalIva = 0; }
                                $("#txtIva"+id).val(totalIva);
                            }
                        });
                    }else{
                        var can = 0;
                        //Validación para campo cantidad
                        if(isNaN($("#txtcantidad"+id).val())){ can = 1; }else{ can = ($("#txtcantidad"+id).val()); }
                        //Vaciamos los campos
                        $("#txtIva"+id).val('0');
                        $("#txtImpoconsumo"+id).val('0');
                        $("#txtAjustepeso"+id).val('0');
                        $("#txtValorAjuste"+id).val('0');
                        //Cambio de campo
                        $("#txtValor"+id).replaceWith('<input type="text" id="txtValor'+id+'" name="txtValor'+id+'" class="form-control campoD" placeholder="Valor" title="Ingrese el valor" onkeypress="return justNumbers(event)"/>');
                        $("#txtValor"+id).focus();
                        //Función de cambio por campo valor
                        $("#txtValor"+id).blur(function(){
                            //Validación de valor de campo valor
                            if($("#txtValor"+id).val() !== 0 ){
                                //Operaciones de valor
                                var valor = $("#txtValor"+id).val() * can;
                                if (isNaN(valor)) { valor = 0; }
                                //Asiganción de valores para el campo de total
                                $("#txtValorAjuste"+id).val(valor);
                            }
                        });
                        //Función de cambio para campo iva
                        $("#txtIva"+id).blur(function(){
                            //Validación de campo iva
                            if($("#txtIva"+id).val() !== 0){
                                //Captura de valores
                                var valor  = $("#txtValor"+id).val()*can;
                                var iva    = $("#txtIva"+id).val();
                                //Operación de iva
                                var totalI = (valor * iva) /100;
                                //Asiganción de valor de iva
                                if (isNaN(totalI)) { totalI = 0; }
                                $("#txtIva"+id).val(totalI);
                                //Asiganción de valores para el campo de total
                                $("#txtValorAjuste"+id).val(valor+totalI);
                            }
                        });
                        //Función de campo para campo impoconsumo
                        $("#txtImpoconsumo"+id).blur(function(){
                            //Validación de campo impoconsumo
                            if($("#txtImpoconsumo"+id).val() !== 0){
                                //Captura de valores
                                var valor  = $("#txtValor"+id).val()*can;
                                var impo   = $("#txtImpoconsumo"+id).val();
                                var iva    = $("#txtIva"+id).val();
                                //Operación de impoconsumo
                                var totalM = (valor*impo) /100;
                                if (isNaN(totalM)) { totalM = 0; }
                                var t      = parseFloat(valor) + parseFloat(iva) + parseFloat(totalM);
                                //ASiganción de valor de impo
                                $("#txtImpoconsumo"+id).val(totalM);
                                //Asiganción de valores para el campo de total
                                $("#txtValorAjuste"+id).val(t);
                            }
                        });
                        //Función para ajuste al peso
                        $("#txtAjustepeso"+id).blur(function(){
                            //Valiación para ajuste al peso
                            if($("#txtAjustepeso"+id).val() !== 0){
                                //Captura de valores
                                var valor  = $("#txtValor"+id).val()*can;
                                var impo   = $("#txtImpoconsumo"+id).val();
                                var iva    = $("#txtIva"+id).val();
                                var ajuste = $("#txtAjustepeso"+id).val();
                                //operaciones
                                var suma     = parseFloat(valor) + parseFloat(impo) + parseFloat(iva);
                                var redondeo = redondeaAlAlza(suma,ajuste);
                                var aj       = redondeoTotal(suma,ajuste);
                                //Asiganción de valores
                                if (isNaN(redondeo)) { redondeo = 0; }
                                $("#txtAjustepeso"+id).val(redondeo);
                                if (isNaN(aj)) { aj = 0; }
                                $("#txtValorAjuste"+id).val(aj);

                            }
                        });
                    }
                });

                $("#txtValor"+id).change(function(){
                    //Validación para el campo de valor no tome valores cero
                    if($("#txtValor"+id).val() !== '0'){
                        var form_data = { concepto : $("#sltconcepto"+id).val(), proceso : 3 };
                        $.ajax({
                            type: 'POST',
                            url: "consultasFacturacion/consultarValor.php",
                            data:form_data,
                            success: function (data) {
                                Impo  = data;
                                valor = $("#txtValor"+id).val();
                                var cantidad = $("#txtcantidad"+id).val();
                                if( cantidad == 0 || cantidad == '' ){
                                    cantidad = 1;
                                }else{
                                    cantidad = $("#txtcantidad"+id).val();
                                }
                                var oper  = (valor * cantidad);
                                totalImpo = (Impo*oper)/100;
                                var suma    = parseFloat(oper) + parseFloat(totalIva) + parseFloat(totalImpo);
                                var redondo = redondeaAlAlza(suma,ajuste) ;
                                var ajusteT = redondeoTotal(suma,ajuste);
                                if (isNaN(totalImpo)) { totalImpo = 0; }
                                if (isNaN(redondo)) { redondo = 0; }
                                if (isNaN(ajusteT)) { ajusteT = 0; }
                                $("#txtImpoconsumo"+id).val(totalImpo);
                                $("#txtAjustepeso"+id).val(redondo);
                                $("#txtValorAjuste"+id).val(ajusteT);
                            }
                        });
                    }

                });
            }

            function calcularValoresEscrito(id) {
                var ajuste    = <?php echo $ajuste[0]; ?>;
                var Impo      = 0.00;
                var iva       = 0.00;
                var valor     = 0;
                var totalIva  = 0;
                var totalImpo = 0;
                $("#txtcantidad"+id).keyup(function(){
                    var form_data = { concepto : $("#sltconcepto"+id).val(), proceso : 2 };
                    $.ajax({
                        type: 'POST',
                        url: "consultasFacturacion/consultarValor.php",
                        data:form_data,
                        success: function (data) {
                            iva      = data;
                            valor    = $("#txtValor"+id).val();
                            totalIva = (iva*valor)/100;
                            if (isNaN(totalIva)){ totalIva = 0; }
                            $("#txtIva"+id).val(totalIva);
                        }
                    });

                    var form_data = { concepto : $("#sltconcepto"+id).val(), proceso : 3 };
                    $.ajax({
                        type: 'POST',
                        url: "consultasFacturacion/consultarValor.php",
                        data:form_data,
                        success: function (data) {
                            Impo      = data;
                            valor     = $("#txtValor"+id).val();
                            totalImpo = (Impo*valor)/100;
                            if (isNaN(totalImpo)){ totalImpo  = 0; }
                            $("#txtImpoconsumo"+id).val(totalImpo);
                            var cantidad = $("#txtcantidad"+id).val();
                            if( cantidad == 0 || cantidad == '' ){
                                cantidad = 1;
                            }else{
                                cantidad = $("#txtcantidad"+id).val();
                            }

                            var oper    = (valor * cantidad);
                            var suma    = oper + totalIva + totalImpo;
                            var redondo = redondeaAlAlza(suma,ajuste);
                            var ajusteT = redondeoTotal(suma,ajuste);
                            $("#txtAjustepeso"+id).val(redondo);
                            $("#txtValorAjuste"+id).val(ajusteT);
                        }
                    });
                });
            }

            function cancelar(id){
                //Creamos las variables en la que cargamos los nombres de los campos y label y le concatenamos la id
                var lblConcepto = 'concepto'+id;
                var sltConcepto = 'sltconcepto'+id;
                var lblCantidad = 'lblCantidad'+id;
                var txtCantidad = 'txtcantidad'+id;
                var lblValor    = 'lblValor'+id;
                var txtValor    = 'txtValor'+id;
                var lblIva      = 'lblIva'+id;
                var txtIva         = 'txtIva'+id;
                var lblImpoconsumo = 'lblImpoconsumo'+id;
                var txtImpoconsumo = 'txtImpoconsumo'+id;
                var lblAjustepeso  = 'lblAjustepeso'+id;
                var txtAjustepeso  = 'txtAjustepeso'+id;
                var lblValorAjuste = 'lblValorAjuste'+id;
                var txtValorAjuste = 'txtValorAjuste'+id;
                var guardar        = 'guardar'+id;
                var cancelar       = 'cancelar'+id;
                var tabla          = 'tab'+id;
                //ocultamos los campos y mostramos los labels
                $("#"+lblConcepto).css('display','block');
                $("#"+sltConcepto).css('display','none');
                $("#"+lblCantidad).css('display','block');
                $("#"+txtCantidad).css('display','none');
                $("#"+lblValor).css('display','block');
                $("#"+txtValor).css('display','none');
                $("#"+lblIva).css('display','block');
                $("#"+txtIva).css('display','none');
                $("#"+lblImpoconsumo).css('display','block');
                $("#"+txtImpoconsumo).css('display','none');
                $("#"+lblAjustepeso).css('display','block');
                $("#"+txtAjustepeso).css('display','none');
                $("#"+lblValorAjuste).css('display','block');
                $("#"+txtValorAjuste).css('display','none');
                $("#"+guardar).css('display','none');
                $("#"+cancelar).css('display','none');
                $("#"+tabla).css('display','none');
            }

            function modificarPago(id_cnt, id_pptal, mov){
                var id          = $("#id").val();
                var fecha       = $("#fecha").val();
                var tercero     = $("#sltTercero").val();
                var centroCosto = $("#sltCentroCosto").val();
                var fechavence  = $("#fechaV").val();
                var descripcion = $("#txtDescripcion").val();

                var form_data = {
                    id               : id,
                    fecha            : fecha,
                    tercero          : tercero,
                    centrocosto      : centroCosto,
                    fechaVencimiento : fechavence,
                    descripcion      : descripcion,
                    id_cnt           : id_cnt,
                    id_pptal         : id_pptal,
                    mov              : mov
                };

                var result='';
                $.ajax({
                    type: 'POST',
                    url: "access.php?controller=Factura&action=Modificar",
                    data:form_data,
                    success: function (data) {
                        result = JSON.parse(data);
                        if (result==true) {
                            $("#mdlModificado").modal('show');
                        }else{
                            $("#mdlNomodificado").modal('show');
                        }
                    }
                });
            }

            function cargarValor(id){
                $("#sltconcepto"+id).append(function(){

                    var form_data = { is_ajax:1, data:+id };

                    $.ajax({
                        type: 'POST',
                        url: "consultasFacturacion/consultarValorT.php",
                        data:form_data,
                        success: function (data) {
                            $("#txtValor"+id).html(data).fadeIn();
                        }
                    });
                });
            }

            function limpiarCampos(){
                $('#sltConcepto').prop('selectedIndex',0);
                $("#sltRubroFuente").prop('selectedIndex',0);
                $("#txtCantidad").val('');
                $("#sltValor").prop('selectedIndex',0);
                $("#txtIva").val('');
                $("#txtImpoconsumo").val('');
                $("#txtAjustePeso").val('');
                $("#txtValorA").val('');
            }

            $("#sltBuscar").change(function () {
                //Captura de variables
                var factura = $("#sltBuscar").val();
                //Array de envio
                var form_data = { factura : factura };
                //Envio ajax
                $.ajax({
                    type:'POST',
                    url:'access.php?controller=Factura&action=buscarCot',
                    data:{ factura: factura },
                    success: function(data){
                        window.location = data;
                    }
                });
            });

            //Eliminar Datos de comprobante, detalle, pptal, detalle, factura
            function eliminarDatos(factura, cnt,pptal, mov){
                if(factura !== 0){
                    //Validamos que la factura cnt y pptal no esten vacias
                    $("#modalEliminarFactura").modal('show');
                    $("#btnEC").click(function(){
                        if( cnt !== 0 && pptal !== 0 ) {
                            //Variable de envio ajax
                            var form_data = { existente : 50, factura : factura, pptal : pptal, cnt : cnt, mov : mov };
                            var result    = '';
                            //Envio ajax
                            $.ajax({
                                type:'POST',
                                url: 'access.php?controller=DetalleFactura&action=EliminarTodos',
                                data: form_data,
                                success : function(data) {
                                    result = JSON.parse(data);
                                    if(result == true) {
                                        $("#mdlEliminado").modal('show');
                                        $("#ver1").click(function(){
                                            window.location.reload();
                                        });
                                    } else{
                                        $("#mdlNoeliminado").modal('show');
                                    }
                                }
                            }).error(function(data,textError) {
                                console.log('Data :'+data+', Error:'+textError);
                            });
                        }
                    });
                }
            }

            sumaFecha = function(d, fecha){
                var Fecha  = new Date();
                var sFecha = fecha || (Fecha.getDate() + "/" + (Fecha.getMonth() +1) + "/" + Fecha.getFullYear());
                var sep    = sFecha.indexOf('/') != -1 ? '/' : '-';
                var aFecha = sFecha.split(sep);
                var fecha  = aFecha[2]+'/'+aFecha[1]+'/'+aFecha[0];
                fecha      = new Date(fecha);
                fecha.setDate(fecha.getDate()+parseInt(d));
                var anno   = fecha.getFullYear();
                var mes    = fecha.getMonth()+1;
                var dia    = fecha.getDate();
                mes        = (mes < 10) ? ("0" + mes) : mes;
                dia        = (dia < 10) ? ("0" + dia) : dia;
                var fechaFinal = dia+sep+mes+sep+anno;
                return (fechaFinal);
            }

            function sum_v(cantidad, valor, iva, x){
                var cantidad = parseFloat(cantidad);
                var valor    = parseFloat(valor);
                var iva      = parseFloat(iva);
                var oper     = (cantidad * valor);
                $("#txtValorAjuste"+x).val(oper+iva);
            }
        </script>
    </div>
</body>
<?php require_once 'footer.php' ?>
<script type="text/javascript" src="js/select2.js"></script>
<script type="text/javascript">
    $(".select2").select2();
    $("#sltTercero").select2({placeholder:"Tercero",allowClear: true});
    //$("#sltCentroCosto").select2({placeholder:"Centro Costo"});
    $("#sltConcepto").select2({placeholder:"Concepto",allowClear: true});
    $("#sltBuscar").select2({placeholder:"Buscar Factura",allowClear: true});
    $("#sltTipoFactura").select2({placeholder:"Tipo Factura",allowClear: true});
    $("#sltCentroCosto").select2({placeholder:"Centro Costo",allowClear: true});
    $("#sltTipoBuscar").select2({placeholder:"Tipo Factura",allowClear: true});
    $("#sltRubros").select2({placeholder:"Rubros",allowClear: true});
    $("#sltFuentes").select2({placeholder:"Rubros",allowClear: true});
    $("#sltBanco").select2({placeholder:"Banco",allowClear: true});
</script>
<script src="js/bootstrap.min.js"></script>
<div class="modal fade" id="mdlModificado" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px;">
                <p>Información modificada correctamente.</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="btnModifico" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="mdlNomodificado" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px;">
                <p>No se ha podido modificar la información.</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="btnNoModifico" class="btn" style="color: #000; margin-top: 2px;" data-dismiss="modal">Aceptar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="mdltipofactura" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px;">
                <p>Seleccione un tipo de factura.</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="tbmtipoF" class="btn" style="color: #000; margin-top: 2px;" data-dismiss="modal" >Aceptar</button>
            </div>
        </div>
    </div>
</div>
<!-- Fin de modal para validación de tipo -->
<!-- Inicio de modal de validación de fecha -->
<div class="modal fade" id="mdlfecha" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px;">
                <p id="mensaje_fecha"></p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="btnNoModifico" class="btn" style="color: #000; margin-top: 2px;" data-dismiss="modal">Aceptar</button>
            </div>
        </div>
    </div>
</div>
<!-- Fin de modal de validación de fecha -->
<!-- Inicio de modales para eliminado -->
<div class="modal fade" id="myModal" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Confirmar</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px;">
                <p>¿Desea eliminar el registro seleccionado de Factura?</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="ver" class="btn" style="color: #000; margin-top: 2px;" data-dismiss="modal" >Aceptar</button>
                <button type="button" class="btn" style="color: #000; margin-top: 2px;" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="mdlEliminado" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px;">
                <p>Información eliminada correctamente.</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px;" data-dismiss="modal" >Aceptar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="mdlNoeliminado" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px;">
                <p>No se pudo eliminar la información, el registro seleccionado está siendo utilizado por otra dependencia.</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="ver2" class="btn btn-default" data-dismiss="modal" >Aceptar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="mdltipoFactura" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px;">
                <p>Seleccione un tipo de factura.</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="btnTipoF" class="btn" style="color: #000; margin-top: 2px;" data-dismiss="modal" >Aceptar</button>
            </div>
        </div>
    </div>
</div>
<!-- Fin de modales para eliminado -->
<!-- Inicio modal de validación de fecha vencimiento -->
<div class="modal fade" id="modalValFechaV" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px;">
                <p>La fecha debe no puede ser menor a la fecha de la factura.</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="btnTipoF" class="btn" style="color: #000; margin-top: 2px;" data-dismiss="modal" >Aceptar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalEliminarFactura" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Confirmar</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px;">
                <p>¿Desea eliminar la Factura seleccionada?</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="btnEC" class="btn" style="color: #000; margin-top: 2px;" data-dismiss="modal" >Aceptar</button>
                <button type="button" class="btn" style="color: #000; margin-top: 2px;" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="mdlConstruir" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px;">
                <p id="mensaje_c"></p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="btnCons" class="btn" style="color: #000; margin-top: 2px;" data-dismiss="modal" onclick="reload()">Aceptar</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    <?php
    if($tipo_co == 1){
        echo "\n$(\".privada\").show('fast');";
        echo "\n$(\".cambio\").removeClass('col-sm-2 col-md-2 col-lg-2')";
        echo "\n$(\".cambio\").addClass('col-sm-1 col-md-1 col-lg-1')";
        echo "\n$(\"#sltVendedor\").attr('required')";
    }else{
        echo "\n$(\"#sltVendedor\").removeAttr('required')";
        echo "\n$(\".privada\").hide('fast');";
    }
    ?>

    $("#sltTipoFactura").change(function(e){
        var tipo = $("#sltTipoFactura").val();
        if( tipo.length > 0 ){
            var form_data = { tipo : $("#sltTipoFactura").val(), action : 1 };
            $.ajax({
                type: 'POST',
                url: "jsonPptal/gf_facturaJson.php",
                data: form_data,
                success: function (data) {
                    $("#txtNumeroF").val(data);
                }
            });
        }else{
            $("#txtNumeroF").val("");
            $(".herencia").fadeOut("fast");
        }
    });

    $('#btnModifico').click(function(){
        document.location.reload();
    });

    $('#btnNoModifico').click(function(){
        document.location.reload();
    });

    $('#ver1').click(function(){
        document.location.reload();
    });

    $('#ver2').click(function(){
        document.location.reload();
    });

    $('#btnG').click(function(){
        document.location.reload();
    });

    $('#btnG2').click(function(){
        document.location.reload();
    });

    //Función para cargar modal de comprobante contable
    function cargarComprobante(idCnt){
        //Vector de envio con mi variable
        var form_data = { idC : idCnt };
        $.ajax({
            type: 'POST',
            url: "modalConsultaComprobanteC.php",
            data: form_data,
            success: function (data) {
                $("#modalComprobanteC").html(data);
                $(".comprobantec").modal('show');
            }
        });
    }
    //Función para cargar modal del comprobante presupuestal
    function cargarPresupuestal(idPptal){
        //Vector de envio con mi variable
        var form_data={
            idP:idPptal
        };
        $.ajax({
            type: 'POST',
            url: "modalConsultaComprobanteP.php",
            data: form_data,
            success: function (data) {
                $("#modalComprobanteP").html(data);
                $(".comprobantep").modal('show');
            }
        });
    }
    //Funcion solo numeros
    function justNumbers(e){
        var keynum = window.event ? window.event.keyCode : e.which;
        if ((keynum == 8) || (keynum == 46) || (keynum == 45))
            return true;
        return /\d/.test(String.fromCharCode(keynum));
    }
    //Función informe
    function informe(){
        window.open('informes/inf_com_fac.php?factura=<?php if(!empty($factura)){echo $factura;}else{echo " ";} ?>');
    }
    //Función para validar fecha
    function validarFecha(){
        //Capturamos la variable del tipo factura
        var tipoF = parseInt($("#sltTipoFactura").val());
        //Validamos que no este vacia
        if(!isNaN(tipoF) || tipoF.length > 0){
            //Preparamos la variable de envio con los valores
            var form_data = { x : 2, fecha : $("#fecha").val(), tipo : tipoF,  id_factura:<?php echo $idFactura == ''? 0 : $idFactura; ?> };
            //Variable de conversion
            var result = '';
            //Envio de ajax
            $.ajax({
                type:'POST',
                url:'consultasBasicas/consultas_factura.php',
                data:form_data,
                success: function(data){
                    //Capturamos el data y lo convertimos a json
                    result = data;
                    //Validamos si el valor es true
                    if(result == true) {
                        $("#mensaje_fecha").html("<p>La fecha es mayor a la anterior factura</p>");
                        $("#mdlfecha").modal('show');   //Muestra modal
                        $("#fecha").val('');            //Campo fecha es vacia
                        $("#fechaV").val('');           //Campo fecha es vacia
                    }else if(result == 5){
                        $("#mensaje_fecha").html("<p>La fecha es menor a la ultima factura</p>");
                        $("#mdlfecha").modal('show');   //Muestra modal
                        $("#fecha").val('');            //Campo fecha es vacia
                        $("#fechaV").val('');           //Campo fecha es vacia
                    }
                }
            });
        }
    }
    //Funcion para sumar 30 dias al cambio de fecha
    function change_date(){
        var fecha = $("#fecha").val();
        var fechaV = sumaFecha(30,fecha);
        $("#fechaV").val(fechaV);
    }
    //Función para validar que la fecha de vencimiento no sea menor a la de fecha
    function diferents_date(){
        var fecha1 = $("#fecha").val();         //Fecha
        var fecha2 = $("#fechaV").val();        //Fecha de vencimiento
        //Dividimos las fechas
        var inicial = fecha1.split("/");        //Fecha
        var final   =  fecha2.split("/");       //Fecha de vencimiento
        //creamos variables de fecha y la formateamos para año-mes-dia
        var dateStart = new Date(inicial[2],inicial[1],inicial[0]); //Fecha
        var dateEnd   = new Date(final[2],final[1],final[0]);       //Fecha de vencimiento
        //Validamos que la fecha de vencimiento no sea mayor que la del campo fecha
        if(dateEnd < dateStart){
            $("#mensaje_fecha").html("<p id=\"mensaje_fecha\">La fecha es menor</p>");
            $("#mdlfecha").modal('show');
            var fv = sumaFecha(30,fecha1);
            $("#fechaV").val(fv);
        }
    }

    $("#sltTipoFactura").change(function(){
        $("#fecha").val("");
        $("#fechaV").val("");
    });

    $("#sltTercero").change(function() {
        var form_data  = { tercero : $("#sltTercero").val() };
        $.ajax({
            type: 'POST',
            url: "consultasFacturacion/consultarFechav.php",
            data:form_data,
            success: function (data) {
                if( data !== 0 ){
                    var fechaV = data;
                    var fecha  = sumaFecha(fechaV,$("#fechaV").val());
                    $("#fechaV").val(fecha);
                }
            }
        });
    });
</script>
<!-- Inivocamos en la parte inferior el archivo que contendra el modal para evitar posibles errores -->
<?php require_once './modalConsultaComprobanteC.php'; ?>
<script type="text/javascript">
    //Función para ajustar la cabezera de la tabla
    $("#modalComprobanteC").on('shown.bs.modal',function(){
        try{
            var dataTable = $("#tablaDetalleC").DataTable();
            dataTable.columns.adjust().responsive.recalc();
        }catch(err){}
    });
</script>
<?php require_once './modalConsultaComprobanteP.php'; ?>
<script src="dist/jquery.validate.js"></script>
<script type="text/javascript">
    $("#modalComprobanteP").on('shown.bs.modal',function(){
        try{
            var dataTable = $("#tablaDetalleP").DataTable();
            dataTable.columns.adjust().responsive.recalc();
        }catch(err){}
    });

    $().ready(function() {
        var validator = $("#form-detalle").validate({
            ignore: "",
            rules:{
                sltTipoPredio:"required",
                txtCodigo:"required"
            },
            messages:{
                sltTipoPredio: "Seleccione tipo de predio",
            },
            errorElement:"em",
            errorPlacement: function(error){
                error.addClass('help-block');
            },
            highlight: function(element){
                var elem = $(element);
                if(elem.hasClass('select2-offscreen')){
                    $("#s2id_"+elem.attr("id")).addClass('has-error').removeClass('has-success');
                }else{
                    $(elem).parents(".col-lg-2").addClass("has-error").removeClass('has-success');
                    $(elem).parents(".col-md-2").addClass("has-error").removeClass('has-success');
                    $(elem).parents(".col-sm-2").addClass("has-error").removeClass('has-success');
                    $(elem).parents(".col-lg-1").addClass("has-error").removeClass('has-success');
                    $(elem).parents(".col-md-1").addClass("has-error").removeClass('has-success');
                    $(elem).parents(".col-sm-1").addClass("has-error").removeClass('has-success');
                }
                if($(element).attr('type') == 'radio'){
                    $(element.form).find("input[type=radio]").each(function(which){
                        $(element.form).find("label[for=" + this.id + "]").addClass("has-error");
                        $(this).addClass("has-error");
                    });
                } else {
                    $(element.form).find("label[for=" + element.id + "]").addClass("has-error");
                    $(element).addClass("has-error");
                }
            },
            unhighlight:function(element){
                var elem = $(element);
                if(elem.hasClass('select2-offscreen')){
                    $("#s2id_"+elem.attr("id")).addClass('has-success').removeClass('has-error');
                }else{
                    $(element).parents(".col-lg-2").addClass('has-success').removeClass('has-error');
                    $(element).parents(".col-md-2").addClass('has-success').removeClass('has-error');
                    $(element).parents(".col-sm-2").addClass('has-success').removeClass('has-error');
                    $(element).parents(".col-lg-1").addClass('has-success').removeClass('has-error');
                    $(element).parents(".col-md-1").addClass('has-success').removeClass('has-error');
                    $(element).parents(".col-sm-1").addClass('has-success').removeClass('has-error');
                }
                if($(element).attr('type') == 'radio'){
                    $(element.form).find("input[type=radio]").each(function(which){
                        $(element.form).find("label[for=" + this.id + "]").addClass("has-success").removeClass("has-error");
                        $(this).addClass("has-success").removeClass("has-error");
                    });
                } else {
                    $(element.form).find("label[for=" + element.id + "]").addClass("has-success").removeClass("has-error");
                    $(element).addClass("has-success").removeClass("has-error");
                }
            }
        });
        $(".cancel").click(function() {
            validator.resetForm();
        });

        var validator = $("#form").validate({
            ignore: "",
            rules:{
                sltTipoPredio:"required",
                txtCodigo:"required"
            },
            messages:{
                sltTipoPredio: "Seleccione tipo de predio",
            },
            errorElement:"em",
            errorPlacement: function(error){
                error.addClass('help-block');
            },
            highlight: function(element){
                var elem = $(element);
                if(elem.hasClass('select2-offscreen')){
                    $("#s2id_"+elem.attr("id")).addClass('has-error').removeClass('has-success');
                }else{
                    $(element).parents(".col-lg-2").addClass("has-error").removeClass('has-success');
                    $(element).parents(".col-md-2").addClass("has-error").removeClass('has-success');
                    $(element).parents(".col-sm-2").addClass("has-error").removeClass('has-success');
                    $(element).parents(".col-lg-5").addClass("has-error").removeClass('has-success');
                    $(element).parents(".col-md-5").addClass("has-error").removeClass('has-success');
                    $(element).parents(".col-sm-5").addClass("has-error").removeClass('has-success');
                }
                if($(element).attr('type') == 'radio'){
                    $(element.form).find("input[type=radio]").each(function(which){
                        $(element.form).find("label[for=" + this.id + "]").addClass("has-error");
                        $(this).addClass("has-error");
                    });
                } else {
                    $(element.form).find("label[for=" + element.id + "]").addClass("has-error");
                    $(element).addClass("has-error");
                }
            },
            unhighlight:function(element){
                var elem = $(element);
                if(elem.hasClass('select2-offscreen')){
                    $("#s2id_"+elem.attr("id")).addClass('has-success').removeClass('has-error');
                }else{
                    $(element).parents(".col-lg-2").addClass('has-success').removeClass('has-error');
                    $(element).parents(".col-md-2").addClass('has-success').removeClass('has-error');
                    $(element).parents(".col-sm-2").addClass('has-success').removeClass('has-error');
                    $(element).parents(".col-lg-5").addClass('has-success').removeClass('has-error');
                    $(element).parents(".col-md-5").addClass('has-success').removeClass('has-error');
                    $(element).parents(".col-sm-5").addClass('has-success').removeClass('has-error');
                }
                if($(element).attr('type') == 'radio'){
                    $(element.form).find("input[type=radio]").each(function(which){
                        $(element.form).find("label[for=" + this.id + "]").addClass("has-success").removeClass("has-error");
                        $(this).addClass("has-success").removeClass("has-error");
                    });
                } else {
                    $(element.form).find("label[for=" + element.id + "]").addClass("has-success").removeClass("has-error");
                    $(element).addClass("has-success").removeClass("has-error");
                }
            }
        });
        $(".cancel").click(function() {
            validator.resetForm();
        });
    });

    function nuevo() {
        window.location='registrar_GF_FACTURA_COTIZACION.php';
    }

    function reconstruirComprobantes(id_factura, id_cnt, id_pptal){
        if(!isNaN(id_factura) && !isNaN(id_cnt) && !isNaN(id_pptal)){
            var form_data = { id_factura : id_factura, id_cnt : id_cnt, id_pptal : id_pptal };

            $.ajax({
                type:"POST",
                url:"access.php?controller=DetalleFactura&action=ReconstruirComprobantes",
                data:form_data,
                success: function(data){
                    if(data.length > 0){
                        $("#mensaje_c").html("<p id=\"mensaje_c\">Información Reconstruida Correctamente</p>");
                        $("#mdlConstruir").modal("show");
                    }else{
                        $("#mensaje_c").html("<p id=\"mensaje_c\">La información no se reconstruyo correctamente</p>");
                        $("#mdlConstruir").modal("show");
                    }
                }
            });
        }
    }

    var QueryString = function(){
        var query_string = {};
        var query        = window.location.search.substring(1);
        var vars         = query.split("&");
        for (var i = 0; i < vars.length; i++) {
            var pair = vars[i].split("=");
            if (typeof query_string[pair[0]] === "undefined") {
                query_string[pair[0]] = decodeURIComponent(pair[1]);
            } else if (typeof query_string[pair[0]] === "string") {
                var arr = [ query_string[pair[0]],decodeURIComponent(pair[1]) ];
                query_string[pair[0]] = arr;
            } else {
                query_string[pair[0]].push(decodeURIComponent(pair[1]));
            }
        }
        return query_string;
    }();

    var $factura = QueryString.factura;

    if($factura){
        $("#btnGuardar").attr('disabled',true);
        $("#btnImprimir, #btnRebuilt").attr('disabled',false);
        $("#btnRebuilt").css('display', 'none');
    }else{
        $("#btnGuardar").attr('disabled',false);
        $("#btnRebuilt").css('display', 'none');
        $("#btnImprimir,#btnModificar,#btnEliminar, #btnRebuilt").attr('disabled',true);
        $("#btnImprimir,#btnModificar,#btnEliminar, #btnRebuilt").removeAttr('onclick');
    }

    $salida = QueryString.mov;
    if(!$salida){
        $("#btnSalida").css('display', 'none');
    }

    function reload(){
        window.location.reload();
    }

    $("#sltConcepto").change(function(e){
        var concepto = e.target.value;
        $.get("access.php?controller=factura&action=obtenerUnidadesConcepto", { concepto: concepto }, function (data) {
            $("#sltUnidad").html(data);
            $("#sltUnidad").trigger("change");
        });
    });

    $("#sltUnidad").change(function (e) {
        let unidad   = e.target.value;
        let concepto = $("#sltConcepto").val();

        if(!isNaN(concepto)){
            $.get("access.php?controller=Punto&action=ObtenerValorTarifaUnidad", { unidad: unidad, concepto: concepto }, function(data){
                $("#sltValor").html(data).fadeIn();
                let valors = $("#sltValor").val();
                CambiarValor(valors);
            });
        }
    });
</script>
<?php if(!empty($_GET['factura'])){ ?>
    <input type="hidden" id="tiporecaudo" name="tiporecaudo">
    <script>
        $(document).ready(function() {
            var form_data = { action : 3, id_factura : $("#id").val() };
            $.ajax({
                type:"POST",
                url:"jsonPptal/gf_facturaJson.php",
                data:form_data,
                success: function(data){
                    if( data !== 0 ){
                        $("#recaudo").css("display", "block");
                        $("#tiporecaudo").val(data);
                    }
                }
            });
        });
    </script>
    <script>
        function modalRecaudo(){
            $("#mdlRecaudo").modal("show");
        }
    </script>
    <div class="modal fade" id="mdlRecaudo" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Banco</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px;">
                    <div class="form-group form-inline" style="margin-left: 100px;">
                        <label for="sltBanco" class="control-label col-sm-2">
                            <strong class="obligado">*</strong>Banco:
                        </label>
                        <select name="sltBanco" id="sltBanco" class="select2_single col-sm-2 form-control input-sm" style="width : 300px; cursor : pointer; height : 30px;" title="Seleccione banco" required>
                            <?php
                            echo '<option value="">Banco</option>';
                            $sql4 = "SELECT  ctb.id_unico,CONCAT(ctb.numerocuenta,' ',ctb.descripcion)
                                            FROM gf_cuenta_bancaria ctb
                                            LEFT JOIN gf_cuenta_bancaria_tercero ctbt ON ctb.id_unico = ctbt.cuentabancaria
                                            WHERE ctbt.tercero ='". $_SESSION['compania']."' ORDER BY ctb.numerocuenta";
                            cargar_combos($sql4);
                            ?>
                        </select>
                        <br/>
                    </div>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="registrarRecaudo" class="btn" style="color: #000; margin-top: 2px;" data-dismiss="modal" >Registrar</button>
                    <button type="button" class="btn" style="color: #000; margin-top: 2px;" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        $("#registrarRecaudo").click(function(){
            if($("#sltBanco").val() !="") {
                var form_data = { action:5, factura:$("#id").val() };
                $.ajax({
                    type:"POST",
                    url:"jsonPptal/gf_facturaJson.php",
                    data:form_data,
                    success: function(data){
                        var resultado = JSON.parse(data);
                        var msj = resultado["msj"];
                        var rta = resultado["rta"];
                        if(rta==0){
                            var form_data={action:4, recaudo:$("#tiporecaudo").val(), banco:$("#sltBanco").val(),id_factura  : $("#id").val()};
                            $.ajax({
                                type:"POST",
                                url:"jsonPptal/gf_facturaJson.php",
                                data:form_data,
                                success: function(data){
                                    if(data ==0){
                                        $("#mensaje").html("Recudo Registrado Correctamente");
                                        $("#myModalError").modal("show");
                                        $("#btnErrorModal").click(function(){
                                            document.location.reload();
                                        })
                                    } else {
                                        $("#mensaje").html("Error Al Registrar Recaudo");
                                        $("#myModalError").modal("show");
                                        $("#btnErrorModal").click(function(){
                                            document.location.reload();
                                        })
                                    }
                                }
                            });
                        } else {
                            $("#mensaje").html(msj);
                            $("#myModalError").modal("show");
                            $("#btnErrorModal").click(function(){
                                $("#myModalError").modal("hide");
                            })
                        }
                    }
                });
            }

        })

        function CambiarValor(valor, tipo){
            try{

                //Validamos que el valor sea !=0
                var sltValor = valor;
                var dato = sltValor.split("/");
                
                if(dato[0]!=='0'){
                    $("#txtValorX").val($("#sltValor option:selected").text());
                    var tarifa = dato[1];
                    var form_data = { tarifa:tarifa, proceso:2 };
                    $.ajax({
                        type: 'POST',
                        url: "consultasFacturacion/consultarValor.php",
                        data:form_data,
                        success: function (data) {
                            var cantidad  = $("#txtCantidad").val();
                            let descuento = $("#txtXDescuento").val();

                            if(cantidad==0 || cantidad==''){
                                cantidad = 1;
                            }else{
                                cantidad = $("#txtCantidad").val();
                            }
                            valor    = $("#sltValor option:selected").text();

                            iva      = data;
                            total    = valor;
                            totalIva = 0;
                            valT     = 0;
                            xvalor   = 0;
                            if(iva !== '0'){
                                xvalor  = total / (1 + iva / 100);
                                $("#txtValorX").val(xvalor.toFixed(0));
                                totalIva = ( iva * xvalor ) / 100;
                                xvalor  = xvalor.toFixed(0) ;
                            } else {
                                xvalor  = total;
                            }
                            valT     = (parseFloat(xvalor) + parseFloat(totalIva.toFixed(0))) * cantidad;

                            $("#txtIva").val(totalIva.toFixed(0));
                            $("#txtValorA").val(valT.toFixed(0));
                            $("#txtImpoconsumo").val(0);
                            $("#txtAjustePeso").val(0);
                        }
                    });

                    $("#txtIva").prop("readonly", false);
                    $("#txtImpoconsumo").prop("readonly",false);
                    $("#txtAjustePeso").prop("readonly",false);
                }else{
                    //Declaracion de variables
                    var can = 0;
                    var valor = 0;
                    var iva = 0.00;
                    var operI = 0;
                    var imp = 0.00;
                    var operM = 0;
                    var sumaS = 0;
                    var ajuste = 0;
                    var ajusteTs = 0;
                    var redondeo = 0;
                    var valT = 0;
                    var xtotal = 0;
                    var xtotalI = 0;
                    //Habilitamos los campos
                    $("#txtIva, #txtImpoconsumo, #txtAjustePeso").prop("readonly", false);
                    //Ponemos valores vacios
                    $("#txtIva, #txtAjustePeso, #txtValorA").val('0');
                    $("#txtImpoconsumo").val('0');
                    //Cambiamos el select por un textbox
                    $("#sltValor").replaceWith('<input type="text" id="txtValor" name="txtValor" class="form-control" style="width:100%; padding:2px;" placeholder="Valor" title="Ingrese el valor" onkeypress="return justNumbers(event)"/>');
                    //Foco al campo de valor
                    $("#txtValor").focus();
                    //Cambio de titulo
                    $("#txtIva").prop("title",'Ingrese el porcentaje de iva');
                    $("#txtImpoconsumo").prop("title",'Ingrese el porcentaje de impuesto al consumo');
                    $("#txtAjustePeso").prop("title","Ajuste al peso");
                    //Cambio de puntero
                    $("#txtIva, #txtImpoconsumo, #txtAjustePeso, #txtValorA").css('cursor','pointer');
                    //Validación para campo de cantidad
                    if($("#txtCantidad").val()==''){
                        can = 1;
                    }else{
                        can = ($("#txtCantidad").val());
                    }
                    //Validación para el campo valor
                    $("#txtValor").blur(function(){
                        //Valor
                        if($("#txtValor").val()!==0){
                            //Operacion de valor
                            valor = ($("#txtValor").val()*can);
                            //Asiganción de valores
                            $("#txtValorA").val(valor);
                            //Validación de campos
                            if(isNaN($("#txtImpoconsumo").val())){
                                $("#txtImpoconsumo").val("0");
                            }
                            if(isNaN($("#txtAjustePeso").val())){
                                $("#txtAjustePeso").val('0');
                            }
                        }
                    });
                    //Validación para el campo de iva
                    $("#txtIva").blur(function(){
                        //iva
                        var iv = $("#txtIva").val();
                        if( iv && $("#txtImpoconsumo").val() == 0){
                            //Captura de valores
                            iva           = ($("#txtIva").val());
                            valor     = ($("#txtValor").val());

                            let xvalor = valor / (1 + iva / 100);

                            $("#txtValorX").val(xvalor.toFixed(2));
                            $("#txtValor").val(xvalor.toFixed(2));
                            //Operaciones
                            operI      = parseFloat(xvalor.toFixed(2)) * parseFloat(iva) / 100;
                            valT       = (parseFloat(xvalor.toFixed(2)) + parseFloat(operI.toFixed(2))) * can;
                            //Asignación de valores
                            $("#txtIva").val(operI.toFixed(2));
                            $("#txtValorA").val(valT.toFixed(2));
                        }
                    });
                    //Validación para campo de impoconsumo
                    $("#txtImpoconsumo").blur(function(){
                        //Impoconsumo
                        var im = $("#txtImpoconsumo").val();
                        if( im && $("#txtIva").val() == 0){
                            //Captura de valores
                            imp           = $("#txtImpoconsumo").val();
                            let descuento = $("#txtXDescuento").val();
                            valor    = ($("#txtValor").val());

                            let xvalor    = valor / (1 + imp / 100)
                            $("#txtValor").val(xvalor.toFixed(2));
                            $("#txtValorX").val(xvalor.toFixed(2));
                            //Operaciones
                            operM   = parseFloat(xvalor.toFixed(2)) * parseFloat(imp) / 100;
                            valT    = (parseFloat(xvalor.toFixed(2)) + parseFloat(operM.toFixed(2))) * can;
                            //Asignación de valores
                            $("#txtImpoconsumo").val(operM.toFixed(2));
                            $("#txtValorA").val(valT);
                            $("#txtValor").val(xvalor.toFixed(2));
                        }
                    });
                    //Validación de ajuste al peso
                    $("#txtAjustePeso").blur(function(){
                        //Ajuste al peso
                        var aj =  $("#txtAjustePeso").val();
                        if(aj!=='0'){
                            //Captura de valores
                            ajuste = $("#txtAjustePeso").val();
                            valor = ($("#txtValor").val()*can);
                            imp = $("#txtImpoconsumo").val();
                            iva = ($("#txtIva").val());
                            //Operaciones
                            sumaS    = valor + xtotal + xtotalI;
                            redondeo = redondeaAlAlza(sumaS,ajuste);
                            ajusteTs = redondeoTotal(sumaS,ajuste);
                            //Asignación de valores
                            $("#txtAjustePeso").val(redondeo);
                            $("#txtValorA").val(ajusteTs);
                        }
                    });
                }
            }catch($e){

            }
        }
    </script>
    <div class="modal fade" id="myModalError" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px;">
                    <labe id="mensaje" name="mensaje" style="font-weight: 700;"></labe>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnErrorModal" class="btn" style="color: #000; margin-top: 2px;" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
     <script type="text/javascript" src="js/select2.js"></script>
      <script src="dist/jquery.validate.js"></script>
       <script src="js/scriptFacturacion.js"></script>
        <script src="js/bootstrap.js"></script>
    <script type="text/javascript">

         $('#s2id_autogen3_search').on("keydown", function(e) {
                let term = e.currentTarget.value;
                let form_data4 = {action: 8, term: term};
                console.log('tercero');
                $.ajax({
                    type:"POST",
                    url:"jsonPptal/gf_tercerosJson.php",
                    data:form_data4,
                    success: function(data){
                        let option = '<option value=""> - </option>';
                        //console.log(data);
                         option = option+data;
                        $("#sltTercero").html(data);
                            
                    }
                }); 
            });

            $('#s2id_autogen1_search').on("keydown", function(e) {
                let term = e.currentTarget.value;
                let form_data4 = {action: 59, term: term};
                console.log('USUSARIO');
                $.ajax({
                    type:"POST",
                    url:"jsonPptal/gf_facturaJson.php",
                    data:form_data4,
                    success: function(data){
                        let option = '';
                        console.log(data);
                         option = option+data;
                        $("#sltVendedor").html(data);
                    }
                }); 
            });


    </script>
    <script type="text/javascript">
        $("#sltBanco").select2({placeholder:"Banco",allowClear: true});
    </script>
<?php } ?>
</html>