"use strict"

const busqueda=document.getElementById("buscador_nombre");
const anterior=document.getElementById("anterior");
const siguiente=document.getElementById("siguiente");
const cargando=document.getElementById("cargando");

const url='../apis/listas_api.php';
mostrar(url);

busqueda.addEventListener("keyup",()=>{
    mostrar(url+"?busqueda="+busqueda.value);
})

anterior.addEventListener("click",cambiarPagina);
siguiente.addEventListener("click",cambiarPagina);

function cambiarPagina(eventos){
    eventos.preventDefault();
    mostrar("../../../.."+eventos.target.getAttribute("href"));
}

async function mostrar(ruta){
    cargando.style.display="flex";
    console.log(ruta);
    const respuesta = await fetch(ruta);
    const info = await respuesta.json();
    const cuerpo=document.querySelector("section");
    cuerpo.innerHTML="<h1 class='mt-2'>Listas</h1>";

    info.datos.forEach(lista => {
        let clase='';
        let boton;
        if(lista.imagen!="assets/img/logo.png"){
            clase='imagen_lista';
        }else{
            lista.imagen="../../"+lista.imagen;
        }
        let admin;
        let envio;
        if(document.getElementById("admin")!=null){
            admin=document.getElementById("admin").innerText;
        }else{
            admin="";
        }
        if(admin=='borrar'){
            boton="<input name='borrar' type='submit' class='btn btn-danger mx-1' value='Borrar'>";
            envio="action='#'";
        }else{
            envio="action='iframe_seguir_lista.php' target='content'";
            if(lista.sigue!=0){
                boton="<input name='seguir' type='submit' class='btn btn-danger mx-1' value='Dejar de seguir'>";
            }else{
                boton="<input name='seguir' type='submit' class='btn btn-primary mx-1' value='Seguir'>";
            }
        }
        const ficha=`
        <div class='listas-card col-md-6 col-12 my-2'>
            <div class='d-flex p-3 h-100 bg-dark'>
                <div class='rectangulo bg-white d-flex align-items-center'>
                    <img class='w-100 ${clase}' src='${lista.imagen}'>
                </div>
                <div class='ms-3 d-flex justify-content-between align-items-center flex-wrap flex-md-nowrap'>
                    <h3 class='text-capitalize text-truncate mx-1'>
                        <a class='text-decoration-none text-truncate d-block' href='pagina_lista.php?id=${lista.id}'>${lista.nombre}</a>
                    </h3>
                    <form ${envio} method='post' enctype='multipart/form-data'>
                        <input type='hidden' name='id' value='${lista.id}'>
                        ${boton}
                    </form>
                </div>
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

    const sig=info.siguiente;
    const pre=info.anterior;

    siguiente.setAttribute("href",sig);
    if(siguiente.getAttribute("href")!=='null'){
        siguiente.style.display="block";
    }else{
        siguiente.style.display="none";
    }
    
    anterior.setAttribute("href",pre);
    if(anterior.getAttribute("href")!=='null'){
        anterior.style.display="block";
    }else{
        anterior.style.display="none";
    }

    
    const botones=document.querySelectorAll(".listas-card input[type='submit']");
    botones.forEach(boton=>{
        boton.addEventListener("click",()=>{
            if(boton.value=="Seguir"){
                boton.value="Dejar de seguir";
                boton.classList.remove("btn-primary");
                boton.classList.add("btn-danger");
            }else{
                boton.value="Seguir";
                boton.classList.remove("btn-danger");
                boton.classList.add("btn-primary");
            }
        });
    });
    cargando.style.display="none";
}

