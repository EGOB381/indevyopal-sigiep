<?php
  require_once('../Conexion/conexion.php');
  session_start();
 
  $id  = $_GET['id'];
  $flujo  = $_GET['flujo'];
  
 $modFlujo = "UPDATE gg_flujo_procesal SET flujo_si = '$flujo' WHERE id_unico = '$id'";
   $flujo= $mysqli->query($modFlujo);
 

   echo json_encode($flujo);
?>