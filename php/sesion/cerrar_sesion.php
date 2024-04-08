<?php
    require_once "../funciones.php";
    apertura();
?>
<main class="container-fluid px-sm-3 p-0 d-flex align-items-center">
    <div class="container-xxl d-flex flex-wrap mx-auto justify-content-center align-items-center flex-column p-0">
        <div>
            <form class=" border border-1 border-dark rounded-2 shadow p-4 bg-secondary" action="" method="post">
                <h1>Cerrar Sesión</h1>
                <div class='d-flex justify-content-center mt-3'>
                    <input name='enviar' type='submit' class='btn btn-danger' value='Cerrar'>
                </div>
            </form>
        </div>
    </div>
</main>
<?php
    if(isset($_POST['enviar'])){
        if(isset($_COOKIE['open'])){
            setcookie('open',"",time()-3600,"/");
        }
        $_SESSION=array();
        session_destroy();
        echo "<p class='notificacion reborde text-center fs-1 mt-3 fw-semibold text-success'>Sesión cerrada con éxito</p>";
        header('Refresh: 1.5; URL=../../index.php');
    }
    cierre();
?>