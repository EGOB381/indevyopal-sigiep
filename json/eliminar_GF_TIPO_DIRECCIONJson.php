<?php 
	require_once('../Conexion/conexion.php');
    session_start();
   
   //Captura de ID e instrucción SQL para su eliminación de la tabla gf_tipo_direccion.
   $id = $_GET['id'];
   $query = "DELETE FROM gf_tipo_direccion WHERE Id_Unico = $id";
   $resultado = $mysqli->query($query);

  echo json_encode($resultado);
?>