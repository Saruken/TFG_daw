<?php
    require_once "../funciones.php";
    apertura();
?>
<main class="container-fluid px-sm-3 p-0 d-flex align-items-center">
    <div class="w-100">
        <div class="container-md row mx-auto">
            <form class="col-xl-7 col-lg-8 mx-auto border border-1 rounded-2 shadow p-4 border-dark bg-secondary" action="#" method="post" enctype='multipart/form-data'>
                <h1 class="text-center mb-3">Nuevo usuario</h1>
                <div class="input-group mb-3 px-5">
                    <span class="input-group-text" id="inputGroup-sizing-default">Nick</span>
                    <input required name="nick" type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default">
                </div>
                <div class="input-group mb-3 px-5">
                    <span class="input-group-text" id="inputGroup-sizing-default">Contraseña</span>
                    <input required name="pass" type="password" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default">
                </div>
                <div class="input-group mb-3 px-5">
                    <span class="input-group-text" id="inputGroup-sizing-default">Correo</span>
                    <input required name="correo" type="email" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default">
                </div>
                <div class="input-group mb-3 px-5">
                    <span class="input-group-text">Fecha de nacimiento</span>
                    <input required name="f_nac" class="form-control" type="date">
                </div>
                <div class="d-flex justify-content-center mt-4">
                    <input name="enviar" type="submit" class="btn btn-primary" value="Registrarme">
                </div>
            </form>
        </div>
    </div>
</main>
<?php
    if(isset($_POST['enviar'])){
        $conexion=conectar();
        $consulta=$conexion->prepare("select count(*) from usuario where nick=?");
        // $consulta=$conexion->prepare("select count * from usuario where correo=?");
        $consulta->bind_param("s",$_POST['nick']);
        $consulta->bind_result($cantidad);
        $consulta->execute();
        $consulta->fetch();

        if($cantidad==0){
            $consulta->close();
                                
            $insercion=$conexion->prepare("insert into usuario (nick,contraseña,correo,f_nac,tipo,estado,imagen) values (?,?,?,?,'Basico',1,'/assets/img/default.avif')");
            $insercion->bind_param("ssss",$_POST['nick'],$_POST['pass'],$_POST['correo'],$_POST['f_nac']);
            $insercion->execute();
            $insercion->close();
            echo "<p class='notificacion text-danger'>Usuario creado con éxito</p>";
        }else{
            echo "<p class='notificacion text-danger'>ERROR - Este usuario ya se encuentra en la base de datos</p>";
        }
        $conexion->close();
        header('Refresh: 1.5; URL=../../inicio.php');
    }
    cierre();
?>