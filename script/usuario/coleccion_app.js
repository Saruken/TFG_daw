"use strict"

const cargando=document.getElementById("cargando");

mostrar();

async function mostrar(){
    cargando.style.display="flex";
    let cambio=false;
    const respuesta = await fetch('../apis/coleccion_api.php');
    const info = await respuesta.json();
    console.log(info.datos)
    const cuerpo=document.querySelector("section");
    cuerpo.innerHTML="";
    let tengo=`<input class='rounded-pill px-4 py-1 bg-tengo' type="submit" name="tengo" value='Lo Tengo'>`;
    let quiero=`<input class='rounded-pill px-4 py-1 bg-quiero' type="submit" name="quiero" value='Lo Quiero'>`;
    let borrar=`<input class='rounded-pill px-4 py-1 bg-danger' type="submit" name="borrar" value='Borrar'>`;
    info.datos.forEach(juego => {
        if(juego.estado==1 && cambio==false){
            cuerpo.innerHTML+='<h2 class="mt-3 mb-0">Tengo</h2>';
            cambio=true;
        }else if(juego.estado==0 && cambio==true){
            cuerpo.innerHTML+='<h2 class="mt-3 mb-0">Quiero</h2>';
            cambio=false;
        }
        let botones;
        if(juego.estado==1){
            botones=`
                <div class='d-none'>
                    ${tengo}
                </div>
                <div>
                    ${borrar}
                </div>
                <div>
                    ${quiero}
                </div>
            `;
        }else{
            botones=`
                <div>
                    ${tengo}
                </div>
                <div>
                    ${borrar}
                </div>
                <div class='d-none'>
                    ${quiero}
                </div>
            `;
        }
        const ficha=`
        <div class='botones-juego col-lg-2 col-sm-4 col-6 fw-bold d-flex my-3 position-relative'>
            <div class='ficha-juego text-center bg-light border border-1 border-dark w-100 position-relative d-flex flex-column rounded-top shadow'>
                <div class='border border-1 border-dark border-top-0 border-end-0 border-start-0 rounded-top'>
                    <img class='w-100 h-100 obf rounded-top' src='${juego.imagen}'>
                </div>
                <a class='text-decoration-none text-secondary' href='../juegos/ficha_juego.php?id=${juego.id}'>
                    <p class='fs-5 mb-0 px-2 text-truncate bg-dark'>
                        ${juego.nombre}
                    </p>
                </a>
            </div>
            <div class="w-100 position-absolute top-0 start-50 translate-middle-x container-fluid">
                <form class='formulario w-100 h-100 d-flex flex-column align-items-center justify-content-evenly bg-dark bg-opacity-50' action="../juegos/iframe_content.php" target="content" method="post" enctype='multipart/form-data'>
                    <input type="hidden" name="id" value='${juego.id}'>
                    <input type="hidden" name="imagen" value='${juego.imagen}'>
                    <input type="hidden" name="nombre" value='${juego.nombre}'>
                    <input type="hidden" name="fecha" value='${juego.f_lanzamiento}'>
                    ${botones}
                </form>
            </div>
        </div>
        `;
        cuerpo.innerHTML+=ficha;
    });

    const formulario_botones=document.querySelectorAll(".formulario");
    formulario_botones.forEach(formu=>{
        formu.querySelectorAll("input[type='submit']").forEach(boton=>{
            boton.addEventListener("click",()=>{
                formu.querySelector("div.d-none").classList.remove("d-none");
                boton.parentElement.classList.add("d-none");
            })
        })
    })
    cargando.style.display="none";
}