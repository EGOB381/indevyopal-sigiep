<?php 
######################################################################################################
#*************************************     Modificaciones      **************************************#
######################################################################################################
#03/01/2017 | Erica G. | Parametrizacion Año
#10/04/2017 | Erica G. | Diseño, tíldes, búsquedas 
###########################################################################################################
?>
<?php
  require_once 'head_listar.php';
  require_once('Conexion/conexion.php');
  $pa = $_SESSION['anno'];
  $queryfuente = "SELECT f.id_unico, 
      f.nombre, 
      f.movimiento, 
      f.predecesor, fu.nombre, f.tipofuente, 
      tf.nombre, 
      f.recursofinanciero, 
      rf.nombre, f.equivalente    
  FROM gf_fuente f  
  LEFT JOIN  gf_fuente fu ON f.predecesor=fu.Id_Unico 
  LEFT JOIN gf_tipo_fuente tf ON f.tipofuente=tf.Id_Unico
  LEFT JOIN gf_recurso_financiero rf ON f.recursofinanciero=rf.id_unico 
  WHERE f.parametrizacionanno = $pa"; 

  $resultado = $mysqli->query($queryfuente);

?>
<title>Listar Fuente</title>
<style>
    body{
        font-size: 12px;
    }
</style>
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Fuente</h2>
                <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                        <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <td style="display: none;">Identificador</td>
                                    <td width="30px" align="center"></td>
                                    <td><strong>Nombre</strong></td>
                                    <td><strong>Movimiento</strong></td>
                                    <td><strong>Predecesor</strong></td>
                                    <td><strong>Tipo Fuente</strong></td>
                                    <td><strong>Recurso Financiero</strong></td>
                                    <td><strong>Equivalente</strong></td>
                                </tr>
                                <tr>
                                    <th style="display: none;">Identificador</th>
                                    <th width="7%"></th>
                                    <th>Nombre</th>
                                    <th>Movimiento</th>
                                    <th>Predecesor</th>
                                    <th>Tipo Fuente</th>
                                    <th>Recurso Financiero</th>
                                    <th>Equivalente</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($row = mysqli_fetch_row($resultado)){?>
                                <tr>
                                   <td style="display: none;"><?php echo $row[0]?></td>
                                   <td>
                                       <a href="#" onclick="javascript:eliminarFuente(<?php echo $row[0];?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>
                                       <a href="modificar_GF_FUENTE.php?id=<?php echo md5($row[0]);?>"><i title="Modificar" class="glyphicon glyphicon-edit" ></i></a>
                                   </td>
                                   <td><?php echo ucwords(mb_strtolower($row[1]));?></td>
                                   <td>
                                     <?php if(($row[2]) == 2){echo "No";}else{echo "Sí";} ?>
                                   </td>
                                   <td><?php echo ucwords(mb_strtolower($row[4]));?></td>
                                   <td><?php echo ucwords(mb_strtolower($row[6]));?></td>
                                   <td><?php echo ucwords(mb_strtolower($row[8]));?></td>                              
                                   <td><?php echo ucwords(mb_strtolower($row[9]));?></td>                              
                               </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <div align="right"><a href="registrar_GF_FUENTE.php" class="btn btn-primary sombra" style=" box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:-20px; margin-right:4px">Registrar Nuevo</a> </div>       
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
              <p>¿Desea eliminar el registro seleccionado de Fuente?</p>
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
    <script type="text/javascript" src="js/menu.js"></script>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>

    <script type="text/javascript">
      function eliminarFuente(id)
      {
         var result = '';
         $("#myModal").modal('show');
         $("#ver").click(function(){
              $("#mymodal").modal('hide');
              $.ajax({
                  type:"GET",
                  url:"json/eliminarFuente.php?id="+id,
                  success: function (data) {
                  result = JSON.parse(data);
                  if(result==true)
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
    
      $('#ver1').click(function(){
        document.location = 'listar_GF_FUENTE.php';
      });
    
    </script>
    <script type="text/javascript">
    
      $('#ver2').click(function(){
        document.location = 'listar_GF_FUENTE.php';
      });
    
    </script>
</body>
</html>

