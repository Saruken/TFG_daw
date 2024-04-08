<?php
    require_once "php/funciones.php";
    apertura();
?>
<!-- <script src='script/carga_api.js' defer></script> -->
<script src='script/index.js' defer></script>
<main>
    <!-- ofertas mas valoradas -->
    <section id='top-ofertas' class='container-fluid row mx-auto mt-3'>
        <h3 class='border-bottom fw-bold'>Últimas Novedades</h3>
    </section>
    <!-- slider -->
    <section id='slider' class='container-fluid row mx-auto my-5'>
        <div id="carouselExampleDark" class="carousel carousel-dark slide">
            <div class="carousel-inner">
                <div class='carousel-item active'>
                    <div id="landing-3" class="landing d-flex align-items-center justify-content-center">
                        <a href="php/landing/landing.php" class="text-decoration-none">
                            <p class="fs-1 borde-text fw-bold">GESTIONA TU COLECCIÓN DE VIDEOJUEGOS</p>
                        </a>
                    </div>
                </div>
                <?php
                    $conexion=conectar();
                    $consulta=$conexion->query("select usuario,juego,texto,puntos from opinion order by rand() limit 4");
                    $i=0;
                    while($lista=$consulta->fetch_array(MYSQLI_ASSOC)){
                        $preparada=$conexion->prepare("select imagen from juego where id=?");
                        $preparada->bind_param("i",$lista['juego']);
                        $preparada->bind_result($imagen);
                        $preparada->execute();
                        $preparada->fetch();
                        $cadena="";
                        for($i=0;$i<5;$i++){
                            if($i<ceil($lista['puntos']-1)){
                                $cadena=$cadena."<i class='fa-solid fa-star'></i>";
                            }else if($i>ceil($lista['puntos']-1)){
                                $cadena=$cadena."<i class='fa-regular fa-star'></i>";
                            }else{
                                if($lista['puntos']-1==ceil($lista['puntos']-1)){
                                    $cadena=$cadena."<i class='fa-solid fa-star'></i>";
                                }else{
                                    $cadena=$cadena."<i class='fa-regular fa-star-half-stroke'></i>";
                                }
                            }
                        }
                        echo "
                        <div class='carousel-item'>
                            <img src='$imagen' class='d-block' alt='...'>
                            <div class='carousel-caption d-block'>
                                <span>
                                    <h5 class='d-inline-block'>$lista[usuario]</h5><p class='d-inline-block ms-2'>$cadena</p>
                                </span>
                                <p>$lista[texto]</p>
                            </div>
                        </div>
                        ";
                        $i++;
                        $preparada->close();
                    }
                    $consulta->close();
                    $conexion->close(); 
                ?>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleDark" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleDark" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    </section>
    <!-- 3 listas mas seguidas -->
    <section id='top-listas' class='container-fluid row mx-auto mb-3'>
        <h3 class='border-bottom fw-bold'>Listas de nuestros usuarios</h3>
    </section>
</main>

<?php  
    cierre();
?>