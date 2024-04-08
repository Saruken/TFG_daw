<?php
    require_once "../funciones.php";
    apertura();

    if(!(isset($_SESSION['log']) and $_SESSION['log']=='admin')){
        header('Refresh: 0; URL=../../index.php');
    }
?>
<main>
    <script src='../script/datos_api.js' defer></script>
    <h1>PONER GIF CARGA</h1>
    <div>
        <form action="#" method="post" class='d-flex my-3'>
            <input type="hidden" name="plataformas" id="input_pl">
            <input class="btn btn-secondary mx-auto" type="submit" name="insertar_pl" value="Actualizar plataformas">
        </form>
    </div>
    <div>
        <form action="#" method="post" class='d-flex my-3'>
            <input type="hidden" name="categorias" id="input_ca">
            <input class="btn btn-secondary mx-auto" type="submit" name="insertar_ca" value="Actualizar categorias">
        </form>
    </div>
</main>
<?php
require_once("funciones.php");
// -----------------------------------------------
    if(isset($_POST['insertar_pl'])){
        $valores=explode(':-:',$_POST['plataformas']);
        for($i=0;$i<count($valores);$i++){
            $fila=explode(',',$valores[$i]);
            
            $conexion=conectar();
            $preparada=$conexion->prepare("select count(*) from plataforma where id=?");
            $preparada->bind_param("i",$fila[0]);
            $preparada->bind_result($cantidad);
            $preparada->execute();
            $preparada->fetch();
            if($cantidad==0){
                $preparada->close();
                $insercion=$conexion->prepare("insert into plataforma (id,nombre,activo) values (?,?,0)");
                $insercion->bind_param("is",$fila[0],$fila[1]);
                $insercion->execute();
                $insercion->close();
            }
            $conexion->close();
        }
        echo "<p>Plataformas actualizadas con éxito</p>";
    }
// -----------------------------------------------
    if(isset($_POST['insertar_ca'])){
        $valores=explode(':-:',$_POST['categorias']);
        for($i=0;$i<count($valores);$i++){
            $fila=explode(',',$valores[$i]);

            $conexion=conectar();
            $preparada=$conexion->prepare("select count(*) from categoria where id=?");
            $preparada->bind_param("i",$fila[0]);
            $preparada->bind_result($cantidad);
            $preparada->execute();
            $preparada->fetch();
            if($cantidad==0){
                $preparada->close();
                $insercion=$conexion->prepare("insert into categoria (id,nombre) values (?,?)");
                $insercion->bind_param("is",$fila[0],$fila[1]);
                $insercion->execute();
                $insercion->close();
            }
            $conexion->close();
        }
        echo "<p>Categorías actualizadas con éxito</p>";
    }
    cierre();
?>