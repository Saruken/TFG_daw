<?php
    require_once "../funciones.php";
    sesion();

    header('Refresh: 0; URL=../../index.php');

    $conexion=conectar();
    if(isset($_POST['tengo']) or isset($_POST['quiero'])){
        $preparada=$conexion->prepare("select count(*) from juegos_usuario where juego=? and usuario=?");
        $preparada->bind_param("is",$_POST['id'],$_SESSION['log']);
        $preparada->bind_result($cantidad);
        $preparada->execute();
        $preparada->fetch();
        $preparada->close();

        if(isset($_POST['tengo'])){
            $estado=1;
        }else{
            $estado=0;
        }

        if($cantidad==0){
            $insercion=$conexion->prepare("insert into juegos_usuario values (?,?,?)");
            $insercion->bind_param("sii",$_SESSION['log'],$_POST['id'],$estado);
            $insercion->execute();
            $insercion->close();
        }else{
            $preparada=$conexion->prepare("select estado from juegos_usuario where juego=? and usuario=?");
            $preparada->bind_param("is",$_POST['id'],$_SESSION['log']);
            $preparada->bind_result($estado_actual);
            $preparada->execute();
            $preparada->fetch();
            $preparada->close();
            if($estado_actual!=$estado){
                $insercion=$conexion->prepare("update juegos_usuario set estado=? where juego=? and usuario=?");
                $insercion->bind_param("iis",$estado,$_POST['id'],$_SESSION['log']);
                $insercion->execute();
                $insercion->close();
            }
        }
    }

    if(isset($_POST['borrar'])){
        $preparada=$conexion->prepare("select count(*) from juegos_usuario where juego=? and usuario=?");
        $preparada->bind_param("is",$_POST['id'],$_SESSION['log']);
        $preparada->bind_result($cantidad);
        $preparada->execute();
        $preparada->fetch();
        $preparada->close();
        if($cantidad==1){
            $insercion=$conexion->prepare("delete from juegos_usuario where juego=? and usuario=?");
            $insercion->bind_param("is",$_POST['id'],$_SESSION['log']);
            $insercion->execute();
            $insercion->close();
        }
    }
    $conexion->close();
?>