"use strict"

const date = new Date();
let currentDate = `${date.getFullYear()}-${add_cero(date.getMonth()+1)}-${add_cero(date.getDate())}`;

console.log(currentDate);

const lista=cargar(`https://api.rawg.io/api/games?key=d2e169486c71473f876ec4efcf729959&page_size=6&ordering=-released&dates=1900-01-01,2023-07-03`);

const cuerpo=document.getElementById("top-ofertas");
lista.then(value=>{
    value.results.forEach(juego => {
        const ficha=`
        <div class='col-lg-2 col-sm-4 col-6 fw-bold d-flex my-3'>
            <a class='w-100 h-100 text-decoration-none' href='php/juegos/ficha_juego.php?id=${juego.id}'>
                <div class='ficha-juego text-center bg-light border border-1 border-dark w-100 position-relative d-flex flex-column rounded-top shadow'>
                    <div class='border border-1 border-dark border-top-0 border-end-0 border-start-0 rounded-top'>
                        <img class='w-100 h-100 obf rounded-top' src='${juego.background_image}'>
                    </div>
                    <p class='fs-5 mb-0 px-2 text-truncate bg-dark'>${juego.name}</p>
                </div>
            </a>
        </div>
        `;
        cuerpo.innerHTML+=ficha;
    });
})

function add_cero(num){
    if(num<10){
        num="0"+num;
    }
    return num;
}

async function cargar(ruta){
    const respuesta = await fetch(ruta)
    const datos = await respuesta.json()
    return datos
}

// listas
const url='php/apis/listas_api.php';
mostrar(url);

async function mostrar(ruta){
    console.log(ruta);
    const respuesta = await fetch(ruta);
    const info = await respuesta.json();
    const cuerpo=document.getElementById("top-listas");
    // cuerpo.innerHTML="<h1>Listas</h1>";

    info.datos.forEach(lista => {
        let clase='';
        let boton;
        console.log(lista.imagen);
        if(lista.imagen!="assets/img/logo.png"){
            clase='imagen_lista';
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
                    <h3 class='text-capitalize text-truncate mx-1'><a class='text-decoration-none' href='pagina_lista.php?id=${lista.id}'>${lista.nombre}</a></h3>
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
}