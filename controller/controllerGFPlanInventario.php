<?php
/**
 * controllerGFPlanInventario.php
 *
 * Archivo de control para registro, modificación y eliminado de la tabla elemento ficha
 *
 * @author Alexander Numpaque
 * @package Plan Inventario
 * @param String $action Variable para indicar que proceso se va a realizar
 * @version $Id: controllerGFPlanInventario.php 001 2017-05-26 Alexander Numpaque$
 */

session_start();
$compania = $_SESSION['compania'];
$param    = $_SESSION['anno'];
if(!empty($_POST['action'])){
    $action =  $_POST['action'];
}elseif ($_GET['action']) {
    $action =  $_GET['action'];
}

require ('../json/registrar_GF_PLAN_INVENTARIOJson.php');

if($action == 'insert') {
    $codigo = '"'.$_POST['codigo'].'"';
    $nombre = '"'.$_POST['nombre'].'"';
    $movimiento = '"'.$_POST['movimiento'].'"';
    $tipoInv = '"'.$_POST['tipoInv'].'"';
    $undFact = '"'.$_POST['undFact'].'"';
    if(empty($_POST['predecesor'])){
        $predecesor = 'NULL';
    }else{
        $predecesor = '"'.$_POST['predecesor'].'"';
    }
    $tipoAct = '"'.$_POST['tipoAct'].'"';

    if (empty($_POST['sltFicha'])) {
        $ficha = 'NULL';
    }else{
        $ficha = '"'.$_POST['sltFicha'].'"';
    }

    if(empty($_REQUEST['chkCapacidad'])){
       $xCantidad = "NULL";
    }else{
        $xCantidad = $_REQUEST['chkCapacidad'];
    }

    if(empty($_REQUEST['chkConcepto'])){
        $xConcepto = "NULL";
    }else{
        $xConcepto = $_REQUEST['chkConcepto'];
    }
    
    if(empty($_REQUEST['codigoBarras'])){
        $codigoB = "NULL";
    }else{
        $codigoB = $_REQUEST['codigoBarras'];
    }

    $padre = $_POST['sltPlanPadre'];
    $result = gf_plan_inventario::save_data($codigo, $nombre, $movimiento, $tipoInv, $undFact, $tipoAct, $compania, $predecesor, $ficha, $xCantidad, $xConcepto, $codigoB);
    echo "<html>\n";
    echo "<head>\n";
    echo "\t<meta charset=\"utf-8\">\n";
    echo "\t<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">\n";
    echo "\t<link rel=\"stylesheet\" href=\"../css/bootstrap.min.css\">\n";
    echo "\t<link rel=\"stylesheet\" href=\"../css/style.css\">\n";
    echo "\t<script src=\"../js/md5.pack.js\"></script>\n";
    echo "\t<script src=\"../js/jquery.min.js\"></script>\n";
    echo "\t<link rel=\"stylesheet\" href=\"../css/jquery-ui.css\" type=\"text/css\" media=\"screen\" title=\"default\" />\n";
    echo "\t<script type=\"text/javascript\" language=\"javascript\" src=\"../js/jquery-1.10.2.js\"></script>\n";
    echo "</head>\n";
    echo "<body>\n";
    echo "</body>\n";
    echo "</html>\n";
    echo "<div class=\"modal fade\" id=\"myModal1\" role=\"dialog\" align=\"center\" >\n";
    echo "\t<div class=\"modal-dialog\">\n";
    echo "\t\t<div class=\"modal-content\">\n";
    echo "\t\t\t<div id=\"forma-modal\" class=\"modal-header\">\n";
    echo "\t\t\t\t<h4 class=\"modal-title\" style=\"font-size: 24px; padding: 3px;\">Información</h4>\n";
    echo "\t\t\t</div>\n";
    echo "\t\t\t<div class=\"modal-body\" style=\"margin-top: 8px\">\n";
    echo "\t\t\t\t<p>Información guardada correctamente.</p>\n";
    echo "\t\t\t</div>\n";
    echo "\t\t\t<div id=\"forma-modal\" class=\"modal-footer\">\n";
    echo "\t\t\t\t<button type=\"button\" id=\"ver1\" class=\"btn\" style=\"color: #000; margin-top: 2px\" data-dismiss=\"modal\" >Aceptar</button>\n";
    echo "\t\t\t</div>\n";
    echo "\t\t</div>\n";
    echo "\t</div>\n";
    echo "</div>\n";
    echo "<div class=\"modal fade\" id=\"myModal2\" role=\"dialog\" align=\"center\" >\n";
    echo "\t<div class=\"modal-dialog\">\n";
    echo "\t\t<div class=\"modal-content\">\n";
    echo "\t\t\t<div id=\"forma-modal\" class=\"modal-header\">\n";
    echo "\t\t\t\t<h4 class=\"modal-title\" style=\"font-size: 24px; padding: 3px;\">Información</h4>\n";
    echo "\t\t\t</div>\n";
    echo "\t\t\t<div class=\"modal-body\" style=\"margin-top: 8px\">\n";
    echo "\t\t\t\t<p>No se ha podido guardar la información.</p>\n";
    echo "\t\t\t\n</div>";
    echo "\t\t\t<div id=\"forma-modal\" class=\"modal-footer\">\n";
    echo "\t\t\t\t<button type=\"button\" id=\"ver2\" class=\"btn\" style=\"color: #000; margin-top: 2px\" data-dismiss=\"modal\">Aceptar</button>\n";
    echo "\t\t\t</div>\n";
    echo "\t\t</div>\n";
    echo "\t</div>\n";
    echo "</div>\n";
    echo "<link rel=\"stylesheet\" href=\"../css/bootstrap-theme.min.css\">";
    echo "<script src=\"../js/bootstrap.js\"></script>";
    if($result==true){
        gf_plan_inventario::save_plan_p($codigo, $padre);
        if($xConcepto == 1){
            $id      = gf_plan_inventario::obtenerIDRegistro($codigo);
            $xExiste = gf_plan_inventario::existeConcepto($id);
            if(empty($xExiste) || $xExiste === 0){
                $data = new gf_plan_inventario();
                $data->tipo_concepto       = 1;
                $data->nombre              = $nombre;
                $data->tipo_operacion      = 1;
                $data->plan_inventario     = $id;
                $data->factor_base         = "NULL";
                $data->compania            = $compania;
                $x = gf_plan_inventario::guardarConceptoFactura($data);
            }
        }
        echo "<script type=\"text/javascript\">\n";
        echo "\t$(\"#myModal1\").modal('show');\n";
        echo "\t$(\"#ver1\").click(function(){\n";
        echo "\t\t$(\"#myModal1\").modal('hide');\n";
        echo "\t\twindow.location='../GF_PLAN_INVENTARIO.php';";
        echo "\t});";
        echo "</script>";
    }else{
        echo "<script type=\"text/javascript\">";
        echo "\t$(\"#myModal2\").modal('show');\n";
        echo "\t$(\"#ver2\").click(function(){\n";
        echo "\t\t$(\"#myModal2\").modal('hide');\n";
        echo "\t\twindow.history.go(-1)";
        echo "\t});";
        echo "</script>";
    }
}elseif($action == 'modify') {
    $id_unico  = '"'.$_POST['id'].'"';
    $codigo = '"'.$_POST['codigo'].'"';
    $nombre = '"'.$_POST['nombre'].'"';
    $movimiento = '"'.$_POST['movimiento'].'"';
    $tipoInv = '"'.$_POST['tipoInv'].'"';
    $undFact = '"'.$_POST['undFact'].'"';
    if(empty($_POST['predecesor'])){
        $predecesor = 'NULL';
    }else{
        $predecesor = '"'.$_POST['predecesor'].'"';
    }
    $tipoAct = '"'.$_POST['tipoAct'].'"';
    if (empty($_POST['sltFicha'])) {
        $ficha = 'NULL';
    }else{
        $ficha = '"'.$_POST['sltFicha'].'"';
    }
    $padre = $_POST['sltPlanPadre'];
    $planAso = $_POST['planAso'];

    if(empty($_REQUEST['chkCapacidad'])){
        $xCantidad = "0";
    }else{
        $xCantidad = $_REQUEST['chkCapacidad'];
    }

    if(empty($_REQUEST['chkConcepto'])){
        $xConcepto = "0";
    }else{
        $xConcepto = $_REQUEST['chkConcepto'];
    }
    if(empty($_REQUEST['codigoBarras'])){
        $codigoB = "NULL";
    }else{
        $codigoB = $_REQUEST['codigoBarras'];
    }

    $result = gf_plan_inventario::modify_data($codigo, $nombre, $movimiento, $tipoInv, $undFact, $tipoAct, $predecesor, $ficha, $id_unico, $xCantidad, $xConcepto,$codigoB);
    echo "<html>\n";
    echo "<head>\n";
    echo "\t<meta charset=\"utf-8\">\n";
    echo "\t<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">\n";
    echo "\t<link rel=\"stylesheet\" href=\"../css/bootstrap.min.css\">\n";
    echo "\t<link rel=\"stylesheet\" href=\"../css/style.css\">\n";
    echo "\t<script src=\"../js/md5.pack.js\"></script>\n";
    echo "\t<script src=\"../js/jquery.min.js\"></script>\n";
    echo "\t<link rel=\"stylesheet\" href=\"../css/jquery-ui.css\" type=\"text/css\" media=\"screen\" title=\"default\" />\n";
    echo "\t<script type=\"text/javascript\" language=\"javascript\" src=\"../js/jquery-1.10.2.js\"></script>\n";
    echo "</head>\n";
    echo "<body>\n";
    echo "</body>\n";
    echo "</html>\n";
    echo "<div class=\"modal fade\" id=\"myModal1\" role=\"dialog\" align=\"center\" >\n";
    echo "\t<div class=\"modal-dialog\">\n";
    echo "\t\t<div class=\"modal-content\">\n";
    echo "\t\t\t<div id=\"forma-modal\" class=\"modal-header\">\n";
    echo "\t\t\t\t<h4 class=\"modal-title\" style=\"font-size: 24px; padding: 3px;\">Información</h4>\n";
    echo "\t\t\t</div>\n";
    echo "\t\t\t<div class=\"modal-body\" style=\"margin-top: 8px\">\n";
    echo "\t\t\t\t<p>Información modificada correctamente.</p>\n";
    echo "\t\t\t</div>\n";
    echo "\t\t\t<div id=\"forma-modal\" class=\"modal-footer\">\n";
    echo "\t\t\t\t<button type=\"button\" id=\"ver1\" class=\"btn\" style=\"color: #000; margin-top: 2px\" data-dismiss=\"modal\" >Aceptar</button>\n";
    echo "\t\t\t</div>\n";
    echo "\t\t</div>\n";
    echo "\t</div>\n";
    echo "</div>\n";
    echo "<div class=\"modal fade\" id=\"myModal2\" role=\"dialog\" align=\"center\" >\n";
    echo "\t<div class=\"modal-dialog\">\n";
    echo "\t\t<div class=\"modal-content\">\n";
    echo "\t\t\t<div id=\"forma-modal\" class=\"modal-header\">\n";
    echo "\t\t\t\t<h4 class=\"modal-title\" style=\"font-size: 24px; padding: 3px;\">Información</h4>\n";
    echo "\t\t\t</div>\n";
    echo "\t\t\t<div class=\"modal-body\" style=\"margin-top: 8px\">\n";
    echo "\t\t\t\t<p>No se ha podido modificar la información.</p>\n";
    echo "\t\t\t</div>\n";
    echo "\t\t\t<div id=\"forma-modal\" class=\"modal-footer\">\n";
    echo "\t\t\t\t<button type=\"button\" id=\"ver2\" class=\"btn\" style=\"color: #000; margin-top: 2px\" data-dismiss=\"modal\">Aceptar</button>\n";
    echo "\t\t\t</div>\n";
    echo "\t\t</div>\n";
    echo "\t</div>\n";
    echo "</div>\n";
    echo "<link rel=\"stylesheet\" href=\"../css/bootstrap-theme.min.css\">";
    echo "<script src=\"../js/bootstrap.js\"></script>";
    if($result==true){
        gf_plan_inventario::modify_plan_p($planAso, $padre, $id_unico);
        if($xConcepto == 1){
            $xExiste = gf_plan_inventario::existeConcepto($id_unico);
            if(empty($xExiste) || $xExiste === 0) {
                $data = new gf_plan_inventario();
                $data->tipo_concepto       = 1;
                $data->nombre              = $nombre;
                $data->tipo_operacion      = 1;
                $data->plan_inventario     = $id_unico;
                $data->concepto_financiero = "NULL";
                $data->formula             = "NULL";
                $data->factor_base         = "NULL";
                $data->parametrizacionanno = $param;
                $x = gf_plan_inventario::guardarConceptoFactura($data);
            }
        }else{
            gf_plan_inventario::eliminarConceptos($id_unico);
        }
        echo "<script type=\"text/javascript\">\n";
        echo "\t$(\"#myModal1\").modal('show');\n";
        echo "\t$(\"#ver1\").click(function(){\n";
        echo "\t\t$(\"#myModal1\").modal('hide');\n";
        echo "\t\twindow.location='../GF_PLAN_INVENTARIO.php';\n";
        echo "\t});";
        echo "</script>";
    }else{
        echo "<script type=\"text/javascript\">";
        echo "\t$(\"#myModal2\").modal('show');\n";
        echo "\t$(\"#ver2\").click(function(){\n";
        echo "\t\t$(\"#myModal2\").modal('hide');\n";
        echo "\t\twindow.history.go(-1)";
        echo "\t});";
        echo "</script>";
    }
}elseif($action == 'delete') {
    $id_unico = $_POST['id_unico'];
    $result = gf_plan_inventario::delete_data($id_unico);
    echo json_encode($result);
}
?>