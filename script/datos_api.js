"use strict"

async function cargar(ruta){
    const respuesta = await fetch(ruta)
    const datos = await respuesta.json()
    return datos
}

const plata=cargar('https://api.rawg.io/api/platforms?key=d2e169486c71473f876ec4efcf729959')

plata.then(response=>{
    let i=0;
    let cadena="";
    response.results.forEach(plataforma=>{
        if(response.results.length-1>i){
            cadena+=plataforma.id+","+plataforma.name+":-:";
        }else{
            cadena+=plataforma.id+","+plataforma.name;
        }
        i++;
    })
    const input_pl=document.getElementById("input_pl");
    input_pl.value=cadena;
})

const cate=cargar('https://api.rawg.io/api/genres?key=d2e169486c71473f876ec4efcf729959')

cate.then(response=>{
    let i=0;
    let cadena="";
    response.results.forEach(categoria=>{
        if(response.results.length-1>i){
            cadena+=categoria.id+","+categoria.name+":-:";
        }else{
            cadena+=categoria.id+","+categoria.name;
        }
        i++;
    })
    const input_ca=document.getElementById("input_ca");
    input_ca.value=cadena;
})

insercion_juegos()
async function insercion_juegos(){
    const respuesta = await fetch('../php/apis/plataformas_api.php')
    const info = await respuesta.json()
    
    let i=0;
    let cadena="";
    
    info.datos.forEach(plata=>{
        if(info.datos.length-1>i){
            cadena+=plata.id+",";
        }else{
            cadena+=plata.id;
        }
        i++;
    })

    let siguiente=`https://api.rawg.io/api/games?key=d2e169486c71473f876ec4efcf729959&page_size=30&platforms${cadena}`;
    let contador=0;
    while(siguiente!=null && contador<10){
        const respuesta2 = await fetch(siguiente)
        const info2 = await respuesta2.json()
        let i=0;
        let cadena="";
        info2.results.forEach(juego=>{
            let plataformas=[];
            juego.platforms.forEach(p=>{
                plataformas.push(p.platform.id);
            })

            let generos=[];
            juego.genres.forEach(g=>{
                generos.push(g.id);
            })

            if(info2.results.length-1>i){
                cadena+=juego.id+","+juego.name+","+juego.released+","+juego.background_image+","+plataformas.toString().replaceAll(",","-")+","+generos.toString().replaceAll(",","-")+":-:";
            }else{
                cadena+=juego.id+","+juego.name+","+juego.released+","+juego.background_image+","+plataformas.toString().replaceAll(",","-")+","+generos.toString().replaceAll(",","-");
            }
            i++;
        })

        const input_ju=document.getElementById("input_ju");
        if(info2.previous==null){
            input_ju.value=cadena;
        }else{
            input_ju.value+=":-:"+cadena;
        }
        siguiente=info2.next;
        contador++;
    }
}


