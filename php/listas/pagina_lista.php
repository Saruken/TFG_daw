<?php
    require_once "../funciones.php";
    apertura();
    
    if(!isset($_SESSION['log'])){
        header('Refresh: 0; URL=../../index.php');
    }

    $conexion=conectar();
    if(isset($_POST['borrar'])){
        $preparada=$conexion->prepare("select count(*) from juegos_lista where juego=? and lista=?");
        $preparada->bind_param("ii",$_POST['id'],$_GET['id']);
        $preparada->bind_result($cantidad);
        $preparada->execute();
        $preparada->fetch();
        $preparada->close();
        if($cantidad==1){
            $insercion=$conexion->prepare("delete from juegos_lista where juego=? and lista=?");
            $insercion->bind_param("ii",$_POST['id'],$_GET['id']);
            $insercion->execute();
            $insercion->close();
        }
    }
    $conexion->close();
?>
<script src="../../script/listas/pagina_lista.js" defer></script>
<main class="container-fluid px-3 p-0 d-flex align-items-start">
    <section class='w-100 row mx-auto'>
        <?php
            $conexion=conectar();

            $preparada=$conexion->prepare("select nombre,usuario from lista where id=?");
            $preparada->bind_param("i",$_GET['id']);
            $preparada->bind_result($nombre,$creador);
            $preparada->execute();
            $preparada->fetch();
            echo "<span><h1 class='d-inline-block mt-2'>$nombre</h1><a class='ms-3 fs-5 text-decoration-none color-autor' href='../usuario/perfil.php?usuario=$creador'>$creador</a></span>";
            $preparada->close();

            $preparada=$conexion->prepare("select count(*) from lista where id=? and usuario=?");
            $preparada->bind_param("is",$_GET['id'],$_SESSION['log']);
            $preparada->bind_result($cantidad);
            $preparada->execute();
            $preparada->fetch();
            $preparada->close();
            if($cantidad!=0){
                $boton="<input class='rounded-pill px-4 py-1 bg-danger' type='submit' name='borrar' value='Sacar de lista'>";
                $envio="#";
            }else{
                $boton="";
                $envio="iframe_content.php' target='content";

            }

            $preparada=$conexion->prepare("select count(*) from juegos_lista where lista=?");
            $preparada->bind_param("i",$_GET['id']);
            $preparada->bind_result($cantidad);
            $preparada->execute();
            $preparada->fetch();
            $preparada->close();
            if($cantidad>0){
                $preparada=$conexion->prepare("select id,imagen,nombre from juego,juegos_lista where id=juego and lista=?");
                $preparada->bind_param("i",$_GET['id']);
                $preparada->bind_result($id,$imagen,$nombre);
                $preparada->execute();
                while($preparada->fetch()){
                    echo "
                    <div class='botones-juego col-lg-2 col-sm-4 col-6 fw-bold d-flex my-3 position-relative'>
                        <div class='ficha-juego text-center bg-light border border-1 border-dark w-100 position-relative d-flex flex-column rounded-top shadow'>
                            <div class='border border-1 border-dark border-top-0 border-end-0 border-start-0 rounded-top'>
                                <img class='w-100 h-100 obf rounded-top' src='$imagen'>
                            </div>
                            <a class='text-decoration-none text-secondary' href='../juegos/ficha_juego.php?id=$id'>
                                <p class='fs-5 mb-0 px-2 text-truncate bg-dark'>
                                    $nombre
                                </p>
                            </a>
                        </div>
                        <div class='w-100 position-absolute top-0 start-50 translate-middle-x container-fluid'>
                            <form class='formulario w-100 h-100 d-flex flex-column align-items-center justify-content-evenly bg-dark bg-opacity-50' action='$envio' method='post' enctype='multipart/form-data'>
                                <input type='hidden' name='id' value='$id'>
                                $boton
                            </form>
                        </div>
                    </div>
                    ";
                }
                $preparada->close();
            }else{
                echo "<p class='fs-3 text-center fw-bold text-danger text-uppercase'>Todavía no se han añadido juegos a la lista</p>";
            }
            
            $conexion->close();
        ?>
    </section>
    <iframe class='d-none' name="content">
    </iframe>
    <div id="cargando">
        <img src="https://media.tenor.com/wpSo-8CrXqUAAAAi/loading-loading-forever.gif">
        <h2>Cargando</h2>
    </div>
</main>
<?php
    cierre();
?>