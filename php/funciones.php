<?php
    if(basename($_SERVER['PHP_SELF'])=="funciones.php"){
        header('Refresh: 0; URL=../index.php');
    }

    function conectar(){
        $con=new mysqli('localhost','root','','db');
        $con->set_charset('utf8');
        return $con;
    }

    function sesion(){
        session_start();
        if(isset($_COOKIE['sesion'])){
            session_decode($_COOKIE['sesion']);
        }
    }

    function formato_fecha($fecha){
        $fecha_buena=date_format(date_create($fecha),"d/m/Y");
        return $fecha_buena;
    }

    function apertura(){
        sesion();

        $ubicacion_actual=basename($_SERVER['PHP_SELF']);

        $ruta="../..";
        if($ubicacion_actual=="index.php"){
            $ruta=".";
        }

        if(isset($_SESSION['log'])){
            if($_SESSION['log']=='admin'){
                $enlaces="
                <li class='nav-item'>
                    <a class='nav-link text-light' href='$ruta/php/listas/listas.php'>Listas</a>
                </li>
                <li class='nav-item'>
                    <a class='nav-link text-light' href='$ruta/php/apis/insertar_datos_api.php'>Actualizar Datos</a>
                </li>
                <li class='nav-item'>
                    <a class='nav-link text-light' href='$ruta/php/sesion/cerrar_sesion.php'>Cerrar Sesión</a>
                </li>
                ";

            }else{
                $enlaces="
                <li class='nav-item'>
                    <a class='nav-link text-light' href='$ruta/php/listas/listas.php'>Listas</a>
                </li>
                <li class='nav-item'>
                    <a class='nav-link text-light' href='$ruta/php/usuario/coleccion.php'>Mi colección</a>
                </li>
                <li class='nav-item'>
                    <a class='nav-link text-light' href='$ruta/php/usuario/perfil.php'>Perfil</a>
                </li>
                <li class='nav-item'>
                    <a class='nav-link text-light' href='$ruta/php/sesion/cerrar_sesion.php'>Cerrar Sesión</a>
                </li>
                ";
            }
        }else{
            $enlaces="
            <li class='nav-item'>
                <a class='nav-link text-light' href='$ruta/php/sesion/iniciar_sesion.php'>Iniciar Sesión</a>
            </li>
            <li class='nav-item'>
                <a class='nav-link text-light' href='$ruta/php/sesion/crear_usuario.php'>Registrarse</a>
            </li>
            ";
        }

        echo "
            <!DOCTYPE html>
            <html lang='es'>
            <head>
                <meta charset='UTF-8'>
                <meta http-equiv='X-UA-Compatible' content='IE=edge'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <title>Inicio</title>
                <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css' rel='stylesheet' integrity='sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65' crossorigin='anonymous'>
                <link rel='stylesheet' href='$ruta/assets/style.css'>
                <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css' integrity='sha512-SzlrxWUlpfuzQ+pcUCosxcglQRNAq/DZjVsC0lE40xsADsfeQoEypE+enwcOiGjk/bSuGGKHEyjSoQ1zVisanQ==' crossorigin='anonymous' referrerpolicy='no-referrer'/>
            </head>
            <body class='position-relative'>
                <header class='cabecera_pagina position-sticky top-0'>
                    <nav class='navbar navbar-expand-lg bg-dark p-0'>
                        <div class='container-fluid h-100 p-0'>
                            <a class='icono ms-2 h-100 rounded-circle m-0 navbar-brand bg-light' href='$ruta'>
                                <img class='img-fluid' src='$ruta/assets/img/logo.png'>
                            </a>
                            <button class='me-2 navbar-toggler bg-light' type='button' data-bs-toggle='collapse' data-bs-target='#navbarTogglerDemo02' aria-controls='navbarTogglerDemo02' aria-expanded='false' aria-label='Toggle navigation'>
                                <span class='navbar-toggler-icon'></span>
                            </button>
                            <div class='collapse navbar-collapse justify-content-lg-end bg-dark' id='navbarTogglerDemo02'>
                                <ul class='ms-2 navbar-nav mb-2 mb-md-0'>
                                    <li class='nav-item'>
                                        <a class='nav-link text-light' href='$ruta'>Inicio</a>
                                    </li>
                                    <li class='nav-item'>
                                        <a class='nav-link text-light' href='$ruta/php/juegos/juegos.php'>Juegos</a>
                                    </li>
                                    $enlaces
                                </ul>
                            </div>
                        </div>
                    </nav>
                </header>
        ";
    }
    
    function cierre(){
        echo "
                <footer class='bg-dark text-light text-center text-lg-start position-absolute w-100'>
                    <div class='text-center p-3' style='background-color: rgba(0, 0, 0, 0.2);'>
                        © 2023 Copyright
                    </div>
                </footer>
                <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js' integrity='sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4' crossorigin='anonymous'></script>
            </body>
            </html>
        ";
    }

    // function bloqueo(){
    //     if(){
    //         if(isset($_SESSION['log'])){

    //         }
    //     }else if(){
            
    //     }
    // }

    function conectarBD($host,$usuario,$contrasena){
        $mbd = new mysqli($host,$usuario,$contrasena);
        $mbd->set_charset("utf8");
        
        return $mbd;
    }

    function generarId($longitud){
        $patron="1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $clave="";
        for($i=1;$i<=$longitud;$i++){
            $caracter=$patron[rand(0,strlen($patron)-1)];
            $clave=$clave.$caracter;
        
        }
        echo $clave;
        return $clave;
    }
?>