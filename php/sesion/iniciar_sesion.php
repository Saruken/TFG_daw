<?php
    require_once "../funciones.php";
    apertura();

    if(isset($_POST['entrar'])){
        $conexion=conectar();
        $preparada=$conexion->prepare("select count(*) from usuario where nick=? and contraseña=? and estado=1");
        $preparada->bind_param("ss",$_POST['nick'],$_POST['pass']);
        $preparada->bind_result($cantidad);
        $preparada->execute();
        $preparada->fetch();
        $preparada->close();
        $conexion->close();
        if($cantidad==1){
            echo "<p class='notificacion reborde text-center fs-1 mt-3 fw-semibold text-success'>Sesión iniciada</p>";
            $_SESSION['log']=$_POST['nick'];
            if(isset($_POST['abierto'])){
                setcookie('open',session_encode(),time()+(1*24*60*60),"/");
            }
            header('Refresh: 1.5; URL=../../index.php');
        }else{
            echo "<p class='notificacion reborde text-center fs-1 mt-3 fw-semibold text-danger'>Nick o contraseña incorrecto(s)</p>";
        }
    }
?>
<main class="container-fluid px-sm-3 p-0 d-flex align-items-center">
    <div class="w-100">
        <div class="container-md row mx-auto">
            <form class="col-xl-7 col-lg-8 mx-auto border border-1 border-dark rounded-2 shadow p-3 bg-secondary formulario-acceso" action="#" method="post" enctype='multipart/form-data'>
                <h1 class="text-center mb-3">Iniciar sesión</h1>
                <div class="input-group mb-3 px-5">
                    <span class="input-group-text" id="inputGroup-sizing-default">Nick</span>
                    <input required name="nick" type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default">
                </div>
                <div class="input-group mb-3 px-5">
                    <span class="input-group-text" id="inputGroup-sizing-default">Contraseña</span>
                    <input required name="pass" type="password" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default">
                </div>
                <div class="d-flex justify-content-center">
                    <input name="entrar" type="submit" class="btn btn-primary" value="Entrar">
                </div>
            </form>
        </div>
    </div>
</main>
<?php
    cierre();
?>