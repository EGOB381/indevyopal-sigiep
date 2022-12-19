<?php
require_once('Conexion/conexion.php');
require_once('head_listar.php');
$query = "SELECT fac.id_unico, fac.numero_factura, 
IF(CONCAT_WS(' ',
      tr.nombreuno,
      tr.nombredos,
      tr.apellidouno,
      tr.apellidodos) 
      IS NULL OR CONCAT_WS(' ',
      tr.nombreuno,
      tr.nombredos,
      tr.apellidouno,
      tr.apellidodos) = '',
      (tr.razonsocial),
      CONCAT_WS(' ',
      tr.nombreuno,
      tr.nombredos,
      tr.apellidouno,
      tr.apellidodos)) AS NOMBRE, fac.fecha_factura, 
      SUM(det.valoru_conversion*det.cantidad), SUM((det.iva/det.valor_trm)*det.cantidad), SUM((det.impoconsumo/det.valor_trm)*det.cantidad), SUM(det.valor_conversion), 
      tr.email, (SELECT CONCAT_WS(' - ',d.direccion, cd.nombre) FROM gf_direccion d LEFT JOIN gf_ciudad cd ON d.ciudad_direccion = cd.id_unico WHERE d.tercero = tr.id_unico LIMIT 1), 
      (IF(tr.razonsocial IS NULL OR tr.razonsocial ='', 'modificar_TERCERO_CLIENTE_NATURAL.php?id_ter_clie_nat', 'modificar_TERCERO_CLIENTE_JURIDICA.php?id_ter_clie_jur')), tr.id_unico, trg.nombre, 
      (SELECT GROUP_CONCAT(DISTINCT rf.nombre) FROM gf_tercero_responsabilidad trs LEFT JOIN gf_responsabilidad_fiscal rf ON trs.responsabilidad = rf.id_unico WHERE trs.tercero = tr.id_unico) as responsabilidesd, tr.numeroidentificacion 
FROM gp_factura fac 
LEFT JOIN gp_tipo_factura gpt ON gpt.id_unico = fac.tipofactura
LEFT JOIN gf_tercero tr ON tr.id_unico = fac.tercero
LEFT JOIN gp_detalle_factura det ON det.factura = fac.id_unico 
LEFT JOIN gf_tipo_regimen trg ON tr.tiporegimen = trg.id_unico 
WHERE gpt.facturacion_e = 1 AND fac.parametrizacionanno = '".$_SESSION['anno']."'  AND fac.cufe IS NULL AND fac.tipo_cambio IS NOT NULL
GROUP BY fac.id_unico
HAVING  SUM(det.valor_total_ajustado)>0
ORDER BY cast(fac.numero_factura as unsigned) DESC";
$resultado = $mysqli->query($query);

?>
  <title>Facturación Extranjera</title>
</head>
<body>

<div class="container-fluid text-center">
    <div class="row content">
    <?php require_once ('menu.php'); ?>
        <div class="col-sm-10 text-left">
            <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;margin-top: -2px">Facturas Extranjeras</h2>
            <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;margin-top: -15px">
                <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <td class="cabeza" style="display: none;">Identificador</td>
                            <td class="cabeza" width="30px" align="center"></td>
                            <td class="cabeza"><strong>Nº</strong></td>
                            <td class="cabeza"><strong>Tercero</strong></td>
                            <td class="cabeza"><strong>Fecha</strong></td>
                            <td class="cabeza"><strong>Valor</strong></td>
                            <td class="cabeza"><strong>IVA</strong></td>
                            <td class="cabeza"><strong>Impoconsumo</strong></td>
                            <td class="cabeza"><strong>Valor Total Ajustado</strong></td>
                            <td class="cabeza"><strong>Email</strong></td>
                            <td class="cabeza"><strong>Dirección</strong></td>
                            <td class="cabeza"><strong>Régimen</strong></td>
                            <td class="cabeza"><strong>Responsabilidades Fiscales</strong></td>
                        </tr>
                        <tr>
                            <th class="cabeza" style="display: none;">Identificador</th>
                            <th class="cabeza" width="7%"></th>
                            <th class="cabeza"><strong>Nº</strong></th>
                            <th class="cabeza"><strong>Tercero</strong></th>
                            <th class="cabeza"><strong>Fecha</strong></th>
                            <th class="cabeza"><strong>Valor</strong></th>
                            <th class="cabeza"><strong>IVA</strong></th>
                            <th class="cabeza"><strong>Impoconsumo</strong></th>
                            <th class="cabeza"><strong>Valor Total Ajustado</strong></th>
                            <th class="cabeza"><strong>Email</strong></th>
                            <th class="cabeza"><strong>Dirección</strong></th>
                            <th class="cabeza"><strong>Régimen</strong></th>
                            <th class="cabeza"><strong>Responsabilidades Fiscales</strong></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_row($resultado)):?>
                          <tr>
                               <td class="campos" style="display: none;"></td>
                              <td>
                                <?php 
                                if(!empty($row[8]) && !empty($row[9]) && !empty($row[12]) && !empty($row[13])){ ?>
                                <a class="campos btn btn-primary sendBill" id="btnEnviar<?=$row[0]?>" href="consultasFacturacion/FacturaExtranjera.php?id=<?=$row[0]?>" type="button" onclick="javaScript:deshabilitar(<?=$row[0]?>)">
                                  <i title="Enviar Factura" class="glyphicon glyphicon-send"></i>
                                </a>
                              <?php } else {
                                echo '<a href="'.$row[10].'='.md5($row[11]).'" target="_blank" style="color:#f12020"><i class="glyphicon glyphicon-edit"></i>Modificar Datos Cliente</a>';
                              } ?>
                              </td>
                              <td class="campos" ><?=$row[1]?></td>
                              <td class="campos" ><?=$row[2].' - '.$row[14]?></td>
                              <td class="campos" ><?=date('d/m/Y', strtotime($row[3]))?></td>
                              <td class="campos" ><?="$ ".number_format($row[4], 2)?></td>
                              <td class="campos" ><?="$ ".number_format($row[5], 2)?></td>
                              <td class="campos" ><?="$ ".number_format($row[6], 2)?></td>
                              <td class="campos" ><?="$ ".number_format($row[7], 2)?></td>
                              <td class="campos" >
                                <?php if(empty($row[8])){
                                  echo '<label style="color:#f12020">CLIENTE NO TIENE CORREO </label>';
                                }  else {echo $row[8];}?></td>
                              <td class="campos" >
                                <?php if(empty($row[9])){
                                  echo '<label style="color:#f12020">CLIENTE NO TIENE DIRECCIÓN</label>';
                                }  else {echo $row[9];}?></td>
                              <td class="campos" >
                                <?php if(empty($row[12])){
                                  echo '<label style="color:#f12020">CLIENTE NO TIENE RÉGIMEN</label>';
                                }  else {echo $row[12];}?></td>
                              <td class="campos" >
                                <?php if(empty($row[13])){
                                  echo '<label style="color:#f12020">CLIENTE NO TIENE RESPONSABILIDADES FISCALES</label>';
                                }  else {echo $row[13];}?></td>

                          </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <div align="right"><a href="facturasElectronicasEnviadas.php?t=3" class="btn btn-primary" style="box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: 20px;margin-bottom: 20px; margin-left:-20px; margin-right:4px" target="_blank"><i class="glyphicon glyphicon-check"></i> Facturas Enviadas</a> </div>       
                </div>
            </div>
        </div>
    </div>
</div>
  <div class="modal fade mdl-info" id="mdlInfo" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;"></h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>

  <?php require_once ('footer.php'); ?>

  <link rel="stylesheet" href="css/bootstrap-theme.min.css">
  <script src="js/bootstrap.min.js"></script>
  <script src="js/facturacion_electronica/facturacion.js"></script>
  <script>
    $("#btnEnviar").click(function(){
      console.log('ENVIAR');
      $("#btnEnviar").attr('disabled', 'true');
    });

    function deshabilitar(id){
      console.log('ENVIAR2');
      $("#btnEnviar"+id).attr('disabled', 'true');
    }
  </script>
</body>
</html>


