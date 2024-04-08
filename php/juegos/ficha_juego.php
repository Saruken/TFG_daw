<?php
    require_once "../funciones.php";
    apertura();
    echo "<p id='id-juego' class='d-none'>".$_GET['id']."</p>";

    if(isset($_SESSION['log']) and $_SESSION['log']=='admin'){
        echo "<p id='admin' class='d-none'>borrar</p>";
        $boton_borrar="<input name='borrar' type='submit' class='btn btn-danger mx-1' value='Borrar'>";
    }else{
        $boton_borrar="";
    }

    if(isset($_POST['borrar'])){
        $conexion=conectar();
        $preparada=$conexion->prepare("delete from opinion where juego=? and usuario=?");
        $preparada->bind_param("is",$_GET['id'],$_POST['usuario']);
        $preparada->execute();
        $preparada->close();
        $conexion->close();
    }

    if(isset($_POST['enviar']) or isset($_POST['insertar'])){
        $conexion=conectar();
        $preparada=$conexion->prepare("select count(*) from juego where id=?");
        $preparada->bind_param("i",$_POST['id']);
        $preparada->bind_result($cantidad);
        $preparada->execute();
        $preparada->fetch();
        $preparada->close();

        if($cantidad==0){
            $insercion=$conexion->prepare("insert into juego values (?,?,?,?)");
            $insercion->bind_param("isss",$_POST['id'],$_POST['nombre'],$_POST['fecha'],$_POST['imagen']);
            $insercion->execute();
            $insercion->close();
        }

        if(isset($_POST['enviar'])){
            $preparada=$conexion->prepare("select count(*) from opinion where usuario=? and juego=?");
            $preparada->bind_param("si",$_SESSION['log'],$_GET['id']);
            $preparada->bind_result($cantidad);
            $preparada->execute();
            $preparada->fetch();
            $preparada->close();
            if($cantidad==0){
                    $preparada=$conexion->prepare("insert into opinion values (?,?,?,?)");
                    $preparada->bind_param("sisd",$_SESSION['log'],$_GET['id'],$_POST['comentario'],$_POST['puntos']);
                    $preparada->execute();
                    $preparada->close();
            }else{
                echo "<p>No puedes añadir más de un comentario en el mismo juego</p>";
            }
        }
        if(isset($_POST['insertar'])){
            $preparada=$conexion->prepare("select count(*) from juegos_lista where lista=? and juego=?");
            $preparada->bind_param("ii",$_POST['lista'],$_GET['id']);
            $preparada->bind_result($cantidad);
            $preparada->execute();
            $preparada->fetch();
            $preparada->close();
            if($cantidad==0){
                    $preparada=$conexion->prepare("insert into juegos_lista values (?,?)");
                    $preparada->bind_param("ii",$_POST['lista'],$_GET['id']);
                    $preparada->execute();
                    $preparada->close();
            }else{
                echo "<p>Este juego ya se encuentra en la lista seleccionada</p>";
            }
        }
        $conexion->close();
    }
?>
<script src='../../script/carga_api.js' defer></script>
<script src="../../script/juegos/ficha_app.js" defer></script>
<main>
    <section class='container-fluid row mx-auto'>
        <div class="info col-xl-9 col-md-11 col-12 mx-auto my-5 rounded-4 border border-3 border-dark position-relative p-3 shadow-lg">
            <p class='row col-9 position-absolute top-0 start-0 h-100 w-100 rounded-4 mb-0 mx-auto'><!-- imagen de fondo --></p>
            <div class='col-md-10 col-12 mx-auto p-3 text-light rounded-2'>
                <h1 class='text-center'><!-- nombre --></h1>
                <div>
                    <p class='mb-1'>Fecha de publicación: <!-- fecha publicacion --></p>
                    <p class='mb-1 color-destaca'><!-- generos --></p>
                    <p class='mb-1 color-destaca'><!-- plataformas --></p>
                </div>
                <p><!-- descripcion --></p>
            </div>
        </div>
    </section>
    <section class='container-fluid row mx-auto'>
        <h2>Juegos Similares</h2>
        <div class='otros d-flex mx-auto col-lg-9 col-11 row justify-content-center'>

        </div>
    </section>
    <?php
        if(isset($_SESSION['log'])){
            echo "<section id='comentarios' class='w-100 border border-3 border-dark bg-secondary'>";
        }else{
            echo "<section id='comentarios' class='w-100 border border-3 border-dark bg-secondary d-none'>";
        }
            $conexion=conectar();
            $preparada=$conexion->prepare("select count(*) from opinion where usuario=? and juego=?");
            $preparada->bind_param("si",$_SESSION['log'],$_GET['id']);
            $preparada->bind_result($cantidad);
            $preparada->execute();
            $preparada->fetch();
            $preparada->close();
            if((isset($_SESSION['log']) and $_SESSION['log']=='admin') or $cantidad!=0){
                $mostrar="class='d-none'";
            }else if($cantidad==0){
                $mostrar="";
            }
            
            echo '
            <form action="" method="post" '.$mostrar.'>
                <div class="estrellas mx-auto w-75 mt-3 fs-5">
                    <i class="fa-regular fa-star"></i>
                    <i class="fa-regular fa-star"></i>
                    <i class="fa-regular fa-star"></i>
                    <i class="fa-regular fa-star"></i>
                    <i class="fa-regular fa-star"></i>
                </div>
                <input type="hidden" name="puntos" value="0">
                <textarea name="comentario" cols="20" rows="6" placeholder="Escribe tu comentario..." class="p-2 d-block mx-auto w-75 m-3 rounded-3"></textarea>
                <div class="row w-75 mx-auto mb-3 d-flex justify-content-end">
                    <input type="submit" name="enviar" value="Enviar" class="col-1 btn btn-primary fit">
                </div>
            </form>
            ';

            $preparada=$conexion->prepare("select imagen,usuario,texto,puntos from opinion,usuario where nick=usuario and juego=?");
            $preparada->bind_param("i",$_GET['id']);
            $preparada->bind_result($imagen,$usuario,$texto,$puntos);
            $preparada->execute();
            while($preparada->fetch()){
                $cadena="";
                for($i=0;$i<5;$i++){
                    if($i<ceil($puntos-1)){
                        $cadena=$cadena."<i class='fa-solid fa-star'></i>";
                    }else if($i>ceil($puntos-1)){
                        $cadena=$cadena."<i class='fa-regular fa-star'></i>";
                    }else{
                        if($puntos-1==ceil($puntos-1)){
                            $cadena=$cadena."<i class='fa-solid fa-star'></i>";
                        }else{
                            $cadena=$cadena."<i class='fa-regular fa-star-half-stroke'></i>";
                        }
                    }
                }
                echo "
                <div class='w-75 mx-auto border border-2 rounded-2 border-dark m-4 p-3 row bg-white position-relative opinion'>
                    <div class='col-2'>
                        <img class='rounded-circle w-100' src='../../$imagen'>
                    </div>
                    <div class='col d-flex flex-column'>
                        <div class='mb-3'>
                            <p class='mb-0 me-2 fw-bold d-inline-block'>$usuario</p>
                            $cadena
                        </div>
                        <p class='m-0'>$texto</p>
                    </div>
                    <form action='#' method='post' enctype='multipart/form-data'>
                        <input type='hidden' name='usuario' value='$usuario'>
                        $boton_borrar
                    </form>
                </div>
                ";
            }
            $preparada->close();
            $conexion->close();
        ?>
    </section>

    <button type="button" class="add-lista btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModalCenter">
        Añadir a lista
    </button>

    <div class="modal fade" id="exampleModalCenter" tabindex="-1" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalCenterTitle">Añadir Juego</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="#" method="post">
                    <div class="modal-body">
                        <div class="mb-3 no-delete">
                            <label for="recipient-name" class="col-form-label">Tus Listas</label>
                            <select name='lista' class='form-select' aria-label='Default select example'>
                            <?php
                                $conexion=conectar();
                                $user=$_SESSION['log'];                            
                                $consulta=$conexion->query("select id,nombre from lista where usuario='$user'");
                                while($lista=$consulta->fetch_array(MYSQLI_ASSOC)){
                                    $preparada=$conexion->prepare("select count(*) from juegos_lista where lista=? and juego=?");
                                    $preparada->bind_param("ii",$lista['id'],$_GET['id']);
                                    $preparada->bind_result($cantidad);
                                    $preparada->execute();
                                    $preparada->fetch();
                                    if($cantidad==0){
                                        echo "<option value='$lista[id]'>$lista[nombre]</option>";
                                    }
                                    $preparada->close();
                                }
                                $consulta->close();
                                $conexion->close();
                            ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div id="cargando">
        <img src="https://media.tenor.com/wpSo-8CrXqUAAAAi/loading-loading-forever.gif">
        <h2>Cargando</h2>
    </div>
</main>
<?php
    cierre();
?>