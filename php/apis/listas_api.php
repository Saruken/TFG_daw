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
    $dbtable='lista';
    if(isset($_SESSION['log'])){
        $usuario=$_SESSION['log'];
        $con=" where usuario!='$usuario'";
    }else{
        $usuario=null;
        $con="";
    }
    if(isset($_GET['busqueda'])){
        if(isset($_SESSION['log'])){
            $con=$con." and nombre like '%".$_GET['busqueda']."%'";
        }else{
            $con=$con." where nombre like '%".$_GET["busqueda"]."%'";
        }
    }

    //PARA SIMULAR EL RETARDO DEL SERVIDOR
    sleep(1);
    $default_limite=8;
    $limite=$_REQUEST["limite"] ?? $default_limite;
    $offset=$_REQUEST["offset"] ?? 0;
    
    $conexion=conectarBD($dbhost,$dbuser,$dbpass);
    $conexion->select_db($dbname);
    
    $datos=[];
    
    $sentencia_total=$conexion->prepare("SELECT count(*) FROM $dbtable$con");

    $sentencia_total->execute();
        
    $total_filas=$sentencia_total->get_result()->fetch_row()[0];
    
    $info["total"]=$total_filas;

    $sentencia=$conexion->query("select id,nombre from lista$con limit $offset, $limite");

    while($lista=$sentencia->fetch_array(MYSQLI_ASSOC)){
        $preparada=$conexion->prepare("select count(*) from listas_seguidas where lista=? and usuario=?");
        $preparada->bind_param("is",$lista['id'],$usuario);
        $preparada->bind_result($sigue);
        $preparada->execute();
        $preparada->fetch();
        $lista["sigue"]=$sigue;
        $preparada->close();

        $preparada=$conexion->prepare("select imagen from juego,juegos_lista where id=juego and lista=? limit 1");
        $preparada->bind_param("i",$lista['id']);
        $preparada->bind_result($img);
        $preparada->execute();
        $preparada->fetch();
        if($img==null){
            $imagen="assets/img/logo.png";
        }else{
            $imagen=$img;
            // $clase='imagen_lista';
        }
        $lista["imagen"]=$imagen;
        $preparada->close();
        $datos[]=$lista;
        $img='';
    }
    $info["datos"]=$datos;

    $patron_URL=explode("?",$_SERVER["REQUEST_URI"])[0];
    
    $nuevo_offset=$offset+$limite;
    if($nuevo_offset<$total_filas){
        $sig_offset=$nuevo_offset;
        if($nuevo_offset+$limite>$total_filas){
            $sig_limite=$total_filas-$nuevo_offset;
        }else{
            $sig_limite=$limite;
        }
        $info["siguiente"]=$patron_URL."?offset=$sig_offset&limite=$sig_limite";  
    }else{
        $info["siguiente"]="null";
    }

    $nuevo_offset=$offset-$limite;

    if($offset==0){
        $info["anterior"]="null";
    }else{
        if($nuevo_offset>0){
            $ant_limite=$default_limite;
            $nuevo_offset=$offset-$default_limite;
            $ant_offset=$nuevo_offset;
        }else{
            $ant_limite=$offset;
            $ant_offset=0;
        }
        $info["anterior"]=$patron_URL."?offset=$ant_offset&limite=$ant_limite";  
    }

    echo json_encode($info);
 ?>