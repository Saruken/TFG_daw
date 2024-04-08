"use strict"

const juegos=document.querySelectorAll(".botones-juego");
const cargando=document.getElementById("cargando");

juegos.forEach(juego=>{
    const id=juego.querySelector("input").value;
    mostrar(juego,id);
})

async function mostrar(juego,id){
    cargando.style.display="flex";
    const respuesta2 = await fetch('../apis/coleccion_api.php');
    const info2 = await respuesta2.json();
    let tengo=`<input class='rounded-pill px-4 py-1 bg-tengo' type="submit" name="tengo" value='Lo Tengo'>`;
    let quiero=`<input class='rounded-pill px-4 py-1 bg-quiero' type="submit" name="quiero" value='Lo Quiero'>`;
    let borrar=`<input class='rounded-pill px-4 py-1 bg-danger' type="submit" name="borrar" value='Borrar'>`;

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
    if(info2.datos.filter(item=>item.id==id).length>0){
        const j2=info2.datos.filter(item=>item.id==id)[0];
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
    const formulario=juego.querySelector(".formulario");
    formulario.innerHTML=formulario.innerHTML+botones;

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