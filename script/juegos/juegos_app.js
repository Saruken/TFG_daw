"use strict"

const busqueda=document.getElementById("buscador_nombre");
const anterior=document.getElementById("anterior");
const siguiente=document.getElementById("siguiente");
const cargando=document.getElementById("cargando");

const url="https://api.rawg.io/api/games?key=d2e169486c71473f876ec4efcf729959&page_size=18";

const urlParams = new URLSearchParams(window.location.search);
const myPlata = urlParams.get('plataforma');
const myGene = urlParams.get('genero');
if(myPlata!==null){
    mostrar(url+'&platforms='+myPlata);
}else if(myGene!==null){
    mostrar(url+'&genres='+myGene);
}else{
    mostrar(url);
}

busqueda.addEventListener("keyup",()=>{
    mostrar(url+"&search="+busqueda.value);
})

anterior.addEventListener("click",cambiarPagina);
siguiente.addEventListener("click",cambiarPagina);

function cambiarPagina(eventos){
    eventos.preventDefault();
    mostrar(eventos.target.getAttribute("href"));
}

async function mostrar(ruta){
    cargando.style.display="flex";
    const respuesta = await fetch(ruta);
    const info = await respuesta.json();
    const respuesta2 = await fetch('../apis/coleccion_api.php');
    const info2 = await respuesta2.json();
    const cuerpo=document.querySelector("section");
    cuerpo.innerHTML="<h1 class='mt-2'>Juegos</h1>";
    let tengo=`<input class='rounded-pill px-4 py-1 bg-tengo' type="submit" name="tengo" value='Lo Tengo'>`;
    let quiero=`<input class='rounded-pill px-4 py-1 bg-quiero' type="submit" name="quiero" value='Lo Quiero'>`;
    let borrar=`<input class='rounded-pill px-4 py-1 bg-danger' type="submit" name="borrar" value='Borrar'>`;
    info.results.forEach(juego => {
        let botones=`
            <div>
                ${tengo}
            </div>
            <div class='d-none'>
                ${borrar}
            </div>
            <div>
                ${quiero}
            </div>
        `;
        if(info2.datos.filter(item=>item.id==juego.id).length>0){
            const j2=info2.datos.filter(item=>item.id==juego.id)[0];
            if(j2.estado==1){
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
        }
        const ficha=`
        <div class='botones-juego col-lg-2 col-sm-4 col-6 fw-bold d-flex my-3 position-relative'>
            <div class='ficha-juego text-center bg-light border border-1 border-dark w-100 position-relative d-flex flex-column rounded-top shadow'>
                <div class='border border-1 border-dark border-top-0 border-end-0 border-start-0 rounded-top'>
                    <img class='w-100 h-100 obf rounded-top' src='${juego.background_image}'>
                </div>
                <a class='text-decoration-none text-secondary' href='ficha_juego.php?id=${juego.id}'>
                    <p class='fs-5 mb-0 px-2 text-truncate bg-dark'>
                        ${juego.name}
                    </p>
                </a>
            </div>
            <div class="w-100 position-absolute top-0 start-50 translate-middle-x container-fluid">
                <form class='formulario w-100 h-100 d-flex flex-column align-items-center justify-content-evenly bg-dark bg-opacity-50' action="iframe_content.php" target="content" method="post" enctype='multipart/form-data'>
                    <input type="hidden" name="id" value='${juego.id}'>
                    <input type="hidden" name="imagen" value='${juego.background_image}'>
                    <input type="hidden" name="nombre" value='${juego.name}'>
                    <input type="hidden" name="fecha" value='${juego.released}'>
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

    const sig=info.next;
    const pre=info.previous;

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

    const lateral_cat=document.getElementById("categorias");
    const respuesta_c = await fetch("../apis/categorias_api.php");
    const info_c = await respuesta_c.json();
    let busqueda_ca="";
    let pla="";
    if(ruta.includes('genres=') || ruta.includes('platforms=')){
        if(ruta.split("&").length>3){
            if(ruta.split("&")[2].includes('genres=')){
                busqueda_ca=ruta.split("&")[2].split("genres=")[1];
                pla=ruta.split("&")[3];
            }else{
                busqueda_ca=ruta.split("&")[3].split("genres=")[1];
                pla=ruta.split("&")[2];
            }
        }else{
            if(ruta.includes('genres=')){
                busqueda_ca=ruta.split("genres=")[1];
            }else{
                pla=ruta.split("&")[2];
            }
        }
    }
    lateral_cat.innerHTML='<p>Categorias</p>';
    info_c.datos.forEach(categoria=>{
        let check='';
        if(categoria.id==busqueda_ca){
            check='checked'
        }
        const dom=`
        <div class="form-check col-5">
            <input class="form-check-input" ${check} type="radio" name="flexRadioDefault1" value='${categoria.id}' id="${categoria.nombre+categoria.id}">
            <label class="form-check-label" for="${categoria.nombre+categoria.id}">${categoria.nombre}</label>
        </div>
        `;
        lateral_cat.innerHTML+=dom;

        let cate=lateral_cat.querySelectorAll("input:checked").value;
        let filtro;
        lateral_cat.querySelectorAll("input").forEach(radio=>{
            radio.addEventListener("change",()=>{
                if(radio.checked){
                    cate=radio.value
                }
                if(pla!=""){
                    pla="&"+pla;
                }
                filtro='&genres='+cate;
                mostrar(url+filtro+pla);
            })
        })
    })

    // ======================================================================0
    const lateral_pla=document.getElementById("plataformas");
    const respuesta_p = await fetch("../apis/plataformas_api.php");
    const info_p = await respuesta_p.json();
    let busqueda_pl="";
    let cat="";
    if(ruta.includes('genres=') || ruta.includes('platforms=')){
        if(ruta.split("&").length>3){
            if(ruta.split("&")[2].includes('platforms=')){
                busqueda_pl=ruta.split("&")[2].split("platforms=")[1];
                cat=ruta.split("&")[3];
            }else{
                busqueda_pl=ruta.split("&")[3].split("platforms=")[1];
                cat=ruta.split("&")[2];
            }
        }else{
            if(ruta.includes('platforms=')){
                busqueda_pl=ruta.split("platforms=")[1];
            }else{
                cat=ruta.split("&")[2];
            }
        }
    }
    lateral_pla.innerHTML='<p class="mt-2">Plataformas</p>';
    info_p.datos.forEach(plataforma=>{
        let check='';
        if(plataforma.id==busqueda_pl){
            check='checked'
        }
        const dom=`
        <div class="form-check col-5">
            <input class="form-check-input" ${check} type="radio" name="flexRadioDefault" value='${plataforma.id}' id="${plataforma.nombre+plataforma.id}">
            <label class="form-check-label" for="${plataforma.nombre+plataforma.id}">${plataforma.nombre}</label>
        </div>
        `;
        lateral_pla.innerHTML+=dom;

        let plata=lateral_pla.querySelectorAll("input:checked").value;
        let filtro;
        lateral_pla.querySelectorAll("input").forEach(radio=>{
            radio.addEventListener("change",()=>{
                if(radio.checked){
                    plata=radio.value
                }
                if(cat!=""){
                    cat="&"+cat;
                }
                filtro='&platforms='+plata;
                mostrar(url+filtro+cat);
            })
        })
    })
    cargando.style.display="none";
}