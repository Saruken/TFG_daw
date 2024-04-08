"use strict"

const slug=document.getElementById("id-juego").innerText;
const lista=cargar(`https://api.rawg.io/api/games/${slug}?key=d2e169486c71473f876ec4efcf729959`);

const seccion=document.querySelector("section>div>p");
const nombre=document.querySelector("h1");
const info=nombre.nextElementSibling;

const modal=document.querySelector(".modal-footer");
const estrellas=document.querySelectorAll(".estrellas>i");
const cargando=document.getElementById("cargando");

lista.then(response=>{
    cargando.style.display="flex";
    seccion.style.backgroundImage=`url(${response.background_image})`;
    seccion.style.backgroundSize="cover";
    seccion.style.backgroundPosition="center";
    seccion.style.backgroundPosition="center";
    seccion.style.filter="grayscale(70%)";
    seccion.style.zIndex ="-1";

    modal.innerHTML=`
        <input type="hidden" name="id" value='${response.id}'>
        <input type="hidden" name="imagen" value='${response.background_image}'>
        <input type="hidden" name="nombre" value='${response.name}'>
        <input type="hidden" name="fecha" value='${response.released}'>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        <input type="submit" class="recargar btn btn-primary" name="insertar" value="Enviar">
    `;
    estrellas[0].parentElement.insertAdjacentHTML("afterend",
    `   <input type="hidden" name="id" value='${response.id}'>
        <input type="hidden" name="imagen" value='${response.background_image}'>
        <input type="hidden" name="nombre" value='${response.name}'>
        <input type="hidden" name="fecha" value='${response.released}'>
    `);

    nombre.innerText=response.name;
    info.children[0].innerText+=" "+response.released;
    
    let array=[];
    response.genres.forEach(genero => {
        array.push(genero.slug);
        let enlace=`<a href='juegos.php?genero=${genero.slug}' class='text-decoration-none enlace-juego'>${genero.name} </a>`;
        info.children[1].innerHTML+=enlace;
    });
    response.platforms.forEach(plataforma=>{
        let enlace=`<a href='juegos.php?plataforma=${plataforma.platform.id}' class='text-decoration-none enlace-juego'>${plataforma.platform.name} </a>`;
        info.children[2].innerHTML+=enlace;
    })
    nombre.nextElementSibling.nextElementSibling.innerText=response.description_raw;
    // carga de juegos con generos parecidos
    const datos=cargar(`https://api.rawg.io/api/games?key=d2e169486c71473f876ec4efcf729959&page_size=20&page=${Math.floor(Math.random()*5)+1}&genres=${array.join()}`);

    datos.then(respuesta=>{
        const cuerpo=document.querySelector(".otros");
        let i=0;
        let array=[];
        array.push(slug);
        while(i<4){
            const juego=respuesta.results[Math.floor(Math.random()*(respuesta.results.length))];
            if(!array.includes(juego.slug)){
                array.push(juego.slug);
                const ficha=`
                <div class='col-lg-3 col-sm-6 col-11 fw-bold d-flex my-3'>
                    <a class='w-100 h-100 text-decoration-none' href='ficha_juego.php?slug=${juego.slug}'>
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
                i++;
            }
        }
    })
    cargando.style.display="none";
})

estrellas.forEach(estrella=>{
    estrella.addEventListener("mouseenter",()=>{
        const rect = estrella.getBoundingClientRect();
        window.addEventListener('mousemove', (event) => {
            let x = event.clientX;
            let y = event.clientY;
            const mitad=(rect.right-rect.left)/2+rect.left;
            const posicion=Array.from(estrella.parentNode.children).indexOf(estrella);
            let puntos;
            if(y<=rect.bottom && y>=rect.top){
                if(x<mitad){
                    estrella.className='';
                    estrella.className='fa-regular fa-star-half-stroke';
                    puntos=posicion+0.5;
                }else{
                    estrella.className='';
                    estrella.className='fa-solid fa-star';
                    puntos=posicion+1;
                }
                for(let i=0;i<estrellas.length;i++){
                    if(i<posicion){
                        estrellas[i].className='';
                        estrellas[i].className='fa-solid fa-star';
                    }else if(i>posicion){
                        estrellas[i].className='';
                        estrellas[i].className='fa-regular fa-star';
                    }
                }
                document.querySelector("input[name='puntos']").value=puntos;
            }
        })
    })
})

function getCursor(event) {
    let x = event.clientX;
    let y = event.clientY;
    let _position = `X: ${x}<br>Y: ${y}`;

    const infoElement = document.getElementById('info');
    infoElement.innerHTML = _position;
    infoElement.style.top = y + "px";
    infoElement.style.left = (x + 20) + "px";
}