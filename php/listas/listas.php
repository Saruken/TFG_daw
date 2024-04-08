<?php
    require_once "../funciones.php";
    apertura();

    if(!isset($_SESSION['log'])){
        header('Refresh: 0; URL=../../index.php');
    }else if($_SESSION['log']=='admin'){
        echo "<p id='admin' class='d-none'>borrar</p>";
    }

    if(isset($_POST['borrar'])){
        $conexion=conectar();
        $preparada=$conexion->prepare("delete from juegos_lista where lista=?");
        $preparada->bind_param("i",$_POST['id']);
        $preparada->execute();
        $preparada->close();

        $preparada=$conexion->prepare("delete from listas_seguidas where lista=?");
        $preparada->bind_param("i",$_POST['id']);
        $preparada->execute();
        $preparada->close();

        $preparada=$conexion->prepare("delete from lista where id=?");
        $preparada->bind_param("i",$_POST['id']);
        $preparada->execute();
        $preparada->close();
        $conexion->close();
    }
?>
<script src="../../script/listas/listas_app.js" defer></script>
<main class="container-fluid px-3 p-0 d-flex align-content-start flex-wrap">
    <input class='busca-lista' type="text" name="" placeholder="Nombre..." id="buscador_nombre">
    <section class='w-100 row mx-auto'>
        <?php
            $conexion=conectar();

            $conexion->close();
        ?>
    </section>
    <div class='text-center d-flex mx-auto paginas'>
        <a class="text-decoration-none fs-5 m-3" id="anterior" href="">Anterior</a>
        <a class="text-decoration-none fs-5 m-3" id="siguiente" href="">Siguiente</a>
    </div>
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