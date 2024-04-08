<?php
    require_once "../funciones.php";
    sesion();

    header('Refresh: 0; URL=../../index.php');
    
    if(isset($_POST['seguir'])){
        if(isset($_SESSION['log'])){
            $conexion=conectar();
    
            $consulta=$conexion->prepare("select count(*) from listas_seguidas where lista=? and usuario=?");
            $consulta->bind_param("ss",$_POST['id'],$_SESSION['log']);
            $consulta->bind_result($cantidad);
            $consulta->execute();
            $consulta->fetch();
            $consulta->close();
    
            if($cantidad==0){    
                $accion=$conexion->prepare("insert into listas_seguidas values (?,?)");
            }else{
                $accion=$conexion->prepare("delete from listas_seguidas where lista=? and usuario=?");
            }
            $accion->bind_param("is",$_POST['id'],$_SESSION['log']);
            $accion->execute();
            $accion->close();
            $conexion->close();
        }
    }
?>