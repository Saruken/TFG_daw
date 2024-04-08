<?php
    require_once "../funciones.php";
    apertura();

    if(!isset($_SESSION['log']) or $_SESSION['log']=='admin'){
        header('Refresh: 0; URL=../../index.php');
    }

    echo "<main>";

    if(!isset($_GET['usuario']) || $_GET['usuario']==$_SESSION['log']){
        $usuario=$_SESSION['log'];

        $conexion=conectar();
        $consulta=$conexion->prepare("select count(*) from lista where usuario=?");
        $consulta->bind_param("s",$usuario);
        $consulta->bind_result($cantidad);
        $consulta->execute();
        $consulta->fetch();
        $consulta->close();
        if($cantidad==10){
            $crear="";
        }else{
            $crear='<button type="button" class="fs-6 fw-bold color-destaca text-decoration-none bg-transparent btn" data-bs-toggle="modal" data-bs-target="#exampleModalLista">Crear nueva</button>';
        }
        $conexion->close();

        $cabecera_listas='<h4 class="border-bottom d-flex justify-content-between">Mis Listas'.$crear.'</h4>';
        $boton='<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModalCenter">Modificar Perfil</button>';
    }else if(isset($_GET['usuario'])){
        $usuario=$_GET['usuario'];
        $cabecera_listas='<h4 class="border-bottom">Listas</h4>';

        $conexion=conectar();
        $preparada=$conexion->prepare("select count(*) from usuarios_seguidos where seguidor=? and seguido=?");
        $preparada->bind_param("ss",$_SESSION['log'],$usuario);
        $preparada->bind_result($cantidad);
        $preparada->execute();
        $preparada->fetch();
        if($cantidad!=1){
            $boton='<form action="#" method="post" enctype="multipart/form-data">
                <input type="hidden" name="seguido" value="'.$usuario.'">
                <input type="submit" name="seguir" value="Seguir" class="btn btn-primary">
            </form>';
        }else{
            $boton='<form action="#" method="post" enctype="multipart/form-data">
                <input type="hidden" name="seguido" value="'.$usuario.'">
                <input type="submit" name="dejar" value="Dejar de seguir" class="btn btn-danger">
            </form>';
        }
        echo $cantidad;
        $preparada->close();
        $conexion->close();
    }

    if(isset($_POST['seguir']) || isset($_POST['dejar'])){
        if(isset($_POST['seguir'])){
            $consulta="insert into usuarios_seguidos values (?,?)";
        }else if(isset($_POST['dejar'])){
            $consulta="delete from usuarios_seguidos where seguidor=? and seguido=?";
        }
        $conexion=conectar();
        $preparada=$conexion->prepare($consulta);
        $preparada->bind_param("ss",$_SESSION['log'],$_POST['seguido']);
        $preparada->execute();
        $preparada->close();
        $conexion->close();
        $actual_link = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
        header('Refresh:0; URL='.$actual_link.'?usuario='.$usuario);
    }

    if(isset($_POST['modificar'])){
        $conexion=conectar();
        if($_FILES['imagen']['name']!=""){
            $formato=explode("/",$_FILES['imagen']['type']);
            if($formato[0]=="image"){
                $ruta="assets/img/usuario/$usuario.$formato[1]";
                $preparada=$conexion->prepare("update usuario set correo=?,f_nac=?,imagen=? where nick=?");
                $preparada->bind_param("ssss",$_POST['correo'],$_POST['f_nac'],$ruta,$usuario);
                $preparada->execute();
                $preparada->close();
                move_uploaded_file($_FILES['imagen']['tmp_name'],"../../$ruta");
            }else{
                echo "El archivo debe de ser una imagen";
            }
        }else{
            $preparada=$conexion->prepare("update usuario set correo=?,f_nac=? where nick=?");
            $preparada->bind_param("sss",$_POST['correo'],$_POST['f_nac'],$usuario);
            $preparada->execute();
            $preparada->close();
        }
        $conexion->close();
    }
    
    if(isset($_POST['borrar'])){
        $conexion=conectar();
        $preparada=$conexion->prepare("delete from lista where id=? and usuario=?");
        $preparada->bind_param("is",$_POST['id'],$_SESSION['log']);
        $preparada->execute();
        $preparada->close();
        $conexion->close();
        $actual_link = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
        header('Refresh:0; URL='.$actual_link.'?usuario='.$_SESSION['log']);
    }

    if(isset($_POST['dejar_lista'])){
        $conexion=conectar();
        $accion=$conexion->prepare("delete from listas_seguidas where lista=? and usuario=?");
        $accion->bind_param("is",$_POST['id'],$_SESSION['log']);
        $accion->execute();
        $accion->close();
        $conexion->close();
    }

    if(isset($_POST['enviar'])){
        $conexion=conectar();

        $preparada=$conexion->prepare("select nick from usuario where nick=? and nick!='admin'");
        $preparada->bind_param("s",$_SESSION['log']);
        $preparada->bind_result($user);
        $preparada->execute();
        $preparada->store_result();
        if($preparada->num_rows>0){
            $preparada->fetch();
        }
        $preparada->close();

        $consulta=$conexion->prepare("select count(*) from lista where nombre=? and usuario=?");
        $consulta->bind_param("ss",$_POST['nombre'],$user);
        $consulta->bind_result($cantidad);
        $consulta->execute();
        $consulta->fetch();
        $consulta->close();

        if($cantidad==0){
            $publica=0;
            if(isset($_POST['publica'])){
                $publica=1;
            }

            $insercion=$conexion->prepare("insert into lista (nombre,usuario) values (?,?)");
            $insercion->bind_param("ss",$_POST['nombre'],$usuario);
            $insercion->execute();
            $insercion->close();
            echo "<p class='notificacion mal'>Lista creada</p>";
        }else{
            echo "<p class='notificacion mal'>ERROR - Ya tienes una lista con este nombre</p>";
        }
        $conexion->close();
    }

    $conexion=conectar();
    $preparada=$conexion->prepare("select imagen from usuario where nick=?");
    $preparada->bind_param("s",$usuario);
    $preparada->bind_result($imagen);
    $preparada->execute();
    $preparada->fetch();
    $preparada->close();
    $conexion->close();

    echo '
    <section class="container-fluid row mx-auto my-3">
        <div class="col-lg-6 col-md-10 col-12 row mx-auto border rounded-4 p-4 border-dark border-2 bg-secondary">
            <div class="col-10 mx-auto d-flex flex-column align-items-center">
                <img class="w-50 ratio ratio-1x1 rounded-circle border border-5 border-dark" src="../../'.$imagen.'" alt="">
                <h3 class="my-4">'.$usuario.'</h3>
                '.$boton.'
            </div>
            <div class="col row mx-auto border p-5 mt-4 rounded-4 border-secondary bg-seguidos text-light">
                <section>
                    '.$cabecera_listas.'
                    <div>';

    $conexion=conectar();
    $preparada=$conexion->prepare("select count(*) from lista where usuario=?");
    $preparada->bind_param("s",$usuario);
    $preparada->bind_result($cantidad);
    $preparada->execute();
    $preparada->fetch();
    $preparada->close();
    if($cantidad==0){
        echo "<p class='fs-4 text-center my-5'>No se ha creado ninguna lista</p>";
    }else{
        $preparada=$conexion->query("select nombre,id from lista where usuario='$usuario'");
        while($lista=$preparada->fetch_array(MYSQLI_ASSOC)){
            $id=$lista['id'];
            $nombre=$lista['nombre'];
            $consulta=$conexion->query("select imagen from juego,juegos_lista where juego=id and lista=$id limit 1");
            $imagen=$consulta->fetch_array(MYSQLI_NUM);
            if($imagen==null){
                $imagen="../../assets/img/logo.png";
                $clase='';
            }else{
                $imagen=$imagen[0];
                $clase='imagen_lista';
            }
            $mio='';
            if($usuario==$_SESSION['log']){
                $mio="<form action='#' method='post' enctype='multipart/form-data'>
                    <input type='hidden' name='id' value='$lista[id]'>
                    <input name='borrar' type='submit' class='btn btn-danger mx-1' value='Borrar'>
                </form>";
            }
            echo "
            <div class='listas-card col-md-12 col my-2'>
                <div class='d-flex p-3 h-100 bg-dark'>
                    <div class='rectangulo bg-white d-flex align-items-center'>
                        <img class='w-100 $clase' src='$imagen'>
                    </div>
                    <div class='ms-3 d-flex justify-content-between align-items-center flex-wrap flex-md-nowrap'>
                        <h3 class='text-capitalize text-truncate mx-1'><a class='text-decoration-none' href='../listas/pagina_lista.php?id=$lista[id]'>$lista[nombre]</a></h3>
                        $mio
                    </div>
                </div>
            </div>
            ";
            $consulta->close();
        }
        $preparada->close();
    }
    $conexion->close();
?>
    <div class="modal fade" id="exampleModalCenter" tabindex="-1" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5 text-dark" id="exampleModalCenterTitle">Modificar perfil</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="#" method="post" enctype='multipart/form-data'>
                    <div class="modal-body text-dark">
                        <div class="mb-3 no-delete">
                            <?php
                                $conexion=conectar();
                                $user=$_SESSION['log'];                            
                                $consulta=$conexion->query("select * from usuario where nick='$user'");
                                $lista=$consulta->fetch_array(MYSQLI_ASSOC);
                                
                                echo "
                                <div class='d-flex flex-column mb-3'>
                                    <label for='correo'>Correo electrónico</label>
                                    <input class='form-control' type='email' name='correo' value='$lista[correo]'>
                                </div>
                                <div class='d-flex flex-column mb-3'>
                                    <label for='f_nac'>Fecha de nacimiento</label>
                                    <input class='form-control' type='date' name='f_nac' value='$lista[f_nac]'>
                                </div>
                                <div class='d-flex flex-column mb-3'>
                                    <label for='imagen'>Perfil</label>
                                    <input class='form-control' type='file' name='imagen'>
                                </div>
                                ";
                                $consulta->close();
                            ?>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <input type="submit" class="recargar btn btn-primary" name="modificar" value="Enviar">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="exampleModalLista" tabindex="-1" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5 text-dark" id="exampleModalCenterTitle">Crear lista</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="#" method="post" enctype='multipart/form-data'>
                    <div class="modal-body text-dark">
                        <div class="mb-3 no-delete">
                            <?php
                                $conexion=conectar();
                                $user=$_SESSION['log'];                            
                                $consulta=$conexion->query("select * from usuario where nick='$user'");
                                $lista=$consulta->fetch_array(MYSQLI_ASSOC);
                                
                                echo "
                                <div class='d-flex flex-column mb-3'>
                                    <label for='nombre'>Nombre</label>
                                    <input class='form-control' type='text' name='nombre'>
                                </div>
                                ";
                                $consulta->close();
                            ?>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <input type="submit" class="recargar btn btn-primary" name="enviar" value="Enviar">
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php
    echo '          </div>
                </section>
                <section>
                    <h4 class="border-bottom">Siguiendo</h4>
                    <details>
                        <summary>Listas</summary>';
    
    $conexion=conectar();
    $preparada=$conexion->prepare("select count(*) from listas_seguidas where usuario=?");
    $preparada->bind_param("s",$usuario);
    $preparada->bind_result($cantidad);
    $preparada->execute();
    $preparada->fetch();
    $preparada->close();
    if($cantidad==0){
        echo "<h3 class='text-center'>No sigue ninguna lista</h3>";
    }else{
        $preparada=$conexion->query("select nombre,id from lista,listas_seguidas where lista=id and listas_seguidas.usuario='$usuario'");
        while($lista=$preparada->fetch_array(MYSQLI_ASSOC)){
            $id=$lista['id'];
            $nombre=$lista['nombre'];
            $consulta=$conexion->query("select imagen from juego,juegos_lista where juego=id and lista=$id limit 1");
            $imagen=$consulta->fetch_array(MYSQLI_NUM);
            if($imagen==null){
                $imagen="../../assets/img/logo.png";
                $clase='';
            }else{
                $imagen=$imagen[0];
                $clase='imagen_lista';
            }
            $mio='';
            if($usuario==$_SESSION['log']){
                $mio="<form action='#' method='post' enctype='multipart/form-data'>
                    <input type='hidden' name='id' value='$lista[id]'>
                    <input name='dejar_lista' type='submit' class='btn btn-danger mx-1' value='Dejar de seguir'>
                </form>";
            }
            echo "
            <div class='listas-card col-md-12 col my-2'>
                <div class='d-flex p-3 h-100 bg-dark'>
                    <div class='rectangulo bg-white d-flex align-items-center'>
                        <img class='w-100 $clase' src='$imagen'>
                    </div>
                    <div class='ms-3 d-flex justify-content-between align-items-center flex-wrap flex-md-nowrap'>
                        <h3 class='text-capitalize text-truncate mx-1'><a class='text-decoration-none' href='../listas/pagina_lista.php?id=$lista[id]'>$lista[nombre]</a></h3>
                        $mio
                    </div>
                </div>
            </div>
            ";
        }
        $preparada->close();
    }

    echo "          </details>
                    <details>
                        <summary>Usuario</summary>";

    $preparada=$conexion->prepare("select count(*) from usuarios_seguidos where seguidor=?");
    $preparada->bind_param("s",$usuario);
    $preparada->bind_result($cantidad);
    $preparada->execute();
    $preparada->fetch();
    $preparada->close();
    if($cantidad==0){
        echo "<h3 class='text-center'>No sigue a ningún usuario</h3>";
    }else{
        $preparada=$conexion->query("select nick,imagen from usuario,usuarios_seguidos where seguido=nick and seguidor='$usuario'");
        while($lista=$preparada->fetch_array(MYSQLI_ASSOC)){
            $nick=$lista['nick'];
            $imagen=$lista['imagen'];
            $mio='';
            if($usuario==$_SESSION['log']){
                $mio="<form action='#' method='post' enctype='multipart/form-data'>
                    <input type='hidden' name='seguido' value='$nick'>
                    <input name='dejar' type='submit' class='btn btn-danger mx-1' value='Dejar de seguir'>
                </form>";
            }
            echo "
            <div class='listas-card col-md-12 col my-2'>
                <div class='d-flex p-3 h-100 bg-dark'>
                    <div class='rounded-circle bg-white d-flex align-items-center'>
                        <img class='w-100 h-100 rounded-circle' src='../../$imagen'>
                    </div>
                    <div class='ms-3 d-flex justify-content-between align-items-center flex-wrap flex-md-nowrap'>
                        <h3 class='text-capitalize text-truncate mx-1'><a class='text-decoration-none' href='../listas/pagina_lista.php?id=$nick'>$nick</a></h3>
                        $mio
                    </div>
                </div>
            </div>
            ";
        }
        $preparada->close();
    }
    $conexion->close();

    echo '              </details>
                    </section>
                </div>
            </div>
        </section>
    </main>
    
    ';
    cierre();
?>