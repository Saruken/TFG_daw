<?php
	//Cabeceras
	header('Content-Type: application/json');
	header("Access-Control-Allow-Origin: *");
    include "../funciones.php";
    
    $dbhost='localhost';
    $dbuser='root';
    $dbpass='';
    $dbname='db';
    $dbtable='categoria';

    //PARA SIMULAR EL RETARDO DEL SERVIDOR
    sleep(1);
    
    $conexion=conectarBD($dbhost,$dbuser,$dbpass);
    $conexion->select_db($dbname);
    
    $datos=[];
    
    $sentencia_total=$conexion->prepare("SELECT count(*) FROM $dbtable");

    $sentencia_total->execute();
        
    $total_filas=$sentencia_total->get_result()->fetch_row()[0];
    
    $info["total"]=$total_filas;

    $sentencia=$conexion->prepare("SELECT id,nombre FROM $dbtable");
    
    $sentencia->execute();

    $resultado=$sentencia->get_result();

    while($fila=$resultado->fetch_assoc()){ 
         	$datos[]=$fila;
    }
    $info["datos"]=$datos;

    echo json_encode($info);
 ?>
