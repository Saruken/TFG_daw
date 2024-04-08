async function cargar(ruta){
    const respuesta = await fetch(ruta)
    const datos = await respuesta.json()
    return datos
}