<?php
    require_once "../funciones.php";
    apertura();

    if(!isset($_SESSION['log']) or $_SESSION['log']=='admin'){
        header('Refresh: 0; URL=../../index.php');
    }

    $conexion=conectar();
    if(isset($_POST['tengo']) or isset($_POST['quiero'])){
        $preparada=$conexion->prepare("select count(*) from juego where id=?");
        $preparada->bind_param("i",$_POST['id']);
        $preparada->bind_result($cantidad);
        $preparada->execute();
        $preparada->fetch();
        $preparada->close();

        if($cantidad==0){
            $insercion=$conexion->prepare("insert into juegos values (?,?,?,?)");
            $insercion->bind_param("isss",$_POST['id'],$_POST['nombre'],$_POST['imagen'],$_POST['fecha']);
            $insercion->execute();
            $insercion->close();
        }

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
<!-- <script src='../../script/carga_api.js' defer></script> -->
<script src="../../script/usuario/coleccion_app.js" defer></script>
<main>

    <!-- <button class="btn btn-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasScrolling" aria-controls="offcanvasScrolling">Enable body scrolling</button> -->

    <!-- <div class="offcanvas offcanvas-start" data-bs-scroll="true" data-bs-backdrop="false" tabindex="-1" id="offcanvasScrolling" aria-labelledby="offcanvasScrollingLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasScrollingLabel">Filtros</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <div id='categorias' class='row w-100 mx-auto d-flex justify-content-between'>
                <p>Categorias-Mirar consulta musltiples categorias</p>

            </div>
            <div id='plataformas' class='row w-100 mx-auto d-flex justify-content-between'>
                <p>Plataformas</p>

            </div>
        </div>
    </div> -->

    <!-- <input type="text" name="" id="buscador_nombre"> -->

    <section class='container-fluid row mx-auto'>

    </section>
    <!-- <div class='text-center'>
        <a id="anterior" href="">Anterior</a>
        <a id="siguiente" href="">Siguiente</a>
    </div> -->
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