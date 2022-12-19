<?php 
###################################################################################################
#**************************************** Modificaciones ****************************************##
###################################################################################################
#31/01/2018 | Erica G. |Campo Clase Descuento
###################################################################################################
#27/09/2022 | Elkin O. |Campo Base Ingresos
###################################################################################################
#05/04/2017 --- Nestor B --- se agrego el atributo mb para que tome las tildes
require_once 'head_listar.php';
  require_once 'Conexion/conexion.php';

  $sql = "SELECT Id_Unico, Nombre, clase_sia,base_ingresos FROM gf_clase_retencion ORDER BY Nombre ASC";

  $rs = $mysqli->query($sql);

  

 ?>
  <title>Listar Clase Retención</title>
 </head>
 <body>

  <div class="container-fluid text-center">
  <div class="row content">
    
    <?php require_once ('menu.php'); ?>

    <div class="col-sm-10 text-left">

      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Clase Retención</h2>

      

      <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
          <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
          <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
            <thead>

              <tr>
                <td style="display: none;">Identificador</td>
                <td width="30px" align="center"></td>
                <td><strong>Nombre</strong></td>
                <td><strong>Clase Descuento</strong></td>
                <td><strong>Base Ingresos</strong></td>
              </tr>

              <tr>
                <th style="display: none;">Identificador</th>
                <th width="7%"></th>
                <th>Nombre</th>
                <th>Clase Descuento</th>
                <th>Base Ingresos</th>
              </tr>

            </thead>
            <tbody>
              
              <?php
                while($row = mysqli_fetch_row($rs)){?>
               <tr>
                <td style="display: none;"><?php echo $row[0]?></td>
                <td><a href="#" onclick="javascript:eliminarContrato(<?php echo $row[0];?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>
                    <a href="EDITAR_GF_CLASE_RETENCION.php?ide=<?php echo md5($row[0]);?>"><i title="Modificar" class="glyphicon glyphicon-edit" ></i></a></td>
                <td><?php echo ucwords(mb_strtolower($row[1]));?></td>
                
                <td><?php if($row[2]==1) { echo 'Descuento Retenciones';} else {echo 'Otros Descuentos';}?></td>
                <td><?php if($row[3]==1) { echo 'Si';} else {echo 'No';}?></td>
                
              </tr>
              <?php } ?>


            </tbody>
          </table>

          <div align="right"><a href="GF_CLASE_RETENCION.php" class="btn btn-primary " style=" box-shadow: 0px 2px 5px 1px gray;
       color: #fff; border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:-20px; margin-right:4px">Registrar Nuevo</a> </div>       

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
          <p>¿Desea eliminar el registro seleccionado Clase Retención?</p>
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


  <?php require_once ('footer.php'); ?>

  <script type="text/javascript" src="js/menu.js"></script>
  <link rel="stylesheet" href="css/bootstrap-theme.min.css">
  <script src="js/bootstrap.min.js"></script>

<script type="text/javascript">
      function eliminarContrato(id)
      {
         var result = '';
         $("#myModal").modal('show');
         $("#ver").click(function(){
              $("#mymodal").modal('hide');
              $.ajax({
                  type:"GET",
                  url:"json/eliminarClaseRetencionJson.php?id="+id,
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
        document.location = 'LISTAR_GF_CLASE_RETENCION.php';
      });
    
  </script>

  <script type="text/javascript">
    
      $('#ver2').click(function(){
        document.location = 'LISTAR_GF_CLASE_RETENCION.php';
      });
    
  </script>

</body>
</html>