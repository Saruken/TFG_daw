<?php
	//Cabeceras
	header('Content-Type: application/json');
	header("Access-Control-Allow-Origin: *");
    include "../funciones.php";
    
    session_start();
    if(isset($_COOKIE['sesion'])){
        session_decode($_COOKIE['sesion']);
    }

    $dbhost='localhost';
    $dbuser='root';
    $dbpass='';
    $dbname='db';
    $dbtable='juegos_usuario';
    if(isset($_SESSION['log'])){
        $usuario=$_SESSION['log'];
    }else{
        $usuario=null;
    }

    //PARA SIMULAR EL RETARDO DEL SERVIDOR
    sleep(1);
    
    $conexion=conectarBD($dbhost,$dbuser,$dbpass);
    $conexion->select_db($dbname);
    
    $datos=[];
    
    $sentencia_total=$conexion->prepare("SELECT count(*) FROM $dbtable where usuario='$usuario'");

    $sentencia_total->execute();
        
    $total_filas=$sentencia_total->get_result()->fetch_row()[0];
    
    $info["total"]=$total_filas;

    $sentencia=$conexion->prepare("SELECT id,nombre,f_lanzamiento,imagen,estado FROM $dbtable,juego where juego=id and usuario='$usuario' order by estado desc");
    
    $sentencia->execute();

    $resultado=$sentencia->get_result();

    while($fila=$resultado->fetch_assoc()){ 
         	$datos[]=$fila;
    }
    $info["datos"]=$datos;

    echo json_encode($info);
 ?>
