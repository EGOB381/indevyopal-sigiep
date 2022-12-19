<?php
#05/04/2017 --- Nestor B --- se agrego el atributo mb para que tome las tildes 
require_once 'head_listar.php';
require_once('Conexion/conexion.php');
$anno = $_SESSION['anno'];
$queryTipoC = "SELECT ehta.id_unico,
	    ehc.id_espacio_habitable,
            ehc.id_concepto,
            eh.descripcion,
            cn.nombre,
            ehta.ano,
            ehta.valor,
            p.anno
            FROM gph_espacio_habitable_tarifa ehta
            left join gph_espacio_habitable_concepto  ehc on ehc.id_unico=ehta.id_espacio_habitable_concepto
            LEFT JOIN gh_espacios_habitables eh on eh.id_unico=ehc.id_espacio_habitable
            LEFT join gp_concepto cn on cn.id_unico=ehc.id_concepto 
            left join gf_parametrizacion_anno p on p.id_unico= ehta.ano 
            WHERE p.id_unico = $anno 
            order by ehc.id_espacio_habitable asc";

$resultado = $mysqli->query($queryTipoC);
?>
<title>Listar Espacio Habitable Tarifa</title>
</head>
<body>  
    <div class="container-fluid text-center">
        <div class="row content">

            <?php require_once 'menu.php'; ?>

            <div class="col-sm-10 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Espacio Habitable Tarifa</h2>
                <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                        <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <td style="display: none;">Identificador</td>
                                    <td width="30px" align="center"></td>
                                    <td><strong>Espacio Habitable</strong></td>
                                    <td><strong>Concepto</strong></td>
                                    <td><strong>Valor</strong></td>
                                </tr>

                                <tr>
                                    <th style="display: none;">Identificador</th>
                                    <th width="7%"></th>
                                    <th>Espacio Habitable</th>
                                    <th>Concepto</th>   
                                    <th>Valor</th>  
                                </tr>

                            </thead>
                            <tbody>

                                <?php while ($row = mysqli_fetch_row($resultado)) { ?>
                                    <tr>
                                        <td style="display: none;"><?php echo $row[0] ?></td>
                                        <td>
                                            <a class="" href="#" onclick="javascript:eliminarTipoc(<?php echo $row[0]; ?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>
                                            <a href="modificar_GPH_ESPACIO_HABITABLE_TARIFA.php?id=<?php echo md5($row[0]); ?>"><i title="Modificar" class="glyphicon glyphicon-edit" ></i></a>
                                            <a class="campos" href="ver_GPH_ESPACIO_HABITABLE_TARIFA.php?id=<?php echo md5($row[0]); ?>">
                                                <i title="Ver Detalle" class="glyphicon glyphicon-eye-open" ></i>
                                            </a>
                                        </td>
                                        <td><?php echo ucwords(mb_strtolower($row[3])) ?></td>      
                                        <td><?php echo ucwords(mb_strtolower($row[4])) ?></td>   
                                        <td><?php echo ucwords(mb_strtolower(number_format($row[6], 2, '.', ','))) ?></td>   
                                    </tr>
                                <?php } ?>

                            </tbody>
                        </table>
                        <div class="text-right"><!-- text-right -->
                            <button style="margin-right:30px; margin-top: 10px;" onclick="reporteExcel()" class="btn sombra btn-primary" title="Generar reporte Excel"><i class="fa fa-file-excel-o" aria-hidden="true"></i></button>
                            <a href="registrar_GPH_ESPACIO_HABITABLE_TARIFA.php" class="btn btn-primary sombra" style=" box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:-20px; margin-right:4px">Registrar Nuevo</a> 
                        </div>
                        

                    </div>      
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="myModal" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>¿Desea eliminar el registro seleccionado de Espacio habitable tarifa?</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="ver" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="myModal1" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Información eliminada correctamente.</p>
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
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>No se pudo eliminar la información, el registro seleccionado está siendo utilizado por otra dependencia.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>

    <?php require_once 'footer.php'; ?>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>

    <script type="text/javascript">
        function eliminarTipoc(id)
        {
            var result = '';
            $("#myModal").modal('show');
            $("#ver").click(function () {
                $("#mymodal").modal('hide');
                $.ajax({
                    type: "GET",
                    url: "json/eliminarEspacioHabitableTarifaJson.php?id=" + id,
                    success: function (data) {
                        result = JSON.parse(data);
                        if (result == true)
                            $("#myModal1").modal('show');
                        else
                            $("#myModal2").modal('show');
                    }
                });
            });
        }
    </script>

    <script type="text/javascript">
        function modal()
        {
            $("#myModal").modal('show');
        }
    </script>

    <script type="text/javascript">

        $('#ver1').click(function () {
            document.location = 'listar_GPH_ESPACIO_HABITABLE_TARIFA.php';
        });

    </script>

    <script type="text/javascript">

        $('#ver2').click(function () {
            document.location = 'listar_GPH_ESPACIO_HABITABLE_TARIFA.php';
        });

    </script>
    <script>
        function reporteExcel(){
            window.open('informes/generar_INF_EH_TARIFA.php');
        }
    </script>
</body>
</html>
